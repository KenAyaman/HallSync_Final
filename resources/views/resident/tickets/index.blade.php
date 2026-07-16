<x-app-layout>
    @php
        $activeTicketsList = $tickets->whereIn('status', ['pending_approval', 'approved']);
        $historyTicketsList = $tickets->whereIn('status', ['closed', 'cancelled', 'rejected']);
        $visibleActiveTickets = $activeTicketsList->take(3);
        $hiddenActiveTickets = $activeTicketsList->slice(3);
        $visibleHistoryTickets = $historyTicketsList->take(3);
        $hiddenHistoryTickets = $historyTicketsList->slice(3);
        $openTickets = $activeTicketsList->count();
        $closedTickets = $tickets->where('status', 'closed')->count();
        $criticalTickets = $tickets->filter(fn ($ticket) => $ticket->normalized_priority === 'critical')->count();
        $assignedTicketsList = $tickets->whereIn('status', ['assigned', 'in_progress']);
        $finishedTicketsList = $tickets->whereIn('status', ['resolved', 'closed']);
        $rejectedTicketsList = $tickets->where('status', 'rejected');
    @endphp
<div class="resident-page hs-ticket-page">
        <section class="resident-page-hero resident-hero-window-light hs-ticket-hero">
            <div class="resident-page-hero-copy">
                <p class="resident-page-kicker">Resident Maintenance Hub</p>
                <h1 class="resident-page-title">My Maintenance Tickets</h1>
                <p class="resident-page-subtitle">
                    Review every submitted concern, monitor progress updates, and keep your maintenance history in one clear timeline.
                </p>

                <div class="resident-hero-stat-row hs-card">
                    <button type="button" class="resident-hero-stat hs-btn" data-ticket-filter="open">
                        <span>Open</span>
                        <strong>{{ $openTickets }}</strong>
                    </button>
                    <button type="button" class="resident-hero-stat hs-btn" data-ticket-filter="closed">
                        <span>Closed</span>
                        <strong>{{ $closedTickets }}</strong>
                    </button>
                    <button type="button" class="resident-hero-stat hs-btn" data-ticket-filter="critical">
                        <span>Priority</span>
                        <strong>{{ $criticalTickets }}</strong>
                    </button>
                </div>
            </div>

            <div class="resident-page-actions">
                <a href="{{ route('tickets.create') }}" class="resident-page-btn resident-page-btn-primary">Create New Ticket</a>
            </div>
        </section>

        <section class="resident-page-panel hs-ticket-panel" data-filter-scope>
            <div class="resident-page-panel-head">
                <div>
                    <h2>Active Requests</h2>
                    <p>Requests waiting for admin review or assignment. Once assigned, they move to the Assigned tab below.</p>
                </div>
                <span class="resident-page-eyebrow">Current Queue</span>
            </div>

            <div class="resident-page-divider"></div>

            <div class="resident-ticket-section-head">
                <h3>Waiting for Action</h3>
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
                    <article class="resident-card resident-card-active"
                             data-filter-card
                             data-search="{{ Str::lower($ticket->title . ' ' . $ticket->description . ' ' . $ticket->ticket_id) }}"
                             data-status="{{ $ticket->status }}"
                             data-priority="{{ $ticket->normalized_priority }}">
                        <div class="resident-card-top">
                            <div>
                                <div class="resident-card-heading">
                                    <h3>{{ $ticket->title }}</h3>
                                    <span class="resident-badge resident-badge-status-{{ $ticket->status }}">
                                        {{ $ticket->status_label }}
                                    </span>
                                    <span class="resident-badge resident-badge-priority-{{ $ticket->normalized_priority }}">
                                        {{ $ticket->priority_label }}
                                    </span>
                                </div>
                                <p class="resident-card-description">{{ Str::limit($ticket->description, 160) }}</p>
                            </div>

                            <div class="resident-card-links resident-card-action-bar" aria-label="Ticket actions">
                                <a href="{{ route('tickets.track', $ticket) }}">Track</a>
                                @if($ticket->status === 'pending_approval')
                                    <a href="{{ route('tickets.edit', $ticket) }}">Edit</a>
                                @endif
                                <a href="{{ route('tickets.show', $ticket) }}">Details</a>
                            </div>
                        </div>

                        @if($ticket->image_path || $ticket->video_path)
                            <div class="resident-media-row">
                                @if($ticket->image_path)
                                    <a href="{{ $ticket->image_url }}" target="_blank" class="resident-ticket-thumb-link">
                                        <img src="{{ $ticket->image_url }}" alt="Ticket attachment" class="resident-ticket-thumb">
                                    </a>
                                @endif

                                @if($ticket->video_path)
                                    <a href="{{ $ticket->video_url }}" target="_blank" class="resident-media-chip">
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
                    <x-resident-empty-state title="No active tickets" description="Your current maintenance concerns will appear here." :action-href="route('tickets.create')" action-label="Create Your First Ticket" />
                @endforelse

                @if($hiddenActiveTickets->isNotEmpty())
                    <div id="resident-more-active" class="resident-more-list" style="display: none;">
                        @foreach($hiddenActiveTickets as $ticket)
                            <article class="resident-card resident-card-active"
                                     data-filter-card
                                     data-search="{{ Str::lower($ticket->title . ' ' . $ticket->description . ' ' . $ticket->ticket_id) }}"
                                     data-status="{{ $ticket->status }}"
                                     data-priority="{{ $ticket->normalized_priority }}">
                                <div class="resident-card-top">
                                    <div>
                                        <div class="resident-card-heading">
                                            <h3>{{ $ticket->title }}</h3>
                                            <span class="resident-badge resident-badge-status-{{ $ticket->status }}">
                                                {{ $ticket->status_label }}
                                            </span>
                                            <span class="resident-badge resident-badge-priority-{{ $ticket->normalized_priority }}">
                                                {{ $ticket->priority_label }}
                                            </span>
                                        </div>
                                        <p class="resident-card-description">{{ Str::limit($ticket->description, 160) }}</p>
                                    </div>

                                    <div class="resident-card-links resident-card-action-bar" aria-label="Ticket actions">
                                        <a href="{{ route('tickets.track', $ticket) }}">Track</a>
                                        @if($ticket->status === 'pending_approval')
                                            <a href="{{ route('tickets.edit', $ticket) }}">Edit</a>
                                        @endif
                                        <a href="{{ route('tickets.show', $ticket) }}">Details</a>
                                    </div>
                                </div>

                                @if($ticket->image_path || $ticket->video_path)
                                    <div class="resident-media-row">
                                        @if($ticket->image_path)
                                            <a href="{{ $ticket->image_url }}" target="_blank" class="resident-ticket-thumb-link">
                                                <img src="{{ $ticket->image_url }}" alt="Ticket attachment" class="resident-ticket-thumb">
                                            </a>
                                        @endif

                                        @if($ticket->video_path)
                                            <a href="{{ $ticket->video_url }}" target="_blank" class="resident-media-chip">
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

                    <button type="button" class="resident-see-more-btn" data-target="resident-more-active" aria-expanded="false">See more</button>
                @endif

                <div class="resident-filter-empty" data-filter-empty>No active tickets match your filters.</div>
            </div>
        </section>

        <section class="resident-page-panel resident-status-panel">
            <nav class="resident-status-tabs" role="tablist" aria-label="Request status categories">
                <button type="button" class="resident-status-tab is-active" data-resident-status-tab="assigned" role="tab" aria-selected="true">
                    Assigned <span>{{ $assignedTicketsList->count() }}</span>
                </button>
                <button type="button" class="resident-status-tab" data-resident-status-tab="finished" role="tab" aria-selected="false">
                    Finished <span>{{ $finishedTicketsList->count() }}</span>
                </button>
                <button type="button" class="resident-status-tab" data-resident-status-tab="rejected" role="tab" aria-selected="false">
                    Rejected <span>{{ $rejectedTicketsList->count() }}</span>
                </button>
            </nav>

            <div class="resident-page-divider"></div>

            @foreach([
                'assigned' => ['title' => 'Assigned Requests', 'copy' => 'Requests routed to maintenance staff, including work already in progress.', 'tickets' => $assignedTicketsList],
                'finished' => ['title' => 'Finished Requests', 'copy' => 'Maintenance requests that have already been resolved.', 'tickets' => $finishedTicketsList],
                'rejected' => ['title' => 'Rejected Requests', 'copy' => 'Requests declined by admin remain visible here for reference.', 'tickets' => $rejectedTicketsList],
            ] as $statusKey => $statusGroup)
                <section class="resident-status-section {{ $statusKey === 'assigned' ? 'is-active' : '' }}"
                         data-resident-status-section="{{ $statusKey }}"
                         role="tabpanel"
                         @if($statusKey !== 'assigned') hidden @endif>
                    <div class="resident-ticket-section-head">
                        <div>
                            <h3>{{ $statusGroup['title'] }}</h3>
                            <p>{{ $statusGroup['copy'] }}</p>
                        </div>
                    </div>

                    <div class="resident-status-list" data-progressive-list>
                        @forelse($statusGroup['tickets'] as $ticket)
                            <article class="resident-status-card" data-progressive-item>
                                <div class="resident-status-copy">
                                    <div class="resident-status-card-head">
                                        <h3>{{ $ticket->title }}</h3>
                                        <span class="resident-badge resident-badge-status-{{ $ticket->status }}">
                                            {{ $ticket->status_label }}
                                        </span>
                                    </div>
                                    <p>{{ Str::limit($ticket->rejection_reason ?: $ticket->description, 120) }}</p>
                                    <div class="resident-status-log-row" aria-label="Ticket metadata">
                                        <span class="resident-status-log-item">
                                            <small>Ticket ID</small>
                                            <strong>{{ $ticket->ticket_id }}</strong>
                                        </span>
                                        <span class="resident-status-log-item">
                                            <small>Updated</small>
                                            <strong>{{ $ticket->updated_at->diffForHumans() }}</strong>
                                        </span>
                                    </div>
                                </div>
                                <a class="resident-status-view-btn" href="{{ route('tickets.show', $ticket) }}">View</a>
                            </article>
                        @empty
                            <x-resident-empty-state compact icon="archive" :title="'No ' . strtolower($statusGroup['title']) . ' yet'" />
                        @endforelse
                    </div>
                </section>
            @endforeach
        </section>

        <section class="resident-page-panel resident-page-panel-history is-collapsed" data-filter-scope>
            <div class="resident-page-panel-head">
                <div>
                    <h2>Past History</h2>
                    <p>Completed and closed tickets are stored here so they stay visually separate from active work.</p>
                </div>
                <button type="button" class="resident-history-toggle" data-history-toggle aria-expanded="false">Show Archive</button>
            </div>

            <div class="resident-page-divider"></div>

            <div class="resident-filter-bar resident-ticket-filter-bar">
                <select class="resident-filter-select" data-filter-select data-filter-key="status">
                    <option value="">All statuses</option>
                    <option value="resolved">Resolved</option>
                    <option value="closed">Closed</option>
                    <option value="cancelled">Cancelled</option>
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
                                        {{ $ticket->status_label }}
                                    </span>
                                    <span class="resident-badge resident-badge-priority-{{ $ticket->normalized_priority }}">
                                        {{ $ticket->priority_label }}
                                    </span>
                                </div>
                                <p class="resident-card-description">{{ Str::limit($ticket->description, 160) }}</p>
                            </div>

                            <div class="resident-card-links">
                                <a class="resident-history-view-btn" href="{{ route('tickets.show', $ticket) }}">View Details</a>
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
                    <x-resident-empty-state compact icon="archive" title="No ticket history yet" description="Completed and rejected tickets will move here." />
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
                                                {{ $ticket->status_label }}
                                            </span>
                                            <span class="resident-badge resident-badge-priority-{{ $ticket->normalized_priority }}">
                                                {{ $ticket->priority_label }}
                                            </span>
                                        </div>
                                        <p class="resident-card-description">{{ Str::limit($ticket->description, 160) }}</p>
                                    </div>

                                    <div class="resident-card-links">
                                        <a class="resident-history-view-btn" href="{{ route('tickets.show', $ticket) }}">View Details</a>
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

                    <button type="button" class="resident-see-more-btn" data-target="resident-more-history" aria-expanded="false">See more</button>
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
                    button.setAttribute('aria-expanded', 'false');
                } else {
                    target.style.display = 'flex';
                    button.textContent = 'Show Less';
                    button.setAttribute('aria-expanded', 'true');
                }
            });
        });

        document.querySelector('[data-history-toggle]')?.addEventListener('click', (event) => {
            const section = event.currentTarget.closest('.resident-page-panel-history');
            const collapsed = section.classList.toggle('is-collapsed');
            event.currentTarget.textContent = collapsed ? 'Show Archive' : 'Hide Archive';
            event.currentTarget.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
        });

        document.querySelectorAll('[data-resident-status-tab]').forEach((tab) => {
            tab.addEventListener('click', () => {
                const target = tab.dataset.residentStatusTab;

                document.querySelectorAll('[data-resident-status-tab]').forEach((item) => {
                    const active = item === tab;
                    item.classList.toggle('is-active', active);
                    item.setAttribute('aria-selected', active ? 'true' : 'false');
                });

                document.querySelectorAll('[data-resident-status-section]').forEach((section) => {
                    const active = section.dataset.residentStatusSection === target;
                    section.classList.toggle('is-active', active);
                    section.hidden = !active;
                });
            });
        });

        document.querySelectorAll('[data-ticket-filter]').forEach((button) => {
            button.addEventListener('click', () => {
                const filter = button.dataset.ticketFilter;
                const historySection = document.querySelector('.resident-page-panel-history');
                const activeSection = document.querySelector('.resident-page-panel:not(.resident-page-panel-history)');

                if (filter === 'closed') {
                    historySection.classList.remove('is-collapsed');
                    document.querySelector('[data-history-toggle]').textContent = 'Hide Archive';
                    const select = historySection?.querySelector('[data-filter-key="status"]');
                    if (select) {
                        select.value = '';
                        select.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                    historySection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                } else {
                    activeSection?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    </script>

    {{-- Migrated styling to global HallSync design tokens in resources/css/app.css.
         Keeping this page CSS removed to avoid radius/shadow/spacing drift across pages.
    --}}
</x-app-layout>
