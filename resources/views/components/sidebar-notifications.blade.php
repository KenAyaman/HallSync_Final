@props([
    'collapsible' => false,
    'heading' => 'Notifications',
    'navNotifications' => collect(),
    'navNotificationCount' => 0,
])

@php($notificationCount = $navNotificationCount ?: $navNotifications->count())

@if($collapsible || $navNotifications->isNotEmpty())
    <section class="sidebar-alerts {{ $collapsible ? 'sidebar-alerts-collapsible' : '' }}"
             aria-labelledby="sidebar-alerts-title"
             @if($collapsible) @click.away="notificationsOpen = false" @endif>
        @if($collapsible)
            <button type="button"
                    class="sidebar-alerts-toggle"
                    @click="notificationsOpen = !notificationsOpen"
                    :aria-expanded="notificationsOpen.toString()"
                    aria-controls="sidebar-alerts-list">
                <span class="sidebar-alerts-toggle-main">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path><path d="M13.7 21a2 2 0 0 1-3.4 0"></path></svg>
                    <span id="sidebar-alerts-title">{{ $heading }}</span>
                </span>
                <span class="sidebar-alerts-toggle-end">
                    @if($notificationCount > 0)
                        <strong>{{ $notificationCount }}</strong>
                    @endif
                    <i aria-hidden="true"></i>
                </span>
            </button>
        @else
            <div class="sidebar-alerts-head">
                <span id="sidebar-alerts-title">{{ $heading }}</span>
                <a href="{{ route('notifications.index') }}">View All</a>
            </div>
        @endif
        <div class="sidebar-alerts-list"
             @if($collapsible)
                 id="sidebar-alerts-list"
                 x-show="notificationsOpen"
                 x-transition:enter="sidebar-alerts-enter"
                 x-transition:enter-start="sidebar-alerts-enter-start"
                 x-transition:enter-end="sidebar-alerts-enter-end"
                 x-transition:leave="sidebar-alerts-leave"
                 x-transition:leave-start="sidebar-alerts-leave-start"
                 x-transition:leave-end="sidebar-alerts-leave-end"
                 style="display: none;"
             @endif>
            @forelse($navNotifications->take(3) as $notification)
                <a href="{{ $notification['url'] }}" class="sidebar-alert-item">
                    <strong>{{ $notification['title'] }}</strong>
                    <span>{{ $notification['message'] }}</span>
                    <small>{{ $notification['time'] }}</small>
                </a>
            @empty
                <p class="sidebar-alerts-empty">No unread notifications.</p>
            @endforelse
        </div>
        @if($collapsible)
            <a href="{{ route('notifications.index') }}" class="sidebar-alerts-view-all">View All</a>
        @endif
    </section>
@endif

@once
    <style>
.sidebar-alerts {
    margin: 18px 16px 0;
    padding: 12px;
    border: 1px solid rgba(214, 168, 91, 0.14);
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.035);
}
.sidebar-alerts-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    color: #d6a85b;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0;
    text-transform: uppercase;
}
.sidebar-alerts-head a, .sidebar-alerts-view-all {
    color: #d6a85b;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-decoration: none;
    text-transform: uppercase;
}
.sidebar-alerts-list {
    display: grid;
    gap: 4px;
    margin-top: 10px;
}
.sidebar-alert-item {
    display: grid;
    gap: 3px;
    padding: 9px;
    border-radius: 6px;
    color: #f5f0e9;
    text-decoration: none;
    transition: background 0.18s ease;
}
.sidebar-alert-item:hover {
    background: rgba(255, 255, 255, 0.06);
}
.sidebar-alert-item strong {
    font-size: 12px;
}
.sidebar-alert-item span {
    overflow: hidden;
    color: #c4bcb2;
    font-size: 12px;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.sidebar-alert-item small {
    color: #9b8f80;
    font-size: 11px;
}
.sidebar-alerts-empty {
    margin: 0;
    padding: 12px 8px 10px;
    color: #a99d8f;
    font-size: 12px;
}
.sidebar-alerts-collapsible {
    flex-shrink: 0;
    padding: 0;
    overflow: hidden;
    background: rgba(255, 255, 255, 0.025);
}
.sidebar-alerts-toggle {
    display: flex;
    width: 100%;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding: 11px 12px;
    border: 0;
    background: transparent;
    color: #d6a85b;
    cursor: pointer;
    font-family: inherit;
}
.sidebar-alerts-toggle:hover {
    background: rgba(255, 255, 255, 0.045);
}
.sidebar-alerts-toggle-main, .sidebar-alerts-toggle-end {
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.sidebar-alerts-toggle-main {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
}
.sidebar-alerts-toggle-main svg {
    width: 16px;
    height: 16px;
    fill: none;
    stroke: currentColor;
    stroke-linecap: round;
    stroke-linejoin: round;
    stroke-width: 1.8;
}
.sidebar-alerts-toggle-end strong {
    display: inline-flex;
    min-width: 22px;
    height: 22px;
    align-items: center;
    justify-content: center;
    padding: 0 6px;
    border: 1px solid rgba(255, 255, 255, 0.22);
    border-radius: 999px;
    background: #dc2626;
    color: #ffffff;
    font-size: 11px;
    box-shadow: 0 4px 10px rgba(127, 29, 29, 0.28);
}
.sidebar-alerts-toggle-end i {
    width: 7px;
    height: 7px;
    border-right: 1.5px solid currentColor;
    border-bottom: 1.5px solid currentColor;
    transform: translateY(-2px) rotate(45deg);
    transition: transform 0.18s ease;
}
.sidebar-alerts-toggle[aria-expanded="true"] .sidebar-alerts-toggle-end i {
    transform: translateY(2px) rotate(225deg);
}
.sidebar-alerts-collapsible .sidebar-alerts-list {
    max-height: 188px;
    gap: 2px;
    margin: 0;
    padding: 2px 4px 6px;
    overflow-y: auto;
    border-top: 1px solid rgba(214, 168, 91, 0.10);
}
.sidebar-alerts-collapsible .sidebar-alert-item {
    padding: 8px;
}
.sidebar-alerts-collapsible .sidebar-alerts-list::-webkit-scrollbar {
    width: 3px;
}
.sidebar-alerts-collapsible .sidebar-alerts-list::-webkit-scrollbar-thumb {
    border-radius: 999px;
    background: rgba(214, 168, 91, 0.28);
}
.sidebar-alerts-view-all {
    display: flex;
    justify-content: center;
    padding: 9px 10px 10px;
    border-top: 1px solid rgba(214, 168, 91, 0.10);
}
.sidebar-alerts-view-all:hover, .sidebar-alerts-head a:hover {
    color: #f5dfb8;
}
.sidebar-alerts-enter, .sidebar-alerts-leave {
    transition: opacity 0.16s ease, transform 0.16s ease;
}
.sidebar-alerts-enter-start, .sidebar-alerts-leave-end {
    opacity: 0;
    transform: translateY(-4px);
}
.sidebar-alerts-enter-end, .sidebar-alerts-leave-start {
    opacity: 1;
    transform: translateY(0);
}
</style>
@endonce
