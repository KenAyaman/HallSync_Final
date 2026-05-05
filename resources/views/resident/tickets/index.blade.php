<x-app-layout>
    @php
        $activeTicketsList = $tickets->whereNotIn('status', ['completed', 'rejected']);
        $historyTicketsList = $tickets->whereIn('status', ['completed', 'rejected']);
        $visibleActiveTickets = $activeTicketsList->take(3);
        $hiddenActiveTickets = $activeTicketsList->slice(3);
        $visibleHistoryTickets = $historyTicketsList->take(3);
        $hiddenHistoryTickets = $historyTicketsList->slice(3);
        $openTickets = $activeTicketsList->count();
        $resolvedTickets = $tickets->where('status', 'completed')->count();
        $criticalTickets = $tickets->filter(fn ($ticket) => $ticket->normalized_priority === 'critical')->count();
    @endphp
    <div class="resident-page">
        <section class="resident-page-hero">
            <div class="resident-page-hero-copy">
                <p class="resident-page-kicker">Resident Maintenance Hub</p>
                <h1 class="resident-page-title">My Maintenance Tickets</h1>
                <p class="resident-page-subtitle">
                    Review every submitted concern, monitor progress updates, and keep your maintenance history in one clear timeline.
                </p>

                <div class="resident-hero-stat-row">
                    <div class="resident-hero-stat">
                        <span>Open</span>
                        <strong>{{ $openTickets }}</strong>
                    </div>
                    <div class="resident-hero-stat">
                        <span>Resolved</span>
                        <strong>{{ $resolvedTickets }}</strong>
                    </div>
                    <div class="resident-hero-stat">
                        <span>Priority</span>
                        <strong>{{ $criticalTickets }}</strong>
                    </div>
                </div>
            </div>

            <div class="resident-page-actions">
                <a href="{{ route('tickets.create') }}" class="resident-page-btn resident-page-btn-primary">Create New Ticket</a>
            </div>
        </section>

        @if(session('success'))
            <div class="resident-flash resident-flash-success" data-auto-dismiss>{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="resident-flash resident-flash-error" data-auto-dismiss>{{ session('error') }}</div>
        @endif

        <section class="resident-page-panel" data-filter-scope>
            <div class="resident-page-panel-head">
                <div>
                    <h2>Active Requests</h2>
                    <p>Your current maintenance concerns that are still waiting, assigned, or being worked on.</p>
                </div>
                <span class="resident-page-eyebrow">Current Queue</span>
            </div>

            <div class="resident-page-divider"></div>

            <div class="resident-filter-bar">
                <input type="search" class="resident-filter-input" placeholder="Search title, description, or ticket ID" data-filter-input data-filter-key="search">
                <select class="resident-filter-select" data-filter-select data-filter-key="status">
                    <option value="">All statuses</option>
                    <option value="received">Received</option>
                    <option value="pending">Pending</option>
                    <option value="assigned">Assigned</option>
                    <option value="in_progress">In Progress</option>
                </select>
                <select class="resident-filter-select" data-filter-select data-filter-key="priority">
                    <option value="">All priorities</option>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="critical">Critical</option>
                </select>
            </div>

            <div class="resident-ticket-section-head">
                <h3>Open and Ongoing</h3>
                <span>{{ $activeTicketsList->count() }} open</span>
            </div>

            <div class="feature-skeleton-stack" data-feature-skeleton>
                @for($i = 0; $i < 3; $i++)
                    <article class="resident-card feature-skeleton-card">
                        <div class="feature-skeleton-top">
                            <div>
                                <div class="feature-skeleton-title-row">
                                    <span class="feature-skeleton-line title"></span>
                                    <span class="feature-skeleton-pill"></span>
                                    <span class="feature-skeleton-pill"></span>
                                </div>
                                <span class="feature-skeleton-line long"></span>
                                <span class="feature-skeleton-line medium"></span>
                            </div>
                            <div class="feature-skeleton-actions">
                                <span class="feature-skeleton-button"></span>
                                <span class="feature-skeleton-button"></span>
                            </div>
                        </div>
                        <div class="feature-skeleton-meta">
                            <span class="feature-skeleton-box"></span>
                            <span class="feature-skeleton-box"></span>
                        </div>
                    </article>
                @endfor
            </div>

            <div class="resident-page-list" data-skeleton-content>
                @forelse($visibleActiveTickets as $ticket)
                    <article class="resident-card"
                             data-filter-card
                             data-search="{{ Str::lower($ticket->title . ' ' . $ticket->description . ' ' . $ticket->ticket_id) }}"
                             data-status="{{ $ticket->status }}"
                             data-priority="{{ $ticket->normalized_priority }}">
                        <div class="resident-card-top">
                            <div>
                                <div class="resident-card-heading">
                                    <h3>{{ $ticket->title }}</h3>
                                    <span class="resident-badge resident-badge-status-{{ $ticket->status }}">
                                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                    </span>
                                    <span class="resident-badge resident-badge-priority-{{ $ticket->normalized_priority }}">
                                        {{ $ticket->priority_label }}
                                    </span>
                                </div>
                                <p class="resident-card-description">{{ Str::limit($ticket->description, 160) }}</p>
                            </div>

                            <div class="resident-card-links">
                                @if(!in_array($ticket->status, ['in_progress', 'completed', 'rejected']))
                                    <a href="{{ route('tickets.edit', $ticket) }}">Edit</a>
                                @endif
                                <a href="{{ route('tickets.show', $ticket) }}">View Details</a>
                            </div>
                        </div>

                        @if($ticket->image_path || $ticket->video_path)
                            <div class="resident-media-row">
                                @if($ticket->image_path)
                                    <a href="{{ asset('storage/' . $ticket->image_path) }}" target="_blank" class="resident-ticket-thumb-link">
                                        <img src="{{ asset('storage/' . $ticket->image_path) }}" alt="Ticket attachment" class="resident-ticket-thumb">
                                    </a>
                                @endif

                                @if($ticket->video_path)
                                    <a href="{{ asset('storage/' . $ticket->video_path) }}" target="_blank" class="resident-media-chip">
                                        Video Attachment
                                    </a>
                                @endif
                            </div>
                        @endif

                        <div class="resident-card-meta-grid">
                            <div class="resident-meta-box">
                                <span>Ticket ID</span>
                                <strong>{{ $ticket->ticket_id }}</strong>
                            </div>
                            <div class="resident-meta-box">
                                <span>Submitted</span>
                                <strong>{{ $ticket->created_at->format('M d, Y h:i A') }}</strong>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="resident-empty-state">
                        <h3>No active tickets</h3>
                        <p>Your current maintenance concerns will appear here.</p>
                        <a href="{{ route('tickets.create') }}">Create Your First Ticket</a>
                    </div>
                @endforelse

                @if($hiddenActiveTickets->isNotEmpty())
                    <div id="resident-more-active" class="resident-more-list" style="display: none;">
                        @foreach($hiddenActiveTickets as $ticket)
                            <article class="resident-card"
                                     data-filter-card
                                     data-search="{{ Str::lower($ticket->title . ' ' . $ticket->description . ' ' . $ticket->ticket_id) }}"
                                     data-status="{{ $ticket->status }}"
                                     data-priority="{{ $ticket->normalized_priority }}">
                                <div class="resident-card-top">
                                    <div>
                                        <div class="resident-card-heading">
                                            <h3>{{ $ticket->title }}</h3>
                                            <span class="resident-badge resident-badge-status-{{ $ticket->status }}">
                                                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                            </span>
                                            <span class="resident-badge resident-badge-priority-{{ $ticket->normalized_priority }}">
                                                {{ $ticket->priority_label }}
                                            </span>
                                        </div>
                                        <p class="resident-card-description">{{ Str::limit($ticket->description, 160) }}</p>
                                    </div>

                                    <div class="resident-card-links">
                                        @if(!in_array($ticket->status, ['in_progress', 'completed', 'rejected']))
                                            <a href="{{ route('tickets.edit', $ticket) }}">Edit</a>
                                        @endif
                                        <a href="{{ route('tickets.show', $ticket) }}">View Details</a>
                                    </div>
                                </div>

                                @if($ticket->image_path || $ticket->video_path)
                                    <div class="resident-media-row">
                                        @if($ticket->image_path)
                                            <a href="{{ asset('storage/' . $ticket->image_path) }}" target="_blank" class="resident-ticket-thumb-link">
                                                <img src="{{ asset('storage/' . $ticket->image_path) }}" alt="Ticket attachment" class="resident-ticket-thumb">
                                            </a>
                                        @endif

                                        @if($ticket->video_path)
                                            <a href="{{ asset('storage/' . $ticket->video_path) }}" target="_blank" class="resident-media-chip">
                                                Video Attachment
                                            </a>
                                        @endif
                                    </div>
                                @endif

                                <div class="resident-card-meta-grid">
                                    <div class="resident-meta-box">
                                        <span>Ticket ID</span>
                                        <strong>{{ $ticket->ticket_id }}</strong>
                                    </div>
                                    <div class="resident-meta-box">
                                        <span>Submitted</span>
                                        <strong>{{ $ticket->created_at->format('M d, Y h:i A') }}</strong>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <button type="button" class="resident-see-more-btn" data-target="resident-more-active">See more</button>
                @endif

                <div class="resident-filter-empty" data-filter-empty>No active tickets match your filters.</div>
            </div>
        </section>

        <section class="resident-page-panel resident-page-panel-history" data-filter-scope>
            <div class="resident-page-panel-head">
                <div>
                    <h2>Past History</h2>
                    <p>Completed and closed tickets are stored here so they stay visually separate from active work.</p>
                </div>
                <span class="resident-page-eyebrow">Archive</span>
            </div>

            <div class="resident-page-divider"></div>

            <div class="resident-filter-bar">
                <input type="search" class="resident-filter-input" placeholder="Search history by title, description, or ticket ID" data-filter-input data-filter-key="search">
                <select class="resident-filter-select" data-filter-select data-filter-key="status">
                    <option value="">All statuses</option>
                    <option value="completed">Completed</option>
                    <option value="rejected">Rejected</option>
                </select>
                <select class="resident-filter-select" data-filter-select data-filter-key="priority">
                    <option value="">All priorities</option>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="critical">Critical</option>
                </select>
            </div>

            <div class="resident-ticket-section-head">
                <h3>Resolved and Archived</h3>
                <span>{{ $historyTicketsList->count() }} archived</span>
            </div>

            <div class="feature-skeleton-stack" data-feature-skeleton>
                @for($i = 0; $i < 2; $i++)
                    <article class="resident-card resident-card-history feature-skeleton-card">
                        <div class="feature-skeleton-top">
                            <div>
                                <div class="feature-skeleton-title-row">
                                    <span class="feature-skeleton-line title"></span>
                                    <span class="feature-skeleton-pill"></span>
                                    <span class="feature-skeleton-pill"></span>
                                </div>
                                <span class="feature-skeleton-line long"></span>
                                <span class="feature-skeleton-line short"></span>
                            </div>
                            <div class="feature-skeleton-actions">
                                <span class="feature-skeleton-button"></span>
                            </div>
                        </div>
                        <div class="feature-skeleton-meta">
                            <span class="feature-skeleton-box"></span>
                            <span class="feature-skeleton-box"></span>
                        </div>
                    </article>
                @endfor
            </div>

            <div class="resident-page-list" data-skeleton-content>
                @forelse($visibleHistoryTickets as $ticket)
                    <article class="resident-card resident-card-history"
                             data-filter-card
                             data-search="{{ Str::lower($ticket->title . ' ' . $ticket->description . ' ' . $ticket->ticket_id) }}"
                             data-status="{{ $ticket->status }}"
                             data-priority="{{ $ticket->normalized_priority }}">
                        <div class="resident-card-top">
                            <div>
                                <div class="resident-card-heading">
                                    <h3>{{ $ticket->title }}</h3>
                                    <span class="resident-badge resident-badge-status-{{ $ticket->status }}">
                                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                    </span>
                                    <span class="resident-badge resident-badge-priority-{{ $ticket->normalized_priority }}">
                                        {{ $ticket->priority_label }}
                                    </span>
                                </div>
                                <p class="resident-card-description">{{ Str::limit($ticket->description, 160) }}</p>
                            </div>

                            <div class="resident-card-links">
                                <a href="{{ route('tickets.show', $ticket) }}">View Details</a>
                            </div>
                        </div>

                        <div class="resident-card-meta-grid">
                            <div class="resident-meta-box">
                                <span>Ticket ID</span>
                                <strong>{{ $ticket->ticket_id }}</strong>
                            </div>
                            <div class="resident-meta-box">
                                <span>Closed</span>
                                <strong>{{ $ticket->updated_at->format('M d, Y h:i A') }}</strong>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="resident-empty-state resident-empty-state-compact">
                        <h3>No ticket history yet</h3>
                        <p>Completed and rejected tickets will move here.</p>
                    </div>
                @endforelse

                @if($hiddenHistoryTickets->isNotEmpty())
                    <div id="resident-more-history" class="resident-more-list" style="display: none;">
                        @foreach($hiddenHistoryTickets as $ticket)
                            <article class="resident-card resident-card-history"
                                     data-filter-card
                                     data-search="{{ Str::lower($ticket->title . ' ' . $ticket->description . ' ' . $ticket->ticket_id) }}"
                                     data-status="{{ $ticket->status }}"
                                     data-priority="{{ $ticket->normalized_priority }}">
                                <div class="resident-card-top">
                                    <div>
                                        <div class="resident-card-heading">
                                            <h3>{{ $ticket->title }}</h3>
                                            <span class="resident-badge resident-badge-status-{{ $ticket->status }}">
                                                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                            </span>
                                            <span class="resident-badge resident-badge-priority-{{ $ticket->normalized_priority }}">
                                                {{ $ticket->priority_label }}
                                            </span>
                                        </div>
                                        <p class="resident-card-description">{{ Str::limit($ticket->description, 160) }}</p>
                                    </div>

                                    <div class="resident-card-links">
                                        <a href="{{ route('tickets.show', $ticket) }}">View Details</a>
                                    </div>
                                </div>

                                <div class="resident-card-meta-grid">
                                    <div class="resident-meta-box">
                                        <span>Ticket ID</span>
                                        <strong>{{ $ticket->ticket_id }}</strong>
                                    </div>
                                    <div class="resident-meta-box">
                                        <span>Closed</span>
                                        <strong>{{ $ticket->updated_at->format('M d, Y h:i A') }}</strong>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <button type="button" class="resident-see-more-btn" data-target="resident-more-history">See more</button>
                @endif

                <div class="resident-filter-empty" data-filter-empty>No history items match your filters.</div>
            </div>
        </section>
    </div>

    <script>
        document.querySelectorAll('[data-auto-dismiss]').forEach((flash) => {
            setTimeout(() => {
                flash.style.transition = 'opacity 0.35s ease, transform 0.35s ease';
                flash.style.opacity = '0';
                flash.style.transform = 'translateY(-6px)';
                setTimeout(() => flash.remove(), 360);
            }, 3200);
        });

        document.querySelectorAll('.resident-see-more-btn').forEach((button) => {
            button.addEventListener('click', () => {
                const target = document.getElementById(button.dataset.target);
                if (!target) {
                    return;
                }

                const expanded = target.style.display !== 'none';
                if (expanded) {
                    target.style.display = 'none';
                    button.textContent = 'See more';
                } else {
                    target.style.display = 'flex';
                    button.textContent = 'Show Less';
                }
            });
        });
    </script>

    <style>
        .resident-page {
            max-width: 1600px;
            margin: 0 auto;
            padding: 24px 16px 32px;
            display: flex;
            flex-direction: column;
            gap: 22px;
        }

        .resident-page-hero,
        .resident-page-panel,
        .resident-flash {
            border: 1px solid rgba(214,168,91,0.14);
            box-shadow: 0 12px 24px rgba(0,0,0,0.14);
        }

        .resident-page-hero {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 20px;
            padding: 28px 30px;
            border-radius: 36px;
            background: linear-gradient(115deg, #1F2023 0%, #24262B 38%, #2C2C2F 62%, #3B3023 100%);
            box-shadow: 0 18px 50px rgba(0, 0, 0, 0.18);
        }

        .resident-page-hero-copy {
            max-width: 860px;
        }

        .resident-page-kicker {
            margin: 0 0 10px;
            color: #D2A04C;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.30em;
        }

        .resident-page-title {
            margin: 0;
            color: #F8F3EA;
            font-family: 'Playfair Display', serif;
            font-size: clamp(2.4rem, 4.6vw, 3.8rem);
            line-height: 1.05;
        }

        .resident-page-subtitle {
            margin: 12px 0 0;
            color: rgba(255,255,255,0.82);
            font-size: 1.02rem;
            line-height: 1.7;
            max-width: 760px;
        }

        .resident-hero-stat-row {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            margin-top: 22px;
        }

        .resident-hero-stat {
            min-width: 110px;
            padding: 12px 14px;
            border-radius: 16px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.07);
        }

        .resident-hero-stat span {
            display: block;
            color: #A89376;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.14em;
            font-weight: 700;
        }

        .resident-hero-stat strong {
            display: block;
            margin-top: 6px;
            color: #F0E9DF;
            font-size: 1.25rem;
            font-weight: 700;
        }

        .resident-page-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .resident-page-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 22px;
            border-radius: 999px;
            text-decoration: none;
            font-size: 0.92rem;
            font-weight: 700;
            transition: transform 0.2s ease;
        }

        .resident-page-btn:hover {
            transform: translateY(-1px);
        }

        .resident-page-btn-primary {
            background: linear-gradient(95deg, #b8842f, #d6a85b);
            color: #17120d;
        }

        .resident-flash {
            padding: 16px 20px;
            border-radius: 20px;
            font-size: 0.95rem;
            font-weight: 600;
            backdrop-filter: blur(10px);
        }

        .resident-flash-success {
            background: rgba(42,44,48,0.78);
            color: #F0E9DF;
        }

        .resident-flash-error {
            background: linear-gradient(180deg, rgba(53, 38, 35, 0.92) 0%, rgba(42, 31, 29, 0.92) 100%);
            color: #F0B3A9;
            border-color: rgba(224,112,96,0.22);
        }

        .resident-page-panel {
            padding: 26px 28px;
            border-radius: 20px;
            background: rgba(42,44,48,0.78);
            backdrop-filter: blur(10px);
        }

        .resident-page-panel-history {
            border-color: rgba(190,147,96,0.16);
            background: linear-gradient(180deg, rgba(42,44,48,0.82) 0%, rgba(35,36,39,0.88) 100%);
        }

        .resident-ticket-section-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
        }

        .resident-ticket-section-head h3 {
            margin: 0;
            color: #F0E9DF;
            font-size: 1.08rem;
            font-weight: 700;
        }

        .resident-ticket-section-head span {
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(214,168,91,0.10);
            border: 1px solid rgba(214,168,91,0.14);
            color: #D6A85B;
            font-size: 0.76rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .resident-page-panel-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 16px;
        }

        .resident-page-panel-head h2 {
            margin: 0;
            color: #F0E9DF;
            font-size: 1.5rem;
            font-family: 'Playfair Display', serif;
        }

        .resident-page-panel-head p {
            margin: 4px 0 0;
            color: #8A7A66;
            font-size: 0.95rem;
        }

        .resident-page-eyebrow {
            color: #D6A85B;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.16em;
        }

        .resident-page-divider {
            height: 1px;
            background: linear-gradient(to right, rgba(214,168,91,0.3), rgba(214,168,91,0.05), transparent);
            margin-bottom: 18px;
        }

        .resident-page-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .resident-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            padding: 22px;
            transition: transform 0.18s ease, border-color 0.18s ease;
        }

        .resident-card:hover {
            transform: translateY(-2px);
            border-color: rgba(214,168,91,0.18);
        }

        .resident-card-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 18px;
        }

        .resident-card-heading {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
        }

        .resident-card-heading h3 {
            margin: 0;
            color: #f0e9df;
            font-size: 1.05rem;
            font-weight: 700;
        }

        .resident-card-description {
            margin: 12px 0 0;
            color: #B8AB98;
            line-height: 1.65;
            font-size: 0.9rem;
        }

        .resident-card-links {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            gap: 14px;
        }

        .resident-card-links a,
        .resident-empty-state a {
            color: #d7b07a;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .resident-badge {
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 0.68rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .resident-badge-status-received,
        .resident-badge-status-pending {
            background: rgba(199, 151, 69, 0.18);
            color: #d7b07a;
        }

        .resident-badge-status-assigned,
        .resident-badge-status-in_progress {
            background: rgba(183, 147, 93, 0.22);
            color: #f0c37b;
        }

        .resident-badge-status-completed {
            background: rgba(255, 255, 255, 0.08);
            color: #beb1a0;
        }

        .resident-badge-priority-low {
            background: rgba(120, 170, 120, 0.16);
            color: #98c48b;
        }

        .resident-badge-priority-medium {
            background: rgba(199, 151, 69, 0.18);
            color: #d7b07a;
        }

        .resident-badge-priority-critical {
            background: rgba(185, 106, 93, 0.16);
            color: #dc9a86;
        }

        .resident-media-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            margin-top: 16px;
        }

        .resident-ticket-thumb-link {
            display: inline-flex;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.08);
            box-shadow: 0 10px 20px rgba(0,0,0,0.16);
        }

        .resident-ticket-thumb {
            width: 92px;
            height: 92px;
            object-fit: cover;
            display: block;
            background: rgba(18,20,23,0.55);
        }

        .resident-media-chip {
            padding: 9px 14px;
            border-radius: 999px;
            border: 1px solid rgba(214, 168, 91, 0.3);
            color: #e4d1b0;
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 600;
            background: rgba(255, 255, 255, 0.03);
        }

        .resident-more-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
            margin-top: 16px;
        }

        .resident-see-more-btn {
            margin-top: 16px;
            margin-left: auto;
            padding: 0;
            border: none;
            background: transparent;
            color: #D6A85B;
            font-weight: 700;
            cursor: pointer;
            display: block;
            text-align: right;
        }

        .resident-card-meta-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin-top: 18px;
        }

        .resident-meta-box {
            padding: 12px 14px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255,255,255,0.05);
        }

        .resident-meta-box span {
            display: block;
            color: #8A7A66;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .resident-meta-box strong {
            display: block;
            margin-top: 6px;
            color: #f0e9df;
            font-size: 0.86rem;
            font-weight: 600;
        }

        .resident-empty-state {
            padding: 34px 20px;
            border-radius: 16px;
            text-align: center;
            border: 1px solid rgba(255,255,255,0.05);
            background: rgba(255, 255, 255, 0.03);
        }

        .resident-empty-state h3 {
            margin: 0;
            color: #f0e9df;
            font-size: 1.3rem;
            font-family: 'Playfair Display', serif;
        }

        .resident-empty-state p {
            margin: 10px 0 18px;
            color: #B8AB98;
            font-size: 0.92rem;
        }

        @media (max-width: 768px) {
            .resident-page {
                padding: 18px 0 28px;
            }

            .resident-page-hero,
            .resident-page-panel {
                padding: 22px;
            }

            .resident-page-hero,
            .resident-card-top {
                flex-direction: column;
                align-items: flex-start;
            }

            .resident-card-links {
                justify-content: flex-start;
            }

            .resident-card-meta-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 560px) {
            .resident-page-title {
                font-size: 2.1rem;
            }

            .resident-page-subtitle {
                font-size: 0.95rem;
            }

            .resident-page-panel-head h2,
            .resident-ticket-section-head h3 {
                font-size: 1.2rem;
            }

            .resident-ticket-section-head {
                flex-direction: column;
                align-items: flex-start;
            }

            .resident-card,
            .resident-page-panel,
            .resident-page-hero {
                border-radius: 22px;
            }

            .resident-card-links,
            .resident-page-actions,
            .resident-page-btn {
                width: 100%;
            }

            .resident-card-links a,
            .resident-page-btn {
                width: 100%;
                text-align: center;
            }

            .resident-ticket-thumb {
                width: 76px;
                height: 76px;
            }
        }
    </style>
</x-app-layout>
