<?php

namespace App\Http\Controllers;

use App\Models\Concern;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConcernController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        abort_unless($user->role === 'resident', 403);

        $concerns = Concern::where('user_id', $user->id)
            ->latest()
            ->get();

        return view('resident.concerns.index', compact('concerns'));
    }

    public function create()
    {
        abort_unless(Auth::user()->role === 'resident', 403);

        $contextTitle = trim((string) request()->query('context_title', ''));
        $contextType = trim((string) request()->query('context_type', ''));

        return view('resident.concerns.create', [
            'prefillCategory' => request()->query('category', 'other'),
            'prefillLocation' => request()->query('location', ''),
            'prefillInvolvedPerson' => request()->query('involved_person', ''),
            'prefillDetails' => request()->query('details', ''),
            'contextTitle' => $contextTitle,
            'contextType' => $contextType,
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(Auth::user()->role === 'resident', 403);

        $request->validate([
            'category' => ['required', 'string', 'in:roommate,noise,cleanliness,safety,policy,shared_space,other'],
            'involved_person' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'details' => ['required', 'string', 'min:12', 'max:3000'],
        ]);

        $categoryLabel = match ($request->category) {
            'roommate' => 'Roommate Conflict',
            'noise' => 'Noise or Disturbance',
            'cleanliness' => 'Cleanliness or Hygiene',
            'safety' => 'Safety Concern',
            'policy' => 'Policy Violation',
            'shared_space' => 'Shared Space Issue',
            default => 'Resident Concern',
        };

        $concern = Concern::create([
            'user_id' => Auth::id(),
            'category' => $request->category,
            'subject' => $categoryLabel,
            'involved_person' => $request->involved_person,
            'location' => $request->location,
            'details' => $request->details,
            'status' => 'submitted',
        ]);

        return redirect()
            ->route('concerns.show', $concern)
            ->with('success', 'Concern report submitted privately to administration.');
    }

    public function show(Concern $concern)
    {
        $user = Auth::user();

        abort_unless($user->role === 'resident' && $concern->user_id === $user->id, 403);

        return view('resident.concerns.show', compact('concern'));
    }

    public function adminIndex()
    {
        abort_unless(Auth::user()->role === 'manager', 403);

        $concerns = Concern::with(['user', 'handler'])
            ->latest()
            ->get();

        $submittedCount = $concerns->where('status', 'submitted')->count();
        $reviewCount = $concerns->where('status', 'in_review')->count();
        $respondedCount = $concerns->where('status', 'responded')->count();
        $closedCount = $concerns->where('status', 'closed')->count();

        return view('admin.concerns.index', compact(
            'concerns',
            'submittedCount',
            'reviewCount',
            'respondedCount',
            'closedCount'
        ));
    }

    public function adminShow(Concern $concern)
    {
        abort_unless(Auth::user()->role === 'manager', 403);

        $concern->load(['user', 'handler']);

        return view('admin.concerns.show', compact('concern'));
    }

    public function adminUpdate(Request $request, Concern $concern)
    {
        abort_unless(Auth::user()->role === 'manager', 403);

        $request->validate([
            'admin_reply' => ['required', 'string', 'max:3000'],
        ]);

        $reply = trim((string) $request->admin_reply);

        $concern->admin_reply = $reply;
        $concern->handled_by = Auth::id();
        $concern->replied_at = now();
        $concern->status = 'responded';

        $concern->save();

        return redirect()
            ->route('admin.concerns.show', $concern)
            ->with('success', 'Concern updated successfully.');
    }
}
