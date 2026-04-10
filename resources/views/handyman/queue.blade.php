<x-app-layout>
<div class="staff-workspace">
    <section class="staff-page-hero">
        <div class="staff-page-copy">
            <p class="staff-page-kicker">Staff Operations</p>
            <h1 class="staff-page-title">Work Queue</h1>
            <p class="staff-page-subtitle">
                Focus on assigned and active jobs only, with quick actions to start work or mark each ticket complete.
            </p>
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

        <div class="staff-ticket-list">
            @forelse($myTickets ?? [] as $ticket)
                <article class="staff-ticket-card" data-status="{{ $ticket->status }}">
                    <div class="staff-ticket-accent priority-{{ $ticket->normalized_priority }}"></div>
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
                            </div>
                        </div>
                        <div class="staff-ticket-actions" onclick="event.stopPropagation()">
                            @if($ticket->status === 'assigned')
                                <form method="POST" action="{{ route('tickets.update-status', $ticket) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="in_progress">
                                    <button type="submit" class="staff-action-btn staff-action-start">Start Work</button>
                                </form>
                            @endif
                            @if($ticket->status === 'in_progress')
                                <form method="POST" action="{{ route('tickets.update-status', $ticket) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="completed">
                                    <button type="submit" class="staff-action-btn staff-action-complete">Mark Complete</button>
                                </form>
                            @endif
                            <a href="{{ route('tickets.show', $ticket) }}" class="staff-action-btn staff-action-view">Open Ticket</a>
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
document.getElementById('filterStatus')?.addEventListener('change', function() {
    const filter = this.value;
    document.querySelectorAll('.staff-ticket-card').forEach((ticket) => {
        ticket.style.display = filter === 'all' || ticket.dataset.status === filter ? '' : 'none';
    });
});
</script>

<style>
.staff-workspace { max-width: 1580px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px; }
.staff-page-hero, .staff-panel { border: 1px solid rgba(214,168,91,0.14); box-shadow: 0 18px 36px rgba(0,0,0,0.18); }
.staff-page-hero { padding: 30px 32px; border-radius: 32px; background: linear-gradient(135deg, rgba(21,25,29,0.96) 0%, rgba(29,35,41,0.96) 48%, rgba(20,33,40,0.98) 100%); }
.staff-page-kicker { margin: 0 0 10px; color: #d6a85b; font-size: 0.76rem; font-weight: 700; letter-spacing: 0.24em; text-transform: uppercase; }
.staff-page-title { margin: 0; color: #f8f3ea; font-family: 'Playfair Display', serif; font-size: clamp(2.2rem, 4vw, 3.4rem); }
.staff-page-subtitle { margin: 12px 0 0; max-width: 760px; color: #b5c1c9; line-height: 1.7; }
.staff-panel { padding: 26px 28px; border-radius: 28px; background: linear-gradient(180deg, rgba(25,31,36,0.96) 0%, rgba(17,22,27,0.98) 100%); }
.staff-panel-head { display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 18px; }
.staff-panel-head-wrap { flex-wrap: wrap; }
.staff-panel-head h2 { margin: 0; color: #f8f3ea; font-family: 'Playfair Display', serif; font-size: 1.4rem; }
.staff-panel-head p { margin: 6px 0 0; color: #93a2ad; }
.staff-filter { padding: 10px 14px; border-radius: 14px; background: rgba(18,24,28,0.92); border: 1px solid rgba(88,135,165,0.22); color: #d1d5db; }
.staff-ticket-list { display: flex; flex-direction: column; gap: 12px; }
.staff-ticket-card { position: relative; overflow: hidden; display: flex; border-radius: 24px; background: linear-gradient(135deg, rgba(27,34,39,0.96) 0%, rgba(20,25,30,0.98) 100%); border: 1px solid rgba(88,135,165,0.12); box-shadow: 0 16px 36px rgba(0,0,0,0.18); }
.staff-ticket-accent { width: 4px; flex-shrink: 0; border-radius: 999px 0 0 999px; }
.priority-critical { background: linear-gradient(180deg, #e07060, #c0392b); }
.priority-medium, .priority-low, .priority- { background: linear-gradient(180deg, #d6a85b, #a8792e); }
.staff-ticket-body { flex: 1; display: flex; gap: 18px; padding: 22px 24px; }
.staff-ticket-main { flex: 1; min-width: 0; }
.staff-ticket-top { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 10px; }
.staff-ticket-id { color: #d6a85b; font-size: 0.74rem; font-weight: 700; letter-spacing: 0.12em; }
.staff-ticket-status-chip { display: inline-flex; padding: 6px 10px; border-radius: 999px; font-size: 0.72rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; }
.status-assigned { background: rgba(214,168,91,0.14); color: #e4c58e; }
.status-in_progress { background: rgba(88,135,165,0.16); color: #b8d7ea; }
.staff-ticket-main h3 { margin: 0; color: #f8f3ea; font-size: 1.18rem; font-weight: 700; }
.staff-ticket-main p { margin: 10px 0 0; color: #afbac2; font-size: 0.92rem; line-height: 1.65; }
.staff-ticket-meta { display: flex; flex-wrap: wrap; gap: 10px 16px; margin-top: 16px; color: #8ea0ad; font-size: 0.8rem; }
.staff-ticket-actions { display: flex; flex-direction: column; gap: 10px; width: 160px; flex-shrink: 0; }
.staff-ticket-actions form { width: 100%; margin: 0; display: flex; }
.staff-action-btn { display: inline-flex; align-items: center; justify-content: center; width: 100%; min-height: 44px; padding: 0 14px; border-radius: 14px; text-decoration: none; font-size: 0.82rem; font-weight: 700; border: 1px solid transparent; cursor: pointer; }
.staff-action-start { background: rgba(88,135,165,0.18); color: #d7ebf8; border-color: rgba(88,135,165,0.26); }
.staff-action-complete { background: linear-gradient(90deg, #b8842f 0%, #d6a85b 100%); color: #ffffff; }
.staff-action-view { background: rgba(255,255,255,0.04); color: #f0e9df; border-color: rgba(255,255,255,0.08); }
.staff-empty-copy { padding: 12px 0; color: #98a8b2; }
@media (max-width: 980px) { .staff-ticket-body { flex-direction: column; } .staff-ticket-actions { width: 100%; flex-direction: row; flex-wrap: wrap; } .staff-action-btn, .staff-ticket-actions form { flex: 1 1 180px; } }
@media (max-width: 640px) { .staff-ticket-actions { flex-direction: column; } .staff-action-btn, .staff-ticket-actions form { width: 100%; flex: 1 1 auto; } }
</style>
</x-app-layout>
