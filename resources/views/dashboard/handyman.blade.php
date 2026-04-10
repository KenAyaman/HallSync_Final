<x-app-layout>
@php
    $assignedCount = $assignedTickets ?? 0;
    $inProgressCount = $inProgressTickets ?? 0;
    $completedCount = $completedToday ?? 0;
    $urgentCount = $urgentTickets ?? 0;
@endphp

<div class="handyman-dashboard">
    <section class="handyman-hero">
        <div class="handyman-hero-copy">
            <p class="handyman-kicker">Maintenance Operations</p>
            <h1 class="handyman-title">
                Good {{ now()->hour < 12 ? 'Morning' : (now()->hour < 18 ? 'Afternoon' : 'Evening') }}
                <span>Staff</span>
            </h1>
            <p class="handyman-subtitle">
                Manage your assigned work orders from one focused queue, move active repairs forward, and keep urgent tickets at the top of your day.
            </p>
        </div>

        <div class="handyman-hero-aside">
            <div class="handyman-chip">Service Mode</div>
            <div class="handyman-hero-stats">
                <div class="handyman-hero-stat">
                    <span>Open queue</span>
                    <strong>{{ ($myTickets ?? collect())->count() }}</strong>
                </div>
                <div class="handyman-hero-stat">
                    <span>Urgent now</span>
                    <strong>{{ $urgentCount }}</strong>
                </div>
            </div>
        </div>
    </section>

    <section class="handyman-metrics">
        <article class="handyman-metric">
            <span class="handyman-metric-label">Assigned to You</span>
            <strong class="handyman-metric-value">{{ $assignedCount }}</strong>
            <p class="handyman-metric-note">Ready for action</p>
        </article>

        <article class="handyman-metric">
            <span class="handyman-metric-label">In Progress</span>
            <strong class="handyman-metric-value">{{ $inProgressCount }}</strong>
            <p class="handyman-metric-note">Currently being worked on</p>
        </article>

        <article class="handyman-metric">
            <span class="handyman-metric-label">Completed This Week</span>
            <strong class="handyman-metric-value">{{ $completedCount }}</strong>
            <p class="handyman-metric-note">Resolved tickets</p>
        </article>

        <article class="handyman-metric handyman-metric-alert">
            <span class="handyman-metric-label">Urgent Tasks</span>
            <strong class="handyman-metric-value">{{ $urgentCount }}</strong>
            <p class="handyman-metric-note">Need immediate attention</p>
        </article>
    </section>

    @if(($urgentTicketsList ?? collect())->count() > 0)
        <section class="handyman-alert-panel">
            <div class="handyman-panel-head">
                <div>
                    <h2>Urgent Tasks</h2>
                    <p>Prioritize these requests before standard work orders</p>
                </div>
            </div>

            <div class="handyman-urgent-list">
                @foreach($urgentTicketsList as $ticket)
                    <div class="handyman-urgent-item">
                        <div>
                            <h3>{{ $ticket->title }}</h3>
                            <p>#{{ $ticket->ticket_id ?? $ticket->id }} | {{ $ticket->user->name ?? 'Resident' }}</p>
                        </div>
                        <a href="{{ route('tickets.show', $ticket) }}">Open Ticket</a>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <section class="handyman-panel handyman-queue-panel">
        <div class="handyman-panel-head handyman-panel-head-wrap">
            <div>
                <h2>Work Queue</h2>
                <p>Your assigned tickets, ready for action, updates, and completion</p>
            </div>
            <div class="handyman-toolbar">
                <div class="handyman-filter-wrap">
                    <select id="filterStatus" class="handyman-filter">
                        <option value="all">All Status</option>
                        <option value="assigned">Assigned</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
                <a href="{{ route('profile.edit') }}" class="handyman-utility-link">Profile Settings</a>
            </div>
        </div>

        <div class="handyman-ticket-list">
            @forelse($myTickets ?? [] as $ticket)
                <article class="handyman-ticket-card" data-status="{{ $ticket->status }}">
                    <div class="handyman-ticket-accent priority-{{ $ticket->priority }}"></div>
                    <div class="handyman-ticket-body">
                        <div class="handyman-ticket-main">
                            <div class="handyman-ticket-top">
                                <div class="handyman-ticket-id">#{{ $ticket->ticket_id ?? $ticket->id }}</div>
                                <span class="handyman-ticket-status-chip status-{{ $ticket->status }}">{{ str_replace('_', ' ', ucfirst($ticket->status)) }}</span>
                                @if($ticket->priority === 'urgent')
                                    <span class="ticket-badge ticket-badge-urgent">Urgent</span>
                                @elseif($ticket->priority === 'high')
                                    <span class="ticket-badge ticket-badge-high">High</span>
                                @endif
                            </div>

                            <h3>{{ $ticket->title }}</h3>
                            <p>{{ Str::limit($ticket->description, 110) }}</p>

                            <div class="handyman-ticket-meta">
                                <span>{{ $ticket->user->name ?? 'Resident' }}</span>
                                @if($ticket->category)
                                    <span>{{ ucfirst($ticket->category) }}</span>
                                @endif
                                <span>{{ $ticket->location ?? 'Location pending' }}</span>
                                <span>{{ $ticket->created_at->format('M d, Y h:i A') }}</span>
                            </div>
                        </div>

                        <div class="handyman-ticket-actions" onclick="event.stopPropagation()">
                            @if($ticket->status === 'assigned')
                                <form method="POST" action="{{ route('tickets.update-status', $ticket) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="in_progress">
                                    <button type="submit" class="ticket-action-btn ticket-action-start">Start Work</button>
                                </form>
                            @endif

                            @if($ticket->status === 'in_progress')
                                <form method="POST" action="{{ route('tickets.update-status', $ticket) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="completed">
                                    <button type="submit" class="ticket-action-btn ticket-action-complete">Mark Complete</button>
                                </form>
                            @endif

                            <a href="{{ route('tickets.show', $ticket) }}" class="ticket-action-btn ticket-action-view">Open Ticket</a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="handyman-empty">Your queue is clear right now. New assigned tickets will appear here automatically.</div>
            @endforelse
        </div>
    </section>

    @if(($completedTickets ?? collect())->count() > 0)
        <section class="handyman-panel">
            <div class="handyman-panel-head">
                <div>
                    <h2>Recently Completed</h2>
                    <p>Tickets completed within the last 7 days</p>
                </div>
            </div>

            <div class="handyman-completed-list">
                @foreach($completedTickets->take(5) as $ticket)
                    <div class="handyman-completed-item">
                        <div>
                            <h3>{{ $ticket->title }}</h3>
                            <p>Completed {{ $ticket->updated_at->diffForHumans() }}</p>
                        </div>
                        <span>Done</span>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
</div>

<script>
document.getElementById('filterStatus')?.addEventListener('change', function() {
    const filter = this.value;
    document.querySelectorAll('.handyman-ticket-card').forEach(ticket => {
        ticket.style.display = filter === 'all' || ticket.dataset.status === filter ? '' : 'none';
    });
});
</script>

<style>
.handyman-dashboard {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.handyman-hero,
.handyman-metric,
.handyman-panel,
.handyman-alert-panel {
    border: 1px solid rgba(214,168,91,0.15);
    box-shadow: 0 25px 50px -12px rgba(0,0,0,0.4);
}

.handyman-hero {
    position: relative;
    overflow: hidden;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 28px;
    padding: 34px 36px;
    border-radius: 36px;
    background: linear-gradient(115deg, #1f2023 0%, #24262b 38%, #2c2c2f 62%, #3b3023 100%);
}

.handyman-hero::before,
.handyman-hero::after {
    content: "";
    position: absolute;
    border-radius: 999px;
    filter: blur(60px);
    pointer-events: none;
}

.handyman-hero::before {
    top: -90px;
    right: 6%;
    width: 290px;
    height: 290px;
    background: rgba(199, 151, 69, 0.24);
}

.handyman-hero::after {
    bottom: -110px;
    left: 12%;
    width: 220px;
    height: 220px;
    background: rgba(255,255,255,0.08);
}

.handyman-hero-copy,
.handyman-hero-aside {
    position: relative;
    z-index: 1;
}

.handyman-kicker {
    margin: 0 0 10px;
    color: #d6a85b;
    font-size: 0.78rem;
    font-weight: 700;
    letter-spacing: 0.24em;
    text-transform: uppercase;
}

.handyman-title {
    margin: 0;
    color: #f8f3ea;
    font-family: 'Playfair Display', serif;
    font-size: clamp(2.3rem, 4vw, 3.7rem);
    line-height: 1.05;
}

.handyman-title span {
    color: #d6a85b;
}

.handyman-subtitle {
    margin: 12px 0 0;
    max-width: 760px;
    color: rgba(255,255,255,0.72);
    line-height: 1.7;
}

.handyman-hero-aside {
    display: grid;
    justify-items: end;
    gap: 14px;
    flex-shrink: 0;
}

.handyman-chip {
    padding: 10px 16px;
    border-radius: 999px;
    background: rgba(214,168,91,0.1);
    border: 1px solid rgba(214,168,91,0.2);
    color: #d6a85b;
    font-size: 0.82rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.12em;
}

.handyman-hero-stats {
    display: grid;
    gap: 12px;
    min-width: 220px;
}

.handyman-hero-stat {
    padding: 16px 18px;
    border-radius: 18px;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.08);
    backdrop-filter: blur(12px);
}

.handyman-hero-stat span {
    display: block;
    color: #bcae99;
    font-size: 0.76rem;
    text-transform: uppercase;
    letter-spacing: 0.12em;
    font-weight: 700;
}

.handyman-hero-stat strong {
    display: block;
    margin-top: 8px;
    color: #f8f3ea;
    font-size: 1.55rem;
}

.handyman-metrics {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 18px;
}

.handyman-metric {
    padding: 22px;
    border-radius: 22px;
    background: linear-gradient(135deg, #1a1c1e 0%, #24262b 100%);
}

.handyman-metric-label {
    display: block;
    color: #d0c8b8;
    font-size: 0.95rem;
}

.handyman-metric-value {
    display: block;
    margin-top: 10px;
    color: #d6a85b;
    font-size: 2.35rem;
    line-height: 1;
}

.handyman-metric-note {
    margin: 10px 0 0;
    color: #8a7a66;
    font-size: 0.84rem;
}

.handyman-metric-alert .handyman-metric-value {
    color: #e07060;
}

.handyman-panel,
.handyman-alert-panel {
    padding: 28px 30px;
    border-radius: 32px;
    background: linear-gradient(180deg, #2a2c30 0%, #1f2023 100%);
}

.handyman-alert-panel {
    border-color: rgba(224,112,96,0.25);
    background: linear-gradient(135deg, rgba(224,112,96,0.12), rgba(224,112,96,0.04));
}

.handyman-panel-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    margin-bottom: 18px;
}

.handyman-panel-head-wrap {
    flex-wrap: wrap;
}

.handyman-toolbar {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.handyman-panel-head h2 {
    margin: 0;
    color: #f8f3ea;
    font-family: 'Playfair Display', serif;
    font-size: 1.45rem;
}

.handyman-panel-head p {
    margin: 6px 0 0;
    color: #9ca3af;
    font-size: 0.88rem;
}

.handyman-urgent-list,
.handyman-ticket-list,
.handyman-completed-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.handyman-urgent-item,
.handyman-completed-item {
    display: flex;
    justify-content: space-between;
    gap: 12px;
    align-items: center;
    padding: 14px 16px;
    border-radius: 18px;
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.08);
}

.handyman-urgent-item h3,
.handyman-completed-item h3 {
    margin: 0;
    color: #fff;
    font-size: 0.98rem;
}

.handyman-urgent-item p,
.handyman-completed-item p {
    margin: 6px 0 0;
    color: #a3a3a3;
    font-size: 0.8rem;
}

.handyman-urgent-item a,
.handyman-completed-item span {
    padding: 9px 14px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 700;
    text-decoration: none;
}

.handyman-urgent-item a {
    background: rgba(224,112,96,0.2);
    color: #e07060;
}

.handyman-completed-item span {
    background: rgba(90,138,90,0.15);
    color: #5a8a5a;
}

.handyman-filter {
    padding: 10px 14px;
    border-radius: 14px;
    background: rgba(37,39,42,0.9);
    border: 1px solid #3a342d;
    color: #d1d5db;
}

.handyman-utility-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 42px;
    padding: 0 16px;
    border-radius: 14px;
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(214,168,91,0.14);
    color: #d9cfbf;
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 700;
    transition: 0.2s ease;
}

.handyman-utility-link:hover {
    background: rgba(255,255,255,0.08);
    color: #fff6e7;
}

.handyman-ticket-card {
    display: flex;
    overflow: hidden;
    border-radius: 22px;
    background: linear-gradient(135deg, #2c2c2f 0%, #25272a 100%);
    border: 1px solid rgba(58,52,45,0.6);
    transition: 0.2s ease;
}

.handyman-ticket-card:hover {
    transform: translateY(-2px);
    border-color: rgba(214,168,91,0.32);
}

.handyman-ticket-accent {
    width: 4px;
}

.priority-urgent { background: linear-gradient(180deg, #e07060, #c0392b); }
.priority-high { background: linear-gradient(180deg, #f0a550, #d97904); }
.priority-medium,
.priority-low,
.priority- { background: linear-gradient(180deg, #d6a85b, #a8792e); }

.handyman-ticket-body {
    flex: 1;
    display: flex;
    justify-content: space-between;
    gap: 18px;
    padding: 20px;
}

.handyman-ticket-main {
    flex: 1;
    min-width: 0;
}

.handyman-ticket-top {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
    flex-wrap: wrap;
}

.handyman-ticket-id {
    color: #d6a85b;
    font-size: 0.78rem;
    font-family: monospace;
    font-weight: 700;
}

.handyman-ticket-status-chip {
    padding: 4px 8px;
    border-radius: 999px;
    font-size: 0.64rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    background: rgba(255,255,255,0.06);
    color: #d1c7b8;
}

.ticket-badge {
    padding: 4px 8px;
    border-radius: 999px;
    font-size: 0.65rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.08em;
}

.ticket-badge-urgent {
    background: rgba(224,112,96,0.2);
    color: #e07060;
}

.ticket-badge-high {
    background: rgba(240,165,80,0.2);
    color: #f0a550;
}

.handyman-ticket-main h3 {
    margin: 0;
    color: #fff;
    font-size: 1.05rem;
    font-weight: 700;
}

.handyman-ticket-main p {
    margin: 10px 0 0;
    color: #9ca3af;
    font-size: 0.9rem;
    line-height: 1.6;
}

.handyman-ticket-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 10px 16px;
    margin-top: 14px;
    color: #6b7280;
    font-size: 0.76rem;
}

.handyman-ticket-actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
    flex-shrink: 0;
}

.ticket-action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 132px;
    padding: 10px 14px;
    border-radius: 14px;
    text-decoration: none;
    font-size: 0.8rem;
    font-weight: 700;
    border: 1px solid transparent;
    cursor: pointer;
    transition: 0.2s ease;
}

.ticket-action-start {
    background: rgba(214,168,91,0.15);
    color: #d6a85b;
    border-color: rgba(214,168,91,0.3);
}

.ticket-action-complete {
    background: rgba(90,138,90,0.15);
    color: #5a8a5a;
    border-color: rgba(90,138,90,0.3);
}

.ticket-action-view {
    background: rgba(190,147,96,0.1);
    color: #be9360;
    border-color: rgba(190,147,96,0.2);
}

.handyman-empty {
    padding: 48px 16px;
    text-align: center;
    color: #9ca3af;
    border-radius: 20px;
    background: rgba(37,39,42,0.4);
    border: 1px dashed rgba(214,168,91,0.2);
}

@media (max-width: 1100px) {
    .handyman-metrics {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .handyman-hero {
        flex-direction: column;
        align-items: flex-start;
    }

    .handyman-hero-aside {
        width: 100%;
        justify-items: stretch;
    }

    .handyman-hero-stats {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        min-width: 0;
        width: 100%;
    }

    .handyman-ticket-body {
        flex-direction: column;
    }

    .handyman-ticket-actions {
        flex-direction: row;
        flex-wrap: wrap;
    }
}

@media (max-width: 768px) {
    .handyman-hero,
    .handyman-panel,
    .handyman-alert-panel {
        padding: 22px;
    }

    .handyman-metrics,
    .handyman-hero-stats {
        grid-template-columns: 1fr;
    }

    .handyman-urgent-item,
    .handyman-completed-item {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>
</x-app-layout>
