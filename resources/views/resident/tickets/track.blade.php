<x-app-layout>
    @php
        $isResolved = in_array($ticket->status, ['resolved', 'completed'], true);
        $canConfirmRepair = $isResolved
            && ($ticket->resolved_at ?? $ticket->updated_at)->gte(now()->subDays((int) config('hallsync.ticket_reopen_days', 7)));
        $terminalMessage = match ($ticket->status) {
            'rejected' => 'Administration declined this request. Open the full ticket record to review the reason.',
            'cancelled' => 'This request was cancelled and is no longer in the active maintenance queue.',
            default => null,
        };
    @endphp

    <div class="ticket-track-page">
        <div class="ticket-track-topbar">
            <a href="{{ route('tickets.index') }}" class="resident-back-link resident-create-back">← Back</a>
        </div>

        <section class="ticket-track-hero resident-hero-window-light">
            <div>
                <p class="ticket-track-kicker">Maintenance Progress</p>
                <h1>Track Your Request</h1>
                <p>Follow the maintenance workflow without the extra request details.</p>
            </div>
        </section>

        <section class="ticket-track-summary">
            <div>
                <span>Ticket ID</span>
                <strong>{{ $ticket->ticket_id }}</strong>
            </div>
            <div>
                <span>Current Status</span>
                <strong>{{ $ticket->status_label }}</strong>
            </div>
            <div>
                <span>Last Updated</span>
                <strong>{{ $ticket->updated_at->diffForHumans() }}</strong>
            </div>
            @if($ticket->assignedTo && in_array($ticket->status, ['assigned', 'in_progress', 'resolved', 'closed']))
                <div>
                    <span>Assigned Staff</span>
                    <strong>{{ $ticket->assignedTo->name }}</strong>
                </div>
            @endif
        </section>

        <section class="ticket-track-panel">
            <div class="ticket-track-panel-head">
                <div>
                    <h2>Ticket Tracker</h2>
                    <p>Each stage becomes highlighted as your request moves forward.</p>
                </div>
                <span class="ticket-track-status">{{ $ticket->status_label }}</span>
            </div>

            @if($terminalMessage)
                <div class="ticket-track-notice">{{ $terminalMessage }}</div>
            @endif

            <div class="ticket-track-progress">
                @foreach($ticket->tracker_steps as $step)
                    <div class="ticket-track-step {{ $step['complete'] ? 'is-complete' : '' }}">
                        <span class="ticket-track-dot"></span>
                        <strong>{{ $step['label'] }}</strong>
                    </div>
                @endforeach
            </div>
        </section>

        @if($canConfirmRepair)
            <section class="ticket-track-decision">
                <div>
                    <p class="ticket-track-kicker">Resident Confirmation</p>
                    <h2>Has the issue been fixed?</h2>
                    <p>Confirm the completed repair or return the ticket to the active queue if the issue remains.</p>
                </div>
                <div class="ticket-track-actions">
                    <form method="POST" action="{{ route('tickets.close', $ticket) }}" data-prevent-double-submit data-submitting-text="Closing Ticket...">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="ticket-track-btn ticket-track-btn-primary">Accept Repair and Close</button>
                    </form>
                    <form method="POST" action="{{ route('tickets.reopen', $ticket) }}" data-prevent-double-submit data-submitting-text="Reopening Ticket...">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="ticket-track-btn ticket-track-btn-secondary">Issue Still Exists</button>
                    </form>
                </div>
            </section>
        @endif

        <section class="ticket-track-panel">
            <div class="ticket-track-panel-head">
                <div>
                    <h2>Status Guide</h2>
                    <p>What each stage means as your request moves through the maintenance workflow.</p>
                </div>
            </div>

            <div class="ticket-track-guide">
                <article><strong>Submitted for Review</strong><p>Your concern is waiting for administration review.</p></article>
                <article><strong>Assigned to Staff</strong><p>A staff member has been assigned to handle the request.</p></article>
                <article><strong>Work in Progress</strong><p>Maintenance work is already underway.</p></article>
                <article><strong>Resolved</strong><p>Staff marked the work resolved. Confirm the repair or report that the issue remains.</p></article>
            </div>
        </section>
    </div>

</x-app-layout>

