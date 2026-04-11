<x-app-layout>
@php
    $recentTickets = $recentTickets ?? collect();
    $announcements = $announcements ?? collect();
    $communityPosts = $communityPosts ?? collect();
    $heroName = Auth::user()->name;
    $activityCards = [
        [
            'label' => 'Active Repairs',
            'value' => $activeTickets ?? 0,
            'note' => ($inProgressTickets ?? 0) . ' urgent issue' . (($inProgressTickets ?? 0) == 1 ? '' : 's') . ' in progress',
            'tone' => 'gold',
            'icon' => 'wrench',
        ],
        [
            'label' => 'Pending Request',
            'value' => $pendingBookings ?? 0,
            'note' => 'Awaiting review',
            'tone' => 'neutral',
            'icon' => 'clipboard',
        ],
        [
            'label' => 'Upcoming Bookings',
            'value' => $upcomingBookingsCount ?? 0,
            'note' => ($nextBookingDate ?? false)
                ? 'Next booking: ' . \Carbon\Carbon::parse($nextBookingDate)->format('M d')
                : 'No upcoming bookings',
            'tone' => 'neutral',
            'icon' => 'calendar',
        ],
    ];
@endphp

<div class="resident-dashboard-shell">
    <section class="resident-home-hero">
        <div class="resident-home-hero-content">
            <p class="resident-home-kicker">Welcome back, {{ $heroName }}</p>
            <h1 class="resident-home-title">Report Maintenance Issue</h1>
            <p class="resident-home-subtitle">How can we help you today?</p>

            <div class="resident-home-actions">
                <a href="{{ route('tickets.create') }}" class="resident-home-btn resident-home-btn-primary">
                    <svg class="resident-home-btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.7 6.3a1 1 0 010 1.4l-1.6 1.6 2.6 2.6 1.6-1.6a1 1 0 011.4 0l1 1a1 1 0 010 1.4l-6.8 6.8a2 2 0 01-1 .55l-3.2.64.64-3.2a2 2 0 01.55-1l6.8-6.8a1 1 0 011.4 0l1 1zM12 7l5 5"></path>
                    </svg>
                    Report Maintenance Issue
                </a>

                <a href="{{ route('bookings.create') }}" class="resident-home-btn resident-home-btn-secondary">
                    <svg class="resident-home-btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Book a Space
                </a>
            </div>
        </div>
    </section>

    <section class="resident-activity">
        <h2 class="resident-section-title">Your Activity</h2>

        <div class="resident-activity-grid">
            @foreach($activityCards as $card)
                <article class="resident-activity-card resident-activity-card-{{ $card['tone'] }}">
                    <div class="resident-activity-card-icon">
                        @if($card['icon'] === 'wrench')
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.7 6.3a1 1 0 010 1.4l-1.6 1.6 2.6 2.6 1.6-1.6a1 1 0 011.4 0l1 1a1 1 0 010 1.4l-6.8 6.8a2 2 0 01-1 .55l-3.2.64.64-3.2a2 2 0 01.55-1l6.8-6.8a1 1 0 011.4 0l1 1zM12 7l5 5"></path>
                            </svg>
                        @elseif($card['icon'] === 'clipboard')
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5h6m-7 3h8m-9 11h10a2 2 0 002-2V7a2 2 0 00-2-2h-1.5a1.5 1.5 0 01-3 0h-3a1.5 1.5 0 01-3 0H7a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        @else
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        @endif
                    </div>

                    <div class="resident-activity-card-copy">
                        <div class="resident-activity-card-top">
                            <strong>{{ $card['value'] }} {{ $card['label'] }}</strong>
                        </div>
                        <p>{{ $card['note'] }}</p>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <section class="resident-content-grid">
        <article class="resident-surface-panel resident-surface-panel-wide">
            <div class="resident-surface-head">
                <div>
                    <h2>Recent Requests</h2>
                    <p>Your latest maintenance submissions</p>
                </div>
                <a href="{{ route('tickets.index') }}">View All</a>
            </div>

            <div class="resident-surface-divider"></div>

            <div class="resident-stack-list">
                @forelse($recentTickets->take(3) as $ticket)
                    <div class="resident-stack-item">
                        <div class="resident-stack-item-icon resident-stack-item-icon-ticket" aria-hidden="true">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M14.7 6.3a1 1 0 010 1.4l-1.6 1.6 2.6 2.6 1.6-1.6a1 1 0 011.4 0l1 1a1 1 0 010 1.4l-6.8 6.8a2 2 0 01-1 .55l-3.2.64.64-3.2a2 2 0 01.55-1l6.8-6.8a1 1 0 011.4 0l1 1zM12 7l5 5"></path>
                            </svg>
                            <span>Request</span>
                        </div>

                        <div class="resident-stack-item-main">
                            <div class="resident-stack-item-row">
                                <div>
                                    <h3>{{ $ticket->title }}</h3>
                                    <p>{{ Str::limit($ticket->description ?? $ticket->content ?? 'No description provided.', 72) }}</p>
                                </div>

                                <div class="resident-stack-item-side">
                                    <span class="resident-status-chip resident-status-chip-{{ $ticket->status }}">
                                        {{ $ticket->status === 'in_progress' ? 'In Progress' : ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                    </span>
                                    <span class="resident-stack-meta">Last updated {{ $ticket->updated_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="resident-empty-state">No recent requests.</div>
                @endforelse
            </div>
        </article>

        <article class="resident-surface-panel resident-surface-panel-narrow">
            <div class="resident-surface-head">
                <div>
                    <h2>Notifications</h2>
                    <p>Important updates from management</p>
                </div>
                <a href="{{ route('announcements.index') }}">View All</a>
            </div>

            <div class="resident-surface-divider"></div>

            <div class="resident-notice-list">
                @forelse($announcements->take(2) as $announcement)
                    <div class="resident-notice-card">
                        <div class="resident-notice-title">
                            <span class="resident-notice-marker">
                                @if($announcement->priority === 'urgent')
                                    !
                                @elseif($announcement->priority === 'important')
                                    i
                                @else
                                    *
                                @endif
                            </span>
                            <h3>{{ $announcement->title }}</h3>
                        </div>
                        <p>{{ Str::limit($announcement->content, 110) }}</p>
                        <span class="resident-stack-meta">{{ $announcement->created_at->diffForHumans() }}</span>
                    </div>
                @empty
                    <div class="resident-empty-state">No notifications yet.</div>
                @endforelse
            </div>
        </article>
    </section>

    <section class="resident-surface-panel">
        <div class="resident-surface-head">
            <div>
                <h2>Community Board</h2>
                <p>Resident conversations, updates, and shared moments around Rexhall</p>
            </div>
            <a href="{{ route('community.index') }}">View All</a>
        </div>

        <div class="resident-surface-divider"></div>

        <div class="resident-community-board">
            @forelse($communityPosts->take(3) as $post)
                <article class="resident-community-entry">
                    <div class="resident-community-entry-main">
                        <h3>{{ $post->title }}</h3>
                        <p>{{ Str::limit($post->content, 180) }}</p>
                        <span class="resident-stack-meta">By {{ $post->user->name ?? 'Resident' }}</span>
                    </div>
                    <div class="resident-community-entry-time">{{ $post->created_at->diffForHumans() }}</div>
                </article>
            @empty
                <div class="resident-empty-state">No community posts yet.</div>
            @endforelse
        </div>
    </section>
</div>

<style>
.resident-dashboard-shell {
    max-width: 1580px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 24px;
    color: #d9cbb8;
}

.resident-home-hero {
    min-height: auto;
    margin-top: -2px;
    background: transparent;
}

.resident-home-hero-content {
    padding: 22px 0 6px;
    max-width: 720px;
}

.resident-home-kicker {
    margin: 0 0 8px;
    color: #e4c9a4;
    font-size: clamp(1.2rem, 2vw, 1.55rem);
    font-weight: 500;
    line-height: 1.2;
}

.resident-home-title {
    margin: 0;
    color: #f5e7d4;
    font-family: 'Playfair Display', serif;
    font-size: clamp(2.7rem, 5vw, 4.6rem);
    line-height: 0.98;
    letter-spacing: -0.03em;
}

.resident-home-subtitle {
    margin: 12px 0 0;
    color: rgba(244,231,212,0.92);
    font-size: clamp(1.05rem, 1.6vw, 1.2rem);
    line-height: 1.55;
}

.resident-home-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    margin-top: 24px;
}

.resident-home-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    min-width: 228px;
    padding: 16px 26px;
    border-radius: 999px;
    text-decoration: none;
    font-size: 0.95rem;
    font-weight: 700;
    transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
}

.resident-home-btn:hover {
    transform: translateY(-2px);
}

.resident-home-btn-icon {
    width: 20px;
    height: 20px;
    flex-shrink: 0;
}

.resident-home-btn-primary {
    color: #fff7ea;
    background: linear-gradient(90deg, #bc7f1c 0%, #d79d3f 100%);
    box-shadow: 0 12px 28px rgba(199, 150, 69, 0.26);
}

.resident-home-btn-secondary {
    color: #f5e7d4;
    background: linear-gradient(135deg, rgba(42,44,48,0.96), rgba(57,48,38,0.92));
    border: 1px solid rgba(214,168,91,0.18);
    box-shadow: 0 12px 28px rgba(0,0,0,0.24);
}

.resident-section-title {
    margin: 0 0 14px;
    color: #f0e4d2;
    font-family: 'Inter', sans-serif;
    font-size: 1.15rem;
    font-weight: 600;
}

.resident-activity-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 16px;
}

.resident-activity-card,
.resident-surface-panel {
    background: rgba(42,44,48,0.78);
    border: 1px solid rgba(214,168,91,0.14);
    border-radius: 20px;
    box-shadow: 0 12px 24px rgba(0,0,0,0.14);
    backdrop-filter: blur(10px);
}

.resident-activity-card {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 20px 22px;
}

.resident-activity-card-icon {
    width: 54px;
    height: 54px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.resident-activity-card-icon svg {
    width: 24px;
    height: 24px;
}

.resident-activity-card-gold .resident-activity-card-icon {
    color: #f3ddba;
    background: linear-gradient(135deg, rgba(190,132,47,0.94), rgba(214,168,91,0.72));
}

.resident-activity-card-neutral .resident-activity-card-icon {
    color: #e4c58e;
    background: rgba(214,168,91,0.12);
    border: 1px solid rgba(214,168,91,0.16);
}

.resident-activity-card-copy {
    min-width: 0;
    flex: 1;
}

.resident-activity-card-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}

.resident-activity-card-top strong {
    color: #f2e6d4;
    font-size: 1rem;
    font-weight: 600;
}

.resident-activity-card-gold .resident-activity-card-top strong {
    color: #e9c17f;
}

.resident-activity-card-copy p {
    margin: 6px 0 0;
    color: #b9aa95;
    font-size: 0.92rem;
}

.resident-content-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.4fr) minmax(300px, 0.6fr);
    gap: 18px;
}

.resident-surface-panel {
    padding: 24px;
}

.resident-surface-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 16px;
}

.resident-surface-head h2 {
    margin: 0;
    color: #F0E9DF;
    font-family: 'Playfair Display', serif;
    font-size: 1.5rem;
    font-weight: 600;
}

.resident-surface-head p {
    margin: 4px 0 0;
    color: #8A7A66;
    font-size: 0.95rem;
}

.resident-surface-head a {
    color: #D6A85B;
    text-decoration: none;
    font-size: 0.82rem;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    white-space: nowrap;
}

.resident-surface-divider {
    height: 1px;
    background: linear-gradient(to right, rgba(214,168,91,0.3), rgba(214,168,91,0.05), transparent);
    margin-bottom: 18px;
}

.resident-stack-list,
.resident-notice-list,
.resident-community-board {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.resident-stack-item,
.resident-notice-card,
.resident-community-entry {
    border-radius: 16px;
    border: 1px solid rgba(255,255,255,0.05);
    background: rgba(255,255,255,0.04);
    box-shadow: none;
}

.resident-stack-item {
    display: flex;
    gap: 16px;
    align-items: center;
    padding: 14px 16px;
}

.resident-stack-item-icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    flex-shrink: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
}

.resident-stack-item-icon-ticket {
    flex-direction: column;
    gap: 3px;
    background: linear-gradient(145deg, rgba(190,132,47,0.22), rgba(96,72,48,0.42));
    border: 1px solid rgba(214,168,91,0.16);
    color: #f1d39c;
    box-shadow: inset 0 1px 0 rgba(255,255,255,0.03);
}

.resident-stack-item-icon-ticket svg {
    width: 18px;
    height: 18px;
}

.resident-stack-item-icon-ticket span {
    font-size: 0.56rem;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    line-height: 1;
}

.resident-stack-item-main {
    min-width: 0;
    flex: 1;
}

.resident-stack-item-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 18px;
}

.resident-stack-item-row h3,
.resident-notice-title h3,
.resident-community-entry h3 {
    margin: 0;
    color: #F0E9DF;
    font-size: 0.98rem;
    font-weight: 700;
}

.resident-stack-item-row p,
.resident-notice-card p,
.resident-community-entry p {
    margin: 4px 0 0;
    color: #B8AB98;
    line-height: 1.65;
    font-size: 0.9rem;
}

.resident-stack-item-side {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 8px;
    flex-shrink: 0;
}

.resident-status-chip {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 7px 14px;
    border-radius: 999px;
    font-size: 0.8rem;
    font-weight: 600;
    border: 1px solid transparent;
}

.resident-status-chip-in_progress {
    color: #f0d18f;
    background: rgba(199,151,69,0.18);
    border-color: rgba(199,151,69,0.16);
}

.resident-status-chip-pending {
    color: #ebd6ab;
    background: rgba(190,147,96,0.18);
    border-color: rgba(190,147,96,0.16);
}

.resident-status-chip-completed {
    color: #d5e3be;
    background: rgba(111,160,111,0.18);
    border-color: rgba(111,160,111,0.16);
}

.resident-status-chip-open,
.resident-status-chip-submitted {
    color: #ddd1be;
    background: rgba(255,255,255,0.07);
    border-color: rgba(255,255,255,0.08);
}

.resident-stack-meta,
.resident-community-entry-time {
    color: #8A7A66;
    font-size: 0.8rem;
}

.resident-notice-card {
    padding: 16px;
}

.resident-notice-title {
    display: flex;
    gap: 10px;
    align-items: flex-start;
}

.resident-notice-marker {
    width: 20px;
    height: 20px;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: rgba(214,168,91,0.14);
    color: #e4c58e;
    font-size: 0.78rem;
    font-weight: 700;
    flex-shrink: 0;
    margin-top: 2px;
}

.resident-community-entry {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    padding: 18px 20px;
}

.resident-community-entry-main {
    min-width: 0;
    flex: 1;
}

.resident-empty-state {
    padding: 28px 20px;
    border-radius: 16px;
    text-align: center;
    color: #B8AB98;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.05);
}

@media (max-width: 1180px) {
    .resident-content-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 960px) {
    .resident-activity-grid {
        grid-template-columns: 1fr;
    }

    .resident-home-hero-content {
        padding: 12px 0 4px;
    }
}

@media (max-width: 768px) {
    .resident-dashboard-shell {
        gap: 20px;
    }

    .resident-home-hero {
        min-height: auto;
        margin-top: 0;
    }

    .resident-home-actions {
        flex-direction: column;
    }

    .resident-home-btn {
        width: 100%;
        min-width: 0;
    }

    .resident-surface-panel,
    .resident-activity-card {
        padding: 20px;
    }

    .resident-surface-head h2 {
        font-size: 1.3rem;
    }

    .resident-surface-head a {
        letter-spacing: 0.08em;
    }

    .resident-stack-item-row,
    .resident-community-entry,
    .resident-surface-head {
        flex-direction: column;
        align-items: flex-start;
    }

    .resident-stack-item {
        align-items: flex-start;
    }

    .resident-stack-item-side {
        align-items: flex-start;
    }
}

@media (max-width: 560px) {
    .resident-home-title {
        font-size: 2.3rem;
        line-height: 1.04;
    }

    .resident-home-kicker {
        font-size: 1.05rem;
    }

    .resident-home-subtitle {
        font-size: 0.98rem;
    }

    .resident-activity-card {
        align-items: flex-start;
    }

    .resident-activity-card-icon,
    .resident-stack-item-icon {
        width: 44px;
        height: 44px;
    }

    .resident-stack-item {
        padding: 14px;
    }

    .resident-stack-item-icon-ticket span {
        display: none;
    }

    .resident-notice-card,
    .resident-community-entry {
        padding: 16px;
    }
}
</style>
</x-app-layout>
