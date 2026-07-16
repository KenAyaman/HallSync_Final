<x-app-layout>
<div class="staff-workspace">
    <section class="admin-overview-hero staff-overview-hero">
        <div>
            <p class="admin-overview-hero__kicker">HallSync Staff</p>
            <h1 class="admin-overview-hero__title">Completed <span>Jobs</span></h1>
            <span class="admin-overview-hero__subtitle">Review recently resolved tickets and completed maintenance history.</span>
        </div>
    </section>

    <section class="staff-panel">
        <div class="staff-panel-head">
            <div>
                <h2>Completed Jobs</h2>
                <p>Recent tickets closed by your staff account.</p>
            </div>
        </div>

        <div class="staff-completed-list" data-progressive-list>
            @forelse($completedTickets ?? [] as $ticket)
                <article class="staff-completed-card" data-progressive-item>
                    <div>
                        <h3>{{ $ticket->title }}</h3>
                        <p>
                            {{ $ticket->location ?? 'Location pending' }} |
                            Completed {{ ($ticket->task_completed_at ?? $ticket->updated_at)->diffForHumans() }} |
                            Duration: {{ $ticket->task_duration_label }}
                        </p>
                    </div>
                    <a href="{{ route('tickets.show', $ticket) }}">Open Ticket</a>
                </article>
            @empty
                <div class="staff-empty-copy">No resolved tickets yet.</div>
            @endforelse
        </div>
    </section>
</div>

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
.staff-completed-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.staff-completed-card {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding: 12px 14px;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.74);
    border: 1px solid rgba(180, 119, 33, 0.16);
}
.staff-completed-card > div {
    flex: 1;
    min-width: 0;
}
.staff-completed-card h3 {
    margin: 0;
    color: #342a23;
    font-size: 1rem;
}
.staff-completed-card p {
    margin: 6px 0 0;
    color: #786b60;
    font-size: 0.84rem;
}
.staff-completed-card a {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    align-self: center;
    min-width: 98px;
    margin-left: auto;
    padding: 9px 12px;
    border-radius: 10px;
    text-decoration: none;
    background: rgba(180, 119, 33, 0.10);
    color: #8a4f0e;
    font-weight: 700;
    font-size: 0.78rem;
}
.staff-empty-copy {
    padding: 12px 0;
    color: #786b60;
}
@media (max-width:768px) {
    .staff-completed-card {
        flex-direction: column;
        align-items: flex-start;
    }
}
@media (max-width:640px) {
    .staff-panel {
        padding: 16px;
        border-radius: 16px;
    }
    .staff-completed-card a {
        width: 100%;
    }
}
</style>
</x-app-layout>
