<x-app-layout>
    <div class="resident-ticket-page">
        <section class="resident-ticket-hero">
            <div class="resident-ticket-hero-copy">
                <p class="resident-ticket-kicker">Maintenance Ticket Overview</p>
                <h1 class="resident-ticket-title">Ticket Details</h1>
                <p class="resident-ticket-subtitle">
                    Review your submitted concern, track its latest progress, and keep all supporting details in one clear place.
                </p>

                <div class="resident-ticket-hero-stats">
                    <div class="resident-ticket-hero-stat">
                        <span>Status</span>
                        <strong>{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</strong>
                    </div>
                    <div class="resident-ticket-hero-stat">
                        <span>Priority</span>
                        <strong>{{ ucfirst($ticket->priority) }}</strong>
                    </div>
                    <div class="resident-ticket-hero-stat">
                        <span>Ticket ID</span>
                        <strong>{{ $ticket->ticket_id }}</strong>
                    </div>
                </div>
            </div>

            <div class="resident-ticket-hero-actions">
                <a href="{{ route('tickets.index') }}" class="resident-ticket-btn resident-ticket-btn-secondary">Back to Tickets</a>
                @if(in_array($ticket->status, ['received', 'assigned']))
                    <a href="{{ route('tickets.edit', $ticket) }}" class="resident-ticket-btn resident-ticket-btn-primary">Edit Ticket</a>
                @endif
            </div>
        </section>

        <div class="resident-ticket-grid">
            <section class="resident-ticket-panel resident-ticket-main-panel">
                <div class="resident-ticket-panel-head">
                    <div>
                        <h2>Ticket Information</h2>
                        <p>The full details of your maintenance request and attached files.</p>
                    </div>

                    <div class="resident-ticket-chip-row">
                        <span class="resident-ticket-badge resident-ticket-badge-status-{{ $ticket->status }}">
                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                        </span>
                        <span class="resident-ticket-badge resident-ticket-badge-priority-{{ $ticket->priority }}">
                            {{ ucfirst($ticket->priority) }} Priority
                        </span>
                    </div>
                </div>

                <div class="resident-ticket-divider"></div>

                <div class="resident-ticket-detail-list">
                    <div class="resident-ticket-detail-box">
                        <span>Ticket ID</span>
                        <strong>{{ $ticket->ticket_id }}</strong>
                    </div>

                    <div class="resident-ticket-detail-box">
                        <span>Title</span>
                        <strong>{{ $ticket->title }}</strong>
                    </div>

                    <div class="resident-ticket-detail-box">
                        <span>Description</span>
                        <p>{{ $ticket->description }}</p>
                    </div>

                    @if($ticket->image_path)
                        <div class="resident-ticket-detail-box">
                            <span>Attached Image</span>
                            <a href="{{ asset('storage/' . $ticket->image_path) }}" target="_blank" class="resident-ticket-media-link">
                                <img src="{{ asset('storage/' . $ticket->image_path) }}" alt="Ticket attachment" class="resident-ticket-image">
                            </a>
                        </div>
                    @endif

                    @if($ticket->video_path)
                        <div class="resident-ticket-detail-box">
                            <span>Attached Video</span>
                            <video controls class="resident-ticket-video">
                                <source src="{{ asset('storage/' . $ticket->video_path) }}">
                            </video>
                        </div>
                    @endif

                    <div class="resident-ticket-meta-grid">
                        <div class="resident-ticket-detail-box">
                            <span>Submitted</span>
                            <strong>{{ $ticket->created_at->format('F d, Y h:i A') }}</strong>
                        </div>

                        <div class="resident-ticket-detail-box">
                            <span>Last Updated</span>
                            <strong>{{ $ticket->updated_at->format('F d, Y h:i A') }}</strong>
                        </div>
                    </div>
                </div>
            </section>

            <aside class="resident-ticket-sidebar">
                <section class="resident-ticket-panel">
                    <div class="resident-ticket-panel-head">
                        <div>
                            <h2>Status Guide</h2>
                            <p>What each stage means as your ticket moves forward.</p>
                        </div>
                    </div>

                    <div class="resident-ticket-divider"></div>

                    <div class="resident-ticket-guide-list">
                        <div class="resident-ticket-guide-item">
                            <strong>Received</strong>
                            <p>Your concern has been submitted and logged.</p>
                        </div>
                        <div class="resident-ticket-guide-item">
                            <strong>Assigned</strong>
                            <p>The ticket has been endorsed to the assigned staff or team.</p>
                        </div>
                        <div class="resident-ticket-guide-item">
                            <strong>In Progress</strong>
                            <p>Maintenance work is already underway.</p>
                        </div>
                        <div class="resident-ticket-guide-item">
                            <strong>Completed</strong>
                            <p>The concern has been resolved and marked finished.</p>
                        </div>
                    </div>
                </section>

                <section class="resident-ticket-panel">
                    <div class="resident-ticket-panel-head">
                        <div>
                            <h2>Quick Actions</h2>
                            <p>Manage this ticket or return to your ticket history.</p>
                        </div>
                    </div>

                    <div class="resident-ticket-divider"></div>

                    <div class="resident-ticket-action-list">
                        <a href="{{ route('tickets.index') }}" class="resident-ticket-text-link">Back to all tickets</a>
                        <a href="{{ route('tickets.create') }}" class="resident-ticket-text-link">Submit another ticket</a>

                        @if(in_array($ticket->status, ['received', 'assigned']))
                            <a href="{{ route('tickets.edit', $ticket) }}" class="resident-ticket-text-link">Edit this ticket</a>

                            <form method="POST" action="{{ route('tickets.destroy', $ticket) }}"
                                  onsubmit="return confirm('Are you sure you want to delete this ticket? This action cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="resident-ticket-delete-btn">Delete Ticket</button>
                            </form>

                            <p class="resident-ticket-note">
                                Only tickets marked as Received or Assigned can still be edited or removed.
                            </p>
                        @endif
                    </div>
                </section>
            </aside>
        </div>
    </div>

    <style>
        .resident-ticket-page {
            max-width: 1600px;
            margin: 0 auto;
            padding: 24px 16px 32px;
            display: flex;
            flex-direction: column;
            gap: 22px;
        }

        .resident-ticket-hero,
        .resident-ticket-panel {
            border: 1px solid rgba(214,168,91,0.14);
            box-shadow: 0 12px 24px rgba(0,0,0,0.14);
        }

        .resident-ticket-hero {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 20px;
            padding: 28px 30px;
            border-radius: 36px;
            background: linear-gradient(115deg, #1F2023 0%, #24262B 38%, #2C2C2F 62%, #3B3023 100%);
            box-shadow: 0 18px 50px rgba(0, 0, 0, 0.18);
        }

        .resident-ticket-hero-copy {
            max-width: 860px;
        }

        .resident-ticket-kicker {
            margin: 0 0 10px;
            color: #D2A04C;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.30em;
        }

        .resident-ticket-title {
            margin: 0;
            color: #F8F3EA;
            font-family: 'Playfair Display', serif;
            font-size: clamp(2.4rem, 4.6vw, 3.8rem);
            line-height: 1.05;
        }

        .resident-ticket-subtitle {
            margin: 12px 0 0;
            color: rgba(255,255,255,0.82);
            font-size: 1.02rem;
            line-height: 1.7;
            max-width: 760px;
        }

        .resident-ticket-hero-stats {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            margin-top: 22px;
        }

        .resident-ticket-hero-stat {
            min-width: 130px;
            padding: 12px 14px;
            border-radius: 16px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.07);
        }

        .resident-ticket-hero-stat span {
            display: block;
            color: #A89376;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.14em;
            font-weight: 700;
        }

        .resident-ticket-hero-stat strong {
            display: block;
            margin-top: 6px;
            color: #F0E9DF;
            font-size: 1rem;
            font-weight: 700;
        }

        .resident-ticket-hero-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            gap: 12px;
        }

        .resident-ticket-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 22px;
            border-radius: 999px;
            text-decoration: none;
            font-size: 0.92rem;
            font-weight: 700;
            transition: transform 0.2s ease, border-color 0.2s ease, background 0.2s ease;
        }

        .resident-ticket-btn:hover {
            transform: translateY(-1px);
        }

        .resident-ticket-btn-primary {
            background: linear-gradient(95deg, #b8842f, #d6a85b);
            color: #17120d;
        }

        .resident-ticket-btn-secondary {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(214,168,91,0.22);
            color: #F0E9DF;
        }

        .resident-ticket-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.15fr) minmax(320px, 0.85fr);
            gap: 24px;
            align-items: start;
        }

        .resident-ticket-sidebar {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .resident-ticket-panel {
            padding: 26px 28px;
            border-radius: 20px;
            background: rgba(42,44,48,0.78);
            backdrop-filter: blur(10px);
        }

        .resident-ticket-panel-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }

        .resident-ticket-panel-head h2 {
            margin: 0;
            color: #F0E9DF;
            font-size: 1.5rem;
            font-family: 'Playfair Display', serif;
        }

        .resident-ticket-panel-head p {
            margin: 4px 0 0;
            color: #8A7A66;
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .resident-ticket-divider {
            height: 1px;
            background: linear-gradient(to right, rgba(214,168,91,0.3), rgba(214,168,91,0.05), transparent);
            margin-bottom: 18px;
        }

        .resident-ticket-chip-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .resident-ticket-badge {
            padding: 6px 11px;
            border-radius: 999px;
            font-size: 0.68rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .resident-ticket-badge-status-received,
        .resident-ticket-badge-status-pending {
            background: rgba(199, 151, 69, 0.18);
            color: #d7b07a;
        }

        .resident-ticket-badge-status-assigned,
        .resident-ticket-badge-status-in_progress {
            background: rgba(183, 147, 93, 0.22);
            color: #f0c37b;
        }

        .resident-ticket-badge-status-completed {
            background: rgba(255, 255, 255, 0.08);
            color: #beb1a0;
        }

        .resident-ticket-badge-priority-low {
            background: rgba(120, 170, 120, 0.16);
            color: #98c48b;
        }

        .resident-ticket-badge-priority-medium {
            background: rgba(199, 151, 69, 0.18);
            color: #d7b07a;
        }

        .resident-ticket-badge-priority-high,
        .resident-ticket-badge-priority-urgent {
            background: rgba(185, 106, 93, 0.16);
            color: #dc9a86;
        }

        .resident-ticket-detail-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .resident-ticket-detail-box,
        .resident-ticket-guide-item {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 16px;
            padding: 16px 18px;
        }

        .resident-ticket-detail-box span,
        .resident-ticket-guide-item strong {
            display: block;
            color: #8A7A66;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.12em;
        }

        .resident-ticket-detail-box strong {
            display: block;
            margin-top: 8px;
            color: #F0E9DF;
            font-size: 0.95rem;
            line-height: 1.7;
            font-weight: 600;
        }

        .resident-ticket-detail-box p,
        .resident-ticket-guide-item p {
            margin: 10px 0 0;
            color: #B8AB98;
            font-size: 0.93rem;
            line-height: 1.75;
        }

        .resident-ticket-media-link {
            display: block;
            margin-top: 12px;
        }

        .resident-ticket-image,
        .resident-ticket-video {
            width: 100%;
            max-height: 340px;
            object-fit: cover;
            border-radius: 14px;
            border: 1px solid rgba(255,255,255,0.08);
            background: rgba(23,18,13,0.45);
        }

        .resident-ticket-meta-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .resident-ticket-guide-list,
        .resident-ticket-action-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .resident-ticket-guide-item strong {
            color: #D6A85B;
        }

        .resident-ticket-text-link {
            color: #d7b07a;
            text-decoration: none;
            font-size: 0.86rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .resident-ticket-delete-btn {
            width: 100%;
            padding: 12px 18px;
            border: 1px solid rgba(224,112,96,0.24);
            border-radius: 999px;
            background: linear-gradient(180deg, rgba(53, 38, 35, 0.92) 0%, rgba(42, 31, 29, 0.92) 100%);
            color: #F0B3A9;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.2s ease, border-color 0.2s ease;
        }

        .resident-ticket-delete-btn:hover {
            transform: translateY(-1px);
            border-color: rgba(224,112,96,0.38);
        }

        .resident-ticket-note {
            margin: 2px 0 0;
            color: #8A7A66;
            font-size: 0.8rem;
            line-height: 1.7;
        }

        @media (max-width: 1024px) {
            .resident-ticket-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .resident-ticket-page {
                padding: 18px 0 28px;
            }

            .resident-ticket-hero,
            .resident-ticket-panel {
                padding: 22px;
            }

            .resident-ticket-hero {
                flex-direction: column;
                align-items: flex-start;
            }

            .resident-ticket-hero-actions {
                width: 100%;
                justify-content: flex-start;
            }

            .resident-ticket-meta-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 560px) {
            .resident-ticket-btn {
                width: 100%;
            }
        }
    </style>
</x-app-layout>
