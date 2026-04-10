<x-app-layout>
@php
    $openConcerns = $concerns->whereIn('status', ['submitted', 'in_review'])->count();
    $respondedConcerns = $concerns->where('status', 'responded')->count();
    $closedConcerns = $concerns->where('status', 'closed')->count();
@endphp
<div class="concern-page">
    <section class="concern-hero">
        <div>
            <p class="concern-kicker">Resident Community Support</p>
            <h1 class="concern-title">My Concern Reports</h1>
            <p class="concern-subtitle">Track private concern reports sent to administration and review official responses in one place.</p>
        </div>
        <div class="concern-hero-actions">
            <a href="{{ route('community.index') }}" class="concern-btn concern-btn-secondary">Back to Community</a>
            <a href="{{ route('concerns.create') }}" class="concern-btn concern-btn-primary">Report New Concern</a>
        </div>
    </section>

    @if (session('success'))
        <div class="concern-alert">{{ session('success') }}</div>
    @endif

    <section class="concern-stats">
        <article class="concern-stat"><span>Open</span><strong>{{ $openConcerns }}</strong></article>
        <article class="concern-stat"><span>Responded</span><strong>{{ $respondedConcerns }}</strong></article>
        <article class="concern-stat"><span>Closed</span><strong>{{ $closedConcerns }}</strong></article>
    </section>

    <section class="concern-card">
        <div class="concern-card-head">
            <div>
                <h2>Private Reports</h2>
                <p>Only you and administration can view the reports listed here.</p>
            </div>
        </div>

        <div class="concern-list">
            @forelse($concerns as $concern)
                <article class="concern-row">
                    <div class="concern-row-main">
                        <div class="concern-row-meta">
                            <span class="concern-status concern-status-{{ $concern->status }}">{{ $concern->status_label }}</span>
                            <span>{{ ucfirst(str_replace('_', ' ', $concern->category)) }}</span>
                            <span>{{ $concern->created_at->format('M d, Y') }}</span>
                        </div>
                        <h3>{{ $concern->subject }}</h3>
                        <p>{{ Str::limit($concern->details, 140) }}</p>
                    </div>
                    <div class="concern-row-side">
                        @if($concern->admin_reply)
                            <span class="concern-reply-note">Admin replied {{ optional($concern->replied_at)->diffForHumans() }}</span>
                        @endif
                        <a href="{{ route('concerns.show', $concern) }}" class="concern-link">View Report</a>
                    </div>
                </article>
            @empty
                <div class="concern-empty">
                    <h3>No concerns submitted yet</h3>
                    <p>If something needs admin attention, you can submit a private report here.</p>
                    <a href="{{ route('concerns.create') }}" class="concern-btn concern-btn-primary">Create First Report</a>
                </div>
            @endforelse
        </div>
    </section>
</div>

<style>
.concern-page { max-width: 980px; margin: 0 auto; display: flex; flex-direction: column; gap: 18px; color: #f0e9df; }
.concern-hero, .concern-card, .concern-alert, .concern-stat { border-radius: 22px; border: 1px solid rgba(214,168,91,0.14); box-shadow: 0 12px 24px rgba(0,0,0,0.14); }
.concern-hero { display: flex; justify-content: space-between; gap: 24px; align-items: center; padding: 28px 30px; background: linear-gradient(115deg, #1F2023 0%, #24262B 38%, #2C2C2F 62%, #3B3023 100%); }
.concern-kicker { margin: 0 0 10px; color: #d6a85b; font-size: 0.82rem; font-weight: 700; letter-spacing: 0.18em; text-transform: uppercase; }
.concern-title { margin: 0; font-family: 'Playfair Display', serif; font-size: clamp(2.2rem, 4vw, 3.3rem); line-height: 1.04; }
.concern-subtitle { margin: 12px 0 0; max-width: 760px; color: rgba(240,233,223,0.74); font-size: 1rem; line-height: 1.7; }
.concern-hero-actions { display: flex; gap: 12px; flex-wrap: wrap; }
.concern-btn { display: inline-flex; align-items: center; justify-content: center; padding: 13px 22px; border-radius: 999px; text-decoration: none; font-weight: 700; border: 1px solid rgba(214,168,91,0.16); }
.concern-btn-primary { background: linear-gradient(135deg, #c79745 0%, #d6a85b 100%); color: #1b150f; }
.concern-btn-secondary { background: rgba(255,255,255,0.04); color: #e8e0d3; }
.concern-alert { padding: 16px 20px; background: rgba(90,138,90,0.16); color: #d8edd8; }
.concern-stats { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; }
.concern-stat { padding: 20px 22px; background: rgba(42,44,48,0.82); }
.concern-stat span { display: block; color: #b8ab98; font-size: 0.82rem; text-transform: uppercase; letter-spacing: 0.14em; }
.concern-stat strong { display: block; margin-top: 8px; color: #d6a85b; font-size: 2rem; }
.concern-card { padding: 20px 22px; background: rgba(42,44,48,0.82); backdrop-filter: blur(10px); }
.concern-card-head h2 { margin: 0; font-size: 1.45rem; font-family: 'Playfair Display', serif; }
.concern-card-head p { margin: 6px 0 0; color: #b8ab98; }
.concern-list { display: flex; flex-direction: column; gap: 14px; margin-top: 20px; }
.concern-row { display: flex; justify-content: space-between; gap: 18px; padding: 18px 20px; border-radius: 20px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); }
.concern-row-main h3 { margin: 10px 0 6px; font-size: 1.05rem; }
.concern-row-main p { margin: 0; color: #b8ab98; line-height: 1.7; }
.concern-row-meta { display: flex; flex-wrap: wrap; gap: 10px; color: #9f927f; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.08em; }
.concern-row-side { display: flex; flex-direction: column; align-items: flex-end; justify-content: space-between; gap: 12px; min-width: 170px; }
.concern-link { color: #d6a85b; font-weight: 700; text-decoration: none; }
.concern-reply-note { color: #b8ab98; font-size: 0.85rem; text-align: right; }
.concern-status { padding: 6px 10px; border-radius: 999px; font-weight: 700; letter-spacing: 0.08em; }
.concern-status-submitted { background: rgba(214,168,91,0.16); color: #d6a85b; }
.concern-status-in_review { background: rgba(103,138,196,0.16); color: #93afd8; }
.concern-status-responded { background: rgba(90,138,90,0.16); color: #8bc18b; }
.concern-status-closed { background: rgba(255,255,255,0.10); color: #d8d0c6; }
.concern-empty { padding: 24px; border-radius: 22px; border: 1px dashed rgba(214,168,91,0.16); text-align: center; }
.concern-empty h3 { margin: 0; font-family: 'Playfair Display', serif; }
.concern-empty p { margin: 10px 0 18px; color: #b8ab98; }
@media (max-width: 900px) { .concern-hero, .concern-row { flex-direction: column; align-items: flex-start; } .concern-stats { grid-template-columns: 1fr; } .concern-row-side { align-items: flex-start; min-width: 0; } .concern-card { padding: 22px; } }
</style>
</x-app-layout>
