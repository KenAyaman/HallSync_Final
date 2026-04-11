<x-app-layout>
@php
    $queuePreview = collect($myTickets ?? [])->take(4);
    $recentCompleted = collect($completedTickets ?? [])->take(4);
@endphp

<div class="staff-workspace">
    <section class="staff-page-hero">
        <div class="staff-hero-grid"></div>

        <div class="staff-page-copy">
            <p class="staff-page-kicker">Staff Overview</p>
            <h1 class="staff-page-title">Overview</h1>
            <p class="staff-page-subtitle">
                Start here to see critical jobs, today's queue status, and the latest work completed around Rexhall.
            </p>
        </div>

        <div class="staff-hero-aside">
            <div class="staff-hero-chip">Operations Ready</div>
            <div class="staff-hero-mini-stats">
                <div class="staff-hero-mini-stat">
                    <span>Queue</span>
                    <strong>{{ collect($myTickets ?? [])->count() }}</strong>
                </div>
                <div class="staff-hero-mini-stat">
                    <span>Critical</span>
                    <strong>{{ $urgentTickets ?? 0 }}</strong>
                </div>
            </div>
        </div>
    </section>

    <section class="staff-metrics">
        <article class="staff-metric-card">
            <span>Assigned</span>
            <strong>{{ $assignedTickets ?? 0 }}</strong>
            <p>Ready to start</p>
        </article>
        <article class="staff-metric-card">
            <span>In Progress</span>
            <strong>{{ $inProgressTickets ?? 0 }}</strong>
            <p>Active repairs</p>
        </article>
        <article class="staff-metric-card">
            <span>Completed This Week</span>
            <strong>{{ $completedToday ?? 0 }}</strong>
            <p>Closed tasks</p>
        </article>
        <article class="staff-metric-card staff-metric-card-alert">
            <span>Critical</span>
            <strong>{{ $urgentTickets ?? 0 }}</strong>
            <p>Needs first attention</p>
        </article>
    </section>

    @if(($urgentTicketsList ?? collect())->count() > 0)
        <section class="staff-panel staff-urgent-panel">
            <div class="staff-panel-head">
                <div>
            <h2>Critical Dispatch</h2>
            <p>Start with these tickets before normal queue work.</p>
                </div>
            </div>

            <div class="staff-urgent-list">
                @foreach($urgentTicketsList as $ticket)
                    <article class="staff-urgent-item">
                        <div>
                            <h3>{{ $ticket->title }}</h3>
                            <p>#{{ $ticket->ticket_id ?? $ticket->id }} | {{ $ticket->location ?? 'Location pending' }}</p>
                        </div>
                        <a href="{{ route('tickets.show', $ticket) }}">Open</a>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    <div class="staff-overview-grid">
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
                    <article class="staff-preview-card">
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
                    <article class="staff-preview-card">
                        <div>
                            <h3>{{ $ticket->title }}</h3>
                            <p>Completed {{ $ticket->updated_at->diffForHumans() }}</p>
                        </div>
                        <a href="{{ route('tickets.show', $ticket) }}">Open</a>
                    </article>
                @empty
                    <div class="staff-empty-copy">No completed tickets yet.</div>
                @endforelse
            </div>
        </section>
    </div>
</div>

<style>
.staff-workspace { max-width: 1580px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px; }
.staff-page-hero, .staff-panel, .staff-metric-card { border: 1px solid rgba(214,168,91,0.14); box-shadow: 0 18px 36px rgba(0,0,0,0.18); }
.staff-page-hero { position: relative; overflow: hidden; display: flex; justify-content: space-between; align-items: center; gap: 28px; padding: 34px 36px; border-radius: 36px; background: radial-gradient(circle at top right, rgba(214,168,91,0.16), transparent 30%), linear-gradient(135deg, rgba(21,25,29,0.96) 0%, rgba(29,35,41,0.96) 48%, rgba(20,33,40,0.98) 100%); }
.staff-hero-grid { position: absolute; inset: 0; background-image: linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px); background-size: 32px 32px; mask-image: linear-gradient(180deg, rgba(0,0,0,0.55), transparent 100%); pointer-events: none; }
.staff-page-copy, .staff-hero-aside { position: relative; z-index: 1; }
.staff-page-kicker { margin: 0 0 10px; color: #d6a85b; font-size: 0.76rem; font-weight: 700; letter-spacing: 0.24em; text-transform: uppercase; }
.staff-page-title { margin: 0; color: #f8f3ea; font-family: 'Playfair Display', serif; font-size: clamp(2.2rem, 4vw, 3.4rem); }
.staff-page-subtitle { margin: 12px 0 0; max-width: 760px; color: #b5c1c9; line-height: 1.7; }
.staff-hero-aside { display: grid; justify-items: end; gap: 14px; flex-shrink: 0; }
.staff-hero-chip { padding: 10px 16px; border-radius: 999px; background: rgba(88,135,165,0.14); border: 1px solid rgba(88,135,165,0.22); color: #d0e3ef; font-size: 0.82rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.12em; }
.staff-hero-mini-stats { display: grid; gap: 12px; min-width: 220px; }
.staff-hero-mini-stat { padding: 16px 18px; border-radius: 18px; background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.08); backdrop-filter: blur(10px); }
.staff-hero-mini-stat span { display: block; color: #c6d4dd; font-size: 0.76rem; text-transform: uppercase; letter-spacing: 0.12em; font-weight: 700; }
.staff-hero-mini-stat strong { display: block; margin-top: 8px; color: #f8f3ea; font-size: 1.55rem; }
.staff-metrics { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 18px; }
.staff-metric-card { padding: 22px; border-radius: 22px; background: linear-gradient(180deg, rgba(25,31,36,0.96) 0%, rgba(17,22,27,0.98) 100%); }
.staff-metric-card span { display: block; color: #bcc7ce; font-size: 0.82rem; text-transform: uppercase; letter-spacing: 0.12em; }
.staff-metric-card strong { display: block; margin-top: 10px; color: #f8f3ea; font-size: 2.2rem; }
.staff-metric-card p { margin: 10px 0 0; color: #8ea0ad; }
.staff-metric-card-alert strong { color: #e07060; }
.staff-panel { padding: 26px 28px; border-radius: 28px; background: linear-gradient(180deg, rgba(25,31,36,0.96) 0%, rgba(17,22,27,0.98) 100%); }
.staff-panel-head { display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 18px; }
.staff-panel-head h2 { margin: 0; color: #f8f3ea; font-family: 'Playfair Display', serif; font-size: 1.4rem; }
.staff-panel-head p { margin: 6px 0 0; color: #93a2ad; }
.staff-panel-link { color: #d6a85b; text-decoration: none; font-weight: 700; }
.staff-overview-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.staff-preview-list, .staff-urgent-list { display: flex; flex-direction: column; gap: 12px; }
.staff-preview-card, .staff-urgent-item { display: flex; justify-content: space-between; align-items: center; gap: 12px; padding: 14px 16px; border-radius: 18px; background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.06); }
.staff-preview-card h3, .staff-urgent-item h3 { margin: 0; color: #f8f3ea; font-size: 1rem; }
.staff-preview-card p, .staff-urgent-item p { margin: 6px 0 0; color: #96a7b2; font-size: 0.84rem; }
.staff-preview-card a, .staff-urgent-item a { display: inline-flex; align-items: center; justify-content: center; min-width: 88px; padding: 10px 14px; border-radius: 12px; text-decoration: none; background: rgba(214,168,91,0.14); color: #f2e2c6; font-weight: 700; }
.staff-empty-copy { padding: 12px 0; color: #98a8b2; }
@media (max-width: 980px) { .staff-metrics, .staff-overview-grid { grid-template-columns: 1fr; } .staff-page-hero { flex-direction: column; align-items: flex-start; } .staff-hero-aside { width: 100%; justify-items: stretch; } .staff-hero-mini-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); min-width: 0; width: 100%; } }
@media (max-width: 640px) { .staff-page-hero, .staff-panel, .staff-metric-card { border-radius: 24px; } .staff-page-hero, .staff-panel { padding: 20px; } .staff-page-title { font-size: 2rem; } .staff-page-subtitle { font-size: 0.95rem; } .staff-hero-mini-stats { grid-template-columns: 1fr; } .staff-panel-head, .staff-preview-card, .staff-urgent-item { flex-direction: column; align-items: flex-start; } .staff-preview-card a, .staff-urgent-item a { width: 100%; } }
</style>
</x-app-layout>
