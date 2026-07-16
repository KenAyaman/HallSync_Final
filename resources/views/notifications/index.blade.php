<x-app-layout>
@php
    $roleLabel = Auth::user()?->role_label ?? 'Account';
@endphp

<section class="notifications-page">
    <header class="notifications-hero">
        <div>
            <p class="notifications-kicker">{{ $roleLabel }} Notifications</p>
            <h1>Notifications</h1>
            <p>Review account updates, assignments, approvals, and items that need your attention.</p>
        </div>
        <div class="notifications-summary" aria-label="Notification summary">
            <strong>{{ $notifications->count() }}</strong>
            <span>{{ $unreadCount }} unread</span>
        </div>
    </header>

    <div class="notifications-panel">
        <div class="notifications-panel-head">
            <div>
                <h2>All Updates</h2>
                <p>Newest notifications appear first.</p>
            </div>
            <a href="{{ route('dashboard') }}" class="notifications-back-link">
                <span aria-hidden="true">←</span>
                <span>Back</span>
            </a>
        </div>

        <div class="notifications-list">
            @forelse($notifications as $notification)
                <a href="{{ $notification['url'] }}"
                   class="notifications-card {{ empty($notification['is_read']) ? 'is-unread' : 'is-read' }}">
                    <span class="notifications-dot" aria-hidden="true"></span>
                    <span class="notifications-card-main">
                        <span class="notifications-card-top">
                            <strong>{{ $notification['message'] }}</strong>
                            <small>{{ $notification['time'] }}</small>
                        </span>
                        <span class="notifications-card-bottom">
                            <span class="notifications-type">{{ $notification['title'] }}</span>
                            <span class="notifications-state">{{ empty($notification['is_read']) ? 'Unread' : 'Read' }}</span>
                        </span>
                    </span>
                    <span class="notifications-open">Open</span>
                </a>
            @empty
        <div class="notifications-empty hs-card" role="status" aria-live="polite">
                    <strong>No notifications yet</strong>
                    <p>New updates will appear here once there is activity for your account.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<style>
.notifications-page {
    width: min(1120px, 100%);
    margin: 0 auto;
    display: grid;
    gap: 22px;
}
.notifications-hero, .notifications-panel {
    border: 1px solid rgba(214, 168, 91, 0.16);
    border-radius: 18px;
    background: rgba(35, 34, 32, 0.88);
    box-shadow: 0 18px 38px rgba(0, 0, 0, 0.18);
}
.notifications-hero {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 22px;
    padding: 30px;
}
.notifications-kicker {
    margin: 0 0 8px;
    color: #D6A85B;
    font-size: 0.78rem;
    font-weight: 800;
    letter-spacing: 0.16em;
    text-transform: uppercase;
}
.notifications-hero h1 {
    margin: 0;
    color: #F8F3EA;
    font-family: 'Playfair Display', Georgia, serif;
    font-size: clamp(2.15rem, 5vw, 3.4rem);
    line-height: 1.05;
}
.notifications-hero p, .notifications-panel-head p, .notifications-empty p {
    margin: 8px 0 0;
    color: #AFA18F;
    line-height: 1.6;
}
.notifications-summary {
    min-width: 130px;
    padding: 18px;
    border-radius: 14px;
    background: rgba(214, 168, 91, 0.10);
    border: 1px solid rgba(214, 168, 91, 0.18);
    text-align: center;
}
.notifications-summary strong {
    display: block;
    color: #F8F3EA;
    font-size: 2.2rem;
    line-height: 1;
}
.notifications-summary span {
    display: block;
    margin-top: 6px;
    color: #D6A85B;
    font-size: 0.78rem;
    font-weight: 800;
    text-transform: uppercase;
}
.notifications-panel {
    padding: 22px;
}
.notifications-panel-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    padding-bottom: 16px;
    border-bottom: 1px solid rgba(214, 168, 91, 0.12);
}
.notifications-panel-head h2 {
    margin: 0;
    color: #F8F3EA;
    font-size: 1.12rem;
}
.notifications-open {
    color: #D6A85B;
    font-size: 0.78rem;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-decoration: none;
    text-transform: uppercase;
}
.notifications-back-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    min-height: 44px;
    padding: 0 16px;
    border-radius: 999px;
    border: 1px solid rgba(214, 168, 91, 0.22);
    background: rgba(214, 168, 91, 0.08);
    color: #F8F3EA;
    font-size: 0.88rem;
    font-weight: 800;
    line-height: 1;
    text-decoration: none;
    transition: background 0.18s ease, border-color 0.18s ease, transform 0.18s ease;
    white-space: nowrap;
}
.notifications-back-link:hover {
    transform: translateY(-1px);
    border-color: rgba(214, 168, 91, 0.36);
    background: rgba(214, 168, 91, 0.14);
}
.notifications-back-link span[aria-hidden="true"] {
    color: #F4D99E;
    font-size: 1rem;
    line-height: 1;
}
.notifications-list {
    display: grid;
    gap: 10px;
    margin-top: 16px;
}
.notifications-card {
    display: grid;
    grid-template-columns: auto minmax(0, 1fr) auto;
    align-items: center;
    gap: 14px;
    padding: 16px;
    border-radius: 14px;
    background: rgba(255, 255, 255, 0.035);
    border: 1px solid rgba(255, 255, 255, 0.07);
    color: #F8F3EA;
    text-decoration: none;
    transition: transform 0.18s ease, border-color 0.18s ease, background 0.18s ease;
}
.notifications-card:hover {
    transform: translateY(-1px);
    border-color: rgba(214, 168, 91, 0.24);
    background: rgba(255, 255, 255, 0.055);
}
.notifications-card.is-unread {
    border-color: rgba(214, 168, 91, 0.24);
}
.notifications-dot {
    width: 10px;
    height: 10px;
    border-radius: 999px;
    background: rgba(214, 168, 91, 0.72);
}
.notifications-card.is-read .notifications-dot {
    background: rgba(160, 151, 139, 0.42);
}
.notifications-card-main {
    min-width: 0;
    display: grid;
    gap: 8px;
}
.notifications-card-top {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 16px;
}
.notifications-card-top strong {
    overflow: hidden;
    color: #F8F3EA;
    font-size: 0.98rem;
    line-height: 1.35;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.notifications-card-top small {
    flex: 0 0 auto;
    color: #AFA18F;
    font-size: 0.8rem;
}
.notifications-card-bottom {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}
.notifications-type, .notifications-state {
    display: inline-flex;
    align-items: center;
    min-height: 26px;
    padding: 5px 10px;
    border-radius: 999px;
    font-size: 0.72rem;
    font-weight: 800;
    text-transform: uppercase;
}
.notifications-type {
    color: #F4D99E;
    background: rgba(214, 168, 91, 0.13);
    border: 1px solid rgba(214, 168, 91, 0.18);
}
.notifications-state {
    color: #B8AB98;
    background: rgba(255, 255, 255, 0.045);
    border: 1px solid rgba(255, 255, 255, 0.07);
}
.notifications-card.is-unread .notifications-state {
    color: #F8F3EA;
}
.notifications-empty {
    padding: 34px 18px;
    border: 1px dashed rgba(214, 168, 91, 0.18);
    border-radius: 14px;
    text-align: center;
}
.notifications-empty strong {
    color: #F8F3EA;
}
@media (max-width:700px) {
    .notifications-hero, .notifications-panel-head, .notifications-card-top {
        flex-direction: column;
        align-items: flex-start;
    }
    .notifications-card {
        grid-template-columns: auto minmax(0, 1fr);
    }
    .notifications-open {
        grid-column: 2;
    }
    .notifications-card-top strong {
        white-space: normal;
    }
}
</style>
</x-app-layout>
