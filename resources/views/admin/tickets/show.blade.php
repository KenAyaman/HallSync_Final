<x-app-layout>
    <div class="admin-ticket-show">
        <section class="admin-ticket-show-hero">
            <div>
                <p class="admin-ticket-show-kicker">Ticket Review</p>
                <h1 class="admin-ticket-show-title">Maintenance Ticket Details</h1>
                <p class="admin-ticket-show-subtitle">
                    Review the request, check attachments, and update assignment or status from one admin workspace.
                </p>
            </div>

            <a href="{{ route('tickets.index') }}" class="admin-ticket-show-back">Back to Tickets</a>
        </section>

        <div class="admin-ticket-show-grid">
            <section class="admin-ticket-show-panel">
                <div class="admin-ticket-show-panel-head">
                    <div>
                        <h2>Ticket Information</h2>
                        <p>Full details of the maintenance concern.</p>
                    </div>
                    <div class="admin-ticket-show-badges">
                        <span class="admin-ticket-badge admin-ticket-badge-status-{{ $ticket->status }}">
                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                        </span>
                        <span class="admin-ticket-badge admin-ticket-badge-priority-{{ $ticket->priority }}">
                            {{ ucfirst($ticket->priority) }} Priority
                        </span>
                    </div>
                </div>

                <div class="admin-ticket-info-grid">
                    <article class="admin-ticket-info-card">
                        <span>Ticket ID</span>
                        <strong>{{ $ticket->ticket_id }}</strong>
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
                        <span>Submitted By</span>
                        <strong>{{ $ticket->user->name ?? 'Resident' }}</strong>
                    </article>

                    <article class="admin-ticket-info-card">
                        <span>Submitted</span>
                        <strong>{{ $ticket->created_at->format('F d, Y h:i A') }}</strong>
                    </article>

                    <article class="admin-ticket-info-card">
                        <span>Last Updated</span>
                        <strong>{{ $ticket->updated_at->format('F d, Y h:i A') }}</strong>
                    </article>

                    <article class="admin-ticket-info-card">
                        <span>Assigned To</span>
                        <strong>{{ $ticket->assignedTo->name ?? 'Not assigned yet' }}</strong>
                    </article>

                    @if($ticket->image_path)
                        <article class="admin-ticket-info-card admin-ticket-info-card-wide">
                            <span>Attached Image</span>
                            <img src="{{ asset('storage/' . $ticket->image_path) }}" alt="Ticket image" class="admin-ticket-media">
                        </article>
                    @endif

                    @if($ticket->video_path)
                        <article class="admin-ticket-info-card admin-ticket-info-card-wide">
                            <span>Attached Video</span>
                            <video controls class="admin-ticket-media">
                                <source src="{{ asset('storage/' . $ticket->video_path) }}">
                            </video>
                        </article>
                    @endif
                </div>
            </section>

            <aside class="admin-ticket-show-sidebar">
                <section class="admin-ticket-show-panel">
                    <div class="admin-ticket-show-panel-head">
                        <div>
                            <h2>Assignment</h2>
                            <p>Assign or reassign this ticket.</p>
                        </div>
                    </div>

                    @if($ticket->status === 'approved')
                        <form method="POST" action="{{ route('tickets.assign', $ticket) }}" class="admin-ticket-form">
                            @csrf
                            <label for="assigned_to">Select Staff</label>
                            <select name="assigned_to" id="assigned_to" required>
                                <option value="">Choose staff...</option>
                                @foreach($handymen as $handyman)
                                    <option value="{{ $handyman->id }}" @selected($ticket->assigned_to === $handyman->id)>
                                        {{ $handyman->name }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="admin-ticket-action admin-ticket-action-primary">Assign Ticket</button>
                        </form>
                    @elseif($ticket->status === 'pending_approval')
                        <form method="POST" action="{{ route('tickets.approve', $ticket) }}" class="admin-ticket-form">
                            @csrf
                            <label for="approve_assigned_to">Approve and assign</label>
                            <select name="assigned_to" id="approve_assigned_to">
                                <option value="">Approve without assigning</option>
                                @foreach($handymen as $handyman)
                                    <option value="{{ $handyman->id }}">{{ $handyman->name }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="admin-ticket-action admin-ticket-action-primary">Approve Ticket</button>
                        </form>
                    @else
                        <div class="admin-ticket-note">
                            <strong>Current Assignee</strong>
                            <p>{{ $ticket->assignedTo->name ?? 'No staff assigned yet.' }}</p>
                        </div>
                    @endif
                </section>

                @if($ticket->status === 'pending_approval')
                    <section class="admin-ticket-show-panel">
                        <div class="admin-ticket-show-panel-head">
                            <div>
                                <h2>Rejection</h2>
                                <p>Decline the request with a reason.</p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('tickets.reject', $ticket) }}" class="admin-ticket-form">
                            @csrf
                            <label for="rejection_reason">Reason for rejection</label>
                            <textarea name="rejection_reason" id="rejection_reason" rows="4" required>{{ old('rejection_reason', $ticket->rejection_reason) }}</textarea>
                            <button type="submit" class="admin-ticket-action admin-ticket-action-danger">Reject Ticket</button>
                        </form>
                    </section>
                @elseif($ticket->rejection_reason)
                    <section class="admin-ticket-show-panel">
                        <div class="admin-ticket-show-panel-head">
                            <div>
                                <h2>Rejection Reason</h2>
                                <p>Recorded admin response.</p>
                            </div>
                        </div>

                        <div class="admin-ticket-note admin-ticket-note-danger">
                            <p>{{ $ticket->rejection_reason }}</p>
                        </div>
                    </section>
                @endif

                <section class="admin-ticket-show-panel">
                    <div class="admin-ticket-show-panel-head">
                        <div>
                            <h2>Admin Summary</h2>
                            <p>Current handling status for this request.</p>
                        </div>
                    </div>

                    <div class="admin-ticket-summary-list">
                        <div class="admin-ticket-summary-row">
                            <span>Status</span>
                            <strong>{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</strong>
                        </div>
                        <div class="admin-ticket-summary-row">
                            <span>Priority</span>
                            <strong>{{ ucfirst($ticket->priority) }}</strong>
                        </div>
                        <div class="admin-ticket-summary-row">
                            <span>Assigned To</span>
                            <strong>{{ $ticket->assignedTo->name ?? 'Pending assignment' }}</strong>
                        </div>
                        <div class="admin-ticket-summary-row">
                            <span>Resident</span>
                            <strong>{{ $ticket->user->name ?? 'Resident' }}</strong>
                        </div>
                    </div>
                </section>
            </aside>
        </div>
    </div>

    <style>
        .admin-ticket-show {
            max-width: 1580px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .admin-ticket-show-hero,
        .admin-ticket-show-panel {
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
            background-image:
                linear-gradient(rgba(214,168,91,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(214,168,91,0.04) 1px, transparent 1px);
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
            color: rgba(255,255,255,0.62);
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
            border: 1px solid rgba(214,168,91,0.18);
            background: rgba(255,255,255,0.04);
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

        .admin-ticket-show-sidebar {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .admin-ticket-show-panel {
            border-radius: 20px;
            padding: 24px;
            background: rgba(42,44,48,0.78);
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

        .admin-ticket-badge-status-pending_approval,
        .admin-ticket-badge-status-approved,
        .admin-ticket-badge-status-received {
            background: rgba(214,168,91,0.16);
            color: #d6a85b;
        }

        .admin-ticket-badge-status-assigned,
        .admin-ticket-badge-status-in_progress {
            background: rgba(190,147,96,0.16);
            color: #be9360;
        }

        .admin-ticket-badge-status-completed {
            background: rgba(90,138,90,0.16);
            color: #5a8a5a;
        }

        .admin-ticket-badge-status-rejected {
            background: rgba(224,112,96,0.16);
            color: #e07060;
        }

        .admin-ticket-badge-priority-low {
            background: rgba(90,138,90,0.16);
            color: #5a8a5a;
        }

        .admin-ticket-badge-priority-medium {
            background: rgba(214,168,91,0.16);
            color: #d6a85b;
        }

        .admin-ticket-badge-priority-high,
        .admin-ticket-badge-priority-urgent {
            background: rgba(224,112,96,0.16);
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
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.05);
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
            border: 1px solid rgba(255,255,255,0.08);
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

        .admin-ticket-form select,
        .admin-ticket-form textarea {
            width: 100%;
            padding: 12px 14px;
            border-radius: 14px;
            border: 1px solid rgba(214,168,91,0.14);
            background: rgba(37,39,42,0.9);
            color: #f0e9df;
            font: inherit;
        }

        .admin-ticket-note {
            padding: 16px;
            border-radius: 16px;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.05);
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
            border-color: rgba(224,112,96,0.18);
            background: rgba(224,112,96,0.06);
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
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.05);
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
            border: 1px solid rgba(214,168,91,0.14);
            font-size: 0.9rem;
            font-weight: 700;
            cursor: pointer;
        }

        .admin-ticket-action-primary {
            background: linear-gradient(135deg, #c79745, #d6a85b);
            color: #18130e;
        }

        .admin-ticket-action-secondary {
            background: rgba(255,255,255,0.04);
            color: #f0e9df;
        }

        .admin-ticket-action-danger {
            background: rgba(224,112,96,0.12);
            border-color: rgba(224,112,96,0.18);
            color: #f0b2a7;
        }

        @media (max-width: 980px) {
            .admin-ticket-show-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
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
        }
    </style>
</x-app-layout>
