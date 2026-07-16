<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceTicket;
use App\Models\User;
use App\Models\UserActivityLog;
use App\Notifications\TicketStatusChangedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Validation\Rule;

class TicketController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'manager') {
            // Eager-load relationships to prevent N+1 on user/assignedTo access in the view.
            // Limit to 500 to avoid memory exhaustion; proper server-side pagination is a future task.
            $tickets = MaintenanceTicket::with(['user', 'assignedTo'])
                ->orderBy('created_at', 'desc')
                ->limit(500)
                ->get();

            $handymen = User::where('role', 'handyman')->where('is_active', true)->orderBy('name')->get();

            return view('admin.tickets.index', compact('tickets', 'handymen'));
        } elseif ($user->role === 'handyman') {
            return redirect()->route('staff.queue');
        } else {
            // Resident sees only their own tickets
            $tickets = MaintenanceTicket::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();

            return view('resident.tickets.index', compact('tickets'));
        }
    }

    public function create()
    {
        $user = Auth::user();

        // Handymen cannot create tickets
        if ($user->role === 'handyman') {
            abort(403, 'Handymen cannot create tickets.');
        }

        if ($user->role === 'manager') {
            return view('admin.tickets.create');
        }

        return view('resident.tickets.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:plumbing,electrical,furniture,hvac,other',
            'location' => 'nullable|string|max:255',
            'priority' => 'required|in:low,medium,critical',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|mimetypes:image/jpeg,image/png,image/webp|max:2048',
            'video' => 'nullable|file|mimes:mp4,mov|mimetypes:video/mp4,video/quicktime|max:10240',
        ], [
            'image.image' => 'The photo could not be uploaded because it is not a supported image.',
            'image.mimes' => 'The photo must be a JPG, PNG, or WebP file.',
            'image.max' => 'The photo is too large. Please upload an image smaller than 2MB.',
            'video.mimes' => 'The video must be an MP4, MOV, or AVI file.',
            'video.max' => 'The video is too large. Please upload a video smaller than 10MB.',
        ]);

        try {
            $ticket = Cache::lock('ticket-submit:' . Auth::id(), 10)->block(3, function () use ($request) {
                if (MaintenanceTicket::isRecentDuplicate(
                    Auth::id(),
                    $request->category,
                    $request->title,
                    $request->description
                )) {
                    return null;
                }

                $ticket = new MaintenanceTicket();
                $ticket->user_id = Auth::id();
                $ticket->ticket_id = 'TKT-' . strtoupper(uniqid());
                $ticket->title = $request->title;
                $ticket->description = $request->description;
                $ticket->category = $request->category;
                $ticket->location = $request->location;
                $ticket->priority = MaintenanceTicket::normalizePriorityValue($request->priority);
                $ticket->status = 'pending_approval';

                if ($request->hasFile('image')) {
                    $ticket->image_path = $request->file('image')->store('tickets/images', 'local');
                }

                if ($request->hasFile('video')) {
                    $ticket->video_path = $request->file('video')->store('tickets/videos', 'local');
                }

                $ticket->save();
                UserActivityLog::record(
                    'ticket.created',
                    'Submitted a maintenance ticket for review.',
                    Auth::user(),
                    Auth::user(),
                    ['ticket_id' => $ticket->id, 'reference' => $ticket->ticket_id]
                );

                return $ticket;
            });

            if (! $ticket) {
                return back()
                    ->withInput()
                    ->with('error', 'A matching maintenance request was already submitted recently. Please track the existing ticket instead.');
            }

            return redirect()->route('tickets.index')
                ->with('success', 'Ticket submitted for admin approval. Ticket ID: ' . $ticket->ticket_id);
        } catch (LockTimeoutException) {
            return back()
                ->withInput()
                ->with('error', 'Your request is still being submitted. Please wait a moment before trying again.');
        } catch (\Exception $e) {
            Log::warning('Ticket submission failed.', [
                'user_id' => Auth::id(),
                'message' => $e->getMessage(),
            ]);

            $hasAttachment = $request->hasFile('image') || $request->hasFile('video');
            $errorMessage = $hasAttachment
                ? 'The attachment could not be saved. Please check the file size and format, then try again.'
                : 'Your request could not be submitted. Please try again.';

            return redirect()->back()
                ->with('error', $errorMessage)
                ->withInput();
        }
    }

    public function reopen(MaintenanceTicket $ticket)
    {
        $user = Auth::user();

        $this->authorize('reopen', $ticket);

        if (! in_array($ticket->status, [MaintenanceTicket::STATUS_RESOLVED, 'completed'], true)) {
            return back()->with('error', 'Only resolved tickets awaiting your confirmation can be reopened.');
        }

        if (($ticket->resolved_at ?? $ticket->updated_at)->lt(now()->subDays((int) config('hallsync.ticket_reopen_days', 7)))) {
            return back()->with('error', 'The reopening period for this ticket has expired. Please submit a new request.');
        }

        if ($ticket->reopen_count >= (int) config('hallsync.ticket_max_reopens', 2)) {
            return back()->with('error', 'This ticket has reached its reopening limit. Please submit a new maintenance request.');
        }

        $ticket->status = $ticket->assignedTo?->is_active ? 'assigned' : 'approved';
        if ($ticket->status === 'approved') {
            $ticket->assigned_to = null;
        }
        $ticket->reopen_count++;
        $ticket->resolved_at = null;
        $ticket->work_started_at = null;
        $ticket->task_started_at = null;
        $ticket->task_completed_at = null;
        $ticket->task_duration_minutes = null;
        $ticket->save();
        UserActivityLog::record(
            'ticket.reopened',
            'Resident reported that a resolved maintenance ticket still needs work.',
            $user,
            $user,
            ['ticket_id' => $ticket->id, 'status' => $ticket->status]
        );

        return back()->with('success', 'Ticket reopened successfully. Staff have been notified through the active queue.');
    }

    public function close(MaintenanceTicket $ticket)
    {
        $this->authorize('close', $ticket);

        if (! in_array($ticket->status, [MaintenanceTicket::STATUS_RESOLVED, 'completed'], true)) {
            return back()->with('error', 'Only resolved tickets can be accepted and closed.');
        }

        $ticket->forceFill([
            'status' => MaintenanceTicket::STATUS_CLOSED,
            'closed_at' => now(),
        ])->save();

        UserActivityLog::record(
            'ticket.closed',
            'Resident confirmed the repair and closed the ticket.',
            Auth::user(),
            Auth::user(),
            ['ticket_id' => $ticket->id]
        );

        return back()->with('success', 'Thank you. The resolved ticket has been closed.');
    }

    public function rate(Request $request, MaintenanceTicket $ticket)
    {
        abort_unless($ticket->user_id === Auth::id(), 403);
        abort_unless(in_array($ticket->status, [
            MaintenanceTicket::STATUS_RESOLVED,
            MaintenanceTicket::STATUS_CLOSED,
        ], true), 403);

        if ($ticket->satisfaction_rated_at) {
            return back()->with('info', 'You have already rated this ticket.');
        }

        $validated = $request->validate([
            'satisfaction_rating' => ['required', 'integer', 'min:1', 'max:5'],
            'satisfaction_note' => ['nullable', 'string', 'max:280'],
        ]);

        $ticket->update([
            'satisfaction_rating' => $validated['satisfaction_rating'],
            'satisfaction_note' => $validated['satisfaction_note'] ?? null,
            'satisfaction_rated_at' => now(),
        ]);

        return back()->with('success', 'Thank you for your feedback!');
    }

    public function requestCancellation(Request $request, MaintenanceTicket $ticket)
    {
        $this->authorize('requestCancellation', $ticket);

        if (! in_array($ticket->status, [
            MaintenanceTicket::STATUS_APPROVED,
            MaintenanceTicket::STATUS_ASSIGNED,
            MaintenanceTicket::STATUS_IN_PROGRESS,
        ], true)) {
            return back()->with('error', 'A cancellation request is only available after approval and before resolution.');
        }

        if ($ticket->cancellation_requested_at) {
            return back()->with('error', 'A cancellation request is already awaiting administration review.');
        }

        $validated = $request->validate([
            'cancellation_reason' => ['required', 'string', 'max:500'],
        ]);

        $ticket->forceFill([
            'cancellation_requested_at' => now(),
            'cancellation_reason' => $validated['cancellation_reason'],
        ])->save();

        UserActivityLog::record(
            'ticket.cancellation_requested',
            'Resident requested cancellation of an active maintenance ticket.',
            Auth::user(),
            Auth::user(),
            ['ticket_id' => $ticket->id, 'reason' => $ticket->cancellation_reason]
        );

        return back()->with('success', 'Cancellation request submitted for administration review.');
    }

    public function show(MaintenanceTicket $ticket)
    {
        $user = Auth::user();

        if ($user->role === 'manager') {
            return view('admin.tickets.show', compact('ticket'));
        }

        if ($user->role === 'handyman') {
            $this->authorize('view', $ticket);
            return view('handyman.tickets.show', compact('ticket'));
        }

        $this->authorize('view', $ticket);

        return view('resident.tickets.show', compact('ticket'));
    }

    public function track(MaintenanceTicket $ticket)
    {
        $user = Auth::user();

        $this->authorize('view', $ticket);

        if (! $user->isResident()) {
            return redirect()->route('tickets.show', $ticket);
        }

        return view('resident.tickets.track', compact('ticket'));
    }

    public function edit(MaintenanceTicket $ticket)
    {
        $user = Auth::user();

        // Handymen cannot edit tickets
        $this->authorize('update', $ticket);

        if ($user->role === 'manager') {
            return view('admin.tickets.edit', compact('ticket'));
        }

        return view('resident.tickets.edit', compact('ticket'));
    }

    public function update(Request $request, MaintenanceTicket $ticket)
    {
        $user = Auth::user();

        $this->authorize('update', $ticket);

        $request->validate([
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,critical',
            'category' => 'required|in:plumbing,electrical,furniture,hvac,other',
            'location' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|mimetypes:image/jpeg,image/png,image/webp|max:2048',
            'video' => 'nullable|file|mimes:mp4,mov|mimetypes:video/mp4,video/quicktime|max:10240',
            'remove_image' => 'nullable|boolean',
            'remove_video' => 'nullable|boolean',
        ]);

        try {
            $ticket->description = $request->description;
            $ticket->priority = MaintenanceTicket::normalizePriorityValue($request->priority);
            $ticket->category = $request->category;
            $ticket->location = $request->location;

            if ($request->has('remove_image') && $request->remove_image) {
                if ($ticket->image_path) {
                    $this->deleteStoredMedia($ticket->image_path);
                    $ticket->image_path = null;
                }
            }

            if ($request->has('remove_video') && $request->remove_video) {
                if ($ticket->video_path) {
                    $this->deleteStoredMedia($ticket->video_path);
                    $ticket->video_path = null;
                }
            }

            if ($request->hasFile('image')) {
                if ($ticket->image_path) {
                    $this->deleteStoredMedia($ticket->image_path);
                }
                $path = $request->file('image')->store('tickets/images', 'local');
                $ticket->image_path = $path;
            }

            if ($request->hasFile('video')) {
                if ($ticket->video_path) {
                    $this->deleteStoredMedia($ticket->video_path);
                }
                $path = $request->file('video')->store('tickets/videos', 'local');
                $ticket->video_path = $path;
            }

            $ticket->save();
            UserActivityLog::record(
                'ticket.updated',
                'Updated a maintenance ticket.',
                $user,
                $user,
                ['ticket_id' => $ticket->id]
            );

            return redirect()->route('tickets.index')
                ->with('success', 'Ticket updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Update failed. Please try again.')
                ->withInput();
        }
    }

    public function destroy(MaintenanceTicket $ticket)
    {
        $user = Auth::user();

        $this->authorize('delete', $ticket);

        if ($ticket->image_path) {
            $this->deleteStoredMedia($ticket->image_path);
        }

        if ($ticket->video_path) {
            $this->deleteStoredMedia($ticket->video_path);
        }

        $ticket->delete();
        UserActivityLog::record(
            'ticket.deleted',
            'Deleted a maintenance ticket.',
            $user,
            $user,
            ['ticket_id' => $ticket->id, 'reference' => $ticket->ticket_id]
        );

        return redirect()->route('tickets.index')
            ->with('success', 'Ticket deleted successfully.');
    }

    public function updateStatus(Request $request, MaintenanceTicket $ticket)
    {
        $user = Auth::user();

        // Only handymen and managers can update status
        if ($user->role !== 'manager' && $user->role !== 'handyman') {
            abort(403);
        }

        // Handymen can only update assigned tickets
        if ($user->role === 'handyman' && $ticket->assigned_to !== $user->id) {
            abort(403);
        }

        $request->validate([
            'status' => ['required', Rule::in([
                MaintenanceTicket::STATUS_IN_PROGRESS,
                MaintenanceTicket::STATUS_RESOLVED,
            ])],
            'completion_note' => ['nullable', 'string', 'max:600'],
        ]);

        $allowedTransitions = [
            MaintenanceTicket::STATUS_ASSIGNED => [MaintenanceTicket::STATUS_IN_PROGRESS],
            MaintenanceTicket::STATUS_IN_PROGRESS => [MaintenanceTicket::STATUS_RESOLVED],
        ];

        if (! in_array($request->status, $allowedTransitions[$ticket->status] ?? [], true)) {
            return back()->with('error', 'That ticket status change is not available.');
        }

        $ticket->status = $request->status;

        if ($ticket->status === MaintenanceTicket::STATUS_IN_PROGRESS) {
            $startedAt = now();

            if (is_null($ticket->work_started_at)) {
                $ticket->work_started_at = $startedAt;
            }

            if (! $ticket->task_started_at) {
                $ticket->task_started_at = $startedAt;
            }
        }

        if ($ticket->status === MaintenanceTicket::STATUS_RESOLVED) {
            $completedAt = now();
            $ticket->work_started_at ??= $ticket->task_started_at ?? $completedAt;
            $ticket->task_started_at ??= $completedAt;
            $ticket->task_completed_at = $completedAt;
            $ticket->task_duration_minutes = max(0, ($ticket->work_started_at ?? $ticket->task_started_at)->diffInMinutes($completedAt));
            if ($request->filled('completion_note')) {
                $ticket->completion_note = strip_tags($request->input('completion_note'));
            }
            $ticket->resolved_at = $completedAt;
        } else {
            $ticket->resolved_at = null;
        }

        $ticket->save();
        UserActivityLog::record(
            'ticket.status_changed',
            'Changed a maintenance ticket status.',
            $ticket->user,
            $user,
            ['ticket_id' => $ticket->id, 'status' => $ticket->status]
        );

        if ($ticket->status === MaintenanceTicket::STATUS_RESOLVED) {
            $this->notifyTicketOwner($ticket, 'resolved');
        }

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Ticket status updated successfully!');
    }

    // Admin approves ticket and optionally assigns handyman in the same step (H-04).
    public function approve(Request $request, MaintenanceTicket $ticket)
    {
        $this->authorize('manage', MaintenanceTicket::class);

        if ($ticket->status !== MaintenanceTicket::STATUS_PENDING_APPROVAL) {
            return back()->with('error', 'Only tickets awaiting approval can be approved.');
        }

        $request->validate([
            'assigned_to' => [
                'nullable',
                Rule::exists('users', 'id')->where(fn ($q) => $q->where('role', 'handyman')->where('is_active', true)),
            ],
        ]);

        $handymanId = $request->filled('assigned_to') ? (int) $request->assigned_to : null;

        $ticket->forceFill([
            'status'      => $handymanId ? MaintenanceTicket::STATUS_ASSIGNED : MaintenanceTicket::STATUS_APPROVED,
            'assigned_to' => $handymanId,
        ])->save();

        UserActivityLog::record(
            'ticket.approved',
            'Approved a maintenance ticket.',
            $ticket->user,
            Auth::user(),
            ['ticket_id' => $ticket->id, 'assigned_to' => $handymanId]
        );
        $this->notifyTicketOwner($ticket, $handymanId ? 'assigned' : 'approved');

        $assignedStaffName = $handymanId
            ? User::whereKey($handymanId)->value('name')
            : null;

        $message = $handymanId
            ? 'Ticket approved and assigned. Task is now in the assigned queue for ' . ($assignedStaffName ?: 'the selected staff member') . '.'
            : 'Ticket approved. Assign staff when ready.';

        return redirect()->route('tickets.index')->with('success', $message);
    }

    // NEW: Admin rejects ticket with reason
    public function reject(Request $request, MaintenanceTicket $ticket)
    {
        $this->authorize('manage', MaintenanceTicket::class);

        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        if ($ticket->status !== MaintenanceTicket::STATUS_PENDING_APPROVAL) {
            return back()->with('error', 'Only tickets awaiting approval can be rejected.');
        }

        $ticket->status = MaintenanceTicket::STATUS_REJECTED;
        $ticket->rejection_reason = $request->rejection_reason;
        $ticket->save();
        UserActivityLog::record(
            'ticket.rejected',
            'Rejected a maintenance ticket.',
            $ticket->user,
            Auth::user(),
            ['ticket_id' => $ticket->id, 'reason' => $ticket->rejection_reason]
        );
        $this->notifyTicketOwner($ticket, 'rejected');

        return redirect()->route('tickets.index')
            ->with('warning', 'Ticket rejected. Resident was notified with the rejection reason.');
    }

    // Admin assigns handyman to approved ticket
    public function assign(Request $request, MaintenanceTicket $ticket)
    {
        $this->authorize('manage', MaintenanceTicket::class);

        if (! in_array($ticket->status, [MaintenanceTicket::STATUS_APPROVED, MaintenanceTicket::STATUS_ASSIGNED], true)) {
            return redirect()->back()
                ->with('error', 'Only approved or currently assigned tickets can be assigned.');
        }

        $request->validate([
            'assigned_to' => [
                'required',
                Rule::exists('users', 'id')->where(fn ($query) => $query
                    ->where('role', 'handyman')
                    ->where('is_active', true)),
            ],
        ]);

        $assignmentResult = DB::transaction(function () use ($request, $ticket) {
            $lockedTicket = MaintenanceTicket::query()->lockForUpdate()->findOrFail($ticket->id);

            if (! in_array($lockedTicket->status, [MaintenanceTicket::STATUS_APPROVED, MaintenanceTicket::STATUS_ASSIGNED], true)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'assigned_to' => 'This ticket is no longer available for assignment.',
                ]);
            }

            $wasReassignment = $lockedTicket->status === MaintenanceTicket::STATUS_ASSIGNED
                && (int) $lockedTicket->assigned_to !== (int) $request->assigned_to;

            $lockedTicket->assigned_to = $request->assigned_to;
            $lockedTicket->status = MaintenanceTicket::STATUS_ASSIGNED;
            $lockedTicket->save();

            UserActivityLog::record(
                'ticket.assigned',
                'Assigned a maintenance ticket to staff.',
                $lockedTicket->user,
                Auth::user(),
                ['ticket_id' => $lockedTicket->id, 'assigned_to' => $lockedTicket->assigned_to]
            );

            return [
                'staff_name' => $lockedTicket->assignedTo()->value('name') ?: 'the selected staff member',
                'was_reassignment' => $wasReassignment,
            ];
        });

        // Notify owner after transaction commits; refresh to get latest assigned_to value.
        $ticket->refresh();
        $this->notifyTicketOwner($ticket, 'assigned');

        $assignmentMessage = ($assignmentResult['was_reassignment'] ?? false)
            ? "Ticket reassigned. Task is now in the assigned queue for {$assignmentResult['staff_name']}."
            : "Ticket assigned. Task is now in the assigned queue for {$assignmentResult['staff_name']}.";

        return redirect()->route('tickets.index')
            ->with('success', $assignmentMessage);
    }

    public function cancel(MaintenanceTicket $ticket)
    {
        $this->authorize('manage', MaintenanceTicket::class);

        if (! $ticket->cancellation_requested_at || ! in_array($ticket->status, [
            MaintenanceTicket::STATUS_APPROVED,
            MaintenanceTicket::STATUS_ASSIGNED,
            MaintenanceTicket::STATUS_IN_PROGRESS,
        ], true)) {
            return back()->with('error', 'This ticket does not have an active cancellation request.');
        }

        $ticket->forceFill([
            'status' => MaintenanceTicket::STATUS_CANCELLED,
        ])->save();

        UserActivityLog::record(
            'ticket.cancelled',
            'Administration approved a resident cancellation request.',
            $ticket->user,
            Auth::user(),
            ['ticket_id' => $ticket->id, 'reason' => $ticket->cancellation_reason]
        );
        $this->notifyTicketOwner($ticket, 'cancelled');

        return redirect()->route('tickets.index')->with('success', 'Ticket cancellation approved.');
    }

    /**
     * Fire an email notification to the ticket owner.
     * Wrapped in try/catch so a misconfigured mailer never blocks the action.
     */
    private function notifyTicketOwner(MaintenanceTicket $ticket, string $event): void
    {
        try {
            $owner = $ticket->user ?? User::find($ticket->user_id);
            $owner?->notify(new TicketStatusChangedNotification($ticket, $event));
        } catch (\Throwable $e) {
            Log::warning('Ticket notification failed.', ['ticket_id' => $ticket->id, 'event' => $event, 'error' => $e->getMessage()]);
        }
    }

    private function deleteStoredMedia(string $path): void
    {
        Storage::disk('local')->delete($path);
        Storage::disk('public')->delete($path);
    }
}
