<x-app-layout>
@php
    $recentTickets      = $recentTickets      ?? collect();
    $announcements      = $announcements      ?? collect();
    $communityPosts     = $communityPosts     ?? collect();
    $heroName           = Auth::user()->name;
    $firstName          = explode(' ', $heroName)[0];
    $activeTickets      = $activeTickets      ?? 0;
    $inProgressTickets  = $inProgressTickets  ?? 0;
    $upcomingBookings   = $upcomingBookingsCount ?? 0;
    $pendingPosts       = $pendingPostsCount  ?? 0;

    // Improvement: time-based greeting so the dashboard feels alive
    $hour     = now()->hour;
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');

    $activityCards = [
        [
            'label'  => 'Active Repairs',
            'value'  => $activeTickets,
            'note'   => $inProgressTickets . ' currently in progress',
            'tone'   => 'gold',
            'icon'   => 'wrench',
            'href'   => route('tickets.index'),
            'aria'   => 'View maintenance tickets',
        ],
        [
            'label'  => 'Upcoming Bookings',
            'value'  => $upcomingBookings,
            'note'   => 'of 3 maximum slots',
            'tone'   => 'neutral',
            'icon'   => 'calendar',
            'href'   => route('bookings.index'),
            'aria'   => 'View facility bookings',
        ],
        [
            'label'  => 'Posts Pending',
            'value'  => $pendingPosts,
            'note'   => 'awaiting moderation',
            'tone'   => 'neutral',
            'icon'   => 'clipboard',
            'href'   => route('community.index'),
            'aria'   => 'View community posts',
        ],
    ];
@endphp

<div class="resident-dashboard-shell">

    {{-- ─────────────────────── HERO ─────────────────────────────── --}}
    <section class="resident-home-hero">
        <div class="resident-home-hero-content">
            {{-- Improvement: personalised time-based greeting builds familiarity --}}
            <p class="resident-home-kicker">{{ $greeting }}, {{ $firstName }}</p>
            <h1 class="resident-home-title">Resident Overview</h1>
            <p class="resident-home-subtitle">Manage maintenance requests, facility bookings, and community updates from one place.</p>

            <div class="resident-home-actions">
                <a href="{{ route('tickets.create') }}" class="resident-home-btn resident-home-btn-primary">
                    <svg class="resident-home-btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.7 6.3a4 4 0 01-5.23 5.23L4.5 16.5a2.12 2.12 0 103 3l4.97-4.97a4 4 0 005.23-5.23l-2.4 2.4-3-3 2.4-2.4z"></path>
                    </svg>
                    Report Maintenance Issue
                </a>

                <a href="{{ route('bookings.create') }}" class="resident-home-btn resident-home-btn-secondary">
                    <svg class="resident-home-btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Book a Facility
                </a>
            </div>
        </div>
    </section>

    {{-- ─────────────────────── ACTIVITY STATS ────────────────────── --}}
    <section class="resident-activity">
        <h2 class="resident-section-title">Your Activity</h2>

        <div class="resident-activity-grid">
            @foreach($activityCards as $card)
                {{--
                    Improvement: activity cards are now clickable links so users can
                    immediately navigate to the relevant section without an extra step.
                    The card still looks identical — it's just wrapped in an <a>.
                --}}
                <a href="{{ $card['href'] }}"
                   class="resident-activity-card resident-activity-card-{{ $card['tone'] }} resident-activity-card-link"
                   aria-label="{{ $card['aria'] }}">

                    <div class="resident-activity-card-icon">
                        @if($card['icon'] === 'wrench')
                            <svg class="resident-dashboard-loaded-content" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.7 6.3a4 4 0 01-5.23 5.23L4.5 16.5a2.12 2.12 0 103 3l4.97-4.97a4 4 0 005.23-5.23l-2.4 2.4-3-3 2.4-2.4z"></path>
                            </svg>
                        @elseif($card['icon'] === 'clipboard')
                            <svg class="resident-dashboard-loaded-content" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5h6m-7 3h8m-9 11h10a2 2 0 002-2V7a2 2 0 00-2-2h-1.5a1.5 1.5 0 01-3 0h-3a1.5 1.5 0 01-3 0H7a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        @else
                            <svg class="resident-dashboard-loaded-content" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        @endif
                        <span class="resident-dashboard-skeleton resident-dashboard-skeleton-icon" aria-hidden="true"></span>
                    </div>

                    <div class="resident-activity-card-copy">
                        <div class="resident-activity-card-top resident-dashboard-loaded-content">
                            {{-- Improvement: value is visually separated from label so the number reads at a glance --}}
                            <strong class="resident-activity-card-value">{{ $card['value'] }}</strong>
                            <span class="resident-activity-card-label">{{ $card['label'] }}</span>
                        </div>
                        <p class="resident-dashboard-loaded-content">{{ $card['note'] }}</p>

                        <div class="resident-dashboard-skeleton-copy" aria-hidden="true">
                            <span class="resident-dashboard-skeleton resident-dashboard-skeleton-line resident-dashboard-skeleton-line-title"></span>
                            <span class="resident-dashboard-skeleton resident-dashboard-skeleton-line resident-dashboard-skeleton-line-short"></span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- ─────────────────────── TWO-COLUMN CONTENT ─────────────────── --}}
    <section class="resident-content-grid">

        {{-- Recent Requests --}}
        <article class="resident-surface-panel resident-surface-panel-wide">
            <div class="resident-surface-head">
                <div>
                    <h2>Recent Requests</h2>
                    <p>Your latest maintenance submissions</p>
                </div>
                <a href="{{ route('tickets.index') }}" class="resident-surface-view-all">View all</a>
            </div>

            <div class="resident-surface-divider"></div>

            {{-- Skeleton --}}
            <div class="resident-stack-list resident-dashboard-skeleton-list" aria-hidden="true">
                @for($i = 0; $i < 3; $i++)
                    <div class="resident-stack-item">
                        <div class="resident-stack-item-icon resident-stack-item-icon-ticket">
                            <span class="resident-dashboard-skeleton resident-dashboard-skeleton-icon-small"></span>
                        </div>
                        <div class="resident-stack-item-main">
                            <div class="resident-stack-item-row">
                                <div class="resident-dashboard-skeleton-main">
                                    <span class="resident-dashboard-skeleton resident-dashboard-skeleton-line resident-dashboard-skeleton-line-title"></span>
                                    <span class="resident-dashboard-skeleton resident-dashboard-skeleton-line resident-dashboard-skeleton-line-wide"></span>
                                </div>
                                <div class="resident-stack-item-side resident-dashboard-skeleton-side">
                                    <span class="resident-dashboard-skeleton resident-dashboard-skeleton-chip"></span>
                                    <span class="resident-dashboard-skeleton resident-dashboard-skeleton-line resident-dashboard-skeleton-line-meta"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>

            {{-- Loaded content --}}
            <div class="resident-stack-list resident-dashboard-loaded-content">
                @forelse($recentTickets->take(3) as $ticket)
                    {{-- Improvement: whole row is a link so users don't have to hunt for the tap target --}}
                    <a href="{{ route('tickets.show', $ticket) }}" class="resident-stack-item resident-stack-item-linked">
                        <div class="resident-stack-item-icon resident-stack-item-icon-ticket" aria-hidden="true">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M14.7 6.3a4 4 0 01-5.23 5.23L4.5 16.5a2.12 2.12 0 103 3l4.97-4.97a4 4 0 005.23-5.23l-2.4 2.4-3-3 2.4-2.4z"></path>
                            </svg>
                        </div>

                        <div class="resident-stack-item-main">
                            <div class="resident-stack-item-row">
                                <div>
                                    <h3>{{ $ticket->title }}</h3>
                                    <p>{{ Str::limit($ticket->description ?? '', 64) }}</p>
                                </div>
                                <div class="resident-stack-item-side">
                                    {{-- Improvement: uses the global resident-status-chip system for visual consistency --}}
                                    <span class="resident-status-chip resident-status-chip-{{ $ticket->status }}">
                                        {{ $ticket->status_label ?? ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                    </span>
                                    <span class="resident-stack-meta">{{ $ticket->updated_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="resident-dashboard-empty">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M14.7 6.3a4 4 0 01-5.23 5.23L4.5 16.5a2.12 2.12 0 103 3l4.97-4.97a4 4 0 005.23-5.23l-2.4 2.4-3-3 2.4-2.4z"></path>
                        </svg>
                        <strong>No recent requests</strong>
                        <p>Your submitted maintenance tickets will appear here.</p>
                        <a href="{{ route('tickets.create') }}">Submit a request</a>
                    </div>
                @endforelse
            </div>
        </article>

        {{-- Notifications --}}
        <article class="resident-surface-panel resident-surface-panel-narrow">
            <div class="resident-surface-head">
                <div>
                    <h2>Notifications
                        @if(($navNotificationCount ?? 0) > 0)
                            <span class="resident-notif-count-badge">{{ $navNotificationCount }}</span>
                        @endif
                    </h2>
                    <p>Updates across your account</p>
                </div>
                <a href="{{ route('notifications.index') }}" class="resident-surface-view-all">View all</a>
            </div>

            <div class="resident-surface-divider"></div>

            {{-- Skeleton --}}
            <div class="resident-notice-list resident-dashboard-skeleton-list" aria-hidden="true">
                @for($i = 0; $i < 2; $i++)
                    <div class="resident-notice-card">
                        <div class="resident-notice-title">
                            <span class="resident-notice-marker">
                                <span class="resident-dashboard-skeleton resident-dashboard-skeleton-dot"></span>
                            </span>
                            <span class="resident-dashboard-skeleton resident-dashboard-skeleton-line resident-dashboard-skeleton-line-title"></span>
                        </div>
                        <span class="resident-dashboard-skeleton resident-dashboard-skeleton-line resident-dashboard-skeleton-line-wide"></span>
                        <span class="resident-dashboard-skeleton resident-dashboard-skeleton-line resident-dashboard-skeleton-line-meta"></span>
                    </div>
                @endfor
            </div>

            {{-- Loaded content — shows the same notifications as the bell --}}
            <div class="resident-notice-list resident-dashboard-loaded-content">
                @forelse(($navNotifications ?? collect())->take(3) as $notification)
                    <a href="{{ $notification['url'] }}" class="resident-notice-card resident-notice-card-linked resident-notice-card-unread">
                        <div class="resident-notice-title">
                            <span class="resident-notice-marker resident-notice-marker-normal" aria-hidden="true"></span>
                            <h3>{{ $notification['message'] }}</h3>
                        </div>
                        <span class="resident-notice-type-label">{{ $notification['title'] }}</span>
                        <span class="resident-stack-meta">{{ $notification['time'] }}</span>
                    </a>
                @empty
                    <div class="resident-dashboard-empty resident-dashboard-empty-sm">
                        <strong>All caught up</strong>
                        <p>New notifications will appear here.</p>
                    </div>
                @endforelse
            </div>
        </article>
    </section>

    {{-- ─────────────────────── COMMUNITY BOARD ───────────────────── --}}
    <section class="resident-surface-panel">
        <div class="resident-surface-head">
            <div>
                <h2>Community Board</h2>
                <p>Resident conversations and shared updates around Rexhall</p>
            </div>
            <a href="{{ route('community.index') }}" class="resident-surface-view-all">View all</a>
        </div>

        <div class="resident-surface-divider"></div>

        {{-- Skeleton --}}
        <div class="resident-community-board resident-dashboard-skeleton-list" aria-hidden="true">
            @for($i = 0; $i < 3; $i++)
                <article class="resident-community-entry">
                    <div class="resident-community-entry-main">
                        <span class="resident-dashboard-skeleton resident-dashboard-skeleton-line resident-dashboard-skeleton-line-title"></span>
                        <span class="resident-dashboard-skeleton resident-dashboard-skeleton-line resident-dashboard-skeleton-line-wide"></span>
                        <span class="resident-dashboard-skeleton resident-dashboard-skeleton-line resident-dashboard-skeleton-line-short"></span>
                        <span class="resident-dashboard-skeleton resident-dashboard-skeleton-line resident-dashboard-skeleton-line-meta"></span>
                    </div>
                    <span class="resident-dashboard-skeleton resident-dashboard-skeleton-line resident-dashboard-skeleton-line-meta"></span>
                </article>
            @endfor
        </div>

        {{-- Loaded content --}}
        <div class="resident-community-board resident-dashboard-loaded-content">
            @forelse($communityPosts->take(3) as $post)
                {{-- Improvement: whole entry is a link; category pill gives quick visual context --}}
                <a href="{{ route('community.show', $post) }}" class="resident-community-entry resident-community-entry-linked">
                    <div class="resident-community-entry-main">
                        <div class="resident-community-entry-heading">
                            <h3>{{ $post->title }}</h3>
                            @if($post->category ?? false)
                                <span class="resident-community-category-pill">{{ ucfirst(str_replace('_', ' ', $post->category)) }}</span>
                            @endif
                        </div>
                        <p>{{ Str::limit($post->content, 160) }}</p>
                        <span class="resident-stack-meta">By {{ $post->user->name ?? 'Resident' }}</span>
                    </div>
                    <div class="resident-community-entry-time">{{ $post->created_at->diffForHumans() }}</div>
                </a>
            @empty
                <div class="resident-dashboard-empty">
                    <strong>No community posts yet</strong>
                    <p>Resident conversations and shared updates will appear here.</p>
                    <a href="{{ route('community.create') }}">Share something</a>
                </div>
            @endforelse
        </div>
    </section>

</div>

<style>
/* ──────────────────────────────────────────────────────────────
   DASHBOARD SHELL
   Keeps the original dark warm palette and spacing intact.
   Only targeted improvements are noted with comments.
────────────────────────────────────────────────────────────── */
.resident-dashboard-shell {
    max-width: 1580px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 24px;
    color: #d9cbb8;
}
/* ── Hero ───────────────────────────────────────────────────── */
.resident-home-hero {
    margin-top: -2px;
    background: transparent;
}
.resident-home-hero-content {
    padding: 22px 0 6px;
    max-width: 720px;
}
.resident-home-kicker {
    margin: 0 0 10px;
    color: #e4c9a4;
    font-size: 0.82rem;
    font-weight: 700;
    line-height: 1.3;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}
.resident-home-title {
    margin: 0;
    color: #f5e7d4;
    font-family: 'Playfair Display', serif;
    font-size: clamp(2.2rem, 4.6vw, 4rem);
    font-weight: 600;
    line-height: 1.06;
    letter-spacing: -0.025em;
}
.resident-home-subtitle {
    margin: 10px 0 0;
    max-width: 680px;
    color: rgba(244, 231, 212, 0.85);
    font-size: clamp(0.96rem, 1.2vw, 1.06rem);
    line-height: 1.65;
}
.resident-home-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 14px;
    margin-top: 26px;
}
.resident-home-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    min-width: 216px;
    padding: 14px 24px;
    border-radius: 999px;
    text-decoration: none;
    font-size: 0.92rem;
    font-weight: 700;
    letter-spacing: 0.01em;
    transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease, border-color 0.2s ease;
}
.resident-home-btn:hover {
    transform: translateY(-2px);
}
.resident-home-btn-icon {
    width: 18px;
    height: 18px;
    flex-shrink: 0;
}
.resident-home-btn-primary {
    color: #fff7ea;
    background: linear-gradient(90deg, #bc7f1c 0%, #d79d3f 100%);
    box-shadow: 0 10px 26px rgba(199, 150, 69, 0.24);
}
.resident-home-btn-primary:hover {
    box-shadow: 0 14px 32px rgba(199, 150, 69, 0.34);
}
.resident-home-btn-secondary {
    color: #f5e7d4;
    background: linear-gradient(135deg, rgba(42, 44, 48, 0.96), rgba(57, 48, 38, 0.92));
    border: 1px solid rgba(214, 168, 91, 0.20);
    box-shadow: 0 10px 26px rgba(0, 0, 0, 0.22);
}
.resident-home-btn-secondary:hover {
    border-color: rgba(214, 168, 91, 0.34);
}
/* ── Section title ──────────────────────────────────────────── */
.resident-section-title {
    margin: 0 0 14px;
    color: #f0e4d2;
    font-family: 'Inter', sans-serif;
    font-size: 1rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}
/* ── Activity cards ─────────────────────────────────────────── */
.resident-activity-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
}
/*
   Improvement:.resident-activity-card-link is added via JS for the
   clickable version. It adds a subtle arrow affordance and a hover
   border highlight so users know the card is interactive.
*/
.resident-activity-card, .resident-surface-panel {
    background: rgba(42, 44, 48, 0.78);
    border: 1px solid rgba(214, 168, 91, 0.13);
    border-radius: 20px;
    box-shadow: 0 10px 22px rgba(0, 0, 0, 0.14);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}
.resident-activity-card {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 20px 22px;
    text-decoration: none;
    color: inherit;
    transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
}
.resident-activity-card-link:hover {
    transform: translateY(-3px);
    border-color: rgba(214, 168, 91, 0.26);
    box-shadow: 0 16px 36px rgba(0, 0, 0, 0.20);
}
.resident-activity-card-icon {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.resident-activity-card-icon svg {
    width: 22px;
    height: 22px;
}
.resident-activity-card-gold .resident-activity-card-icon {
    color: #f3ddba;
    background: linear-gradient(135deg, rgba(190, 132, 47, 0.90), rgba(214, 168, 91, 0.66));
    box-shadow: 0 6px 18px rgba(180, 119, 33, 0.22);
}
.resident-activity-card-neutral .resident-activity-card-icon {
    color: #e4c58e;
    background: rgba(214, 168, 91, 0.12);
    border: 1px solid rgba(214, 168, 91, 0.18);
}
.resident-activity-card-copy {
    display: flex;
    flex-direction: column;
    gap: 3px;
    min-width: 0;
    flex: 1;
}
.resident-activity-card-top {
    display: flex;
    align-items: baseline;
    gap: 6px;
    flex-wrap: wrap;
}
/*
   Improvement:number and label are separated so the count reads
   instantly (large number) before the context label (smaller).
*/
.resident-activity-card-value {
    display: block;
    color: #f2e6d4;
    font-size: 1.9rem;
    font-weight: 800;
    line-height: 1;
    letter-spacing: -0.03em;
}
.resident-activity-card-gold .resident-activity-card-value {
    color: #e9c17f;
}
.resident-activity-card-label {
    color: #c4b6a2;
    font-size: 0.84rem;
    font-weight: 600;
    line-height: 1.3;
}
.resident-activity-card-copy p {
    margin: 4px 0 0;
    color: #b9aa95;
    font-size: 0.82rem;
    line-height: 1.35;
}
/* ── Content grid ───────────────────────────────────────────── */
.resident-content-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.4fr) minmax(300px, 0.6fr);
    gap: 16px;
}
/* ── Surface panels ─────────────────────────────────────────── */
.resident-surface-panel {
    padding: 22px 24px;
}
.resident-surface-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 14px;
}
.resident-surface-head h2 {
    margin: 0;
    color: #F0E9DF;
    font-family: 'Playfair Display', serif;
    font-size: 1.38rem;
    font-weight: 600;
    line-height: 1.2;
}
.resident-surface-head p {
    margin: 4px 0 0;
    color: #8A7A66;
    font-size: 0.82rem;
    line-height: 1.5;
}
/*
   Improvement:"View all" uses a consistent understated style across
   all panels — small, uppercase, gold — so users always know where
   to tap for the full list.
*/
.resident-surface-view-all {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    color: #D6A85B;
    text-decoration: none;
    font-size: 0.76rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    white-space: nowrap;
    flex-shrink: 0;
    padding-top: 3px;
    transition: opacity 0.15s ease;
}
.resident-surface-view-all:hover {
    opacity: 0.70;
}
.resident-surface-divider {
    height: 1px;
    background: linear-gradient(to right, rgba(214, 168, 91, 0.28), rgba(214, 168, 91, 0.06), transparent);
    margin-bottom: 16px;
}
/* ── Stack list (tickets) ───────────────────────────────────── */
.resident-stack-list, .resident-notice-list, .resident-community-board {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.resident-stack-item {
    display: flex;
    gap: 14px;
    align-items: center;
    padding: 13px 15px;
    border-radius: 14px;
    border: 1px solid rgba(255, 255, 255, 0.05);
    background: rgba(255, 255, 255, 0.035);
}
/*
   Improvement:linked stack items get hover feedback so the
   entire row feels tappable, not just a small link inside it.
*/
.resident-stack-item-linked {
    text-decoration: none;
    color: inherit;
    transition: background 0.16s ease, border-color 0.16s ease, transform 0.16s ease;
    cursor: pointer;
}
.resident-stack-item-linked:hover {
    background: rgba(214, 168, 91, 0.08);
    border-color: rgba(214, 168, 91, 0.18);
    transform: translateX(3px);
}
.resident-stack-item-icon {
    width: 44px;
    height: 44px;
    border-radius: 13px;
    flex-shrink: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
}
.resident-stack-item-icon-ticket {
    flex-direction: column;
    gap: 2px;
    background: linear-gradient(145deg, rgba(190, 132, 47, 0.20), rgba(96, 72, 48, 0.38));
    border: 1px solid rgba(214, 168, 91, 0.16);
    color: #f1d39c;
}
.resident-stack-item-icon-ticket svg {
    width: 17px;
    height: 17px;
}
.resident-stack-item-main {
    min-width: 0;
    flex: 1;
}
.resident-stack-item-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
}
.resident-stack-item-row h3, .resident-notice-title h3, .resident-community-entry h3 {
    margin: 0;
    color: #F0E9DF;
    font-size: 0.94rem;
    font-weight: 600;
    line-height: 1.35;
}
.resident-stack-item-row p, .resident-notice-card p, .resident-community-entry p {
    margin: 4px 0 0;
    color: #B0A592;
    line-height: 1.6;
    font-size: 0.84rem;
}
.resident-stack-item-side {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 6px;
    flex-shrink: 0;
}
/* ── Status chips ────────────────────────────────────────────── */
/*
   Improvement:the full status set is now covered so no chip ever
   falls through to an unstyled default. Each status has a distinct
   readable colour that works on both dark and light panel surfaces.
*/
.resident-status-chip {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 5px 11px;
    border-radius: 999px;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    border: 1px solid transparent;
    white-space: nowrap;
}
.resident-status-chip-pending_approval, .resident-status-chip-pending {
    background: rgba(190, 140, 60, 0.16);
    color: #e6c17a;
    border-color: rgba(190, 140, 60, 0.22);
}
.resident-status-chip-approved {
    background: rgba(80, 148, 180, 0.14);
    color: #9acae0;
    border-color: rgba(80, 148, 180, 0.20);
}
.resident-status-chip-assigned {
    background: rgba(190, 140, 60, 0.14);
    color: #d9a44a;
    border-color: rgba(190, 140, 60, 0.20);
}
.resident-status-chip-in_progress {
    background: rgba(130, 160, 60, 0.16);
    color: #b3d175;
    border-color: rgba(130, 160, 60, 0.22);
}
.resident-status-chip-resolved, .resident-status-chip-completed {
    background: rgba(80, 160, 100, 0.16);
    color: #8fd4a8;
    border-color: rgba(80, 160, 100, 0.22);
}
.resident-status-chip-closed {
    background: rgba(140, 140, 140, 0.14);
    color: #c0b8ac;
    border-color: rgba(140, 140, 140, 0.18);
}
.resident-status-chip-rejected, .resident-status-chip-cancelled {
    background: rgba(210, 90, 80, 0.14);
    color: #f0a29a;
    border-color: rgba(210, 90, 80, 0.20);
}
.resident-status-chip-submitted, .resident-status-chip-open {
    background: rgba(255, 255, 255, 0.07);
    color: #ddd1be;
    border-color: rgba(255, 255, 255, 0.09);
}
.resident-stack-meta, .resident-community-entry-time {
    color: #8A7A66;
    font-size: 0.78rem;
}
/* ── Notice cards ───────────────────────────────────────────── */
.resident-notice-card {
    padding: 14px 16px;
    border-radius: 14px;
    border: 1px solid rgba(255, 255, 255, 0.05);
    background: rgba(255, 255, 255, 0.035);
    text-decoration: none;
    color: inherit;
    display: block;
}
.resident-notice-card-linked {
    transition: transform 0.18s ease, background 0.16s ease, border-color 0.16s ease, box-shadow 0.18s ease;
    cursor: pointer;
}
.resident-notice-card-linked:hover {
    transform: translateY(-2px);
    background: rgba(214, 168, 91, 0.08);
    border-color: rgba(214, 168, 91, 0.18);
    box-shadow: 0 10px 22px rgba(0, 0, 0, 0.14);
}
.resident-notice-card-unread {
    border-color: rgba(214, 168, 91, 0.1);
    background: rgba(214, 168, 91, 0.04);
}
.resident-notice-type-label {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-top: 5px;
    padding: 3px 9px;
    border-radius: 999px;
    border: 1px solid rgba(190, 140, 60, 0.20);
    background: rgba(190, 140, 60, 0.14);
    color: #d9a44a;
    font-size: 0.64rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    line-height: 1.1;
    text-transform: uppercase;
    white-space: nowrap;
}
.resident-notif-count-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 22px;
    height: 22px;
    padding: 0 6px;
    border-radius: 999px;
    background: #e07060;
    color: #ffffff;
    font-size: 0.68rem;
    font-weight: 800;
    line-height: 1;
    vertical-align: middle;
    margin-left: 6px;
}
.resident-notice-title {
    display: flex;
    gap: 10px;
    align-items: flex-start;
    margin-bottom: 6px;
    min-width: 0;
}
.resident-notice-title h3 {
    flex: 1;
    min-width: 0;
    overflow: hidden;
    white-space: nowrap;
    -webkit-mask-image: linear-gradient(to right, #000 0, #000 calc(100% - 34px), transparent 100%);
    mask-image: linear-gradient(to right, #000 0, #000 calc(100% - 34px), transparent 100%);
}
/*
   Improvement:priority markers are now visually distinct by colour,
   not just a generic dot. Urgent pulses to draw the eye without
   being disruptive to the rest of the page.
*/
.resident-notice-marker {
    width: 10px;
    height: 10px;
    border-radius: 999px;
    flex-shrink: 0;
    margin-top: 5px;
    background: rgba(214, 168, 91, 0.50);
}
.resident-notice-marker-normal {
    background: rgba(214, 168, 91, 0.42);
}
.resident-notice-marker-important {
    background: #5890b4;
    box-shadow: 0 0 0 3px rgba(88, 144, 180, 0.16);
}
.resident-notice-marker-urgent {
    background: #d4685a;
    box-shadow: 0 0 0 3px rgba(212, 104, 90, 0.18);
    animation: resident-priority-pulse 1.8s ease infinite;
}
@keyframes resident-priority-pulse {
    0%, 100% {
        box-shadow: 0 0 0 3px rgba(212, 104, 90, 0.18);
    }
    50% {
        box-shadow: 0 0 0 6px rgba(212, 104, 90, 0.06);
    }
}
/* ── Community board ────────────────────────────────────────── */
.resident-community-entry {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 14px;
    padding: 15px 17px;
    border-radius: 14px;
    border: 1px solid rgba(255, 255, 255, 0.05);
    background: rgba(255, 255, 255, 0.035);
    text-decoration: none;
    color: inherit;
}
.resident-community-entry-linked {
    transition: transform 0.18s ease, background 0.16s ease, border-color 0.16s ease, box-shadow 0.18s ease;
    cursor: pointer;
}
.resident-community-entry-linked:hover {
    transform: translateY(-2px);
    background: rgba(214, 168, 91, 0.08);
    border-color: rgba(214, 168, 91, 0.18);
    box-shadow: 0 10px 22px rgba(0, 0, 0, 0.14);
}
.resident-community-entry-main {
    min-width: 0;
    flex: 1;
}
.resident-community-entry-heading {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 4px;
}
/*
   Improvement:category pill gives quick visual context so users
   know post type before reading the content.
*/
.resident-community-category-pill {
    display: inline-flex;
    padding: 2px 8px;
    border-radius: 999px;
    background: rgba(214, 168, 91, 0.10);
    border: 1px solid rgba(214, 168, 91, 0.16);
    color: #c9a45a;
    font-size: 0.64rem;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    flex-shrink: 0;
}
/* ── Dashboard empty states ─────────────────────────────────── */
.resident-dashboard-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 28px 20px;
    border-radius: 14px;
    border: 1px dashed rgba(214, 168, 91, 0.18);
    background: rgba(214, 168, 91, 0.03);
    text-align: center;
    color: #8A7A66;
}
.resident-dashboard-empty svg {
    width: 30px;
    height: 30px;
    margin-bottom: 4px;
    opacity: 0.5;
}
.resident-dashboard-empty strong {
    display: block;
    color: #c4b6a2;
    font-size: 0.92rem;
    font-weight: 600;
}
.resident-dashboard-empty p {
    margin: 0;
    font-size: 0.82rem;
    line-height: 1.55;
}
.resident-dashboard-empty a {
    color: #D6A85B;
    font-size: 0.8rem;
    font-weight: 700;
    text-decoration: none;
    margin-top: 4px;
}
.resident-dashboard-empty-sm {
    padding: 18px;
}
/* ── Skeleton loading ───────────────────────────────────────── */
.resident-dashboard-skeleton, .resident-dashboard-skeleton-list {
    display: none;
}
.resident-dashboard-skeleton {
    border-radius: 999px;
    background: linear-gradient(90deg, rgba(255, 255, 255, 0.055), rgba(255, 255, 255, 0.17), rgba(255, 255, 255, 0.055));
    background-size: 220% 100%;
    animation: skeleton-shimmer 1.15s ease-in-out infinite;
}
html.is-loading .resident-dashboard-loaded-content {
    display: none !important;
}
html.is-loading .resident-dashboard-skeleton {
    display: block;
}
html.is-loading .resident-dashboard-skeleton-list {
    display: flex;
}
.resident-dashboard-skeleton-copy, .resident-dashboard-skeleton-main {
    width: 100%;
    display: none;
}
html.is-loading .resident-dashboard-skeleton-copy, html.is-loading .resident-dashboard-skeleton-main {
    display: block;
}
.resident-dashboard-skeleton-icon {
    width: 22px;
    height: 22px;
}
.resident-dashboard-skeleton-icon-small {
    width: 24px;
    height: 24px;
}
.resident-dashboard-skeleton-dot {
    width: 8px;
    height: 8px;
}
.resident-dashboard-skeleton-line {
    height: 11px;
    margin-top: 8px;
}
.resident-dashboard-skeleton-line:first-child {
    margin-top: 0;
}
.resident-dashboard-skeleton-line-title {
    width: min(200px, 72%);
    height: 14px;
}
.resident-dashboard-skeleton-line-wide {
    width: min(480px, 86%);
}
.resident-dashboard-skeleton-line-short {
    width: min(160px, 50%);
}
.resident-dashboard-skeleton-line-meta {
    width: 130px;
    height: 10px;
}
.resident-dashboard-skeleton-chip {
    width: 90px;
    height: 28px;
    border-radius: 999px;
}
.resident-dashboard-skeleton-side {
    min-width: 140px;
}
/* ── Responsive ─────────────────────────────────────────────── */
@media (max-width:1180px) {
    .resident-content-grid {
        grid-template-columns: 1fr;
    }
}
@media (max-width:960px) {
    .resident-activity-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    .resident-home-hero-content {
        padding: 10px 0 4px;
    }
}
@media (max-width:768px) {
    .resident-dashboard-shell {
        gap: 18px;
    }
    .resident-home-actions {
        flex-direction: column;
    }
    .resident-home-btn {
        width: 100%;
        min-width: 0;
    }
    .resident-surface-panel {
        padding: 18px 20px;
    }
    .resident-surface-head {
        flex-wrap: wrap;
    }
}
@media (max-width:560px) {
    .resident-activity-grid {
        grid-template-columns: 1fr;
    }
    .resident-home-title {
        font-size: 2.2rem;
    }
    .resident-stack-item-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    .resident-stack-item-side {
        align-items: flex-start;
        flex-direction: row;
        flex-wrap: wrap;
    }
    .resident-community-entry {
        flex-direction: column;
    }
}
@media (max-width:640px) {
    .resident-home-actions {
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: 10px !important;
        margin-top: 18px !important;
    }
    .resident-home-btn {
        width: 100% !important;
        min-width: 0 !important;
        height: 52px !important;
        min-height: 52px !important;
        max-height: 52px !important;
        padding: 0 16px !important;
        border-radius: 16px !important;
        font-size: 0.92rem !important;
        line-height: 1.2 !important;
        justify-content: center !important;
    }
    .resident-home-btn-icon {
        width: 17px !important;
        height: 17px !important;
    }
}
</style>
</x-app-layout>
