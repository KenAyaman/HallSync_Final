<x-app-layout>
@php
    $urgentTickets = $tickets->filter(fn ($ticket) => $ticket->normalized_priority === 'critical' && $ticket->status !== 'completed');
    $urgentCount = $urgentTickets->count();
    $firstUrgent = $urgentTickets->first();

    $pendingApprovalTickets = $tickets->where('status', 'pending_approval');
    $assignedTickets = $tickets->where('status', 'assigned');
    $queueTickets = $tickets->whereNotIn('status', ['assigned', 'completed']);
    $finishedTickets = $tickets->where('status', 'completed');

    $openCount = $pendingApprovalTickets->count();
    $assignedCount = $assignedTickets->count();
    $inProgressCount = $tickets->where('status', 'in_progress')->count();
    $finishedCount = $finishedTickets->count();

    $priorityMeta = [
        'critical' => [
            'label' => 'Critical',
            'tone' => 'Immediate dispatch',
            'bg' => 'rgba(224,112,96,0.12)',
            'fg' => '#F0B3A9',
            'border' => 'rgba(224,112,96,0.24)',
            'accent' => '#E07060',
        ],
        'medium' => [
            'label' => 'Medium',
            'tone' => 'Standard queue',
            'bg' => 'rgba(190,147,96,0.12)',
            'fg' => '#D7B48D',
            'border' => 'rgba(190,147,96,0.22)',
            'accent' => '#BE9360',
        ],
        'low' => [
            'label' => 'Low',
            'tone' => 'Routine follow-up',
            'bg' => 'rgba(111,160,111,0.10)',
            'fg' => '#A8CAA8',
            'border' => 'rgba(111,160,111,0.22)',
            'accent' => '#6FA06F',
        ],
    ];
@endphp

<div class="dash-root admin-ticket-page">
    @if (session('success'))
        <div class="admin-ticket-flash admin-ticket-flash-success" data-auto-dismiss>{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="admin-ticket-flash admin-ticket-flash-error" data-auto-dismiss>{{ session('error') }}</div>
    @endif

    {{-- PAGE HEADER WITH PREMIUM ADMIN DESIGN --}}
    <div class="relative overflow-hidden rounded-[36px] border border-[#3A342D]"
         style="
            background:
                linear-gradient(115deg,
                    #1A1C1E 0%,
                    #1F2023 38%,
                    #24262B 62%,
                    #2C2C2F 100%);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
         ">

        <div class="absolute top-[-90px] right-[10%] w-[320px] h-[320px] rounded-full blur-3xl opacity-20"
             style="background: rgba(199, 151, 69, 0.3);"></div>

        <div class="absolute bottom-[-120px] left-[18%] w-[260px] h-[260px] rounded-full blur-3xl opacity-10"
             style="background: rgba(255,255,255,0.08);"></div>

        <div class="relative z-10 px-8 py-10 md:px-14 md:py-12">
            <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                <div class="max-w-2xl">
                    <div class="mb-3 flex items-center gap-3">
                        <span class="inline-block w-8 h-8 rounded-full"
                              style="background: linear-gradient(135deg, #D6A85B, #B8842F);"></span>
                        <span class="inline-block text-[11px] tracking-[0.30em] uppercase"
                              style="color: #D6A85B; font-weight: 700;">
                            Admin Control Panel
                        </span>
                    </div>

                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-[1.05] mb-4"
                        style="font-family: 'Playfair Display', serif; color: #F8F3EA;">
                        Maintenance<br>
                        <span style="color: #D6A85B;">Command Center</span>
                    </h1>

                    <p class="text-base md:text-lg leading-relaxed max-w-xl"
                       style="color: rgba(255,255,255,0.7);">
                        Oversee all maintenance operations, assign tasks to handymen,
                        and ensure timely resolution of resident concerns.
                    </p>
                </div>

                <div class="shrink-0 flex items-center gap-3 px-4 py-2 rounded-full"
                     style="background: rgba(214,168,91,0.1); border: 1px solid rgba(214,168,91,0.2);">
                    <span class="text-xs font-mono" style="color: #D6A85B;">👑 Administrator Access</span>
                </div>
            </div>
        </div>
    </div>

    {{-- URGENT ATTENTION BOX --}}
    @if($urgentCount > 0)
        <div class="relative overflow-hidden rounded-[28px]"
             style="
                background: linear-gradient(135deg, rgba(104, 29, 24, 0.72), rgba(224,112,96,0.10));
                border: 1px solid rgba(224,112,96,0.35);
                box-shadow: 0 18px 40px rgba(0,0,0,0.28);
             ">

            <div class="absolute inset-y-0 left-0 w-[6px]"
                 style="background: linear-gradient(180deg, #FF8A7A 0%, #E07060 55%, #B5473A 100%);"></div>

            <div class="absolute top-[-40px] right-[6%] w-[220px] h-[220px] rounded-full blur-3xl opacity-25"
                 style="background: rgba(224,112,96,0.35);"></div>

            <div class="p-6 md:p-7">
                <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-6">
                    <div class="flex items-start gap-4 md:gap-5">
                        <div class="w-16 h-16 rounded-full flex items-center justify-center shrink-0"
                             style="background: rgba(224,112,96,0.18); border: 1px solid rgba(224,112,96,0.28); box-shadow: 0 0 30px rgba(224,112,96,0.12);">
                            <span class="text-[28px]">🚨</span>
                        </div>

                        <div>
                            <div class="text-[11px] font-bold uppercase tracking-[0.24em] mb-2"
                                 style="color: #FF9C8D;">
                                Critical Attention Required
                            </div>

                            <div class="text-2xl md:text-3xl font-bold leading-tight"
                                 style="color: #F8F3EA; font-family: 'Playfair Display', serif;">
                                {{ $urgentCount }} Critical Request{{ $urgentCount != 1 ? 's' : '' }}
                            </div>

                            <p class="mt-2 text-sm md:text-base"
                               style="color: rgba(255,255,255,0.72);">
                                These tickets should be reviewed and assigned before the rest of the queue.
                            </p>
                        </div>
                    </div>

                    @if($firstUrgent)
                        <div class="grid grid-cols-1 lg:grid-cols-[1fr_auto] gap-4 items-stretch xl:min-w-[620px]">
                            <div class="rounded-2xl px-4 py-4 md:px-5"
                                 style="background: rgba(255,255,255,0.04); border: 1px solid rgba(224,112,96,0.18);">
                                <div class="flex flex-wrap items-center gap-2 mb-2">
                                    <span class="px-3 py-1 rounded-full text-[11px] font-bold tracking-[0.12em] uppercase"
                                          style="background: rgba(224,112,96,0.16); color: #FF9C8D;">
                                        Critical Alert
                                    </span>
                                    <span class="text-xs font-mono" style="color: #FFB2A7;">
                                        #{{ $firstUrgent->ticket_id ?? $firstUrgent->id }}
                                    </span>
                                </div>

                                <div class="text-lg md:text-xl font-semibold mb-2"
                                     style="color: #F8F3EA;">
                                    {{ $firstUrgent->title }}
                                </div>

                                <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-xs md:text-sm"
                                     style="color: #D8BEB7;">
                                    <span>{{ $firstUrgent->user->name ?? 'Resident' }}</span>
                                    <span>•</span>
                                    <span>{{ $firstUrgent->created_at->format('M d, Y h:i A') }}</span>
                                    @if(!empty($firstUrgent->location))
                                        <span>•</span>
                                        <span>{{ $firstUrgent->location }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex flex-col gap-3">
                                <button type="button"
                                        onclick="openAssignModal({{ $firstUrgent->id }})"
                                        class="px-5 py-3 rounded-xl text-sm font-semibold transition-all duration-200"
                                        style="background: linear-gradient(90deg, #D66556 0%, #E88977 100%); color: #fff; box-shadow: 0 10px 24px rgba(224,112,96,0.20);"
                                        onmouseover="this.style.transform='translateY(-1px)'"
                                        onmouseout="this.style.transform='translateY(0)'">
                                    Assign Now
                                </button>

                                <a href="{{ route('tickets.show', $firstUrgent) }}"
                                   class="px-5 py-3 rounded-xl text-sm font-semibold text-center transition-all duration-200"
                                   style="background: rgba(255,255,255,0.05); color: #FFD4CC; border: 1px solid rgba(224,112,96,0.22); text-decoration: none;"
                                   onmouseover="this.style.background='rgba(255,255,255,0.09)'"
                                   onmouseout="this.style.background='rgba(255,255,255,0.05)'">
                                    View Details
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- STATS CARDS --}}
    <div class="admin-metrics-grid">
        <div class="admin-metric-card">
            <div class="admin-metric-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 5h6m-7 3h8m-9 11h10a2 2 0 002-2V7a2 2 0 00-2-2h-1.5a1.5 1.5 0 01-3 0h-3a1.5 1.5 0 01-3 0H7a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </div>
            <div class="admin-metric-body">
                <div class="admin-metric-value">{{ $openCount }}</div>
                <div class="admin-metric-label">Awaiting Assignment</div>
            </div>
            <div class="admin-metric-sub">Ready for admin action</div>
        </div>

        <div class="admin-metric-card admin-metric-card-alert">
            <div class="admin-metric-icon admin-metric-icon-alert">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <div class="admin-metric-body">
                <div class="admin-metric-value admin-metric-value-alert">{{ $assignedCount }}</div>
                <div class="admin-metric-label">Assigned</div>
            </div>
            <div class="admin-metric-sub admin-metric-sub-alert">Already routed to staff</div>
        </div>

        <div class="admin-metric-card admin-metric-card-success">
            <div class="admin-metric-icon admin-metric-icon-success">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            <div class="admin-metric-body">
                <div class="admin-metric-value admin-metric-value-success">{{ $inProgressCount }}</div>
                <div class="admin-metric-label">In Progress</div>
            </div>
            <div class="admin-metric-sub">Currently handled by staff</div>
        </div>

        <div class="admin-metric-card">
            <div class="admin-metric-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <div class="admin-metric-body">
                <div class="admin-metric-value">{{ $finishedCount }}</div>
                <div class="admin-metric-label">Finished</div>
            </div>
            <div class="admin-metric-sub">Completed by staff</div>
        </div>
    </div>

    {{-- MAIN OPERATIONS QUEUE --}}
    <div class="admin-ticket-panel">
        <div class="admin-ticket-panel-head">
            <div>
                <h2 class="admin-ticket-panel-title">Operations Queue</h2>
                <p class="admin-ticket-panel-sub">Handle requests that still need admin attention before or during active operations.</p>
            </div>

            <div class="admin-ticket-filters">
                <select id="filterStatus" class="admin-filter-select">
                    <option value="all">All Status</option>
                    <option value="pending_approval">Awaiting Assignment</option>
                    <option value="assigned">Assigned</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                    <option value="rejected">Rejected</option>
                </select>
                <select id="filterPriority" class="admin-filter-select">
                    <option value="all">All Priority</option>
                    <option value="critical">Critical</option>
                    <option value="medium">Medium</option>
                    <option value="low">Low</option>
                </select>
            </div>
        </div>

        <div class="admin-ticket-panel-divider"></div>

        <div class="space-y-3">
            @forelse($queueTickets as $ticket)
                @php
                    $priority = $priorityMeta[$ticket->normalized_priority] ?? $priorityMeta['medium'];
                @endphp
                <div class="ticket-card"
                     data-status="{{ $ticket->status }}"
                     data-priority="{{ $ticket->normalized_priority }}">

                    <div class="flex rounded-[22px] transition-all duration-200 cursor-pointer overflow-hidden"
                         style="
                            background: linear-gradient(135deg, #2C2C2F 0%, #25272A 100%);
                            border: 1px solid rgba(58,52,45,0.6);
                            box-shadow: 0 8px 22px rgba(0,0,0,0.12);
                         "
                         onclick="window.location.href='{{ route('tickets.show', $ticket) }}'"
                         onmouseover="this.style.transform='translateY(-2px)'; this.style.borderColor='rgba(214,168,91,0.32)'; this.style.boxShadow='0 18px 38px rgba(0,0,0,0.22)'"
                         onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='rgba(58,52,45,0.6)'; this.style.boxShadow='0 8px 22px rgba(0,0,0,0.12)'">

                        <div class="flex-1 p-5 md:p-6">
                            <div class="flex flex-col xl:flex-row xl:items-start xl:justify-between gap-5">

                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-2 mb-3">
                                        <span class="text-xs font-mono" style="color: #D6A85B;">
                                            #{{ $ticket->ticket_id ?? $ticket->id }}
                                        </span>

                                        <span class="text-[11px] font-bold px-3 py-1 rounded-full uppercase tracking-[0.10em]"
                                              style="background: {{ $priority['bg'] }}; color: {{ $priority['fg'] }}; border: 1px solid {{ $priority['border'] }};">
                                            {{ $priority['label'] }}
                                        </span>
                                    </div>

                                    <h3 style="font-size: 19px; font-weight: 700; color: #F8F3EA; margin-bottom: 8px; line-height: 1.25;">
                                        {{ $ticket->title }}
                                    </h3>

                                    <p style="font-size: 13px; color: #B0A898; line-height: 1.6; margin-bottom: 14px; max-width: 900px;">
                                        {{ Str::limit($ticket->description, 120) }}
                                    </p>

                                    <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-xs"
                                         style="color: #8A7A66;">
                                        <div class="flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            {{ $ticket->user->name ?? 'Resident' }}
                                        </div>

                                        <div class="w-px h-3" style="background: #5A4A3A;"></div>

                                        <div class="flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            {{ $ticket->created_at->format('M d, Y h:i A') }}
                                        </div>

                                        @if(!empty($ticket->location))
                                            <div class="w-px h-3" style="background: #5A4A3A;"></div>

                                            <div class="flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0L6.343 16.657a8 8 0 1111.314 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                {{ $ticket->location }}
                                            </div>
                                        @endif

                                        @if(!empty($ticket->assignedTo?->name))
                                            <div class="w-px h-3" style="background: #5A4A3A;"></div>

                                            <div class="flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                Assigned to {{ $ticket->assignedTo->name }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex flex-wrap items-center gap-3 mt-4">
                                        <div class="px-3.5 py-2 rounded-2xl"
                                             style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); min-width: 168px;">
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="inline-block w-2.5 h-2.5 rounded-full"
                                                      style="background: {{ $priority['accent'] }};"></span>
                                                <span class="text-[11px] font-semibold uppercase tracking-[0.12em]"
                                                      style="color: {{ $priority['fg'] }};">
                                                    {{ $priority['label'] }}
                                                </span>
                                            </div>
                                            <div class="text-xs" style="color: #9F9485;">
                                                {{ $priority['tone'] }}
                                            </div>
                                        </div>

                                        <span class="px-3 py-1 rounded-full text-xs font-semibold"
                                              style="background: {{ $priority['bg'] }}; color: {{ $priority['fg'] }}; border: 1px solid {{ $priority['border'] }};">
                                            {{ $ticket->priority_label }}
                                        </span>

                                        <span class="px-3 py-1 rounded-full text-xs font-semibold"
                                              style="background:
                                                {{ $ticket->status === 'completed' ? 'rgba(90,138,90,0.15)' :
                                                   ($ticket->status === 'in_progress' ? 'rgba(90,138,90,0.15)' :
                                                   ($ticket->status === 'assigned' ? 'rgba(190,147,96,0.15)' : 'rgba(214,168,91,0.15)')) }};
                                                     color:
                                                {{ $ticket->status === 'completed' ? '#5A8A5A' :
                                                   ($ticket->status === 'in_progress' ? '#5A8A5A' :
                                                   ($ticket->status === 'assigned' ? '#BE9360' : '#D6A85B')) }};
                                                     border: 1px solid rgba(255,255,255,0.04);">
                                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                        </span>
                                    </div>
                                </div>

                                <div class="flex xl:flex-col gap-2 flex-shrink-0"
                                     onclick="event.stopPropagation()">
                                    <button type="button"
                                            onclick="openAssignModal({{ $ticket->id }})"
                                            class="px-4 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 flex items-center justify-center gap-1.5"
                                            style="background: rgba(214,168,91,0.12); color: #D6A85B; border: 1px solid rgba(214,168,91,0.2);"
                                            onmouseover="this.style.background='rgba(214,168,91,0.22)'"
                                            onmouseout="this.style.background='rgba(214,168,91,0.12)'">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        {{ $ticket->assigned_to ? 'Reassign' : 'Assign' }}
                                    </button>

                                    <a href="{{ route('tickets.show', $ticket) }}"
                                       class="px-4 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 flex items-center justify-center gap-1.5"
                                       style="background: rgba(190,147,96,0.1); color: #BE9360; border: 1px solid rgba(190,147,96,0.2); text-decoration: none;"
                                       onmouseover="this.style.background='rgba(190,147,96,0.18)'"
                                       onmouseout="this.style.background='rgba(190,147,96,0.1)'">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        Details
                                    </a>

                                    @if($ticket->status === 'pending_approval')
                                        <div class="flex gap-2">
                                            <button onclick="openRejectModal({{ $ticket->id }})" 
                                                    class="px-4 py-2 rounded-xl text-xs font-semibold flex items-center gap-1.5"
                                                    style="background: rgba(224,112,96,0.15); color: #E07060; border: 1px solid rgba(224,112,96,0.3);"
                                                    onmouseover="this.style.background='rgba(224,112,96,0.22)'"
                                                    onmouseout="this.style.background='rgba(224,112,96,0.15)'">
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
                <div style="text-align: center; padding: 80px 40px; background: linear-gradient(135deg, rgba(37,39,42,0.4), rgba(31,32,35,0.4)); border: 1px dashed rgba(214,168,91,0.2); border-radius: 24px;">
                    <div class="w-20 h-20 rounded-full mx-auto mb-5 flex items-center justify-center"
                         style="background: rgba(214,168,91,0.1);">
                        <span class="text-4xl">🎫</span>
                    </div>
                    <h3 class="text-2xl font-semibold mb-2"
                        style="color: #F8F3EA; font-family: 'Playfair Display', serif;">
                        No requests in queue
                    </h3>
                    <p style="color: #8A7A66;" class="max-w-md mx-auto">
                        Assigned and finished work now lives in its own section, so the admin queue is fully clear.
                    </p>
                </div>
            @endforelse
        </div>
    </div>

    <div class="admin-ticket-section-grid">
        <div class="admin-ticket-panel">
            <div class="admin-ticket-panel-head">
                <div>
                    <h2 class="admin-ticket-panel-title">Assigned</h2>
                    <p class="admin-ticket-panel-sub">Tickets already handed over to staff and waiting for work to start.</p>
                </div>
                <span class="admin-section-chip">{{ $assignedCount }} assigned</span>
            </div>

            <div class="admin-ticket-panel-divider"></div>

            <div class="admin-status-stack">
                @forelse($assignedTickets as $ticket)
                    <div class="admin-status-card {{ $loop->index >= 3 ? 'is-hidden-by-default' : '' }}" data-collapsible-item="assigned">
                        <div>
                            <div class="admin-status-card-top">
                                <strong>{{ $ticket->title }}</strong>
                                <span class="admin-status-badge admin-status-badge-assigned">Assigned</span>
                            </div>
                            <p>{{ Str::limit($ticket->description, 95) }}</p>
                            <div class="admin-status-meta">
                                <span>#{{ $ticket->ticket_id ?? $ticket->id }}</span>
                                <span>{{ $ticket->assignedTo->name ?? 'Staff not set' }}</span>
                                <span>{{ $ticket->updated_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <a href="{{ route('tickets.show', $ticket) }}" class="admin-status-link">View</a>
                    </div>
                @empty
                    <div class="admin-status-empty">No assigned tickets right now.</div>
                @endforelse
            </div>
            @if($assignedCount > 3)
                <div class="admin-collapsible-action">
                    <button type="button" class="admin-collapsible-toggle" data-target-list="assigned" data-expand-label="See more" data-collapse-label="Hide more">See more</button>
                </div>
            @endif
        </div>

        <div class="admin-ticket-panel">
            <div class="admin-ticket-panel-head">
                <div>
                    <h2 class="admin-ticket-panel-title">Completed</h2>
                    <p class="admin-ticket-panel-sub">Completed requests so admin can review what staff already resolved.</p>
                </div>
                <span class="admin-section-chip admin-section-chip-success">{{ $finishedCount }} completed</span>
            </div>

            <div class="admin-ticket-panel-divider"></div>

            <div class="admin-status-stack">
                @forelse($finishedTickets as $ticket)
                    <div class="admin-status-card {{ $loop->index >= 3 ? 'is-hidden-by-default' : '' }}" data-collapsible-item="completed">
                        <div>
                            <div class="admin-status-card-top">
                                <strong>{{ $ticket->title }}</strong>
                                <span class="admin-status-badge admin-status-badge-finished">Finished</span>
                            </div>
                            <p>{{ Str::limit($ticket->description, 95) }}</p>
                            <div class="admin-status-meta">
                                <span>#{{ $ticket->ticket_id ?? $ticket->id }}</span>
                                <span>{{ $ticket->assignedTo->name ?? 'No staff recorded' }}</span>
                                <span>Completed {{ $ticket->updated_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <a href="{{ route('tickets.show', $ticket) }}" class="admin-status-link">View</a>
                    </div>
                @empty
                    <div class="admin-status-empty">No finished tickets yet.</div>
                @endforelse
            </div>
            @if($finishedCount > 3)
                <div class="admin-collapsible-action">
                    <button type="button" class="admin-collapsible-toggle" data-target-list="completed" data-expand-label="See more" data-collapse-label="Hide more">See more</button>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- ASSIGN MODAL --}}
<div id="assignModal"
     class="fixed inset-0 bg-black/70 hidden items-center justify-center z-50 backdrop-blur-sm"
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
                    class="text-3xl cursor-pointer leading-none transition-all duration-200 hover:opacity-70"
                    style="color: #8A7A66; background: none; border: none;">
                ×
            </button>
        </div>

        <form id="assignForm" method="POST">
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
                        class="flex-1 px-4 py-3 rounded-xl font-semibold transition-all duration-200"
                        style="background: rgba(168,159,145,0.1); color: #B0A898; border: 1px solid rgba(168,159,145,0.2);"
                        onmouseover="this.style.background='rgba(168,159,145,0.2)'"
                        onmouseout="this.style.background='rgba(168,159,145,0.1)'">
                    Cancel
                </button>

                <button type="submit"
                        class="flex-1 px-4 py-3 rounded-xl font-bold transition-all duration-200 flex items-center justify-center gap-2"
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
function openAssignModal(ticketId) {
    const modal = document.getElementById('assignModal');
    const form = document.getElementById('assignForm');
    form.action = `/tickets/${ticketId}/assign`;
    modal.style.display = 'flex';
}

function closeAssignModal() {
    const modal = document.getElementById('assignModal');
    modal.style.display = 'none';
}

function openApproveModal(ticketId) {
    const modal = document.getElementById('approveModal');
    const form = document.getElementById('approveForm');
    form.action = `/tickets/${ticketId}/approve`;
    modal.style.display = 'flex';
}

function closeApproveModal() {
    const modal = document.getElementById('approveModal');
    modal.style.display = 'none';
}

function openRejectModal(ticketId) {
    const modal = document.getElementById('rejectModal');
    const form = document.getElementById('rejectForm');
    form.action = `/tickets/${ticketId}/reject`;
    modal.style.display = 'flex';
}

function closeRejectModal() {
    const modal = document.getElementById('rejectModal');
    modal.style.display = 'none';
}

const filterStatus = document.getElementById('filterStatus');
const filterPriority = document.getElementById('filterPriority');

if (filterStatus && filterPriority) {
    filterStatus.addEventListener('change', filterTickets);
    filterPriority.addEventListener('change', filterTickets);
}

function filterTickets() {
    const statusFilter = document.getElementById('filterStatus').value;
    const priorityFilter = document.getElementById('filterPriority').value;
    const tickets = document.querySelectorAll('.ticket-card');

    tickets.forEach(ticket => {
        const status = ticket.dataset.status;
        const priority = ticket.dataset.priority;

        const statusMatch = statusFilter === 'all' || status === statusFilter;
        const priorityMatch = priorityFilter === 'all' || priority === priorityFilter;

        if (statusMatch && priorityMatch) {
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
                this.style.display = 'none';
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
                item.style.display = expanded ? 'none' : 'flex';
            }
        });

        button.dataset.expanded = expanded ? 'false' : 'true';
        button.textContent = expanded ? button.dataset.expandLabel : button.dataset.collapseLabel;
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
    box-shadow: 0 12px 24px rgba(0,0,0,0.14);
}

.admin-ticket-flash-success {
    background: linear-gradient(180deg, rgba(46, 58, 41, 0.92) 0%, rgba(34, 46, 31, 0.92) 100%);
    border: 1px solid rgba(157, 195, 117, 0.18);
    color: #D5E3BE;
}

.admin-ticket-flash-error {
    background: linear-gradient(180deg, rgba(53, 38, 35, 0.92) 0%, rgba(42, 31, 29, 0.92) 100%);
    border: 1px solid rgba(224,112,96,0.22);
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

.urgent-icon-label {
    color: #ffb2a7;
    font-size: 0.75rem;
    font-weight: 800;
    letter-spacing: 0.14em;
    text-transform: uppercase;
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
    border: 1px solid rgba(214,168,91,0.18) !important;
    box-shadow: none !important;
}

.admin-ticket-page > div:first-of-type::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(rgba(214,168,91,0.04) 1px, transparent 1px),
        linear-gradient(90deg, rgba(214,168,91,0.04) 1px, transparent 1px);
    background-size: 48px 48px;
    pointer-events: none;
}

.admin-ticket-page > div:first-of-type > div.absolute:first-child {
    top: -60px !important;
    right: -40px !important;
    width: 280px !important;
    height: 280px !important;
    background: radial-gradient(circle, rgba(214,168,91,0.15) 0%, transparent 70%) !important;
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
    color: rgba(255,255,255,0.62) !important;
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
    color: rgba(255,255,255,0.4) !important;
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

.admin-ticket-page > div:nth-of-type(2) {
    border-radius: 20px !important;
    background: linear-gradient(180deg, rgba(53, 38, 35, 0.92) 0%, rgba(42, 31, 29, 0.92) 100%) !important;
    border: 1px solid rgba(224,112,96,0.18) !important;
    box-shadow: 0 16px 32px rgba(0,0,0,0.18) !important;
}

.admin-ticket-page > div:nth-of-type(2) > div.absolute:first-child {
    width: 4px !important;
    background: #e07060 !important;
}

.admin-ticket-page > div:nth-of-type(2) > div.absolute:nth-child(2) {
    display: none !important;
}

.admin-ticket-page > div:nth-of-type(2) .w-16.h-16.rounded-full {
    width: 44px !important;
    height: 44px !important;
    border-radius: 12px !important;
    background: rgba(224,112,96,0.12) !important;
    border: 1px solid rgba(224,112,96,0.16) !important;
    box-shadow: none !important;
}

.admin-ticket-page > div:nth-of-type(2) .w-16.h-16.rounded-full span {
    font-size: 0 !important;
}

.admin-ticket-page > div:nth-of-type(2) .w-16.h-16.rounded-full::before {
    content: 'Urgent';
    color: #f2b0a5;
    font-size: 0.68rem;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.admin-ticket-page > div:nth-of-type(2) .text-\[11px\].font-bold.uppercase.tracking-\[0\.24em\] {
    color: #f2b0a5 !important;
    letter-spacing: 0.16em !important;
}

.admin-ticket-page > div:nth-of-type(2) .text-2xl.md\:text-3xl {
    font-size: 1.65rem !important;
    line-height: 1.2 !important;
}

.admin-ticket-page > div:nth-of-type(2) .grid.grid-cols-1.lg\:grid-cols-\[1fr_auto\] {
    min-width: 0 !important;
}

.admin-ticket-page > div:nth-of-type(2) .rounded-2xl.px-4.py-4 {
    background: rgba(255,255,255,0.03) !important;
    border: 1px solid rgba(255,255,255,0.06) !important;
}

.admin-ticket-page > div:nth-of-type(2) .rounded-2xl.px-4.py-4 .text-xs.font-mono {
    color: #c9b8a5 !important;
}

.admin-ticket-page > div:nth-of-type(2) .rounded-2xl.px-4.py-4 .flex.flex-wrap.items-center.gap-x-4 span:nth-child(even) {
    opacity: 0.5;
}

.admin-ticket-page > div:nth-of-type(2) .flex.flex-col.gap-3 button,
.admin-ticket-page > div:nth-of-type(2) .flex.flex-col.gap-3 a {
    box-shadow: none !important;
    border-radius: 12px !important;
}

.admin-metrics-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 16px;
}

.admin-metric-card {
    background: rgba(37,39,42,0.88);
    border-radius: 16px;
    padding: 18px 20px;
    border: 1px solid rgba(214,168,91,0.14);
    display: flex;
    align-items: center;
    gap: 14px;
    color: #c4b8a8;
    backdrop-filter: blur(10px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.14);
    transition: transform 0.2s ease, border-color 0.2s ease;
}

.admin-metric-card:hover {
    transform: translateY(-2px);
    border-color: rgba(214,168,91,0.26);
}

.admin-metric-card-alert {
    border-color: rgba(224,112,96,0.18);
}

.admin-metric-card-success {
    border-color: rgba(90,138,90,0.18);
}

.admin-metric-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(214,168,91,0.12);
    color: #d6a85b;
    flex-shrink: 0;
}

.admin-metric-icon-alert {
    background: rgba(224,112,96,0.12);
    color: #e07060;
}

.admin-metric-icon-success {
    background: rgba(90,138,90,0.14);
    color: #5a8a5a;
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
    color: #e07060;
}

.admin-metric-value-success {
    color: #5a8a5a;
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
    color: #e39a8f;
}

.admin-ticket-panel {
    background: rgba(42,44,48,0.78);
    border-radius: 20px;
    padding: 22px 24px;
    border: 1px solid rgba(214,168,91,0.14);
    backdrop-filter: blur(10px);
    box-shadow: 0 12px 24px rgba(0,0,0,0.14);
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
    font-weight: 600;
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
    background: rgba(37,39,42,0.9);
    border: 1px solid rgba(214,168,91,0.14);
    color: #d0c8b8;
}

.admin-ticket-panel-divider {
    height: 1px;
    background: linear-gradient(to right, rgba(214,168,91,0.3), rgba(214,168,91,0.05), transparent);
    margin-bottom: 24px;
}

.admin-ticket-section-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 22px;
}

.admin-section-chip {
    padding: 8px 14px;
    border-radius: 999px;
    background: rgba(214,168,91,0.10);
    border: 1px solid rgba(214,168,91,0.16);
    color: #D6A85B;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
}

.admin-section-chip-success {
    background: rgba(90,138,90,0.12);
    border-color: rgba(90,138,90,0.18);
    color: #A8CAA8;
}

.admin-status-stack {
    display: grid;
    gap: 12px;
}

.admin-status-card.is-hidden-by-default {
    display: none;
}

.admin-collapsible-action {
    display: flex;
    justify-content: flex-end;
    margin-top: 12px;
}

.admin-collapsible-toggle {
    border: none;
    background: transparent;
    color: #D6A85B;
    font-size: 0.88rem;
    font-weight: 700;
    cursor: pointer;
}

.admin-status-card {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 14px;
    padding: 16px 18px;
    border-radius: 18px;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.05);
}

.admin-status-card-top {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.admin-status-card strong {
    color: #F8F3EA;
    font-size: 0.98rem;
    font-weight: 700;
}

.admin-status-card p {
    margin: 8px 0 0;
    color: #B0A898;
    font-size: 0.88rem;
    line-height: 1.65;
}

.admin-status-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 10px;
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
    background: rgba(190,147,96,0.15);
    color: #E4C58E;
    border: 1px solid rgba(190,147,96,0.2);
}

.admin-status-badge-finished {
    background: rgba(90,138,90,0.15);
    color: #A8CAA8;
    border: 1px solid rgba(90,138,90,0.2);
}

.admin-status-link {
    padding: 10px 14px;
    border-radius: 999px;
    background: rgba(214,168,91,0.08);
    border: 1px solid rgba(214,168,91,0.14);
    color: #D6A85B;
    text-decoration: none;
    font-size: 0.8rem;
    font-weight: 700;
    white-space: nowrap;
}

.admin-status-empty {
    padding: 28px 20px;
    border-radius: 18px;
    text-align: center;
    color: #8A7A66;
    border: 1px dashed rgba(214,168,91,0.16);
    background: rgba(255,255,255,0.02);
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
::-webkit-scrollbar-thumb:hover {
    background: #C49A4A;
}

@media (max-width: 1000px) {
    .admin-metrics-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .admin-ticket-section-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
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
}

@media (max-width: 560px) {
    .admin-metrics-grid {
        grid-template-columns: 1fr;
    }

    .admin-ticket-panel {
        padding: 20px;
    }
}
</style>
</x-app-layout>
