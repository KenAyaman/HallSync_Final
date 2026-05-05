<div x-data="{ open: false, notificationsOpen: false }" class="role-topbar-wrap">
    @php
        $navLinks = [
            ['label' => 'Home', 'route' => 'dashboard', 'match' => 'dashboard'],
            ['label' => 'Maintenance', 'route' => 'tickets.index', 'match' => 'tickets.*'],
            ['label' => 'Book Spaces', 'route' => 'bookings.index', 'match' => 'bookings.*'],
            ['label' => 'Announcements', 'route' => 'announcements.index', 'match' => 'announcements.*'],
            ['label' => 'Community', 'route' => 'community.index', 'match' => 'community.*'],
            ['label' => 'Concerns', 'route' => 'concerns.index', 'match' => 'concerns.*'],
        ];
    @endphp

    <nav class="role-topbar">
        <a href="{{ route('dashboard') }}" class="role-brand">
            <span class="role-brand-copy">
                <span class="role-brand-title">Hall<span>Sync</span></span>
                <span class="role-brand-subtitle">Resident Portal</span>
            </span>
        </a>

        <div class="role-nav-shell">
            @foreach($navLinks as $link)
                <a href="{{ route($link['route']) }}"
                   class="role-nav-link {{ request()->routeIs($link['match']) ? 'is-active' : '' }}">
                    {{ $link['label'] }}
                </a>
            @endforeach
        </div>

        <div class="role-topbar-actions auth-only-desktop">
            @guest
                <a href="{{ route('login') }}" class="role-action-btn">Log in</a>
                <a href="{{ route('register') }}" class="role-action-btn primary">Sign Up</a>
            @endguest

            @auth
                <div class="role-notification-wrap" @click.away="notificationsOpen = false">
                    <button type="button"
                            class="role-notification-btn"
                            @click="notificationsOpen = !notificationsOpen"
                            aria-label="Open notifications">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.4-1.4a2 2 0 01-.6-1.4V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0a3 3 0 11-6 0m6 0H9"></path>
                        </svg>
                        @if(($navNotificationCount ?? 0) > 0)
                            <span class="role-notification-count">{{ $navNotificationCount }}</span>
                        @endif
                    </button>

                    <div x-show="notificationsOpen"
                         x-transition
                         class="role-notification-panel"
                         style="display: none;">
                        <div class="role-notification-head">
                            <strong>Notifications</strong>
                            <span>{{ $navNotificationCount ?? 0 }} item{{ ($navNotificationCount ?? 0) === 1 ? '' : 's' }}</span>
                        </div>

                        <div class="role-notification-list">
                            @forelse(($navNotifications ?? collect()) as $notification)
                                <a href="{{ $notification['url'] }}" class="role-notification-item" @click="notificationsOpen = false">
                                    <span class="role-notification-label">{{ $notification['title'] }}</span>
                                    <strong>{{ $notification['message'] }}</strong>
                                    <small>{{ $notification['time'] }}</small>
                                </a>
                            @empty
                                <div class="role-notification-empty">No new notifications right now.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <a href="{{ route('profile.edit') }}" class="role-user-chip" aria-label="Open profile">
                    <span class="role-user-avatar" aria-hidden="true">
                        @if (Auth::user()->profile_photo_url)
                            <img src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}">
                        @else
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M15.75 6.75a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 19.5a7.5 7.5 0 0115 0"></path>
                            </svg>
                        @endif
                    </span>
                    <span class="role-user-tooltip">
                        {{ Auth::user()->name }} | {{ ucfirst(Auth::user()->role) }}
                    </span>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="role-action-btn">Log out</button>
                </form>
            @endauth
        </div>

        <button @click="open = !open" class="role-mobile-toggle" aria-label="Toggle navigation">
            <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path :class="{'hidden': open, 'inline-flex': !open}" class="inline-flex"
                      stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 6h16M4 12h16M4 18h16"/>
                <path :class="{'hidden': !open, 'inline-flex': open}" class="hidden"
                      stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </nav>

    <div x-show="open" x-transition class="role-mobile-panel" style="display:none;">
        @foreach($navLinks as $link)
            <a href="{{ route($link['route']) }}"
               class="role-nav-link {{ request()->routeIs($link['match']) ? 'is-active' : '' }}">
                {{ $link['label'] }}
            </a>
        @endforeach

        @guest
            <a href="{{ route('login') }}" class="role-action-btn">Log in</a>
            <a href="{{ route('register') }}" class="role-action-btn primary">Sign Up</a>
        @endguest

        @auth
            <div class="role-mobile-notification-block">
                <div class="role-mobile-notification-head">
                    <strong>Notifications</strong>
                    <span>{{ $navNotificationCount ?? 0 }}</span>
                </div>
                @forelse(($navNotifications ?? collect())->take(3) as $notification)
                    <a href="{{ $notification['url'] }}" class="role-mobile-notification-item">
                        <strong>{{ $notification['title'] }}</strong>
                        <span>{{ $notification['message'] }}</span>
                    </a>
                @empty
                    <div class="role-mobile-notification-empty">No new notifications.</div>
                @endforelse
            </div>

            <a href="{{ route('profile.edit') }}" class="role-action-btn">{{ Auth::user()->name }}</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="role-action-btn">Log out</button>
            </form>
        @endauth
    </div>
</div>
