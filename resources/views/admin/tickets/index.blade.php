<x-app-layout>
@php
    $urgentTickets = $tickets->filter(fn ($ticket) => $ticket->normalized_priority === 'critical'
        && ! $ticket->assigned_to
        && in_array($ticket->status, ['pending_approval', 'approved'], true));
    $urgentCount = $urgentTickets->count();
    $firstUrgent = $urgentTickets->first();
    $urgentAwaitingApprovalCount = $urgentTickets->where('status', 'pending_approval')->count();
    $urgentReadyToAssignCount = $urgentTickets->where('status', 'approved')->count();
    $oldestUrgent = $urgentTickets->sortBy('created_at')->first();

    $pendingApprovalTickets = $tickets->where('status', 'pending_approval');
    $assignedTickets = $tickets->filter(fn ($ticket) => $ticket->assigned_to
        && in_array($ticket->status, ['approved', 'assigned', 'in_progress'], true));
    $queueTickets = $tickets->filter(fn ($ticket) => ! $ticket->assigned_to
        && in_array($ticket->status, ['pending_approval', 'approved'], true));
    $finishedTickets = $tickets->whereIn('status', ['resolved', 'closed']);
    $rejectedTickets = $tickets->where('status', 'rejected');

    $openCount = $pendingApprovalTickets->count();
    $assignedCount = $assignedTickets->count();
    $inProgressCount = $tickets->where('status', 'in_progress')->count();
    $finishedCount = $finishedTickets->count();
    $rejectedCount = $rejectedTickets->count();

    $priorityMeta = [
        'critical' => [
            'label' => 'Critical',
            'bg' => 'rgba(224,112,96,0.12)',
            'fg' => '#F0B3A9',
            'border' => 'rgba(224,112,96,0.24)',
        ],
        'medium' => [
            'label' => 'Medium',
            'bg' => 'rgba(190,147,96,0.12)',
            'fg' => '#D7B48D',
            'border' => 'rgba(190,147,96,0.22)',
        ],
        'low' => [
            'label' => 'Low',
            'bg' => 'rgba(111,160,111,0.10)',
            'fg' => '#A8CAA8',
            'border' => 'rgba(111,160,111,0.22)',
        ],
    ];
@endphp

<div class="dash-root admin-ticket-page">
    <section class="admin-overview-hero">
        <div>
            <p class="admin-overview-hero__kicker">HallSync Admin</p>
            <h1 class="admin-overview-hero__title">Maintenance <span>Command Center</span></h1>
            <span class="admin-overview-hero__subtitle">Oversee maintenance operations, assign staff, and keep resident requests moving.</span>
        </div>
    </section>

    {{-- URGENT ATTENTION BOX --}}
    @if($urgentCount > 0)
        <section class="admin-critical-brief" aria-label="Critical maintenance requests">
            <div class="admin-critical-summary">
                <div class="admin-critical-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" />
                    </svg>
                </div>
                <div>
                    <p class="admin-critical-kicker">Critical Attention Required</p>
                    <h2>{{ $urgentCount }} Critical Request{{ $urgentCount != 1 ? 's' : '' }}</h2>
                    <div class="admin-critical-metrics" aria-label="Critical request summary">
                        <span>{{ $urgentAwaitingApprovalCount }} awaiting approval</span>
                        @if($oldestUrgent)
                            <span>Oldest {{ $oldestUrgent->created_at->diffForHumans(null, true) }}</span>
                        @endif
                        <span>{{ $urgentReadyToAssignCount }} ready to assign</span>
                    </div>
                </div>
            </div>

            @if($firstUrgent)
                <div class="admin-critical-focus">
                    <div class="admin-critical-card">
                        <div class="admin-critical-card-top">
                            <span>Next Dispatch</span>
                            <code>#{{ $firstUrgent->ticket_id ?? $firstUrgent->id }}</code>
                        </div>
                        <strong>{{ $firstUrgent->title }}</strong>
                        <div class="admin-critical-meta">
                            <span>{{ $firstUrgent->user->name ?? 'Resident' }}</span>
                            <span>{{ $firstUrgent->created_at->format('M d, Y h:i A') }}</span>
                            @if(!empty($firstUrgent->location))
                                <span>{{ $firstUrgent->location }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="admin-critical-actions">
                        <button type="button"
                                onclick="{{ $firstUrgent->status === 'pending_approval' ? 'openApproveModal(' . $firstUrgent->id . ')' : 'openAssignModal(' . $firstUrgent->id . ')' }}"
                                class="admin-critical-action admin-critical-action-primary">
                            {{ $firstUrgent->status === 'pending_approval' ? 'Approve Request' : 'Assign Now' }}
                        </button>
                        <a href="{{ route('tickets.show', $firstUrgent) }}" class="admin-critical-action admin-critical-action-secondary">
                            View Details
                        </a>
                    </div>
                </div>
            @endif
        </section>
    @endif

    {{-- STATS CARDS --}}
    <div class="admin-compact-stats admin-compact-stats-5">
        <x-admin-compact-stat icon="inbox" :value="$openCount" label="Awaiting Assignment" note="Ready for admin action" />
        <x-admin-compact-stat icon="users" :value="$assignedCount" label="Assigned" note="Already routed to staff" tone="blue" />
        <x-admin-compact-stat icon="clock" :value="$inProgressCount" label="In Progress" note="Currently handled by staff" tone="green" />
        <x-admin-compact-stat icon="check" :value="$finishedCount" label="Finished" note="Completed by staff" tone="green" />
        <x-admin-compact-stat icon="alert" :value="$rejectedCount" label="Rejected" note="Declined requests" tone="red" />
    </div>

    <hr class="app-soft-divider">

    {{-- MAIN OPERATIONS QUEUE --}}
    <div class="admin-ticket-panel admin-operations-queue">
        <div class="admin-ticket-panel-head">
            <div>
                <h2 class="admin-ticket-panel-title">Operations Queue</h2>
                <p class="admin-ticket-panel-sub">Handle requests that still need admin attention before or during active operations.</p>
            </div>

            <div class="admin-ticket-filters">
                <select id="filterPriority" class="admin-filter-select admin-operations-filter-native" aria-hidden="true" tabindex="-1">
                    <option value="all">All Priority</option>
                    <option value="critical">Critical</option>
                    <option value="medium">Medium</option>
                    <option value="low">Low</option>
                </select>
                <div class="admin-priority-dropdown" data-priority-dropdown>
                    <button type="button"
                            class="admin-operations-filter"
                            aria-haspopup="listbox"
                            aria-expanded="false"
                            data-priority-trigger>
                        <span data-priority-label>All Priority</span>
                        <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="m6 9 6 6 6-6" />
                        </svg>
                    </button>
                    <div class="admin-priority-menu" role="listbox" data-priority-menu hidden>
                        <button type="button" role="option" aria-selected="true" data-priority-option value="all">All Priority</button>
                        <button type="button" role="option" aria-selected="false" data-priority-option value="critical">Critical</button>
                        <button type="button" role="option" aria-selected="false" data-priority-option value="medium">Medium</button>
                        <button type="button" role="option" aria-selected="false" data-priority-option value="low">Low</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="admin-ticket-panel-divider"></div>

        <div class="space-y-3" data-progressive-list>
            @forelse($queueTickets as $ticket)
                @php
                    $priority = $priorityMeta[$ticket->normalized_priority] ?? $priorityMeta['medium'];
                @endphp
                <div class="ticket-card" data-progressive-item
                     data-status="{{ $ticket->status }}"
                     data-priority="{{ $ticket->normalized_priority }}">

                    <div class="ticket-card-shell {{ $ticket->normalized_priority === 'critical' ? 'ticket-card-shell-critical' : '' }} flex transition-all duration-200 cursor-pointer overflow-hidden"
                         onclick="window.location.href='{{ route('tickets.show', $ticket) }}'">

                        <div class="ticket-card-body flex-1 p-5 md:p-6">
                            <div class="ticket-card-layout flex flex-col xl:flex-row xl:items-start xl:justify-between gap-5">

                                <div class="ticket-card-main flex-1 min-w-0">
                                    <small class="ticket-card-clean-meta">
                                        {{ $ticket->ticket_id ?? $ticket->id }} &middot; {{ strtoupper($priority['label']) }} &middot; {{ strtoupper(str_replace('_', ' ', $ticket->status)) }} &middot; {{ $ticket->created_at->format('M d, Y') }}
                                    </small>
                                    <h3 class="ticket-card-clean-title">{{ $ticket->title }}</h3>
                                    <p class="ticket-card-clean-copy">{{ Str::limit($ticket->description, 120) }}</p>

                                    <div class="ticket-card-clean-hidden ticket-card-heading flex flex-wrap items-center gap-2 mb-3">
                                        <span class="text-xs font-mono" style="color: #D6A85B;">
                                            #{{ $ticket->ticket_id ?? $ticket->id }}
                                        </span>

                                        <span class="ticket-card-priority text-[11px] font-bold px-3 py-1 rounded-full uppercase tracking-[0.10em]"
                                              style="background: {{ $priority['bg'] }}; color: {{ $priority['fg'] }}; border: 1px solid {{ $priority['border'] }};">
                                            {{ $priority['label'] }}{{ $ticket->normalized_priority === 'critical' ? ' priority' : '' }}
                                        </span>

                                        <span class="ticket-card-status ticket-card-status-{{ str_replace('_', '-', $ticket->status) }}">
                                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                        </span>
                                    </div>

                                    <h3 class="ticket-card-clean-hidden" style="font-size: 19px; font-weight: 700; color: #F8F3EA; margin-bottom: 8px; line-height: 1.25;">
                                        {{ $ticket->title }}
                                    </h3>

                                    <p class="ticket-card-clean-hidden" style="font-size: 13px; color: #B0A898; line-height: 1.6; margin-bottom: 14px; max-width: 900px;">
                                        {{ Str::limit($ticket->description, 120) }}
                                    </p>

                                    <div class="ticket-card-clean-hidden ticket-card-meta flex flex-wrap items-center gap-x-4 gap-y-2 text-xs"
                                         style="color: #8A7A66;">
                                        <div class="ticket-card-meta-item flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            {{ $ticket->user->name ?? 'Resident' }}
                                        </div>

                                        <div class="ticket-card-meta-divider w-px h-3" style="background: #5A4A3A;"></div>

                                        <div class="ticket-card-meta-item flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            {{ $ticket->created_at->format('M d, Y h:i A') }}
                                        </div>

                                        @if(!empty($ticket->location))
                                            <div class="ticket-card-meta-divider w-px h-3" style="background: #5A4A3A;"></div>

                                            <div class="ticket-card-meta-item flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0L6.343 16.657a8 8 0 1111.314 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                {{ $ticket->location }}
                                            </div>
                                        @endif

                                        @if(!empty($ticket->assignedTo?->name))
                                            <div class="ticket-card-meta-divider w-px h-3" style="background: #5A4A3A;"></div>

                                            <div class="ticket-card-meta-item flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                Assigned to {{ $ticket->assignedTo->name }}
                                            </div>
                                        @endif
                                    </div>

                                </div>

                                <div class="ticket-card-actions flex-shrink-0"
                                     onclick="event.stopPropagation()">
                                    <a href="{{ route('tickets.show', $ticket) }}"
                                       class="ticket-card-action ticket-card-action-view">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View Details
                                    </a>

                                    <button type="button"
                                            onclick="{{ $ticket->status === 'pending_approval' ? 'openApproveModal(' . $ticket->id . ')' : 'openAssignModal(' . $ticket->id . ')' }}"
                                            class="ticket-card-action {{ $ticket->status === 'pending_approval' ? 'ticket-card-action-approve' : 'ticket-card-action-primary' }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        {{ $ticket->status === 'pending_approval' ? 'Approve' : ($ticket->assigned_to ? 'Reassign' : 'Assign') }}
                                    </button>

                                    @if($ticket->status === 'pending_approval')
                                        <div class="ticket-card-reject-wrap">
                                            <button type="button" onclick="openRejectModal({{ $ticket->id }})"
                                                    class="ticket-card-action ticket-card-action-reject">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                                Reject
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                    <div class="admin-empty-state admin-empty-state-legacy">
                        <div class="w-20 h-20 rounded-full mx-auto mb-5 flex items-center justify-center"
                             style="background: rgba(214,168,91,0.14); color: #D6A85B;">
                            <span class="text-4xl">🎫</span>
                        </div>
                        <h3 class="text-2xl font-semibold mb-2" style="color: #F5F2E8; font-family: 'Inter', system-ui, sans-serif; letter-spacing: -0.02em;">
                            No requests in queue
                        </h3>
                        <p style="color: #C4B89B; font-family: 'Inter', system-ui, sans-serif; line-height: 1.75;" class="max-w-md mx-auto">
                            Assigned and finished work now lives in its own section, so the admin queue is fully clear.
                        </p>
                    </div>
            @endforelse
        </div>
    </div>

    <hr class="app-soft-divider admin-archive-divider">

    <div class="admin-ticket-panel admin-ticket-archive">
        <nav class="admin-archive-tabs" role="tablist" aria-label="Ticket archive categories">
            <button type="button" class="admin-archive-tab is-active" data-archive-tab="assigned" role="tab" aria-selected="true">
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7h3a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h3" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5h6v4H9z" />
                </svg>
                Assigned <span>{{ $assignedCount }}</span>
            </button>
            <button type="button" class="admin-archive-tab" data-archive-tab="completed" role="tab" aria-selected="false">
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 6 9 17l-5-5" />
                </svg>
                Finished <span>{{ $finishedCount }}</span>
            </button>
            <button type="button" class="admin-archive-tab" data-archive-tab="rejected" role="tab" aria-selected="false">
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 6 6 18M6 6l12 12" />
                </svg>
                Rejected <span>{{ $rejectedCount }}</span>
            </button>
        </nav>

        <div class="admin-ticket-panel-divider"></div>

        <section class="admin-archive-section is-active" data-archive-section="assigned" role="tabpanel">
            <div class="admin-ticket-panel-head">
                <div>
                    <h2 class="admin-ticket-panel-title">Assigned</h2>
                    <p class="admin-ticket-panel-sub">Tickets already routed to staff, including work currently in progress.</p>
                </div>
            </div>

            <div class="admin-ticket-panel-divider"></div>

            <div class="admin-status-stack">
                @forelse($assignedTickets as $ticket)
                    @php
                        $priority = $priorityMeta[$ticket->normalized_priority] ?? $priorityMeta['medium'];
                    @endphp
                    <div class="admin-status-card {{ $loop->index >= 3 ? 'is-hidden-by-default' : '' }}" data-collapsible-item="assigned">
                        <div>
                            <small class="admin-status-clean-meta">
                                {{ $ticket->ticket_id ?? $ticket->id }} &middot; {{ strtoupper($priority['label']) }} &middot; {{ strtoupper($ticket->status === 'in_progress' ? 'In progress' : 'Assigned') }} &middot; {{ $ticket->updated_at->format('M d, Y') }}
                            </small>
                            <strong>{{ $ticket->title }}</strong>
                            <p>{{ Str::limit($ticket->description, 95) }}</p>
                            <div class="admin-status-meta">
                                <span>{{ $ticket->assignedTo->name ?? 'Staff not set' }}</span>
                                <span>{{ $ticket->updated_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <a href="{{ route('tickets.show', $ticket) }}" class="admin-status-link">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            View Details
                        </a>
                    </div>
                @empty
                    <x-admin-empty-state compact icon="archive" title="No assigned tickets right now" description="Assigned maintenance work will appear here." />
                @endforelse
            </div>
            <div class="admin-collapsible-action">
                @if($assignedCount > 3)
                    <button type="button" class="admin-collapsible-toggle" data-target-list="assigned" data-expand-label="See more" data-collapse-label="Show less" aria-expanded="false">See more</button>
                @else
                    <span class="admin-collapsible-note">You're all caught up</span>
                @endif
            </div>
        </section>

        <section class="admin-archive-section" data-archive-section="completed" role="tabpanel" hidden>
            <div class="admin-ticket-panel-head">
                <div>
                    <h2 class="admin-ticket-panel-title">Finished</h2>
                    <p class="admin-ticket-panel-sub">Completed requests so admin can review what staff already resolved.</p>
                </div>
            </div>

            <div class="admin-ticket-panel-divider"></div>

            <div class="admin-status-stack">
                @forelse($finishedTickets as $ticket)
                    @php
                        $priority = $priorityMeta[$ticket->normalized_priority] ?? $priorityMeta['medium'];
                    @endphp
                    <div class="admin-status-card {{ $loop->index >= 3 ? 'is-hidden-by-default' : '' }}" data-collapsible-item="completed">
                        <div>
                            <small class="admin-status-clean-meta">
                                {{ $ticket->ticket_id ?? $ticket->id }} &middot; {{ strtoupper($priority['label']) }} &middot; FINISHED &middot; {{ $ticket->updated_at->format('M d, Y') }}
                            </small>
                            <strong>{{ $ticket->title }}</strong>
                            <p>{{ Str::limit($ticket->description, 95) }}</p>
                            <div class="admin-status-meta">
                                <span>{{ $ticket->assignedTo->name ?? 'No staff recorded' }}</span>
                                <span>Completed {{ $ticket->updated_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <a href="{{ route('tickets.show', $ticket) }}" class="admin-status-link">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            View Details
                        </a>
                    </div>
                @empty
                    <x-admin-empty-state compact icon="archive" title="No finished tickets yet" description="Completed maintenance work will appear here." />
                @endforelse
            </div>
            <div class="admin-collapsible-action">
                @if($finishedCount > 3)
                    <button type="button" class="admin-collapsible-toggle" data-target-list="completed" data-expand-label="See more" data-collapse-label="Show less" aria-expanded="false">See more</button>
                @else
                    <span class="admin-collapsible-note">You're all caught up</span>
                @endif
            </div>
        </section>

        <section class="admin-archive-section" data-archive-section="rejected" role="tabpanel" hidden>
            <div class="admin-ticket-panel-head">
                <div>
                    <h2 class="admin-ticket-panel-title">Rejected</h2>
                    <p class="admin-ticket-panel-sub">Declined requests remain available for review and audit.</p>
                </div>
            </div>

            <div class="admin-ticket-panel-divider"></div>

            <div class="admin-status-stack">
                @forelse($rejectedTickets as $ticket)
                    @php
                        $priority = $priorityMeta[$ticket->normalized_priority] ?? $priorityMeta['medium'];
                    @endphp
                    <div class="admin-status-card {{ $loop->index >= 3 ? 'is-hidden-by-default' : '' }}" data-collapsible-item="rejected">
                        <div>
                            <small class="admin-status-clean-meta">
                                {{ $ticket->ticket_id ?? $ticket->id }} &middot; {{ strtoupper($priority['label']) }} &middot; REJECTED &middot; {{ $ticket->updated_at->format('M d, Y') }}
                            </small>
                            <strong>{{ $ticket->title }}</strong>
                            <p>{{ Str::limit($ticket->rejection_reason ?: $ticket->description, 120) }}</p>
                            <div class="admin-status-meta">
                                <span>{{ $ticket->user->name ?? 'Resident' }}</span>
                                <span>{{ $ticket->updated_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <a href="{{ route('tickets.show', $ticket) }}" class="admin-status-link">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            View Details
                        </a>
                    </div>
                @empty
                    <x-admin-empty-state compact icon="archive" title="No rejected tickets right now" description="Rejected maintenance requests will appear here." />
                @endforelse
            </div>
            <div class="admin-collapsible-action">
                @if($rejectedCount > 3)
                    <button type="button" class="admin-collapsible-toggle" data-target-list="rejected" data-expand-label="See more" data-collapse-label="Show less" aria-expanded="false">See more</button>
                @else
                    <span class="admin-collapsible-note">You're all caught up</span>
                @endif
            </div>
        </section>
    </div>
</div>

@include('admin.tickets.modals')

{{-- ASSIGN MODAL --}}
<div id="assignModal"
     class="admin-ticket-modal-backdrop fixed inset-0 bg-black/70 hidden items-center justify-center z-50 backdrop-blur-sm"
     style="display: none;">
    <div style="
        background: linear-gradient(135deg, #2A2C30 0%, #1F2023 100%);
        border: 1px solid #3A342D;
        border-radius: 28px;
        padding: 32px;
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
        max-width: 90vw;
        width: 420px;
    ">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px;">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full flex items-center justify-center"
                     style="background: rgba(214,168,91,0.15);">
                    <svg class="w-5 h-5" style="color: #D6A85B;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <h3 style="font-size: 22px; font-weight: 700; color: #F8F3EA; font-family: 'Playfair Display', serif; margin: 0;">
                    Assign to Staff
                </h3>
            </div>

            <button type="button"
                    onclick="closeAssignModal()"
                    class="admin-dialog-close text-3xl cursor-pointer leading-none transition-all duration-200 hover:opacity-70"
                    style="color: #8A7A66; background: none; border: none;">
                ×
            </button>
        </div>

        <form id="assignForm"
              method="POST"
              data-prevent-double-submit
              data-submitting-text="Assigning Ticket...">
            @csrf

            <div class="mb-6">
                <div class="mb-3 text-xs font-semibold uppercase tracking-[0.14em]" style="color: #D6A85B;">
                    Assigning this ticket will move it into the staff queue immediately.
                </div>
                <label class="block text-sm font-semibold mb-2" style="color: #D0C8B8;">
                    Select Staff
                </label>

                <select name="assigned_to"
                        class="w-full px-4 py-3 rounded-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-[#D6A85B]"
                        style="background: rgba(37,39,42,0.9); border: 1px solid #3A342D; color: #F8F3EA;"
                        required>
                    <option value="">Choose staff...</option>
                    @foreach($handymen ?? [] as $handyman)
                        <option value="{{ $handyman->id }}">
                            {{ $handyman->name }} ({{ $handyman->role === 'handyman' ? 'Staff' : ucfirst($handyman->role ?? 'handyman') }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button"
                        onclick="closeAssignModal()"
                        class="admin-ticket-modal-action admin-ticket-modal-action-secondary flex-1 px-4 py-3 rounded-xl font-semibold transition-all duration-200"
                        style="background: rgba(168,159,145,0.1); color: #B0A898; border: 1px solid rgba(168,159,145,0.2);"
                        onmouseover="this.style.background='rgba(168,159,145,0.2)'"
                        onmouseout="this.style.background='rgba(168,159,145,0.1)'">
                    Cancel
                </button>

                <button type="submit"
                        class="admin-ticket-modal-action admin-ticket-modal-action-primary flex-1 px-4 py-3 rounded-xl font-bold transition-all duration-200 flex items-center justify-center gap-2"
                        style="background: linear-gradient(90deg, #B8842F 0%, #D6A85B 100%); color: white; box-shadow: 0 4px 15px rgba(199,151,69,0.3);"
                        onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 8px 25px rgba(199,151,69,0.4)'"
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(199,151,69,0.3)'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Confirm Assignment
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showTicketModal(modal) {
    if (!modal) return;

    if (modal.parentElement !== document.body) {
        document.body.appendChild(modal);
    }

    document.body.classList.add('admin-ticket-modal-open');
    modal.style.display = 'flex';
    modal.setAttribute('aria-hidden', 'false');
}

function hideTicketModal(modal) {
    if (!modal) return;

    modal.style.display = 'none';
    modal.setAttribute('aria-hidden', 'true');

    const modalOpen = ['assignModal', 'approveModal', 'rejectModal'].some((modalId) => {
        const ticketModal = document.getElementById(modalId);
        return ticketModal && ticketModal.style.display !== 'none';
    });

    if (!modalOpen) {
        document.body.classList.remove('admin-ticket-modal-open');
    }
}

function openAssignModal(ticketId) {
    const modal = document.getElementById('assignModal');
    const form = document.getElementById('assignForm');
    form.action = `/tickets/${ticketId}/assign`;
    showTicketModal(modal);
}

function closeAssignModal() {
    const modal = document.getElementById('assignModal');
    hideTicketModal(modal);
}

function openApproveModal(ticketId) {
    const modal = document.getElementById('approveModal');
    const form = document.getElementById('approveForm');
    if (!modal || !form) return;
    form.action = `/tickets/${ticketId}/approve`;
    showTicketModal(modal);
}

function closeApproveModal() {
    const modal = document.getElementById('approveModal');
    hideTicketModal(modal);
}

function openRejectModal(ticketId) {
    const modal = document.getElementById('rejectModal');
    const form = document.getElementById('rejectForm');
    if (!modal || !form) return;
    form.action = `/tickets/${ticketId}/reject`;
    showTicketModal(modal);
    form.querySelector('[name="rejection_reason"]')?.focus();
}

function closeRejectModal() {
    const modal = document.getElementById('rejectModal');
    hideTicketModal(modal);
}

const filterPriority = document.getElementById('filterPriority');

if (filterPriority) {
    filterPriority.addEventListener('change', filterTickets);
}

document.querySelectorAll('[data-priority-dropdown]').forEach((dropdown) => {
    const select = document.getElementById('filterPriority');
    const trigger = dropdown.querySelector('[data-priority-trigger]');
    const label = dropdown.querySelector('[data-priority-label]');
    const menu = dropdown.querySelector('[data-priority-menu]');
    const options = Array.from(dropdown.querySelectorAll('[data-priority-option]'));

    if (!select || !trigger || !label || !menu || options.length === 0) {
        return;
    }

    const closeMenu = () => {
        dropdown.classList.remove('is-open');
        trigger.setAttribute('aria-expanded', 'false');
        menu.hidden = true;
    };

    const openMenu = () => {
        dropdown.classList.add('is-open');
        trigger.setAttribute('aria-expanded', 'true');
        menu.hidden = false;
    };

    const syncSelection = (value) => {
        const selectedOption = options.find((option) => option.value === value) || options[0];
        label.textContent = selectedOption.textContent.trim();
        options.forEach((option) => {
            option.setAttribute('aria-selected', option === selectedOption ? 'true' : 'false');
        });
    };

    trigger.addEventListener('click', () => {
        if (menu.hidden) {
            openMenu();
        } else {
            closeMenu();
        }
    });

    options.forEach((option) => {
        option.addEventListener('click', () => {
            select.value = option.value;
            syncSelection(option.value);
            closeMenu();
            select.dispatchEvent(new Event('change', { bubbles: true }));
        });
    });

    select.addEventListener('change', () => syncSelection(select.value));
    syncSelection(select.value);

    document.addEventListener('click', (event) => {
        if (!dropdown.contains(event.target)) {
            closeMenu();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeMenu();
        }
    });
});

function filterTickets() {
    const priorityFilter = document.getElementById('filterPriority').value;
    const tickets = document.querySelectorAll('.ticket-card');

    tickets.forEach(ticket => {
        const priority = ticket.dataset.priority;

        const priorityMatch = priorityFilter === 'all' || priority === priorityFilter;

        if (priorityMatch) {
            ticket.style.display = '';
        } else {
            ticket.style.display = 'none';
        }
    });
}

document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
        closeAssignModal();
        closeApproveModal();
        closeRejectModal();
    }
});

['assignModal', 'approveModal', 'rejectModal'].forEach(modalId => {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.addEventListener('click', function (event) {
            if (event.target === this) {
                hideTicketModal(this);
            }
        });
    }
});

document.querySelectorAll('[data-auto-dismiss]').forEach((flash) => {
    setTimeout(() => {
        flash.style.transition = 'opacity 0.35s ease, transform 0.35s ease';
        flash.style.opacity = '0';
        flash.style.transform = 'translateY(-6px)';
        setTimeout(() => flash.remove(), 360);
    }, 3200);
});

document.querySelectorAll('.admin-collapsible-toggle').forEach((button) => {
    button.addEventListener('click', () => {
        const target = button.dataset.targetList;
        const items = document.querySelectorAll(`[data-collapsible-item="${target}"]`);
        const expanded = button.dataset.expanded === 'true';

        items.forEach((item, index) => {
            if (index >= 3) {
                item.classList.toggle('is-hidden-by-default', expanded);
                item.style.display = '';
            }
        });

        button.dataset.expanded = expanded ? 'false' : 'true';
        button.setAttribute('aria-expanded', expanded ? 'false' : 'true');
        button.textContent = expanded ? button.dataset.expandLabel : button.dataset.collapseLabel;
    });
});

document.querySelectorAll('.admin-archive-tab').forEach((button) => {
    button.addEventListener('click', () => {
        const target = button.dataset.archiveTab;

        document.querySelectorAll('.admin-archive-tab').forEach((tab) => {
            const active = tab === button;
            tab.classList.toggle('is-active', active);
            tab.setAttribute('aria-selected', active ? 'true' : 'false');
        });

        document.querySelectorAll('.admin-archive-section').forEach((section) => {
            const active = section.dataset.archiveSection === target;
            section.classList.toggle('is-active', active);
            section.hidden = !active;
        });
    });
});
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap');
.admin-ticket-flash {
    padding: 16px 18px;
    border-radius: 18px;
    font-size: 0.92rem;
    font-weight: 600;
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.14);
}
.admin-ticket-flash-success {
    background: linear-gradient(180deg, rgba(46, 58, 41, 0.92) 0%, rgba(34, 46, 31, 0.92) 100%);
    border: 1px solid rgba(157, 195, 117, 0.18);
    color: #D5E3BE;
}
.admin-ticket-flash-error {
    background: linear-gradient(180deg, rgba(53, 38, 35, 0.92) 0%, rgba(42, 31, 29, 0.92) 100%);
    border: 1px solid rgba(224, 112, 96, 0.22);
    color: #F0B3A9;
}
.dash-root.admin-ticket-page {
    font-family: 'Inter', sans-serif;
    color: #c4b8a8;
    min-height: 100vh;
    padding: 0;
    max-width: 1580px;
    width: 100%;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 28px;
    position: relative;
    z-index: 1;
    font-size: 16px;
    line-height: 1.55;
}
.admin-ticket-page {
    gap: 28px;
}
.admin-ticket-page > .app-soft-divider {
    margin: -10px 0;
}
.admin-ticket-page > .app-soft-divider.admin-archive-divider {
    margin: -8px 0 10px;
}
.urgent-icon-label {
    color: #ffb2a7;
    font-size: 0.75rem;
    font-weight: 800;
    letter-spacing: 0.14em;
    text-transform: uppercase;
}
.admin-critical-brief {
    position: relative;
    overflow: hidden;
    display: grid;
    grid-template-columns: minmax(260px, 0.95fr) minmax(420px, 1.25fr);
    gap: 18px;
    align-items: stretch;
    padding: 22px;
    border-radius: 18px;
    background: linear-gradient(135deg, rgba(111, 30, 23, 0.98), rgba(126, 35, 24, 0.96) 48%, rgba(70, 27, 24, 0.98)), radial-gradient(circle at 12% 12%, rgba(255, 224, 205, 0.18), transparent 34%);
    border: 1px solid rgba(255, 214, 196, 0.18);
    box-shadow: 0 18px 34px rgba(51, 21, 17, 0.28);
}


.admin-critical-brief::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image: linear-gradient(rgba(255, 255, 255, 0.035) 1px, transparent 1px), linear-gradient(90deg, rgba(255, 255, 255, 0.035) 1px, transparent 1px);
    background-size: 42px 42px;
    pointer-events: none;
}
.admin-critical-summary, .admin-critical-focus {
    position: relative;
    z-index: 1;
}
.admin-critical-summary {
    display: flex;
    align-items: center;
    gap: 16px;
    min-width: 0;
}
.admin-critical-icon {
    width: 52px;
    height: 52px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 auto;
    color: #FFE2D8;
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.22);
}
.admin-critical-icon svg {
    width: 25px;
    height: 25px;
}
.admin-critical-kicker {
    margin: 0 0 4px;
    color: rgba(255, 217, 207, 0.86);
    font-size: 0.72rem;
    font-weight: 800;
    letter-spacing: 0.13em;
    line-height: 1.3;
    text-transform: uppercase;
}
.admin-critical-summary h2 {
    margin: 0;
    color: #FFFFFF;
    font-family: 'Playfair Display', serif;
    font-size: clamp(1.55rem, 2.2vw, 2.15rem);
    line-height: 1.08;
}
.admin-critical-metrics {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 12px;
}
.admin-critical-metrics span {
    display: inline-flex;
    align-items: center;
    min-height: 28px;
    padding: 5px 10px;
    border-radius: 999px;
    color: rgba(255, 242, 236, 0.86);
    background: rgba(255, 255, 255, 0.10);
    border: 1px solid rgba(255, 255, 255, 0.13);
    font-size: 0.76rem;
    font-weight: 700;
}
.admin-critical-focus {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    gap: 12px;
    min-width: 0;
}
.admin-critical-card {
    min-width: 0;
    padding: 15px 16px;
    border-radius: 14px;
    background: rgba(35, 12, 10, 0.30);
    border: 1px solid rgba(255, 255, 255, 0.17);
}
.admin-critical-card-top {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    margin-bottom: 7px;
}
.admin-critical-card-top span {
    padding: 3px 9px;
    border-radius: 999px;
    color: #6B1D17;
    background: #FFE7DE;
    font-size: 0.66rem;
    font-weight: 900;
    letter-spacing: 0.10em;
    text-transform: uppercase;
}
.admin-critical-card-top code {
    color: rgba(255, 255, 255, 0.68);
    font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
    font-size: 0.75rem;
}
.admin-critical-card strong {
    display: block;
    overflow: hidden;
    color: #FFFFFF;
    font-size: 0.96rem;
    line-height: 1.35;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.admin-critical-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 6px 14px;
    margin-top: 7px;
    color: rgba(255, 255, 255, 0.72);
    font-size: 0.78rem;
}
.admin-critical-actions {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.admin-critical-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 136px;
    min-height: 42px;
    padding: 10px 16px;
    border-radius: 12px;
    font-size: 0.84rem;
    font-weight: 800;
    line-height: 1.1;
    text-align: center;
    text-decoration: none;
    transition: transform 0.16s ease, background 0.16s ease, border-color 0.16s ease;
}
.admin-critical-action:hover {
    transform: translateY(-1px);
}
.admin-critical-action-primary {
    color: #6B1D17;
    background: #FFFFFF;
    border: 1px solid #FFFFFF;
    box-shadow: 0 10px 18px rgba(35, 12, 10, 0.20);
}
.admin-critical-action-secondary {
    color: #FFFFFF;
    background: rgba(255, 255, 255, 0.10);
    border: 1px solid rgba(255, 255, 255, 0.36);
}
.admin-critical-action-secondary:hover {
    background: rgba(255, 255, 255, 0.17);
}
.admin-ticket-page > div:first-of-type {
    position: relative !important;
    overflow: hidden !important;
    border-radius: 20px !important;
    background: linear-gradient(120deg, #111009 0%, #1C1A12 50%, #201E14 100%) !important;
    padding: 36px 44px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 24px !important;
    border: 1px solid rgba(214, 168, 91, 0.18) !important;
    box-shadow: none !important;
}
.admin-ticket-page > div:first-of-type::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image: linear-gradient(rgba(214, 168, 91, 0.04) 1px, transparent 1px), linear-gradient(90deg, rgba(214, 168, 91, 0.04) 1px, transparent 1px);
    background-size: 48px 48px;
    pointer-events: none;
}
.admin-ticket-page > div:first-of-type > div.absolute:first-child {
    top: -60px !important;
    right: -40px !important;
    width: 280px !important;
    height: 280px !important;
    background: radial-gradient(circle, rgba(214, 168, 91, 0.15) 0%, transparent 70%) !important;
    opacity: 1 !important;
    filter: none !important;
}
.admin-ticket-page > div:first-of-type > div.absolute:nth-child(2) {
    display: none !important;
}
.admin-ticket-page > div:first-of-type > div.relative {
    position: relative !important;
    z-index: 2 !important;
    width: 100% !important;
    padding: 0 !important;
}
.admin-ticket-page > div:first-of-type .mb-3.flex.items-center.gap-3 {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    margin-bottom: 12px !important;
    font-size: 0.875rem !important;
    letter-spacing: 0.18em !important;
    text-transform: uppercase !important;
    color: #d6a85b !important;
    font-weight: 700 !important;
}
.admin-ticket-page > div:first-of-type .mb-3.flex.items-center.gap-3 span:first-child {
    width: 6px !important;
    height: 6px !important;
    border-radius: 999px !important;
    background: #d6a85b !important;
}
.admin-ticket-page > div:first-of-type .mb-3.flex.items-center.gap-3 span:last-child {
    font-size: 0.875rem !important;
    letter-spacing: 0.18em !important;
}
.admin-ticket-page > div:first-of-type h1 {
    font-family: 'Playfair Display', serif !important;
    font-size: clamp(2.5rem, 4vw, 3.5rem) !important;
    font-weight: 700 !important;
    color: #f0e9df !important;
    line-height: 1.12 !important;
    margin-bottom: 12px !important;
}
.admin-ticket-page > div:first-of-type p {
    font-size: 1.125rem !important;
    color: rgba(255, 255, 255, 0.62) !important;
    max-width: 760px !important;
}
.admin-ticket-page > div:first-of-type .shrink-0 {
    position: relative !important;
    z-index: 2 !important;
    text-align: right !important;
    flex-shrink: 0 !important;
    background: transparent !important;
    border: none !important;
    padding: 0 !important;
}
.admin-ticket-page > div:first-of-type .shrink-0 span {
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    color: rgba(255, 255, 255, 0.4) !important;
    font-size: 0.75rem !important;
    letter-spacing: 0.1em !important;
    text-transform: uppercase !important;
}
.admin-ticket-page > div:first-of-type .shrink-0 span::before {
    content: '';
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #5A8A5A;
    display: inline-block;
}
.admin-metrics-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 16px;
}
.admin-metric-card {
    background: rgba(48, 45, 40, 0.86);
    border-radius: 16px;
    padding: 18px 20px;
    border: 1px solid rgba(214, 168, 91, 0.18);
    display: flex;
    align-items: center;
    gap: 14px;
    color: #c4b8a8;
    backdrop-filter: blur(10px);
    box-shadow: 0 10px 20px rgba(72, 48, 24, 0.10);
    transition: transform 0.2s ease, border-color 0.2s ease;
}
.admin-metric-card:hover {
    transform: translateY(-2px);
    border-color: rgba(214, 168, 91, 0.26);
}
.admin-metric-card-alert {
    border-color: rgba(214, 168, 91, 0.18);
}
.admin-metric-card-success {
    border-color: rgba(214, 168, 91, 0.18);
}
.admin-metric-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(214, 168, 91, 0.12);
    color: #d6a85b;
    flex-shrink: 0;
}
.admin-metric-icon-alert {
    background: rgba(214, 168, 91, 0.12);
    color: #d6a85b;
}
.admin-metric-icon-success {
    background: rgba(214, 168, 91, 0.12);
    color: #d6a85b;
}
.admin-metric-body {
    min-width: 0;
}
.admin-metric-value {
    font-size: 2.35rem;
    font-weight: 700;
    line-height: 1;
    color: #f0e9df;
}
.admin-metric-value-alert {
    color: #f0e9df;
}
.admin-metric-value-success {
    color: #f0e9df;
}
.admin-metric-label {
    margin-top: 4px;
    color: #8a7a66;
    font-size: 0.95rem;
}
.admin-metric-sub {
    margin-left: auto;
    color: #8a7a66;
    font-size: 0.9rem;
}
.admin-metric-sub-alert {
    color: #8a7a66;
}
.admin-ticket-panel {
    background: rgba(42, 44, 48, 0.78);
    border-radius: 20px;
    padding: 22px 24px;
    border: 1px solid rgba(214, 168, 91, 0.14);
    backdrop-filter: blur(10px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.14);
}
.admin-ticket-panel-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    margin-bottom: 16px;
    flex-wrap: wrap;
}
.admin-ticket-panel-title {
    margin: 0;
    color: #f0e9df;
    font-size: 1.5rem;
    font-family: 'Playfair Display', serif;
}
.admin-ticket-panel-sub {
    margin-top: 2px;
    color: #8a7a66;
    font-size: 0.95rem;
}
.admin-ticket-filters {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}
.admin-filter-select {
    padding: 10px 14px;
    border-radius: 12px;
    font-size: 0.9rem;
    background: rgba(37, 39, 42, 0.9);
    border: 1px solid rgba(214, 168, 91, 0.14);
    color: #d0c8b8;
}
.admin-ticket-panel-divider {
    height: 1px;
    background: linear-gradient(to right, rgba(214, 168, 91, 0.3), rgba(214, 168, 91, 0.05), transparent);
    margin-bottom: 24px;
}
.ticket-card {
    margin-top: 12px;
}
.ticket-card-clean-hidden {
    display: none !important;
}
.ticket-card-clean-meta {
    display: block;
    color: #9b8d81;
    font-size: .68rem;
    font-weight: 700;
    letter-spacing: 0;
    text-transform: uppercase;
}
.ticket-card-clean-title {
    margin: 7px 0 4px !important;
    color: #342a23 !important;
    font-family: 'Inter', sans-serif !important;
    font-size: .95rem !important;
    font-weight: 400 !important;
    line-height: 1.35 !important;
}
.ticket-card-clean-copy {
    margin: 0 !important;
    color: #786b60 !important;
    font-size: .8rem !important;
    line-height: 1.55 !important;
}
.admin-ticket-panel:not(.admin-ticket-archive) .ticket-card-shell {
    border: 1px solid #e3d8ca !important;
    border-radius: 10px !important;
    background: #fbf8f3 !important;
    box-shadow: none !important;
}
.admin-ticket-panel:not(.admin-ticket-archive) .ticket-card-shell-critical {
    border: 1px solid rgba(185, 80, 66, 0.32) !important;
    border-left: 4px solid #d9564a !important;
    border-radius: 10px !important;
    background: #fff5f4 !important;
    box-shadow: 0 2px 8px rgba(185, 80, 66, 0.08) !important;
}
.admin-ticket-panel:not(.admin-ticket-archive) .ticket-card-shell:hover {
    border-color: #d9c7af !important;
    background: #fffdf9 !important;
    box-shadow: none !important;
    transform: none !important;
}
.admin-ticket-panel:not(.admin-ticket-archive):not(.admin-operations-queue) .ticket-card-body {
    padding: 15px 16px !important;
}
.admin-ticket-panel:not(.admin-ticket-archive) .ticket-card-action {
    min-height: 38px !important;
    padding: 0.5rem 1rem !important;
    border-radius: 8px !important;
    box-shadow: none !important;
    font-size: 0.8125rem !important;
}
.admin-ticket-panel:not(.admin-ticket-archive) .ticket-card-action svg {
    display: block !important;
    width: 14px !important;
    height: 14px !important;
}
.admin-operations-queue {
    --operations-row-padding: 24px 30px;
    /* overflow-x hidden keeps the rounded card edges clean; overflow-y must stay
       visible or the priority dropdown menu (an absolutely-positioned child)
       gets clipped when it opens below the filter button. */
    overflow-x: hidden;
    overflow-y: visible;
    padding: 0;
    border: 1px solid rgba(107, 79, 58, 0.22);
    border-radius: 14px;
    background: #6B4F3A;
    box-shadow: 0 14px 28px rgba(79, 58, 44, 0.12);
}
.admin-operations-queue .admin-ticket-panel-head {
    margin: 0;
    padding: 22px 30px 20px;
    border-bottom: 0;
    background: #6B4F3A;
}
.admin-operations-queue .admin-ticket-panel-title {
    color: #fff7ea;
}
.admin-operations-queue .admin-ticket-panel-sub {
    color: rgba(255, 247, 234, 0.78);
}
.admin-operations-queue .admin-operations-filter-native {
    position: absolute;
    width: 1px;
    height: 1px;
    overflow: hidden;
    opacity: 0;
    pointer-events: none;
}
/* ==========================================================================
   COMPLETE DROPDOWN INTERACTION LAYER (ADMIN OPERATIONS FILTER)
   ========================================================================== */

/* 1. Interactive Wrapper Container */
.admin-operations-queue .admin-priority-dropdown {
    position: relative;
    display: inline-flex;
    z-index: 40; /* Lifts interaction plane above nearby cards */
} 

/* 2. Base Filter Button Styling */
.admin-operations-queue .admin-operations-filter {
    /* --- Layout & Structure --- */
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 45px;
    padding: 0 20px;
    border: none !important; 
    border-radius: 777px !important;
    white-space: nowrap;

    /* --- Typography --- */
    font-size: .74rem;
    font-weight: 800;
    letter-spacing: .075em;
    line-height: 1;
    text-transform: uppercase;
    text-decoration: none;

    /* --- Color & Depth (Premium Gold Gradient) --- */
    background: linear-gradient(90deg, #B8842F 0%, #D6A85B 100%) !important;
    color: #FFFFFF !important;
    box-shadow: 0 12px 28px rgba(199, 150, 69, 0.3) !important;
    
    /* --- Clickability Safeguards --- */
    cursor: pointer;
    pointer-events: auto !important; 

    /* --- Smooth Physics Transitions --- */
    transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.3s ease, filter 0.3s ease !important;
}

/* 3. Embedded Chevron Icon Configuration */
.admin-operations-queue .admin-operations-filter svg {
    width: 16px;
    height: 16px;
    flex: 0 0 auto;
    fill: currentColor !important; /* Forces arrow to match white text color */
    pointer-events: none; /* Passes click handlers through to the main button */
    transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
}

/* 4. Hover & Focus Micro-Animations */
.admin-operations-queue .admin-operations-filter:hover,
.admin-operations-queue .admin-operations-filter:focus-visible {
    transform: translateY(-3px) !important;
    box-shadow: 0 20px 40px rgba(199, 150, 69, 0.4) !important;
    filter: brightness(1.08) !important; /* Premium luxury shine glow */
    outline: none !important;
}

/* Subtle downward bounce on the inner chevron during button hover */
.admin-operations-queue .admin-operations-filter:hover svg {
    transform: translateY(1px);
}

/* 5. Click Active / Pressed Physics */
.admin-operations-queue .admin-operations-filter:active {
    transform: translateY(-1px) !important;
    box-shadow: 0 8px 16px rgba(199, 150, 69, 0.3) !important;
    filter: brightness(0.95) !important;
}

/* 6. Active State When Dropdown Menu Is Open */
.admin-operations-queue .admin-priority-dropdown.is-open .admin-operations-filter {
    transform: translateY(-1px) !important; 
    background: linear-gradient(90deg, #B8842F 0%, #D6A85B 100%) !important;
    color: #FFFFFF !important;
    box-shadow: 0 10px 20px rgba(199, 150, 69, 0.25) !important;
    filter: brightness(0.93) !important; /* Subtle tint indicator that menu is active */
}

/* Flipping the internal arrow completely upside down when open */
.admin-operations-queue .admin-priority-dropdown.is-open .admin-operations-filter svg {
    transform: rotate(180deg) !important;
}
/* --- Menu Overlay Fix --- */
.admin-operations-queue .admin-priority-menu {
    position: absolute;
    top: calc(100% + 6px);
    right: 0;
    z-index: 50; /* Keep menu strictly stacked above the button layer */
    width: 190px;
    overflow: hidden;
    padding: 6px;
    border: 1px solid rgba(107, 79, 58, 0.16);
    border-radius: 14px;
    background: #fffaf5;
    box-shadow: 0 16px 32px rgba(47, 39, 31, 0.18);
}
.admin-operations-queue .admin-priority-menu[hidden] {
    display: none;
}
.admin-operations-queue .admin-priority-menu button {
    display: flex;
    width: 100%;
    min-height: 36px;
    align-items: center;
    padding: 0 11px;
    border: 0;
    border-radius: 9px;
    background: transparent;
    color: #4d3b2e;
    font-size: 0.78rem;
    font-weight: 700;
    text-align: left;
    cursor: pointer;
    transition: background-color 0.16s ease, color 0.16s ease;
}
.admin-operations-queue .admin-priority-menu button:hover,
.admin-operations-queue .admin-priority-menu button:focus-visible {
    background: #fff2e8;
    color: #8f2929;
    outline: none;
}
.admin-operations-queue .admin-priority-menu button[aria-selected="true"] {
    background: #f2dfd2;
    color: #7a4f16;
}
.admin-operations-queue .admin-ticket-panel-divider {
    display: none;
}
.admin-operations-queue [data-progressive-list] {
    display: flex;
    flex-direction: column;
    gap: 0 !important;
    overflow: hidden;
    margin: 0 22px;
    border: 1px solid rgba(227, 216, 202, 0.92) !important;
    border-radius: 8px !important;
    background: #fffdf8 !important;
    min-height: 360px; /* Add this */
}
.admin-operations-queue .ticket-card {
    margin: 0 !important;
    border-bottom: 1px solid #e3d8ca !important;
    background: #fffdf8 !important;
}
.admin-operations-queue .ticket-card:last-child {
    border-bottom: 0 !important;
}
.admin-ticket-panel.admin-operations-queue:not(.admin-ticket-archive) .ticket-card-shell,
.admin-ticket-panel.admin-operations-queue:not(.admin-ticket-archive) .ticket-card-shell-critical,
.admin-ticket-panel.admin-operations-queue:not(.admin-ticket-archive) .ticket-card-shell:hover,
.admin-ticket-panel.admin-operations-queue:not(.admin-ticket-archive) .ticket-card-shell-critical:hover {
    border: 0 !important;
    border-radius: 0 !important;
    background: transparent !important;
    box-shadow: none !important;
    transform: none !important;
}
.admin-operations-queue .ticket-card:hover {
    background: #f7f2eb !important;
}
.admin-operations-queue .ticket-card-body {
    padding: var(--operations-row-padding) !important;
}
.admin-operations-queue .app-progressive-action {
    padding: 16px 30px 18px;
    border-top: 0;
    background: #6B4F3A;
}
.admin-operations-queue .app-progressive-action button {
    border-color: rgba(255, 247, 234, 0.22);
    background: rgba(255, 255, 255, 0.06);
    color: #fff7ea;
}
.ticket-card-shell {
    display: flex;
    overflow: hidden;
    position: relative;
    border: 1px solid rgba(214, 168, 91, 0.14);
    border-radius: 16px;
    background: linear-gradient(135deg, #2C2C2F 0%, #25272A 100%);
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.10);
    transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
}
.ticket-card-shell:hover {
    transform: translateY(-2px);
    border-color: rgba(214, 168, 91, 0.30);
    box-shadow: 0 14px 30px rgba(0, 0, 0, 0.20);
}
.ticket-card-shell-critical {
    border-color: rgba(224, 112, 96, 0.46);
    background: linear-gradient(135deg, rgba(91, 40, 39, 0.72) 0%, rgba(54, 39, 40, 0.96) 38%, #25272A 100%);
    box-shadow: 0 8px 24px rgba(111, 38, 35, 0.20);
}
.ticket-card-shell-critical:hover {
    border-color: rgba(224, 112, 96, 0.74);
    box-shadow: 0 16px 34px rgba(111, 38, 35, 0.34);
}
.ticket-card-body {
    flex: 1 1 0%;
    min-width: 0;
    padding: 16px 18px;
}
.ticket-card-layout {
    display: flex;
    flex-direction: column;
    gap: 14px;
}
.ticket-card-main {
    flex: 1 1 0%;
    min-width: 0;
}
.ticket-card-heading, .ticket-card-meta, .ticket-card-meta-item, .ticket-card-actions, .ticket-card-action, .ticket-card-reject-wrap {
    display: flex;
    align-items: center;
}
.ticket-card-heading, .ticket-card-meta {
    flex-wrap: wrap;
}
.ticket-card-heading {
    gap: 8px;
    margin-bottom: 10px;
}
.ticket-card-meta {
    column-gap: 16px;
    row-gap: 8px;
    font-size: 0.75rem;
}
.ticket-card-meta-item {
    gap: 6px;
}
.ticket-card-meta-divider {
    width: 1px;
    height: 12px;
}
.ticket-card-actions {
    display: flex;
    flex-shrink: 0;
    flex-wrap: wrap;
    align-items: center;
    justify-content: flex-end;
    gap: 0.75rem;
}
.ticket-card-action {
    display: inline-flex;
    min-height: 38px;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-size: 0.8125rem;
    font-weight: 700;
    line-height: 1.2;
    text-align: center;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.2s ease-in-out;
}
.ticket-card-action-primary {
    border: 1px solid rgba(67, 124, 78, 0.42);
    background: #cfe7d4;
    color: #24502d;
    box-shadow: none;
}
.ticket-card-action-primary:hover {
    border-color: rgba(67, 124, 78, 0.58);
    background: #b9dcc1;
    color: #1f4727;
    transform: translateY(-1px);
    box-shadow: none;
}
.ticket-card-action-view {
    min-width: 130px;
    border: 1px solid rgba(214, 168, 91, 0.46);
    background: rgba(214, 168, 91, 0.16);
    color: #7a4f16;
}
.ticket-card-action-view:hover {
    border-color: rgba(214, 168, 91, 0.62);
    background: rgba(214, 168, 91, 0.24);
    color: #65400f;
    transform: translateY(-1px);
}
.ticket-card-action-approve {
    min-width: 105px;
    border: 1px solid rgba(67, 124, 78, 0.30);
    background: #dff0e3;
    color: #2f5f39;
}
.ticket-card-action-approve:hover {
    border-color: rgba(67, 124, 78, 0.42);
    background: #cfe7d4;
    color: #24502d;
    transform: translateY(-1px);
}
.ticket-card-action-secondary {
    border: 1px solid rgba(180, 119, 33, 0.22);
    background: rgba(180, 119, 33, 0.12);
    color: #7a4f16;
}
.ticket-card-action-secondary:hover {
    border-color: rgba(180, 119, 33, 0.34);
    background: #fff8ee;
    color: #6b4310;
    transform: translateY(-1px);
}
.admin-ticket-panel:not(.admin-ticket-archive) .ticket-card-action-approve {
    border-color: rgba(67, 124, 78, 0.34) !important;
    background: #dff0e3 !important;
    color: #2f5f39 !important;
}
.admin-ticket-panel:not(.admin-ticket-archive) .ticket-card-action-approve:hover {
    border-color: rgba(67, 124, 78, 0.48) !important;
    background: #cfe7d4 !important;
    color: #24502d !important;
}
.ticket-card-reject-wrap {
    display: flex;
    align-items: center;
}
.ticket-card-action-reject {
    min-width: 95px;
    border: 1px solid rgba(224, 112, 96, 0.34);
    background: #fde8e6;
    color: #9d3129;
}
.ticket-card-action-reject:hover {
    border-color: rgba(224, 112, 96, 0.48);
    background: #fbd8d5;
    color: #842820;
    transform: translateY(-1px);
}
.ticket-card-action-danger-text {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 0.5rem 0.25rem;
    border: 0;
    border-radius: 6px;
    background: transparent;
    color: rgba(157, 49, 41, 0.74);
    cursor: pointer;
    font-size: 0.8125rem;
    font-weight: 600;
    line-height: 1.2;
    transition: all 0.2s ease-in-out;
}
.ticket-card-action-danger-text:hover {
    background: transparent;
    color: rgba(132, 40, 32, 1);
    transform: translateY(-1px);
    text-decoration: none;
}
.ticket-card-status {
    padding: 4px 9px;
    border: 1px solid rgba(214, 168, 91, 0.16);
    border-radius: 999px;
    background: rgba(214, 168, 91, 0.10);
    color: #D6A85B;
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
}
.ticket-card-status-in-progress, .ticket-card-status-completed {
    border-color: rgba(90, 138, 90, 0.20);
    background: rgba(90, 138, 90, 0.14);
    color: #A8CAA8;
}
.ticket-card-status-assigned {
    border-color: rgba(190, 147, 96, 0.22);
    background: rgba(190, 147, 96, 0.14);
    color: #D7B48D;
}
.admin-ticket-modal-backdrop {
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    width: 100vw;
    min-height: 100vh;
    min-height: 100dvh;
    margin: 0;
    padding: 24px;
    z-index: 1000;
}
body.admin-ticket-modal-open {
    overflow: hidden;
}
.admin-ticket-modal {
    width: min(460px, calc(100vw - 32px));
    padding: 26px;
    border: 1px solid rgba(214, 168, 91, 0.20);
    border-radius: 22px;
    background: linear-gradient(135deg, #2A2C30 0%, #1F2023 100%);
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.42);
}
.admin-ticket-modal-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 18px;
    margin-bottom: 20px;
}
.admin-ticket-modal-head h3 {
    margin: 0;
    color: #F8F3EA;
    font-family: 'Playfair Display', serif;
    font-size: 1.45rem;
}
.admin-ticket-modal-head p {
    margin: 5px 0 0;
    color: #AFA18F;
    font-size: 0.84rem;
    line-height: 1.55;
}
.admin-ticket-modal-head button {
    border: 0;
    background: transparent;
    color: #AFA18F;
    font-size: 1.8rem;
    line-height: 1;
    cursor: pointer;
}
.admin-ticket-modal-label {
    display: block;
    margin-bottom: 8px;
    color: #D6A85B;
    font-size: 0.76rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}
.admin-ticket-modal-textarea {
    width: 100%;
    min-height: 100px;
    resize: vertical;
    padding: 12px 14px;
    border: 1px solid rgba(214, 168, 91, 0.18);
    border-radius: 12px;
    background: rgba(37, 39, 42, 0.92);
    color: #F8F3EA;
    outline: none;
}
.admin-ticket-modal-textarea:focus {
    border-color: rgba(214, 168, 91, 0.52);
    box-shadow: 0 0 0 3px rgba(214, 168, 91, 0.08);
}
.admin-ticket-modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 20px;
}
.admin-ticket-modal-actions button {
    padding: 10px 15px;
    border-radius: 999px;
    font-size: 0.8rem;
    font-weight: 700;
    cursor: pointer;
}
.admin-ticket-modal-secondary {
    border: 1px solid rgba(168, 159, 145, 0.20);
    background: rgba(168, 159, 145, 0.08);
    color: #B0A898;
}
.admin-ticket-modal-primary {
    border: 1px solid rgba(214, 168, 91, 0.22);
    background: linear-gradient(90deg, #B8842F, #D6A85B);
    color: #fff;
}
.admin-ticket-modal-danger {
    border: 1px solid rgba(224, 112, 96, 0.32);
    background: rgba(224, 112, 96, 0.16);
    color: #F0A195;
}
.ticket-card svg {
    display: block;
    width: 14px !important;
    height: 14px !important;
    min-width: 14px;
    flex: 0 0 14px;
}
.ticket-card button svg, .ticket-card a svg {
    pointer-events: none;
}
@media (min-width:1280px) {
    .ticket-card-layout {
        flex-direction: row;
        align-items: flex-start;
        justify-content: space-between;
    }
}
.admin-ticket-archive {
    --archive-row-padding: 13px 18px;
    display: block;
    overflow: hidden;
    padding: 0;
    border: 1px solid rgba(107, 79, 58, 0.20);
    border-radius: 14px;
    background: #fffdf8;
    box-shadow: 0 12px 24px rgba(79, 58, 44, 0.09);
}
.admin-archive-tabs {
    display: flex;
    align-items: center;
    gap: 3px;
    overflow-x: auto;
    margin: 0;
    padding: 0 18px;
    border-bottom: 0;
    background: #fffdf8;
}
.admin-archive-tab {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    min-width: max-content;
    padding: 12px 15px;
    border: 0;
    border-bottom: 2px solid transparent;
    background: transparent;
    color: #5d5043;
    font-size: 0.82rem;
    font-weight: 600;
    cursor: pointer;
    transition: color 0.18s ease, border-color 0.18s ease, background 0.18s ease;
}
.admin-archive-tab svg {
    width: 16px;
    height: 16px;
    flex: 0 0 auto;
}
.admin-archive-tab span {
    display: inline;
    color: #8a7a66;
    font-size: 0.74rem;
}
.admin-archive-tab:hover {
    color: #b47721;
    background: rgba(180, 119, 33, 0.08);
}
.admin-archive-tab.is-active {
    border-bottom-color: #D6A85B;
    color: #D6A85B;
}
.admin-archive-tab.is-active span {
    color: #D6A85B;
}
.admin-archive-tabs + .admin-ticket-panel-divider {
    display: none;
}
.admin-archive-section[hidden] {
    display: none;
}
.admin-archive-section {
    background: transparent;
}
.admin-archive-section > .admin-ticket-panel-head {
    margin: 0;
    padding: 22px 24px 16px;
    border-bottom: 1px solid rgba(255, 247, 234, 0.28);
    background: #6B4F3A;
}
.admin-archive-section > .admin-ticket-panel-head .admin-ticket-panel-title {
    color: #fff7ea;
}
.admin-archive-section > .admin-ticket-panel-head .admin-ticket-panel-sub {
    color: rgba(255, 247, 234, 0.78);
}
.admin-archive-section > .admin-ticket-panel-divider {
    display: none;
}
.admin-status-stack {
    display: flex;
    flex-direction: column;
    gap: 0;
    overflow: hidden;
    margin: 0;
    border: 0;
    border-radius: 0;
    background: #fffdf8;
}
.admin-status-card.is-hidden-by-default {
    display: none !important;
}
.admin-collapsible-action {
    display: flex;
    justify-content: flex-end;
    padding: 8px 18px;
    border-top: 1px solid rgba(255, 247, 234, 0.14);
    background: #6B4F3A;
}
.admin-collapsible-toggle {
    display: inline-flex;
    min-height: 34px;
    align-items: center;
    gap: 7px;
    padding: 7px 11px;
    border: 1px solid rgba(255, 247, 234, 0.22);
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.06);
    color: #fff7ea;
    font-size: 0.76rem;
    font-weight: 800;
    cursor: pointer;
    transition: background 0.18s ease, border-color 0.18s ease, transform 0.18s ease;
}
.admin-collapsible-toggle:hover {
    border-color: rgba(255, 247, 234, 0.34);
    background: rgba(255, 255, 255, 0.12);
    transform: translateY(-1px);
}
.admin-collapsible-note {
    color: rgba(255, 247, 234, 0.6);
    font-size: 0.76rem;
    font-weight: 700;
    letter-spacing: 0.02em;
}
.admin-status-card {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 14px;
    padding: var(--archive-row-padding);
    border: 0 !important;
    border-bottom: 1px solid #e3d8ca !important;
    border-radius: 0;
    background: #fffdf8 !important;
    box-shadow: none;
    transition: background 0.18s ease;
}
.admin-status-card:last-child {
    border-bottom: 0 !important;
}
.admin-status-card:hover {
    background: #f7f2eb !important;
}
.admin-status-card-top {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}
.admin-status-clean-meta {
    display: block;
    color: #9b8d81;
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0;
    text-transform: uppercase;
}
.admin-status-card strong {
    display: block;
    margin-top: 7px;
    color: #342a23;
    font-size: 0.95rem;
    font-weight: 700;
    line-height: 1.35;
}
.admin-status-card p {
    margin: 4px 0 0;
    color: #786b60;
    font-size: 0.8rem;
    line-height: 1.55;
}
.admin-status-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 8px;
    color: #8A7A66;
    font-size: 0.76rem;
}
.admin-status-badge {
    padding: 6px 10px;
    border-radius: 999px;
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}
.admin-status-badge-assigned {
    background: rgba(190, 147, 96, 0.15);
    color: #E4C58E;
    border: 1px solid rgba(190, 147, 96, 0.2);
}
.admin-status-badge-finished {
    background: rgba(90, 138, 90, 0.15);
    color: #A8CAA8;
    border: 1px solid rgba(90, 138, 90, 0.2);
}
.admin-status-badge-rejected {
    background: rgba(224, 112, 96, 0.14);
    color: #F0A195;
    border: 1px solid rgba(224, 112, 96, 0.22);
}
@media (max-width:760px) {
    .ticket-card-action {
        min-width: 0;
        width: 100%;
    }
}
.admin-status-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    min-height: 38px;
    min-width: 130px;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    background: rgba(214, 168, 91, 0.16);
    border: 1px solid rgba(214, 168, 91, 0.46);
    color: #7a4f16;
    text-decoration: none;
    font-size: 0.8125rem;
    font-weight: 700;
    line-height: 1.2;
    white-space: nowrap;
    transition: background 0.18s ease, border-color 0.18s ease, color 0.18s ease, transform 0.18s ease;
}
.admin-status-link svg {
    display: block;
    width: 14px;
    height: 14px;
    flex: 0 0 14px;
}
.admin-status-link:hover {
    border-color: rgba(214, 168, 91, 0.62);
    background: rgba(214, 168, 91, 0.24);
    color: #65400f;
    transform: translateY(-1px);
}
body.role-manager .admin-content-shell .admin-ticket-page .admin-status-card .admin-status-link {
    min-width: 130px !important;
    padding: 0.5rem 1rem !important;
    border-color: rgba(214, 168, 91, 0.46) !important;
    background: rgba(214, 168, 91, 0.16) !important;
    color: #7a4f16 !important;
}
body.role-manager .admin-content-shell .admin-ticket-page .admin-status-card .admin-status-link:hover {
    border-color: rgba(214, 168, 91, 0.62) !important;
    background: rgba(214, 168, 91, 0.24) !important;
    color: #65400f !important;
}
.admin-status-empty {
    padding: 28px 20px;
    border-radius: 18px;
    text-align: center;
    color: #8A7A66;
    border: 1px dashed rgba(214, 168, 91, 0.16);
    background: rgba(255, 255, 255, 0.02);
}
/* Custom scrollbar */
::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}
::-webkit-scrollbar-track {
    background: #2A2C30;
    border-radius: 10px;
}
::-webkit-scrollbar-thumb {
    background: #D6A85B;
    border-radius: 10px;
}
.admin-empty-state{
    min-height:360px;
    display:flex;
    flex-direction:column;
    justify-content:center;
    align-items:center;
    text-align:center;
    padding:48px 32px;
    color:#5B4635;
}

.admin-empty-state h3{
    margin-top:18px;
    margin-bottom:10px;
    font-size:28px;
    font-weight:700;
    color:#3F3024;
}

.admin-empty-state p{
    max-width:500px;
    color:#8A7A66;
    line-height:1.7;
}
::-webkit-scrollbar-thumb:hover {
    background: #C49A4A;
}
@media (max-width:1000px) {
    .admin-metrics-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    .admin-critical-brief {
        grid-template-columns: 1fr;
    }
    .admin-critical-focus {
        grid-template-columns: minmax(0, 1fr) minmax(150px, auto);
    }
}
@media (max-width:768px) {
    .dash-root.admin-ticket-page {
        gap: 16px;
    }
    .admin-ticket-page > div:first-of-type {
        padding: 24px !important;
    }
    .admin-ticket-page > div:first-of-type > div.relative > div {
        flex-direction: column !important;
        align-items: flex-start !important;
    }
    .admin-metric-card {
        flex-direction: column;
        align-items: flex-start;
    }
    .admin-metric-sub {
        margin-left: 0;
    }
    .admin-critical-brief {
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: 12px !important;
        padding: 16px !important;
        border-radius: 16px !important;
        background: linear-gradient(180deg, #7b2118 0%, #511a17 100%) !important;
        box-shadow: 0 12px 26px rgba(80, 24, 18, 0.24) !important;
    }
    .admin-critical-brief::before {
        display: none;
    }
    .admin-critical-summary {
        display: grid !important;
        grid-template-columns: 42px minmax(0, 1fr) !important;
        align-items: start !important;
        gap: 12px !important;
    }
    .admin-critical-icon {
        width: 42px !important;
        height: 42px !important;
        border-radius: 14px !important;
    }
    .admin-critical-kicker {
        margin-bottom: 3px !important;
        font-size: 0.68rem !important;
        letter-spacing: 0.08em !important;
    }
    .admin-critical-summary h2 {
        font-family: 'Inter', sans-serif !important;
        font-size: 1.18rem !important;
        line-height: 1.12 !important;
    }
    .admin-critical-metrics {
        display: flex !important;
        gap: 6px !important;
        margin-top: 8px !important;
        overflow-x: auto;
        padding-bottom: 2px;
        scrollbar-width: none;
    }
    .admin-critical-metrics::-webkit-scrollbar {
        display: none;
    }
    .admin-critical-metrics span {
        flex: 0 0 auto;
        min-height: 30px;
        padding: 6px 9px;
        font-size: 0.72rem;
    }
    .admin-critical-focus {
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: 10px !important;
    }
    .admin-critical-card {
        padding: 13px !important;
        border-radius: 14px !important;
        background: rgba(35, 12, 10, 0.24) !important;
    }
    .admin-critical-card-top {
        margin-bottom: 8px;
    }
    .admin-critical-card strong {
        white-space: normal !important;
        font-size: 1rem !important;
        line-height: 1.28 !important;
    }
    .admin-critical-meta {
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: 5px !important;
        margin-top: 9px !important;
        font-size: 0.82rem !important;
    }
    .admin-critical-actions {
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: 8px !important;
        width: 100%;
    }
    .admin-critical-action {
        width: 100%;
        min-height: 50px !important;
        border-radius: 14px !important;
        font-size: 0.92rem !important;
    }
    .admin-critical-action-secondary {
        min-height: 44px !important;
        background: rgba(255, 255, 255, 0.08) !important;
    }
}
@media (max-width:560px) {
    .admin-metrics-grid {
        grid-template-columns: 1fr;
    }
    .admin-ticket-panel {
        padding: 20px;
    }
    .ticket-card-body {
        padding: 16px;
    }
    .admin-ticket-page > div:first-of-type h1 {
        font-size: 2.2rem !important;
    }
    .admin-ticket-page > div:first-of-type p {
        font-size: 0.95rem !important;
    }
    .admin-filter-select, .admin-ticket-filters {
        width: 100%;
    }
    .admin-filter-select {
        min-width: 0;
    }
    .ticket-card-shell {
        flex-direction: column;
    }
    .admin-status-card {
        flex-direction: column;
        align-items: flex-start;
    }
    .admin-status-link {
        width: 100%;
        text-align: center;
    }
}
</style>
</x-app-layout>
