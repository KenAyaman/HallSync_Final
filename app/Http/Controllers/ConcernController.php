<?php

namespace App\Http\Controllers;

use App\Models\Concern;
use App\Models\ConcernEvidence;
use App\Models\User;
use App\Models\UserActivityLog;
use App\Notifications\ConcernUpdatedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ConcernController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        abort_unless($user->isResident(), 403);

        $concerns = Concern::query()
            ->where('user_id', $user->id)
            ->with(['handler', 'messages'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = '%' . trim((string) $request->search) . '%';
                $query->where(fn ($builder) => $builder
                    ->where('concern_id', 'like', $search)
                    ->orWhere('subject', 'like', $search)
                    ->orWhere('location', 'like', $search));
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->when($request->filled('category'), fn ($query) => $query->where('category', $request->category))
            ->when($request->filled('date'), fn ($query) => $query->whereDate('created_at', $request->date))
            ->latest()
            ->get();

        return view('resident.concerns.index', compact('concerns'));
    }

    public function create()
    {
        abort_unless(Auth::user()->isResident(), 403);

        return view('resident.concerns.create', [
            'concern' => new Concern(),
            'categories' => Concern::CATEGORIES,
            'prefillCategory' => request()->query('category', 'other'),
            'prefillLocation' => request()->query('location', ''),
            'prefillInvolvedPerson' => request()->query('involved_person', ''),
            'prefillDetails' => request()->query('details', ''),
            'contextTitle' => trim((string) request()->query('context_title', '')),
            'contextType' => trim((string) request()->query('context_type', '')),
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        abort_unless($user->isResident(), 403);

        $validated = $this->validateResidentConcern($request);
        $validated['subject'] ??= Concern::CATEGORIES[$validated['category']] ?? 'Resident Concern';
        $validated['incident_at'] ??= now();
        $isDraft = $request->input('action') === 'draft';

        // Retry up to 5 times on duplicate concern_id UNIQUE violations (H-06).
        $concern = null;
        $attempts = 0;
        while ($concern === null && $attempts < 5) {
            $attempts++;
            try {
                $concern = DB::transaction(function () use ($request, $validated, $isDraft, $user) {
                    $concern = Concern::create(array_merge($validated, [
                        'concern_id' => $this->nextConcernId(),
                        'user_id' => $user->id,
                        'priority' => $this->calculatePriority($validated['category'], $validated['location']),
                        'status' => $isDraft ? 'draft' : 'submitted',
                        'submitted_at' => $isDraft ? null : now(),
                        'due_at' => $isDraft ? null : $this->calculateDueAt($validated['category']),
                    ]));

                    $concern->statusHistories()->create([
                        'changed_by' => $user->id,
                        'to_status' => $concern->status,
                        'reason' => $isDraft ? 'Resident saved a draft.' : 'Resident submitted the concern.',
                    ]);
                    $concern->recordAudit($isDraft ? 'concern.drafted' : 'concern.submitted', $user);
                    $this->storeEvidence($request, $concern, $user);
                    UserActivityLog::record('concern.created', 'Created a private resident concern.', $user, $user, ['concern_id' => $concern->id]);

                    return $concern;
                });
            } catch (\Illuminate\Database\QueryException $e) {
                // 1062 = MySQL duplicate entry; 19 = SQLite UNIQUE constraint failed.
                if (! in_array((int) $e->errorInfo[1], [1062, 19], true) || $attempts >= 5) {
                    throw $e;
                }
                // else: retry with a fresh random ID
            }
        }

        return redirect()
            ->route('concerns.show', $concern)
            ->with('success', $isDraft ? 'Concern draft saved successfully.' : 'Concern submitted successfully. Administration has received your complaint.');
    }

    public function show(Concern $concern)
    {
        $this->authorizeResidentConcern($concern);
        $concern->load(['handler', 'messages.user', 'evidence.uploader', 'statusHistories.actor', 'assignments.assignee']);
        $concern->messages()->where('user_id', '!=', Auth::id())->whereNull('read_at')->update(['read_at' => now()]);
        $concern->recordAudit('concern.viewed', Auth::user());

        return view('resident.concerns.show', compact('concern'));
    }

    public function edit(Concern $concern)
    {
        $this->authorizeResidentConcern($concern);
        abort_unless($concern->isEditableByResident(), 403);

        return view('resident.concerns.create', [
            'concern' => $concern,
            'categories' => Concern::CATEGORIES,
            'prefillCategory' => $concern->category,
            'prefillLocation' => $concern->location,
            'prefillInvolvedPerson' => $concern->involved_person,
            'prefillDetails' => $concern->details,
        ]);
    }

    public function update(Request $request, Concern $concern)
    {
        $this->authorizeResidentConcern($concern);
        abort_unless($concern->isEditableByResident(), 403);

        $validated = $this->validateResidentConcern($request);
        $submitDraft = $concern->status === 'draft' && $request->input('action') !== 'draft';

        DB::transaction(function () use ($request, $concern, $validated, $submitDraft) {
            $concern->update(array_merge($validated, [
                'priority' => $this->calculatePriority($validated['category'], $validated['location']),
                'due_at' => $concern->due_at ?? $this->calculateDueAt($validated['category']),
            ]));
            $this->storeEvidence($request, $concern, Auth::user());
            $concern->recordAudit('concern.updated', Auth::user());

            if ($submitDraft) {
                $concern->transitionTo('submitted', Auth::user(), 'Resident submitted the saved draft.');
            }
        });

        return redirect()->route('concerns.show', $concern)->with('success', 'Concern updated.');
    }

    public function destroy(Concern $concern)
    {
        // Use the policy so authorization logic is in one place (M-09).
        $this->authorize('delete', $concern);

        foreach ($concern->evidence as $evidence) {
            Storage::disk($evidence->disk)->delete($evidence->path);
        }
        $concern->delete();

        return redirect()->route('concerns.index')->with('warning', 'Concern deleted successfully.');
    }

    public function addMessage(Request $request, Concern $concern)
    {
        $this->authorizeParticipant($concern);
        $validated = $request->validate(['message' => ['required', 'string', 'min:2', 'max:3000']]);
        $concern->messages()->create(['user_id' => Auth::id(), 'message' => trim($validated['message'])]);
        $concern->recordAudit('message.created', Auth::user());

        if (Auth::user()->isManager()) {
            $fromStatus = $concern->status;
            $concern->forceFill([
                'admin_reply'  => trim($validated['message']),
                'replied_at'   => now(),
                'handled_by'   => Auth::id(),
                'status'       => 'awaiting_resident_response',
            ])->save();
            $concern->statusHistories()->create([
                'changed_by'  => Auth::id(),
                'from_status' => $fromStatus,
                'to_status'   => 'awaiting_resident_response',
                'reason'      => 'Administration sent a message.',
            ]);
        }

        return back()->with('success', 'Private update added.');
    }

    public function addEvidence(Request $request, Concern $concern)
    {
        $this->authorizeParticipant($concern);
        $this->validateEvidence($request);
        $this->storeEvidence($request, $concern, Auth::user());
        $concern->recordAudit('evidence.added', Auth::user());

        return back()->with('success', 'Evidence added securely.');
    }

    public function downloadEvidence(Concern $concern, ConcernEvidence $evidence)
    {
        $this->authorizeParticipant($concern);
        abort_unless($evidence->concern_id === $concern->id, 404);
        abort_unless(Storage::disk($evidence->disk)->exists($evidence->path), 404);
        $concern->recordAudit('evidence.downloaded', Auth::user(), ['evidence_id' => $evidence->id]);

        return Storage::disk($evidence->disk)->download($evidence->path, $evidence->original_name);
    }

    public function residentDecision(Request $request, Concern $concern)
    {
        $this->authorizeResidentConcern($concern);
        abort_unless($concern->status === 'resolved', 403);
        $validated = $request->validate([
            'decision' => ['required', Rule::in(['accept', 'reopen'])],
            'reason' => ['required_if:decision,reopen', 'nullable', 'string', 'min:10', 'max:1000'],
        ]);

        if ($validated['decision'] === 'reopen') {
            abort_if($concern->reopen_count >= 2, 422, 'This concern has reached the reopen limit. Contact administration for further review.');
            $concern->increment('reopen_count');
            $concern->transitionTo('reopened', Auth::user(), $validated['reason']);
        } else {
            $concern->transitionTo('closed', Auth::user(), 'Resident accepted the resolution.');
        }

        return back()->with('success', $validated['decision'] === 'reopen' ? 'Concern reopened for review.' : 'Resolution accepted and concern closed.');
    }

    public function adminIndex(Request $request)
    {
        abort_unless(Auth::user()->isManager(), 403);

        $search   = trim((string) $request->get('search', ''));
        $status   = $request->get('status', 'all');
        $category = $request->get('category', 'all');

        $query = Concern::with('user')->latest();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('details', 'like', "%{$search}%")
                  ->orWhere('concern_id', 'like', "%{$search}%")
                  ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$search}%"));
            });
        }

        // Use status-based filtering instead of the legacy admin_reply null-check (M-02).
        if ($status === 'awaiting') {
            $query->whereIn('status', ['submitted', 'reopened']);
        } elseif ($status === 'replied') {
            $query->whereIn('status', ['awaiting_resident_response', 'responded', 'under_review', 'investigation_ongoing']);
        } elseif ($status === 'open') {
            $query->whereNotIn('status', ['closed', 'rejected']);
        } elseif ($status === 'closed') {
            $query->whereIn('status', ['closed', 'rejected']);
        }

        if ($category !== 'all') {
            $query->where('category', $category);
        }

        $concerns = $query->paginate(20)->withQueryString();

        // Compute all three summary-card stats in one query instead of three (M-04).
        // CASE WHEN is ANSI SQL and works in both MySQL and SQLite.
        $stats = Concern::toBase()->selectRaw("
            SUM(CASE WHEN status IN ('submitted', 'reopened') THEN 1 ELSE 0 END)          AS submitted_count,
            SUM(CASE WHEN status NOT IN ('closed', 'rejected') AND priority = 'urgent' THEN 1 ELSE 0 END) AS urgent_count,
            SUM(CASE WHEN status NOT IN ('closed', 'rejected') THEN 1 ELSE 0 END)         AS open_count
        ")->first();

        return view('admin.concerns.index', [
            'concerns'       => $concerns,
            'submittedCount' => (int) ($stats->submitted_count ?? 0),
            'urgentCount'    => (int) ($stats->urgent_count ?? 0),
            'openCount'      => (int) ($stats->open_count ?? 0),
            'filters'        => compact('search', 'status', 'category'),
            'categories'     => array_keys(Concern::CATEGORIES ?? []),
        ]);
    }

    public function adminShow(Concern $concern)
    {
        abort_unless(Auth::user()->isManager(), 403);
        $concern->load('user');
        $concern->recordAudit('concern.viewed', Auth::user());

        return view('admin.concerns.show', compact('concern'));
    }

    public function adminTransition(Request $request, Concern $concern)
    {
        abort_unless(Auth::user()->isManager(), 403);
        // 'responded' was retired (backfill migration 2026_06_02_010000). It remains in STATUSES for
        // display purposes only but is not a valid transition target — exclude it here (C-05).
        $validTargets = array_diff(array_keys(Concern::STATUSES), ['responded', 'draft']);
        $validated = $request->validate([
            'status' => ['required', Rule::in($validTargets)],
            'reason' => ['nullable', 'string', 'max:2000'],
            'resolution_notes' => ['required_if:status,resolved', 'nullable', 'string', 'max:3000'],
        ]);
        if ($validated['status'] === 'resolved') {
            $concern->resolution_notes = trim((string) $validated['resolution_notes']);
            $concern->save();
        }
        $concern->transitionTo($validated['status'], Auth::user(), $validated['reason'] ?? null);

        if (in_array($validated['status'], ['resolved', 'closed', 'rejected'], true)) {
            $this->notifyConcernOwner($concern, 'status_changed');
        }

        $flashKey = $validated['status'] === 'rejected' ? 'error' : 'success';
        return back()->with($flashKey, 'Concern status updated.');
    }

    public function adminUpdate(Request $request, Concern $concern)
    {
        abort_unless(Auth::user()->isManager(), 403);
        $validated = $request->validate(['admin_reply' => ['required', 'string', 'min:2', 'max:3000']]);
        $reply = trim($validated['admin_reply']);

        // Capture current status before mutating so the history entry is accurate (C-03).
        $fromStatus = $concern->status;

        $concern->messages()->create(['user_id' => Auth::id(), 'message' => $reply]);
        // Use 'awaiting_resident_response' — 'responded' was retired by the backfill migration (C-04).
        $concern->forceFill([
            'admin_reply' => $reply,
            'replied_at' => now(),
            'handled_by' => Auth::id(),
            'status' => 'awaiting_resident_response',
        ])->save();
        $concern->statusHistories()->create([
            'changed_by' => Auth::id(),
            'from_status' => $fromStatus,
            'to_status' => 'awaiting_resident_response',
            'reason' => 'Administration reply sent; awaiting resident response.',
        ]);
        $concern->recordAudit('message.created', Auth::user(), ['source' => 'admin_reply']);
        $this->notifyConcernOwner($concern, 'replied');

        return redirect()->route('admin.concerns.show', $concern)->with('success', 'Private resident update sent.');
    }

    public function assign(Request $request, Concern $concern)
    {
        abort_unless(Auth::user()->isManager(), 403);
        $validated = $request->validate([
            'assigned_to' => ['required', 'exists:users,id'],
            'assignment_role' => ['required', Rule::in(['Dorm Manager', 'Resident Affairs Officer', 'Security Personnel', 'Staff Member'])],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
        $assignee = User::findOrFail($validated['assigned_to']);
        abort_unless($assignee->is_active && in_array($assignee->role, ['manager', 'handyman'], true), 422);

        DB::transaction(function () use ($concern, $validated) {
            $concern->assignments()->whereNull('ended_at')->update(['ended_at' => now()]);
            $concern->assignments()->create(array_merge($validated, ['assigned_by' => Auth::id()]));
            $concern->update(['handled_by' => $validated['assigned_to']]);
            $concern->recordAudit('assignment.changed', Auth::user(), $validated);
        });

        return back()->with('success', 'Concern assignment recorded.');
    }

    public function addInternalNote(Request $request, Concern $concern)
    {
        abort_unless(Auth::user()->isManager(), 403);
        $validated = $request->validate(['note' => ['required', 'string', 'min:3', 'max:3000']]);
        $concern->internalNotes()->create(['author_id' => Auth::id(), 'note' => trim($validated['note'])]);
        $concern->recordAudit('internal_note.created', Auth::user());

        return back()->with('success', 'Internal note saved.');
    }

    private function validateResidentConcern(Request $request): array
    {
        $validated = $request->validate([
            'category' => ['required', Rule::in(array_keys(Concern::CATEGORIES))],
            'subject' => ['nullable', 'string', 'max:180'],
            'involved_person' => ['nullable', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'incident_at' => ['nullable', 'date', 'before_or_equal:now'],
            'details' => ['required', 'string', 'max:5000'],
            'is_anonymous' => ['nullable', 'boolean'],
        ]);
        $this->validateEvidence($request);
        $validated['is_anonymous'] = $request->boolean('is_anonymous');

        return $validated;
    }

    private function validateEvidence(Request $request): void
    {
        $request->validate([
            'evidence' => ['nullable', 'array', 'max:5'],
            'evidence.*' => ['file', 'mimes:jpg,jpeg,png,webp,mp4,mov,pdf', 'max:10240'],
        ]);
    }

    private function storeEvidence(Request $request, Concern $concern, User $user): void
    {
        foreach ($request->file('evidence', []) as $file) {
            $path = $file->store("concerns/{$concern->id}", 'local');
            $concern->evidence()->create([
                'uploaded_by' => $user->id,
                'disk' => 'local',
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
                'size' => $file->getSize(),
                'sha256' => hash_file('sha256', $file->getRealPath()),
            ]);
        }
    }

    private function calculatePriority(string $category, string $location): string
    {
        $priority = match ($category) {
            'safety', 'harassment' => 'urgent',
            'substance', 'policy' => 'high',
            'noise', 'roommate', 'facility_misuse' => 'medium',
            default => 'low',
        };

        $repeatLocationCount = Concern::query()
            ->where('location', $location)
            ->whereNotIn('status', ['closed', 'rejected'])
            ->count();

        return $repeatLocationCount >= 2 && $priority !== 'urgent' ? 'high' : $priority;
    }

    private function calculateDueAt(string $category)
    {
        return now()->addHours(in_array($category, ['safety', 'harassment'], true) ? 4 : 48);
    }

    /**
     * Generate a unique concern ID.
     * The do-while SELECT+INSERT check is not atomic, so under concurrent submissions two
     * requests can collide on the UNIQUE constraint. This is handled in store() by catching
     * the QueryException and retrying (H-06). The method itself just generates a candidate.
     */
    private function nextConcernId(): string
    {
        return 'CON-' . now()->format('ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
    }

    private function authorizeResidentConcern(Concern $concern): void
    {
        abort_unless(Auth::user()->isResident() && $concern->user_id === Auth::id(), 403);
    }

    private function authorizeParticipant(Concern $concern): void
    {
        $user = Auth::user();
        abort_unless($user->isManager() || ($user->isResident() && $concern->user_id === $user->id), 403);
    }

    /**
     * Fire an email notification to the concern owner.
     * Wrapped in try/catch so a misconfigured mailer never blocks the action.
     */
    private function notifyConcernOwner(Concern $concern, string $event): void
    {
        try {
            $owner = $concern->user ?? User::find($concern->user_id);
            $owner?->notify(new ConcernUpdatedNotification($concern, $event));
        } catch (\Throwable $e) {
            Log::warning('Concern notification failed.', ['concern_id' => $concern->id, 'event' => $event, 'error' => $e->getMessage()]);
        }
    }
}
