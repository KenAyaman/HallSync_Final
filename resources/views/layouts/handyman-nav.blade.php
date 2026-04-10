<div x-data="{ open: false }" class="staff-nav-wrapper" x-cloak>
    <button @click="open = !open"
        class="staff-burger-btn"
        :class="{ 'staff-burger-active': open }"
        aria-label="Toggle navigation">
        <span class="staff-burger-line"></span>
        <span class="staff-burger-line"></span>
        <span class="staff-burger-line"></span>
    </button>

    <div x-show="open"
         @click="open = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="staff-overlay"
         style="display: none;">
    </div>

    <aside class="staff-sidebar"
           :class="{ 'staff-sidebar-open': open }"
           x-transition:enter="transition ease-out duration-300"
           x-transition:enter-start="transform -translate-x-full"
           x-transition:enter-end="transform translate-x-0"
           x-transition:leave="transition ease-in duration-200"
           x-transition:leave-start="transform translate-x-0"
           x-transition:leave-end="transform -translate-x-full">

        <div class="staff-sidebar-header">
            <div class="staff-brand-name">Hall<span>Sync</span></div>
            <div class="staff-brand-tagline">Staff Operations Desk</div>
            <div class="staff-role-chip">
                <span class="staff-role-dot"></span>
                Staff
            </div>
        </div>

        <div class="staff-nav-section-label">Staff Workspace</div>

        <nav class="staff-sidebar-nav">
            <a href="{{ route('staff.overview') }}" class="staff-nav-item {{ request()->routeIs('staff.overview') ? 'staff-nav-active' : '' }}">
                <div class="staff-nav-icon-wrap">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                        <rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/>
                    </svg>
                </div>
                <span class="staff-nav-label">Overview</span>
            </a>

            <a href="{{ route('staff.queue') }}" class="staff-nav-item {{ request()->routeIs('staff.queue') || request()->routeIs('tickets.show') || request()->routeIs('tickets.index') ? 'staff-nav-active' : '' }}">
                <div class="staff-nav-icon-wrap">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/>
                        <path d="M13 5v2"/><path d="M13 17v2"/><path d="M13 11v2"/>
                    </svg>
                </div>
                <span class="staff-nav-label">Work Queue</span>
            </a>

            <a href="{{ route('staff.completed') }}" class="staff-nav-item {{ request()->routeIs('staff.completed') ? 'staff-nav-active' : '' }}">
                <div class="staff-nav-icon-wrap">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                </div>
                <span class="staff-nav-label">Recently Completed</span>
            </a>
        </nav>

        <div class="staff-sidebar-footer">
            <a href="{{ route('profile.edit') }}" class="staff-user-card">
                <span class="staff-user-avatar">
                    @if(Auth::user()->profile_photo_url)
                        <img src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}">
                    @else
                        {{ substr(Auth::user()->name, 0, 1) }}
                    @endif
                </span>
                <span class="staff-user-meta">
                    <span class="staff-user-name">{{ Auth::user()->name }}</span>
                    <span class="staff-user-role">Staff profile</span>
                </span>
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="staff-logout-btn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                    Sign Out
                </button>
            </form>
        </div>
    </aside>
</div>

<style>
[x-cloak] { display: none !important; }
.staff-nav-wrapper { position: relative; z-index: 1000; }
.staff-burger-btn { position: fixed; top: 20px; left: 24px; z-index: 200; width: 44px; height: 44px; background: transparent; border: none; border-radius: 14px; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 5px; cursor: pointer; transition: all 0.2s ease; }
.staff-burger-btn:hover, .staff-burger-active { background: rgba(12,16,19,0.88); backdrop-filter: blur(12px); border: 1px solid rgba(88,135,165,0.28); }
.staff-burger-line { display: block; width: 18px; height: 2px; background: #d6a85b; border-radius: 10px; transition: all 0.28s cubic-bezier(0.4,0,0.2,1); transform-origin: center; }
.staff-burger-active .staff-burger-line:nth-child(1) { transform: translateY(7px) rotate(45deg); }
.staff-burger-active .staff-burger-line:nth-child(2) { opacity: 0; transform: scaleX(0); }
.staff-burger-active .staff-burger-line:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }
.staff-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.58); backdrop-filter: blur(6px); z-index: 150; }
.staff-sidebar { position: fixed; top: 0; left: 0; width: 292px; height: 100vh; background: linear-gradient(180deg, #0d1318 0%, #10181d 42%, #0e1419 100%); border-right: 1px solid rgba(214,168,91,0.12); display: flex; flex-direction: column; z-index: 160; transform: translateX(-100%); overflow-y: auto; }
.staff-sidebar-open { transform: translateX(0) !important; box-shadow: 14px 0 44px rgba(0,0,0,0.46); }
.staff-sidebar::before { content: ''; position: absolute; top: -90px; right: -40px; width: 220px; height: 220px; background: radial-gradient(circle, rgba(88,135,165,0.14) 0%, transparent 70%); pointer-events: none; }
.staff-sidebar-header { padding: 32px 24px 24px 80px; border-bottom: 1px solid rgba(214,168,91,0.12); }
.staff-brand-name { font-family: 'Playfair Display', Georgia, serif; font-size: 24px; font-weight: 700; color: #f5f0e9; }
.staff-brand-name span { color: #d6a85b; }
.staff-brand-tagline { margin-top: 4px; font-size: 11px; letter-spacing: 0.24em; text-transform: uppercase; color: #9fb1bd; font-weight: 700; }
.staff-role-chip { margin-top: 18px; display: inline-flex; align-items: center; gap: 8px; padding: 10px 14px; border-radius: 999px; background: rgba(214,168,91,0.08); border: 1px solid rgba(214,168,91,0.14); color: #d6a85b; font-size: 12px; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; }
.staff-role-dot { width: 6px; height: 6px; border-radius: 999px; background: #d6a85b; }
.staff-nav-section-label { padding: 22px 24px 12px; color: #738592; font-size: 11px; font-weight: 700; letter-spacing: 0.18em; text-transform: uppercase; }
.staff-sidebar-nav { display: flex; flex-direction: column; gap: 6px; padding: 0 16px; }
.staff-nav-item { display: flex; align-items: center; gap: 14px; padding: 14px 16px; border-radius: 18px; color: #d7d0c4; text-decoration: none; transition: 0.2s ease; }
.staff-nav-item:hover { background: rgba(255,255,255,0.04); color: #fff6e7; }
.staff-nav-active { background: linear-gradient(135deg, rgba(214,168,91,0.16) 0%, rgba(214,168,91,0.06) 100%); border: 1px solid rgba(214,168,91,0.16); color: #fff2dc; }
.staff-nav-icon-wrap { width: 40px; height: 40px; border-radius: 14px; display: inline-flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.04); color: #bfd5e3; flex-shrink: 0; }
.staff-nav-active .staff-nav-icon-wrap { background: rgba(214,168,91,0.14); color: #d6a85b; }
.staff-nav-label { font-size: 0.95rem; font-weight: 600; }
.staff-sidebar-footer { margin-top: auto; padding: 18px 16px 22px; border-top: 1px solid rgba(214,168,91,0.1); }
.staff-user-card { display: flex; align-items: center; gap: 12px; padding: 14px; border-radius: 18px; text-decoration: none; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); color: #f5f0e9; }
.staff-user-avatar { width: 44px; height: 44px; border-radius: 999px; overflow: hidden; display: inline-flex; align-items: center; justify-content: center; background: rgba(214,168,91,0.14); color: #f3e5cf; font-weight: 700; flex-shrink: 0; }
.staff-user-avatar img { width: 100%; height: 100%; object-fit: cover; }
.staff-user-meta { display: flex; flex-direction: column; min-width: 0; }
.staff-user-name { color: #f5f0e9; font-weight: 700; }
.staff-user-role { margin-top: 2px; color: #91a0aa; font-size: 0.82rem; }
.staff-logout-btn { width: 100%; margin-top: 12px; display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 12px 16px; border-radius: 16px; border: 1px solid rgba(255,255,255,0.08); background: rgba(255,255,255,0.04); color: #f1eadf; font-weight: 700; cursor: pointer; }
.staff-logout-btn:hover { background: rgba(255,255,255,0.08); }
</style>
