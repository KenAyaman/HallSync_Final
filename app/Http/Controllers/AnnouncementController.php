<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\UserActivityLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AnnouncementController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->role === 'manager') {
            // Managers see ALL announcements, including expired ones, for audit purposes (H-11).
            $announcements = Announcement::orderByDesc('is_pinned')->orderBy('created_at', 'desc')->get();
            $activeCount   = $announcements->where('is_active', true)
                ->filter(fn ($a) => ! $a->expires_at || $a->expires_at->isFuture())
                ->count();
            $totalCount    = $announcements->count();

            return view('admin.announcements.index', compact('announcements', 'activeCount', 'totalCount'));
        }
        
        // Resident view - only see active announcements
        $announcements = Announcement::visibleToResidents()
            ->orderByDesc('is_pinned')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('resident.announcements.index', compact('announcements'));
    }

    public function create()
    {
        // Only managers can create announcements
        if (Auth::user()->role !== 'manager') {
            abort(403);
        }
        
        return view('admin.announcements.create');
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'manager') {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'priority' => 'required|in:normal,important,urgent',
            'starts_at' => 'nullable|date',
            'expiration_option' => 'required|in:24_hours,7_days,30_days,custom,never',
            'custom_expires_at' => 'nullable|required_if:expiration_option,custom|date|after:now',
        ]);
        $this->validateCustomExpiryAfterStart($request);

        [$expiresAt, $isPinned] = $this->expirationValues($request);

        $announcement = new Announcement();
        $announcement->user_id = Auth::id();
        $announcement->title = $request->title;
        $announcement->content = $request->content;
        $announcement->priority = $request->priority;
        $announcement->is_active = true;
        $announcement->starts_at = $request->filled('starts_at') ? Carbon::parse($request->starts_at) : now();
        $announcement->expires_at = $expiresAt;
        $announcement->is_pinned = $isPinned;
        $announcement->save();
        UserActivityLog::record('announcement.created', 'Published an announcement.', Auth::user(), Auth::user(), ['announcement_id' => $announcement->id]);

        return redirect()->route('announcements.index')
            ->with('success', 'Announcement published!');
    }

    public function show(Announcement $announcement)
    {
        $user = Auth::user();
        
        if ($user->role === 'manager') {
            return view('admin.announcements.show', compact('announcement'));
        }

        abort_unless(Announcement::visibleToResidents()->whereKey($announcement)->exists(), 404);
        
        return view('resident.announcements.show', compact('announcement'));
    }

    public function edit(Announcement $announcement)
    {
        if (Auth::user()->role !== 'manager') {
            abort(403);
        }
        
        return view('admin.announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        if (Auth::user()->role !== 'manager') {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'priority' => 'required|in:normal,important,urgent',
            'starts_at' => 'nullable|date',
            'expiration_option' => 'required|in:24_hours,7_days,30_days,custom,never',
            'custom_expires_at' => 'nullable|required_if:expiration_option,custom|date|after:now',
        ]);
        $this->validateCustomExpiryAfterStart($request);

        [$expiresAt, $isPinned] = $this->expirationValues($request);

        $before = $announcement->only(['title', 'content', 'priority', 'starts_at', 'expires_at', 'is_pinned']);

        $announcement->update([
            'title'     => $request->title,
            'content'   => $request->content,
            'priority'  => $request->priority,
            // Preserve the original starts_at when the field is not submitted (M-06).
            // Using now() as fallback would silently make a scheduled announcement go live immediately.
            'starts_at' => $request->filled('starts_at') ? Carbon::parse($request->starts_at) : $announcement->starts_at,
            'expires_at'=> $expiresAt,
            'is_pinned' => $isPinned,
        ]);

        $changedFields = collect($announcement->only(array_keys($before)))
            ->filter(fn ($v, $k) => (string) $before[$k] !== (string) $v)
            ->keys()
            ->values()
            ->all();

        UserActivityLog::record('announcement.updated', 'Updated an announcement.', Auth::user(), Auth::user(), [
            'announcement_id' => $announcement->id,
            'changed_fields'  => $changedFields,
        ]);

        return redirect()->route('announcements.index')
            ->with('success', 'Announcement updated successfully!');
    }

    public function toggle(Announcement $announcement)
    {
        if (Auth::user()->role !== 'manager') {
            abort(403);
        }
        
        $announcement->is_active = !$announcement->is_active;
        $announcement->save();
        UserActivityLog::record('announcement.visibility_changed', 'Changed announcement visibility.', Auth::user(), Auth::user(), ['announcement_id' => $announcement->id, 'is_active' => $announcement->is_active]);
        
        $status = $announcement->is_active ? 'published' : 'hidden';
        return redirect()->route('announcements.index')
            ->with('success', "Announcement {$status} successfully!");
    }

    public function destroy(Announcement $announcement)
    {
        if (Auth::user()->role !== 'manager') {
            abort(403);
        }
        
        $announcement->delete();
        UserActivityLog::record('announcement.deleted', 'Deleted an announcement.', Auth::user(), Auth::user(), ['announcement_id' => $announcement->id]);
        
        return redirect()->route('announcements.index')
            ->with('success', 'Announcement deleted successfully!');
    }

    protected function expirationValues(Request $request): array
    {
        $startsAt = $request->filled('starts_at') ? Carbon::parse($request->starts_at) : now();

        return match ($request->expiration_option) {
            '24_hours' => [$startsAt->copy()->addDay(), false],
            '30_days'  => [$startsAt->copy()->addDays(30), false],
            'custom'   => [Carbon::parse($request->custom_expires_at), false],
            // 'never' sets is_pinned = true — this is intentional: permanent announcements are pinned (L-05).
            'never'    => [null, true],
            default    => [$startsAt->copy()->addDays(7), false],
        };
    }

    private function validateCustomExpiryAfterStart(Request $request): void
    {
        if ($request->input('expiration_option') !== 'custom' || ! $request->filled('custom_expires_at')) {
            return;
        }

        $startsAt = $request->filled('starts_at') ? Carbon::parse($request->starts_at) : now();
        $expiresAt = Carbon::parse($request->custom_expires_at);

        if ($expiresAt->lessThanOrEqualTo($startsAt)) {
            throw ValidationException::withMessages([
                'custom_expires_at' => 'The expiration date must be after the scheduled start date.',
            ]);
        }
    }
}
