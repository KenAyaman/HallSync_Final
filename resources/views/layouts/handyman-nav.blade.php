<div x-data="{ open: false }" class="role-topbar-wrap">
    @php
        $navLinks = [
            [
                'label' => 'Work Queue',
                'route' => 'dashboard',
                'active' => request()->routeIs('dashboard') || request()->routeIs('tickets.show') || request()->routeIs('tickets.index'),
            ],
        ];
    @endphp

    <nav class="role-topbar">
        <a href="{{ route('dashboard') }}" class="role-brand">
            <span class="role-brand-copy">
                <span class="role-brand-title">Hall<span>Sync</span></span>
                <span class="role-brand-subtitle">Maintenance Operations</span>
            </span>
        </a>

        <div class="role-nav-shell">
            @foreach($navLinks as $link)
                <a href="{{ route($link['route']) }}"
                   class="role-nav-link {{ $link['active'] ? 'is-active' : '' }}">
                    {{ $link['label'] }}
                </a>
            @endforeach
        </div>

        <div class="role-topbar-actions auth-only-desktop">
            <a href="{{ route('profile.edit') }}" class="role-user-chip" aria-label="Open profile">
                <span class="role-user-avatar">
                    @if(Auth::user()->profile_photo_url)
                        <img src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}">
                    @else
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.75 6.75a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0ZM4.5 20.118a7.5 7.5 0 0115 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.5-1.632Z" />
                        </svg>
                    @endif
                </span>
                <span class="role-user-tooltip">{{ Auth::user()->name }} - Staff</span>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="role-action-btn">Log out</button>
            </form>
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
               class="role-nav-link {{ $link['active'] ? 'is-active' : '' }}">
                {{ $link['label'] }}
            </a>
        @endforeach

        <a href="{{ route('profile.edit') }}" class="role-action-btn">Profile</a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="role-action-btn">Log out</button>
        </form>
    </div>
</div>
