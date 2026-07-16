<x-app-layout>
@php
    $queuePreview = collect($myTickets ?? [])->take(4);
    $recentCompleted = collect($completedTickets ?? [])->take(4);
@endphp

<div class="staff-workspace">
    <section class="admin-overview-hero staff-overview-hero">
        <div>
            <p class="admin-overview-hero__kicker">HallSync Staff</p>
            <h1 class="admin-overview-hero__title">Work <span>Overview</span></h1>
            <span class="admin-overview-hero__subtitle">Track assigned jobs, active repairs, and critical requests from the same operations design system.</span>
        </div>
    </section>

    <section class="staff-metrics admin-compact-stats admin-compact-stats-4">
        <a href="{{ route('staff.queue', ['status' => 'assigned']) }}" class="staff-metric-link">
            <x-admin-compact-stat icon="inbox" :value="$assignedTickets ?? 0" label="Assigned" note="Ready to start" tone="blue" />
        </a>
        <a href="{{ route('staff.queue', ['status' => 'in_progress']) }}" class="staff-metric-link">
            <x-admin-compact-stat icon="clock" :value="$inProgressTickets ?? 0" label="In Progress" note="Active repairs" tone="green" />
        </a>
        <a href="{{ route('staff.completed') }}" class="staff-metric-link">
            <x-admin-compact-stat icon="check" :value="$completedToday ?? 0" label="Completed Today" note="Closed tasks" tone="green" />
        </a>
        <a href="{{ route('staff.queue', ['priority' => 'critical']) }}" class="staff-metric-link">
            <x-admin-compact-stat icon="alert" :value="$urgentTickets ?? 0" label="Critical" note="Needs first attention" tone="red" />
        </a>
    </section>

    @if(($urgentTicketsList ?? collect())->count() > 0)
        <section class="staff-panel staff-urgent-panel">
            <div class="staff-panel-head">
                <div>
            <h2>Critical Dispatch</h2>
            <p>Start with these tickets before normal queue work.</p>
                </div>
            </div>

            <div class="staff-urgent-list" data-critical-dispatch-list>
                @foreach($urgentTicketsList as $ticket)
                    <article class="staff-urgent-item {{ $loop->index > 0 ? 'is-hidden-critical' : '' }}" data-critical-dispatch-item>
                        <div>
                            <h3>{{ $ticket->title }}</h3>
                            <p>#{{ $ticket->ticket_id ?? $ticket->id }} | {{ $ticket->location ?? 'Location pending' }}</p>
                        </div>
                        <a href="{{ route('tickets.show', $ticket) }}">Open</a>
                    </article>
                @endforeach
                @if($urgentTicketsList->count() > 1)
                    <button type="button" class="staff-show-more-btn" data-critical-dispatch-toggle>
                        Show more critical requests
                    </button>
                @endif
            </div>
        </section>
    @endif

    <div class="staff-overview-stack">
        <section class="staff-panel">
            <div class="staff-panel-head">
                <div>
                    <h2>Work Queue Preview</h2>
                    <p>Your next assigned or active jobs.</p>
                </div>
                <a href="{{ route('staff.queue') }}" class="staff-panel-link">Open Queue</a>
            </div>

            <div class="staff-preview-list">
                @forelse($queuePreview as $ticket)
                    <article class="staff-preview-card staff-work-row">
                        <div>
                            <h3>{{ $ticket->title }}</h3>
                            <p>{{ $ticket->location ?? 'Location pending' }} | {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</p>
                        </div>
                        <a href="{{ route('tickets.show', $ticket) }}">View</a>
                    </article>
                @empty
                    <div class="staff-empty-copy">No active jobs in your queue right now.</div>
                @endforelse
            </div>
        </section>

        <section class="staff-panel">
            <div class="staff-panel-head">
                <div>
                    <h2>Recently Completed</h2>
                    <p>Your latest finished work orders.</p>
                </div>
                <a href="{{ route('staff.completed') }}" class="staff-panel-link">View All</a>
            </div>

            <div class="staff-preview-list">
                @forelse($recentCompleted as $ticket)
                    <article class="staff-preview-card staff-work-row">
                        <div>
                            <h3>{{ $ticket->title }}</h3>
                            <p>Completed {{ $ticket->updated_at->diffForHumans() }}</p>
                        </div>
                        <a href="{{ route('tickets.show', $ticket) }}">Open</a>
                    </article>
                @empty
                    <div class="staff-empty-copy">No resolved tickets yet.</div>
                @endforelse
            </div>
        </section>
    </div>
</div>

<script>
document.querySelector('[data-critical-dispatch-toggle]')?.addEventListener('click', (event) => {
    const button = event.currentTarget;
    const list = button.closest('[data-critical-dispatch-list]');
    const isExpanded = button.dataset.expanded === 'true';

    list?.querySelectorAll('[data-critical-dispatch-item]').forEach((item, index) => {
        if (index > 0) {
            item.classList.toggle('is-hidden-critical', isExpanded);
        }
    });

    button.dataset.expanded = isExpanded ? 'false' : 'true';
    button.textContent = isExpanded ? 'Show more critical requests' : 'Show fewer critical requests';
});
</script>

<style>
.staff-workspace {
    max-width: 1580px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 18px;
}
.staff-panel, .staff-metric-card {
    border: 1px solid rgba(179, 137, 76, 0.24);
    box-shadow: 0 10px 24px rgba(87, 65, 38, 0.08);
}
.staff-metrics {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 12px;
}
.staff-metric-card {
    min-height: 96px;
    padding: 14px 16px;
    border-radius: 16px;
    background: linear-gradient(180deg, #fffaf2 0%, #f6efe5 100%);
    color: #342a23;
    text-decoration: none;
    transition: transform .18s ease, border-color .18s ease, box-shadow .18s ease;
}
.staff-metric-card:hover {
    transform: translateY(-2px);
    border-color: rgba(180, 119, 33, 0.34);
    box-shadow: 0 14px 28px rgba(87, 65, 38, 0.12);
}
.staff-metric-card span {
    display: block;
    color: #786b60;
    font-size: 0.82rem;
    text-transform: uppercase;
    letter-spacing: 0.12em;
}
.staff-metric-card strong {
    display: block;
    margin-top: 6px;
    color: #2f271f;
    font-size: 1.55rem;
    line-height: 1;
}
.staff-metric-card p {
    margin: 6px 0 0;
    color: #7d7064;
    font-size: 0.78rem;
}
.staff-metric-card-alert strong {
    color: #c24135;
}
.staff-panel {
    padding: 18px 20px;
    border-radius: 18px;
    background: linear-gradient(180deg, #fffdf9 0%, #f7f0e6 100%);
}
.staff-urgent-panel {
    position: relative;
    overflow: hidden;
    border-color: rgba(220, 38, 38, 0.28);
    background: linear-gradient(rgba(255, 255, 255, 0.16) 1px, transparent 1px), linear-gradient(90deg, rgba(255, 255, 255, 0.16) 1px, transparent 1px), linear-gradient(135deg, #b93a32 0%, #9e2f2a 52%, #7f1d1d 100%);
    background-size: 54px 54px, 54px 54px, auto;
    box-shadow: 0 18px 34px rgba(127, 29, 29, 0.18);
}
.staff-urgent-panel .staff-panel-head h2 {
    color: #fff7ed;
}
.staff-urgent-panel .staff-panel-head p {
    color: rgba(255, 247, 237, 0.78);
}
.staff-panel-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    margin-bottom: 18px;
}
.staff-panel-head h2 {
    margin: 0;
    color: #342a23;
    font-family: 'Playfair Display', serif;
    font-size: 1.4rem;
}
.staff-panel-head p {
    margin: 6px 0 0;
    color: #786b60;
}
.staff-panel-link {
    color: #b47721;
    text-decoration: none;
    font-weight: 700;
}
.staff-overview-stack {
    display: grid;
    gap: 14px;
}
.staff-preview-list, .staff-urgent-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.staff-preview-card, .staff-urgent-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding: 12px 14px;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.72);
    border: 1px solid rgba(180, 119, 33, 0.16);
}
.staff-preview-card > div, .staff-urgent-item > div {
    flex: 1;
    min-width: 0;
}
.staff-urgent-item {
    border-color: rgba(254, 226, 226, 0.18);
    background: rgba(69, 10, 10, 0.34);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.04);
}
.staff-urgent-item.is-hidden-critical {
    display: none;
}
.staff-work-row {
    min-height: 58px;
}
.staff-preview-card h3, .staff-urgent-item h3 {
    margin: 0;
    color: #342a23;
    font-size: 1rem;
}
.staff-preview-card p, .staff-urgent-item p {
    margin: 6px 0 0;
    color: #786b60;
    font-size: 0.84rem;
}
.staff-preview-card a, .staff-urgent-item a {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    align-self: center;
    min-width: 88px;
    margin-left: auto;
    padding: 10px 14px;
    border-radius: 12px;
    text-decoration: none;
    background: rgba(180, 119, 33, 0.12);
    color: #8a4f0e;
    font-weight: 700;
}
.staff-urgent-item p {
    color: rgba(255, 247, 237, 0.72);
}
.staff-urgent-item h3 {
    color: #fff7ed;
}
.staff-urgent-item a {
    background: #fff7ed;
    color: #7f1d1d;
    border: 1px solid rgba(255, 255, 255, 0.55);
}
.staff-urgent-item a:hover {
    background: #ffffff;
}
.staff-show-more-btn {
    align-self: flex-end;
    padding: 9px 14px;
    border: 1px solid rgba(255, 247, 237, 0.42);
    border-radius: 999px;
    background: rgba(255, 247, 237, 0.14);
    color: #fff7ed;
    cursor: pointer;
    font-weight: 800;
}
.staff-show-more-btn:hover {
    background: rgba(255, 247, 237, 0.22);
}
.staff-empty-copy {
    padding: 12px 0;
    color: #786b60;
}
@media (max-width:980px) {
    .staff-metrics {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}
@media (max-width:640px) {
    .staff-metrics {
        grid-template-columns: 1fr;
    }
    .staff-panel, .staff-metric-card {
        border-radius: 16px;
    }
    .staff-panel {
        padding: 16px;
    }
    .staff-panel-head, .staff-preview-card, .staff-urgent-item {
        flex-direction: column;
        align-items: flex-start;
    }
    .staff-preview-card a, .staff-urgent-item a {
        width: 100%;
    }
}
</style>
</x-app-layout>
