<x-app-layout>
    <div class="admin-ticket-show admin-detail-page">
        <x-admin-breadcrumb :crumbs="[
            ['label' => 'Maintenance Tickets', 'route' => 'tickets.index'],
            ['label' => $ticket->ticket_id],
        ]" />

        <section class="admin-ticket-show-hero admin-detail-hero">
            <div>
                <p class="admin-ticket-show-kicker">Ticket Review</p>
                <h1 class="admin-ticket-show-title">Maintenance Ticket Details</h1>
                <p class="admin-ticket-show-subtitle">
                    Review the request, check attachments, and update assignment or status from one admin workspace.
                </p>
            </div>

            <a href="{{ route('tickets.index') }}" class="admin-ticket-show-back">Back to Tickets</a>
        </section>

    <div class="admin-ticket-show-grid admin-ticket-show-grid-single">
            <section class="admin-ticket-show-panel admin-detail-panel">
                <div class="admin-ticket-show-panel-head">
                    <div>
                        <h2>Ticket Information</h2>
                        <p>Full details of the maintenance concern.</p>
                    </div>
                    <div class="admin-ticket-show-badges">
                        <span class="admin-ticket-badge admin-ticket-badge-status-{{ $ticket->status }}">
                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                        </span>
                        <span class="admin-ticket-badge admin-ticket-badge-priority-{{ $ticket->normalized_priority }}">
                            {{ $ticket->priority_label }}
                        </span>
                    </div>
                </div>

                <div class="admin-ticket-info-grid">
                    <article class="admin-ticket-info-card">
                        <span>Ticket ID</span>
                        <strong>{{ $ticket->ticket_id }}</strong>
                    </article>

                    <article class="admin-ticket-info-card">
                        <span>Submitted By</span>
                        <strong>{{ $ticket->user->name ?? 'Resident' }}</strong>
                    </article>

                    <article class="admin-ticket-info-card admin-ticket-info-card-wide">
                        <span>Title</span>
                        <strong>{{ $ticket->title }}</strong>
                    </article>

                    <article class="admin-ticket-info-card admin-ticket-info-card-wide">
                        <span>Description</span>
                        <p>{{ $ticket->description }}</p>
                    </article>

                    <article class="admin-ticket-info-card">
                        <span>Submitted</span>
                        <strong>{{ $ticket->created_at->format('F d, Y h:i A') }}</strong>
                    </article>

                    <article class="admin-ticket-info-card">
                        <span>Last Updated</span>
                        <strong>{{ $ticket->updated_at->format('F d, Y h:i A') }}</strong>
                    </article>

                    <article class="admin-ticket-info-card admin-ticket-info-card-wide">
                        <span>Assigned To</span>
                        <strong>{{ $ticket->assignedTo->name ?? 'Not assigned yet' }}</strong>
                    </article>

                    <article class="admin-ticket-info-card">
                        <span>Task Started</span>
                        <strong>{{ $ticket->task_started_at?->format('F d, Y h:i A') ?? 'Not started yet' }}</strong>
                    </article>

                    <article class="admin-ticket-info-card">
                        <span>Task Completed</span>
                        <strong>{{ $ticket->task_completed_at?->format('F d, Y h:i A') ?? 'Not completed yet' }}</strong>
                    </article>

                    <article class="admin-ticket-info-card admin-ticket-info-card-wide">
                        <span>Task Duration</span>
                        <strong>{{ $ticket->task_duration_label }}</strong>
                    </article>

                    @if($ticket->completion_note)
                        <div class="ticket-completion-note">
                            <span>Staff Completion Note</span>
                            <p>{{ $ticket->completion_note }}</p>
                        </div>
                    @endif

                    @if($ticket->image_path)
                        <article class="admin-ticket-info-card admin-ticket-info-card-wide">
                            <span>Attached Image</span>
                            <img src="{{ $ticket->image_url }}" alt="Ticket image" class="admin-ticket-media">
                        </article>
                    @endif

                    @if($ticket->video_path)
                        <article class="admin-ticket-info-card admin-ticket-info-card-wide">
                            <span>Attached Video</span>
                            <video controls class="admin-ticket-media">
                                <source src="{{ $ticket->video_url }}">
                            </video>
                        </article>
                    @endif
                </div>

                @if($ticket->work_started_at || $ticket->task_completed_at)
                <div class="handyman-time-tracking">
                    <h4 class="handyman-time-heading">Time Tracking</h4>
                    <div class="handyman-time-grid">

                        @if($ticket->work_started_at)
                        <div class="handyman-time-item">
                            <span class="handyman-time-label">Work Started</span>
                            <strong class="handyman-time-value">
                                {{ $ticket->work_started_at->format('M d, Y') }}
                            </strong>
                            <span class="handyman-time-sub">
                                {{ $ticket->work_started_at->format('h:i A') }}
                            </span>
                        </div>
                        @endif

                        @if($ticket->task_completed_at)
                        <div class="handyman-time-item">
                            <span class="handyman-time-label">Completed</span>
                            <strong class="handyman-time-value">
                                {{ $ticket->task_completed_at->format('M d, Y') }}
                            </strong>
                            <span class="handyman-time-sub">
                                {{ $ticket->task_completed_at->format('h:i A') }}
                            </span>
                        </div>
                        @endif

                        @if($ticket->work_started_at && $ticket->task_completed_at)
                        <div class="handyman-time-item handyman-time-duration">
                            <span class="handyman-time-label">Time Taken</span>
                            @php
                                $diff = $ticket->work_started_at
                                               ->diff($ticket->task_completed_at);
                                $hours   = ($diff->days * 24) + $diff->h;
                                $minutes = $diff->i;
                            @endphp
                            <strong class="handyman-time-value">
                                @if($hours > 0)
                                    {{ $hours }}h {{ $minutes }}m
                                @else
                                    {{ $minutes }}m
                                @endif
                            </strong>
                            <span class="handyman-time-sub">
                                from start to completion
                            </span>
                        </div>
                        @endif

                    </div>
                </div>
                @endif

                @if($ticket->cancellation_requested_at && $ticket->status !== 'cancelled')
                    <section class="admin-ticket-inline-action">
                        <div>
                            <h2>Cancellation Request</h2>
                            <p>{{ $ticket->cancellation_reason }}</p>
                        </div>
                        <form method="POST" action="{{ route('tickets.cancel', $ticket) }}"
                              data-confirm-message="Approve this cancellation? The ticket will be marked cancelled and the resident will be notified."
                              data-prevent-double-submit data-submitting-text="Approving…">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="admin-ticket-action admin-ticket-action-danger">Approve Cancellation</button>
                        </form>
                    </section>
                @endif
            </section>
        </div>
    </div>

    <style>
.ticket-completion-note, .resident-ticket-completion-note {
    margin-top: 16px;
    padding: 14px 16px;
    border-radius: 10px;
    background: var(--hs-surface, #f8f4ed);
    border: 1px solid var(--hs-border, #e8e0d5);
}
.ticket-completion-note span, .resident-ticket-completion-note span {
    display: block;
    font-size: .70rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: var(--hs-muted, #786b60);
    margin-bottom: 6px;
}
.ticket-completion-note p, .resident-ticket-completion-note p {
    margin: 0;
    font-size: .88rem;
    line-height: 1.6;
    color: var(--hs-text, #2c2419);
}
.admin-ticket-show {
    max-width: 1580px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 24px;
}
.admin-ticket-show-hero, .admin-ticket-show-panel {
    border: 1px solid rgba(214, 168, 91, 0.14);
    box-shadow: 0 14px 28px rgba(0, 0, 0, 0.14);
}
.admin-ticket-show-hero {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    gap: 24px;
    padding: 32px 36px;
    border-radius: 20px;
    background: linear-gradient(120deg, #111009 0%, #1c1a12 50%, #201e14 100%);
    position: relative;
    overflow: hidden;
}
.admin-ticket-show-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image: linear-gradient(rgba(214, 168, 91, 0.04) 1px, transparent 1px), linear-gradient(90deg, rgba(214, 168, 91, 0.04) 1px, transparent 1px);
    background-size: 48px 48px;
    pointer-events: none;
}
.admin-ticket-show-kicker {
    margin: 0 0 10px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #d6a85b;
    font-size: 0.875rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.18em;
    position: relative;
    z-index: 1;
}
.admin-ticket-show-kicker::before {
    content: '';
    width: 6px;
    height: 6px;
    border-radius: 999px;
    background: #d6a85b;
}
.admin-ticket-show-title {
    margin: 0;
    color: #f0e9df;
    font-family: 'Playfair Display', serif;
    font-size: clamp(2.3rem, 4vw, 3.3rem);
    line-height: 1.12;
    position: relative;
    z-index: 1;
}
.admin-ticket-show-subtitle {
    margin: 12px 0 0;
    color: rgba(255, 255, 255, 0.62);
    font-size: 1.05rem;
    max-width: 760px;
    position: relative;
    z-index: 1;
}
.admin-ticket-show-back {
    position: relative;
    z-index: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 12px 18px;
    border-radius: 999px;
    border: 1px solid rgba(214, 168, 91, 0.18);
    background: rgba(255, 255, 255, 0.04);
    color: #f0e9df;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 700;
}
.admin-ticket-show-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.15fr) minmax(300px, 0.85fr);
    gap: 24px;
}
.admin-ticket-show-grid-single {
    grid-template-columns: 1fr;
}
.admin-ticket-inline-action {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    margin-top: 16px;
    padding: 14px 15px;
    border: 1px solid rgba(224, 112, 96, 0.18);
    border-radius: 10px;
    background: rgba(224, 112, 96, 0.06);
}
.admin-ticket-inline-action h2 {
    margin: 0;
    color: #a3423b;
    font-family: 'Playfair Display', serif;
    font-size: 1.2rem;
}
.admin-ticket-inline-action p {
    margin: 4px 0 0;
    color: #786b60;
    font-size: 0.82rem;
    line-height: 1.55;
}
.admin-ticket-show-panel {
    border-radius: 20px;
    padding: 24px;
    background: rgba(42, 44, 48, 0.78);
    backdrop-filter: blur(10px);
}
.admin-ticket-show-panel-head {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 16px;
    margin-bottom: 18px;
}
.admin-ticket-show-panel-head h2 {
    margin: 0;
    color: #f0e9df;
    font-family: 'Playfair Display', serif;
    font-size: 1.45rem;
}
.admin-ticket-show-panel-head p {
    margin: 6px 0 0;
    color: #8a7a66;
    font-size: 0.95rem;
}
.admin-ticket-show-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}
.admin-ticket-badge {
    padding: 6px 10px;
    border-radius: 999px;
    font-size: 0.72rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.08em;
}
.admin-ticket-badge-status-pending_approval, .admin-ticket-badge-status-approved, .admin-ticket-badge-status-received {
    background: rgba(214, 168, 91, 0.16);
    color: #d6a85b;
}
.admin-ticket-badge-status-assigned, .admin-ticket-badge-status-in_progress {
    background: rgba(190, 147, 96, 0.16);
    color: #be9360;
}
.admin-ticket-badge-status-completed, .admin-ticket-badge-status-resolved, .admin-ticket-badge-status-closed {
    background: rgba(90, 138, 90, 0.16);
    color: #5a8a5a;
}
.admin-ticket-badge-status-rejected {
    background: rgba(224, 112, 96, 0.16);
    color: #e07060;
}
.admin-ticket-badge-priority-low {
    background: rgba(90, 138, 90, 0.16);
    color: #5a8a5a;
}
.admin-ticket-badge-priority-medium {
    background: rgba(214, 168, 91, 0.16);
    color: #d6a85b;
}
.admin-ticket-badge-priority-critical {
    background: rgba(224, 112, 96, 0.16);
    color: #e07060;
}
.admin-ticket-info-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
}
.admin-ticket-info-card {
    padding: 18px;
    border-radius: 18px;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.05);
}
.admin-ticket-info-card-wide {
    grid-column: 1 / -1;
}
.admin-ticket-info-card span {
    display: block;
    margin-bottom: 8px;
    color: #8a7a66;
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.12em;
}
.admin-ticket-info-card strong {
    color: #f0e9df;
    font-size: 1rem;
    line-height: 1.6;
}
.admin-ticket-info-card p {
    margin: 0;
    color: #c4b8a8;
    line-height: 1.7;
    font-size: 0.95rem;
}
.admin-ticket-media {
    width: 100%;
    border-radius: 16px;
    border: 1px solid rgba(255, 255, 255, 0.08);
}
.admin-ticket-form {
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.admin-ticket-form label {
    color: #c4b8a8;
    font-size: 0.9rem;
    font-weight: 600;
}
.admin-ticket-form select, .admin-ticket-form textarea {
    width: 100%;
    padding: 12px 14px;
    border-radius: 14px;
    border: 1px solid rgba(214, 168, 91, 0.14);
    background: rgba(37, 39, 42, 0.9);
    color: #f0e9df;
    font: inherit;
}
.admin-ticket-note {
    padding: 16px;
    border-radius: 16px;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.05);
}
.admin-ticket-note strong {
    display: block;
    color: #f0e9df;
    margin-bottom: 8px;
}
.admin-ticket-note p {
    margin: 0;
    color: #c4b8a8;
    line-height: 1.7;
}
.admin-ticket-note-danger {
    border-color: rgba(224, 112, 96, 0.18);
    background: rgba(224, 112, 96, 0.06);
}
.admin-ticket-summary-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.admin-ticket-summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding: 14px 16px;
    border-radius: 16px;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.05);
}
.admin-ticket-summary-row span {
    color: #8a7a66;
    font-size: 0.85rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
}
.admin-ticket-summary-row strong {
    color: #f0e9df;
    font-size: 0.95rem;
    text-align: right;
}
.admin-ticket-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 12px 16px;
    border-radius: 14px;
    text-decoration: none;
    border: 1px solid rgba(214, 168, 91, 0.14);
    font-size: 0.9rem;
    font-weight: 700;
    cursor: pointer;
}
.admin-ticket-action-primary {
    background: linear-gradient(135deg, #c79745, #d6a85b);
    color: #18130e;
}
.admin-ticket-action-secondary {
    background: rgba(255, 255, 255, 0.04);
    color: #f0e9df;
}
.admin-ticket-action-danger {
    background: rgba(224, 112, 96, 0.12);
    border-color: rgba(224, 112, 96, 0.18);
    color: #f0b2a7;
}
.handyman-time-tracking {
    margin-top: 24px;
    padding-top: 20px;
    border-top: 1px solid var(--color-border, #e8e0d5);
}
.handyman-time-heading {
    font-size: .72rem;
    font-weight: 700;
    letter-spacing: .09em;
    text-transform: uppercase;
    color: var(--color-text-secondary, #786b60);
    margin-bottom: 14px;
}
.handyman-time-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 16px;
}
.handyman-time-item {
    display: flex;
    flex-direction: column;
    gap: 3px;
}
.handyman-time-label {
    font-size: .70rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .07em;
    color: var(--color-text-secondary, #786b60);
}
.handyman-time-value {
    font-size: 1.05rem;
    font-weight: 800;
    color: #F4ECDC;
}
.handyman-time-sub {
    font-size: .75rem;
    color: var(--color-text-secondary, #786b60);
}
.handyman-time-duration .handyman-time-value {
    color: #4f805c;
}
@media (max-width:980px) {
    .admin-ticket-show-grid {
        grid-template-columns: 1fr;
    }
}
@media (max-width:768px) {
    .admin-ticket-show {
        gap: 16px;
    }
    .admin-ticket-show-hero {
        padding: 24px;
        flex-direction: column;
        align-items: flex-start;
    }
    .admin-ticket-info-grid {
        grid-template-columns: 1fr;
    }
    .admin-ticket-inline-action {
        align-items: flex-start;
        flex-direction: column;
    }
    .handyman-time-grid {
        grid-template-columns: 1fr;
    }
}
</style>
</x-app-layout>
