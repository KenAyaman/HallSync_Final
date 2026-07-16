<x-app-layout>
<div class="resident-concern-page">

    <section class="resident-concern-hero concern-hero">
        <div class="resident-concern-hero-copy">
            <p class="resident-page-kicker">Private Resident Support</p>
            <h1 class="resident-concern-title">My Concerns</h1>
            <p class="resident-concern-subtitle">Submit a private complaint and read administration replies in one place.</p>
        </div>
        {{-- Consistent primary button matching the design system used on every other resident page --}}
        <a href="{{ route('concerns.create') }}" class="resident-page-btn resident-page-btn-primary">
            Report a Concern
        </a>
    </section>

    <section class="resident-concern-panel">
        <div class="resident-concern-panel-head">
            <div>
                <h2>Your Complaints</h2>
                <p>Administration replies also appear in your notifications.</p>
            </div>
            {{-- Improvement: show count so the user knows how many records they have at a glance --}}
            @if($concerns->count())
                <span class="resident-concern-count">{{ $concerns->count() }} record{{ $concerns->count() === 1 ? '' : 's' }}</span>
            @endif
        </div>

        <div class="resident-concern-list">
            @forelse($concerns as $concern)
                {{--
                    Improvement: the entire row is a link (was split into article + separate link).
                    This gives a much larger, more comfortable tap target especially on mobile.
                --}}
                <a href="{{ route('concerns.show', $concern) }}" class="resident-concern-row {{ $concern->admin_reply ? 'is-replied' : 'is-awaiting' }}">
                    <div class="resident-concern-row-main">
                        <div class="resident-concern-row-meta">
                            <span>{{ $concern->concern_id }}</span>
                            <span>{{ $concern->category_label }}</span>
                            <span>{{ $concern->created_at->format('M d, Y') }}</span>
                            <span class="resident-concern-badge resident-concern-badge-{{ $concern->admin_reply ? 'replied' : 'awaiting' }}">
                                {{ $concern->admin_reply ? 'Replied' : 'Awaiting Reply' }}
                            </span>
                        </div>
                        <h3>{{ $concern->subject }}</h3>
                        <p>{{ Str::limit($concern->details, 145) }}</p>
                    </div>
                    <div class="resident-concern-row-side">
                        <span class="resident-concern-row-cta" aria-hidden="true">View Details</span>
                    </div>
                </a>
            @empty
                {{--
                    Improvement: empty state matches the same warm, helpful pattern
                    used on other resident empty states, with a direct call to action.
                --}}
                <div class="resident-concern-empty">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
                    </svg>
                    <strong>No concerns yet</strong>
                    <p>Your submitted complaints and administration replies will appear here.</p>
                    <a href="{{ route('concerns.create') }}" class="resident-concern-empty-action">Report a Concern</a>
                </div>
            @endforelse
        </div>
    </section>

</div>

<style>
.resident-concern-page {
    max-width: 1180px;
    margin: 0 auto;
    display: grid;
    gap: 20px;
    color: #f0e9df;
}
/* ── Hero — mirrors the pattern on all other resident pages ── */
.resident-concern-hero {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
    padding: 28px 30px;
    border-radius: 36px;
    border: 1px solid rgba(214, 168, 91, 0.14);
    box-shadow: 0 14px 32px rgba(0, 0, 0, 0.18);
    /* Shares the same gradient family as other resident heroes */
    background: linear-gradient(115deg, #1F2023 0%, #24262B 38%, #2C2C2F 62%, #3B3023 100%);
}
.resident-concern-hero-copy {
    max-width: 680px;
}
.resident-concern-title {
    margin: 6px 0 0;
    color: #F8F3EA;
    font-family: 'Playfair Display', serif;
    font-size: clamp(2.2rem, 4vw, 3.2rem);
    font-weight: 600;
    line-height: 1.06;
}
.resident-concern-subtitle {
    display: block;
    margin-top: 8px;
    color: rgba(255, 255, 255, 0.78);
    font-size: 0.96rem;
    line-height: 1.6;
}
/* ── Panel ─────────────────────────────────────────────────── */
.resident-concern-panel {
    border-radius: 20px;
    border: 1px solid rgba(214, 168, 91, 0.13);
    background: rgba(42, 44, 48, 0.78);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    box-shadow: 0 10px 22px rgba(0, 0, 0, 0.14);
    overflow: hidden;
}
.resident-concern-panel-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 18px 22px 15px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.06);
}
.resident-concern-panel-head h2 {
    margin: 0;
    color: #F0E9DF;
    font-family: 'Playfair Display', serif;
    font-size: 1.38rem;
    font-weight: 600;
}
.resident-concern-panel-head p {
    margin: 4px 0 0;
    color: #8A7A66;
    font-size: 0.82rem;
    line-height: 1.5;
}
.resident-concern-count {
    color: #8A7A66;
    font-size: 0.76rem;
    font-weight: 700;
    white-space: nowrap;
}
/* ── List rows ──────────────────────────────────────────────── */
.resident-concern-list {
    display: flex;
    flex-direction: column;
}
.resident-concern-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 18px;
    padding: 16px 22px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    text-decoration: none;
    color: inherit;
    transition: background 0.16s ease, border-color 0.16s ease;
}
.resident-concern-row:last-child {
    border-bottom: none;
}
.resident-concern-row:hover {
    background: rgba(214, 168, 91, 0.06);
}
.resident-concern-row-main {
    flex: 1;
    min-width: 0;
}
.resident-concern-row-meta {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 6px 9px;
    color: #9f927f;
    font-size: 0.70rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    margin-bottom: 7px;
}
.resident-concern-row-meta > span:not(.resident-concern-badge) {
    display: inline-flex;
    align-items: center;
    gap: 9px;
}
.resident-concern-row-meta > span:not(.resident-concern-badge) + span:not(.resident-concern-badge)::before {
    content: '';
    width: 4px;
    height: 4px;
    border-radius: 999px;
    background: rgba(214, 168, 91, 0.42);
    flex: 0 0 auto;
}
.resident-concern-row h3 {
    margin: 0 0 4px;
    color: #F0E9DF;
    font-size: 0.96rem;
    font-weight: 600;
    line-height: 1.35;
}
.resident-concern-row p {
    margin: 0;
    color: #B0A592;
    font-size: 0.84rem;
    line-height: 1.55;
}
.resident-concern-row.is-replied p {
    color: rgba(176, 165, 146, 0.68);
}
.resident-concern-row-side {
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    min-width: 132px;
    align-self: stretch;
}
/* ── Status badges ──────────────────────────────────────────── */
.resident-concern-badge {
    display: inline-flex;
    align-items: center;
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 0.66rem;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    border: 1px solid transparent;
}
.resident-concern-badge-replied {
    background: rgba(111, 130, 96, 0.13);
    color: rgba(181, 196, 164, 0.74);
    border-color: rgba(111, 130, 96, 0.20);
}
.resident-concern-badge-awaiting {
    background: rgba(190, 140, 60, 0.18);
    color: #F0C879;
    border-color: rgba(190, 140, 60, 0.28);
}
.resident-concern-row-cta {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 36px;
    padding: 0 15px;
    border: 1px solid rgba(214, 168, 91, 0.16);
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.035);
    color: rgba(232, 202, 156, 0.82);
    font-size: 0.72rem;
    font-weight: 650;
    letter-spacing: 0.07em;
    line-height: 1;
    text-transform: uppercase;
    white-space: nowrap;
    transition: background 0.14s ease, border-color 0.14s ease, color 0.14s ease, transform 0.14s ease;
}
.resident-concern-row:hover .resident-concern-row-cta {
    border-color: rgba(214, 168, 91, 0.26);
    background: rgba(214, 168, 91, 0.075);
    color: #F0D7A0;
    transform: translateY(-1px);
}
/* ── Empty state ────────────────────────────────────────────── */
.resident-concern-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 42px 28px;
    text-align: center;
    color: #8A7A66;
}
.resident-concern-empty svg {
    width: 34px;
    height: 34px;
    margin-bottom: 4px;
    opacity: 0.45;
}
.resident-concern-empty strong {
    display: block;
    color: #c4b6a2;
    font-size: 0.96rem;
    font-weight: 600;
}
.resident-concern-empty p {
    margin: 0;
    max-width: 360px;
    font-size: 0.84rem;
    line-height: 1.6;
}
.resident-concern-empty-action {
    display: inline-flex;
    align-items: center;
    margin-top: 8px;
    padding: 10px 20px;
    border-radius: 999px;
    background: linear-gradient(90deg, #bc7f1c 0%, #d79d3f 100%);
    color: #fff7ea;
    font-size: 0.84rem;
    font-weight: 700;
    text-decoration: none;
    box-shadow: 0 8px 20px rgba(199, 150, 69, 0.22);
    transition: transform 0.18s ease, box-shadow 0.18s ease;
}
.resident-concern-empty-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 26px rgba(199, 150, 69, 0.32);
}
/* ── Hero action button ─────────────────────────────────────── */
.resident-page-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 12px 22px;
    border-radius: 999px;
    text-decoration: none;
    font-size: 0.92rem;
    font-weight: 700;
    white-space: nowrap;
    flex-shrink: 0;
    transition: transform 0.2s ease;
}
.resident-page-btn-primary {
    background: linear-gradient(95deg, #b8842f, #d6a85b);
    color: #17120d;
}
.resident-page-btn-primary:hover {
    transform: translateY(-1px);
}
/* ── Responsive ─────────────────────────────────────────────── */
@media (max-width:760px) {
    .resident-concern-hero {
        flex-direction: column;
        align-items: flex-start;
        padding: 22px 22px;
    }
    .resident-concern-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }
    .resident-concern-row-side {
        align-items: center;
        flex-direction: row;
        justify-content: flex-start;
        width: 100%;
        min-width: 0;
        align-self: auto;
    }
}
</style>
</x-app-layout>
