<x-app-layout>
<div class="admin-concern-page">
    <section class="admin-concern-hero">
        <div>
            <p class="admin-concern-kicker">Resident Support Desk</p>
            <h1 class="admin-concern-title">Concern Reports</h1>
            <p class="admin-concern-subtitle">Review private resident concerns, respond professionally, and keep a documented support trail inside HallSync.</p>
        </div>
    </section>

    @if (session('success'))
        <div class="admin-concern-alert">{{ session('success') }}</div>
    @endif

    <section class="admin-concern-stats">
        <article class="admin-concern-stat"><span>Submitted</span><strong>{{ $submittedCount }}</strong></article>
        <article class="admin-concern-stat"><span>Under Review</span><strong>{{ $reviewCount }}</strong></article>
        <article class="admin-concern-stat"><span>Responded</span><strong>{{ $respondedCount }}</strong></article>
        <article class="admin-concern-stat"><span>Closed</span><strong>{{ $closedCount }}</strong></article>
    </section>

    <section class="admin-concern-card">
        <div class="admin-concern-head">
            <div>
                <h2>Incoming Reports</h2>
                <p>Open a report to review details and send a private response.</p>
            </div>
        </div>

        <div class="admin-concern-list">
            @forelse($concerns as $concern)
                <article class="admin-concern-row">
                    <div class="admin-concern-main">
                        <div class="admin-concern-meta">
                            <span class="admin-concern-status admin-concern-status-{{ $concern->status }}">{{ $concern->status_label }}</span>
                            <span>{{ ucfirst(str_replace('_', ' ', $concern->category)) }}</span>
                            <span>{{ $concern->created_at->format('M d, Y h:i A') }}</span>
                        </div>
                        <h3>{{ $concern->subject }}</h3>
                        <p>{{ $concern->user->name }}{{ $concern->location ? ' • ' . $concern->location : '' }}</p>
                    </div>
                    <a href="{{ route('admin.concerns.show', $concern) }}" class="admin-concern-link">Open</a>
                </article>
            @empty
                <div class="admin-concern-empty">No concern reports yet.</div>
            @endforelse
        </div>
    </section>
</div>

<style>
.admin-concern-page { max-width: 1580px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px; color: #f0e9df; }
.admin-concern-hero, .admin-concern-card, .admin-concern-alert, .admin-concern-stat { border-radius: 28px; border: 1px solid rgba(214,168,91,0.16); box-shadow: 0 24px 48px rgba(0,0,0,0.20); }
.admin-concern-hero { padding: 34px 38px; background: linear-gradient(135deg, rgba(20,16,13,0.96) 0%, rgba(30,23,18,0.90) 55%, rgba(25,19,15,0.96) 100%); }
.admin-concern-kicker { margin: 0 0 10px; color: #d6a85b; font-size: 0.82rem; font-weight: 700; letter-spacing: 0.18em; text-transform: uppercase; }
.admin-concern-title { margin: 0; font-family: 'Playfair Display', serif; font-size: clamp(2.4rem, 4vw, 3.5rem); }
.admin-concern-subtitle { margin: 12px 0 0; max-width: 780px; color: rgba(240,233,223,0.72); line-height: 1.7; }
.admin-concern-alert { padding: 16px 20px; background: rgba(90,138,90,0.16); color: #d8edd8; }
.admin-concern-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; }
.admin-concern-stat { padding: 22px; background: rgba(36,33,31,0.82); }
.admin-concern-stat span { display: block; color: #b8ab98; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.14em; }
.admin-concern-stat strong { display: block; margin-top: 8px; color: #d6a85b; font-size: 2rem; }
.admin-concern-card { padding: 28px; background: rgba(42,44,48,0.82); backdrop-filter: blur(12px); }
.admin-concern-head h2 { margin: 0; font-size: 1.5rem; font-family: 'Playfair Display', serif; }
.admin-concern-head p { margin: 6px 0 0; color: #b8ab98; }
.admin-concern-list { display: flex; flex-direction: column; gap: 14px; margin-top: 20px; }
.admin-concern-row { display: flex; justify-content: space-between; align-items: center; gap: 18px; padding: 18px 20px; border-radius: 20px; background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.05); }
.admin-concern-main h3 { margin: 10px 0 6px; font-size: 1.05rem; }
.admin-concern-main p { margin: 0; color: #b8ab98; }
.admin-concern-meta { display: flex; flex-wrap: wrap; gap: 10px; color: #9f927f; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.08em; }
.admin-concern-link { color: #d6a85b; font-weight: 700; text-decoration: none; }
.admin-concern-status { padding: 6px 10px; border-radius: 999px; font-weight: 700; letter-spacing: 0.08em; }
.admin-concern-status-submitted { background: rgba(214,168,91,0.16); color: #d6a85b; }
.admin-concern-status-in_review { background: rgba(103,138,196,0.16); color: #93afd8; }
.admin-concern-status-responded { background: rgba(90,138,90,0.16); color: #8bc18b; }
.admin-concern-status-closed { background: rgba(255,255,255,0.10); color: #d8d0c6; }
.admin-concern-empty { padding: 22px; border-radius: 20px; text-align: center; color: #b8ab98; border: 1px dashed rgba(214,168,91,0.16); }
@media (max-width: 980px) { .admin-concern-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); } .admin-concern-row { flex-direction: column; align-items: flex-start; } }
</style>
</x-app-layout>
