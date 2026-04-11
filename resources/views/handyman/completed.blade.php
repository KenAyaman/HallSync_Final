<x-app-layout>
<div class="staff-workspace">
    <section class="staff-page-hero">
        <div class="staff-page-copy">
            <p class="staff-page-kicker">Staff Operations</p>
            <h1 class="staff-page-title">Recently Completed</h1>
            <p class="staff-page-subtitle">
                Review your finished jobs so you can keep track of what was already resolved and reported back.
            </p>
        </div>
    </section>

    <section class="staff-panel">
        <div class="staff-panel-head">
            <div>
                <h2>Completed Jobs</h2>
                <p>Recent tickets closed by your staff account.</p>
            </div>
        </div>

        <div class="staff-completed-list">
            @forelse($completedTickets ?? [] as $ticket)
                <article class="staff-completed-card">
                    <div>
                        <h3>{{ $ticket->title }}</h3>
                        <p>{{ $ticket->location ?? 'Location pending' }} | Completed {{ $ticket->updated_at->diffForHumans() }}</p>
                    </div>
                    <a href="{{ route('tickets.show', $ticket) }}">Open Ticket</a>
                </article>
            @empty
                <div class="staff-empty-copy">No completed tickets yet.</div>
            @endforelse
        </div>
    </section>
</div>

<style>
.staff-workspace { max-width: 1580px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px; }
.staff-page-hero, .staff-panel { border: 1px solid rgba(214,168,91,0.14); box-shadow: 0 18px 36px rgba(0,0,0,0.18); }
.staff-page-hero { padding: 30px 32px; border-radius: 32px; background: linear-gradient(135deg, rgba(21,25,29,0.96) 0%, rgba(29,35,41,0.96) 48%, rgba(20,33,40,0.98) 100%); }
.staff-page-kicker { margin: 0 0 10px; color: #d6a85b; font-size: 0.76rem; font-weight: 700; letter-spacing: 0.24em; text-transform: uppercase; }
.staff-page-title { margin: 0; color: #f8f3ea; font-family: 'Playfair Display', serif; font-size: clamp(2.2rem, 4vw, 3.4rem); }
.staff-page-subtitle { margin: 12px 0 0; max-width: 760px; color: #b5c1c9; line-height: 1.7; }
.staff-panel { padding: 26px 28px; border-radius: 28px; background: linear-gradient(180deg, rgba(25,31,36,0.96) 0%, rgba(17,22,27,0.98) 100%); }
.staff-panel-head { display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 18px; }
.staff-panel-head h2 { margin: 0; color: #f8f3ea; font-family: 'Playfair Display', serif; font-size: 1.4rem; }
.staff-panel-head p { margin: 6px 0 0; color: #93a2ad; }
.staff-completed-list { display: flex; flex-direction: column; gap: 12px; }
.staff-completed-card { display: flex; justify-content: space-between; align-items: center; gap: 12px; padding: 16px 18px; border-radius: 18px; background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.06); }
.staff-completed-card h3 { margin: 0; color: #f8f3ea; font-size: 1rem; }
.staff-completed-card p { margin: 6px 0 0; color: #96a7b2; font-size: 0.84rem; }
.staff-completed-card a { display: inline-flex; align-items: center; justify-content: center; min-width: 98px; padding: 10px 14px; border-radius: 12px; text-decoration: none; background: rgba(214,168,91,0.14); color: #f2e2c6; font-weight: 700; }
.staff-empty-copy { padding: 12px 0; color: #98a8b2; }
@media (max-width: 768px) { .staff-completed-card { flex-direction: column; align-items: flex-start; } }
@media (max-width: 640px) { .staff-page-hero, .staff-panel { padding: 20px; border-radius: 24px; } .staff-page-title { font-size: 2rem; } .staff-page-subtitle { font-size: 0.95rem; } .staff-completed-card a { width: 100%; } }
</style>
</x-app-layout>
