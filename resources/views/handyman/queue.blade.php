<x-app-layout>
<div class="staff-workspace">
    <section class="admin-overview-hero staff-overview-hero">
        <div>
            <p class="admin-overview-hero__kicker">HallSync Staff</p>
            <h1 class="admin-overview-hero__title">Work <span>Queue</span></h1>
            <span class="admin-overview-hero__subtitle">Review assigned maintenance work and keep status updates aligned with operations.</span>
        </div>
    </section>

    <section class="staff-panel">
        <div class="staff-panel-head staff-panel-head-wrap">
            <div>
                <h2>Assigned Jobs</h2>
                <p>Everything already routed to you for action.</p>
            </div>
            <div class="staff-toolbar">
                <select id="filterStatus" class="staff-filter">
                    <option value="all">All Status</option>
                    <option value="assigned">Assigned</option>
                    <option value="in_progress">In Progress</option>
                </select>
            </div>
        </div>

        <div class="staff-ticket-list" data-progressive-list data-progressive-limit="4">
            @forelse($myTickets ?? [] as $ticket)
                <article class="staff-ticket-card" data-progressive-item data-status="{{ $ticket->status }}" data-priority="{{ $ticket->normalized_priority }}">
                    <div class="staff-ticket-body">
                        <div class="staff-ticket-main">
                            <div class="staff-ticket-top">
                                <div class="staff-ticket-id">#{{ $ticket->ticket_id ?? $ticket->id }}</div>
                                <span class="staff-ticket-status-chip status-{{ $ticket->status }}">{{ str_replace('_', ' ', ucfirst($ticket->status)) }}</span>
                            </div>
                            <h3>{{ $ticket->title }}</h3>
                            <p>{{ Str::limit($ticket->description, 120) }}</p>
                            <div class="staff-ticket-meta">
                                <span>{{ $ticket->user->name ?? 'Resident' }}</span>
                                <span>{{ $ticket->location ?? 'Location pending' }}</span>
                                <span>{{ $ticket->created_at->format('M d, Y h:i A') }}</span>
                                @if($ticket->task_started_at)
                                    <span>Started {{ $ticket->task_started_at->format('M d, h:i A') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="staff-ticket-actions" onclick="event.stopPropagation()">
                            @if($ticket->status === 'assigned')
                                <form method="POST"
                                      action="{{ route('tickets.update-status', $ticket) }}"
                                      data-prevent-double-submit
                                      data-submitting-text="Starting Work...">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="in_progress">
                                    <button type="submit" class="ticket-card-action ticket-card-action-approve">Start Work</button>
                                </form>
                            @endif
                            @if($ticket->status === 'in_progress')
                                <form method="POST"
                                      action="{{ route('tickets.update-status', $ticket) }}"
                                      data-prevent-double-submit
                                      data-submitting-text="Marking Resolved...">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="resolved">
<button type="submit" class="ticket-card-action ticket-card-action-approve">Finish Work</button>
                                </form>
                            @endif
<a href="{{ route('tickets.show', $ticket) }}" class="ticket-card-action ticket-card-action-view">View Details</a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="staff-empty-copy">No active jobs in your queue right now.</div>
            @endforelse
        </div>
    </section>
</div>

<script>
const statusFilter = document.getElementById('filterStatus');
const queueParams = new URLSearchParams(window.location.search);
const priorityFilter = queueParams.get('priority');

if (statusFilter && queueParams.get('status')) {
    statusFilter.value = queueParams.get('status');
}

const applyQueueFilters = () => {
    const filter = statusFilter?.value ?? 'all';
    document.querySelectorAll('.staff-ticket-card').forEach((ticket) => {
        const matchesStatus = filter === 'all' || ticket.dataset.status === filter;
        const matchesPriority = !priorityFilter || ticket.dataset.priority === priorityFilter;
        ticket.style.display = matchesStatus && matchesPriority ? '' : 'none';
    });
};

statusFilter?.addEventListener('change', applyQueueFilters);
applyQueueFilters();
</script>

<style>
.staff-workspace {
    max-width: 1580px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 18px;
}
.staff-panel {
    padding: 18px 20px;
    border: 1px solid rgba(179, 137, 76, 0.24);
    border-radius: 18px;
    background: linear-gradient(180deg, #fffdf9 0%, #f7f0e6 100%);
    box-shadow: 0 10px 24px rgba(87, 65, 38, 0.08);
}
.staff-panel-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    margin-bottom: 18px;
}
.staff-panel-head-wrap {
    flex-wrap: wrap;
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
.staff-filter {
    padding: 10px 14px;
    border-radius: 14px;
    background: #fffaf2;
    border: 1px solid rgba(180, 119, 33, 0.22);
    color: #342a23;
}
.staff-ticket-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.staff-ticket-card {
    position: relative;
    overflow: hidden;
    display: flex;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.74);
    border: 1px solid rgba(180, 119, 33, 0.16);
    box-shadow: none;
}
.staff-ticket-body {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 14px 16px;
}
.staff-ticket-main {
    flex: 1;
    min-width: 0;
}
.staff-ticket-top {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 6px;
}
.staff-ticket-id {
    color: #b47721;
    font-size: 0.74rem;
    font-weight: 700;
    letter-spacing: 0.12em;
}
.staff-ticket-status-chip {
    display: inline-flex;
    padding: 6px 10px;
    border-radius: 999px;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}
.status-assigned {
    background: rgba(180, 119, 33, 0.12);
    color: #8a4f0e;
}
.status-in_progress {
    background: rgba(82, 120, 140, 0.14);
    color: #345984;
}
.staff-ticket-main h3 {
    margin: 0;
    color: #342a23;
    font-size: 1.02rem;
    font-weight: 700;
}
.staff-ticket-main p {
    margin: 6px 0 0;
    color: #63574e;
    font-size: 0.84rem;
    line-height: 1.45;
}
.staff-ticket-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 8px 14px;
    margin-top: 10px;
    color: #786b60;
    font-size: 0.76rem;
}
.staff-ticket-actions {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    align-items: center;
    align-self: center;
    gap: 8px;
    width: auto;
    max-width: 360px;
    margin-left: auto;
    flex-shrink: 0;
    justify-content: flex-end;
}
.staff-ticket-actions form {
    width: auto;
    margin: 0;
    display: flex;
}
.staff-action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: auto;
    min-width: 104px;
    min-height: 38px;
    padding: 0 12px;
    border-radius: 10px;
    text-decoration: none;
    font-size: 0.78rem;
    font-weight: 700;
    border: 1px solid transparent;
    cursor: pointer;
    white-space: nowrap;
}
.staff-action-start {
    background: rgba(82, 120, 140, 0.12);
    color: #345984;
    border-color: rgba(82, 120, 140, 0.22);
}
.staff-action-complete {
    background: linear-gradient(90deg, #b8842f 0%, #d6a85b 100%);
    color: #ffffff;
}
.staff-action-view {
    background: rgba(180, 119, 33, 0.10);
    color: #8a4f0e;
    border-color: rgba(180, 119, 33, 0.18);
}
.staff-empty-copy {
    padding: 12px 0;
    color: #786b60;
}
@media (max-width:980px) {
    .staff-ticket-body {
        align-items: stretch;
        flex-direction: column;
    }
    .staff-ticket-actions {
        width: 100%;
        max-width: none;
        margin-left: 0;
        justify-content: flex-start;
    }
    .staff-action-btn, .staff-ticket-actions form {
        flex: 1 1 180px;
    }
}
@media (max-width:640px) {
    .staff-panel {
        padding: 16px;
        border-radius: 16px;
    }
    .staff-ticket-card {
        border-radius: 12px;
    }
    .staff-ticket-body {
        padding: 14px;
    }
    .staff-ticket-actions {
        flex-direction: column;
    }
    .staff-action-btn, .staff-ticket-actions form {
        width: 100%;
        flex: 1 1 auto;
    }
}
</style>
</x-app-layout>
