{{-- admin-nav.blade.php - Premium Admin Sidebar with Minimal Burger --}}
<div x-data="{ open: false }" class="admin-nav-wrapper" x-cloak>

    {{-- Minimal Burger Button (no brown box, just gold lines) --}}
    <button @click="open = !open"
        class="admin-burger-btn"
        :class="{ 'burger-active': open }"
        aria-label="Toggle navigation">
        <span class="burger-line"></span>
        <span class="burger-line"></span>
        <span class="burger-line"></span>
    </button>

    {{-- Blurry Backdrop Overlay --}}
    <div x-show="open"
         @click="open = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="admin-overlay"
         style="display: none;">
    </div>

    {{-- Premium Sidebar (slides OVER content, never pushes) --}}
    <aside class="admin-sidebar"
           :class="{ 'sidebar-open': open }"
           x-transition:enter="transition ease-out duration-300"
           x-transition:enter-start="transform -translate-x-full"
           x-transition:enter-end="transform translate-x-0"
           x-transition:leave="transition ease-in duration-200"
           x-transition:leave-start="transform translate-x-0"
           x-transition:leave-end="transform -translate-x-full">

        {{-- Sidebar Header with Brand --}}
        <div class="sidebar-header">
            <div class="brand-lockup">
                <div>
                    <div class="brand-name">Hall<span>Sync</span></div>
                    <div class="brand-tagline">Facility Management</div>
                </div>
            </div>
            <div class="role-chip">
                <span class="role-dot"></span>
                Administrator
            </div>
        </div>

        {{-- Navigation Section Label --}}
        <div class="nav-section-label">Main Navigation</div>

        {{-- Navigation Links --}}
        <nav class="sidebar-nav">
            {{-- Oversight Dashboard --}}
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'nav-active' : '' }}">
                <div class="nav-icon-wrap">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                        <rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/>
                    </svg>
                </div>
                <span class="nav-label">Oversight</span>
            </a>

            {{-- Tickets --}}
            <a href="{{ route('tickets.index') }}" class="nav-item {{ request()->routeIs('tickets.*') ? 'nav-active' : '' }}">
                <div class="nav-icon-wrap">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/>
                        <path d="M13 5v2"/><path d="M13 17v2"/><path d="M13 11v2"/>
                    </svg>
                </div>
                <span class="nav-label">Tickets</span>
                @if(isset($openTicketsCount) && $openTicketsCount > 0)
                    <span class="nav-badge">{{ $openTicketsCount }}</span>
                @endif
            </a>

            {{-- Bookings --}}
            <a href="{{ route('bookings.index') }}" class="nav-item {{ request()->routeIs('bookings.*') || request()->routeIs('admin.bookings.*') ? 'nav-active' : '' }}">
                <div class="nav-icon-wrap">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                        <path d="m9 16 2 2 4-4"/>
                    </svg>
                </div>
                <span class="nav-label">Bookings</span>
                @if(isset($pendingBookingsCount) && $pendingBookingsCount > 0)
                    <span class="nav-badge">{{ $pendingBookingsCount }}</span>
                @endif
            </a>

            {{-- Announcements --}}
            <a href="{{ route('announcements.index') }}" class="nav-item {{ request()->routeIs('announcements.*') ? 'nav-active' : '' }}">
                <div class="nav-icon-wrap">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m3 11 19-9-9 19-2-8-8-2z"/>
                    </svg>
                </div>
                <span class="nav-label">Announcements</span>
            </a>

            {{-- Community --}}
            <a href="{{ route('community.index') }}" class="nav-item {{ request()->routeIs('community.*') ? 'nav-active' : '' }}">
                <div class="nav-icon-wrap">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <span class="nav-label">Community</span>
            </a>
        </nav>

        {{-- Sidebar Footer with User Info & Logout --}}
        <div class="sidebar-footer">
            <div class="user-card">
                <div class="user-avatar">{{ substr(Auth::user()->name, 0, 1) }}</div>
                <div class="user-meta">
                    <span class="user-name">{{ Auth::user()->name }}</span>
                    <span class="user-role">Administrator</span>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
/* ──────────────────────────────────────────────────────────────
   PREMIUM ADMIN SIDEBAR STYLES
   ────────────────────────────────────────────────────────────── */

/* ── CSS Variables ─────────────────────────────────────────── */
:root {
    --sidebar-bg:       #0D0C08;
    --sidebar-border:   rgba(214,168,91,0.12);
    --sidebar-width:    280px;
    --gold:             #D6A85B;
    --gold-dim:         rgba(214,168,91,0.12);
    --gold-faint:       rgba(214,168,91,0.05);
    --gold-glow:        rgba(214,168,91,0.08);
    --text-primary:     #F5F0E9;
    --text-secondary:   #C4BCB2;
    --text-muted:       #7A7268;
    --text-soft:        #A8A094;
    --accent-red:       #E07060;
    --accent-green:     #5A8A5A;
    --transition:       0.28s cubic-bezier(0.4, 0, 0.2, 1);
}

/* ── x-cloak (prevent flash) ───────────────────────────────── */
[x-cloak] { display: none !important; }

/* ── Wrapper ───────────────────────────────────────────────── */
.admin-nav-wrapper {
    position: relative;
    z-index: 1000;
}

/* ──────────────────────────────────────────────────────────────
   MINIMAL BURGER BUTTON (No brown box, just gold lines)
   ────────────────────────────────────────────────────────────── */
.admin-burger-btn {
    position: fixed;
    top: 20px;
    left: 24px;
    z-index: 200;
    width: 44px;
    height: 44px;
    
    /* No background by default */
    background: transparent;
    backdrop-filter: none;
    border: none;
    
    border-radius: 14px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 5px;
    cursor: pointer;
    padding: 0;
    transition: all 0.2s ease;
}

/* Subtle background appears only on hover */
.admin-burger-btn:hover {
    background: rgba(13, 12, 8, 0.8);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(214,168,91,0.25);
}

/* The three gold lines */
.burger-line {
    display: block;
    width: 18px;
    height: 2px;
    background: var(--gold);
    border-radius: 10px;
    transition: all 0.28s cubic-bezier(0.4, 0, 0.2, 1);
    transform-origin: center;
}

/* Transform burger into X when open */
.burger-active .burger-line:nth-child(1) { 
    transform: translateY(7px) rotate(45deg); 
}
.burger-active .burger-line:nth-child(2) { 
    opacity: 0; 
    transform: scaleX(0); 
}
.burger-active .burger-line:nth-child(3) { 
    transform: translateY(-7px) rotate(-45deg); 
}

/* Keep consistent background when open */
.burger-active {
    background: rgba(13, 12, 8, 0.85);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(214,168,91,0.3);
}

/* ──────────────────────────────────────────────────────────────
   BLURRY BACKDROP OVERLAY
   ────────────────────────────────────────────────────────────── */
.admin-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.55);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    z-index: 150;
    cursor: pointer;
}

/* ──────────────────────────────────────────────────────────────
   PREMIUM SIDEBAR
   ────────────────────────────────────────────────────────────── */
.admin-sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: var(--sidebar-width);
    height: 100vh;
    background: var(--sidebar-bg);
    border-right: 1px solid var(--sidebar-border);
    display: flex;
    flex-direction: column;
    z-index: 160;
    transform: translateX(-100%);
    transition: transform var(--transition);
    overflow-y: auto;
    overflow-x: hidden;
    box-shadow: none;
}

/* Sidebar open state */
.sidebar-open {
    transform: translateX(0) !important;
    box-shadow: 12px 0 40px rgba(0, 0, 0, 0.5);
}

/* Custom scrollbar */
.admin-sidebar::-webkit-scrollbar {
    width: 4px;
}
.admin-sidebar::-webkit-scrollbar-track {
    background: rgba(255,255,255,0.03);
}
.admin-sidebar::-webkit-scrollbar-thumb {
    background: var(--gold-dim);
    border-radius: 4px;
}

/* Subtle ambient glow effect */
.admin-sidebar::before {
    content: '';
    position: absolute;
    top: -100px;
    right: -50px;
    width: 200px;
    height: 200px;
    background: radial-gradient(circle, rgba(214,168,91,0.06) 0%, transparent 70%);
    pointer-events: none;
}

/* ── Sidebar Header ───────────────────────────────────────── */
.sidebar-header {
    padding: 32px 24px 24px 80px;
    border-bottom: 1px solid var(--sidebar-border);
}

.brand-lockup {
    display: flex;
    align-items: center;
    gap: 0;
    margin-bottom: 20px;
}

.brand-name {
    font-family: 'Playfair Display', Georgia, 'Times New Roman', serif;
    font-size: 24px;
    font-weight: 700;
    color: var(--text-primary);
    line-height: 1;
    letter-spacing: -0.3px;
}

.brand-name span {
    color: var(--gold);
}

.brand-tagline {
    font-size: 9px;
    color: var(--text-muted);
    letter-spacing: 0.1em;
    margin-top: 4px;
    text-transform: uppercase;
    font-weight: 500;
}

.role-chip {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 10px;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--gold);
    background: var(--gold-dim);
    padding: 6px 14px;
    border-radius: 40px;
    border: 1px solid rgba(214,168,91,0.2);
    font-weight: 600;
}

.role-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: var(--gold);
    animation: pulse-dot 2.4s ease-in-out infinite;
}

@keyframes pulse-dot {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.4; transform: scale(0.7); }
}

/* ── Navigation Section Label ─────────────────────────────── */
.nav-section-label {
    font-size: 9px;
    letter-spacing: 0.16em;
    text-transform: uppercase;
    color: var(--text-muted);
    font-weight: 700;
    padding: 24px 24px 10px;
}

/* ── Navigation Items ─────────────────────────────────────── */
.sidebar-nav {
    display: flex;
    flex-direction: column;
    gap: 4px;
    padding: 0 14px;
    flex: 1;
}

.nav-item {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 10px 14px;
    border-radius: 12px;
    color: var(--text-soft);
    text-decoration: none;
    font-size: 13.5px;
    font-weight: 500;
    transition: all 0.2s ease;
    position: relative;
    border: 1px solid transparent;
}

.nav-item:hover:not(.nav-active) {
    background: var(--gold-faint);
    color: var(--text-primary);
    border-color: var(--sidebar-border);
}

.nav-active {
    background: var(--gold-dim);
    color: var(--gold);
    border-color: rgba(214,168,91,0.25);
}

.nav-active::before {
    content: '';
    position: absolute;
    left: -14px;
    top: 50%;
    transform: translateY(-50%);
    width: 3px;
    height: 55%;
    background: var(--gold);
    border-radius: 0 4px 4px 0;
}

.nav-icon-wrap {
    width: 34px;
    height: 34px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    background: rgba(255,255,255,0.04);
    flex-shrink: 0;
    transition: all 0.2s ease;
}

.nav-active .nav-icon-wrap {
    background: rgba(214,168,91,0.15);
    color: var(--gold);
}

.nav-item:hover:not(.nav-active) .nav-icon-wrap {
    background: rgba(255,255,255,0.06);
}

.nav-label {
    flex: 1;
    font-weight: 500;
}

.nav-badge {
    background: var(--accent-red);
    color: white;
    font-size: 10px;
    font-weight: 700;
    min-width: 20px;
    height: 20px;
    padding: 0 7px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* ── Sidebar Footer ───────────────────────────────────────── */
.sidebar-footer {
    padding: 16px 14px 24px;
    border-top: 1px solid var(--sidebar-border);
    margin-top: auto;
}

.user-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: rgba(255,255,255,0.03);
    border-radius: 14px;
    border: 1px solid var(--sidebar-border);
    margin-bottom: 14px;
    transition: all 0.2s ease;
}

.user-card:hover {
    background: rgba(255,255,255,0.05);
    border-color: rgba(214,168,91,0.2);
}

.user-avatar {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #C79745, var(--gold));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    font-weight: 700;
    color: #1A1714;
    flex-shrink: 0;
    text-transform: uppercase;
}

.user-meta {
    display: flex;
    flex-direction: column;
    min-width: 0;
}

.user-name {
    font-size: 13px;
    font-weight: 600;
    color: var(--text-primary);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.user-role {
    font-size: 9px;
    color: var(--text-muted);
    margin-top: 2px;
    text-transform: uppercase;
    letter-spacing: 0.06em;
}

.logout-btn {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 11px 16px;
    background: rgba(255,255,255,0.03);
    border: 1px solid var(--sidebar-border);
    border-radius: 12px;
    color: var(--text-muted);
    cursor: pointer;
    transition: all 0.2s ease;
    font-family: inherit;
    font-size: 13px;
    font-weight: 500;
}

.logout-btn:hover {
    background: rgba(224,112,96,0.1);
    border-color: rgba(224,112,96,0.3);
    color: var(--accent-red);
}

/* ── Responsive Adjustments ───────────────────────────────── */
@media (max-width: 768px) {
    .admin-burger-btn {
        top: 16px;
        left: 16px;
    }

    .sidebar-header {
        padding-left: 72px;
    }
    
    .admin-sidebar {
        width: 260px;
    }
}
</style>
