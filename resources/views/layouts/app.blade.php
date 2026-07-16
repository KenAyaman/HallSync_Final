<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'HallSync') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">

    @php
        $isDashboard = request()->routeIs('dashboard');
        $showHero = Auth::check();
        $role = Auth::check() ? Auth::user()->role : 'guest';
    @endphp
    <script>
        document.documentElement.classList.add('is-loading');
        try {
            const role = @json($role);
            if (role === 'resident') {
                localStorage.setItem('hallsync-theme', 'dark');
                document.documentElement.classList.remove('theme-light');
            } else if (localStorage.getItem('hallsync-theme') === 'light') {
                document.documentElement.classList.add('theme-light');
            }
        } catch (error) {}
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>

/* Align the inner contents of the custom dropdown button */
.access-page .access-select-trigger {
    display: flex !important;
    align-items: center !important; 
    justify-content: space-between !important; /* Pushes text left, caret icon right */
    width: 100% !important;
    height: 100% !important;
    padding: 0 16px !important; /* Match horizontal padding of your search bar */
    box-sizing: border-box !important;
}

/* Perfect alignment for the text label container inside the dropdown */
.access-page [data-access-dropdown-label] {
    display: inline-flex !important;
    align-items: center !important;
    line-height: 1 !important; /* Removes inner text box line height shifts */
    margin: 0 !important;
    padding: 0 !important;
}

/* Mirror layout typography values precisely to make baseline characters match */
.access-page input[type="search"],
.access-page [data-access-dropdown-label],
.access-page .access-select-trigger {
    font-size: 0.95rem !important; /* Ensure identical text sizing */
    font-family: inherit !important;
}
/* Force the search/dropdown block elements to level out on a middle line */
.access-page .access-field,
.access-page .access-field-wide,
.access-page .access-control-panel label {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    vertical-align: middle !important;
    padding-top: 0 !important;
    padding-bottom: 0 !important;
    height: 42px !important; /* Forces uniform physical box heights across both elements */
}

/* Ensure child inputs and selects clear baseline offsets and take uniform layout lines */
.access-page input[type="search"],
.access-page select {
    display: block !important;
    margin: 0 !important;
    height: 100% !important;
    vertical-align: middle !important;
}
:root {
    --app-bg: #1A1714;
    --app-bg-soft: #211D19;
    --accent-gold: #C49A6C;
    --accent-gold-strong: #D6A85B;
    --text-heading: #F0E9DF;
    --text-body: #C4B8A8;
    --text-muted: #8A7A66;
}
html, body {
    margin: 0;
    padding: 0;
    min-height: 100vh;
    background: var(--app-bg);
    font-family: 'Inter', sans-serif;
    color: var(--text-heading);
    overflow-x: hidden;
}
body.role-resident {
    background: var(--app-bg);
}
body.role-guest {
    background: var(--app-bg);
}
body.role-handyman {
    background: radial-gradient(circle at top right, rgba(82, 120, 140, 0.10), transparent 28%), radial-gradient(circle at bottom left, rgba(214, 168, 91, 0.12), transparent 34%), linear-gradient(180deg, #f5efe6 0%, #ebe0d2 48%, #f8f4ee 100%);
    --text-heading: #342a23;
    --text-body: #5f5146;
    --text-muted: #786b60;
}
body.role-manager {
    --admin-bg-top: #f4efe7;
    --admin-bg-mid: #e8dfd3;
    --admin-bg-bottom: #f7f3ed;
    --admin-grid-line: rgba(87, 72, 55, 0.045);
    --admin-grid-line-soft: rgba(87, 72, 55, 0.032);
    --admin-ink: #2f2a24;
    --admin-ink-soft: #5e554a;
    --admin-ink-muted: #786b5d;
    --text-heading: var(--admin-ink);
    --text-body: var(--admin-ink-soft);
    --text-muted: var(--admin-ink-muted);
    background: radial-gradient(circle at top right, rgba(214, 168, 91, 0.11), transparent 28%), radial-gradient(circle at bottom left, rgba(120, 100, 75, 0.08), transparent 34%), linear-gradient(180deg, var(--admin-bg-top) 0%, var(--admin-bg-mid) 48%, var(--admin-bg-bottom) 100%);
}

/* Increase admin dashboard scale on desktop screens for improved readability */
@media (min-width: 1024px) {
    body.role-manager {
        zoom: 1.1; /* Globally scales admin dashboard to 110% */
    }
}
* {
    box-sizing: border-box;
}
textarea {
    min-height: 100px;
}
select {
    -webkit-appearance: none;
    appearance: none;
}
.app-skip-link {
    position: fixed;
    top: 10px;
    left: 10px;
    z-index: 10000;
    padding: 10px 14px;
    border-radius: 10px;
    background: #fff;
    color: #211d19;
    font-weight: 800;
    transform: translateY(-160%);
}
.app-skip-link:focus {
    transform: translateY(0);
}
.app-sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}

/* GLOBAL BACKGROUND GLOW */
.global-bg-glow {
    position: fixed;
    inset: 0;
    pointer-events: none;
    z-index: 0;
    background: radial-gradient(circle at 85% 5%, rgba(214, 168, 91, 0.10), transparent 30%), radial-gradient(circle at 10% 90%, rgba(196, 154, 108, 0.08), transparent 32%);
}
body.role-resident .global-bg-glow, body.role-guest .global-bg-glow {
    background: radial-gradient(circle at 85% 5%, rgba(214, 168, 91, 0.10), transparent 30%), radial-gradient(circle at 10% 90%, rgba(196, 154, 108, 0.08), transparent 32%);
}
body.role-handyman .global-bg-glow {
    background: radial-gradient(circle at 84% 8%, rgba(82, 120, 140, 0.10), transparent 28%), radial-gradient(circle at 14% 88%, rgba(214, 168, 91, 0.10), transparent 30%), radial-gradient(circle at 50% 0%, rgba(255, 255, 255, 0.44), transparent 28%);
}
body.role-manager .global-bg-glow {
    background: radial-gradient(circle at 84% 8%, rgba(214, 168, 91, 0.10), transparent 30%), radial-gradient(circle at 12% 86%, rgba(120, 100, 75, 0.07), transparent 34%), radial-gradient(circle at 50% 0%, rgba(255, 255, 255, 0.42), transparent 28%);
}
/* TOP HERO IMAGE */
.top-bg-image-layer {
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:360px;
    pointer-events:none;
    z-index:1;
    background-image:url('{{ asset('1.1.png') }}');
    background-repeat: no-repeat;
    background-position: top center;
    background-size: 100% auto;
    opacity: 0.9;
    mask-image: linear-gradient(to bottom, black 40%, transparent 100%), linear-gradient(to right, transparent 0%, black 40%);
    mask-composite: intersect;
}
.top-bg-image-layer::after {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(90deg, rgba(19, 16, 13, 0.76) 0%, rgba(19, 16, 13, 0.54) 32%, rgba(19, 16, 13, 0.18) 62%, rgba(19, 16, 13, 0) 100%);
}
body.role-resident .top-bg-image-layer {
    background-image:url('{{ asset('1.1.png') }}');
    background-repeat: no-repeat;
    background-position: top center;
    background-size: 100% auto;
    opacity: 0.9;
}
body.role-resident .top-bg-image-layer::after {
    background: linear-gradient(90deg, rgba(19, 16, 13, 0.76) 0%, rgba(19, 16, 13, 0.54) 32%, rgba(19, 16, 13, 0.18) 62%, rgba(19, 16, 13, 0) 100%);
}
html.theme-light body.role-resident {
    background: radial-gradient(circle at 82% 4%, rgba(255, 255, 255, 0.45), transparent 24%), linear-gradient(180deg, #f7efe5 0%, #f4eadc 48%, #ead9c2 100%);
}
html.theme-light body.role-resident .top-bg-image-layer {
    height:540px;
    background-image:url('{{ asset('White1.jpg') }}');
    background-repeat: no-repeat;
    background-position: top center;
    background-size: auto 100%;
    opacity: 0.92;
    filter: saturate(1.02) contrast(1);
    mask-image: linear-gradient(to bottom, black 42%, transparent 100%), linear-gradient(to right, transparent 0%, black 34%);
    mask-composite: intersect;
    -webkit-mask-image: linear-gradient(to bottom, black 42%, transparent 100%), linear-gradient(to right, transparent 0%, black 34%);
    -webkit-mask-composite: source-in;
}
html.theme-light body.role-resident .top-bg-image-layer::after {
    background: linear-gradient(180deg, rgba(251, 247, 240, 0.08) 0%, rgba(251, 247, 240, 0.28) 58%, rgba(251, 247, 240, 0.92) 100%), linear-gradient(90deg, rgba(251, 247, 240, 0.88) 0%, rgba(251, 247, 240, 0.58) 32%, rgba(251, 247, 240, 0.16) 66%, rgba(251, 247, 240, 0) 100%);
}
body.role-guest .top-bg-image-layer {
    background-image:url('{{ asset('1.1.png') }}');
    background-repeat: no-repeat;
    background-position: top center;
    background-size: 100% auto;
    opacity: 0.9;
}
body.role-guest .top-bg-image-layer::after {
    background: linear-gradient(90deg, rgba(19, 16, 13, 0.76) 0%, rgba(19, 16, 13, 0.54) 32%, rgba(19, 16, 13, 0.18) 62%, rgba(19, 16, 13, 0) 100%);
}
body.role-handyman .top-bg-image-layer {
    height: 420px;
    background-image: radial-gradient(circle at top right, rgba(214, 168, 91, 0.14), transparent 24%), radial-gradient(circle at 18% 10%, rgba(82, 120, 140, 0.08), transparent 26%), linear-gradient(135deg, rgba(255, 250, 242, 0.94) 0%, rgba(239, 226, 209, 0.88) 44%, rgba(248, 244, 238, 0.82) 100%);
    background-repeat: no-repeat;
    background-position: top center;
    background-size: cover;
    opacity: 1;
    mask-image: linear-gradient(to bottom, black 42%, transparent 100%);
}
body.role-handyman .top-bg-image-layer::after {
    background: linear-gradient(90deg, rgba(245, 239, 230, 0.72) 0%, rgba(245, 239, 230, 0.38) 36%, rgba(245, 239, 230, 0.12) 64%, rgba(245, 239, 230, 0) 100%);
}
body.role-manager .top-bg-image-layer {
    height: 430px;
    background-image: radial-gradient(circle at 78% 20%, rgba(214, 168, 91, 0.12), transparent 24%), radial-gradient(circle at 16% 8%, rgba(120, 100, 75, 0.09), transparent 26%), linear-gradient(135deg, rgba(246, 241, 234, 0.96) 0%, rgba(232, 223, 211, 0.90) 42%, rgba(244, 238, 230, 0.86) 70%, rgba(250, 247, 242, 0.96) 100%);
    background-repeat: no-repeat;
    background-position: top center;
    background-size: cover;
    opacity: 1;
    mask-image: linear-gradient(to bottom, black 44%, transparent 100%);
}
body.role-manager .top-bg-image-layer::after {
    background: linear-gradient(90deg, rgba(244, 238, 230, 0.52) 0%, rgba(244, 238, 230, 0.28) 38%, rgba(244, 238, 230, 0.08) 66%, rgba(244, 238, 230, 0) 100%);
}
main {
    position: relative;
    z-index: 2;
    width: 100%;
}
/* ================= RESIDENT / HANDYMAN ================= */
.app-main {
    max-width: 1700px;
    margin: 0 auto;
    padding: 24px 24px 48px;
    background: transparent;
}
.app-main.full-bleed {
    max-width: none;
}
.app-main.app-main-handyman {
    max-width: 1580px;
    padding-top: 18px;
}
.app-main.app-main-handyman.full-bleed {
    max-width: 1580px;
}
body.role-resident .app-main {
    position: relative;
}
body.role-resident .app-main::before {
    display: none;
}
/* ================= ADMIN (FIXED) ================= */
.admin-main-content {
    width: 100%;
    min-height: 100vh;
    padding: 24px 24px 48px;
    /* 🔥 FIX: SAME AS RESIDENT */
    background: transparent;
    position: relative;
    z-index: 2;
}
body.role-manager .admin-main-content::before {
    content: "";
    position: fixed;
    inset: 0;
    pointer-events: none;
    z-index: 0;
    background: radial-gradient(circle at 18% 8%, rgba(214, 168, 91, 0.10), transparent 24%), radial-gradient(circle at 82% 16%, rgba(120, 100, 75, 0.08), transparent 26%);
    background-size: auto, auto;
    mask-image: linear-gradient(to bottom, rgba(0, 0, 0, 0.9), transparent 58%);
}
.admin-content-shell {
    width: 100%;
    max-width: 1580px;
    margin: 0 auto;
    position: relative;
    z-index: 1;
}
.app-main, .admin-content-shell {
    animation: app-page-enter 0.42s cubic-bezier(0.22, 1, 0.36, 1) both;
}
@keyframes app-page-enter {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
@media (prefers-reduced-motion:reduce) {
    .app-main, .admin-content-shell {
        animation: none;
    }
}
body.role-manager .admin-content-shell {
    color: var(--admin-ink);
}
body.role-manager .admin-content-shell > .space-y-6 h1, body.role-manager .admin-content-shell > .space-y-8 h1, body.role-manager .admin-content-shell > .space-y-6 > div:not([style*="background"]) h2, body.role-manager .admin-content-shell > .space-y-8 > div:not([style*="background"]) h2 {
    color: var(--admin-ink) !important;
}
body.role-manager .admin-content-shell > .space-y-6 p, body.role-manager .admin-content-shell > .space-y-8 p, body.role-manager .admin-content-shell > .space-y-6 .text-gray-400, body.role-manager .admin-content-shell > .space-y-8 .text-gray-400, body.role-manager .admin-content-shell > .space-y-6 .text-gray-500, body.role-manager .admin-content-shell > .space-y-8 .text-gray-500 {
    color: var(--admin-ink-soft) !important;
}
body.role-manager .admin-content-shell > .space-y-6 > div[style*="background:#1F2023"], body.role-manager .admin-content-shell > .space-y-6 > div[style*="background:#1F2023"], body.role-manager .admin-content-shell > .space-y-6 > div[style*="background:#2A2C30"], body.role-manager .admin-content-shell > .space-y-6 > div[style*="background:#2A2C30"], body.role-manager .admin-content-shell > .space-y-8 > div[style*="background:#1F2023"], body.role-manager .admin-content-shell > .space-y-8 > div[style*="background:#1F2023"], body.role-manager .admin-content-shell > .space-y-8 > div[style*="background:#2A2C30"], body.role-manager .admin-content-shell > .space-y-8 > div[style*="background:#2A2C30"] {
    background: linear-gradient(180deg, rgba(43, 42, 39, 0.95) 0%, rgba(31, 31, 29, 0.95) 100%) !important;
    border-color: rgba(214, 168, 91, 0.18) !important;
    box-shadow: 0 18px 36px rgba(72, 48, 24, 0.16);
}
body.role-manager .admin-content-shell > .space-y-6 input, body.role-manager .admin-content-shell > .space-y-6 select, body.role-manager .admin-content-shell > .space-y-6 textarea, body.role-manager .admin-content-shell > .space-y-8 input, body.role-manager .admin-content-shell > .space-y-8 select, body.role-manager .admin-content-shell > .space-y-8 textarea {
    background: rgba(48, 45, 40, 0.95) !important;
    border-color: rgba(214, 168, 91, 0.20) !important;
    color: #f8f3ea !important;
}
body.role-manager .admin-content-shell > .space-y-6 table .text-white, body.role-manager .admin-content-shell > .space-y-8 table .text-white {
    color: #f8f3ea !important;
}
body.role-manager .admin-content-shell > .space-y-6 > div:not([style*="background"]) .text-white, body.role-manager .admin-content-shell > .space-y-8 > div:not([style*="background"]) .text-white, body.role-manager .admin-content-shell > .space-y-6 > div:not([style*="background"]) .text-gray-300, body.role-manager .admin-content-shell > .space-y-8 > div:not([style*="background"]) .text-gray-300 {
    color: var(--admin-ink) !important;
}
.admin-content-shell.dashboard-shell {
    max-width: 1580px;
}
header, nav {
    position: relative;
    z-index: 50;
}
/* ================= RESPONSIVE ================= */
@media (min-width:768px) {
    .app-main, .admin-main-content {
        padding: 28px 40px 56px;
    }
    .app-main.app-main-handyman {
        padding-top: 20px;
    }
    .top-bg-image-layer {
        height: 520px;
    }
}
@media (min-width:1024px) {
    .app-main, .admin-main-content {
        padding: 32px 64px 64px;
    }
    .app-main.app-main-handyman {
        padding-top: 22px;
    }
}
@media (max-width:768px) {
    .admin-main-content {
        padding: 20px 16px 40px;
    }
    .app-main.app-main-handyman {
        padding-top: 14px;
    }
}
/* ================= SHARED ROLE TOPBAR ================= */
.role-topbar-wrap {
    max-width: 1600px;
    margin: 0 auto;
    padding: 18px 24px 10px;
    position: relative;
    z-index: 55;
}
.role-topbar-wrap.staff-topbar-wrap {
    max-width: 1580px;
}
.role-topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 18px;
}
.role-brand {
    display: inline-flex;
    align-items: center;
    text-decoration: none;
    color: var(--text-heading);
    flex: 1 1 0;
    min-width: 0;
}
.role-brand-copy {
    display: flex;
    flex-direction: column;
    gap: 3px;
}
.role-brand-title {
    font-family: 'Playfair Display', serif;
    font-size: 1.75rem;
    font-weight: 700;
    line-height: 0.92;
    color: var(--text-heading);
    letter-spacing: -0.03em;
    text-shadow: 0 8px 20px rgba(0, 0, 0, 0.16);
}
.role-brand-title span {
    color: var(--accent-gold-strong);
}
.role-brand-subtitle {
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 0.22em;
    color: rgba(196, 184, 168, 0.78);
    font-weight: 700;
}
.role-nav-shell {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 7px;
    border-radius: 999px;
    background: linear-gradient(180deg, rgba(30, 27, 23, 0.82) 0%, rgba(22, 20, 18, 0.88) 100%);
    border: 1px solid rgba(255, 255, 255, 0.09);
    backdrop-filter: blur(18px);
    box-shadow: 0 18px 42px rgba(0, 0, 0, 0.22);
    flex: 0 1 auto;
    margin-inline: auto;
}
.role-nav-link {
    padding: 11px 18px;
    border-radius: 999px;
    text-decoration: none;
    color: rgba(240, 233, 223, 0.84);
    font-size: 0.92rem;
    font-weight: 600;
    transition: 0.2s ease;
    white-space: nowrap;
}
.role-nav-link:hover {
    color: #fff6e7;
    background: rgba(255, 255, 255, 0.06);
}
.role-nav-link.is-active {
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.06) 100%);
    color: #fff6e7;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.05);
}
.role-topbar-actions {
    display: flex;
    align-items: center;
    gap: 12px;
    flex: 1 1 0;
    min-width: 0;
    justify-content: flex-end;
}
.role-user-chip {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 46px;
    height: 46px;
    border-radius: 999px;
    position: relative;
    background: linear-gradient(180deg, rgba(30, 27, 23, 0.82) 0%, rgba(22, 20, 18, 0.9) 100%);
    border: 1px solid rgba(255, 255, 255, 0.08);
    color: var(--text-heading);
    backdrop-filter: blur(18px);
    text-decoration: none;
    box-shadow: 0 16px 34px rgba(0, 0, 0, 0.2);
}
.role-notification-wrap {
    position: relative;
}
.role-theme-toggle {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 46px;
    height: 46px;
    border-radius: 999px;
    position: relative;
    background: linear-gradient(180deg, rgba(30, 27, 23, 0.82) 0%, rgba(22, 20, 18, 0.9) 100%);
    border: 1px solid rgba(255, 255, 255, 0.08);
    color: var(--text-heading);
    backdrop-filter: blur(18px);
    box-shadow: 0 16px 34px rgba(0, 0, 0, 0.2);
    cursor: pointer;
    transition: transform 0.2s ease, background 0.2s ease, border-color 0.2s ease;
}
.role-theme-toggle:hover {
    transform: translateY(-1px);
    background: rgba(255, 255, 255, 0.06);
}
.role-theme-icon {
    width: 19px;
    height: 19px;
}
.role-theme-icon-moon, html.theme-light .role-theme-icon-sun {
    display: none;
}
html.theme-light .role-theme-icon-moon {
    display: block;
}
.role-notification-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 46px;
    height: 46px;
    border-radius: 999px;
    position: relative;
    background: linear-gradient(180deg, rgba(30, 27, 23, 0.82) 0%, rgba(22, 20, 18, 0.9) 100%);
    border: 1px solid rgba(255, 255, 255, 0.08);
    color: var(--text-heading);
    backdrop-filter: blur(18px);
    box-shadow: 0 16px 34px rgba(0, 0, 0, 0.2);
    cursor: pointer;
}
.role-notification-btn svg {
    width: 20px;
    height: 20px;
}
.role-notification-count {
    position: absolute;
    top: -4px;
    right: -2px;
    min-width: 20px;
    height: 20px;
    padding: 0 6px;
    border-radius: 999px;
    background: linear-gradient(135deg, #e0705f 0%, #c94f43 100%);
    color: #fff;
    font-size: 0.72rem;
    font-weight: 800;
    line-height: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    box-shadow: 0 10px 20px rgba(201, 79, 67, 0.32);
}
.role-notification-panel {
    position: absolute;
    top: calc(100% + 12px);
    right: 0;
    width: min(360px, 90vw);
    padding: 14px;
    border-radius: 20px;
    background: rgba(24, 21, 18, 0.96);
    border: 1px solid rgba(255, 255, 255, 0.08);
    box-shadow: 0 18px 40px rgba(0, 0, 0, 0.24);
    max-height: calc(100vh - 120px);
    overflow-y: auto;
}
.role-notification-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 12px;
    padding-bottom: 12px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.06);
}
.role-notification-head strong {
    color: #f0e9df;
    font-size: 0.95rem;
}
.role-notification-head span, .role-notification-head a, .role-mobile-notification-head a, .role-notification-item small, .role-mobile-notification-head span {
    color: #9f927f;
    font-size: 0.8rem;
}
.role-notification-head a, .role-mobile-notification-head a {
    color: #d6a85b;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-decoration: none;
    text-transform: uppercase;
}
.role-notification-list {
    display: grid;
    gap: 10px;
}
.role-notification-item, .role-mobile-notification-item {
    display: block;
    padding: 12px 14px;
    border-radius: 16px;
    text-decoration: none;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.05);
}
.role-notification-item:hover, .role-mobile-notification-item:hover {
    background: rgba(255, 255, 255, 0.05);
}
.role-notification-label {
    display: block;
    color: #d6a85b;
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.12em;
    margin-bottom: 5px;
}
.role-notification-item strong, .role-mobile-notification-item strong {
    display: block;
    color: #f0e9df;
    font-size: 0.92rem;
    line-height: 1.45;
}
.role-mobile-notification-item span {
    display: block;
    margin-top: 4px;
    color: #b8ab98;
    font-size: 0.84rem;
    line-height: 1.5;
}
.role-notification-empty, .role-mobile-notification-empty {
    padding: 12px 14px;
    border-radius: 16px;
    background: rgba(255, 255, 255, 0.03);
    border: 1px dashed rgba(214, 168, 91, 0.18);
    color: #b8ab98;
    font-size: 0.88rem;
}
.role-user-avatar {
    width: 100%;
    height: 100%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #f0dfc3;
    overflow: hidden;
    border-radius: 999px;
}
.role-user-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.role-user-avatar svg {
    width: 20px;
    height: 20px;
}
.role-user-tooltip {
    position: absolute;
    top: calc(100% + 10px);
    right: 0;
    padding: 9px 12px;
    border-radius: 12px;
    white-space: nowrap;
    background: rgba(24, 21, 18, 0.94);
    border: 1px solid rgba(255, 255, 255, 0.08);
    color: #f0e9df;
    font-size: 0.8rem;
    font-weight: 600;
    opacity: 0;
    pointer-events: none;
    transform: translateY(-4px);
    transition: 0.18s ease;
    box-shadow: 0 16px 34px rgba(0, 0, 0, 0.22);
}
.role-user-chip:hover .role-user-tooltip, .role-user-chip:focus-visible .role-user-tooltip {
    opacity: 1;
    transform: translateY(0);
}
.role-action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 11px 18px;
    border-radius: 999px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    background: linear-gradient(180deg, rgba(30, 27, 23, 0.72) 0%, rgba(22, 20, 18, 0.82) 100%);
    color: #e7e0d5;
    font-size: 0.9rem;
    font-weight: 700;
    text-decoration: none;
    cursor: pointer;
    transition: 0.2s ease;
    font-family: inherit;
    backdrop-filter: blur(16px);
}
.role-action-btn:hover {
    background: linear-gradient(180deg, rgba(41, 37, 32, 0.82) 0%, rgba(27, 24, 21, 0.92) 100%);
    color: #fff6e7;
}
.role-action-btn.primary {
    background: linear-gradient(135deg, #c79745 0%, #d6a85b 100%);
    color: #1a1714;
    border-color: rgba(214, 168, 91, 0.25);
}
.role-mobile-toggle {
    display: none;
    width: 44px;
    height: 44px;
    border-radius: 999px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    background: linear-gradient(180deg, rgba(30, 27, 23, 0.82) 0%, rgba(22, 20, 18, 0.9) 100%);
    color: #e7e0d5;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    backdrop-filter: blur(16px);
}
.role-mobile-panel {
    margin-top: 14px;
    padding: 14px;
    border-radius: 22px;
    background: rgba(24, 21, 18, 0.96);
    border: 1px solid rgba(255, 255, 255, 0.08);
    box-shadow: 0 18px 40px rgba(0, 0, 0, 0.24);
}
.role-mobile-panel .role-nav-link, .role-mobile-panel .role-action-btn {
    width: 100%;
    justify-content: flex-start;
    border-radius: 14px;
    margin-bottom: 6px;
}
.role-mobile-panel .role-nav-link {
    display: flex;
    padding: 12px 14px;
}
.role-mobile-panel .role-action-btn {
    margin-top: 8px;
}
.role-mobile-notification-block {
    margin-bottom: 10px;
    padding: 12px;
    border-radius: 18px;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.05);
}
.role-mobile-notification-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 10px;
}

.role-mobile-notification-head strong {
    color: #f0e9df;
    font-size: 0.9rem;
}
@media (max-width:980px) {
    .role-nav-shell, .role-topbar-actions.auth-only-desktop {
        display: none;
    }
    .role-mobile-toggle {
        display: inline-flex;
    }
    .role-topbar-wrap {
        padding: 18px 16px 6px;
    }
    .role-brand-title {
        font-size: 1.5rem;
    }
    .role-brand {
        flex: 0 1 auto;
    }
}
</style>
</head>
<body class="role-{{ $role }}">
<a class="app-skip-link" href="#app-main-content">Skip to main content</a>
<p class="app-sr-only" role="status" aria-live="polite">Page loaded. Use the skip link to move to the main content.</p>

<div class="global-bg-glow"></div>

@if($showHero)
    <div class="top-bg-image-layer"></div>
@endif

@auth
    @if(Auth::user()->role === 'manager')
        @include('layouts.admin-nav')
        <main id="app-main-content" class="admin-main-content" tabindex="-1">
            <div class="admin-content-shell {{ $isDashboard ? 'dashboard-shell' : '' }}">
                {{ $slot }}
            </div>
        </main>

    @elseif(Auth::user()->role === 'handyman')
        @include('layouts.handyman-nav')
        <main id="app-main-content" class="app-main app-main-handyman" tabindex="-1">
            {{ $slot }}
        </main>

    @else
        @include('layouts.navigation')
        <main id="app-main-content" class="app-main {{ $isDashboard ? 'full-bleed' : '' }}" tabindex="-1">
            {{ $slot }}
        </main>
    @endif
@else
    @include('layouts.navigation')
    <main id="app-main-content" class="app-main" tabindex="-1">
        {{ $slot }}
    </main>
@endauth

<style>
@media (max-width: 768px) {
    :root {
        --m-space-1: 4px;
        --m-space-2: 8px;
        --m-space-3: 12px;
        --m-space-4: 16px;
        --m-space-5: 20px;
        --m-space-6: 24px;
        --m-page-pad: clamp(16px, 4.8vw, 20px);
        --m-gap-page: 20px;
        --m-gap-section: 16px;
        --m-gap-list: 10px;
        --m-radius-sm: 10px;
        --m-radius-md: 14px;
        --m-radius-lg: 16px;
        --m-radius-xl: 20px;
        --m-touch: 48px;
        --m-font-title: clamp(1.42rem, 6.2vw, 1.72rem);
        --m-font-section: clamp(1.12rem, 4.8vw, 1.28rem);
        --m-font-card: 1rem;
        --m-font-body: 0.94rem;
        --m-font-caption: 0.78rem;
        --m-shadow-card: 0 10px 22px rgba(44, 30, 18, 0.10);
        --m-shadow-raised: 0 18px 38px rgba(32, 22, 14, 0.18);
        --m-ease: 160ms ease;
        --m-surface-light: #fffdf9;
        --m-surface-warm: #fff8ee;
        --m-border-warm: rgba(109, 80, 54, 0.16);
        --m-ink: var(--text-heading);
        --m-body: var(--text-body);
        --m-muted: var(--text-muted);
        --m-accent: var(--accent-gold-strong);
    }
    html,
    body {
        width: 100%;
        overflow-x: hidden !important;
        scroll-padding-top: 76px;
    }
    body {
        font-size: 16px;
        line-height: 1.5;
    }
    body.role-manager,
    body.role-handyman {
        --m-surface-light: #fffdf9;
        --m-surface-warm: #fff8ee;
        --m-border-warm: rgba(109, 80, 54, 0.16);
        --m-ink: #342a23;
        --m-body: #5f5146;
        --m-muted: #786b60;
    }
    body.role-resident,
    body.role-guest {
        --m-surface-light: rgba(36, 31, 26, 0.88);
        --m-surface-warm: rgba(45, 39, 32, 0.88);
        --m-border-warm: rgba(214, 168, 91, 0.16);
        --m-ink: #f0e9df;
        --m-body: #cfc4b5;
        --m-muted: #a69683;
    }
    .app-main,
    .app-main.full-bleed,
    .app-main.app-main-handyman,
    .admin-main-content {
        width: 100% !important;
        max-width: 100% !important;
        padding: 76px var(--m-page-pad) 36px !important;
    }
    body.role-resident .app-main,
    body.role-guest .app-main {
        padding-top: 14px !important;
    }
    .admin-content-shell,
    .app-main,
    .dash-root,
    .hs-dashboard,
    .resident-dashboard-shell,
    .staff-workspace,
    .resident-page,
    .resident-ticket-page,
    .resident-booking-page,
    .resident-ticket-create-page,
    .resident-booking-create-page,
    .resident-concern-form-page,
    .community-feed-page,
    .community-post-page,
    .concern-page,
    .notifications-page,
    .profile-page,
    .access-page,
    .booking-dashboard {
        display: flex !important;
        flex-direction: column !important;
        gap: var(--m-gap-page) !important;
        width: 100% !important;
        max-width: 100% !important;
    }
    .admin-content-shell > *,
    .app-main > *,
    .dash-root > *,
    .hs-dashboard > *,
    .resident-dashboard-shell > *,
    .staff-workspace > *,
    .booking-shell > * {
        margin-top: 0 !important;
        margin-bottom: 0 !important;
    }
    .top-bg-image-layer {
        height: 210px !important;
        opacity: 0.48 !important;
        background-size: cover !important;
    }
    .global-bg-glow {
        opacity: 0.62;
    }

    /* Unified mobile app bars and drawers. */
    .role-topbar-wrap {
        position: sticky !important;
        top: 0;
        z-index: 900;
        padding: 10px var(--m-page-pad) 8px !important;
        background: color-mix(in srgb, var(--app-bg) 88%, transparent);
        border-bottom: 1px solid rgba(214, 168, 91, 0.14);
        backdrop-filter: blur(16px);
    }
    body.role-manager .admin-nav-wrapper::before,
    body.role-handyman .staff-nav-wrapper::before {
        content: "";
        position: fixed;
        inset: 0 0 auto;
        height: 68px;
        z-index: 180;
        background: color-mix(in srgb, var(--admin-bg-top, #f5efe6) 90%, transparent);
        border-bottom: 1px solid rgba(109, 80, 54, 0.14);
        backdrop-filter: blur(16px);
        pointer-events: none;
    }
    body.role-manager .admin-nav-wrapper::after,
    body.role-handyman .staff-nav-wrapper::after {
        content: "HallSync";
        position: fixed;
        top: 15px;
        left: 76px;
        z-index: 211;
        color: var(--m-ink);
        font-size: 1.02rem;
        font-weight: 850;
        letter-spacing: 0;
        line-height: 1;
        pointer-events: none;
    }
    /* Burger/X buttons: remove the surrounding “box” so only the X glyph shows on top of sticky header. */
    .admin-burger-btn,
    .staff-burger-btn,
    .role-mobile-toggle {
        position: absolute !important;
        top: 10px !important;
    }

    /* Shared size */
    .admin-burger-btn,
    .staff-burger-btn,
    .role-mobile-toggle,
    .role-notification-btn,
    .role-user-chip {
        width: var(--m-touch) !important;
        height: var(--m-touch) !important;
        min-width: var(--m-touch) !important;
        border-radius: 0 !important;
        z-index: 980 !important;

        /* strip box visuals */
        background: transparent !important;
        border: 0 !important;
        box-shadow: none !important;
        padding: 0 !important;
    }

    /* Align horizontally near sidebar edge */
    .admin-burger-btn,
    .staff-burger-btn {
        left: var(--m-page-pad) !important;
        z-index: 212 !important;
    }

    /* Align mobile toggle to the right side */
    .role-mobile-toggle {
        right: var(--m-page-pad) !important;
    }


    /* When open, keep the X aligned with the sidebar edge.
       Prevents it from being pushed to the far right / end of the topbar. */

    .admin-overlay,
    .staff-overlay {
        background: rgba(25, 20, 16, 0.42) !important;
        backdrop-filter: blur(8px) !important;
    }
    .admin-sidebar,
    .staff-sidebar {
        width: min(88vw, 340px) !important;
        max-width: 340px !important;
        border-radius: 0 var(--m-radius-xl) var(--m-radius-xl) 0 !important;
        box-shadow: var(--m-shadow-raised) !important;
    }
    .role-mobile-panel {
        position: fixed !important;
        inset: 70px var(--m-page-pad) auto !important;
        max-height: calc(100dvh - 86px) !important;
        padding: var(--m-space-3) !important;
        border-radius: var(--m-radius-xl) !important;
        overflow-y: auto !important;
        overscroll-behavior: contain;
        box-shadow: var(--m-shadow-raised) !important;
    }
    .nav-item,
    .staff-nav-item,
    .role-mobile-panel .role-nav-link,
    .role-mobile-panel .role-action-btn,
    .logout-btn,
    .staff-logout-btn {
        min-height: var(--m-touch) !important;
        border-radius: var(--m-radius-md) !important;
        font-size: 0.94rem !important;
        letter-spacing: 0 !important;
    }

    /* Unified headers. */
    :is(
        .admin-overview-hero,
        .resident-home-hero,
        .resident-page-hero,
        .resident-ticket-hero,
        .ticket-track-hero,
        .resident-ticket-create-hero,
        .resident-ticket-edit-hero,
        .resident-booking-hero,
        .resident-booking-create-hero,
        .resident-booking-edit-hero,
        .resident-announcement-hero,
        .community-feed-hero,
        .community-post-hero,
        .concern-hero,
        .resident-concern-form-hero,
        .notifications-hero,
        .access-form-hero,
        .account-hero,
        .hs-topbar,
        .staff-overview-hero,
        .handyman-ticket-hero
    ) {
        min-height: 0 !important;
        padding: var(--m-space-4) !important;
        border-radius: var(--m-radius-lg) !important;
        gap: var(--m-space-3) !important;
        box-shadow: var(--m-shadow-card) !important;
    }
    :is(
        .admin-overview-hero__title,
        .resident-home-title,
        .resident-page-title,
        .resident-ticket-title,
        .ticket-track-title,
        .resident-booking-title,
        .resident-booking-create-title,
        .resident-ticket-create-title,
        .community-feed-title,
        .community-post-title,
        .concern-title,
        .resident-concern-form-hero h1,
        .notifications-hero h1,
        .access-form-title,
        .account-hero h1,
        .hs-topbar h1,
        .handyman-ticket-title
    ) {
        font-size: var(--m-font-title) !important;
        line-height: 1.12 !important;
        letter-spacing: 0 !important;
        margin: 0 !important;
    }
    :is(
        .admin-overview-hero__subtitle,
        .resident-home-subtitle,
        .resident-page-subtitle,
        .resident-ticket-subtitle,
        .resident-booking-subtitle,
        .resident-booking-create-subtitle,
        .resident-ticket-create-subtitle,
        .community-feed-subtitle,
        .community-post-subtitle,
        .concern-subtitle,
        .resident-concern-form-hero span,
        .notifications-hero p,
        .access-form-subtitle,
        .hs-date,
        .handyman-ticket-subtitle
    ) {
        font-size: var(--m-font-body) !important;
        line-height: 1.45 !important;
        max-width: 100% !important;
    }
    :is(
        .admin-overview-hero__kicker,
        .resident-home-kicker,
        .resident-page-kicker,
        .resident-ticket-kicker,
        .resident-booking-kicker,
        .resident-ticket-create-kicker,
        .resident-booking-create-kicker,
        .community-feed-kicker,
        .community-post-kicker,
        .concern-kicker,
        .hs-eyebrow
    ) {
        font-size: 0.72rem !important;
        line-height: 1.2 !important;
        letter-spacing: 0.08em !important;
        margin-bottom: var(--m-space-2) !important;
    }

    /* Unified surfaces, cards, and lists. */
    :is(
        .admin-ticket-panel,
        .admin-status-card,
        .ticket-card-shell,
        .admin-concern-card,
        .admin-concern-row,
        .admin-community-review-panel,
        .admin-community-review-card,
        .announcement-card,
        .announcement-standards-panel,
        .booking-panel,
        .summary-row,
        .history-row,
        .access-directory,
        .access-table tbody tr,
        .hs-card,
        .hs-metric-card,
        .hs-diagnostic-card,
        .da-card,
        .da-kpi-card,
        .da-insight-card,
        .resident-activity-card,
        .resident-surface-panel,
        .resident-page-panel,
        .resident-ticket-panel,
        .resident-booking-panel,
        .resident-booking-detail-panel,
        .resident-stack-item,
        .resident-notice-card,
        .resident-community-entry,
        .community-composer-card,
        .community-feed-card,
        .community-review-strip,
        .community-review-card,
        .community-post-panel,
        .community-comment-card,
        .concern-card,
        .resident-concern-form-panel,
        .notifications-panel,
        .notifications-card,
        .staff-panel,
        .staff-ticket-card,
        .staff-preview-card,
        .staff-urgent-item,
        .staff-completed-card,
        .handyman-ticket-panel,
        .handyman-ticket-meta-item,
        .handyman-ticket-note-item,
        .profile-card,
        .rex-auth-card,
        .admin-empty-state,
        .resident-empty-state,
        .community-empty-state
    ) {
        width: 100% !important;
        max-width: 100% !important;
        min-height: 0 !important;
        padding: var(--m-space-4) !important;
        border-radius: var(--m-radius-lg) !important;
        box-shadow: var(--m-shadow-card) !important;
        overflow: hidden;
    }
    :is(
        .resident-stack-list,
        .resident-notice-list,
        .resident-community-list,
        .community-feed-list,
        .staff-ticket-list,
        .staff-preview-list,
        .staff-urgent-list,
        .staff-completed-list,
        .admin-concern-list,
        .summary-list,
        .history-list,
        .notifications-list,
        [data-progressive-list]
    ) {
        display: grid !important;
        gap: var(--m-gap-list) !important;
        border: 0 !important;
    }
    :is(
        .resident-stack-item,
        .resident-notice-card,
        .community-feed-card,
        .community-review-card,
        .staff-ticket-card,
        .staff-preview-card,
        .staff-urgent-item,
        .staff-completed-card,
        .admin-concern-row,
        .admin-community-review-card,
        .summary-row,
        .history-row,
        .notifications-card
    ) {
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: var(--m-space-2) !important;
        border: 1px solid var(--m-border-warm) !important;
    }

    /* Unified typography rhythm inside content. */
    .app-main :is(h2, .resident-section-title, .admin-ticket-panel-title, .panel-heading h2, .staff-panel-head h2, .notifications-panel-head h2, .hs-section-heading h2),
    .admin-main-content :is(h2, .admin-ticket-panel-title, .panel-heading h2, .access-panel-head h2, .hs-section-heading h2),
    .app-main-handyman :is(h2, .staff-panel-head h2, .handyman-ticket-panel-title) {
        font-size: var(--m-font-section) !important;
        line-height: 1.18 !important;
        letter-spacing: 0 !important;
        margin: 0 !important;
    }
    .app-main :is(h3, h4),
    .admin-main-content :is(h3, h4),
    .app-main-handyman :is(h3, h4) {
        font-size: var(--m-font-card) !important;
        line-height: 1.25 !important;
        letter-spacing: 0 !important;
    }
    .app-main :is(p, li, small),
    .admin-main-content :is(p, li, small),
    .app-main-handyman :is(p, li, small) {
        line-height: 1.45 !important;
    }

    /* Unified grids and spacing. */
    .app-main :is(.grid, [class*="grid-cols-"], .resident-content-grid, .resident-activity-grid, .resident-card-grid, .resident-meta-grid, .resident-booking-create-stats, .resident-ticket-info-grid),
    .admin-main-content :is(.grid, [class*="grid-cols-"], .admin-compact-stats, .hs-command-grid, .hs-metrics, .hs-diagnostics, .hs-analytics-grid, .da-kpi-grid, .da-insight-grid, .da-two-col, .access-stats, .access-filters, .month-grid),
    .app-main-handyman :is(.grid, [class*="grid-cols-"], .staff-metrics, .handyman-ticket-grid) {
        grid-template-columns: 1fr !important;
        gap: var(--m-space-3) !important;
    }
    :is(.flex-row, [class*="flex-row"]) {
        flex-direction: column !important;
        align-items: stretch !important;
    }

    /* Unified controls. */
    :is(input:not([type="checkbox"]):not([type="radio"]), select, textarea, .text-input, .resident-booking-create-input, .resident-ticket-create-input, .concern-input, .community-filter-input, .community-filter-select, .staff-filter) {
        width: 100% !important;
        min-height: var(--m-touch) !important;
        padding: 12px 14px !important;
        border-radius: var(--m-radius-md) !important;
        font-size: 16px !important;
        line-height: 1.25 !important;
    }
    /* Preserve user page filter input and dropdown button styling */
    body.role-manager .user-filter-input,
    body.role-manager .admin-concern-filter-input {
        padding: 0 10px !important;
        border-radius: 7px !important;
        font-size: 0.8rem !important;
        background: #fffdf9 !important;
        color: #453b33 !important;
        border: 1px solid #dfd5c8 !important;
    }
    body.role-manager .user-operations-filter,
    body.role-manager .admin-concern-operations-filter {
        padding: 0 20px !important;
        border-radius: 7px !important;
        font-size: 0.74rem !important;
        background: #fffdf9 !important;
        color: #453b33 !important;
        border: 1px solid #dfd5c8 !important;
        min-height: 45px !important;
    }
    /* Preserve Apply button gradient styling */
    body.role-manager .user-filter-btn,
    body.role-manager .admin-concern-filter-btn {
        border-radius: 7px !important;
        background: linear-gradient(90deg, #B8842F 0%, #D6A85B 100%) !important;
        color: #FFFFFF !important;
        box-shadow: 0 12px 28px rgba(199, 150, 69, 0.3) !important;
        min-height: 45px !important;
        padding: 0 20px !important;
    }
    /* Preserve dropdown menu styling */
    body.role-manager .user-priority-menu,
    body.role-manager .admin-concern-priority-menu {
        border-radius: 7px !important;
    }
    body.role-manager .user-priority-menu button,
    body.role-manager .admin-concern-priority-menu button {
        border-radius: 7px !important;
    }
    textarea {
        min-height: 118px !important;
    }
    label {
        font-size: var(--m-font-caption);
    }
    :is(
        button,
        input[type="submit"],
        input[type="button"],
        a[class*="btn"],
        a[class*="button"],
        a[class*="action"],
        .resident-home-btn,
        .resident-page-btn,
        .resident-ticket-create-btn,
        .resident-booking-create-btn,
        .community-action-btn,
        .community-review-actions a,
        .community-review-actions button,
        .admin-critical-action,
        .staff-action-btn,
        .handyman-ticket-btn,
        .booking-modal-button,
        .app-confirm-btn,
        .app-progressive-toggle
    ) {
        min-height: var(--m-touch) !important;
        border-radius: var(--m-radius-md) !important;
        font-size: 0.92rem !important;
        letter-spacing: 0 !important;
        line-height: 1.18 !important;
        text-align: center !important;
        white-space: normal !important;
        touch-action: manipulation;
    }
    
    :is(.actions, .Actions, [class*="actions"], [class*="Actions"], .resident-home-actions, .resident-booking-create-form-actions, .resident-ticket-create-actions, .community-feed-actions, .staff-ticket-actions, .handyman-ticket-hero-actions) {
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: var(--m-space-2) !important;
        align-items: stretch !important;
    }
    :is(.actions, .Actions, [class*="actions"], [class*="Actions"], .resident-home-actions, .resident-booking-create-form-actions, .resident-ticket-create-actions, .community-feed-actions, .staff-ticket-actions, .handyman-ticket-hero-actions) > * {
        width: 100% !important;
        min-width: 0 !important;
    }

    /* Unified badges and chips. */
    :is(
        [class*="badge"],
        [class*="Badge"],
        [class*="chip"],
        [class*="Chip"],
        .status-badge,
        .resident-status-chip,
        .community-status-chip,
        .staff-ticket-status-chip,
        .ticket-card-status,
        .ticket-card-priority,
        .admin-critical-metrics span,
        .hs-health
    ) {
        min-height: 30px !important;
        padding: 6px 10px !important;
        border-radius: 999px !important;
        font-size: 0.72rem !important;
        font-weight: 800 !important;
        letter-spacing: 0.03em !important;
        line-height: 1.1 !important;
        text-transform: none !important;
        align-items: center;
    }

    /* Unified dialogs and bottom sheets. */
    :is(.app-confirm-backdrop, .admin-ticket-modal-backdrop, .booking-modal, [class*="modal-backdrop"]) {
        align-items: flex-end !important;
        justify-content: center !important;
        padding: var(--m-space-3) !important;
        backdrop-filter: blur(8px) !important;
    }
    :is(.app-confirm-dialog, .booking-modal-card, [role="dialog"], [class*="modal-card"], [class*="Modal"]) {
        width: 100% !important;
        max-width: 100% !important;
        max-height: min(88dvh, 720px) !important;
        overflow-y: auto !important;
        border-radius: 22px 22px var(--m-radius-lg) var(--m-radius-lg) !important;
        box-shadow: var(--m-shadow-raised) !important;
    }

    /* Unified loading, empty, and progressive states. */
    :is(.feature-skeleton-card, .feature-skeleton-stack, [data-feature-skeleton], .resident-dashboard-skeleton) {
        border-radius: var(--m-radius-lg) !important;
    }
    :is(.admin-empty-state, .resident-empty-state, .community-empty-state, .staff-empty-copy, .resident-dashboard-empty, .notifications-empty) {
        display: grid !important;
        place-items: center;
        gap: var(--m-space-2) !important;
        min-height: 160px !important;
        text-align: center !important;
    }
    .app-progressive-action {
        margin-top: var(--m-space-2) !important;
    }
    .app-progressive-toggle {
        width: 100% !important;
    }

    /* Tables are not desktop tables on phones unless a page opts into contained scroll. */
    table {
        max-width: 100% !important;
        min-width: 0 !important;
    }
    .overflow-x-auto,
    .da-table-scroll,
    .da-table-wrap,
    .access-table-wrap,
    .calendar-table-wrap {
        max-width: 100% !important;
    }

    /* Motion and focus. */
    :is(a, button, input, select, textarea) {
        transition-duration: 160ms !important;
        transition-timing-function: ease !important;
    }
    :is(a, button, input, select, textarea):focus-visible {
        outline: 3px solid color-mix(in srgb, var(--m-accent) 55%, transparent) !important;
        outline-offset: 2px !important;
    }
}
@media (max-width: 414px) {
    :root {
        --m-page-pad: 16px;
        --m-gap-page: 18px;
        --m-gap-section: 14px;
    }
}
@media (max-width: 375px) {
    :root {
        --m-space-4: 14px;
        --m-font-title: clamp(1.34rem, 6.4vw, 1.56rem);
    }
    .admin-sidebar,
    .staff-sidebar {
        width: min(92vw, 326px) !important;
    }
}
@media (max-width: 320px) {
    :root {
        --m-page-pad: 14px;
        --m-space-4: 12px;
        --m-touch: 46px;
    }
    body.role-manager .admin-nav-wrapper::after,
    body.role-handyman .staff-nav-wrapper::after {
        left: 70px;
        font-size: 0.96rem;
    }

@media (min-width: 1024px) {
    .admin-sidebar,
    .staff-sidebar {
        zoom: 0.9091;
        max-height: 100vh;
        overflow-y: auto;
        position: fixed;
    }
}
}
</style>

@php
    $currentToastRole = auth()->check() ? auth()->user()?->role : null;

    $roleAwareToastType = function (string $type, string $message) use ($currentToastRole): string {
        if ($type !== 'success') {
            return $type;
        }

        $isResident = $currentToastRole === 'resident';
        $isManagement = in_array($currentToastRole, ['manager', 'handyman'], true);
        $residentDestructive = '/\b(ticket|booking|post|comment|concern|request|reservation)\b.*\b(deleted|delete|removed|remove|cancelled|canceled|cancel|rejected|declined)\b|\b(deleted|delete|cancelled|canceled|rejected|declined)\b.*\b(ticket|booking|post|comment|concern|request|reservation)\b/i';
        $managementDestructive = '/\b(user|account|resident|staff|admin|ticket|booking|post|comment|announcement|concern|request|reservation)\b.*\b(deleted|delete|deactivated|disabled|archived|removed|remove|move-out|cancelled|canceled|cancel|rejected|declined)\b|\b(deleted|delete|deactivated|disabled|archived|removed|remove|cancelled|canceled|rejected|declined)\b.*\b(user|account|resident|staff|admin|ticket|booking|post|comment|announcement|concern|request|reservation)\b|^deleted\s+/i';

        if (($isResident && preg_match($residentDestructive, $message))
            || ($isManagement && preg_match($managementDestructive, $message))) {
            return 'warning';
        }

        return $type;
    };

    $toastPresentation = function (string $type, string $message): array {
        if ($type === 'error') {
            return ['title' => 'Error', 'detail' => $message];
        }

        if ($type === 'warning') {
            return ['title' => 'Warning', 'detail' => $message];
        }

        if ($type === 'info') {
            return ['title' => 'Notice', 'detail' => $message];
        }

        if (preg_match('/^(.+?[.!])(?:\s+(.+))$/', $message, $matches)) {
            return [
                'title' => rtrim($matches[1], '.!'),
                'detail' => $matches[2],
            ];
        }

        return ['title' => rtrim($message, '.!'), 'detail' => null];
    };

    $inlineStatusKeys = ['profile-updated', 'password-updated'];
    $statusMessage = session('status') && !in_array(session('status'), $inlineStatusKeys, true)
        ? session('status')
        : null;

    $validationErrors = $errors->any()
        ? collect($errors->all())->map(fn($msg) => ['type' => 'error', 'message' => $msg])->toArray()
        : [];

    $toastMessages = collect([
        session('success') ? ['type' => $roleAwareToastType('success', session('success')), 'message' => session('success')] : null,
        session('temporary_password') ? ['type' => 'warning', 'message' => 'Temporary password ready. Copy it from the account record before leaving this page.'] : null,
        session('error') ? ['type' => 'error', 'message' => session('error')] : null,
        session('warning') ? ['type' => 'warning', 'message' => session('warning')] : null,
        $statusMessage ? ['type' => $roleAwareToastType('info', $statusMessage), 'message' => $statusMessage] : null,
        ...$validationErrors,
    ])->filter()->map(function ($toast) use ($toastPresentation) {
        return array_merge($toast, $toastPresentation($toast['type'], $toast['message']));
    });
@endphp

<div id="app-nav-progress" aria-hidden="true"></div>

<div class="app-toast-stack" aria-live="polite" aria-atomic="true">
    @foreach($toastMessages as $toast)
        <div class="app-toast app-toast-{{ $toast['type'] }}" data-toast role="status">
            <span class="app-toast-icon" aria-hidden="true"></span>
            <div class="app-toast-copy">
                <strong>{{ $toast['title'] }}</strong>
                @if($toast['detail'])
                    <span>{{ $toast['detail'] }}</span>
                @endif
            </div>
            <button type="button" data-toast-close aria-label="Dismiss notification">×</button>
        </div>
    @endforeach
</div>

<div class="app-confirm-backdrop" data-confirm-modal aria-hidden="true">
    <section class="app-confirm-dialog" role="dialog" aria-modal="true" aria-labelledby="confirm-title">
        <div class="app-confirm-icon" aria-hidden="true">!</div>
        <div>
            <h2 id="confirm-title">Confirm Action</h2>
            <p data-confirm-message>This action needs confirmation.</p>
        </div>
        <div class="app-confirm-actions">
            <button type="button" class="app-confirm-btn app-confirm-btn-secondary" data-confirm-cancel>Cancel</button>
            <button type="button" class="app-confirm-btn app-confirm-btn-primary" data-confirm-accept>Continue</button>
        </div>
    </section>
</div>

<style>
@media not all {
    html.theme-light body.role-resident .role-brand-title, html.theme-light body.role-resident .role-notification-head strong, html.theme-light body.role-resident .role-notification-item strong, html.theme-light body.role-resident .role-mobile-notification-head strong, html.theme-light body.role-resident .role-mobile-notification-item strong {
        color: #2f271f;
    }
    html.theme-light body.role-resident .role-brand-subtitle, html.theme-light body.role-resident .role-mobile-notification-item span, html.theme-light body.role-resident .role-notification-empty, html.theme-light body.role-resident .role-mobile-notification-empty {
        color: #766856;
    }
    html.theme-light body.role-resident .role-nav-shell, html.theme-light body.role-resident .role-action-btn, html.theme-light body.role-resident .role-theme-toggle, html.theme-light body.role-resident .role-user-chip, html.theme-light body.role-resident .role-notification-btn, html.theme-light body.role-resident .role-mobile-toggle, html.theme-light body.role-resident .role-mobile-panel, html.theme-light body.role-resident .role-notification-panel {
        background: rgba(255, 253, 248, 0.86);
        border-color: rgba(76, 62, 46, 0.14);
        color: #40352a;
        box-shadow: 0 18px 36px rgba(94, 73, 45, 0.12);
    }
    html.theme-light body.role-resident .role-nav-link {
        color: #5c4b3b;
    }
    html.theme-light body.role-resident .role-nav-link:hover, html.theme-light body.role-resident .role-nav-link.is-active {
        background: rgba(185, 130, 47, 0.13);
        color: #2f271f;
    }
    html.theme-light body.role-resident .role-action-btn:hover {
        background: #ffffff;
        color: #2f271f;
    }
    html.theme-light body.role-resident .role-notification-item, html.theme-light body.role-resident .role-mobile-notification-block, html.theme-light body.role-resident .role-mobile-notification-item {
        background: rgba(80, 62, 42, 0.045);
        border-color: rgba(76, 62, 46, 0.10);
    }
    html.theme-light body.role-resident .resident-dashboard-shell, html.theme-light body.role-resident .resident-page, html.theme-light body.role-resident .resident-ticket-create-page, html.theme-light body.role-resident .resident-booking-create-page, html.theme-light body.role-resident .community-feed-page, html.theme-light body.role-resident .concern-page {
        color: #2f271f;
    }
    html.theme-light body.role-resident .resident-page-hero, html.theme-light body.role-resident .resident-ticket-create-hero, html.theme-light body.role-resident .resident-booking-create-hero, html.theme-light body.role-resident .community-feed-hero, html.theme-light body.role-resident .concern-hero {
        background: linear-gradient(120deg, rgba(255, 253, 248, 0.94) 0%, rgba(248, 240, 229, 0.94) 54%, rgba(235, 222, 205, 0.92) 100%) !important;
        border-color: rgba(76, 62, 46, 0.16) !important;
        box-shadow: 0 18px 38px rgba(94, 73, 45, 0.13) !important;
    }
    html.theme-light body.role-resident .resident-activity-card, html.theme-light body.role-resident .resident-surface-panel, html.theme-light body.role-resident .resident-page-panel, html.theme-light body.role-resident .resident-ticket-create-panel, html.theme-light body.role-resident .resident-booking-create-panel, html.theme-light body.role-resident .community-review-strip, html.theme-light body.role-resident .community-composer-card, html.theme-light body.role-resident .community-feed-card, html.theme-light body.role-resident .community-empty-state, html.theme-light body.role-resident .concern-card {
        background: rgba(255, 253, 248, 0.88) !important;
        border-color: rgba(76, 62, 46, 0.13) !important;
        box-shadow: 0 14px 30px rgba(94, 73, 45, 0.10) !important;
    }
    html.theme-light body.role-resident .resident-stack-item, html.theme-light body.role-resident .resident-notice-card, html.theme-light body.role-resident .resident-community-entry, html.theme-light body.role-resident .resident-card, html.theme-light body.role-resident .resident-meta-box, html.theme-light body.role-resident .resident-empty-state, html.theme-light body.role-resident .resident-ticket-upload-panel, html.theme-light body.role-resident .resident-ticket-priority-card, html.theme-light body.role-resident .resident-booking-slot, html.theme-light body.role-resident .community-review-card, html.theme-light body.role-resident .community-composer-trigger, html.theme-light body.role-resident .community-action-btn {
        background: rgba(80, 62, 42, 0.045) !important;
        border-color: rgba(76, 62, 46, 0.11) !important;
        color: #3a3026 !important;
    }
    html.theme-light body.role-resident .resident-home-kicker, html.theme-light body.role-resident .resident-page-kicker, html.theme-light body.role-resident .resident-ticket-create-kicker, html.theme-light body.role-resident .resident-booking-create-kicker, html.theme-light body.role-resident .community-feed-kicker, html.theme-light body.role-resident .concern-kicker, html.theme-light body.role-resident .resident-page-eyebrow, html.theme-light body.role-resident .resident-booking-create-eyebrow, html.theme-light body.role-resident .resident-ticket-create-chip, html.theme-light body.role-resident .concern-badge, html.theme-light body.role-resident .community-review-actions a, html.theme-light body.role-resident .community-review-actions button, html.theme-light body.role-resident .resident-card-links a, html.theme-light body.role-resident .resident-card-links button, html.theme-light body.role-resident .resident-see-more-btn, html.theme-light body.role-resident .resident-surface-head a, html.theme-light body.role-resident .resident-empty-state a, html.theme-light body.role-resident .community-empty-state a {
        color: #9b641d !important;
    }
    html.theme-light body.role-resident .resident-home-title, html.theme-light body.role-resident .resident-section-title, html.theme-light body.role-resident .resident-surface-head h2, html.theme-light body.role-resident .resident-surface-head h3, html.theme-light body.role-resident .resident-stack-item-row h3, html.theme-light body.role-resident .resident-notice-title h3, html.theme-light body.role-resident .resident-community-entry h3, html.theme-light body.role-resident .resident-page-title, html.theme-light body.role-resident .resident-page-panel-head h2, html.theme-light body.role-resident .resident-card-heading h3, html.theme-light body.role-resident .resident-day-heading, html.theme-light body.role-resident .resident-ticket-section-head h3, html.theme-light body.role-resident .resident-ticket-create-title, html.theme-light body.role-resident .resident-ticket-create-panel-head h2, html.theme-light body.role-resident .resident-ticket-upload-head h3, html.theme-light body.role-resident .resident-ticket-priority-name, html.theme-light body.role-resident .resident-booking-create-title, html.theme-light body.role-resident .resident-booking-create-head h2, html.theme-light body.role-resident .community-feed-title, html.theme-light body.role-resident .community-section-head h2, html.theme-light body.role-resident .community-review-top h3, html.theme-light body.role-resident .community-feed-copy h3, html.theme-light body.role-resident .community-feed-author-copy strong, html.theme-light body.role-resident .concern-title, html.theme-light body.role-resident .concern-card-head h2 {
        color: #2f271f !important;
    }
    html.theme-light body.role-resident .resident-home-subtitle, html.theme-light body.role-resident .resident-page-subtitle, html.theme-light body.role-resident .resident-ticket-create-subtitle, html.theme-light body.role-resident .resident-booking-create-subtitle, html.theme-light body.role-resident .community-feed-subtitle, html.theme-light body.role-resident .concern-subtitle, html.theme-light body.role-resident .resident-surface-head p, html.theme-light body.role-resident .resident-stack-item-row p, html.theme-light body.role-resident .resident-notice-card p, html.theme-light body.role-resident .resident-community-entry p, html.theme-light body.role-resident .resident-card-description, html.theme-light body.role-resident .resident-page-panel-head p, html.theme-light body.role-resident .resident-ticket-create-panel-head p, html.theme-light body.role-resident .resident-ticket-priority-copy, html.theme-light body.role-resident .resident-ticket-upload-head p, html.theme-light body.role-resident .resident-ticket-upload-note, html.theme-light body.role-resident .resident-booking-create-head p, html.theme-light body.role-resident .resident-booking-create-help, html.theme-light body.role-resident .community-section-head p, html.theme-light body.role-resident .community-review-top p, html.theme-light body.role-resident .community-feed-copy p, html.theme-light body.role-resident .community-feed-author-copy span, html.theme-light body.role-resident .community-feed-stats-row, html.theme-light body.role-resident .concern-card-head p {
        color: #655747 !important;
    }
    html.theme-light body.role-resident .resident-hero-stat, html.theme-light body.role-resident .resident-booking-create-stat, html.theme-light body.role-resident .community-feed-stat {
        background: rgba(255, 255, 255, 0.62) !important;
        border-color: rgba(76, 62, 46, 0.10) !important;
    }
    html.theme-light body.role-resident .resident-hero-stat span, html.theme-light body.role-resident .resident-booking-create-stat span, html.theme-light body.role-resident .community-feed-stat span, html.theme-light body.role-resident .resident-meta-box span, html.theme-light body.role-resident .resident-stack-meta, html.theme-light body.role-resident .resident-community-entry-time, html.theme-light body.role-resident .community-review-meta {
        color: #806f5c !important;
    }
    html.theme-light body.role-resident .resident-hero-stat strong, html.theme-light body.role-resident .resident-booking-create-stat strong, html.theme-light body.role-resident .community-feed-stat strong, html.theme-light body.role-resident .resident-meta-box strong {
        color: #2f271f !important;
    }
    html.theme-light body.role-resident .resident-ticket-create-input, html.theme-light body.role-resident .resident-ticket-create-input-file, html.theme-light body.role-resident .resident-booking-create-input, html.theme-light body.role-resident .concern-input {
        background: rgba(255, 255, 255, 0.86) !important;
        border-color: rgba(76, 62, 46, 0.16) !important;
        color: #2f271f !important;
    }
    html.theme-light body.role-resident .resident-ticket-create-label, html.theme-light body.role-resident .resident-booking-create-label, html.theme-light body.role-resident .concern-label {
        color: #574839 !important;
    }
    html.theme-light body.role-resident .resident-home-btn-secondary, html.theme-light body.role-resident .resident-ticket-create-btn-secondary, html.theme-light body.role-resident .resident-booking-create-btn-secondary, html.theme-light body.role-resident .concern-btn-secondary, html.theme-light body.role-resident .community-composer-secondary-action {
        background: rgba(255, 255, 255, 0.74) !important;
        border-color: rgba(76, 62, 46, 0.14) !important;
        color: #40352a !important;
    }
    html.theme-light body.role-resident .resident-status-chip-completed, html.theme-light body.role-resident .resident-badge-status-approved, html.theme-light body.role-resident .resident-badge-status-completed, html.theme-light body.role-resident .resident-badge-priority-low {
        background: rgba(76, 128, 76, 0.13) !important;
        color: #426f3d !important;
    }
    html.theme-light body.role-resident .resident-badge-status-rejected, html.theme-light body.role-resident .resident-badge-status-cancelled, html.theme-light body.role-resident .resident-badge-priority-critical, html.theme-light body.role-resident .community-status-chip-rejected {
        background: rgba(172, 70, 54, 0.12) !important;
        color: #9b3e31 !important;
    }
    html.theme-light body.role-resident .resident-status-chip-in_progress, html.theme-light body.role-resident .resident-badge-status-assigned, html.theme-light body.role-resident .resident-badge-status-in_progress {
        background: rgba(57, 111, 136, 0.13) !important;
        color: #336b83 !important;
    }
    @media (max-width:1200px) and (min-width:981px) {
        .role-nav-link {
            padding-inline: 13px;
            font-size: 0.86rem;
        }
    }
    body.role-resident .resident-page-hero, body.role-resident .resident-ticket-create-hero, body.role-resident .resident-booking-create-hero, body.role-resident .community-feed-hero, body.role-resident .concern-hero {
        padding: 20px 24px !important;
        border-radius: 24px !important;
        min-height: 0 !important;
        align-items: center !important;
    }
    body.role-resident .resident-page-title, body.role-resident .resident-ticket-create-title, body.role-resident .resident-booking-create-title, body.role-resident .community-feed-title, body.role-resident .concern-title {
        font-size: clamp(1.9rem, 3vw, 2.65rem) !important;
        line-height: 1.08 !important;
    }
    body.role-resident .resident-page-kicker, body.role-resident .resident-ticket-create-kicker, body.role-resident .resident-booking-create-kicker, body.role-resident .community-feed-kicker, body.role-resident .concern-kicker {
        margin-bottom: 7px !important;
        letter-spacing: 0.18em !important;
    }
    body.role-resident .resident-page-subtitle, body.role-resident .resident-ticket-create-subtitle, body.role-resident .resident-booking-create-subtitle, body.role-resident .community-feed-subtitle, body.role-resident .concern-subtitle {
        margin-top: 8px !important;
        max-width: 700px !important;
        font-size: 0.96rem !important;
        line-height: 1.55 !important;
    }
    body.role-resident .resident-hero-stat-row, body.role-resident .resident-booking-create-stats, body.role-resident .community-feed-stats {
        margin-top: 14px !important;
        gap: 10px !important;
    }
    body.role-resident .resident-hero-stat, body.role-resident .resident-booking-create-stat, body.role-resident .community-feed-stat {
        min-width: 96px !important;
        padding: 9px 12px !important;
        border-radius: 12px !important;
    }
    body.role-resident .resident-hero-stat strong, body.role-resident .community-feed-stat strong {
        font-size: 1.05rem !important;
    }
    body.role-resident .resident-page-btn, body.role-resident .resident-ticket-create-btn, body.role-resident .resident-booking-create-btn, body.role-resident .concern-btn {
        padding: 11px 18px !important;
    }
    body.role-resident .resident-ticket-create-page, body.role-resident .resident-booking-create-page, body.role-resident .resident-page, body.role-resident .community-feed-page, body.role-resident .concern-page {
        gap: 18px !important;
    }
    @media (max-width:768px) {
        body.role-resident .resident-page-hero, body.role-resident .resident-ticket-create-hero, body.role-resident .resident-booking-create-hero, body.role-resident .community-feed-hero, body.role-resident .concern-hero {
            padding: 18px !important;
            border-radius: 20px !important;
            align-items: flex-start !important;
        }
        body.role-resident .resident-page-title, body.role-resident .resident-ticket-create-title, body.role-resident .resident-booking-create-title, body.role-resident .community-feed-title, body.role-resident .concern-title {
            font-size: clamp(1.75rem, 8vw, 2.25rem) !important;
        }
    }
}
#app-nav-progress {
    position: fixed;
    top: 0;
    left: 0;
    height: 3px;
    width: 0%;
    opacity: 0;
    z-index: 9999;
    pointer-events: none;
    background: linear-gradient(90deg, #c49a6c, #d6a85b, #e8c278);
    border-radius: 0 2px 2px 0;
    box-shadow: 0 0 8px rgba(214, 168, 91, 0.5);
}
body.role-resident #app-nav-progress {
    background: linear-gradient(90deg, #b88740, #d6a85b, #e6bd6a);
}
body.role-handyman #app-nav-progress {
    background: linear-gradient(90deg, #4a8aae, #5c9fc0, #78b8d4);
    box-shadow: 0 0 8px rgba(88, 135, 165, 0.5);
}
.app-toast-stack {
    position: fixed;
    top: 72px;
    right: 20px;
    z-index: 9999;
    display: grid;
    gap: 10px;
    width: min(360px, calc(100vw - 32px));
    pointer-events: none;
}
.resident-back-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    min-height: 42px;
    padding: 10px 16px;
    border: 1px solid rgba(214, 168, 91, 0.22);
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.05);
    color: #f0e9df;
    font-size: 0.9rem;
    font-weight: 700;
    text-decoration: none;
    transition: background 0.18s ease, border-color 0.18s ease, transform 0.18s ease;
}
.resident-back-link::before {
    content: '\2190';
    font-size: 1rem;
    line-height: 1;
}
.resident-back-link:hover {
    border-color: rgba(214, 168, 91, 0.42);
    background: rgba(214, 168, 91, 0.1);
    transform: translateY(-1px);
}
.resident-create-back::before {
    content: none;
}
body.role-resident .resident-ticket-hero, body.role-resident .resident-ticket-edit-hero, body.role-resident .resident-ticket-create-hero, body.role-resident .resident-booking-hero, body.role-resident .resident-booking-edit-hero, body.role-resident .resident-booking-create-hero, body.role-resident .resident-announcement-hero {
    align-items: center;
}
body.role-resident .resident-ticket-hero-actions, body.role-resident .resident-ticket-edit-actions, body.role-resident .resident-ticket-create-hero-actions, body.role-resident .resident-booking-hero-actions, body.role-resident .resident-booking-edit-actions, body.role-resident .resident-booking-create-actions, body.role-resident .resident-announcement-hero-actions {
    align-self: center;
}
.app-toast {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 15px 18px;
    border-radius: 12px;
    border: none;
    background: #3b82f6;
    color: #ffffff;
    box-shadow: 0 6px 24px rgba(59, 130, 246, 0.40);
    pointer-events: auto;
    cursor: pointer;
    transform: translateY(-12px);
    opacity: 0;
    animation: toast-in 0.28s cubic-bezier(0.22, 1, 0.36, 1) forwards;
}
.app-toast-copy {
    display: flex;
    flex-direction: column;
    gap: 2px;
    min-width: 0;
    flex: 1;
}
.app-toast-copy strong {
    color: #ffffff;
    font-size: 0.95rem;
    font-weight: 700;
    line-height: 1.3;
}
.app-toast-copy span {
    color: rgba(255, 255, 255, 0.82);
    font-size: 0.82rem;
    font-weight: 400;
    line-height: 1.4;
}
.app-toast button {
    border: none;
    background: rgba(255, 255, 255, 0.22);
    color: #ffffff;
    cursor: pointer;
    font-size: 1rem;
    line-height: 1;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: background 0.15s ease;
}
.app-toast button:hover {
    background: rgba(255, 255, 255, 0.36);
}
.app-toast-icon {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.25);
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.88rem;
    font-weight: 900;
    color: #ffffff;
}
.app-toast-icon::after {
    content: 'i';
    font-style: italic;
}
/* Success — green */
.app-toast-success {
    background: #16a34a;
    box-shadow: 0 6px 24px rgba(22, 163, 74, 0.40);
}
.app-toast-success .app-toast-icon::after {
    content: '✓';
    font-style: normal;
    font-size: 1rem;
}
/* Error — red */
.app-toast-error {
    background: #dc2626;
    box-shadow: 0 6px 24px rgba(220, 38, 38, 0.40);
}
.app-toast-error .app-toast-icon::after {
    content: '✕';
    font-style: normal;
}
/* Warning — amber */
.app-toast-warning {
    background: #d97706;
    box-shadow: 0 6px 24px rgba(217, 119, 6, 0.40);
}
.app-toast-warning .app-toast-icon::after {
    content: '!';
    font-style: normal;
    font-weight: 900;
}
.app-toast.is-leaving {
    animation: toast-out 0.22s ease forwards;
}
@keyframes toast-in {
    from {
        opacity: 0;
        transform: translateY(-12px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
@keyframes toast-out {
    to {
        opacity: 0;
        transform: translateY(-8px);
    }
}
.feature-skeleton-line, .feature-skeleton-pill, .feature-skeleton-avatar, .feature-skeleton-box, .feature-skeleton-button {
    display: block;
    border-radius: 999px;
    background: linear-gradient(90deg, rgba(255, 255, 255, 0.06), rgba(255, 255, 255, 0.16), rgba(255, 255, 255, 0.06));
    background-size: 220% 100%;
    animation: skeleton-shimmer 1.15s ease-in-out infinite;
}
[data-feature-skeleton] {
    display: none;
}
html.is-loading [data-feature-skeleton] {
    display: grid;
}
html.is-loading [data-skeleton-content] {
    display: none !important;
}
.feature-skeleton-stack {
    gap: 14px;
}
.feature-skeleton-card {
    padding: 20px 22px;
    border-radius: 16px;
    border: 1px solid rgba(255, 255, 255, 0.05);
    background: rgba(255, 255, 255, 0.03);
    min-height: 178px;
}
.resident-card.feature-skeleton-card, .community-feed-card.feature-skeleton-card {
    display: block;
}
.resident-card-schedule.feature-skeleton-card {
    min-height: 148px;
}
.community-feed-card.feature-skeleton-card {
    min-height: 256px;
}
.feature-skeleton-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 18px;
}
.feature-skeleton-title-row {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}
.feature-skeleton-line {
    height: 12px;
    margin-top: 10px;
}
.feature-skeleton-line.title {
    width: min(280px, 64vw);
    height: 18px;
    margin-top: 0;
}
.feature-skeleton-line.short {
    width: 42%;
}
.feature-skeleton-line.medium {
    width: 68%;
}
.feature-skeleton-line.long {
    width: 88%;
}
.feature-skeleton-pill {
    width: 92px;
    height: 24px;
}
.feature-skeleton-actions {
    display: flex;
    gap: 10px;
    flex-shrink: 0;
}
.feature-skeleton-button {
    width: 104px;
    height: 34px;
    border-radius: 999px;
}
.feature-skeleton-meta {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
    margin-top: 18px;
}
.feature-skeleton-box {
    height: 64px;
    border-radius: 14px;
}
.feature-skeleton-day {
    width: 170px;
    height: 24px;
    margin: 2px auto 14px;
}
.feature-skeleton-post-head {
    display: flex;
    align-items: center;
    gap: 12px;
}
.feature-skeleton-avatar {
    width: 52px;
    height: 52px;
    flex-shrink: 0;
}
.feature-skeleton-post-actions {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px;
    margin-top: 16px;
    padding-top: 12px;
    border-top: 1px solid rgba(255, 255, 255, 0.05);
}
.feature-skeleton-post-actions .feature-skeleton-box {
    height: 44px;
    border-radius: 12px;
}
@keyframes skeleton-shimmer {
    0% {
        background-position: 120% 0;
    }
    100% {
        background-position: -120% 0;
    }
}
.app-confirm-backdrop {
    position: fixed;
    inset: 0;
    z-index: 130;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 18px;
    background: rgba(12, 10, 8, 0.52);
    backdrop-filter: blur(5px);
}
.app-confirm-backdrop.is-active {
    display: flex;
}
.app-confirm-dialog {
    width: min(440px, 100%);
    display: grid;
    grid-template-columns: auto 1fr;
    gap: 16px;
    padding: 22px;
    border-radius: 22px;
    border: 1px solid rgba(214, 168, 91, 0.18);
    background: rgba(30, 27, 23, 0.98);
    box-shadow: 0 26px 70px rgba(0, 0, 0, 0.32);
}
.app-confirm-icon {
    width: 42px;
    height: 42px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 14px;
    background: rgba(214, 168, 91, 0.13);
    color: #d6a85b;
    font-weight: 900;
}
.app-confirm-dialog h2 {
    margin: 0;
    color: #f0e9df;
    font-size: 1.12rem;
}
.app-confirm-dialog p {
    margin: 8px 0 0;
    color: #c4b8a8;
    line-height: 1.6;
}
.app-confirm-actions {
    grid-column: 1 / -1;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 4px;
}
.app-confirm-btn {
    border: 1px solid rgba(214, 168, 91, 0.16);
    border-radius: 999px;
    padding: 11px 18px;
    font-weight: 800;
    cursor: pointer;
}
.app-confirm-btn-primary {
    background: linear-gradient(135deg, #c79745 0%, #d6a85b 100%);
    color: #1a1714;
}
.app-confirm-btn-secondary {
    background: rgba(255, 255, 255, 0.04);
    color: #e8e0d3;
}
.resident-filter-bar, .community-filter-bar {
    display: grid;
    grid-template-columns: minmax(220px, 1fr) repeat(2, minmax(150px, 0.35fr));
    gap: 12px;
    margin-bottom: 18px;
}
.community-filter-bar {
    grid-template-columns: minmax(220px, 1fr) minmax(150px, 0.35fr);
}
.resident-filter-input, .resident-filter-select, .community-filter-input, .community-filter-select {
    width: 100%;
    min-height: 44px;
    border-radius: 14px;
    border: 1px solid rgba(214, 168, 91, 0.14);
    background: rgba(37, 39, 42, 0.90);
    color: #f8f3ea;
    padding: 0 14px;
    font: inherit;
    outline: none;
}
.resident-filter-input:focus, .resident-filter-select:focus, .community-filter-input:focus, .community-filter-select:focus {
    border-color: rgba(214, 168, 91, 0.38);
    box-shadow: 0 0 0 4px rgba(214, 168, 91, 0.08);
}
.resident-filter-empty, .community-filter-empty {
    display: none;
    padding: 22px;
    border-radius: 18px;
    text-align: center;
    color: #b8ab98;
    background: rgba(255, 255, 255, 0.03);
    border: 1px dashed rgba(214, 168, 91, 0.18);
}
.resident-filter-empty.is-visible, .community-filter-empty.is-visible {
    display: block;
}
.app-button-spinner {
    width: 15px;
    height: 15px;
    flex: 0 0 auto;
    border-radius: 999px;
    border: 2px solid currentColor;
    border-right-color: transparent;
    animation: app-button-spin 0.7s linear infinite;
}
@keyframes app-button-spin {
    to {
        transform: rotate(360deg);
    }
}
body.role-resident .resident-flash[data-auto-dismiss], body.role-resident .community-feedback-banner[data-auto-dismiss] {
    display: none !important;
}
body.role-resident .resident-empty-state::before, body.role-resident .community-empty-state::before {
    content: "";
    width: 42px;
    height: 42px;
    display: inline-flex;
    margin: 0 auto 12px;
    border-radius: 14px;
    background: linear-gradient(135deg, rgba(214, 168, 91, 0.24), rgba(214, 168, 91, 0.08)), radial-gradient(circle at center, rgba(255, 255, 255, 0.12), transparent 55%);
    border: 1px solid rgba(214, 168, 91, 0.18);
}
/* Solid-color toasts look identical in light and dark themes — no overrides needed */
html.theme-light body.role-resident .app-confirm-dialog {
    background: rgba(255, 253, 248, 0.98);
    border-color: rgba(76, 62, 46, 0.14);
    color: #2f271f;
    box-shadow: 0 22px 54px rgba(94, 73, 45, 0.16);
}
html.theme-light body.role-resident .app-confirm-backdrop {
    background: rgba(52, 40, 25, 0.22);
}
html.theme-light body.role-resident .app-confirm-dialog h2 {
    color: #2f271f;
}
html.theme-light body.role-resident .app-confirm-dialog p, html.theme-light body.role-resident .resident-filter-empty, html.theme-light body.role-resident .community-filter-empty {
    color: #655747;
}
html.theme-light body.role-resident .resident-filter-input, html.theme-light body.role-resident .resident-filter-select, html.theme-light body.role-resident .community-filter-input, html.theme-light body.role-resident .community-filter-select {
    background: rgba(255, 255, 255, 0.86);
    border-color: rgba(76, 62, 46, 0.16);
    color: #2f271f;
}
html.theme-light body.role-resident .feature-skeleton-card {
    background: rgba(80, 62, 42, 0.045);
    border-color: rgba(76, 62, 46, 0.11);
}
html.theme-light body.role-resident .feature-skeleton-line, html.theme-light body.role-resident .feature-skeleton-pill, html.theme-light body.role-resident .feature-skeleton-avatar, html.theme-light body.role-resident .feature-skeleton-box, html.theme-light body.role-resident .feature-skeleton-button {
    background: linear-gradient(90deg, rgba(80, 62, 42, 0.06), rgba(80, 62, 42, 0.16), rgba(80, 62, 42, 0.06));
    background-size: 220% 100%;
}
@media (max-width:760px) {
    .resident-filter-bar, .community-filter-bar {
        grid-template-columns: 1fr;
    }
    .app-toast-stack {
        top: 64px;
        right: 12px;
    }
    .app-confirm-dialog {
        grid-template-columns: 1fr;
    }
    .app-confirm-actions {
        display: grid;
        grid-template-columns: 1fr;
    }
    .feature-skeleton-top, .feature-skeleton-actions {
        flex-direction: column;
        align-items: stretch;
    }
    .feature-skeleton-meta, .feature-skeleton-post-actions {
        grid-template-columns: 1fr;
    }
}
@media not all {
    /* Resident consistency pass:keeps resident pages using one palette despite page-local CSS. */
    body.role-resident {
        --resident-page-title: #f3ece2;
        --resident-page-body: #d2c8bb;
        --resident-page-muted: #a29382;
        --resident-accent: #e0ad55;
        --resident-accent-soft: rgba(224, 173, 85, 0.14);
        --resident-border: rgba(224, 173, 85, 0.18);
        --resident-border-soft: rgba(255, 244, 225, 0.09);
        --resident-border-strong: rgba(224, 173, 85, 0.24);
        --resident-radius-panel: 22px;
        --resident-radius-card: 16px;
        --resident-radius-control: 14px;
        --resident-radius-pill: 999px;
        --resident-hero: linear-gradient(115deg, #1b1e1e 0%, #252727 48%, #2b2924 72%, #372a19 100%);
        --resident-surface: linear-gradient(180deg, rgba(42, 44, 42, 0.94) 0%, rgba(34, 36, 35, 0.94) 100%);
        --resident-surface-inner: rgba(255, 244, 225, 0.055);
        --resident-input: rgba(21, 23, 23, 0.94);
        --resident-shadow: 0 18px 42px rgba(0, 0, 0, 0.24);
        --resident-success-bg: rgba(111, 160, 111, 0.16);
        --resident-success-text: #a8d39a;
        --resident-danger-bg: rgba(224, 112, 96, 0.14);
        --resident-danger-text: #f0b3a9;
        --resident-info-bg: rgba(104, 145, 171, 0.14);
        --resident-info-text: #a9c9dc;
        --resident-warning-bg: rgba(224, 173, 85, 0.16);
        --resident-warning-text: #e9c783;
    }
    html.theme-light body.role-resident {
        --resident-page-title: #2f271f;
        --resident-page-body: #5d5043;
        --resident-page-muted: #806f5c;
        --resident-accent: #9b641d;
        --resident-accent-soft: rgba(185, 130, 47, 0.13);
        --resident-border: rgba(76, 62, 46, 0.14);
        --resident-border-soft: rgba(76, 62, 46, 0.10);
        --resident-border-strong: rgba(185, 130, 47, 0.22);
        --resident-hero: linear-gradient(120deg, rgba(255, 253, 248, 0.94) 0%, rgba(248, 240, 229, 0.94) 54%, rgba(235, 222, 205, 0.92) 100%);
        --resident-surface: rgba(255, 253, 248, 0.88);
        --resident-surface-inner: rgba(80, 62, 42, 0.045);
        --resident-input: rgba(255, 255, 255, 0.86);
        --resident-shadow: 0 14px 30px rgba(94, 73, 45, 0.10);
        --resident-success-bg: rgba(76, 128, 76, 0.13);
        --resident-success-text: #426f3d;
        --resident-danger-bg: rgba(172, 70, 54, 0.12);
        --resident-danger-text: #9b3e31;
        --resident-info-bg: rgba(57, 111, 136, 0.13);
        --resident-info-text: #336b83;
        --resident-warning-bg: rgba(185, 130, 47, 0.13);
        --resident-warning-text: #7c541d;
    }
    body.role-resident .resident-page-hero, body.role-resident .resident-ticket-create-hero, body.role-resident .resident-booking-create-hero, body.role-resident .resident-booking-hero, body.role-resident .resident-ticket-hero, body.role-resident .community-feed-hero, body.role-resident .community-post-hero, body.role-resident .concern-hero {
        background: var(--resident-hero) !important;
        border-color: var(--resident-border) !important;
        border-width: 1px !important;
        border-style: solid !important;
        border-radius: var(--resident-radius-panel) !important;
        box-shadow: var(--resident-shadow) !important;
    }
    body.role-resident .resident-activity-card, body.role-resident .resident-surface-panel, body.role-resident .resident-page-panel, body.role-resident .resident-ticket-create-panel, body.role-resident .resident-booking-create-panel, body.role-resident .resident-ticket-panel, body.role-resident .resident-booking-panel, body.role-resident .resident-booking-detail-panel, body.role-resident .community-review-strip, body.role-resident .community-composer-card, body.role-resident .community-feed-card, body.role-resident .community-post-panel, body.role-resident .community-comment-card, body.role-resident .community-empty-state, body.role-resident .concern-card, body.role-resident .concern-alert-context {
        background: var(--resident-surface) !important;
        border-color: var(--resident-border) !important;
        border-width: 1px !important;
        border-style: solid !important;
        border-radius: var(--resident-radius-panel) !important;
        color: var(--resident-page-title) !important;
        box-shadow: var(--resident-shadow) !important;
    }
    body.role-resident .resident-card, body.role-resident .resident-stack-item, body.role-resident .resident-notice-card, body.role-resident .resident-community-entry, body.role-resident .resident-meta-box, body.role-resident .resident-ticket-upload-panel, body.role-resident .resident-ticket-priority-card, body.role-resident .resident-booking-slot, body.role-resident .community-review-card, body.role-resident .community-composer-trigger, body.role-resident .community-action-btn, body.role-resident .community-comment, body.role-resident .resident-empty-state, body.role-resident .resident-filter-empty, body.role-resident .community-filter-empty {
        background: var(--resident-surface-inner) !important;
        border-color: var(--resident-border-soft) !important;
        border-width: 1px !important;
        border-style: solid !important;
        border-radius: var(--resident-radius-card) !important;
        color: var(--resident-page-title) !important;
    }
    body.role-resident .resident-empty-state, body.role-resident .community-empty-state, body.role-resident .resident-filter-empty, body.role-resident .community-filter-empty {
        border-style: dashed !important;
        border-color: var(--resident-border-strong) !important;
    }
    body.role-resident .resident-home-title, body.role-resident .resident-section-title, body.role-resident .resident-page-title, body.role-resident .resident-ticket-create-title, body.role-resident .resident-booking-create-title, body.role-resident .resident-booking-title, body.role-resident .resident-ticket-title, body.role-resident .community-feed-title, body.role-resident .community-post-title, body.role-resident .concern-title, body.role-resident .resident-surface-head h2, body.role-resident .resident-page-panel-head h2, body.role-resident .resident-ticket-create-panel-head h2, body.role-resident .resident-booking-create-head h2, body.role-resident .resident-card-heading h3, body.role-resident .resident-stack-item-row h3, body.role-resident .resident-notice-title h3, body.role-resident .resident-community-entry h3, body.role-resident .community-section-head h2, body.role-resident .community-review-top h3, body.role-resident .community-feed-copy h3, body.role-resident .community-feed-author-copy strong, body.role-resident .concern-card-head h2 {
        color: var(--resident-page-title) !important;
    }
    body.role-resident .resident-home-subtitle, body.role-resident .resident-page-subtitle, body.role-resident .resident-ticket-create-subtitle, body.role-resident .resident-booking-create-subtitle, body.role-resident .resident-booking-subtitle, body.role-resident .resident-ticket-subtitle, body.role-resident .community-feed-subtitle, body.role-resident .community-post-subtitle, body.role-resident .concern-subtitle, body.role-resident .resident-card-description, body.role-resident .resident-stack-item-row p, body.role-resident .resident-notice-card p, body.role-resident .resident-community-entry p, body.role-resident .community-feed-copy p, body.role-resident .community-review-top p, body.role-resident .concern-card-head p {
        color: var(--resident-page-body) !important;
    }
    body.role-resident .resident-surface-head p, body.role-resident .resident-page-panel-head p, body.role-resident .resident-meta-box span, body.role-resident .resident-stack-meta, body.role-resident .resident-community-entry-time, body.role-resident .resident-hero-stat span, body.role-resident .resident-booking-create-stat span, body.role-resident .community-feed-stat span, body.role-resident .community-feed-author-copy span, body.role-resident .community-feed-stats-row, body.role-resident .community-review-meta, body.role-resident .resident-ticket-upload-note, body.role-resident .resident-booking-create-help {
        color: var(--resident-page-muted) !important;
    }
    body.role-resident .resident-home-kicker, body.role-resident .resident-page-kicker, body.role-resident .resident-ticket-create-kicker, body.role-resident .resident-booking-create-kicker, body.role-resident .resident-booking-kicker, body.role-resident .resident-ticket-kicker, body.role-resident .community-feed-kicker, body.role-resident .community-post-kicker, body.role-resident .concern-kicker, body.role-resident .resident-page-eyebrow, body.role-resident .resident-booking-create-eyebrow, body.role-resident .resident-ticket-create-chip, body.role-resident .concern-badge, body.role-resident .resident-surface-head a, body.role-resident .resident-see-more-btn, body.role-resident .resident-empty-state a, body.role-resident .community-empty-state a, body.role-resident .community-review-actions a, body.role-resident .community-review-actions button, body.role-resident .resident-card-links a, body.role-resident .resident-card-links button {
        color: var(--resident-accent) !important;
    }
    body.role-resident .resident-page-btn-primary, body.role-resident .resident-ticket-create-btn-primary, body.role-resident .resident-booking-create-btn-primary, body.role-resident .community-composer-actions a:not(.community-composer-secondary-action), body.role-resident .community-pagination-link a, body.role-resident .concern-btn-primary, body.role-resident .resident-home-btn-primary {
        background: linear-gradient(95deg, #c9953f, #e0ad55) !important;
        color: #17120d !important;
        border-color: rgba(224, 173, 85, 0.28) !important;
    }
    body.role-resident .resident-home-btn-secondary, body.role-resident .resident-ticket-create-btn-secondary, body.role-resident .resident-booking-create-btn-secondary, body.role-resident .resident-page-btn-secondary, body.role-resident .community-composer-secondary-action, body.role-resident .concern-btn-secondary {
        background: var(--resident-surface-inner) !important;
        color: var(--resident-page-title) !important;
        border-color: var(--resident-border) !important;
    }
    body.role-resident .resident-ticket-create-input, body.role-resident .resident-ticket-create-input-file, body.role-resident .resident-booking-create-input, body.role-resident .concern-input, body.role-resident .resident-filter-input, body.role-resident .resident-filter-select, body.role-resident .community-filter-input, body.role-resident .community-filter-select, body.role-resident .community-post-input, body.role-resident .community-comment-input {
        background: var(--resident-input) !important;
        border-color: var(--resident-border) !important;
        border-width: 1px !important;
        border-style: solid !important;
        border-radius: var(--resident-radius-control) !important;
        color: var(--resident-page-title) !important;
    }
    body.role-resident .resident-surface-divider, body.role-resident .resident-page-divider, body.role-resident .resident-ticket-create-divider, body.role-resident .resident-booking-create-divider, body.role-resident .community-post-divider {
        background: linear-gradient(to right, var(--resident-border), rgba(224, 173, 85, 0.06), transparent) !important;
    }
    body.role-resident .resident-badge-status-approved, body.role-resident .resident-badge-status-completed, body.role-resident .resident-status-chip-completed, body.role-resident .resident-badge-priority-low {
        background: var(--resident-success-bg) !important;
        color: var(--resident-success-text) !important;
    }
    body.role-resident .resident-badge-status-rejected, body.role-resident .resident-badge-status-cancelled, body.role-resident .resident-badge-priority-critical, body.role-resident .community-status-chip-rejected, body.role-resident .resident-flash-error, body.role-resident .concern-alert-error, body.role-resident .community-review-note {
        background: var(--resident-danger-bg) !important;
        border-color: rgba(224, 112, 96, 0.20) !important;
        color: var(--resident-danger-text) !important;
    }
    body.role-resident .resident-badge-status-assigned, body.role-resident .resident-badge-status-in_progress, body.role-resident .resident-status-chip-in_progress {
        background: var(--resident-info-bg) !important;
        color: var(--resident-info-text) !important;
    }
    body.role-resident .resident-badge-status-received, body.role-resident .resident-badge-status-pending_approval, body.role-resident .resident-badge-status-pending, body.role-resident .resident-badge-priority-medium, body.role-resident .community-status-chip-pending, body.role-resident .resident-status-chip-pending {
        background: var(--resident-warning-bg) !important;
        color: var(--resident-warning-text) !important;
    }
    body.role-resident .resident-page-btn, body.role-resident .resident-home-btn, body.role-resident .resident-ticket-create-btn, body.role-resident .resident-booking-create-btn, body.role-resident .concern-btn, body.role-resident .community-composer-actions a, body.role-resident .community-pagination-link a, body.role-resident .community-action-btn, body.role-resident .community-review-actions a, body.role-resident .community-review-actions button, body.role-resident .resident-card-links a, body.role-resident .resident-card-links button {
        border-width: 1px !important;
        border-style: solid !important;
        border-radius: var(--resident-radius-pill) !important;
    }
    body.role-resident .resident-badge, body.role-resident .resident-status-chip, body.role-resident .community-status-chip, body.role-resident .resident-ticket-create-chip, body.role-resident .resident-page-eyebrow, body.role-resident .concern-badge {
        border-width: 1px !important;
        border-style: solid !important;
        border-color: color-mix(in srgb, var(--resident-accent) 26%, transparent) !important;
        border-radius: var(--resident-radius-pill) !important;
    }
    body.role-resident .resident-ticket-thumb-link, body.role-resident .resident-ticket-thumb, body.role-resident .community-feed-media, body.role-resident .community-review-media, body.role-resident .resident-ticket-preview-media {
        border-width: 1px !important;
        border-style: solid !important;
        border-color: var(--resident-border-soft) !important;
        border-radius: var(--resident-radius-card) !important;
    }
    body.role-resident .resident-card:hover, body.role-resident .resident-stack-item:hover, body.role-resident .resident-community-entry:hover, body.role-resident .community-feed-card:hover, body.role-resident .community-review-card:hover {
        border-color: var(--resident-border-strong) !important;
    }
    /* Resident redesign experiment:calmer system-style layout with less marketing weight. */
    body.role-resident .top-bg-image-layer {
        height: 420px;
        opacity: 0.58;
        filter: saturate(0.86) contrast(0.96);
        mask-image: linear-gradient(to bottom, black 34%, transparent 100%);
    }
    html.theme-light body.role-resident .top-bg-image-layer {
        height:540px;
        background-image:url('{{ asset('White1.jpg') }}');
        background-repeat: no-repeat;
        background-position: top center;
        background-size: auto 100%;
        opacity: 0.92;
        filter: saturate(1.02) contrast(1);
        mask-image: linear-gradient(to bottom, black 42%, transparent 100%), linear-gradient(to right, transparent 0%, black 34%);
        mask-composite: intersect;
        -webkit-mask-image: linear-gradient(to bottom, black 42%, transparent 100%), linear-gradient(to right, transparent 0%, black 34%);
        -webkit-mask-composite: source-in;
    }
    body.role-resident .top-bg-image-layer::after {
        background: linear-gradient(180deg, rgba(20, 21, 21, 0.18) 0%, rgba(20, 21, 21, 0.74) 70%, rgba(20, 21, 21, 0.98) 100%), linear-gradient(90deg, rgba(20, 21, 21, 0.86) 0%, rgba(20, 21, 21, 0.58) 34%, rgba(20, 21, 21, 0.18) 70%, rgba(20, 21, 21, 0.04) 100%);
    }
    html.theme-light body.role-resident .top-bg-image-layer::after {
        background: linear-gradient(180deg, rgba(251, 247, 240, 0.08) 0%, rgba(251, 247, 240, 0.28) 58%, rgba(251, 247, 240, 0.92) 100%), linear-gradient(90deg, rgba(251, 247, 240, 0.88) 0%, rgba(251, 247, 240, 0.58) 32%, rgba(251, 247, 240, 0.16) 66%, rgba(251, 247, 240, 0) 100%);
    }
    body.role-resident .role-topbar-wrap {
        max-width: 1500px;
        padding-top: 14px;
    }
    body.role-resident .role-topbar {
        padding: 8px 0;
    }
    body.role-resident .role-brand-title {
        font-size: 1.45rem;
        letter-spacing: 0;
    }
    body.role-resident .role-brand-subtitle {
        font-size: 0.64rem;
        letter-spacing: 0.18em;
    }
    body.role-resident .role-nav-shell {
        padding: 5px;
        gap: 4px;
        border-radius: 18px;
    }
    body.role-resident .role-nav-link {
        padding: 10px 14px;
        border-radius: 14px;
        font-size: 0.86rem;
    }
    body.role-resident .role-action-btn, body.role-resident .role-theme-toggle, body.role-resident .role-notification-btn, body.role-resident .role-user-chip {
        height: 42px;
        min-height: 42px;
    }
    body.role-resident .role-theme-toggle, body.role-resident .role-notification-btn, body.role-resident .role-user-chip {
        width: 42px;
    }
    body.role-resident .app-main {
        max-width: 1500px;
        padding-top: 18px;
    }
    body.role-resident .app-main.full-bleed {
        max-width: 1500px;
    }
    body.role-resident .resident-dashboard-shell, body.role-resident .resident-page, body.role-resident .resident-ticket-create-page, body.role-resident .resident-booking-create-page, body.role-resident .community-feed-page, body.role-resident .concern-page, body.role-resident .resident-ticket-page, body.role-resident .resident-booking-page, body.role-resident .community-post-page {
        max-width: 1500px !important;
        gap: 16px !important;
    }
    body.role-resident .community-feed-page, body.role-resident .concern-page {
        width: min(100%, 1180px) !important;
    }
    body.role-resident .resident-home-hero {
        min-height: 0;
        margin-top: 0;
    }
    body.role-resident .resident-home-hero-content {
        max-width: 860px;
        padding: 28px 0 10px;
    }
    body.role-resident .resident-home-kicker {
        margin-bottom: 8px;
        font-family: 'Inter', sans-serif;
        font-size: 0.82rem;
        font-weight: 800;
        letter-spacing: 0.14em;
        text-transform: uppercase;
    }
    body.role-resident .resident-home-title {
        max-width: 780px;
        font-size: clamp(2.35rem, 4vw, 3.65rem) !important;
        line-height: 1.02 !important;
        letter-spacing: 0 !important;
    }
    body.role-resident .resident-home-subtitle {
        max-width: 680px;
        margin-top: 10px;
        font-size: 1rem;
        line-height: 1.55;
    }
    body.role-resident .resident-home-actions {
        gap: 10px;
        margin-top: 20px;
    }
    body.role-resident .resident-home-btn, body.role-resident .resident-page-btn, body.role-resident .resident-ticket-create-btn, body.role-resident .resident-booking-create-btn, body.role-resident .concern-btn {
        min-height: 42px;
        padding: 11px 16px !important;
        font-size: 0.86rem !important;
        border-radius: 12px !important;
    }
    body.role-resident .resident-home-btn {
        min-width: 184px;
    }
    body.role-resident .resident-section-title {
        margin-bottom: 10px;
        font-family: 'Inter', sans-serif;
        font-size: 1rem;
        font-weight: 800;
    }
    body.role-resident .resident-activity-grid {
        gap: 12px;
    }
    body.role-resident .resident-activity-card {
        min-height: 88px;
        padding: 16px 18px !important;
        border-radius: 16px !important;
    }
    body.role-resident .resident-activity-card-icon {
        width: 46px;
        height: 46px;
        border-radius: 12px;
    }
    body.role-resident .resident-activity-card-top strong {
        font-size: 0.95rem;
    }
    body.role-resident .resident-activity-card-copy p {
        font-size: 0.84rem;
    }
    body.role-resident .resident-page-hero, body.role-resident .resident-ticket-create-hero, body.role-resident .resident-booking-create-hero, body.role-resident .resident-ticket-hero, body.role-resident .resident-booking-hero, body.role-resident .community-feed-hero, body.role-resident .community-post-hero, body.role-resident .concern-hero {
        padding: 18px 20px !important;
        border-radius: 18px !important;
        align-items: center !important;
    }
    body.role-resident .resident-page-title, body.role-resident .resident-ticket-create-title, body.role-resident .resident-booking-create-title, body.role-resident .resident-ticket-title, body.role-resident .resident-booking-title, body.role-resident .community-feed-title, body.role-resident .community-post-title, body.role-resident .concern-title {
        font-size: clamp(1.65rem, 2.35vw, 2.15rem) !important;
        line-height: 1.08 !important;
        letter-spacing: 0 !important;
    }
    body.role-resident .resident-page-subtitle, body.role-resident .resident-ticket-create-subtitle, body.role-resident .resident-booking-create-subtitle, body.role-resident .resident-ticket-subtitle, body.role-resident .resident-booking-subtitle, body.role-resident .community-feed-subtitle, body.role-resident .community-post-subtitle, body.role-resident .concern-subtitle {
        max-width: 720px;
        margin-top: 7px !important;
        font-size: 0.9rem !important;
        line-height: 1.48 !important;
    }
    body.role-resident .resident-hero-stat-row, body.role-resident .resident-ticket-hero-stats, body.role-resident .resident-booking-create-stats, body.role-resident .community-feed-stats {
        margin-top: 12px !important;
        gap: 8px !important;
    }
    body.role-resident .resident-hero-stat, body.role-resident .resident-ticket-hero-stat, body.role-resident .resident-booking-create-stat, body.role-resident .community-feed-stat {
        min-width: 92px !important;
        padding: 8px 10px !important;
        border-radius: 12px !important;
    }
    body.role-resident .resident-hero-stat strong, body.role-resident .resident-ticket-hero-stat strong, body.role-resident .resident-booking-create-stat strong, body.role-resident .community-feed-stat strong {
        font-size: 1rem !important;
    }
    body.role-resident .resident-content-grid {
        grid-template-columns: minmax(0, 1.5fr) minmax(320px, 0.6fr);
        gap: 14px;
    }
    body.role-resident .resident-surface-panel, body.role-resident .resident-page-panel, body.role-resident .resident-ticket-create-panel, body.role-resident .resident-booking-create-panel, body.role-resident .resident-ticket-panel, body.role-resident .resident-booking-panel, body.role-resident .community-review-strip, body.role-resident .community-composer-card, body.role-resident .community-feed-card, body.role-resident .community-post-panel, body.role-resident .concern-card {
        padding: 18px 20px !important;
        border-radius: 16px !important;
    }
    body.role-resident .resident-surface-head, body.role-resident .resident-page-panel-head, body.role-resident .resident-ticket-create-panel-head, body.role-resident .resident-booking-create-head, body.role-resident .community-section-head, body.role-resident .concern-card-head {
        margin-bottom: 12px !important;
    }
    body.role-resident .resident-surface-head h2, body.role-resident .resident-page-panel-head h2, body.role-resident .resident-ticket-create-panel-head h2, body.role-resident .resident-booking-create-head h2, body.role-resident .community-section-head h2, body.role-resident .concern-card-head h2 {
        font-family: 'Inter', sans-serif !important;
        font-size: 1.08rem !important;
        font-weight: 800 !important;
    }
    body.role-resident .resident-surface-head p, body.role-resident .resident-page-panel-head p, body.role-resident .resident-ticket-create-panel-head p, body.role-resident .resident-booking-create-head p, body.role-resident .community-section-head p, body.role-resident .concern-card-head p {
        font-size: 0.84rem !important;
    }
    body.role-resident .resident-card, body.role-resident .resident-stack-item, body.role-resident .resident-notice-card, body.role-resident .resident-community-entry, body.role-resident .community-review-card, body.role-resident .community-comment, body.role-resident .resident-empty-state, body.role-resident .community-empty-state {
        border-radius: 14px !important;
        padding: 14px 16px !important;
    }
    body.role-resident .resident-card-heading h3, body.role-resident .resident-stack-item-row h3, body.role-resident .resident-notice-title h3, body.role-resident .resident-community-entry h3, body.role-resident .community-feed-copy h3, body.role-resident .community-review-top h3 {
        font-size: 0.98rem !important;
    }
    body.role-resident .resident-card-description, body.role-resident .resident-stack-item-row p, body.role-resident .resident-notice-card p, body.role-resident .resident-community-entry p, body.role-resident .community-feed-copy p, body.role-resident .community-review-top p {
        font-size: 0.86rem !important;
        line-height: 1.55 !important;
    }
    body.role-resident .resident-card-meta-grid, body.role-resident .resident-booking-schedule-grid, body.role-resident .resident-ticket-hero-stats {
        gap: 10px !important;
    }
    body.role-resident .resident-meta-box {
        padding: 10px 12px !important;
        border-radius: 12px !important;
    }
    body.role-resident .resident-filter-bar, body.role-resident .community-filter-bar {
        gap: 10px;
        margin-bottom: 14px;
    }
    body.role-resident .resident-filter-input, body.role-resident .resident-filter-select, body.role-resident .community-filter-input, body.role-resident .community-filter-select, body.role-resident .resident-ticket-create-input, body.role-resident .resident-ticket-create-input-file, body.role-resident .resident-booking-create-input, body.role-resident .concern-input {
        min-height: 42px;
        border-radius: 12px !important;
        font-size: 0.9rem !important;
    }
    body.role-resident .community-composer-card {
        align-items: stretch;
    }
    body.role-resident .community-composer-avatar, body.role-resident .community-feed-avatar {
        width: 44px;
        height: 44px;
    }
    body.role-resident .community-composer-trigger {
        min-height: 46px;
        border-radius: 12px !important;
    }
    body.role-resident .community-feed-media {
        border-radius: 14px !important;
        margin-top: 12px;
    }
    body.role-resident .resident-badge, body.role-resident .resident-status-chip, body.role-resident .community-status-chip {
        min-height: 26px;
        padding: 5px 9px !important;
        font-size: 0.66rem !important;
        letter-spacing: 0.06em !important;
    }
    @media (max-width:980px) {
        body.role-resident .app-main, body.role-resident .app-main.full-bleed {
            padding-inline: 18px;
        }
        body.role-resident .resident-content-grid {
            grid-template-columns: 1fr;
        }
    }
    @media (max-width:640px) {
        body.role-resident .app-main, body.role-resident .app-main.full-bleed {
            padding-inline: 14px;
        }
        body.role-resident .resident-home-title {
            font-size: 2.15rem !important;
        }
        body.role-resident .resident-home-actions, body.role-resident .resident-page-actions, body.role-resident .resident-ticket-create-hero-actions, body.role-resident .resident-booking-create-actions, body.role-resident .concern-hero-actions {
            width: 100%;
            display: grid;
            grid-template-columns: 1fr;
        }
        body.role-resident .resident-home-btn {
            width: 100%;
        }
    }
}
@media not all {
    /* Resident polish pass:keeps the original layout, but gives the surfaces clearer hierarchy. */
    body.role-resident {
        --resident-polish-panel: rgba(39, 43, 45, 0.72);
        --resident-polish-panel-strong: rgba(45, 50, 52, 0.78);
        --resident-polish-card: #1b2022;
        --resident-polish-card-warm: #302c24;
        --resident-polish-border: rgba(238, 197, 128, 0.22);
        --resident-polish-border-soft: rgba(255, 244, 225, 0.10);
        --resident-polish-border-hover: rgba(238, 197, 128, 0.38);
        --resident-polish-title: #f5ecdf;
        --resident-polish-body: #cbbfac;
        --resident-polish-muted: #9f907b;
        --resident-polish-accent: #e0ad55;
        --resident-polish-glow: 0 18px 42px rgba(0, 0, 0, 0.22), 0 0 28px rgba(214, 168, 91, 0.055);
        --resident-polish-glow-strong: 0 20px 48px rgba(0, 0, 0, 0.26), 0 0 42px rgba(214, 168, 91, 0.10);
    }
    body.role-resident .resident-home-hero-content {
        max-width: 640px;
        padding-top: 14px;
        padding-bottom: 2px;
    }
    body.role-resident .resident-home-kicker {
        color: #e6c891;
        font-size: clamp(1.05rem, 1.45vw, 1.32rem);
    }
    body.role-resident .resident-home-title {
        max-width: 620px;
        color: var(--resident-polish-title);
        font-size: clamp(2.45rem, 4.2vw, 3.75rem);
        line-height: 1.02;
        text-wrap: balance;
    }
    body.role-resident .resident-home-subtitle {
        margin-top: 10px;
        color: var(--resident-polish-body);
        font-size: clamp(0.98rem, 1.2vw, 1.08rem);
    }
    body.role-resident .resident-home-actions {
        margin-top: 24px;
    }
    body.role-resident .resident-page-hero, body.role-resident .resident-ticket-create-hero, body.role-resident .resident-booking-create-hero, body.role-resident .resident-booking-hero, body.role-resident .resident-ticket-hero, body.role-resident .community-feed-hero, body.role-resident .community-post-hero, body.role-resident .concern-hero {
        padding: 24px 28px !important;
        min-height: 0 !important;
        border-radius: 20px !important;
        background: radial-gradient(circle at 84% 16%, rgba(224, 173, 85, 0.16), transparent 34%), linear-gradient(135deg, rgba(48, 52, 53, 0.82) 0%, rgba(28, 32, 34, 0.74) 58%, rgba(72, 52, 25, 0.58) 100%) !important;
        backdrop-filter: blur(18px) saturate(1.08);
        -webkit-backdrop-filter: blur(18px) saturate(1.08);
        border: 1px solid var(--resident-polish-border) !important;
        box-shadow: var(--resident-polish-glow-strong) !important;
    }
    body.role-resident .resident-page-title, body.role-resident .resident-ticket-create-title, body.role-resident .resident-booking-create-title, body.role-resident .resident-booking-title, body.role-resident .resident-ticket-title, body.role-resident .community-feed-title, body.role-resident .community-post-title, body.role-resident .concern-title {
        color: var(--resident-polish-title) !important;
        font-size: clamp(2rem, 3.4vw, 3rem) !important;
        line-height: 1.08 !important;
        text-wrap: balance;
    }
    body.role-resident .resident-page-subtitle, body.role-resident .resident-ticket-create-subtitle, body.role-resident .resident-booking-create-subtitle, body.role-resident .resident-booking-subtitle, body.role-resident .resident-ticket-subtitle, body.role-resident .community-feed-subtitle, body.role-resident .community-post-subtitle, body.role-resident .concern-subtitle {
        max-width: 720px;
        color: var(--resident-polish-body) !important;
        font-size: 0.98rem !important;
        line-height: 1.58 !important;
    }
    body.role-resident .resident-page-kicker, body.role-resident .resident-ticket-create-kicker, body.role-resident .resident-booking-create-kicker, body.role-resident .resident-booking-kicker, body.role-resident .resident-ticket-kicker, body.role-resident .community-feed-kicker, body.role-resident .community-post-kicker, body.role-resident .concern-kicker {
        color: var(--resident-polish-accent) !important;
    }
    body.role-resident .resident-activity-card, body.role-resident .resident-surface-panel, body.role-resident .resident-page-panel, body.role-resident .resident-ticket-create-panel, body.role-resident .resident-booking-create-panel, body.role-resident .resident-ticket-panel, body.role-resident .resident-booking-panel, body.role-resident .resident-booking-detail-panel, body.role-resident .community-review-strip, body.role-resident .community-composer-card, body.role-resident .community-feed-card, body.role-resident .community-post-panel, body.role-resident .community-comment-card, body.role-resident .community-empty-state, body.role-resident .concern-card, body.role-resident .concern-alert-context {
        background: radial-gradient(circle at 92% 8%, rgba(214, 168, 91, 0.10), transparent 28%), linear-gradient(180deg, rgba(45, 50, 52, 0.76) 0%, var(--resident-polish-panel) 100%) !important;
        backdrop-filter: blur(16px) saturate(1.06);
        -webkit-backdrop-filter: blur(16px) saturate(1.06);
        border: 1px solid var(--resident-polish-border) !important;
        border-radius: 18px !important;
        box-shadow: var(--resident-polish-glow) !important;
    }
    body.role-resident .resident-activity-card {
        background: radial-gradient(circle at 88% 12%, rgba(224, 173, 85, 0.13), transparent 30%), linear-gradient(180deg, rgba(52, 57, 59, 0.82), var(--resident-polish-panel-strong)) !important;
        box-shadow: var(--resident-polish-glow) !important;
    }
    body.role-resident .resident-page-panel, body.role-resident .resident-ticket-create-panel, body.role-resident .resident-booking-create-panel, body.role-resident .resident-ticket-panel, body.role-resident .resident-booking-panel, body.role-resident .resident-booking-detail-panel, body.role-resident .community-composer-card, body.role-resident .community-feed-card, body.role-resident .community-post-panel, body.role-resident .community-comment-card, body.role-resident .concern-card {
        background: linear-gradient(180deg, #262b2d 0%, #222729 100%) !important;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
        box-shadow: 0 14px 30px rgba(0, 0, 0, 0.17) !important;
    }
    body.role-resident .resident-card, body.role-resident .resident-stack-item, body.role-resident .resident-notice-card, body.role-resident .resident-community-entry, body.role-resident .resident-ticket-upload-panel, body.role-resident .resident-ticket-priority-card, body.role-resident .resident-booking-slot, body.role-resident .community-review-card, body.role-resident .community-composer-trigger, body.role-resident .community-action-btn, body.role-resident .community-comment, body.role-resident .resident-empty-state, body.role-resident .resident-filter-empty, body.role-resident .community-filter-empty {
        background: var(--resident-polish-card) !important;
        backdrop-filter: none;
        -webkit-backdrop-filter: none;
        border: 1px solid var(--resident-polish-border-soft) !important;
        border-radius: 14px !important;
        box-shadow: 0 10px 22px rgba(0, 0, 0, 0.13);
    }
    body.role-resident .resident-meta-box, body.role-resident .resident-hero-stat, body.role-resident .resident-booking-create-stat, body.role-resident .community-feed-stat, body.role-resident .resident-filter-bar, body.role-resident .community-filter-bar {
        background: var(--resident-polish-card-warm) !important;
        backdrop-filter: none;
        -webkit-backdrop-filter: none;
        border: 1px solid rgba(214, 168, 91, 0.13) !important;
        border-radius: 14px !important;
    }
    body.role-resident .resident-card:hover, body.role-resident .resident-stack-item:hover, body.role-resident .resident-community-entry:hover, body.role-resident .community-feed-card:hover, body.role-resident .community-review-card:hover {
        border-color: var(--resident-polish-border-hover) !important;
    }
    body.role-resident .resident-surface-head h2, body.role-resident .resident-surface-head h3, body.role-resident .resident-page-panel-head h2, body.role-resident .resident-card-heading h3, body.role-resident .resident-stack-item-row h3, body.role-resident .resident-notice-title h3, body.role-resident .resident-community-entry h3, body.role-resident .resident-activity-card-top strong, body.role-resident .community-section-head h2, body.role-resident .community-review-top h3, body.role-resident .community-feed-copy h3, body.role-resident .community-feed-author-copy strong, body.role-resident .concern-card-head h2 {
        color: var(--resident-polish-title) !important;
    }
    body.role-resident .resident-surface-head p, body.role-resident .resident-page-panel-head p, body.role-resident .resident-card-description, body.role-resident .resident-stack-item-row p, body.role-resident .resident-notice-card p, body.role-resident .resident-community-entry p, body.role-resident .community-feed-copy p, body.role-resident .community-review-top p, body.role-resident .concern-card-head p {
        color: var(--resident-polish-body) !important;
    }
    body.role-resident .resident-meta-box span, body.role-resident .resident-stack-meta, body.role-resident .resident-community-entry-time, body.role-resident .community-review-meta, body.role-resident .resident-ticket-upload-note, body.role-resident .resident-booking-create-help {
        color: var(--resident-polish-muted) !important;
    }
    body.role-resident .resident-filter-input, body.role-resident .resident-filter-select, body.role-resident .community-filter-input, body.role-resident .community-filter-select, body.role-resident .resident-ticket-create-input, body.role-resident .resident-ticket-create-input-file, body.role-resident .resident-booking-create-input, body.role-resident .concern-input, body.role-resident .community-post-input, body.role-resident .community-comment-input {
        background: #15191a !important;
        border-color: rgba(255, 244, 225, 0.11) !important;
        color: var(--resident-polish-title) !important;
    }
    @media (max-width:760px) {
        body.role-resident .resident-home-title {
            font-size: clamp(2.15rem, 11vw, 3rem);
        }
        body.role-resident .resident-page-hero, body.role-resident .resident-ticket-create-hero, body.role-resident .resident-booking-create-hero, body.role-resident .community-feed-hero, body.role-resident .concern-hero {
            padding: 22px !important;
        }
    }
}
/* Resident refinement pass:solid surfaces, clearer nested cards, smaller feature-page heroes. */
body.role-resident {
    --resident-solid-panel: #2a2d30;
    --resident-solid-panel-alt: #26292b;
    --resident-solid-card: #191b1d;
    --resident-solid-card-alt: #1f2123;
    --resident-solid-border: rgba(224, 173, 85, 0.20);
    --resident-solid-border-soft: rgba(255, 244, 225, 0.10);
    --resident-solid-title: #f4eadc;
    --resident-solid-body: #c8bdad;
    --resident-solid-muted: #9a8c79;
    --resident-solid-accent: #d6a85b;
}
body.role-resident .resident-activity-card, body.role-resident .resident-surface-panel, body.role-resident .resident-page-panel, body.role-resident .resident-ticket-create-panel, body.role-resident .resident-booking-create-panel, body.role-resident .resident-ticket-panel, body.role-resident .resident-booking-panel, body.role-resident .resident-booking-detail-panel, body.role-resident .community-review-strip, body.role-resident .community-composer-card, body.role-resident .community-feed-card, body.role-resident .community-post-panel, body.role-resident .community-comment-card, body.role-resident .community-empty-state, body.role-resident .concern-card, body.role-resident .concern-alert-context {
    background: linear-gradient(180deg, var(--resident-solid-panel) 0%, var(--resident-solid-panel-alt) 100%) !important;
    border: 1px solid var(--resident-solid-border) !important;
    box-shadow: 0 14px 30px rgba(0, 0, 0, 0.24) !important;
    backdrop-filter: none !important;
    -webkit-backdrop-filter: none !important;
}
body.role-resident .resident-card, body.role-resident .resident-stack-item, body.role-resident .resident-notice-card, body.role-resident .resident-community-entry, body.role-resident .resident-ticket-upload-panel, body.role-resident .resident-ticket-priority-card, body.role-resident .resident-booking-slot, body.role-resident .community-review-card, body.role-resident .community-composer-trigger, body.role-resident .community-action-btn, body.role-resident .community-comment, body.role-resident .resident-empty-state, body.role-resident .resident-filter-empty, body.role-resident .community-filter-empty {
    background: linear-gradient(180deg, var(--resident-solid-card-alt) 0%, var(--resident-solid-card) 100%) !important;
    border: 1px solid var(--resident-solid-border-soft) !important;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.025), 0 8px 18px rgba(0, 0, 0, 0.20) !important;
    backdrop-filter: none !important;
    -webkit-backdrop-filter: none !important;
}
body.role-resident .resident-page-hero, body.role-resident .resident-ticket-create-hero, body.role-resident .resident-booking-create-hero, body.role-resident .resident-ticket-hero, body.role-resident .resident-booking-hero, body.role-resident .community-feed-hero, body.role-resident .community-post-hero, body.role-resident .concern-hero {
    min-height: 0 !important;
    padding: 18px 22px !important;
    border-radius: 18px !important;
    display: flex !important;
    align-items: center !important;
    gap: 18px !important;
    background: linear-gradient(180deg, var(--resident-solid-panel) 0%, var(--resident-solid-panel-alt) 100%) !important;
    border: 1px solid var(--resident-solid-border) !important;
    box-shadow: 0 14px 30px rgba(0, 0, 0, 0.24) !important;
    backdrop-filter: none !important;
    -webkit-backdrop-filter: none !important;
}
body.role-resident .resident-page-title, body.role-resident .resident-ticket-create-title, body.role-resident .resident-booking-create-title, body.role-resident .resident-ticket-title, body.role-resident .resident-booking-title, body.role-resident .community-feed-title, body.role-resident .community-post-title, body.role-resident .concern-title {
    font-size: clamp(1.7rem, 2.6vw, 2.45rem) !important;
    line-height: 1.08 !important;
    letter-spacing: 0 !important;
}
body.role-resident .resident-page-subtitle, body.role-resident .resident-ticket-create-subtitle, body.role-resident .resident-booking-create-subtitle, body.role-resident .resident-ticket-subtitle, body.role-resident .resident-booking-subtitle, body.role-resident .community-feed-subtitle, body.role-resident .community-post-subtitle, body.role-resident .concern-subtitle {
    max-width: 760px !important;
    margin-top: 8px !important;
    font-size: 0.92rem !important;
    line-height: 1.45 !important;
}
body.role-resident .resident-page-kicker, body.role-resident .resident-ticket-create-kicker, body.role-resident .resident-booking-create-kicker, body.role-resident .resident-ticket-kicker, body.role-resident .resident-booking-kicker, body.role-resident .community-feed-kicker, body.role-resident .community-post-kicker, body.role-resident .concern-kicker {
    margin-bottom: 6px !important;
    font-size: 0.72rem !important;
    letter-spacing: 0.14em !important;
}
body.role-resident .resident-hero-stat-row, body.role-resident .resident-booking-create-stats, body.role-resident .community-feed-stats {
    margin-top: 14px !important;
    gap: 10px !important;
}
body.role-resident .resident-hero-stat, body.role-resident .resident-booking-create-stat, body.role-resident .community-feed-stat {
    min-width: 92px !important;
    padding: 9px 12px !important;
    border-radius: 12px !important;
    background: var(--resident-solid-card-alt) !important;
    border-color: var(--resident-solid-border-soft) !important;
}
body.role-resident .concern-hero {
    justify-content: space-between !important;
}
body.role-resident .concern-hero > div:first-child {
    min-width: 0 !important;
    flex: 1 1 auto !important;
}
body.role-resident .concern-hero-actions {
    width: auto !important;
    min-width: 210px !important;
    flex: 0 0 210px !important;
    display: grid !important;
    grid-template-columns: 1fr !important;
    gap: 10px !important;
    align-self: center !important;
    justify-content: stretch !important;
    margin-left: auto !important;
}
body.role-resident .concern-hero-actions .concern-btn {
    width: 100% !important;
    min-width: 0 !important;
}
body.role-resident .resident-activity-card {
    min-height: 76px !important;
    padding: 14px 16px !important;
}
body.role-resident .resident-activity-card-icon, body.role-resident .resident-stack-item-icon {
    background: #2b251c !important;
    border: 1px solid rgba(214, 168, 91, 0.26) !important;
    color: #e2bd78 !important;
}
body.role-resident .resident-activity-card-gold .resident-activity-card-icon {
    background: linear-gradient(135deg, #9f6b24 0%, #c79039 100%) !important;
    color: #fff3dc !important;
    border-color: rgba(236, 195, 126, 0.34) !important;
}
body.role-resident .resident-surface-head h2, body.role-resident .resident-section-title, body.role-resident .resident-stack-item-row h3, body.role-resident .resident-notice-title h3, body.role-resident .resident-community-entry h3, body.role-resident .resident-activity-card-top strong {
    color: var(--resident-solid-title) !important;
}
body.role-resident .resident-surface-head p, body.role-resident .resident-stack-item-row p, body.role-resident .resident-notice-card p, body.role-resident .resident-community-entry p, body.role-resident .resident-activity-card-copy p {
    color: var(--resident-solid-body) !important;
}
body.role-resident .resident-stack-meta, body.role-resident .resident-community-entry-time {
    color: var(--resident-solid-muted) !important;
}
body.role-resident .resident-surface-head a {
    color: var(--resident-solid-accent) !important;
}
body.role-resident .resident-surface-divider {
    background: linear-gradient(to right, rgba(214, 168, 91, 0.38), rgba(214, 168, 91, 0.10), transparent) !important;
}
body.role-resident .resident-status-chip {
    border-color: rgba(255, 244, 225, 0.10) !important;
}
body.role-resident .resident-status-chip-completed {
    background: rgba(93, 132, 83, 0.24) !important;
    color: #d9eccd !important;
}
body.role-resident .resident-status-chip-in_progress, body.role-resident .resident-status-chip-assigned {
    background: rgba(214, 168, 91, 0.18) !important;
    color: #efd59e !important;
}
html.theme-light body.role-resident {
    --resident-light-bg: #f4eadc;
    --resident-light-panel: #decab0;
    --resident-light-panel-2: #d5bea0;
    --resident-light-card: #c6aa86;
    --resident-light-card-2: #b99870;
    --resident-light-border: rgba(111, 78, 45, 0.28);
    --resident-light-border-soft: rgba(111, 78, 45, 0.18);
    --resident-light-title: #2f2419;
    --resident-light-body: #5c4834;
    --resident-light-muted: #765f45;
    --resident-light-accent: #8f5f22;
    --resident-light-accent-strong: #a86f24;
    background: radial-gradient(circle at 82% 4%, rgba(255, 255, 255, 0.45), transparent 24%), linear-gradient(180deg, #f7efe5 0%, var(--resident-light-bg) 48%, #ead9c2 100%) !important;
    color: var(--resident-light-title) !important;
}
html.theme-light body.role-resident .role-nav-shell, html.theme-light body.role-resident .role-action-btn, html.theme-light body.role-resident .role-theme-toggle, html.theme-light body.role-resident .role-user-chip, html.theme-light body.role-resident .role-notification-btn, html.theme-light body.role-resident .role-mobile-toggle, html.theme-light body.role-resident .role-mobile-panel, html.theme-light body.role-resident .role-notification-panel {
    background: rgba(226, 208, 184, 0.90) !important;
    border-color: var(--resident-light-border) !important;
    color: var(--resident-light-title) !important;
    box-shadow: 0 16px 34px rgba(111, 78, 45, 0.14) !important;
}
html.theme-light body.role-resident .role-nav-link {
    color: var(--resident-light-body) !important;
}
html.theme-light body.role-resident .role-nav-link:hover, html.theme-light body.role-resident .role-nav-link.is-active {
    background: rgba(143, 95, 34, 0.14) !important;
    color: var(--resident-light-title) !important;
}
html.theme-light body.role-resident .resident-activity-card, html.theme-light body.role-resident .resident-surface-panel, html.theme-light body.role-resident .resident-page-hero, html.theme-light body.role-resident .resident-ticket-create-hero, html.theme-light body.role-resident .resident-booking-create-hero, html.theme-light body.role-resident .resident-ticket-hero, html.theme-light body.role-resident .resident-booking-hero, html.theme-light body.role-resident .community-feed-hero, html.theme-light body.role-resident .community-post-hero, html.theme-light body.role-resident .concern-hero, html.theme-light body.role-resident .resident-page-panel, html.theme-light body.role-resident .resident-ticket-create-panel, html.theme-light body.role-resident .resident-booking-create-panel, html.theme-light body.role-resident .resident-ticket-panel, html.theme-light body.role-resident .resident-booking-panel, html.theme-light body.role-resident .resident-booking-detail-panel, html.theme-light body.role-resident .community-review-strip, html.theme-light body.role-resident .community-composer-card, html.theme-light body.role-resident .community-feed-card, html.theme-light body.role-resident .community-post-panel, html.theme-light body.role-resident .community-comment-card, html.theme-light body.role-resident .community-empty-state, html.theme-light body.role-resident .concern-card, html.theme-light body.role-resident .concern-alert-context {
    background: linear-gradient(180deg, var(--resident-light-panel) 0%, var(--resident-light-panel-2) 100%) !important;
    border-color: var(--resident-light-border) !important;
    box-shadow: 0 16px 34px rgba(111, 78, 45, 0.15) !important;
    color: var(--resident-light-title) !important;
}
html.theme-light body.role-resident .resident-card, html.theme-light body.role-resident .resident-stack-item, html.theme-light body.role-resident .resident-notice-card, html.theme-light body.role-resident .resident-community-entry, html.theme-light body.role-resident .resident-meta-box, html.theme-light body.role-resident .resident-ticket-upload-panel, html.theme-light body.role-resident .resident-ticket-priority-card, html.theme-light body.role-resident .resident-booking-slot, html.theme-light body.role-resident .community-review-card, html.theme-light body.role-resident .community-composer-trigger, html.theme-light body.role-resident .community-action-btn, html.theme-light body.role-resident .community-comment, html.theme-light body.role-resident .resident-empty-state, html.theme-light body.role-resident .resident-filter-empty, html.theme-light body.role-resident .community-filter-empty {
    background: linear-gradient(180deg, rgba(198, 170, 134, 0.88) 0%, rgba(185, 152, 112, 0.88) 100%) !important;
    border-color: var(--resident-light-border-soft) !important;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.22), 0 8px 18px rgba(111, 78, 45, 0.12) !important;
    color: var(--resident-light-title) !important;
}
html.theme-light body.role-resident .resident-home-title, html.theme-light body.role-resident .resident-section-title, html.theme-light body.role-resident .resident-surface-head h2, html.theme-light body.role-resident .resident-surface-head h3, html.theme-light body.role-resident .resident-stack-item-row h3, html.theme-light body.role-resident .resident-notice-title h3, html.theme-light body.role-resident .resident-community-entry h3, html.theme-light body.role-resident .resident-page-title, html.theme-light body.role-resident .resident-page-panel-head h2, html.theme-light body.role-resident .resident-card-heading h3, html.theme-light body.role-resident .resident-day-heading, html.theme-light body.role-resident .resident-ticket-section-head h3, html.theme-light body.role-resident .resident-ticket-create-title, html.theme-light body.role-resident .resident-ticket-create-panel-head h2, html.theme-light body.role-resident .resident-ticket-upload-head h3, html.theme-light body.role-resident .resident-ticket-priority-name, html.theme-light body.role-resident .resident-booking-create-title, html.theme-light body.role-resident .resident-booking-create-head h2, html.theme-light body.role-resident .community-feed-title, html.theme-light body.role-resident .community-section-head h2, html.theme-light body.role-resident .community-review-top h3, html.theme-light body.role-resident .community-feed-copy h3, html.theme-light body.role-resident .community-feed-author-copy strong, html.theme-light body.role-resident .concern-title, html.theme-light body.role-resident .concern-card-head h2 {
    color: var(--resident-light-title) !important;
}
html.theme-light body.role-resident .resident-home-subtitle, html.theme-light body.role-resident .resident-page-subtitle, html.theme-light body.role-resident .resident-ticket-create-subtitle, html.theme-light body.role-resident .resident-booking-create-subtitle, html.theme-light body.role-resident .resident-ticket-subtitle, html.theme-light body.role-resident .resident-booking-subtitle, html.theme-light body.role-resident .community-feed-subtitle, html.theme-light body.role-resident .concern-subtitle, html.theme-light body.role-resident .resident-surface-head p, html.theme-light body.role-resident .resident-stack-item-row p, html.theme-light body.role-resident .resident-notice-card p, html.theme-light body.role-resident .resident-community-entry p, html.theme-light body.role-resident .resident-card-description, html.theme-light body.role-resident .resident-page-panel-head p, html.theme-light body.role-resident .resident-ticket-create-panel-head p, html.theme-light body.role-resident .resident-ticket-priority-copy, html.theme-light body.role-resident .resident-ticket-upload-head p, html.theme-light body.role-resident .resident-ticket-upload-note, html.theme-light body.role-resident .resident-booking-create-head p, html.theme-light body.role-resident .resident-booking-create-help, html.theme-light body.role-resident .community-section-head p, html.theme-light body.role-resident .community-review-top p, html.theme-light body.role-resident .community-feed-copy p, html.theme-light body.role-resident .community-feed-author-copy span, html.theme-light body.role-resident .community-feed-stats-row, html.theme-light body.role-resident .concern-card-head p {
    color: var(--resident-light-body) !important;
}
html.theme-light body.role-resident .resident-stack-meta, html.theme-light body.role-resident .resident-community-entry-time, html.theme-light body.role-resident .resident-meta-box span, html.theme-light body.role-resident .community-review-meta {
    color: var(--resident-light-muted) !important;
}
html.theme-light body.role-resident .resident-home-kicker, html.theme-light body.role-resident .resident-page-kicker, html.theme-light body.role-resident .resident-ticket-create-kicker, html.theme-light body.role-resident .resident-booking-create-kicker, html.theme-light body.role-resident .resident-ticket-kicker, html.theme-light body.role-resident .resident-booking-kicker, html.theme-light body.role-resident .community-feed-kicker, html.theme-light body.role-resident .concern-kicker, html.theme-light body.role-resident .resident-surface-head a, html.theme-light body.role-resident .resident-see-more-btn, html.theme-light body.role-resident .resident-empty-state a, html.theme-light body.role-resident .community-empty-state a, html.theme-light body.role-resident .community-review-actions a, html.theme-light body.role-resident .community-review-actions button, html.theme-light body.role-resident .resident-card-links a, html.theme-light body.role-resident .resident-card-links button {
    color: var(--resident-light-accent) !important;
}
html.theme-light body.role-resident .resident-home-btn-primary, html.theme-light body.role-resident .resident-page-btn-primary, html.theme-light body.role-resident .resident-ticket-create-btn-primary, html.theme-light body.role-resident .resident-booking-create-btn-primary, html.theme-light body.role-resident .concern-btn-primary, html.theme-light body.role-resident .community-composer-actions a:not(.community-composer-secondary-action), html.theme-light body.role-resident .community-pagination-link a {
    background: linear-gradient(135deg, #a66d24 0%, #c98a35 100%) !important;
    border-color: rgba(111, 78, 45, 0.30) !important;
    color: #fff7ea !important;
    box-shadow: 0 12px 24px rgba(111, 78, 45, 0.18) !important;
}
html.theme-light body.role-resident .resident-home-btn-secondary, html.theme-light body.role-resident .resident-page-btn-secondary, html.theme-light body.role-resident .resident-ticket-create-btn-secondary, html.theme-light body.role-resident .resident-booking-create-btn-secondary, html.theme-light body.role-resident .concern-btn-secondary, html.theme-light body.role-resident .community-composer-secondary-action, html.theme-light body.role-resident .role-action-btn {
    background: rgba(226, 208, 184, 0.92) !important;
    border-color: var(--resident-light-border) !important;
    color: var(--resident-light-title) !important;
}
html.theme-light body.role-resident .resident-activity-card-icon, html.theme-light body.role-resident .resident-stack-item-icon {
    background: rgba(143, 95, 34, 0.18) !important;
    border-color: rgba(111, 78, 45, 0.24) !important;
    color: var(--resident-light-accent) !important;
}
html.theme-light body.role-resident .resident-activity-card-gold .resident-activity-card-icon {
    background: linear-gradient(135deg, #a66d24 0%, #c98a35 100%) !important;
    color: #fff7ea !important;
}
html.theme-light body.role-resident .resident-filter-input, html.theme-light body.role-resident .resident-filter-select, html.theme-light body.role-resident .community-filter-input, html.theme-light body.role-resident .community-filter-select, html.theme-light body.role-resident .resident-ticket-create-input, html.theme-light body.role-resident .resident-ticket-create-input-file, html.theme-light body.role-resident .resident-booking-create-input, html.theme-light body.role-resident .concern-input, html.theme-light body.role-resident .community-post-input, html.theme-light body.role-resident .community-comment-input {
    background: rgba(246, 237, 224, 0.92) !important;
    border-color: var(--resident-light-border) !important;
    color: var(--resident-light-title) !important;
}
html.theme-light body.role-resident .resident-surface-divider, html.theme-light body.role-resident .resident-page-divider, html.theme-light body.role-resident .resident-ticket-create-divider, html.theme-light body.role-resident .resident-booking-create-divider, html.theme-light body.role-resident .community-post-divider {
    background: linear-gradient(to right, rgba(111, 78, 45, 0.32), rgba(111, 78, 45, 0.10), transparent) !important;
}
html.theme-light body.role-resident .resident-status-chip-completed, html.theme-light body.role-resident .resident-badge-status-approved, html.theme-light body.role-resident .resident-badge-status-completed {
    background: rgba(92, 116, 79, 0.16) !important;
    color: #3f5d35 !important;
}
html.theme-light body.role-resident .resident-status-chip-in_progress, html.theme-light body.role-resident .resident-badge-status-assigned, html.theme-light body.role-resident .resident-badge-status-in_progress {
    background: rgba(143, 95, 34, 0.18) !important;
    color: #6b461a !important;
}
@media (max-width:640px) {
    body.role-resident .resident-page-hero, body.role-resident .resident-ticket-create-hero, body.role-resident .resident-booking-create-hero, body.role-resident .resident-ticket-hero, body.role-resident .resident-booking-hero, body.role-resident .community-feed-hero, body.role-resident .community-post-hero, body.role-resident .concern-hero {
        padding: 16px !important;
        align-items: flex-start !important;
        flex-direction: column !important;
    }
    body.role-resident .concern-hero-actions {
        width: 100% !important;
        min-width: 0 !important;
        flex: none !important;
    }
    body.role-resident .resident-page-title, body.role-resident .resident-ticket-create-title, body.role-resident .resident-booking-create-title, body.role-resident .resident-ticket-title, body.role-resident .resident-booking-title, body.role-resident .community-feed-title, body.role-resident .community-post-title, body.role-resident .concern-title {
        font-size: clamp(1.55rem, 7vw, 2rem) !important;
    }
}
/* Late admin-only theme pass. Keep every manager screen on the Overview dashboard tokens. */
body.role-manager .admin-content-shell {
    --admin-bg: #f8f4ed;
    --admin-card: #ffffff;
    --admin-card-strong: #ffffff;
    --admin-card-border: #e8e0d5;
    --admin-dark-card: #34312c;
    --admin-dark-card-2: #2d2a26;
    --admin-dark-border: rgba(214, 168, 91, 0.16);
    --admin-gold: #b47721;
    --admin-gold-soft: #fbf3e4;
    --admin-hero: linear-gradient(120deg, #34312c 0%, #3d3932 52%, #2d2a26 100%);
    --admin-hero-border: rgba(185, 130, 47, 0.22);
    --admin-hero-grid: rgba(255, 244, 225, 0.035);
    --admin-hero-title: #f8f3ea;
    --admin-hero-muted: #e2d6c8;
    --admin-surface: #ffffff;
    --admin-surface-soft: #fcfaf6;
    --admin-surface-inner: #fbf8f3;
    --admin-surface-border: #e8e0d5;
    --admin-surface-border-soft: #f0ebe4;
    --admin-surface-title: #342a23;
    --admin-surface-body: #786b60;
    --admin-surface-muted: #9b8d81;
    --admin-page-title: #342a23;
    --admin-page-body: #786b60;
    --admin-page-muted: #9b8d81;
    --admin-red: #bd5349;
    --admin-red-soft: #fbeceb;
    --admin-green: #4f805c;
    --admin-green-soft: #edf6ef;
    --admin-blue: #52788c;
    --admin-blue-soft: #edf4f7;
    color: var(--admin-page-title);
    font-family: 'DM Sans', sans-serif;
}
body.role-manager .admin-overview-hero {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 20px;
    padding: 8px 2px 18px;
    border: 0;
    border-bottom: 1px solid #e3d8ca;
    background: transparent;
    box-shadow: none;
}
body.role-manager .admin-overview-hero__kicker {
    margin: 0;
    color: #806f5c;
    font-size: 0.82rem;
    font-weight: 800 !important;
    letter-spacing: 0.14em;
    line-height: 1.35;
    text-transform: uppercase;
}
body.role-manager .admin-overview-hero__title {
    margin: 3px 0 0;
    color: #2f271f;
    font-family: 'Playfair Display', serif;
    font-size: clamp(1.85rem, 3vw, 2.35rem);
    letter-spacing: 0;
    line-height: 1.15;
}
body.role-manager .admin-overview-hero__title span {
    color: #b47721;
}
body.role-manager .admin-overview-hero__subtitle {
    display: block;
    margin: 5px 0 0;
    max-width: 680px;
    color: #806f5c;
    font-size: 0.82rem;
    font-weight: 400 !important;
    line-height: 1.45;
}
body.role-manager .admin-overview-hero__actions {
    display: flex;
    flex: 0 0 auto;
    align-items: center;
    justify-content: flex-end;
    gap: 10px;
}
@media (max-width: 760px) {
    body.role-manager .admin-overview-hero {
        align-items: flex-start;
        flex-direction: column;
    }
    body.role-manager .admin-overview-hero__actions {
        justify-content: flex-start;
        width: 100%;
    }
}
body.role-manager .admin-content-shell:not(.dashboard-shell) {
    padding: 18px 20px 24px;
    border-radius: 16px;
    background: var(--admin-bg);
}
body.role-manager .admin-main-content:has(.dashboard-shell) {
    padding: 12px 14px 28px;
}
body.role-manager .dashboard-shell, body.role-manager .dashboard-shell .hs-dashboard {
    max-width: 1580px;
}
body.role-manager .dashboard-shell .hs-dashboard {
    padding: 16px 18px 22px;
    font-size: 17px;
}
body.role-manager .admin-content-shell button, body.role-manager .admin-content-shell input, body.role-manager .admin-content-shell select, body.role-manager .admin-content-shell textarea {
    font-family: inherit;
}
/* Keep the Community Hub's calm card motion consistent across manager workspaces. */
body.role-manager .admin-content-shell:is( .hs-metric-card, .hs-card, .hs-diagnostics article, .hs-forecast-stat, .hs-log-summary article, .metric-card, .admin-metric-card, .admin-ticket-panel, .ticket-card-shell, .admin-status-card, .booking-panel, .stat-card, .admin-concern-stat, .admin-concern-card, .admin-concern-row, .admin-user-stat, .admin-user-toolbar, .admin-user-table-card, .announcement-standards-panel, .announcement-card, .admin-community-review-panel, .admin-community-review-card, .admin-feature-stat-grid > div ) {
    animation: admin-card-enter 0.46s cubic-bezier(0.22, 1, 0.36, 1) both;
}
body.role-manager .admin-content-shell:is( .hs-metric-card, .hs-card, .hs-diagnostics article, .hs-forecast-stat, .hs-log-summary article, .metric-card, .admin-metric-card, .ticket-card-shell, .admin-status-card, .stat-card, .admin-concern-stat, .admin-concern-row, .admin-user-stat, .announcement-card, .admin-community-review-card, .admin-feature-stat-grid > div ) {
    transition: transform 0.24s ease, box-shadow 0.24s ease, border-color 0.24s ease;
}
body.role-manager .admin-content-shell:is( .hs-metric-card, .hs-card, .hs-diagnostics article, .hs-forecast-stat, .hs-log-summary article, .metric-card, .admin-metric-card, .ticket-card-shell, .admin-status-card, .stat-card, .admin-concern-stat, .admin-concern-row, .admin-user-stat, .announcement-card, .admin-community-review-card, .admin-feature-stat-grid > div ):hover {
    transform: translateY(-3px);
    border-color: rgba(185, 130, 47, 0.30) !important;
    box-shadow: 0 10px 22px rgba(84, 61, 37, 0.10) !important;
}
body.role-manager .admin-content-shell:is( .hs-metrics, .hs-diagnostics, .hs-predictive-side, .hs-log-summary, .stats-grid, .admin-concern-stats, .admin-user-stats, .admin-feature-stat-grid ) >:nth-child(2) {
    animation-delay: 0.05s;
}
body.role-manager .admin-content-shell:is( .hs-metrics, .hs-diagnostics, .hs-predictive-side, .hs-log-summary, .stats-grid, .admin-concern-stats, .admin-user-stats, .admin-feature-stat-grid ) >:nth-child(3) {
    animation-delay: 0.10s;
}
body.role-manager .admin-content-shell:is( .hs-metrics, .hs-diagnostics, .hs-predictive-side, .hs-log-summary, .stats-grid, .admin-concern-stats, .admin-user-stats, .admin-feature-stat-grid ) >:nth-child(n+4) {
    animation-delay: 0.15s;
}
@keyframes admin-card-enter {
    from {
        opacity: 0;
        transform: translateY(12px) scale(0.985);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}
@media (prefers-reduced-motion:reduce) {
    body.role-manager .admin-content-shell:is( .hs-metric-card, .hs-card, .hs-diagnostics article, .hs-forecast-stat, .hs-log-summary article, .metric-card, .admin-metric-card, .admin-ticket-panel, .ticket-card-shell, .admin-status-card, .booking-panel, .stat-card, .admin-concern-stat, .admin-concern-card, .admin-concern-row, .admin-user-stat, .admin-user-toolbar, .admin-user-table-card, .announcement-standards-panel, .announcement-card, .admin-community-review-panel, .admin-community-review-card, .admin-feature-stat-grid > div ) {
        animation: none;
        transition: none;
    }
}
body.role-manager .admin-shell, body.role-manager .admin-ticket-page, body.role-manager .admin-concern-page, body.role-manager .admin-user-page, body.role-manager .booking-dashboard {
    font-family: 'DM Sans', sans-serif !important;
}
body.role-manager .admin-content-shell > .space-y-6 >:not([style*="background"]):not(.rounded-lg):not(.rounded-xl) h1, body.role-manager .admin-content-shell > .space-y-8 >:not([style*="background"]):not(.rounded-lg):not(.rounded-xl) h1, body.role-manager .admin-content-shell > .space-y-6 >:not([style*="background"]):not(.rounded-lg):not(.rounded-xl) h2, body.role-manager .admin-content-shell > .space-y-8 >:not([style*="background"]):not(.rounded-lg):not(.rounded-xl) h2, body.role-manager .admin-content-shell .section-title {
    color: var(--admin-page-title) !important;
}
body.role-manager .admin-content-shell > .space-y-6 >:not([style*="background"]):not(.rounded-lg):not(.rounded-xl) p, body.role-manager .admin-content-shell > .space-y-8 >:not([style*="background"]):not(.rounded-lg):not(.rounded-xl) p, body.role-manager .admin-content-shell .section-sub {
    color: var(--admin-page-muted) !important;
}
body.role-manager .admin-panel-card, body.role-manager .admin-main-panel, body.role-manager .admin-form-panel, body.role-manager .admin-ticket-panel, body.role-manager .admin-ticket-show-panel, body.role-manager .admin-concern-card, body.role-manager .admin-concern-stat, body.role-manager .booking-panel, body.role-manager .chart-card, body.role-manager .panel, body.role-manager .admin-content-shell > .space-y-6 > .rounded-lg, body.role-manager .admin-content-shell > .space-y-6 > .rounded-xl, body.role-manager .admin-content-shell > .space-y-8 > .rounded-lg, body.role-manager .admin-content-shell > .space-y-8 > .rounded-xl, body.role-manager .admin-content-shell > .space-y-6 > div[style*="background:#1F2023"], body.role-manager .admin-content-shell > .space-y-6 > div[style*="background:#2A2C30"], body.role-manager .admin-content-shell > .space-y-8 > div[style*="background:#1F2023"], body.role-manager .admin-content-shell > .space-y-8 > div[style*="background:#2A2C30"], body.role-manager .admin-content-shell > .space-y-6 > div[style*="linear-gradient(180deg, #2A2C30"], body.role-manager .admin-content-shell > .space-y-8 > div[style*="linear-gradient(180deg, #2A2C30"] {
    background: var(--admin-surface) !important;
    border-color: var(--admin-surface-border) !important;
    color: var(--admin-surface-body) !important;
    box-shadow: 0 3px 10px rgba(84, 61, 37, 0.045) !important;
}
body.role-manager .booking-dashboard .stat-card, body.role-manager .metric-card, body.role-manager .admin-concern-stat, body.role-manager .admin-user-stat, body.role-manager .announcement-standards-panel, body.role-manager .admin-feature-stat-grid > div {
    background: linear-gradient(180deg, var(--admin-card) 0%, var(--admin-card-strong) 100%) !important;
    border: 1px solid var(--admin-card-border) !important;
    color: var(--admin-page-body) !important;
    box-shadow: none !important;
    backdrop-filter: none !important;
}
body.role-manager .admin-metric-card {
    background: linear-gradient(180deg, var(--admin-card) 0%, var(--admin-card-strong) 100%) !important;
    border: 1px solid var(--admin-card-border) !important;
    color: var(--admin-page-body) !important;
    box-shadow: none !important;
    backdrop-filter: none !important;
}
body.role-manager .metric-card, body.role-manager .admin-metric-card, body.role-manager .booking-dashboard .stat-card, body.role-manager .admin-concern-stat, body.role-manager .admin-user-stat, body.role-manager .admin-feature-stat-grid > div {
    min-height: 92px !important;
    padding: 18px 20px !important;
    border-radius: 16px !important;
    gap: 8px !important;
}
body.role-manager .booking-dashboard .stat-card, body.role-manager .admin-concern-stat, body.role-manager .admin-user-stat, body.role-manager .admin-feature-stat-grid > div {
    justify-content: center !important;
    align-items: stretch !important;
    display: flex !important;
    flex-direction: column !important;
}
body.role-manager .stat-card-main {
    display: flex !important;
    align-items: flex-end !important;
    justify-content: space-between !important;
    gap: 14px !important;
    width: 100% !important;
}
body.role-manager .stat-card-main .stat-label, body.role-manager .stat-card-main span {
    text-align: right !important;
    max-width: 56% !important;
}
body.role-manager .admin-feature-stat-grid {
    gap: 16px !important;
}
body.role-manager .admin-feature-stat-grid > div {
    box-shadow: none !important;
    transform: none;
}
body.role-manager .admin-feature-stat-grid > div:hover {
    transform: translateY(-2px) !important;
    border-color: rgba(52, 49, 44, 0.56) !important;
}
body.role-manager .admin-feature-stat-grid > div > .absolute {
    display: none !important;
}
body.role-manager .admin-feature-stat-grid > div > .relative {
    padding: 0 !important;
}
body.role-manager .admin-feature-stat-grid > div > .relative > .flex:first-child {
    margin-bottom: 8px !important;
}
body.role-manager .admin-feature-stat-grid [class*="w-11"], body.role-manager .admin-feature-stat-grid [class*="w-12"] {
    width: 44px !important;
    height: 44px !important;
    border-radius: 12px !important;
    background: rgba(52, 49, 44, 0.08) !important;
    border: 1px solid rgba(52, 49, 44, 0.10) !important;
}
body.role-manager .admin-feature-stat-grid svg {
    width: 20px !important;
    height: 20px !important;
    color: #9b6b25 !important;
}
body.role-manager .admin-panel-card h2, body.role-manager .admin-panel-card h3, body.role-manager .admin-main-panel h2, body.role-manager .admin-main-panel h3, body.role-manager .admin-ticket-panel h2, body.role-manager .admin-ticket-panel h3, body.role-manager .admin-ticket-show-panel h2, body.role-manager .admin-ticket-show-panel h3, body.role-manager .admin-concern-card h2, body.role-manager .admin-concern-card h3, body.role-manager .booking-panel h2, body.role-manager .booking-panel h3, body.role-manager .metric-card .metric-value, body.role-manager .booking-dashboard .stat-card .stat-value, body.role-manager .admin-concern-stat strong, body.role-manager .admin-user-stat .text-xl, body.role-manager .admin-metric-value, body.role-manager .admin-metric-value-alert, body.role-manager .admin-metric-value-success, body.role-manager .announcement-standards-panel div, body.role-manager .announcement-standards-panel strong, body.role-manager .announcement-standards-panel .font-bold, body.role-manager .announcement-standards-panel .font-semibold, body.role-manager .announcement-standards-panel [style*="font-weight:700"], body.role-manager .chart-card .chart-title, body.role-manager .panel .panel-title, body.role-manager .admin-status-card strong, body.role-manager .admin-ticket-info-card strong, body.role-manager .admin-ticket-summary-row strong, body.role-manager .admin-detail-item strong, body.role-manager .admin-meta-value, body.role-manager .summary-row h3, body.role-manager .history-row h3, body.role-manager .admin-content-shell table .text-white, body.role-manager .admin-content-shell table td, body.role-manager .admin-content-shell table .font-medium {
    color: var(--admin-surface-title) !important;
}
body.role-manager .metric-card .metric-value, body.role-manager .booking-dashboard .stat-card .stat-value, body.role-manager .admin-concern-stat strong, body.role-manager .admin-user-stat .text-xl, body.role-manager .admin-metric-value, body.role-manager .admin-metric-value-alert, body.role-manager .admin-metric-value-success, body.role-manager .announcement-standards-panel div, body.role-manager .announcement-standards-panel strong, body.role-manager .announcement-standards-panel .font-bold, body.role-manager .announcement-standards-panel .font-semibold, body.role-manager .announcement-standards-panel [style*="font-weight:700"] {
    color: var(--admin-page-title) !important;
}
body.role-manager .admin-panel-card p, body.role-manager .admin-main-panel p, body.role-manager .admin-ticket-panel p, body.role-manager .admin-ticket-show-panel p, body.role-manager .admin-concern-card p, body.role-manager .booking-panel p, body.role-manager .metric-card .metric-label, body.role-manager .metric-card .metric-sub, body.role-manager .booking-dashboard .stat-card .stat-label, body.role-manager .booking-dashboard .stat-card .stat-note, body.role-manager .admin-concern-stat span, body.role-manager .admin-user-stat .text-xs, body.role-manager .admin-metric-label, body.role-manager .admin-metric-sub, body.role-manager .admin-metric-sub-alert, body.role-manager .announcement-standards-panel p, body.role-manager .announcement-standards-panel span, body.role-manager .chart-card .chart-desc, body.role-manager .chart-card .chart-legend, body.role-manager .panel .panel-sub, body.role-manager .panel .list-item-meta, body.role-manager .panel .empty-state, body.role-manager .admin-ticket-panel-sub, body.role-manager .admin-status-card p, body.role-manager .admin-status-meta, body.role-manager .admin-ticket-info-card span, body.role-manager .admin-ticket-info-card p, body.role-manager .admin-ticket-summary-row span, body.role-manager .admin-detail-item span, body.role-manager .admin-meta-label, body.role-manager .summary-row p, body.role-manager .history-row p, body.role-manager .empty-copy, body.role-manager .booking-chip-time, body.role-manager .calendar-legend-item, body.role-manager .time-cell, body.role-manager .admin-content-shell table .text-gray-300, body.role-manager .admin-content-shell table .text-gray-400, body.role-manager .admin-content-shell table .text-gray-500 {
    color: var(--admin-surface-muted) !important;
}
body.role-manager .metric-card .metric-label, body.role-manager .metric-card .metric-sub, body.role-manager .booking-dashboard .stat-card .stat-label, body.role-manager .booking-dashboard .stat-card .stat-note, body.role-manager .admin-concern-stat span, body.role-manager .admin-user-stat .text-xs, body.role-manager .admin-metric-label, body.role-manager .admin-metric-sub, body.role-manager .admin-metric-sub-alert, body.role-manager .announcement-standards-panel p, body.role-manager .announcement-standards-panel span {
    color: var(--admin-page-muted) !important;
}
body.role-manager .admin-metric-icon, body.role-manager .admin-metric-icon-alert, body.role-manager .admin-metric-icon-success, body.role-manager .metric-icon, body.role-manager .admin-user-stat-icon {
    background: rgba(52, 49, 44, 0.08) !important;
    border: 1px solid rgba(52, 49, 44, 0.10) !important;
    color: #9b6b25 !important;
}
body.role-manager .metric-card .metric-value, body.role-manager .admin-metric-value, body.role-manager .booking-dashboard .stat-card .stat-value, body.role-manager .admin-concern-stat strong, body.role-manager .admin-user-stat .text-xl, body.role-manager .admin-feature-stat-grid [class*="text-4xl"], body.role-manager .admin-feature-stat-grid [class*="text-[34px]"], body.role-manager .admin-community-page .admin-feature-stat-grid > div > div:first-child > div:first-child, body.role-manager .admin-concern-stat .stat-card-main strong {
    color: #332c24 !important;
    font-weight: 800 !important;
    letter-spacing: 0 !important;
    font-size: 2.35rem !important;
    line-height: 1 !important;
}
body.role-manager .metric-card .metric-label, body.role-manager .admin-metric-label, body.role-manager .booking-dashboard .stat-card .stat-label, body.role-manager .admin-concern-stat span, body.role-manager .admin-user-stat .text-xs, body.role-manager .admin-feature-stat-grid .font-semibold.uppercase, body.role-manager .admin-community-page .admin-feature-stat-grid > div > div:nth-child(2), body.role-manager .admin-concern-stat .stat-card-main span {
    color: #725f4c !important;
    font-size: 0.78rem !important;
    font-weight: 650 !important;
    letter-spacing: 0.09em !important;
    text-transform: uppercase !important;
}
body.role-manager .metric-card .metric-sub, body.role-manager .admin-metric-sub, body.role-manager .admin-metric-sub-alert, body.role-manager .booking-dashboard .stat-card .stat-note, body.role-manager .admin-feature-stat-grid .text-xs.mt-1, body.role-manager .admin-community-page .admin-feature-stat-grid > div > div:nth-child(3), body.role-manager .admin-concern-stat small {
    color: #8a7a68 !important;
    font-size: 0.86rem !important;
    line-height: 1.35 !important;
}
body.role-manager .admin-metric-card-alert .admin-metric-icon {
    color: #a0681f !important;
    background: rgba(185, 130, 47, 0.12) !important;
    border-color: rgba(185, 130, 47, 0.18) !important;
}
body.role-manager .admin-metric-card-success .admin-metric-icon {
    color: #5c744f !important;
    background: rgba(92, 116, 79, 0.12) !important;
    border-color: rgba(92, 116, 79, 0.18) !important;
}
body.role-manager .admin-status-card, body.role-manager .admin-ticket-info-card, body.role-manager .admin-ticket-summary-row, body.role-manager .admin-ticket-note, body.role-manager .admin-detail-item, body.role-manager .admin-detail-block, body.role-manager .admin-meta-item, body.role-manager .summary-row, body.role-manager .history-row, body.role-manager .calendar-legend, body.role-manager .week-day, body.role-manager .empty-slot, body.role-manager .booking-chip, body.role-manager .admin-concern-row, body.role-manager .admin-content-shell tbody tr {
    background: var(--admin-surface-inner) !important;
    border-color: var(--admin-surface-border-soft) !important;
}
body.role-manager .admin-content-shell input, body.role-manager .admin-content-shell select:not(.admin-operations-filter), body.role-manager .admin-content-shell textarea, body.role-manager .admin-input, body.role-manager .admin-filter-select:not(.admin-operations-filter), body.role-manager .admin-ticket-form select:not(.admin-operations-filter), body.role-manager .admin-ticket-form textarea, body.role-manager .admin-form-input, body.role-manager .date-picker-label, body.role-manager .mode-pill, body.role-manager .nav-pill, body.role-manager .date-badge {
    background: #fffdf9 !important;
    border-color: #dfd5c8 !important;
    color: var(--admin-surface-title) !important;
}
body.role-manager .admin-content-shell input::placeholder, body.role-manager .admin-content-shell textarea::placeholder {
    color: #9b8d81 !important;
}
body.role-manager .admin-content-shell label, body.role-manager .admin-label, body.role-manager .admin-ticket-form label {
    color: var(--admin-surface-body) !important;
}
body.role-manager .admin-secondary-btn, body.role-manager .admin-ticket-show-back, body.role-manager .admin-ticket-action-secondary, body.role-manager .admin-status-link {
    background: #fffdf9 !important;
    border-color: #dfd5c8 !important;
    color: var(--admin-page-body) !important;
}
@media (max-width:768px) {
    body.role-manager .admin-content-shell:not(.dashboard-shell) {
        padding: 14px 12px 18px;
        border-radius: 12px;
    }
}
body.role-manager .admin-content-shell .filter-tab:not(.active) {
    color: var(--admin-page-body) !important;
}
body.role-manager .admin-content-shell .filter-tab.active {
    color: #6f461b !important;
    background: var(--admin-gold-soft) !important;
    border-color: rgba(185, 130, 47, 0.24) !important;
}
body.role-manager .admin-concern-hero, body.role-manager .admin-ticket-show-hero, body.role-manager .dash-hero, body.role-manager .admin-shell > div:first-of-type, body.role-manager .admin-ticket-page > div:first-of-type, body.role-manager .booking-hero {
    background: var(--admin-hero) !important;
    border-color: var(--admin-hero-border) !important;
    color: var(--admin-surface-title) !important;
    box-shadow: 0 18px 38px rgba(72, 48, 24, 0.14) !important;
}
body.role-manager .admin-concern-hero::before, body.role-manager .admin-ticket-show-hero::before, body.role-manager .dash-hero .hero-grid-overlay, body.role-manager .admin-shell > div:first-of-type::before, body.role-manager .admin-ticket-page > div:first-of-type::before, body.role-manager .booking-hero::before {
    background-image: linear-gradient(var(--admin-hero-grid) 1px, transparent 1px), linear-gradient(90deg, var(--admin-hero-grid) 1px, transparent 1px) !important;
    background-size: 64px 64px !important;
}
body.role-manager .admin-concern-hero h1, body.role-manager .admin-ticket-show-hero h1, body.role-manager .dash-hero .hero-title, body.role-manager .admin-shell > div:first-of-type h1, body.role-manager .admin-ticket-page > div:first-of-type h1, body.role-manager .booking-hero h1, body.role-manager .admin-concern-title, body.role-manager .admin-ticket-show-title, body.role-manager .booking-title {
    color: var(--admin-hero-title) !important;
}
body.role-manager .admin-concern-hero p:not(.admin-concern-kicker), body.role-manager .admin-ticket-show-hero p:not(.admin-ticket-show-kicker), body.role-manager .dash-hero .hero-sub, body.role-manager .admin-shell > div:first-of-type p, body.role-manager .admin-ticket-page > div:first-of-type p, body.role-manager .booking-hero p:not(.booking-kicker) {
    color: var(--admin-hero-muted) !important;
}
body.role-manager .admin-ticket-page > div:first-of-type, body.role-manager .admin-announcements-page > div:first-of-type, body.role-manager .admin-community-page > div:first-of-type {
    border-radius: 24px !important;
    min-height: 212px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 24px !important;
    text-align: left !important;
}
body.role-manager .admin-ticket-page > div:first-of-type {
    padding: 0 !important;
}
body.role-manager .admin-ticket-page > div:first-of-type > div.relative, body.role-manager .admin-announcements-page > div:first-of-type > div.relative, body.role-manager .admin-community-page > div:first-of-type > div.relative {
    padding: 36px 44px !important;
    width: 100% !important;
}
body.role-manager .admin-announcements-page > div:first-of-type > div.relative > div, body.role-manager .admin-community-page > div:first-of-type > div.relative > div, body.role-manager .admin-ticket-page > div:first-of-type > div.relative > div {
    width: 100% !important;
    align-items: center !important;
}
body.role-manager .admin-ticket-page > div:first-of-type > *, body.role-manager .admin-announcements-page > div:first-of-type > *, body.role-manager .admin-community-page > div:first-of-type > * {
    text-align: left !important;
}
body.role-manager .admin-ticket-page > div:first-of-type h1, body.role-manager .admin-announcements-page > div:first-of-type h1, body.role-manager .admin-community-page > div:first-of-type h1 {
    font-size: clamp(2rem, 2.75vw, 2.65rem) !important;
    line-height: 1.08 !important;
    margin-bottom: 10px !important;
    letter-spacing: 0 !important;
}
body.role-manager .admin-ticket-page > div:first-of-type p, body.role-manager .admin-announcements-page > div:first-of-type p, body.role-manager .admin-community-page > div:first-of-type p {
    font-size: 1rem !important;
    line-height: 1.48 !important;
    max-width: 760px !important;
}
body.role-manager .admin-announcements-page > div:first-of-type .shrink-0, body.role-manager .admin-community-page > div:first-of-type .shrink-0, body.role-manager .admin-ticket-page > div:first-of-type .shrink-0 {
    margin-left: auto !important;
    align-self: center !important;
}
body.role-manager .admin-announcements-page > div:first-of-type .shrink-0 a {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    white-space: nowrap !important;
}
@media (max-width:768px) {
    body.role-manager .admin-ticket-page > div:first-of-type, body.role-manager .admin-announcements-page > div:first-of-type, body.role-manager .admin-community-page > div:first-of-type {
        min-height: 0 !important;
    }
    body.role-manager .admin-ticket-page > div:first-of-type {
        padding: 24px !important;
        align-items: flex-start !important;
        flex-direction: column !important;
    }
    body.role-manager .admin-announcements-page > div:first-of-type > div.relative, body.role-manager .admin-community-page > div:first-of-type > div.relative {
        padding: 24px !important;
    }
    body.role-manager .admin-ticket-page > div:first-of-type h1, body.role-manager .admin-announcements-page > div:first-of-type h1, body.role-manager .admin-community-page > div:first-of-type h1 {
        font-size: clamp(2rem, 10vw, 2.65rem) !important;
    }
}
body.role-manager .admin-shell > div[style*="#2A2C30"] h2, body.role-manager .admin-shell > div[style*="#1F2023"] h2, body.role-manager .admin-shell > div[style*="#2C2C2F"] h2, body.role-manager .admin-shell > div[style*="#25272A"] h2, body.role-manager .admin-shell > div[style*="#2A2C30"] h3, body.role-manager .admin-shell > div[style*="#1F2023"] h3, body.role-manager .admin-shell > div[style*="#2C2C2F"] h3, body.role-manager .admin-shell > div[style*="#25272A"] h3, body.role-manager .admin-shell div[style*="#2C2C2F"] h3, body.role-manager .admin-shell div[style*="#25272A"] h3 {
    color: var(--admin-surface-title) !important;
}
body.role-manager .admin-shell > div[style*="#2A2C30"] p, body.role-manager .admin-shell > div[style*="#1F2023"] p, body.role-manager .admin-shell > div[style*="#2C2C2F"] p, body.role-manager .admin-shell > div[style*="#25272A"] p, body.role-manager .admin-shell div[style*="#2C2C2F"] div, body.role-manager .admin-shell div[style*="#25272A"] div {
    color: var(--admin-surface-body) !important;
}
/* Dark admin cards need stronger body contrast than light page-level helper text. */
body.role-manager .admin-panel-card p, body.role-manager .admin-main-panel p, body.role-manager .admin-form-panel p, body.role-manager .admin-ticket-panel p, body.role-manager .admin-ticket-show-panel p, body.role-manager .admin-concern-card p, body.role-manager .booking-panel p, body.role-manager .chart-card p, body.role-manager .panel p, body.role-manager .announcement-card__excerpt, body.role-manager .admin-announcement-body, body.role-manager .ticket-card-main > p, body.role-manager .admin-concern-main p {
    color: var(--admin-surface-body) !important;
}
body.role-manager .admin-panel-sub, body.role-manager .admin-ticket-panel-sub, body.role-manager .admin-status-meta, body.role-manager .ticket-card-meta, body.role-manager .announcement-card__date, body.role-manager .announcement-card__age, body.role-manager .announcement-card__record, body.role-manager .admin-meta-label, body.role-manager .admin-concern-meta, body.role-manager .chart-card .chart-desc, body.role-manager .chart-card .chart-insight, body.role-manager .panel .panel-sub, body.role-manager .panel .list-item-meta {
    color: var(--admin-surface-muted) !important;
}
/* Custom manager list components use the same light operational-card language. */
body.role-manager .ticket-card-shell, body.role-manager .announcement-card, body.role-manager .admin-user-toolbar, body.role-manager .admin-user-table-card, body.role-manager .admin-user-form-card {
    background: var(--admin-surface) !important;
    border-color: var(--admin-surface-border) !important;
    color: var(--admin-surface-body) !important;
    box-shadow: 0 3px 10px rgba(84, 61, 37, 0.045) !important;
}
body.role-manager .ticket-card-shell-critical {
    background: linear-gradient(135deg, rgba(189, 83, 73, 0.10) 0%, #fffaf7 48%, #ffffff 100%) !important;
    border-color: rgba(189, 83, 73, 0.32) !important;
}
body.role-manager .ticket-card-main h3, body.role-manager .announcement-card__title, body.role-manager .admin-user-table-head h2 {
    color: var(--admin-surface-title) !important;
}
body.role-manager .announcement-card__date, body.role-manager .announcement-card__age, body.role-manager .announcement-card__record, body.role-manager .admin-user-table-head p {
    color: var(--admin-surface-muted) !important;
}
body.role-manager .admin-user-table tbody tr {
    background: var(--admin-surface-inner) !important;
    border-color: var(--admin-surface-border-soft) !important;
}
/* Overview-style manager architecture:compact headers, dense cards, and restrained actions. */
body.role-manager .admin-ticket-page > div:first-of-type, body.role-manager .admin-announcements-page > div:first-of-type, body.role-manager .admin-community-page > div:first-of-type, body.role-manager .admin-announce-create-page > div:first-of-type, body.role-manager .admin-announce-edit-page > div:first-of-type, body.role-manager .admin-announce-show-page > div:first-of-type, body.role-manager .admin-concern-hero, body.role-manager .admin-ticket-show-hero, body.role-manager .booking-hero, body.role-manager .admin-user-hero, body.role-manager .admin-user-form-hero {
    min-height: 0 !important;
    padding: 8px 2px 18px !important;
    border: 0 !important;
    border-radius: 0 !important;
    background: transparent !important;
    color: var(--admin-page-title) !important;
    box-shadow: none !important;
}
body.role-manager .admin-ticket-page > div:first-of-type > div.relative, body.role-manager .admin-announcements-page > div:first-of-type > div.relative, body.role-manager .admin-community-page > div:first-of-type > div.relative, body.role-manager .admin-announce-create-page > div:first-of-type > div.relative, body.role-manager .admin-announce-edit-page > div:first-of-type > div.relative, body.role-manager .admin-announce-show-page > div:first-of-type > div.relative {
    padding: 0 !important;
}
body.role-manager .admin-ticket-page > div:first-of-type > .absolute, body.role-manager .admin-announcements-page > div:first-of-type > .absolute, body.role-manager .admin-community-page > div:first-of-type > .absolute, body.role-manager .admin-announce-create-page > div:first-of-type > .absolute, body.role-manager .admin-announce-edit-page > div:first-of-type > .absolute, body.role-manager .admin-announce-show-page > div:first-of-type > .absolute, body.role-manager .admin-ticket-page > div:first-of-type::before, body.role-manager .admin-concern-hero::before, body.role-manager .admin-ticket-show-hero::before, body.role-manager .booking-hero::before {
    display: none !important;
}
body.role-manager .admin-ticket-page > div:first-of-type h1, body.role-manager .admin-announcements-page > div:first-of-type h1, body.role-manager .admin-community-page > div:first-of-type h1, body.role-manager .admin-announce-create-page > div:first-of-type h1, body.role-manager .admin-announce-edit-page > div:first-of-type h1, body.role-manager .admin-announce-show-page > div:first-of-type h1, body.role-manager .admin-concern-title, body.role-manager .admin-ticket-show-title, body.role-manager .booking-title, body.role-manager .admin-user-hero h1, body.role-manager .admin-user-form-hero h1 {
    margin: 3px 0 0 !important;
    color: var(--admin-page-title) !important;
    font-family: 'Playfair Display', serif !important;
    font-size: clamp(1.85rem, 3vw, 2.35rem) !important;
    line-height: 1.15 !important;
    letter-spacing: 0 !important;
}
body.role-manager .admin-ticket-page > div:first-of-type p, body.role-manager .admin-announcements-page > div:first-of-type p, body.role-manager .admin-community-page > div:first-of-type p, body.role-manager .admin-announce-create-page > div:first-of-type p, body.role-manager .admin-announce-edit-page > div:first-of-type p, body.role-manager .admin-announce-show-page > div:first-of-type p, body.role-manager .admin-concern-subtitle, body.role-manager .admin-ticket-show-subtitle, body.role-manager .booking-subtitle, body.role-manager .admin-user-hero p, body.role-manager .admin-user-form-hero p {
    margin-top: 5px !important;
    color: var(--admin-page-body) !important;
    font-size: 0.82rem !important;
    line-height: 1.5 !important;
}
body.role-manager .admin-ticket-page > div:first-of-type .mb-3, body.role-manager .admin-announcements-page > div:first-of-type .mb-3, body.role-manager .admin-community-page > div:first-of-type .mb-3, body.role-manager .admin-announce-create-page > div:first-of-type .mb-3, body.role-manager .admin-announce-edit-page > div:first-of-type .mb-3, body.role-manager .admin-announce-show-page > div:first-of-type .mb-3 {
    margin-bottom: 2px !important;
}
body.role-manager .admin-ticket-page > div:first-of-type h1 br, body.role-manager .admin-announcements-page > div:first-of-type h1 br, body.role-manager .admin-community-page > div:first-of-type h1 br, body.role-manager .admin-announce-create-page > div:first-of-type h1 br, body.role-manager .admin-announce-edit-page > div:first-of-type h1 br, body.role-manager .admin-announce-show-page > div:first-of-type h1 br {
    display: none !important;
}
body.role-manager .admin-ticket-page > div:first-of-type h1 span, body.role-manager .admin-announcements-page > div:first-of-type h1 span, body.role-manager .admin-community-page > div:first-of-type h1 span, body.role-manager .admin-announce-create-page > div:first-of-type h1 span, body.role-manager .admin-announce-edit-page > div:first-of-type h1 span, body.role-manager .admin-announce-show-page > div:first-of-type h1 span {
    color: var(--admin-page-title) !important;
}
body.role-manager .admin-metrics-grid, body.role-manager .stats-grid, body.role-manager .admin-concern-stats, body.role-manager .admin-user-stats, body.role-manager .admin-feature-stat-grid {
    gap: 12px !important;
}
body.role-manager .metric-card, body.role-manager .admin-metric-card, body.role-manager .booking-dashboard .stat-card, body.role-manager .admin-concern-stat, body.role-manager .admin-user-stat, body.role-manager .admin-feature-stat-grid > div {
    min-height: 0 !important;
    padding: 16px 18px !important;
    border-radius: 12px !important;
    gap: 14px !important;
}
body.role-manager .admin-metric-value, body.role-manager .booking-dashboard .stat-card .stat-value, body.role-manager .admin-concern-stat strong, body.role-manager .admin-user-stat .text-xl, body.role-manager .admin-feature-stat-grid [class*="text-4xl"], body.role-manager .admin-feature-stat-grid [class*="text-[34px]"] {
    font-size: 1.8rem !important;
}
body.role-manager .admin-panel-card, body.role-manager .admin-main-panel, body.role-manager .admin-form-panel, body.role-manager .admin-ticket-panel, body.role-manager .admin-ticket-show-panel, body.role-manager .admin-concern-card, body.role-manager .booking-panel, body.role-manager .announcement-standards-panel, body.role-manager .admin-user-toolbar, body.role-manager .admin-user-table-card {
    border-radius: 12px !important;
    box-shadow: 0 3px 10px rgba(84, 61, 37, 0.035) !important;
}
body.role-manager .admin-primary-btn, body.role-manager .admin-secondary-btn, body.role-manager .admin-ticket-action, body.role-manager .admin-ticket-show-back, body.role-manager .admin-concern-btn, body.role-manager .admin-concern-link, body.role-manager .admin-status-link, body.role-manager .announcement-action, body.role-manager .admin-user-primary-action, body.role-manager .nav-pill, body.role-manager .mode-pill {
    min-height: 36px !important;
    padding: 8px 11px !important;
    border-radius: 8px !important;
    font-size: 0.74rem !important;
    line-height: 1.1 !important;
}
body.role-manager .ticket-card-shell, body.role-manager .announcement-card, body.role-manager .admin-status-card, body.role-manager .admin-concern-row, body.role-manager .admin-detail-item, body.role-manager .admin-detail-block, body.role-manager .summary-row, body.role-manager .history-row {
    border-radius: 10px !important;
}
body.role-manager .admin-community-review-panel {
    padding: 16px !important;
    border: 1px solid var(--admin-surface-border) !important;
    border-radius: 12px !important;
    background: var(--admin-surface) !important;
    box-shadow: 0 3px 10px rgba(84, 61, 37, 0.035) !important;
}
body.role-manager .admin-community-review-panel > div:first-child {
    margin-bottom: 14px !important;
}
body.role-manager .admin-community-review-card {
    margin-bottom: 10px !important;
    padding: 14px 16px !important;
    border: 1px solid var(--admin-surface-border-soft) !important;
    border-radius: 10px !important;
    background: var(--admin-surface-inner) !important;
    box-shadow: none !important;
}
body.role-manager .admin-community-review-card h3 {
    font-size: 0.95rem !important;
}
body.role-manager .admin-community-review-card p, body.role-manager .admin-community-empty p {
    color: var(--admin-surface-body) !important;
    font-size: 0.82rem !important;
    line-height: 1.5 !important;
}
body.role-manager .admin-community-action {
    min-height: 36px !important;
    padding: 8px 11px !important;
    border-radius: 8px !important;
    box-shadow: none !important;
    font-size: 0.74rem !important;
}
body.role-manager .admin-community-empty {
    padding: 32px 20px !important;
    border-radius: 10px !important;
    background: #fbf8f3 !important;
}
body.role-manager .announcement-action--details, body.role-manager .announcement-action--edit {
    border-color: rgba(185, 130, 47, 0.22) !important;
    background: rgba(185, 130, 47, 0.10) !important;
    color: #8b5b1d !important;
}
body.role-manager .announcement-action--delete {
    border-color: rgba(189, 83, 73, 0.24) !important;
    background: rgba(189, 83, 73, 0.10) !important;
    color: #a3433b !important;
}
body.role-manager .admin-user-form-card {
    padding: 18px !important;
}
body.role-manager .admin-user-form-card .flex.gap-3 {
    border-color: var(--admin-surface-border-soft) !important;
}
/* Exact Overview density for manager workflow pages. */
body.role-manager .admin-concern-kicker, body.role-manager .booking-kicker, body.role-manager .admin-user-kicker, body.role-manager .admin-ticket-page > div:first-of-type .mb-3, body.role-manager .admin-announcements-page > div:first-of-type .mb-3, body.role-manager .admin-community-page > div:first-of-type .mb-3, body.role-manager .admin-announce-create-page > div:first-of-type .mb-3, body.role-manager .admin-announce-edit-page > div:first-of-type .mb-3, body.role-manager .admin-announce-show-page > div:first-of-type .mb-3 {
    color: var(--admin-gold) !important;
    font-size: 0.68rem !important;
    font-weight: 700 !important;
    letter-spacing: 0.16em !important;
    line-height: 1.2 !important;
    text-transform: uppercase !important;
}
body.role-manager .admin-concern-kicker::before, body.role-manager .booking-kicker::before, body.role-manager .admin-ticket-page > div:first-of-type .mb-3.flex.items-center.gap-3 span:first-child, body.role-manager .admin-community-page > div:first-of-type .mb-3.flex.items-center.gap-3 > div:first-child {
    display: none !important;
}
body.role-manager .admin-ticket-page > div:first-of-type .mb-3.flex.items-center.gap-3, body.role-manager .admin-community-page > div:first-of-type .mb-3.flex.items-center.gap-3 {
    gap: 0 !important;
    margin-bottom: 2px !important;
}
body.role-manager .admin-ticket-page > div:first-of-type .mb-3.flex.items-center.gap-3 span:last-child {
    color: var(--admin-gold) !important;
    font-size: 0.68rem !important;
    letter-spacing: 0.16em !important;
}
body.role-manager .admin-metric-label, body.role-manager .booking-dashboard .stat-card .stat-label, body.role-manager .admin-concern-stat span, body.role-manager .admin-user-stat span, body.role-manager .admin-feature-stat-grid span {
    color: var(--admin-page-body) !important;
    font-size: 0.76rem !important;
    letter-spacing: 0 !important;
    text-transform: none !important;
}
body.role-manager .metric-card, body.role-manager .admin-metric-card, body.role-manager .booking-dashboard .stat-card, body.role-manager .admin-concern-stat, body.role-manager .admin-user-stat, body.role-manager .admin-feature-stat-grid > div, body.role-manager .admin-panel-card, body.role-manager .admin-main-panel, body.role-manager .admin-form-panel, body.role-manager .admin-ticket-panel, body.role-manager .admin-ticket-show-panel, body.role-manager .admin-concern-card, body.role-manager .booking-panel, body.role-manager .announcement-standards-panel, body.role-manager .admin-user-toolbar, body.role-manager .admin-user-table-card {
    border: 1px solid var(--admin-card-border) !important;
    border-radius: 16px !important;
    background: var(--admin-card-strong) !important;
    box-shadow: 0 10px 30px rgba(79, 58, 44, 0.12) !important;
}
body.role-manager .admin-feature-stat-grid > div > .relative.p-6 {
    padding: 0 !important;
}
body.role-manager .admin-ticket-panel, body.role-manager .booking-panel, body.role-manager .admin-concern-card, body.role-manager .admin-panel-card {
    padding: 24px !important;
}
body.role-manager .ticket-card-body, body.role-manager .announcement-card, body.role-manager .admin-status-card, body.role-manager .admin-concern-row {
    padding: 14px 16px !important;
}
body.role-manager .panel-heading h2, body.role-manager .admin-ticket-panel h2, body.role-manager .admin-concern-section-head h2, body.role-manager .admin-user-table-head h2 {
    color: var(--admin-page-title) !important;
    font-family: 'Playfair Display', serif !important;
    font-size: 1.55rem !important;
    line-height: 1.15 !important;
}
body.role-manager .panel-heading p, body.role-manager .admin-ticket-panel-sub, body.role-manager .admin-concern-section-head p, body.role-manager .admin-user-table-head p {
    margin-top: 5px !important;
    color: var(--admin-page-body) !important;
    font-size: 0.82rem !important;
    line-height: 1.5 !important;
}
/* Final light-surface typography pass:match the Overview dashboard contrast ladder. */
body.role-manager .booking-dashboard {
    --booking-bg: #f8f4ed !important;
    --booking-panel: #ffffff !important;
    --booking-border: #e8e0d5 !important;
    --booking-border-strong: rgba(185, 130, 47, 0.34) !important;
    --booking-gold: #b47721 !important;
    --booking-gold-soft: #fbf3e4 !important;
    --booking-text: #342a23 !important;
    --booking-muted: #786b60 !important;
    --booking-muted-2: #9b8d81 !important;
    --booking-shadow: 0 3px 10px rgba(84, 61, 37, 0.035) !important;
    color: var(--admin-page-body) !important;
}
body.role-manager .panel-heading h2, body.role-manager .summary-row h3, body.role-manager .history-row h3, body.role-manager .ticket-card-main h3, body.role-manager .admin-status-card strong, body.role-manager .admin-concern-main h3, body.role-manager .admin-concern-section-head h2, body.role-manager .admin-detail-item strong, body.role-manager .admin-user-table-head h2, body.role-manager .admin-user-identity > span:first-child, body.role-manager .announcement-card__title, body.role-manager .admin-panel-title {
    color: var(--admin-page-title) !important;
}
body.role-manager .panel-heading p, body.role-manager .week-day-name, body.role-manager .week-day-count, body.role-manager .time-cell, body.role-manager .calendar-legend-item, body.role-manager .empty-slot, body.role-manager .booking-chip-time, body.role-manager .summary-row p, body.role-manager .history-row p, body.role-manager .ticket-card-main > p, body.role-manager .ticket-card-meta, body.role-manager .admin-status-card p, body.role-manager .admin-status-meta, body.role-manager .admin-concern-main p, body.role-manager .admin-concern-meta, body.role-manager .admin-concern-section-head p, body.role-manager .admin-detail-block p, body.role-manager .admin-detail-block-reply small, body.role-manager .admin-user-table-head p, body.role-manager .announcement-card__excerpt, body.role-manager .announcement-card__record, body.role-manager .announcement-card__date, body.role-manager .announcement-card__age {
    color: var(--admin-page-muted) !important;
}
body.role-manager .admin-detail-item span, body.role-manager .admin-detail-block span, body.role-manager .admin-form-label, body.role-manager .admin-user-form-card label {
    color: var(--admin-page-body) !important;
}
body.role-manager .admin-concern-page {
    color: var(--admin-page-body) !important;
}
body.role-manager .admin-community-review-card .text-sm, body.role-manager .admin-community-review-card .text-xs, body.role-manager .admin-community-review-card [style*="color:#B0A898"], body.role-manager .admin-community-review-card [style*="color:#8A7A66"] {
    color: var(--admin-page-muted) !important;
}
body.role-manager .calendar-legend, body.role-manager .week-day, body.role-manager .empty-slot, body.role-manager .summary-row, body.role-manager .history-row, body.role-manager .admin-detail-item, body.role-manager .admin-detail-block {
    background: var(--admin-surface-inner) !important;
    border-color: var(--admin-surface-border-soft) !important;
}
body.role-manager .date-picker-label input {
    color-scheme: light !important;
}
/* Submitted records:use clean white cards with simple lines against the off-white admin background. */
body.role-manager .ticket-card-shell, body.role-manager .announcement-card, body.role-manager .admin-community-review-card, body.role-manager .admin-concern-row, body.role-manager .booking-chip, body.role-manager .summary-row, body.role-manager .history-row {
    background: #ffffff !important;
    border-color: rgba(120, 100, 75, 0.14) !important;
    color: #786b60 !important;
    box-shadow: none !important;
}
body.role-manager .ticket-card-shell:hover, body.role-manager .announcement-card:hover, body.role-manager .admin-community-review-card:hover, body.role-manager .admin-concern-row:hover, body.role-manager .booking-chip:hover {
    background: #fcfaf6 !important;
    border-color: rgba(120, 100, 75, 0.24) !important;
    box-shadow: none !important;
}
body.role-manager .ticket-card-shell-critical {
    background: #ffffff !important;
    border-color: rgba(189, 83, 73, 0.34) !important;
}
body.role-manager .ticket-card-main h3, body.role-manager .announcement-card__title, body.role-manager .admin-community-review-card h3, body.role-manager .admin-concern-main h3, body.role-manager .summary-row h3, body.role-manager .history-row h3, body.role-manager .booking-chip-name {
    color: #342a23 !important;
}
body.role-manager .ticket-card-main > p, body.role-manager .ticket-card-meta, body.role-manager .announcement-card__excerpt, body.role-manager .announcement-card__record, body.role-manager .announcement-card__date, body.role-manager .announcement-card__age, body.role-manager .admin-community-review-card p, body.role-manager .admin-community-review-card .text-sm, body.role-manager .admin-community-review-card .text-xs, body.role-manager .admin-concern-main p, body.role-manager .admin-concern-meta, body.role-manager .summary-row p, body.role-manager .history-row p, body.role-manager .booking-chip-time {
    color: #9b8d81 !important;
}
body.role-manager .booking-dashboard .booking-chip-reserved {
    background: linear-gradient(135deg, #f0bc62 0%, #d89426 56%, #9b5f12 100%) !important;
    border-color: rgba(255, 238, 198, 0.48) !important;
    color: #fffaf0 !important;
    box-shadow: inset 0 -16px 28px rgba(87, 48, 8, 0.18), 0 10px 22px rgba(126, 74, 17, 0.22) !important;
}
body.role-manager .booking-dashboard .booking-chip-reserved:hover {
    background: linear-gradient(135deg, #f4c873 0%, #df9c2e 56%, #9b5f12 100%) !important;
    border-color: rgba(255, 248, 232, 0.74) !important;
    box-shadow: inset 0 -16px 28px rgba(87, 48, 8, 0.22), 0 15px 30px rgba(126, 74, 17, 0.32) !important;
}
body.role-manager .booking-dashboard .booking-chip-reserved .booking-chip-name, body.role-manager .booking-dashboard .booking-chip-reserved .booking-chip-residents, body.role-manager .booking-dashboard .booking-chip-reserved .booking-chip-time {
    color: #ffffff !important;
}
body.role-manager .booking-dashboard .booking-chip-reserved .booking-chip-residents, body.role-manager .booking-dashboard .booking-chip-reserved .booking-chip-time {
    color: rgba(255, 250, 240, 0.9) !important;
}
/* Keep resident workflow heroes compact and consistent across list-adjacent screens. */
body.role-resident .resident-ticket-hero, body.role-resident .resident-ticket-create-hero, body.role-resident .resident-ticket-edit-hero, body.role-resident .resident-booking-hero, body.role-resident .resident-booking-create-hero, body.role-resident .resident-booking-edit-hero, body.role-resident .resident-announcement-hero {
    min-height: 0 !important;
    padding: 20px 22px !important;
    border-radius: 20px !important;
    align-items: center !important;
    gap: 20px !important;
}
body.role-resident .resident-ticket-title, body.role-resident .resident-ticket-create-title, body.role-resident .resident-ticket-edit-title, body.role-resident .resident-booking-title, body.role-resident .resident-booking-create-title, body.role-resident .resident-booking-edit-title, body.role-resident .resident-announcement-title {
    font-size: clamp(1.7rem, 2.6vw, 2.45rem) !important;
    line-height: 1.08 !important;
    letter-spacing: 0 !important;
}
body.role-resident .resident-ticket-subtitle, body.role-resident .resident-ticket-create-subtitle, body.role-resident .resident-ticket-edit-subtitle, body.role-resident .resident-booking-subtitle, body.role-resident .resident-booking-create-subtitle, body.role-resident .resident-booking-edit-subtitle, body.role-resident .resident-announcement-subtitle {
    max-width: 760px !important;
    margin-top: 8px !important;
    font-size: 0.92rem !important;
    line-height: 1.45 !important;
}
body.role-resident .resident-ticket-hero-stats, body.role-resident .resident-ticket-create-stats, body.role-resident .resident-ticket-edit-stats, body.role-resident .resident-booking-hero-stats, body.role-resident .resident-booking-create-stats, body.role-resident .resident-booking-edit-stats, body.role-resident .resident-announcement-stat-row {
    margin-top: 14px !important;
    gap: 10px !important;
}
body.role-resident .resident-ticket-hero-stat, body.role-resident .resident-ticket-create-stat, body.role-resident .resident-ticket-edit-stat, body.role-resident .resident-booking-hero-stat, body.role-resident .resident-booking-create-stat, body.role-resident .resident-booking-edit-stat, body.role-resident .resident-announcement-stat {
    min-width: 112px !important;
    padding: 10px 14px !important;
}
body.role-resident .resident-ticket-hero-actions, body.role-resident .resident-ticket-create-hero-actions, body.role-resident .resident-ticket-edit-actions, body.role-resident .resident-booking-hero-actions, body.role-resident .resident-booking-create-actions, body.role-resident .resident-booking-edit-actions, body.role-resident .resident-announcement-hero-actions {
    align-self: center !important;
}
@media (max-width:768px) {
    body.role-resident .resident-ticket-hero, body.role-resident .resident-ticket-create-hero, body.role-resident .resident-ticket-edit-hero, body.role-resident .resident-booking-hero, body.role-resident .resident-booking-create-hero, body.role-resident .resident-booking-edit-hero, body.role-resident .resident-announcement-hero {
        padding: 18px !important;
        align-items: flex-start !important;
    }
}
/* Resident hero stats use one slim segmented strip instead of separate cards. */
body.role-resident:is( .resident-hero-stat-row, .resident-ticket-hero-stats, .resident-ticket-create-stats, .resident-ticket-edit-stats, .resident-booking-hero-stats, .resident-booking-create-stats, .resident-booking-edit-stats, .resident-announcement-stat-row ) {
    display: inline-flex !important;
    width: fit-content;
    max-width: 100%;
    flex-wrap: wrap !important;
    gap: 0 !important;
    margin-top: 14px !important;
    overflow: hidden;
    border: 1px solid rgba(214, 168, 91, 0.12);
    border-radius: 14px;
    background: rgba(24, 25, 28, 0.80);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.025);
}
body.role-resident:is( .resident-hero-stat, .resident-ticket-hero-stat, .resident-ticket-create-stat, .resident-ticket-edit-stat, .resident-booking-hero-stat, .resident-booking-create-stat, .resident-booking-edit-stat, .resident-announcement-stat ) {
    display: inline-flex !important;
    min-width: 0 !important;
    align-items: baseline;
    gap: 7px;
    padding: 11px 16px !important;
    border: 0 !important;
    border-radius: 0 !important;
    background: transparent !important;
    box-shadow: none !important;
}
body.role-resident:is( .resident-hero-stat, .resident-ticket-hero-stat, .resident-ticket-create-stat, .resident-ticket-edit-stat, .resident-booking-hero-stat, .resident-booking-create-stat, .resident-booking-edit-stat, .resident-announcement-stat ) +:is( .resident-hero-stat, .resident-ticket-hero-stat, .resident-ticket-create-stat, .resident-ticket-edit-stat, .resident-booking-hero-stat, .resident-booking-create-stat, .resident-booking-edit-stat, .resident-announcement-stat ) {
    border-left: 1px solid rgba(214, 168, 91, 0.12) !important;
}
body.role-resident:is( .resident-hero-stat, .resident-ticket-hero-stat, .resident-ticket-create-stat, .resident-ticket-edit-stat, .resident-booking-hero-stat, .resident-booking-create-stat, .resident-booking-edit-stat, .resident-announcement-stat ) span {
    display: inline !important;
    color: #b8ab98 !important;
    font-size: 0.66rem !important;
    font-weight: 800 !important;
    letter-spacing: 0.16em !important;
    line-height: 1.2;
    text-transform: uppercase;
    white-space: nowrap;
}
body.role-resident:is( .resident-hero-stat, .resident-ticket-hero-stat, .resident-ticket-create-stat, .resident-ticket-edit-stat, .resident-booking-hero-stat, .resident-booking-create-stat, .resident-booking-edit-stat, .resident-announcement-stat ) strong {
    display: inline !important;
    margin-top: 0 !important;
    color: #d6a85b !important;
    font-size: 0.88rem !important;
    font-weight: 800 !important;
    line-height: 1.2;
    white-space: nowrap;
}
@media (max-width:640px) {
    body.role-resident:is( .resident-hero-stat, .resident-ticket-hero-stat, .resident-ticket-create-stat, .resident-ticket-edit-stat, .resident-booking-hero-stat, .resident-booking-create-stat, .resident-booking-edit-stat, .resident-announcement-stat ) {
        padding: 10px 12px !important;
    }
}
/* Selectable cards:make the chosen value unmistakable and keyboard-visible. */
body.role-resident .resident-ticket-priority-card, body.role-resident .community-category-card {
    position: relative !important;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
    background: rgba(255, 255, 255, 0.035) !important;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.03);
    transition: transform 0.18s ease, border-color 0.18s ease, background 0.18s ease, box-shadow 0.18s ease !important;
}
body.role-resident .resident-ticket-priority-card::after, body.role-resident .community-category-card::after {
    content: '\2713';
    position: absolute;
    top: 12px;
    right: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 999px;
    background: rgba(0, 0, 0, 0.16);
    color: transparent;
    font-size: 14px;
    font-weight: 800;
    line-height: 1;
    transition: border-color 0.18s ease, background 0.18s ease, color 0.18s ease, transform 0.18s ease;
}
@media (hover:hover) {
    body.role-resident .resident-ticket-priority-card:hover, body.role-resident .community-category-card:hover {
        border-color: rgba(214, 168, 91, 0.42) !important;
        background: rgba(214, 168, 91, 0.075) !important;
        transform: translateY(-2px);
    }
}
body.role-resident .resident-ticket-priority-card:focus-within, body.role-resident .community-category-card:focus-within {
    outline: 2px solid rgba(214, 168, 91, 0.9);
    outline-offset: 3px;
}
body.role-resident .resident-ticket-priority-card.is-active, body.role-resident .resident-ticket-priority-card:has(input:checked), body.role-resident .community-category-card.is-active, body.role-resident .community-category-card:has(input:checked) {
    border-color: rgba(214, 168, 91, 0.72) !important;
    background: rgba(214, 168, 91, 0.14) !important;
    box-shadow: 0 0 0 3px rgba(214, 168, 91, 0.1), inset 0 1px 0 rgba(255, 255, 255, 0.08) !important;
    transform: translateY(-1px);
}
body.role-resident .resident-ticket-priority-card.is-active::after, body.role-resident .resident-ticket-priority-card:has(input:checked)::after, body.role-resident .community-category-card.is-active::after, body.role-resident .community-category-card:has(input:checked)::after {
    border-color: rgba(214, 168, 91, 0.86);
    background: #d6a85b;
    color: #1b150f;
    transform: scale(1.04);
}
body.role-resident .resident-ticket-priority-card.is-active[data-priority="low"], body.role-resident .resident-ticket-priority-card[data-priority="low"]:has(input:checked) {
    border-color: rgba(120, 170, 120, 0.76) !important;
    background: rgba(120, 170, 120, 0.15) !important;
    box-shadow: 0 0 0 3px rgba(120, 170, 120, 0.1) !important;
}
body.role-resident .resident-ticket-priority-card.is-active[data-priority="low"]::after, body.role-resident .resident-ticket-priority-card[data-priority="low"]:has(input:checked)::after {
    border-color: #8fbd85;
    background: #8fbd85;
}
body.role-resident .resident-ticket-priority-card.is-active[data-priority="critical"], body.role-resident .resident-ticket-priority-card[data-priority="critical"]:has(input:checked) {
    border-color: rgba(224, 112, 96, 0.8) !important;
    background: rgba(224, 112, 96, 0.15) !important;
    box-shadow: 0 0 0 3px rgba(224, 112, 96, 0.1) !important;
}
body.role-resident .resident-ticket-priority-card.is-active[data-priority="critical"]::after, body.role-resident .resident-ticket-priority-card[data-priority="critical"]:has(input:checked)::after {
    border-color: #e07060;
    background: #e07060;
    color: #24110e;
}
body.role-resident .resident-booking-slot {
    position: relative;
    transition: transform 0.18s ease, border-color 0.18s ease, background 0.18s ease, box-shadow 0.18s ease !important;
}
body.role-resident .resident-booking-slot.is-available:hover {
    border-color: rgba(120, 170, 120, 0.56) !important;
    background: rgba(120, 170, 120, 0.16) !important;
    transform: translateY(-2px);
}
body.role-resident .resident-booking-slot.is-selected::after {
    content: '\2713';
    position: absolute;
    top: 8px;
    right: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 21px;
    height: 21px;
    border-radius: 999px;
    background: #d6a85b;
    color: #1b150f;
    font-size: 12px;
    font-weight: 800;
    line-height: 1;
}
/* Resident empty states share one calm, warm treatment across every feature. */
body.role-resident .resident-unified-empty {
    display: flex;
    width: 100%;
    min-height: 190px;
    padding: 34px 28px;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border: 1px solid rgba(214, 168, 91, 0.18);
    border-radius: 18px;
    background: var(--resident-solid-card-alt);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.025);
    color: #f4eadc;
    text-align: center;
}
body.role-resident .resident-unified-empty-icon {
    display: grid;
    width: 52px;
    height: 52px;
    margin-bottom: 16px;
    place-items: center;
    border: 1px solid rgba(214, 168, 91, 0.28);
    border-radius: 16px;
    background: linear-gradient(135deg, rgba(214, 168, 91, 0.18), rgba(214, 168, 91, 0.06));
    color: #e2b661;
}
body.role-resident .resident-unified-empty-icon svg {
    width: 25px;
    height: 25px;
    fill: none;
    stroke: currentColor;
    stroke-linecap: round;
    stroke-linejoin: round;
    stroke-width: 1.65;
}
body.role-resident .resident-unified-empty h3 {
    margin: 0;
    color: #f4eadc;
    font-family: 'Playfair Display', serif;
    font-size: 1.5rem;
    font-weight: 600;
    letter-spacing: -0.01em;
    line-height: 1.18;
}
body.role-resident .resident-unified-empty p {
    max-width: 620px;
    margin: 10px auto 0;
    color: #c9bda9;
    font-size: 0.96rem;
    line-height: 1.65;
}
body.role-resident .resident-unified-empty-action {
    display: inline-flex;
    min-height: 42px;
    margin-top: 20px;
    padding: 0 20px;
    align-items: center;
    justify-content: center;
    border: 1px solid rgba(232, 190, 105, 0.7);
    border-radius: 999px;
    background: linear-gradient(135deg, #c9943f, #e0b35c);
    box-shadow: 0 10px 22px rgba(199, 151, 69, 0.16);
    color: #24190c;
    font-size: 0.82rem;
    font-weight: 800;
    letter-spacing: 0.02em;
    text-decoration: none;
    transition: transform 0.18s ease, box-shadow 0.18s ease, filter 0.18s ease;
}
body.role-resident .resident-unified-empty-action:hover {
    box-shadow: 0 14px 26px rgba(199, 151, 69, 0.22);
    filter: brightness(1.04);
    transform: translateY(-1px);
}
body.role-resident .resident-unified-empty-compact {
    min-height: 138px;
    padding: 24px 20px;
}
body.role-resident .resident-unified-empty-compact .resident-unified-empty-icon {
    width: 42px;
    height: 42px;
    margin-bottom: 12px;
    border-radius: 13px;
}
body.role-resident .resident-unified-empty-compact .resident-unified-empty-icon svg {
    width: 20px;
    height: 20px;
}
body.role-resident .resident-unified-empty-compact h3 {
    font-size: 1.08rem;
}
body.role-resident .resident-unified-empty-compact p {
    margin-top: 7px;
    font-size: 0.86rem;
}
html.theme-light body.role-resident .resident-unified-empty {
    border-color: rgba(111, 78, 45, 0.2);
    background: linear-gradient(180deg, rgba(255, 251, 244, 0.9), rgba(244, 231, 211, 0.9));
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.72);
}
html.theme-light body.role-resident .resident-unified-empty h3 {
    color: #3c2f23;
}
html.theme-light body.role-resident .resident-unified-empty p {
    color: #6d5a48;
}
html.theme-light body.role-resident .resident-unified-empty-icon {
    border-color: rgba(166, 109, 36, 0.24);
    background: rgba(199, 151, 69, 0.13);
    color: #a66d24;
}
/* Resident forms use the same solid operational surfaces as list and detail pages. */
body.role-resident .resident-ticket-hero, body.role-resident .resident-ticket-create-hero, body.role-resident .resident-ticket-edit-hero, body.role-resident .resident-booking-hero, body.role-resident .resident-booking-create-hero, body.role-resident .resident-booking-edit-hero, body.role-resident .resident-announcement-hero {
    background: var(--resident-solid-panel) !important;
    border-color: rgba(214, 168, 91, 0.22) !important;
    box-shadow: 0 14px 30px rgba(0, 0, 0, 0.2) !important;
    backdrop-filter: none !important;
    -webkit-backdrop-filter: none !important;
}
body.role-resident .resident-ticket-create-panel, body.role-resident .resident-ticket-edit-panel, body.role-resident .resident-booking-create-panel, body.role-resident .resident-booking-edit-panel, body.role-resident .community-create-panel, body.role-resident .community-edit-panel, body.role-resident .concern-card {
    background: var(--resident-solid-panel) !important;
    border-color: rgba(214, 168, 91, 0.2) !important;
    box-shadow: 0 14px 30px rgba(0, 0, 0, 0.2) !important;
    backdrop-filter: none !important;
    -webkit-backdrop-filter: none !important;
}
body.role-resident .resident-ticket-edit-subpanel, body.role-resident .resident-ticket-upload-panel, body.role-resident .resident-booking-edit-note-item, body.role-resident .resident-booking-edit-meta-item, body.role-resident .community-create-subpanel, body.role-resident .community-edit-subpanel, body.role-resident .community-create-composer, body.role-resident .community-edit-composer {
    background: var(--resident-solid-panel-alt) !important;
    border-color: rgba(255, 244, 225, 0.1) !important;
    box-shadow: none !important;
    backdrop-filter: none !important;
    -webkit-backdrop-filter: none !important;
}
body.role-resident .resident-ticket-create-input, body.role-resident .resident-ticket-create-input-file, body.role-resident .resident-ticket-edit-input, body.role-resident .resident-booking-create-input, body.role-resident .resident-booking-edit-input, body.role-resident .community-create-input, body.role-resident .community-edit-input, body.role-resident .concern-input {
    background: var(--resident-solid-card-alt) !important;
    border-color: rgba(214, 168, 91, 0.18) !important;
    color: #f4eadc !important;
    box-shadow: none !important;
}
body.role-resident .resident-ticket-create-input:focus, body.role-resident .resident-ticket-edit-input:focus, body.role-resident .resident-booking-create-input:focus, body.role-resident .resident-booking-edit-input:focus, body.role-resident .community-create-input:focus, body.role-resident .community-edit-input:focus, body.role-resident .concern-input:focus {
    border-color: rgba(214, 168, 91, 0.62) !important;
    box-shadow: 0 0 0 3px rgba(214, 168, 91, 0.12) !important;
}
body.role-resident .resident-ticket-edit-input-readonly {
    background: var(--resident-solid-card) !important;
    color: #b8ab98 !important;
}
[data-progressive-item].app-progressive-hidden {
    display: none !important;
}
.app-soft-divider,
.hs-analytics-divider {
    width: 100%;
    height: 1px;
    margin: 2.5rem 0;
    border: 0;
    background: linear-gradient(90deg, transparent, rgba(120, 107, 96, 0.14), transparent);
}
.app-progressive-action {
    display: flex;
    justify-content: flex-end;
    padding-top: 16px;
}
.app-progressive-toggle {
    display: inline-flex;
    min-height: 34px;
    align-items: center;
    gap: 7px;
    padding: 7px 11px;
    border: 1px solid rgba(214, 168, 91, 0.24);
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.035);
    color: #d6a85b;
    font-size: 0.76rem;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.18s ease, border-color 0.18s ease, color 0.18s ease;
}
.app-progressive-toggle:hover {
    border-color: rgba(214, 168, 91, 0.36);
    background: rgba(214, 168, 91, 0.09);
}
:is(.app-progressive-toggle, .resident-see-more-btn, .admin-collapsible-toggle)::after {
    content: "";
    width: 7px;
    height: 7px;
    border-right: 1.5px solid currentColor;
    border-bottom: 1.5px solid currentColor;
    transform: translateY(-2px) rotate(45deg);
    transition: transform 0.18s ease;
}
:is(.app-progressive-toggle, .resident-see-more-btn, .admin-collapsible-toggle)[aria-expanded="true"]::after {
    transform: translateY(2px) rotate(225deg);
}
body.role-resident .resident-see-more-btn {
    display: inline-flex !important;
    min-height: 34px;
    align-items: center;
    gap: 7px;
    padding: 7px 11px !important;
    border: 1px solid rgba(214, 168, 91, 0.24) !important;
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.035) !important;
    color: var(--resident-accent) !important;
    font-size: 0.76rem;
    line-height: 1;
    transition: background 0.18s ease, border-color 0.18s ease, color 0.18s ease;
}
body.role-resident .resident-see-more-btn:hover {
    border-color: rgba(214, 168, 91, 0.42) !important;
    background: rgba(214, 168, 91, 0.09) !important;
}
html.theme-light body.role-resident .resident-see-more-btn {
    border-color: #e3cfaf !important;
    background: #fffdf9 !important;
    color: var(--resident-light-accent) !important;
}
html.theme-light body.role-resident .resident-see-more-btn:hover {
    border-color: #cfaa72 !important;
    background: #fff8ed !important;
}
/* Final manager workspace pass:one dashboard language across every admin route. */
body.role-manager .admin-content-shell {
    --manager-workspace-bg: #f7f2eb;
    --manager-panel-bg: #ffffff;
    --manager-panel-border: #e5dccf;
    --manager-panel-divider: #f0e9df;
    --manager-content-bg: #fffdf9;
    --manager-content-title: #342a23;
    --manager-content-body: #6f6256;
    --manager-content-muted: #95877a;
    --manager-accent-soft: #fff8ed;
    --manager-content-shadow: 0 4px 12px rgba(84, 61, 37, 0.055);
}
body.role-manager .admin-content-shell:not(.dashboard-shell), body.role-manager .dashboard-shell .hs-dashboard {
    background: var(--manager-workspace-bg) !important;
}
body.role-manager .admin-shell.space-y-8 >:not([hidden]) ~:not([hidden]) {
    margin-top: 18px !important;
}
body.role-manager .admin-user-page.space-y-6 >:not([hidden]) ~:not([hidden]) {
    margin-top: 16px !important;
}
body.role-manager .admin-content-shell:is( .hs-card, .admin-panel-card, .admin-main-panel, .admin-form-panel, .admin-ticket-panel, .admin-ticket-show-panel, .booking-panel, .admin-concern-card, .admin-user-toolbar, .admin-user-table-card, .admin-community-review-panel ) {
    border: 1px solid var(--manager-panel-border) !important;
    border-radius: 12px !important;
    background: var(--manager-panel-bg) !important;
    box-shadow: var(--manager-content-shadow) !important;
}
body.role-manager .admin-content-shell:is( .hs-metric-card, .hs-diagnostics article, .hs-forecast-stat, .hs-log-summary article, .metric-card, .admin-metric-card, .ticket-card-shell, .admin-status-card, .booking-dashboard .stat-card, .admin-concern-stat, .admin-concern-row, .admin-user-stat, .announcement-card, .admin-community-review-card, .admin-feature-stat-grid > div ) {
    /* Deliberately avoid decorative edge bars; use surface tint and border contrast only. */
    border: 1px solid var(--manager-panel-border) !important;
    border-radius: 10px !important;
    background: linear-gradient(135deg, var(--manager-card-tint, var(--manager-accent-soft)) 0%, var(--manager-content-bg) 62%, #ffffff 100%) !important;
    box-shadow: var(--manager-content-shadow) !important;
}
body.role-manager .admin-content-shell:is( .hs-metrics, .stats-grid, .admin-metrics-grid, .admin-concern-stats, .admin-user-stats, .admin-feature-stat-grid ) >:nth-child(4n + 1) {
    --manager-card-tint: #fff8ed;
}
body.role-manager .admin-content-shell:is( .hs-metrics, .stats-grid, .admin-metrics-grid, .admin-concern-stats, .admin-user-stats, .admin-feature-stat-grid ) >:nth-child(4n + 2) {
    --manager-card-tint: #f3f9f3;
}
body.role-manager .admin-content-shell:is( .hs-metrics, .stats-grid, .admin-metrics-grid, .admin-concern-stats, .admin-user-stats, .admin-feature-stat-grid ) >:nth-child(4n + 3) {
    --manager-card-tint: #f2f7f9;
}
body.role-manager .admin-content-shell:is( .hs-metrics, .stats-grid, .admin-metrics-grid, .admin-concern-stats, .admin-user-stats, .admin-feature-stat-grid ) >:nth-child(4n + 4) {
    --manager-card-tint: #fff6f4;
}
body.role-manager .admin-content-shell .ticket-card-shell-critical {
    --manager-card-tint: #fff6f4;
}
body.role-manager .dashboard-shell .hs-alert-panel {
    box-shadow: var(--manager-content-shadow) !important;
}
body.role-manager .dashboard-shell .hs-actions-panel {
    box-shadow: var(--manager-content-shadow) !important;
}
body.role-manager .admin-content-shell:is( .hs-card-heading, .panel-heading, .admin-ticket-panel-head, .admin-user-table-head ) {
    border-color: var(--manager-panel-divider) !important;
}
body.role-manager .admin-content-shell:is( .ticket-card-main h3, .admin-status-card strong, .admin-concern-main h3, .announcement-card__title, .admin-community-review-card h3, .admin-user-identity > span:first-child, .summary-row h3, .history-row h3 ) {
    color: var(--manager-content-title) !important;
    font-size: 0.92rem !important;
    line-height: 1.35 !important;
}
/*
     * Manager section typography follows the Overview dashboard:restrained
     * Playfair headings with compact DM Sans support copy.
     */
body.role-manager .admin-content-shell:is( .hs-section-heading h2, .admin-ticket-panel-title, .admin-ticket-show-panel h2, .admin-concern-head h2, .admin-concern-section-head h2, .booking-panel .panel-heading h2, .admin-panel-title, .admin-panel-card > div:first-child h2, .admin-community-review-panel > div:first-child h2, .access-panel-head h2, .access-form-head h2, .account-panel-head h2 ) {
    margin: 0 !important;
    color: var(--manager-content-title) !important;
    font-family: 'Playfair Display', serif !important;
    font-size: 1.55rem !important;
    font-weight: 400 !important;
    letter-spacing: -0.015em !important;
    line-height: 1.15 !important;
}
body.role-manager .admin-content-shell:is( .hs-section-heading > div > p:last-child:not(.hs-eyebrow), .admin-ticket-panel-sub, .admin-ticket-show-panel p, .admin-concern-head p, .admin-concern-section-head p, .booking-panel .panel-heading p, .admin-panel-sub, .admin-panel-card > div:first-child p, .admin-community-review-panel > div:first-child p ) {
    margin-top: 5px !important;
    color: var(--manager-content-body) !important;
    font-family: 'DM Sans', sans-serif !important;
    font-size: 0.82rem !important;
    font-weight: 400 !important;
    line-height: 1.5 !important;
}
body.role-manager .admin-content-shell:is( .hs-eyebrow, .access-eyebrow, .account-panel-head > div > p ) {
    color: var(--manager-content-accent) !important;
    font-family: 'DM Sans', sans-serif !important;
    font-size: 0.68rem !important;
    font-weight: 700 !important;
    letter-spacing: 0.16em !important;
    line-height: 1.2 !important;
    text-transform: uppercase !important;
}
body.role-manager .admin-content-shell:is( .ticket-card-main > p, .admin-status-card p, .admin-concern-main p, .announcement-card__excerpt, .admin-community-review-card p, .summary-row p, .history-row p ) {
    color: var(--manager-content-body) !important;
    font-size: 0.82rem !important;
    line-height: 1.5 !important;
}
body.role-manager .admin-content-shell:is( .ticket-card-meta, .admin-status-meta, .admin-concern-meta, .announcement-card__record, .announcement-card__date, .announcement-card__age ) {
    color: var(--manager-content-muted) !important;
    font-size: 0.72rem !important;
}
@media (min-width:1101px) {
    body.role-manager .dashboard-shell {
        max-width: 1580px !important;
    }
    body.role-manager .dashboard-shell .hs-dashboard {
        width: 100%;
        max-width: 1580px !important;
        margin: 0 auto;
        zoom: 1;
    }
}
@media (max-width:1100px) {
    body.role-manager .dashboard-shell .hs-dashboard {
        width: 100%;
        zoom: 1;
    }
}
/* Compact manager summary cards:reserve vertical space for actual content. */
body.role-manager .admin-content-shell:is( .hs-metrics, .stats-grid, .admin-metrics-grid, .admin-concern-stats, .admin-user-stats, .admin-feature-stat-grid ) {
    gap: 8px !important;
}
body.role-manager .admin-content-shell:is( .hs-metric-card, .metric-card, .admin-metric-card, .booking-dashboard .stat-card, .admin-concern-stat, .admin-user-stat, .admin-feature-stat-grid > div ) {
    min-height: 0 !important;
    gap: 10px !important;
    padding: 10px 13px !important;
    border-radius: 10px !important;
}
body.role-manager .admin-content-shell:is( .hs-metric-icon, .admin-metric-icon, .admin-user-stat-icon ) {
    width: 34px !important;
    height: 34px !important;
    border-radius: 9px !important;
}
body.role-manager .admin-content-shell:is( .hs-metric-icon, .admin-metric-icon ) svg {
    width: 17px !important;
    height: 17px !important;
}
body.role-manager .admin-content-shell:is( .hs-metric-card strong, .metric-card .metric-value, .admin-metric-value, .booking-dashboard .stat-card .stat-value, .admin-concern-stat strong, .admin-user-stat .text-xl, .admin-feature-stat-grid [class*="text-4xl"], .admin-feature-stat-grid [class*="text-[34px]"] ) {
    font-size: 1.4rem !important;
    line-height: 1 !important;
}
body.role-manager .admin-content-shell:is( .hs-metric-card p, .hs-metric-card span, .metric-card .metric-label, .admin-metric-label, .booking-dashboard .stat-card .stat-label, .admin-concern-stat span, .admin-user-stat .text-xs, .admin-feature-stat-grid .font-semibold.uppercase ) {
    font-size: 0.7rem !important;
    line-height: 1.25 !important;
}
body.role-manager .admin-content-shell:is( .metric-card .metric-sub, .admin-metric-sub, .booking-dashboard .stat-card .stat-note, .admin-concern-stat small, .admin-feature-stat-grid .text-xs.mt-1 ) {
    font-size: 0.66rem !important;
    line-height: 1.3 !important;
}
body.role-manager .admin-content-shell .admin-feature-stat-grid > div > .relative {
    padding: 0 !important;
}
body.role-manager .admin-content-shell .admin-feature-stat-grid > div > .relative > .flex:first-child {
    margin-bottom: 7px !important;
}
/* Manager hero typography:remove the remaining dark-theme text colors from admin headers. */
body.role-manager .admin-content-shell {
    --manager-content-accent: #8b5b1d;
}
body.role-manager .admin-content-shell:is( .admin-ticket-page > div:first-of-type, .admin-announcements-page > div:first-of-type, .admin-community-page > div:first-of-type, .admin-announce-create-page > div:first-of-type, .admin-announce-edit-page > div:first-of-type, .admin-announce-show-page > div:first-of-type, .admin-concern-hero, .admin-ticket-show-hero, .booking-hero, .admin-user-hero, .admin-user-form-hero ) {
    color: var(--manager-content-title) !important;
}
body.role-manager .admin-content-shell:is( .admin-ticket-page > div:first-of-type h1, .admin-announcements-page > div:first-of-type h1, .admin-community-page > div:first-of-type h1, .admin-announce-create-page > div:first-of-type h1, .admin-announce-edit-page > div:first-of-type h1, .admin-announce-show-page > div:first-of-type h1, .admin-concern-title, .admin-ticket-show-title, .booking-title, .admin-user-hero h1, .admin-user-form-hero h1 ), body.role-manager .admin-content-shell:is( .admin-ticket-page > div:first-of-type h1, .admin-announcements-page > div:first-of-type h1, .admin-community-page > div:first-of-type h1, .admin-announce-create-page > div:first-of-type h1, .admin-announce-edit-page > div:first-of-type h1, .admin-announce-show-page > div:first-of-type h1 ) span {
    color: var(--manager-content-title) !important;
}
/* Match the dashboard greeting's restrained Playfair weight across admin heroes. */
body.role-manager .admin-content-shell:is( .admin-ticket-page > div:first-of-type h1, .booking-title, .admin-announcements-page > div:first-of-type h1, .admin-community-page > div:first-of-type h1 ), body.role-manager .admin-content-shell:is( .admin-ticket-page > div:first-of-type h1, .admin-announcements-page > div:first-of-type h1, .admin-community-page > div:first-of-type h1 ) span {
    font-weight: 400 !important;
}
body.role-manager .admin-content-shell:is( .admin-ticket-page > div:first-of-type p, .admin-announcements-page > div:first-of-type p, .admin-community-page > div:first-of-type p, .admin-announce-create-page > div:first-of-type p, .admin-announce-edit-page > div:first-of-type p, .admin-announce-show-page > div:first-of-type p, .admin-concern-subtitle, .admin-ticket-show-subtitle, .booking-subtitle, .admin-user-hero p, .admin-user-form-hero p ) {
    color: var(--manager-content-body) !important;
}
body.role-manager .admin-content-shell:is( .admin-concern-kicker, .admin-ticket-show-kicker, .booking-kicker, .access-kicker, .access-form-hero > p, .admin-user-kicker, .admin-ticket-page > div:first-of-type .mb-3 span:last-child, .admin-announcements-page > div:first-of-type .mb-3 span:last-child, .admin-community-page > div:first-of-type .mb-3 span:last-child, .admin-announce-create-page > div:first-of-type .mb-3 span:last-child, .admin-announce-edit-page > div:first-of-type .mb-3 span:last-child, .admin-announce-show-page > div:first-of-type .mb-3 span:last-child ) {
    color: var(--manager-content-accent) !important;
}
/* Keep every admin hero eyebrow aligned with the dashboard greeting. */
body.role-manager .admin-content-shell:is( .admin-concern-kicker, .booking-kicker, .access-kicker, .access-form-hero > p, .admin-ticket-page > div:first-of-type .mb-3 span:last-child, .admin-announcements-page > div:first-of-type .mb-3 span:last-child, .admin-community-page > div:first-of-type .mb-3 span:last-child, .admin-announce-create-page > div:first-of-type .mb-3 span:last-child, .admin-announce-edit-page > div:first-of-type .mb-3 span:last-child, .admin-announce-show-page > div:first-of-type .mb-3 span:last-child ) {
    display: block !important;
    margin: 0 0 8px !important;
    color: var(--manager-content-accent) !important;
    font-size: 0.68rem !important;
    font-weight: 700 !important;
    letter-spacing: 0.16em !important;
    line-height: 1.2 !important;
    text-transform: uppercase !important;
}
body.role-manager .admin-content-shell:is( .admin-ticket-page > div:first-of-type .mb-3, .admin-announcements-page > div:first-of-type .mb-3, .admin-community-page > div:first-of-type .mb-3, .admin-announce-create-page > div:first-of-type .mb-3, .admin-announce-edit-page > div:first-of-type .mb-3, .admin-announce-show-page > div:first-of-type .mb-3 ) {
    margin-bottom: 8px !important;
    gap: 0 !important;
}
body.role-manager .admin-content-shell:is( .admin-ticket-page > div:first-of-type .mb-3 span:last-child, .admin-announcements-page > div:first-of-type .mb-3 span:last-child, .admin-community-page > div:first-of-type .mb-3 span:last-child, .admin-announce-create-page > div:first-of-type .mb-3 span:last-child, .admin-announce-edit-page > div:first-of-type .mb-3 span:last-child, .admin-announce-show-page > div:first-of-type .mb-3 span:last-child ) {
    margin-bottom: 0 !important;
}
body.role-manager .admin-content-shell:is( .admin-concern-kicker, .booking-kicker )::before, body.role-manager .admin-content-shell .admin-ticket-page > div:first-of-type .mb-3 > span:first-child, body.role-manager .admin-content-shell .admin-community-page > div:first-of-type .mb-3 > div:first-child {
    display: none !important;
}
body.role-manager .admin-content-shell .admin-ticket-show-kicker {
    margin-bottom: 2px !important;
    gap: 0 !important;
    font-size: 0.68rem !important;
    letter-spacing: 0.16em !important;
    line-height: 1.2 !important;
}
body.role-manager .admin-content-shell .admin-ticket-show-kicker::before {
    display: none !important;
}
body.role-manager .admin-content-shell:is( .admin-community-page > div:first-of-type .shrink-0 span, .admin-user-form-hero > a, .admin-user-form-hero p span ) {
    color: var(--manager-content-accent) !important;
}
body.role-manager .admin-content-shell:is( .admin-ticket-show-back, .admin-concern-hero .admin-concern-btn-secondary, .booking-dashboard .date-badge ) {
    border-color: #ead6b8 !important;
    background: var(--manager-accent-soft) !important;
    color: var(--manager-content-accent) !important;
}
/* Compact manager booking week selector. */
body.role-manager .admin-content-shell .booking-panel .week-strip {
    gap: 6px !important;
}
body.role-manager .admin-content-shell .booking-panel .week-day {
    gap: 1px !important;
    padding: 7px 8px !important;
    border-radius: 8px !important;
}
body.role-manager .admin-content-shell .booking-panel .week-day-name, body.role-manager .admin-content-shell .booking-panel .week-day-count {
    font-size: 0.67rem !important;
    line-height: 1.25 !important;
}
body.role-manager .admin-content-shell .booking-panel .week-day-number {
    font-size: 0.94rem !important;
    line-height: 1.15 !important;
}
/* Brown shell treatment for admin booking panels. */
body.role-manager .admin-content-shell .booking-dashboard .booking-panel {
    overflow: hidden !important;
    padding: 24px !important;
    border: 1px solid rgba(107, 79, 58, 0.22) !important;
    border-radius: 16px !important;
    background: #6B4F3A !important;
    box-shadow: 0 10px 30px rgba(79, 58, 44, 0.12) !important;
}
body.role-manager .admin-content-shell .booking-dashboard .booking-panel .panel-heading h2 {
    color: #fff7ea !important;
}
body.role-manager .admin-content-shell .booking-dashboard .booking-panel .panel-heading p {
    color: rgba(255, 247, 234, 0.78) !important;
}
body.role-manager .admin-content-shell .booking-dashboard .booking-panel .date-picker-label {
    border-color: rgba(255, 247, 234, 0.26) !important;
    background: rgba(255, 255, 255, 0.08) !important;
    color: #fff7ea !important;
}
body.role-manager .admin-content-shell .booking-dashboard .booking-panel .date-picker-label input {
    color: #fff7ea !important;
}
body.role-manager .admin-content-shell .booking-dashboard .booking-panel .week-strip,
body.role-manager .admin-content-shell .booking-dashboard .booking-panel .calendar-legend,
body.role-manager .admin-content-shell .booking-dashboard .booking-panel .calendar-table-wrap,
body.role-manager .admin-content-shell .booking-dashboard .booking-panel .summary-list,
body.role-manager .admin-content-shell .booking-dashboard .booking-panel .history-list {
    margin-left: 22px !important;
    margin-right: 22px !important;
}
body.role-manager .admin-content-shell .booking-dashboard .booking-panel .week-strip {
    margin-bottom: 22px !important;
    padding: 14px !important;
    border: 1px solid rgba(227, 216, 202, 0.92) !important;
    border-radius: 8px !important;
    background: var(--manager-record-panel) !important;
}
body.role-manager .admin-content-shell .booking-dashboard .booking-panel .calendar-legend {
    margin-bottom: 16px !important;
}
body.role-manager .admin-content-shell .booking-dashboard .booking-panel .calendar-table-wrap,
body.role-manager .admin-content-shell .booking-dashboard .booking-panel .summary-list,
body.role-manager .admin-content-shell .booking-dashboard .booking-panel .history-list {
    margin-bottom: 22px !important;
}
/* Operations queue records:content-forward surfaces without excess height. */
body.role-manager .admin-content-shell .admin-ticket-panel:not(.admin-ticket-archive) .admin-ticket-panel-head {
    margin-bottom: 10px !important;
}
body.role-manager .admin-content-shell .admin-ticket-panel:not(.admin-ticket-archive) .admin-ticket-panel-divider {
    margin-bottom: 10px !important;
    background: var(--manager-panel-divider) !important;
}
body.role-manager .admin-content-shell .admin-ticket-panel:not(.admin-ticket-archive) [data-progressive-list] {
    display: grid;
    gap: 7px !important;
}
body.role-manager .admin-content-shell .admin-ticket-panel:not(.admin-ticket-archive) .ticket-card {
    margin-top: 0 !important;
}
body.role-manager .admin-content-shell .admin-ticket-panel:not(.admin-ticket-archive) .ticket-card-shell {
    border-color: #eadfce !important;
    border-radius: 9px !important;
    background: linear-gradient(135deg, #fffaf2 0%, #fffdf9 52%, #ffffff 100%) !important;
    box-shadow: 0 2px 7px rgba(84, 61, 37, 0.04) !important;
}
body.role-manager .admin-content-shell .admin-ticket-panel:not(.admin-ticket-archive) .ticket-card-shell:hover {
    border-color: #dfc9a8 !important;
    background: linear-gradient(135deg, #fff7e9 0%, #fffdf9 58%, #ffffff 100%) !important;
    box-shadow: 0 5px 12px rgba(84, 61, 37, 0.07) !important;
}
body.role-manager .admin-content-shell .admin-ticket-panel:not(.admin-ticket-archive) .ticket-card-shell-critical {
    border-color: #efc9c6 !important;
    background: linear-gradient(135deg, #fff6f4 0%, #fffdf9 58%, #ffffff 100%) !important;
}
body.role-manager .admin-content-shell .admin-ticket-panel:not(.admin-ticket-archive):not(.admin-operations-queue) .ticket-card-body {
    padding: 10px 12px !important;
}
body.role-manager .admin-content-shell .admin-ticket-panel:not(.admin-ticket-archive) .ticket-card-layout {
    flex-direction: row !important;
    align-items: center !important;
    gap: 12px !important;
}
body.role-manager .admin-content-shell .admin-ticket-panel:not(.admin-ticket-archive) .ticket-card-heading {
    gap: 6px !important;
    margin-bottom: 5px !important;
}
body.role-manager .admin-content-shell .admin-ticket-panel:not(.admin-ticket-archive) .ticket-card-heading > span {
    padding: 3px 7px !important;
    font-size: 0.63rem !important;
    line-height: 1.2 !important;
}
body.role-manager .admin-content-shell .admin-ticket-panel:not(.admin-ticket-archive) .ticket-card-heading > span:first-child {
    padding-left: 0 !important;
}
body.role-manager .admin-content-shell .admin-ticket-panel:not(.admin-ticket-archive) .ticket-card-main h3 {
    margin-bottom: 3px !important;
    font-size: 0.9rem !important;
    line-height: 1.3 !important;
}
body.role-manager .admin-content-shell .admin-ticket-panel:not(.admin-ticket-archive) .ticket-card-main > p {
    margin-bottom: 6px !important;
    font-size: 0.74rem !important;
    line-height: 1.4 !important;
}
body.role-manager .admin-content-shell .admin-ticket-panel:not(.admin-ticket-archive) .ticket-card-meta {
    column-gap: 9px !important;
    row-gap: 4px !important;
    font-size: 0.67rem !important;
}
body.role-manager .admin-content-shell .admin-ticket-panel:not(.admin-ticket-archive) .ticket-card-meta-divider {
    background: #ddd2c4 !important;
}
body.role-manager .admin-content-shell .admin-ticket-panel:not(.admin-ticket-archive) .ticket-card-actions {
    flex-direction: row !important;
    align-items: center !important;
    flex-wrap: wrap !important;
    gap: 8px !important;
}
body.role-manager .admin-content-shell .admin-ticket-panel:not(.admin-ticket-archive) .ticket-card-action {
    min-height: 32px !important;
    min-width: 110px !important;
    gap: 8px !important;
    padding: 0.5rem 1rem !important;
    border-radius: 6px !important;
    font-size: 0.8125rem !important;
    line-height: 1.2 !important;
}
body.role-manager .admin-content-shell .admin-ticket-panel:not(.admin-ticket-archive) .ticket-card-action svg {
    display: block !important;
    width: 14px !important;
    height: 14px !important;
}
body.role-manager .admin-content-shell .admin-ticket-panel:not(.admin-ticket-archive) .ticket-card-reject-wrap {
    gap: 5px !important;
}
body.role-manager .admin-content-shell .admin-ticket-panel:not(.admin-ticket-archive):not(.admin-operations-queue) .admin-filter-select {
    padding: 7px 10px !important;
    border-color: #eadfce !important;
    border-radius: 8px !important;
    background: #fffdf9 !important;
    color: var(--manager-content-title) !important;
    font-size: 0.76rem !important;
}
@media (max-width:760px) {
    body.role-manager .admin-content-shell .booking-panel .week-strip {
        gap: 4px !important;
    }
    body.role-manager .admin-content-shell .booking-panel .week-day {
        padding: 6px 3px !important;
    }
    body.role-manager .admin-content-shell .booking-panel .week-day-count {
        display: none !important;
    }
    body.role-manager .admin-content-shell .admin-ticket-panel:not(.admin-ticket-archive) .ticket-card-layout {
        flex-direction: column !important;
        align-items: stretch !important;
    }
    body.role-manager .admin-content-shell .admin-ticket-panel:not(.admin-ticket-archive) .ticket-card-actions {
        justify-content: flex-start !important;
    }
}
/* Unified manager controls:one shape, density, and contrast ladder across admin features. */
body.role-manager .admin-content-shell {
    --manager-control-height: 36px;
    --manager-control-radius: 8px;
    --manager-control-border: #dfd5c8;
    --manager-control-bg: #fffdf9;
    --manager-control-hover: #fff8ed;
    --manager-control-focus: rgba(180, 119, 33, 0.22);
    --manager-control-danger: #a3433b;
    --manager-control-danger-bg: #fff4f2;
    --manager-control-danger-border: #efc9c6;
    --manager-control-success: #4f805c;
    --manager-control-success-bg: #edf6ef;
    --manager-control-success-border: #cee1d2;
}
body.role-manager .admin-content-shell:is( .admin-primary-btn, .admin-secondary-btn, .admin-hero-action, .admin-ticket-action, .admin-ticket-show-back, .admin-ticket-modal-actions button, .admin-ticket-modal-action, .admin-concern-btn, .admin-concern-link, .admin-status-link, .announcement-action, .admin-user-primary-action, .admin-user-search-action, .admin-user-form-action, .admin-user-modal-action, .admin-community-action, .booking-modal-button, .admin-collapsible-toggle, .app-progressive-toggle ) {
    display: inline-flex !important;
    min-height: var(--manager-control-height) !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 6px !important;
    padding: 8px 12px !important;
    border: 1px solid transparent !important;
    border-radius: var(--manager-control-radius) !important;
    box-shadow: none !important;
    font-size: 0.74rem !important;
    font-weight: 700 !important;
    line-height: 1.1 !important;
    text-decoration: none !important;
    white-space: nowrap !important;
    cursor: pointer !important;
    transition: background 0.18s ease, border-color 0.18s ease, color 0.18s ease, transform 0.18s ease !important;
}
body.role-manager .admin-content-shell:is( .admin-primary-btn, .admin-hero-action-primary, .admin-ticket-action-primary, .admin-ticket-modal-primary, .admin-ticket-modal-action-primary, .admin-concern-btn-primary, .admin-user-primary-action, .admin-user-search-action, .admin-user-form-action-primary, .admin-user-modal-action-primary, .booking-modal-button ) {
    border-color: #b47721 !important;
    background: linear-gradient(135deg, #b47721 0%, #c78a35 100%) !important;
    color: #ffffff !important;
}
body.role-manager .admin-content-shell:is( .admin-secondary-btn, .admin-hero-action-secondary, .admin-ticket-show-back, .admin-ticket-action-secondary, .admin-ticket-modal-secondary, .admin-ticket-modal-action-secondary, .admin-concern-btn-secondary, .admin-status-link, .announcement-action--details, .announcement-action--edit, .admin-user-form-action-secondary, .admin-user-modal-action-secondary, .admin-community-action-secondary, .admin-collapsible-toggle, .app-progressive-toggle ) {
    border-color: var(--manager-control-border) !important;
    background: var(--manager-control-bg) !important;
    color: var(--manager-content-accent) !important;
}
body.role-manager .admin-content-shell:is( .admin-danger-btn, .admin-ticket-action-danger, .admin-ticket-modal-danger, .announcement-action--delete, .admin-user-form-action-danger, .admin-community-action-danger, .admin-community-action-reject ) {
    border-color: var(--manager-control-danger-border) !important;
    background: var(--manager-control-danger-bg) !important;
    color: var(--manager-control-danger) !important;
}
body.role-manager .admin-content-shell:is( .admin-community-action-approve ) {
    border-color: var(--manager-control-success-border) !important;
    background: var(--manager-control-success-bg) !important;
    color: var(--manager-control-success) !important;
}
body.role-manager .admin-content-shell:is( .admin-primary-btn, .admin-hero-action-primary, .admin-ticket-action-primary, .admin-ticket-modal-primary, .admin-ticket-modal-action-primary, .admin-concern-btn-primary, .admin-user-primary-action, .admin-user-search-action, .admin-user-form-action-primary, .admin-user-modal-action-primary, .booking-modal-button ):hover {
    background: linear-gradient(135deg, #a96c1b 0%, #bd7e29 100%) !important;
    transform: translateY(-1px) !important;
}
body.role-manager .admin-content-shell:is( .admin-secondary-btn, .admin-hero-action-secondary, .admin-ticket-show-back, .admin-ticket-action-secondary, .admin-ticket-modal-secondary, .admin-ticket-modal-action-secondary, .admin-concern-btn-secondary, .admin-status-link, .announcement-action--details, .announcement-action--edit, .admin-user-form-action-secondary, .admin-user-modal-action-secondary, .admin-community-action-secondary, .admin-collapsible-toggle, .app-progressive-toggle ):hover {
    border-color: #d3bd9d !important;
    background: var(--manager-control-hover) !important;
    color: #754713 !important;
}
body.role-manager .admin-content-shell:is( .admin-danger-btn, .admin-ticket-action-danger, .admin-ticket-modal-danger, .announcement-action--delete, .admin-user-form-action-danger, .admin-community-action-danger, .admin-community-action-reject ):hover {
    border-color: #e5aaa5 !important;
    background: #ffebe8 !important;
    color: #933a33 !important;
}
body.role-manager .admin-content-shell .admin-community-action-approve:hover {
    border-color: #afd2b6 !important;
    background: #e3f1e6 !important;
    color: #416e4d !important;
}
body.role-manager .admin-content-shell:is( .filter-tab, .admin-user-filter-action, .mode-pill, .nav-pill ) {
    display: inline-flex !important;
    min-height: 34px !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 7px 10px !important;
    border: 1px solid var(--manager-control-border) !important;
    border-radius: 7px !important;
    background: var(--manager-control-bg) !important;
    color: var(--manager-content-body) !important;
    font-size: 0.72rem !important;
    font-weight: 700 !important;
    line-height: 1 !important;
    text-decoration: none !important;
    cursor: pointer !important;
    transition: background 0.18s ease, border-color 0.18s ease, color 0.18s ease !important;
}
body.role-manager .admin-content-shell:is( .filter-tab.active, .admin-user-filter-action.is-active, .mode-pill.is-active, .nav-pill-highlight ) {
    border-color: #e5c99f !important;
    background: var(--manager-accent-soft) !important;
    color: var(--manager-content-accent) !important;
    text-decoration: none !important;
}
body.role-manager .admin-content-shell:is( .admin-user-row-action, .admin-dialog-close, .admin-ticket-modal-head button, .booking-modal-close ) {
    display: inline-grid !important;
    width: 32px !important;
    height: 32px !important;
    min-height: 32px !important;
    padding: 0 !important;
    place-items: center !important;
    border: 1px solid var(--manager-control-border) !important;
    border-radius: 7px !important;
    background: var(--manager-control-bg) !important;
    box-shadow: none !important;
    color: var(--manager-content-accent) !important;
    cursor: pointer !important;
    transition: background 0.18s ease, border-color 0.18s ease, color 0.18s ease !important;
}
body.role-manager .admin-content-shell .admin-user-row-action-delete {
    border-color: var(--manager-control-danger-border) !important;
    background: var(--manager-control-danger-bg) !important;
    color: var(--manager-control-danger) !important;
}
body.role-manager .admin-content-shell:is( button, a, label[for] ):focus-visible {
    outline: 3px solid var(--manager-control-focus) !important;
    outline-offset: 2px !important;
}
/* Manager record lists:one neutral surface with line-separated rows. */
body.role-manager .admin-content-shell {
    --manager-record-panel: var(--admin-surface-inner);
    --manager-record-row: var(--admin-surface-inner);
    --manager-record-divider: #e3d8ca;
    --manager-record-hover: #f7f2eb;
}
body.role-manager .admin-content-shell:is( .admin-ticket-panel:not(.admin-ticket-archive) [data-progressive-list], .admin-ticket-archive .admin-status-stack, .admin-announcements-page .admin-panel-card > [data-progressive-list], .admin-community-review-panel > [data-progressive-list], .admin-concern-list, .booking-panel .summary-list, .booking-panel .history-list ) {
    display: flex !important;
    flex-direction: column !important;
    gap: 0 !important;
    overflow: hidden !important;
    border: 1px solid var(--manager-record-divider) !important;
    border-radius: 10px !important;
    background: var(--manager-record-panel) !important;
}
body.role-manager .admin-content-shell .admin-concern-list {
    margin-top: 14px !important;
}
/* Match the daily calendar grid to the rounded inner surfaces used by admin record lists. */
body.role-manager .admin-content-shell .booking-dashboard .calendar-table-wrap {
    overflow-x: auto !important;
    overflow-y: hidden !important;
    border: 1px solid var(--manager-record-divider) !important;
    border-radius: 10px !important;
    background: var(--manager-record-panel) !important;
}
body.role-manager .admin-content-shell .booking-dashboard .calendar-table tbody tr:last-child td {
    border-bottom: 0 !important;
}
body.role-manager .admin-content-shell:is( .admin-ticket-panel:not(.admin-ticket-archive) .ticket-card, .admin-announcements-page .announcement-card, .admin-community-review-card, .admin-concern-row, .booking-panel .summary-row, .booking-panel .history-row ) {
    margin: 0 !important;
    border: 0 !important;
    border-bottom: 1px solid var(--manager-record-divider) !important;
    border-radius: 0 !important;
    background: var(--manager-record-row) !important;
    box-shadow: none !important;
    transform: none !important;
}
body.role-manager .admin-content-shell:is( .admin-ticket-panel:not(.admin-ticket-archive) .ticket-card:last-child, .admin-announcements-page .announcement-card:last-child, .admin-community-review-card:last-child, .admin-concern-row:last-child, .booking-panel .summary-row:last-child, .booking-panel .history-row:last-child ) {
    border-bottom: 0 !important;
}
body.role-manager .admin-content-shell:is( .admin-ticket-panel:not(.admin-ticket-archive) .ticket-card-shell, .admin-ticket-panel:not(.admin-ticket-archive) .ticket-card-shell-critical ) {
    border: 0 !important;
    border-radius: 0 !important;
    background: transparent !important;
    box-shadow: none !important;
}
body.role-manager .admin-content-shell:is( .admin-ticket-panel:not(.admin-ticket-archive) .ticket-card:hover, .admin-announcements-page .announcement-card:hover, .admin-community-review-card:hover, .admin-concern-row:hover, .booking-panel .summary-row:hover, .booking-panel .history-row:hover ) {
    border-bottom-color: var(--manager-record-divider) !important;
    background: var(--manager-record-hover) !important;
    box-shadow: none !important;
    transform: none !important;
}
body.role-manager .admin-content-shell:is( .admin-ticket-panel:not(.admin-ticket-archive) .ticket-card, .admin-announcements-page .announcement-card, .admin-community-review-card, .admin-concern-row, .booking-panel .summary-row, .booking-panel .history-row ):focus-within {
    background: #ffffff !important;
}
body.role-manager .admin-content-shell .admin-ticket-panel:not(.admin-ticket-archive) .ticket-card:hover .ticket-card-shell {
    background: transparent !important;
    box-shadow: none !important;
    transform: none !important;
}
body.role-manager .admin-content-shell .admin-operations-queue {
    overflow: hidden !important;
    padding: 0 !important;
    border: 1px solid rgba(107, 79, 58, 0.22) !important;
    border-radius: 14px !important;
    background: #6B4F3A !important;
    box-shadow: 0 14px 28px rgba(79, 58, 44, 0.12) !important;
}
body.role-manager .admin-content-shell .admin-operations-queue .admin-ticket-panel-head {
    margin: 0 !important;
    padding: 22px 30px 20px !important;
    border-color: transparent !important;
    background: #6B4F3A !important;
}
body.role-manager .admin-content-shell .admin-operations-queue .admin-ticket-panel-title {
    color: #fff7ea !important;
}
body.role-manager .admin-content-shell .admin-operations-queue .admin-ticket-panel-sub {
    color: rgba(255, 247, 234, 0.78) !important;
}
body.role-manager .admin-content-shell .admin-operations-queue .admin-ticket-panel-divider {
    display: none !important;
}
body.role-manager .admin-content-shell .admin-operations-queue [data-progressive-list] {
    margin: 0 22px !important;
    border: 1px solid rgba(227, 216, 202, 0.92) !important;
    border-radius: 8px !important;
    background: var(--manager-record-panel) !important;
}
body.role-manager .admin-content-shell .admin-operations-queue .ticket-card {
    border-bottom: 1px solid var(--manager-record-divider) !important;
}
body.role-manager .admin-content-shell .admin-operations-queue .ticket-card-body {
    padding: var(--operations-row-padding) !important;
}
body.role-manager .admin-content-shell .admin-operations-queue .ticket-card:last-child {
    border-bottom: 0 !important;
}
body.role-manager .admin-content-shell .admin-ticket-panel.admin-operations-queue:not(.admin-ticket-archive) .ticket-card-shell,
body.role-manager .admin-content-shell .admin-ticket-panel.admin-operations-queue:not(.admin-ticket-archive) .ticket-card-shell-critical,
body.role-manager .admin-content-shell .admin-ticket-panel.admin-operations-queue:not(.admin-ticket-archive) .ticket-card-shell:hover,
body.role-manager .admin-content-shell .admin-ticket-panel.admin-operations-queue:not(.admin-ticket-archive) .ticket-card-shell-critical:hover {
    border: 0 !important;
    border-radius: 0 !important;
    background: transparent !important;
    box-shadow: none !important;
    transform: none !important;
}
body.role-manager .admin-content-shell .admin-operations-queue .app-progressive-action {
    padding: 16px 30px 18px !important;
    border-top: 0 !important;
    background: #6B4F3A !important;
}
body.role-manager .admin-content-shell .admin-operations-queue .app-progressive-action button {
    border-color: rgba(255, 247, 234, 0.22) !important;
    background: rgba(255, 255, 255, 0.06) !important;
    color: #fff7ea !important;
}
body.role-manager .admin-content-shell .admin-ticket-archive {
    overflow: hidden !important;
    padding: 0 !important;
    border: 1px solid rgba(107, 79, 58, 0.20) !important;
    border-radius: 14px !important;
    background: var(--manager-record-panel) !important;
    box-shadow: 0 12px 24px rgba(79, 58, 44, 0.09) !important;
}
body.role-manager .admin-content-shell .admin-ticket-archive .admin-archive-tabs {
    gap: 3px !important;
    margin: 0 !important;
    padding: 0 !important;
    border-bottom: 1px solid var(--manager-record-divider) !important;
    background: var(--manager-record-panel) !important;
}
body.role-manager .admin-content-shell .admin-ticket-archive .admin-archive-tab {
    align-items: center !important;
    justify-content: center !important;
    gap: 8px !important;
    padding: 12px 15px !important;
    border: 0 !important;
    border-bottom: 2px solid transparent !important;
    border-radius: 0 !important;
    background: transparent !important;
    color: var(--manager-content-body) !important;
    font-size: 0.82rem !important;
    font-weight: 600 !important;
}
body.role-manager .admin-content-shell .admin-ticket-archive .admin-archive-tab svg {
    display: block !important;
    width: 16px !important;
    height: 16px !important;
    flex: 0 0 16px !important;
}
body.role-manager .admin-content-shell .admin-ticket-archive .admin-archive-tab span {
    color: var(--manager-content-muted) !important;
}
body.role-manager .admin-content-shell .admin-ticket-archive .admin-archive-tab:hover {
    background: rgba(15, 23, 42, 0.04) !important;
    color: var(--manager-content-title) !important;
}
body.role-manager .admin-content-shell .admin-ticket-archive .admin-archive-tab.is-active {
    border-bottom-color: #D6A85B !important;
    background: transparent !important;
    color: #D6A85B !important;
}
body.role-manager .admin-content-shell .admin-ticket-archive .admin-archive-tab.is-active span {
    color: #D6A85B !important;
}
body.role-manager .admin-content-shell .admin-ticket-archive .admin-archive-section {
    background: transparent !important;
}
body.role-manager .admin-content-shell .admin-ticket-archive .admin-ticket-panel-head {
    margin: 0 !important;
    padding: 22px 24px 16px !important;
    border-bottom: 1px solid rgba(255, 247, 234, 0.28) !important;
    background: #6B4F3A !important;
}
body.role-manager .admin-content-shell .admin-ticket-archive .admin-ticket-panel-head .admin-ticket-panel-title {
    color: #fff7ea !important;
}
body.role-manager .admin-content-shell .admin-ticket-archive .admin-ticket-panel-head .admin-ticket-panel-sub {
    color: rgba(255, 247, 234, 0.78) !important;
}
body.role-manager .admin-content-shell .admin-ticket-archive .admin-archive-section > .admin-ticket-panel-divider {
    display: none !important;
}
body.role-manager .admin-content-shell .admin-ticket-archive .admin-status-stack {
    margin: 0 !important;
    border: 0 !important;
    border-radius: 0 !important;
    background: var(--manager-record-panel) !important;
}
body.role-manager .admin-content-shell .admin-ticket-archive .admin-status-card {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 14px !important;
    padding: var(--archive-row-padding) !important;
    border: 0 !important;
    border-bottom: 1px solid var(--manager-record-divider) !important;
    border-radius: 0 !important;
    background: var(--manager-record-row) !important;
    box-shadow: none !important;
    transform: none !important;
}
body.role-manager .admin-content-shell .admin-ticket-archive .admin-status-card:hover {
    background: var(--manager-record-hover) !important;
}
body.role-manager .admin-content-shell .admin-ticket-archive .admin-status-card:last-child {
    border-bottom: 0 !important;
}
body.role-manager .admin-content-shell .admin-ticket-archive .admin-status-card.is-hidden-by-default {
    display: none !important;
}
body.role-manager .admin-content-shell .admin-ticket-archive .admin-status-clean-meta {
    color: #9b8d81 !important;
    font-size: 0.68rem !important;
    font-weight: 700 !important;
    letter-spacing: 0 !important;
    text-transform: uppercase !important;
}
body.role-manager .admin-content-shell .admin-ticket-archive .admin-status-card strong {
    display: block !important;
    margin-top: 7px !important;
    color: #342a23 !important;
    font-family: 'Inter', sans-serif !important;
    font-size: 0.95rem !important;
    font-weight: 400 !important;
    line-height: 1.35 !important;
}
body.role-manager .admin-content-shell .admin-ticket-archive .admin-status-card p {
    margin: 4px 0 0 !important;
    color: #786b60 !important;
    font-size: 0.8rem !important;
    line-height: 1.55 !important;
}
body.role-manager .admin-content-shell .admin-ticket-archive .admin-status-meta {
    margin-top: 8px !important;
    color: #8A7A66 !important;
    font-size: 0.76rem !important;
}
body.role-manager .admin-content-shell .admin-ticket-archive .admin-status-link {
    align-self: center !important;
    flex: 0 0 auto !important;
}
body.role-manager .admin-content-shell .admin-ticket-archive .admin-collapsible-action {
    padding: 8px 18px !important;
    border-top: 1px solid rgba(255, 247, 234, 0.14) !important;
    background: #6B4F3A !important;
}
body.role-manager .admin-content-shell .admin-ticket-archive .admin-collapsible-toggle {
    border-color: rgba(255, 247, 234, 0.22) !important;
    background: rgba(255, 255, 255, 0.06) !important;
    color: #fff7ea !important;
    font-size: 0.76rem !important;
    font-weight: 800 !important;
}
body.role-manager .admin-content-shell .admin-ticket-archive .admin-collapsible-toggle:hover {
    border-color: rgba(255, 247, 234, 0.34) !important;
    background: rgba(255, 255, 255, 0.12) !important;
    transform: translateY(-1px) !important;
}
/* Brown shell treatment for admin record panels outside Maintenance/Bookings. */
body.role-manager .admin-content-shell :is(
    .admin-announcements-page .admin-panel-card,
    .admin-community-review-panel,
    .admin-concern-panel,
    .access-panel:not(.feature-skeleton-stack)
) {
    overflow: hidden !important;
    padding: 0 !important;
    border: 1px solid rgba(107, 79, 58, 0.22) !important;
    border-radius: 14px !important;
    background: #6B4F3A !important;
    box-shadow: 0 14px 28px rgba(79, 58, 44, 0.12) !important;
}
body.role-manager .admin-content-shell :is(
    .admin-announcements-page .admin-panel-card .admin-brown-panel-head,
    .admin-community-review-panel .admin-brown-panel-head,
    .admin-concern-panel .admin-concern-panel-head,
    .access-panel:not(.feature-skeleton-stack) .access-panel-head
) {
    margin: 0 !important;
    padding: 22px 28px 18px !important;
    background: #6B4F3A !important;
}
body.role-manager .admin-content-shell :is(
    .admin-announcements-page .admin-panel-card .admin-brown-panel-head h2,
    .admin-community-review-panel .admin-brown-panel-head h2,
    .admin-concern-panel .admin-concern-panel-head h2,
    .access-panel:not(.feature-skeleton-stack) .access-panel-head h2
) {
    color: #fff7ea !important;
}
body.role-manager .admin-content-shell :is(
    .admin-announcements-page .admin-panel-card .admin-brown-panel-head p,
    .admin-community-review-panel .admin-brown-panel-head p,
    .admin-concern-panel .admin-concern-panel-head p,
    .access-panel:not(.feature-skeleton-stack) .access-panel-head p
) {
    color: rgba(255, 247, 234, 0.78) !important;
}
body.role-manager .admin-content-shell .admin-announcements-page .admin-panel-card > div[style*="height: 1px"],
body.role-manager .admin-content-shell .admin-community-review-panel > div[style*="height:1px"] {
    display: none !important;
}
body.role-manager .admin-content-shell .announcement-visibility-item {
    border-left-color: rgba(255, 247, 234, 0.44) !important;
}
body.role-manager .admin-content-shell .announcement-visibility-item strong,
body.role-manager .admin-content-shell .announcement-visibility-item span {
    color: #fff7ea !important;
}
body.role-manager .admin-content-shell :is(
    .admin-announcements-page .admin-panel-card > [data-progressive-list],
    .admin-community-review-panel > [data-progressive-list],
    .admin-concern-filters,
    .access-filters,
    .access-role-tabs,
    .access-table-wrap
) {
    margin-left: 22px !important;
    margin-right: 22px !important;
    border: 1px solid rgba(227, 216, 202, 0.92) !important;
    background: var(--manager-record-panel) !important;
}
body.role-manager .admin-content-shell :is(
    .admin-announcements-page .admin-panel-card > [data-progressive-list],
    .admin-community-review-panel > [data-progressive-list],
    .access-table-wrap
) {
    overflow: hidden !important;
    margin-top: 22px !important;
    margin-bottom: 22px !important;
    border-radius: 8px !important;
}
body.role-manager .admin-content-shell :is(.admin-concern-filters, .access-filters) {
    margin-top: 16px !important;
    margin-bottom: 16px !important;
    padding: 14px !important;
    border-radius: 8px !important;
}
body.role-manager .admin-content-shell .access-role-tabs {
    margin-top: 16px !important;
    padding: 0 14px !important;
    border-radius: 8px 8px 0 0 !important;
    border-bottom: 0 !important;
}
body.role-manager .admin-content-shell .access-table-wrap {
    margin-top: 0 !important;
    border-radius: 0 0 8px 8px !important;
}
body.role-manager .admin-content-shell .admin-concern-section-head {
    margin: 0 22px !important;
    border-color: rgba(227, 216, 202, 0.92) !important;
    background: #fbf8f3 !important;
}
body.role-manager .admin-content-shell .admin-concern-list {
    overflow: hidden !important;
    margin: 0 22px 16px !important;
    border: 1px solid rgba(227, 216, 202, 0.92) !important;
    border-radius: 8px !important;
    background: var(--manager-record-panel) !important;
}
body.role-manager .admin-content-shell .admin-concern-section-head + .admin-concern-list {
    border-top-left-radius: 0 !important;
    border-top-right-radius: 0 !important;
}
body.role-manager .admin-content-shell .admin-concern-page .admin-concern-filters {
    overflow: visible !important;
    border-radius: 8px !important;
}
body.role-manager .admin-content-shell .admin-concern-page .admin-concern-section-head {
    margin: 0 22px !important;
    padding: 12px 18px 10px !important;
    border: 1px solid rgba(227, 216, 202, 0.92) !important;
    border-bottom: 0 !important;
    border-radius: 8px 8px 0 0 !important;
    background: #fffdf9 !important;
}
body.role-manager .admin-content-shell .admin-concern-page .admin-concern-section-head-replied {
    margin-top: 16px !important;
}
body.role-manager .admin-content-shell .admin-concern-page .admin-concern-list {
    margin: 0 22px 16px !important;
    border: 1px solid rgba(227, 216, 202, 0.92) !important;
    border-top: 0 !important;
    border-radius: 0 0 8px 8px !important;
}
body.role-manager .admin-content-shell .admin-concern-page .admin-concern-section-label {
    display: inline-flex !important;
    align-items: center !important;
    padding: 0 !important;
    border: 0 !important;
    background: none !important;
    border-radius: 0 !important;
    font-size: 0.72rem !important;
    font-weight: 800 !important;
    letter-spacing: 0.12em !important;
    text-transform: uppercase !important;
}
body.role-manager .admin-content-shell .admin-concern-page .admin-concern-section-label-awaiting {
    color: #96631f !important;
}
body.role-manager .admin-content-shell .admin-concern-page .admin-concern-section-label-replied {
    color: #2f5c39 !important;
}
body.role-manager .admin-content-shell .admin-concern-page .admin-concern-section-count {
    color: #7c6f64 !important;
    font-weight: 700 !important;
}
body.role-manager .admin-content-shell .admin-community-status {
    border: 1px solid rgba(255, 247, 234, 0.22) !important;
    background: rgba(255, 255, 255, 0.08) !important;
    color: #fff7ea !important;
}
body.role-manager .admin-content-shell :is(
    .admin-announcements-page .announcement-card,
    .admin-community-review-card,
    .admin-concern-row
) {
    margin: 0 !important;
    padding: 16px 18px !important;
    border: 0 !important;
    border-bottom: 1px solid var(--manager-record-divider) !important;
    border-radius: 0 !important;
    background: var(--manager-record-row) !important;
    box-shadow: none !important;
    transform: none !important;
}
body.role-manager .admin-content-shell :is(
    .admin-announcements-page .announcement-card:last-child,
    .admin-community-review-card:last-child,
    .admin-concern-row:last-child
) {
    border-bottom: 0 !important;
}
body.role-manager .admin-content-shell :is(
    .admin-announcements-page .announcement-card:hover,
    .admin-community-review-card:hover,
    .admin-concern-row:hover
) {
    background: var(--manager-record-hover) !important;
    box-shadow: none !important;
    transform: none !important;
}
body.role-manager .admin-content-shell :is(
    .announcement-card .announcement-action,
    .admin-community-review-card .admin-community-action,
    .admin-concern-row .admin-concern-row-action
) {
    border-color: #d2ae7b !important;
    background: #f3e3cc !important;
    color: #68400f !important;
    box-shadow: none !important;
}
body.role-manager .admin-content-shell :is(
    .announcement-card .announcement-action--delete,
    .admin-community-review-card .admin-community-action-danger,
    .admin-community-review-card .admin-community-action-reject
) {
    border-color: #dda29d !important;
    background: #f7dfdc !important;
    color: #8f342e !important;
}
body.role-manager .admin-content-shell .admin-community-review-card .admin-community-action-approve {
    border-color: #9fc6a8 !important;
    background: #deeee1 !important;
    color: #356140 !important;
}
body.role-manager .admin-content-shell:is( .admin-announcements-page .announcement-card, .admin-community-review-card, .admin-concern-row, .booking-panel .summary-row, .booking-panel .history-row ) {
    padding: 12px 14px !important;
}
body.role-manager .admin-content-shell .admin-community-review-card {
    margin-bottom: 0 !important;
}
body.role-manager .admin-content-shell .admin-community-review-card >:is(.flex, div) {
    gap: 12px !important;
}
body.role-manager .admin-content-shell .announcement-card__topline {
    padding-bottom: 7px !important;
    border-bottom-color: var(--manager-record-divider) !important;
}
body.role-manager .admin-content-shell .announcement-card__body {
    padding-top: 0 !important;
}
body.role-manager .admin-content-shell:is( .admin-concern-row small, .ticket-card-clean-meta, .announcement-card__clean-meta, .admin-community-clean-meta ) {
    color: #9b8d81 !important;
    font-family: 'Inter', sans-serif !important;
    font-size: 0.68rem !important;
    font-weight: 700 !important;
    letter-spacing: 0 !important;
    line-height: 1.4 !important;
    text-transform: uppercase !important;
}
body.role-manager .admin-content-shell:is( .admin-concern-row h3, .ticket-card-clean-title, .announcement-card__title, .admin-community-clean-title ) {
    margin: 7px 0 4px !important;
    color: #342a23 !important;
    font-family: 'Inter', sans-serif !important;
    font-size: 0.95rem !important;
    font-weight: 400 !important;
    line-height: 1.35 !important;
}
body.role-manager .admin-content-shell:is( .admin-concern-row p, .ticket-card-clean-copy, .announcement-card__excerpt, .admin-community-clean-copy ) {
    margin: 0 !important;
    color: #786b60 !important;
    font-size: 0.8rem !important;
    font-weight: 400 !important;
    line-height: 1.55 !important;
}
body.role-manager .admin-content-shell .ticket-card-action svg {
    display: block !important;
}
body.role-manager .admin-content-shell:is( .booking-panel .summary-row p, .booking-panel .history-row p, .admin-status-card p ) {
    margin-top: 4px !important;
}
body.role-manager .admin-content-shell .admin-status-meta {
    margin-top: 6px !important;
}
/* Content-row actions need stronger contrast than passive controls. */
body.role-manager .admin-content-shell:is( .ticket-card .ticket-card-action, .admin-status-card .admin-status-link, .announcement-card .announcement-action, .admin-community-review-card .admin-community-action, .admin-concern-row .admin-concern-link ) {
    border-color: #d2ae7b !important;
    background: #f3e3cc !important;
    color: #68400f !important;
}
body.role-manager .admin-content-shell:is( .ticket-card .ticket-card-action, .admin-status-card .admin-status-link, .announcement-card .announcement-action, .admin-community-review-card .admin-community-action, .admin-concern-row .admin-concern-link ):hover {
    border-color: #bd8d4c !important;
    background: #ead1ad !important;
    color: #503009 !important;
}
body.role-manager .admin-content-shell:is( .ticket-card .ticket-card-reject-wrap .ticket-card-action, .announcement-card .announcement-action--delete, .admin-community-review-card .admin-community-action-danger, .admin-community-review-card .admin-community-action-reject ) {
    border-color: #dda29d !important;
    background: #f7dfdc !important;
    color: #8f342e !important;
}
body.role-manager .admin-content-shell:is( .ticket-card .ticket-card-reject-wrap .ticket-card-action, .announcement-card .announcement-action--delete, .admin-community-review-card .admin-community-action-danger, .admin-community-review-card .admin-community-action-reject ):hover {
    border-color: #cc7f78 !important;
    background: #efc8c4 !important;
    color: #742720 !important;
}
body.role-manager .admin-content-shell .admin-community-review-card .admin-community-action-approve {
    border-color: #9fc6a8 !important;
    background: #deeee1 !important;
    color: #356140 !important;
}
body.role-manager .admin-content-shell .admin-community-review-card .admin-community-action-approve:hover {
    border-color: #7ead89 !important;
    background: #cce4d1 !important;
    color: #285332 !important;
}
/* Manager badges use firm, readable tones on light content surfaces. */
body.role-manager:is( .ticket-card-priority, .ticket-card-status, .admin-status-badge, .admin-section-chip, .admin-ticket-badge, .booking-dashboard .status-badge, .admin-concern-status, .announcement-card__priority, .admin-panel-badge.priority-normal, .admin-panel-badge.priority-important, .admin-panel-badge.priority-urgent, .admin-community-status ) {
    border: 1px solid transparent !important;
}
body.role-manager:is( .ticket-card[data-priority="medium"] .ticket-card-priority, .ticket-card-status-pending-approval, .ticket-card-status-received, .ticket-card-status-approved, .admin-ticket-badge-status-pending_approval, .admin-ticket-badge-status-received, .admin-ticket-badge-status-approved, .admin-ticket-badge-priority-medium, .booking-dashboard .status-pending, .admin-concern-status-submitted, .announcement-card__priority-important, .admin-panel-badge.priority-important, .admin-community-status-pending ) {
    border-color: #d2ae7b !important;
    background: #f3e3cc !important;
    color: #68400f !important;
}
body.role-manager:is( .ticket-card[data-priority="critical"] .ticket-card-priority, .ticket-card-status-rejected, .admin-status-badge-rejected, .admin-section-chip-rejected, .admin-ticket-badge-status-rejected, .admin-ticket-badge-priority-critical, .booking-dashboard .status-rejected, .announcement-card__priority-urgent, .admin-panel-badge.priority-urgent, .admin-community-status-rejected ) {
    border-color: #dda29d !important;
    background: #f7dfdc !important;
    color: #8f342e !important;
}
body.role-manager:is( .ticket-card[data-priority="low"] .ticket-card-priority, .ticket-card-status-completed, .admin-status-badge-finished, .admin-section-chip-success, .admin-ticket-badge-status-completed, .admin-ticket-badge-priority-low, .booking-dashboard .status-approved, .admin-concern-status-responded, .admin-community-status-published ) {
    border-color: #9fc6a8 !important;
    background: #deeee1 !important;
    color: #356140 !important;
}
body.role-manager:is( .ticket-card-status-assigned, .ticket-card-status-in-progress, .admin-status-badge-assigned, .admin-section-chip:not(.admin-section-chip-success):not(.admin-section-chip-rejected), .admin-ticket-badge-status-assigned, .admin-ticket-badge-status-in_progress, .admin-concern-status-in_review ) {
    border-color: #a8bedf !important;
    background: #e1eafa !important;
    color: #345984 !important;
}
body.role-manager:is( .admin-concern-status-closed, .announcement-card__priority-normal, .admin-panel-badge.priority-normal ) {
    border-color: #c8bdae !important;
    background: #eee9e2 !important;
    color: #5e554a !important;
}
/* Empty calendar slots need a visible boundary for quick schedule scanning. */
body.role-manager .booking-dashboard .empty-slot {
    border-color: #c8b69f !important;
    background: #fbf8f3 !important;
    color: #75695d !important;
}
/* Shared admin empty state:warm, readable, and consistent with the overview palette. */
body.role-manager .admin-empty-state {
    display: flex !important;
    min-height: 250px;
    padding: 42px 28px !important;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0;
    border: 1px solid #eadfce !important;
    border-radius: 20px !important;
    background: linear-gradient(145deg, #fffdf9 0%, #fbf6ee 100%) !important;
    box-shadow: 0 12px 28px rgba(92, 67, 38, 0.06) !important;
    color: var(--admin-surface-title) !important;
    text-align: center;
}
body.role-manager .admin-empty-state-icon {
    display: grid;
    width: 64px;
    height: 64px;
    margin-bottom: 18px;
    place-items: center;
    border: 1px solid #ead4b2;
    border-radius: 50%;
    background: #fbf0dd;
    color: #b47721;
}
body.role-manager .admin-empty-state-icon svg {
    width: 30px;
    height: 30px;
    fill: none;
    stroke: currentColor;
    stroke-linecap: round;
    stroke-linejoin: round;
    stroke-width: 1.65;
}
body.role-manager .admin-empty-state h3 {
    margin: 0 !important;
    color: #3b3129 !important;
    font-family: 'Playfair Display', serif !important;
    font-size: 1.75rem !important;
    font-weight: 600 !important;
    letter-spacing: -0.01em !important;
    line-height: 1.15 !important;
}
body.role-manager .admin-empty-state p {
    max-width: 580px !important;
    margin: 10px auto 0 !important;
    color: #857565 !important;
    font-family: 'DM Sans', sans-serif !important;
    font-size: 0.98rem !important;
    line-height: 1.65 !important;
}
body.role-manager .admin-empty-state-action {
    display: inline-flex;
    min-height: 40px;
    margin-top: 20px;
    padding: 0 17px;
    align-items: center;
    justify-content: center;
    border: 1px solid #c9954d;
    border-radius: 999px;
    background: #b47721;
    color: #fffdf9 !important;
    font-size: 0.8rem;
    font-weight: 700;
    text-decoration: none;
    transition: background 0.18s ease, transform 0.18s ease;
}
body.role-manager .admin-empty-state-action:hover {
    background: #986017;
    transform: translateY(-1px);
}
body.role-manager .admin-empty-state-compact {
    min-height: 168px;
    padding: 28px 20px !important;
    box-shadow: none !important;
}
body.role-manager .admin-empty-state-compact .admin-empty-state-icon {
    width: 48px;
    height: 48px;
    margin-bottom: 13px;
}
body.role-manager .admin-empty-state-compact .admin-empty-state-icon svg {
    width: 23px;
    height: 23px;
}
body.role-manager .admin-empty-state-compact h3 {
    font-size: 1.28rem !important;
}
body.role-manager .admin-empty-state-compact p {
    font-size: 0.88rem !important;
}
/* Record lists already provide the frame; avoid a doubled edge around their empty state. */
body.role-manager .admin-content-shell:is( [data-progressive-list], .admin-status-stack ) > .admin-empty-state {
    width: 100%;
    border: 0 !important;
    border-radius: 0 !important;
    background: transparent !important;
    box-shadow: none !important;
}
/* Restyle old emoji medallions retained only to avoid encoding churn in legacy templates. */
body.role-manager .admin-empty-state-legacy > div:first-child {
    display: grid !important;
    width: 64px !important;
    height: 64px !important;
    margin: 0 auto 18px !important;
    place-items: center;
    border: 1px solid #ead4b2 !important;
    border-radius: 50% !important;
    background: #fbf0dd !important;
    color: #b47721 !important;
}
body.role-manager .admin-empty-state-legacy > div:first-child span {
    display: none !important;
}
body.role-manager .admin-empty-state-legacy > div:first-child::before {
    content: "";
    width: 25px;
    height: 18px;
    border: 2px solid #b47721;
    border-radius: 4px;
    background: linear-gradient(#b47721, #b47721) 50% 6px / 13px 2px no-repeat, linear-gradient(#b47721, #b47721) 50% 11px / 9px 2px no-repeat;
}
body.role-manager .admin-community-page .admin-empty-state-legacy > div:first-child::before {
    content: "\2713";
    width: auto;
    height: auto;
    border: 0;
    background: none;
    font-size: 1.75rem;
    font-weight: 700;
    line-height: 1;
}
/* Resident dashboard hero reads as a portal header, not an editorial banner. */
body.role-resident .resident-home-kicker {
    margin-bottom: 10px;
    font-family: 'Inter', sans-serif;
    font-size: 0.78rem;
    font-weight: 750;
    line-height: 1.3;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}
body.role-resident .resident-home-title {
    max-width: 680px;
    font-family: 'Playfair Display', serif;
    font-size: clamp(2.15rem, 4.6vw, 4rem) !important;
    font-weight: 600;
    line-height: 1.06 !important;
    letter-spacing: -0.025em !important;
}
body.role-resident .resident-home-subtitle {
    max-width: 680px;
    margin-top: 10px;
    font-size: clamp(0.98rem, 1.2vw, 1.08rem);
    line-height: 1.6;
}
@if(!$isDashboard)
/* Shared resident hero ornament:a soft window-light treatment stays behind readable content. */
body.role-resident:is( .resident-page-hero, .resident-ticket-hero, .ticket-track-hero, .resident-ticket-create-hero, .resident-ticket-edit-hero, .resident-booking-hero, .resident-booking-create-hero, .resident-booking-edit-hero, .resident-announcement-hero, .community-feed-hero, .community-post-hero, .concern-hero ) {
    position: relative !important;
    isolation: isolate;
    overflow: hidden !important;
}
body.role-resident:is( .resident-page-hero, .resident-ticket-hero, .ticket-track-hero, .resident-ticket-create-hero, .resident-ticket-edit-hero, .resident-booking-hero, .resident-booking-create-hero, .resident-booking-edit-hero, .resident-announcement-hero, .community-feed-hero, .community-post-hero, .concern-hero ) > * {
    position: relative;
    z-index: 2;
}
body.role-resident:is( .resident-page-hero, .resident-ticket-hero, .ticket-track-hero, .resident-ticket-create-hero, .resident-ticket-edit-hero, .resident-booking-hero, .resident-booking-create-hero, .resident-booking-edit-hero, .resident-announcement-hero, .community-feed-hero, .community-post-hero, .concern-hero )::before {
    content: "";
    position: absolute;
    inset: 0;
    z-index: 0;
    pointer-events: none;
    opacity: 0.86;
    background: radial-gradient(ellipse at 88% 38%, rgba(240, 208, 152, 0.2) 0 11%, transparent 32%), radial-gradient(ellipse at 82% 112%, rgba(178, 112, 66, 0.18) 0 18%, transparent 45%), linear-gradient(90deg, transparent 58%, rgba(240, 222, 190, 0.025) 58% 62%, transparent 62% 68%, rgba(240, 222, 190, 0.035) 68% 72%, transparent 72% 78%, rgba(240, 222, 190, 0.025) 78% 82%, transparent 82%), linear-gradient(105deg, transparent 36%, rgba(214, 168, 91, 0.035) 72%, rgba(178, 112, 66, 0.1) 100%);
}
body.role-resident:is( .resident-page-hero, .resident-ticket-hero, .ticket-track-hero, .resident-ticket-create-hero, .resident-ticket-edit-hero, .resident-booking-hero, .resident-booking-create-hero, .resident-booking-edit-hero, .resident-announcement-hero, .community-feed-hero, .community-post-hero, .concern-hero )::after {
    content: "";
    position: absolute;
    right: clamp(24px, 5vw, 86px);
    bottom: clamp(-94px, -7vw, -62px);
    z-index: 1;
    width: clamp(148px, 19vw, 230px);
    height: clamp(210px, 27vw, 320px);
    pointer-events: none;
    border: 1px solid rgba(240, 208, 152, 0.16);
    border-bottom: 0;
    border-radius: 999px 999px 0 0;
    background: linear-gradient(90deg, transparent 49.4%, rgba(240, 222, 190, 0.12) 49.5% 50.5%, transparent 50.6%), linear-gradient(180deg, transparent 54%, rgba(240, 222, 190, 0.1) 54.2% 54.8%, transparent 55%), radial-gradient(ellipse at 50% 28%, rgba(240, 208, 152, 0.16), rgba(214, 168, 91, 0.05) 48%, transparent 74%);
    box-shadow: 0 0 34px rgba(214, 168, 91, 0.08), 0 0 0 18px rgba(214, 168, 91, 0.018);
    opacity: 0.88;
}
/* Announcements:softly layered paper waves suggest notices pinned in a shared lobby. */
body.role-resident .resident-hero-paper-waves::before {
    opacity: 0.92;
    background: radial-gradient(ellipse at 82% 118%, rgba(153, 91, 54, 0.2) 0 22%, transparent 46%), radial-gradient(ellipse at 94% 112%, rgba(214, 168, 91, 0.2) 0 28%, transparent 52%), radial-gradient(ellipse at 68% 120%, rgba(108, 72, 53, 0.28) 0 26%, transparent 54%), linear-gradient(105deg, transparent 38%, rgba(214, 168, 91, 0.045) 100%);
}
body.role-resident .resident-hero-paper-waves::after {
    right: -5%;
    bottom: -58%;
    width: 56%;
    height: 118%;
    border: 0;
    border-radius: 48% 52% 0 0;
    background: radial-gradient(ellipse at 50% 100%, transparent 0 41%, rgba(240, 208, 152, 0.11) 41.5% 42%, transparent 42.5% 53%, rgba(214, 168, 91, 0.09) 53.5% 54%, transparent 54.5% 65%, rgba(178, 112, 66, 0.08) 65.5% 66%, transparent 66.5%);
    box-shadow: none;
    opacity: 0.9;
}
/* Bookings:a linen-like weave adds texture without competing with schedule content. */
body.role-resident .resident-hero-woven::before {
    opacity: 0.66;
    background: radial-gradient(ellipse at 88% 32%, rgba(214, 168, 91, 0.18), transparent 34%), repeating-linear-gradient(0deg, rgba(240, 222, 190, 0.035) 0 1px, transparent 1px 8px), repeating-linear-gradient(90deg, rgba(214, 168, 91, 0.035) 0 1px, transparent 1px 8px), linear-gradient(105deg, transparent 42%, rgba(178, 112, 66, 0.09) 100%);
    mask-image: linear-gradient(90deg, transparent 34%, rgba(0, 0, 0, 0.5) 62%, #000 100%);
}
body.role-resident .resident-hero-woven::after {
    right: -32px;
    bottom: -82px;
    width: clamp(170px, 22vw, 270px);
    height: clamp(170px, 22vw, 270px);
    border: 1px solid rgba(240, 208, 152, 0.12);
    border-radius: 50%;
    background: radial-gradient(circle, rgba(214, 168, 91, 0.1), transparent 66%);
    box-shadow: 0 0 0 16px rgba(214, 168, 91, 0.018);
    opacity: 0.78;
}
/* Community:low-contrast leaf shadows make the social space feel residential. */
body.role-resident .resident-hero-botanical::before {
    opacity: 0.74;
    background: radial-gradient(ellipse at 92% 24%, rgba(214, 168, 91, 0.16), transparent 30%), radial-gradient(ellipse at 76% 118%, rgba(178, 112, 66, 0.16), transparent 42%), linear-gradient(104deg, transparent 42%, rgba(214, 168, 91, 0.07) 100%);
}
body.role-resident .resident-hero-botanical::after {
    right: clamp(12px, 4vw, 64px);
    bottom: -42px;
    width: clamp(170px, 23vw, 280px);
    height: clamp(160px, 21vw, 250px);
    border: 0;
    border-radius: 0;
    background: radial-gradient(ellipse at 72% 24%, rgba(240, 222, 190, 0.1) 0 12%, transparent 12.8%), radial-gradient(ellipse at 48% 42%, rgba(214, 168, 91, 0.11) 0 14%, transparent 14.8%), radial-gradient(ellipse at 78% 60%, rgba(178, 112, 66, 0.12) 0 13%, transparent 13.8%), radial-gradient(ellipse at 36% 72%, rgba(240, 222, 190, 0.08) 0 12%, transparent 12.8%), linear-gradient(112deg, transparent 49%, rgba(214, 168, 91, 0.13) 49.5% 50.5%, transparent 51%);
    box-shadow: none;
    opacity: 0.86;
    transform: rotate(-10deg);
}
/* Concerns:a focused lamp halo creates a quieter, reassuring support space. */
body.role-resident .resident-hero-lamp-glow::before {
    opacity: 0.92;
    background: radial-gradient(circle at 86% 44%, rgba(240, 208, 152, 0.23) 0 8%, rgba(214, 168, 91, 0.12) 18%, transparent 38%), radial-gradient(circle at 72% 112%, rgba(178, 112, 66, 0.12), transparent 42%), linear-gradient(105deg, transparent 44%, rgba(214, 168, 91, 0.055) 100%);
}
body.role-resident .resident-hero-lamp-glow::after {
    right: clamp(36px, 8vw, 132px);
    bottom: -54px;
    width: clamp(118px, 15vw, 170px);
    height: clamp(118px, 15vw, 170px);
    border: 1px solid rgba(240, 208, 152, 0.14);
    border-radius: 50%;
    background: radial-gradient(circle, rgba(240, 208, 152, 0.15), rgba(214, 168, 91, 0.05) 48%, transparent 72%);
    box-shadow: 0 0 38px rgba(214, 168, 91, 0.11);
    opacity: 0.84;
}
@endif
/* Shared manager metrics: align admin stat cards with the Overview KPI cards. */
body.role-manager .admin-content-shell .admin-compact-stats {
    --compact-stat-card-min: 200px;
    --compact-stat-min-height: 88px;
    --compact-stat-padding: 14px 16px;
    --compact-stat-main-gap: 8px;
    --compact-stat-value-size: clamp(1.45rem, 1.7vw, 1.86rem);
    display: grid !important;
    align-items: stretch !important;
    gap: 14px 18px !important;
    grid-template-columns: repeat(auto-fit, minmax(min(100%, var(--compact-stat-card-min)), 1fr)) !important;
}
body.role-manager .admin-content-shell .admin-compact-stats-3 {
    --compact-stat-card-min: 220px;
    --compact-stat-min-height: 92px;
    --compact-stat-padding: 15px 18px;
    --compact-stat-main-gap: 9px;
    --compact-stat-value-size: clamp(1.5rem, 1.9vw, 1.95rem);
    grid-template-columns: repeat(auto-fit, minmax(min(100%, var(--compact-stat-card-min)), 1fr)) !important;
}
body.role-manager .admin-content-shell .admin-compact-stats-4 {
    --compact-stat-card-min: 210px;
    --compact-stat-min-height: 90px;
    --compact-stat-padding: 15px 17px;
    --compact-stat-main-gap: 8px;
    --compact-stat-value-size: clamp(1.48rem, 1.8vw, 1.9rem);
    grid-template-columns: repeat(auto-fit, minmax(min(100%, var(--compact-stat-card-min)), 1fr)) !important;
}
body.role-manager .admin-content-shell .admin-compact-stats-5 {
    --compact-stat-card-min: 176px;
    --compact-stat-min-height: 88px;
    --compact-stat-padding: 14px 16px;
    --compact-stat-main-gap: 8px;
    --compact-stat-value-size: clamp(1.45rem, 1.55vw, 1.78rem);
    gap: 12px 14px !important;
    grid-template-columns: repeat(auto-fit, minmax(min(100%, var(--compact-stat-card-min)), 1fr)) !important;
}
body.role-manager .admin-content-shell .admin-compact-stat {
    --compact-stat-accent: #b47721;
    --compact-stat-border: rgba(180, 119, 33, 0.22);
    --compact-stat-glow: rgba(180, 119, 33, 0.12);
    --compact-stat-start: #fff8ed;
    --compact-stat-end: #ffffff;
    position: relative !important;
    isolation: isolate !important;
    overflow: hidden !important;
    display: flex !important;
    width: 100% !important;
    max-width: none !important;
    min-height: var(--compact-stat-min-height) !important;
    flex-direction: column !important;
    align-items: flex-start !important;
    justify-content: flex-start !important;
    gap: 8px !important;
    padding: var(--compact-stat-padding) !important;
    border: 1px solid var(--compact-stat-border) !important;
    border-radius: 12px !important;
    background: linear-gradient(135deg, var(--compact-stat-start) 0%, var(--compact-stat-end) 72%) !important;
    box-shadow: 0 3px 10px rgba(84, 61, 37, 0.035) !important;
    color: #342a23 !important;
    transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease !important;
}
body.role-manager .admin-content-shell .admin-compact-stat::after {
    content: "" !important;
    position: absolute !important;
    top: -28px !important;
    right: -18px !important;
    z-index: -1 !important;
    width: 64px !important;
    height: 64px !important;
    border-radius: 50% !important;
    background: radial-gradient(circle, var(--compact-stat-glow), transparent 70%) !important;
    pointer-events: none !important;
}
body.role-manager .admin-content-shell .admin-compact-stat:hover {
    transform: translateY(-2px) !important;
    border-color: var(--compact-stat-accent) !important;
    box-shadow: 0 7px 16px var(--compact-stat-glow) !important;
}
body.role-manager .admin-content-shell .admin-compact-stat-blue {
    --compact-stat-accent: #52788c;
    --compact-stat-border: rgba(82, 120, 140, 0.22);
    --compact-stat-glow: rgba(82, 120, 140, 0.12);
    --compact-stat-start: #f1f7f9;
    --compact-stat-end: #ffffff;
}
body.role-manager .admin-content-shell .admin-compact-stat-green {
    --compact-stat-accent: #4f805c;
    --compact-stat-border: rgba(79, 128, 92, 0.22);
    --compact-stat-glow: rgba(79, 128, 92, 0.12);
    --compact-stat-start: #f1f8f2;
    --compact-stat-end: #ffffff;
}
body.role-manager .admin-content-shell .admin-compact-stat-red {
    --compact-stat-accent: #bd5349;
    --compact-stat-border: rgba(189, 83, 73, 0.22);
    --compact-stat-glow: rgba(189, 83, 73, 0.12);
    --compact-stat-start: #fdf2f1;
    --compact-stat-end: #ffffff;
}
body.role-manager .admin-content-shell .admin-compact-stat-icon {
    display: none !important;
}
body.role-manager .admin-content-shell .admin-compact-stat-blue .admin-compact-stat-icon {
    display: none !important;
}
body.role-manager .admin-content-shell .admin-compact-stat-green .admin-compact-stat-icon {
    display: none !important;
}
body.role-manager .admin-content-shell .admin-compact-stat-red .admin-compact-stat-icon {
    display: none !important;
}
body.role-manager .admin-content-shell .admin-compact-stat-icon svg {
    width: 17px !important;
    height: 17px !important;
    fill: none !important;
    stroke: currentColor !important;
    stroke-linecap: round !important;
    stroke-linejoin: round !important;
    stroke-width: 1.7 !important;
}
body.role-manager .admin-content-shell .admin-compact-stat-main {
    display: flex !important;
    min-width: 0 !important;
    flex-direction: column !important;
    gap: var(--compact-stat-main-gap) !important;
}
body.role-manager .admin-content-shell .admin-compact-stat-main span {
    order: 1 !important;
    display: block !important;
    min-width: 0 !important;
    color: #806f5c !important;
    font-size: clamp(0.6rem, 0.62vw, 0.66rem) !important;
    font-weight: 800 !important;
    letter-spacing: 0.13em !important;
    line-height: 1.32 !important;
    text-transform: uppercase !important;
}
body.role-manager .admin-content-shell .admin-compact-stat-main strong {
    order: 2 !important;
    display: block !important;
    color: var(--compact-stat-accent) !important;
    font-family: 'Playfair Display', serif !important;
    font-size: var(--compact-stat-value-size) !important;
    font-weight: 700 !important;
    letter-spacing: 0 !important;
    line-height: 0.95 !important;
}
body.role-manager .admin-content-shell .admin-compact-stat small {
    display: block !important;
    margin: 0 !important;
    color: #806f5c !important;
    font-size: clamp(0.68rem, 0.72vw, 0.74rem) !important;
    line-height: 1.35 !important;
    text-align: left !important;
}
/* Announcement information labels are legends and summaries, not controls. */
body.role-manager .admin-content-shell .announcement-priority-legend {
    display: grid !important;
    gap: 8px !important;
}
body.role-manager .admin-content-shell .announcement-priority-key {
    display: block !important;
    color: #5f5143 !important;
    font-size: 0.76rem !important;
    font-weight: 700 !important;
    line-height: 1.35 !important;
}
body.role-manager .admin-content-shell .announcement-visibility-summary {
    display: flex !important;
    align-items: stretch !important;
    gap: 18px !important;
}
body.role-manager .admin-content-shell .announcement-visibility-item {
    display: grid !important;
    min-width: 68px !important;
    gap: 2px !important;
    padding-left: 10px !important;
}
body.role-manager .admin-content-shell .announcement-visibility-item strong {
    color: #342a23 !important;
    font-size: 1.15rem !important;
    font-weight: 800 !important;
    line-height: 1 !important;
}
body.role-manager .admin-content-shell .announcement-visibility-item span {
    color: #786b60 !important;
    font-size: 0.72rem !important;
    font-weight: 700 !important;
    letter-spacing: 0.06em !important;
    line-height: 1.2 !important;
    text-transform: uppercase !important;
}
/* Shared admin record pages:calm, compact, and visually consistent. */
body.role-manager .admin-content-shell .admin-detail-page {
    gap: 18px !important;
}
body.role-manager .admin-content-shell .admin-detail-hero {
    position: relative !important;
    overflow: hidden !important;
    padding: 24px 26px !important;
    border: 1px solid #e3d8ca !important;
    border-radius: 14px !important;
    background: linear-gradient(135deg, #fffaf2 0%, #f8f3eb 58%, #f1e7d8 100%) !important;
    box-shadow: none !important;
}
body.role-manager .admin-content-shell .admin-detail-hero::before, body.role-manager .admin-content-shell .admin-detail-hero > .absolute {
    display: none !important;
}
body.role-manager .admin-content-shell .admin-detail-panel {
    padding: 18px !important;
    border: 1px solid #e3d8ca !important;
    border-radius: 12px !important;
    background: #fffdf9 !important;
    box-shadow: 0 3px 10px rgba(84, 61, 37, 0.035) !important;
    backdrop-filter: none !important;
}
body.role-manager .admin-content-shell .admin-ticket-show-grid, body.role-manager .admin-content-shell .admin-ticket-show-sidebar, body.role-manager .admin-content-shell .account-layout, body.role-manager .admin-content-shell .account-main, body.role-manager .admin-content-shell .account-sidebar {
    gap: 16px !important;
}
body.role-manager .admin-content-shell .admin-ticket-show-grid-single {
    grid-template-columns: 1fr !important;
}
body.role-manager .admin-content-shell .admin-ticket-show-kicker, body.role-manager .admin-content-shell .admin-announce-show-page .admin-detail-hero span[class*="tracking-"], body.role-manager .admin-content-shell .account-hero p, body.role-manager .admin-content-shell .admin-concern-kicker {
    color: #b47721 !important;
}
body.role-manager .admin-content-shell .admin-ticket-show-title, body.role-manager .admin-content-shell .admin-announce-show-page .admin-detail-hero h1, body.role-manager .admin-content-shell .account-hero h1, body.role-manager .admin-content-shell .admin-concern-title {
    color: #342a23 !important;
    font-size: clamp(2rem, 3.4vw, 3rem) !important;
}
body.role-manager .admin-content-shell .admin-announce-show-page .admin-detail-hero h1 span {
    color: #6f5130 !important;
}
body.role-manager .admin-content-shell .admin-ticket-show-subtitle, body.role-manager .admin-content-shell .admin-announce-show-page .admin-detail-hero p, body.role-manager .admin-content-shell .account-hero span, body.role-manager .admin-content-shell .admin-concern-subtitle {
    color: #786b60 !important;
    font-size: 0.9rem !important;
    line-height: 1.6 !important;
}
body.role-manager .admin-content-shell .admin-ticket-show-back, body.role-manager .admin-content-shell .admin-announce-show-page .admin-detail-hero a, body.role-manager .admin-content-shell .account-hero a, body.role-manager .admin-content-shell .admin-concern-record-hero a, body.role-manager .admin-content-shell .access-form-hero a {
    padding: 9px 12px !important;
    border: 1px solid #ead6b8 !important;
    border-radius: 8px !important;
    background: #fbf3e4 !important;
    box-shadow: none !important;
    color: #8b5b1d !important;
    font-size: 0.76rem !important;
    font-weight: 700 !important;
}
body.role-manager .admin-content-shell .admin-announce-show-page .admin-detail-hero > .relative {
    padding: 0 !important;
}
body.role-manager .admin-content-shell .admin-ticket-show-panel-head h2, body.role-manager .admin-content-shell .admin-panel-title, body.role-manager .admin-content-shell .account-panel h2 {
    color: #342a23 !important;
}
body.role-manager .admin-content-shell .admin-ticket-show-panel-head p, body.role-manager .admin-content-shell .admin-panel-sub {
    color: #786b60 !important;
    font-size: 0.82rem !important;
}
body.role-manager .admin-content-shell .admin-ticket-info-grid, body.role-manager .admin-content-shell .admin-announcement-meta-grid {
    gap: 10px !important;
}
body.role-manager .admin-content-shell .admin-ticket-info-card, body.role-manager .admin-content-shell .admin-meta-item, body.role-manager .admin-content-shell .admin-ticket-note, body.role-manager .admin-content-shell .admin-ticket-summary-row, body.role-manager .admin-content-shell .admin-announcement-body {
    padding: 12px 13px !important;
    border: 1px solid #eee4d7 !important;
    border-radius: 9px !important;
    background: #fbf8f3 !important;
}
body.role-manager .admin-content-shell .admin-ticket-info-card span, body.role-manager .admin-content-shell .admin-meta-label, body.role-manager .admin-content-shell .admin-ticket-summary-row span {
    color: #7a6c5f !important;
    font-size: 0.72rem !important;
    letter-spacing: 0.08em !important;
    font-weight: 700 !important;
}
body.role-manager .admin-content-shell .admin-ticket-info-card strong, body.role-manager .admin-content-shell .admin-ticket-summary-row strong, body.role-manager .admin-content-shell .admin-meta-value {
    color: #2c2419 !important;
    font-size: 0.92rem !important;
    font-weight: 600 !important;
    line-height: 1.4 !important;
}
body.role-manager .admin-content-shell .admin-ticket-info-card p, body.role-manager .admin-content-shell .admin-ticket-note p, body.role-manager .admin-content-shell .admin-announcement-body {
    color: #63574e !important;
    font-size: 0.92rem !important;
    line-height: 1.7 !important;
}
body.role-manager .admin-content-shell .admin-ticket-info-card, body.role-manager .admin-content-shell .admin-meta-item, body.role-manager .admin-content-shell .admin-ticket-note, body.role-manager .admin-content-shell .admin-ticket-summary-row {
    padding: 14px 15px !important;
}
body.role-manager .admin-content-shell .admin-ticket-form label {
    color: #786b60 !important;
    font-size: 0.78rem !important;
}
body.role-manager .admin-content-shell .admin-ticket-form select, body.role-manager .admin-content-shell .admin-ticket-form textarea {
    padding: 10px 11px !important;
    border: 1px solid #e3d8ca !important;
    border-radius: 8px !important;
    background: #fffdf9 !important;
    color: #342a23 !important;
    font-size: 0.8rem !important;
}
body.role-manager .admin-content-shell .admin-ticket-action, body.role-manager .admin-content-shell .admin-primary-btn, body.role-manager .admin-content-shell .admin-secondary-btn {
    padding: 9px 12px !important;
    border-radius: 8px !important;
    font-size: 0.76rem !important;
}
body.role-manager .admin-content-shell .admin-ticket-action-primary, body.role-manager .admin-content-shell .admin-action-link {
    background: #fbf3e4 !important;
    color: #8b5b1d !important;
    border: 1px solid #ead6b8 !important;
    box-shadow: none !important;
}
body.role-manager .admin-content-shell .admin-secondary-btn {
    background: #fffdf9 !important;
    color: #786b60 !important;
    border: 1px solid #e3d8ca !important;
}
body.role-manager .admin-content-shell .admin-danger-btn, body.role-manager .admin-content-shell .admin-ticket-action-danger {
    border-color: #ecc9c5 !important;
    background: #fcf0ef !important;
    color: #a3423b !important;
}
body.role-manager .admin-content-shell .account-hero {
    align-items: center !important;
}
body.role-manager .admin-content-shell .account-panel-head {
    padding: 13px 15px !important;
}
body.role-manager .admin-content-shell .resident-booking-page {
    max-width: 1580px !important;
    padding: 0 !important;
    gap: 16px !important;
}
body.role-manager .admin-content-shell .resident-booking-hero, body.role-manager .admin-content-shell .resident-booking-panel {
    padding: 18px !important;
    border: 1px solid #e3d8ca !important;
    border-radius: 12px !important;
    background: #fffdf9 !important;
    box-shadow: 0 3px 10px rgba(84, 61, 37, 0.035) !important;
    backdrop-filter: none !important;
}
body.role-manager .admin-content-shell .resident-booking-hero {
    padding: 24px 26px !important;
    border-radius: 14px !important;
    background: linear-gradient(135deg, #fffaf2 0%, #f8f3eb 58%, #f1e7d8 100%) !important;
}
body.role-manager .admin-content-shell .resident-booking-kicker {
    color: #b47721 !important;
}
body.role-manager .admin-content-shell .resident-booking-title, body.role-manager .admin-content-shell .resident-booking-panel-head h2 {
    color: #342a23 !important;
}
body.role-manager .admin-content-shell .resident-booking-title {
    font-size: clamp(2rem, 3.4vw, 3rem) !important;
}
body.role-manager .admin-content-shell .resident-booking-subtitle, body.role-manager .admin-content-shell .resident-booking-panel-head p {
    color: #786b60 !important;
    font-size: 0.84rem !important;
}
body.role-manager .admin-content-shell .resident-booking-hero-stats, body.role-manager .admin-content-shell .resident-booking-detail-list, body.role-manager .admin-content-shell .resident-booking-meta-grid {
    gap: 9px !important;
}
body.role-manager .admin-content-shell .resident-booking-hero-stat, body.role-manager .admin-content-shell .resident-booking-detail-box {
    padding: 12px 13px !important;
    border: 1px solid #eee4d7 !important;
    border-radius: 9px !important;
    background: #fbf8f3 !important;
}
body.role-manager .admin-content-shell .resident-booking-hero-stat span, body.role-manager .admin-content-shell .resident-booking-detail-box span {
    color: #8b7d70 !important;
    font-size: 0.68rem !important;
}
body.role-manager .admin-content-shell .resident-booking-hero-stat strong, body.role-manager .admin-content-shell .resident-booking-detail-box strong {
    color: #342a23 !important;
    font-size: 0.84rem !important;
}
body.role-manager .admin-content-shell .resident-booking-hero-actions a {
    padding: 9px 12px !important;
    border: 1px solid #ead6b8 !important;
    border-radius: 8px !important;
    background: #fbf3e4 !important;
    box-shadow: none !important;
    color: #8b5b1d !important;
    font-size: 0.76rem !important;
    font-weight: 700 !important;
}
/* Maintenance record facts use the same quiet table rhythm as account details. */
body.role-manager .admin-content-shell .admin-ticket-show-panel {
    background: #f7f6f3 !important;
}
body.role-manager .admin-content-shell .admin-ticket-info-grid {
    overflow: hidden !important;
    display: grid !important;
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    gap: 0 !important;
    border-top: 1px solid #e3d8ca !important;
    border-left: 1px solid #e3d8ca !important;
    border-radius: 0 !important;
    background: transparent !important;
}
body.role-manager .admin-content-shell .admin-ticket-info-card {
    min-height: 70px !important;
    padding: 13px 15px !important;
    border: 0 !important;
    border-right: 1px solid #e3d8ca !important;
    border-bottom: 1px solid #e3d8ca !important;
    border-radius: 0 !important;
    background: transparent !important;
    box-shadow: none !important;
}
body.role-manager .admin-content-shell .admin-ticket-info-card-wide {
    grid-column: 1 / -1 !important;
}
body.role-manager .admin-content-shell .admin-ticket-info-card span {
    margin-bottom: 7px !important;
    color: #8b7d70 !important;
    font-family: 'Inter', sans-serif !important;
    font-size: 0.67rem !important;
    font-weight: 700 !important;
    letter-spacing: 0.1em !important;
    line-height: 1.25 !important;
}
body.role-manager .admin-content-shell .admin-ticket-info-card strong {
    color: #453b33 !important;
    font-family: 'Inter', sans-serif !important;
    font-size: 0.86rem !important;
    font-weight: 600 !important;
    letter-spacing: 0 !important;
    line-height: 1.55 !important;
}
body.role-manager .admin-content-shell .admin-ticket-info-card p {
    color: #786b60 !important;
    font-family: 'Inter', sans-serif !important;
    font-size: 0.84rem !important;
    font-weight: 400 !important;
    line-height: 1.65 !important;
}
body.role-manager .admin-content-shell .admin-ticket-media {
    margin-top: 5px !important;
    border: 1px solid #e3d8ca !important;
    border-radius: 8px !important;
}
/* Brown detail-record pass: bring remaining admin record pages into the current system. */
body.role-manager .admin-content-shell :is(.admin-detail-page, .access-form-page) {
    max-width: 1580px !important;
    margin: 0 auto !important;
    gap: 18px !important;
}
body.role-manager .admin-content-shell :is(.admin-detail-hero, .access-form-hero) {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 24px !important;
    overflow: hidden !important;
    padding: 26px 30px !important;
    border: 1px solid rgba(107, 79, 58, 0.22) !important;
    border-radius: 14px !important;
    background: #6b4f3a !important;
    box-shadow: 0 14px 28px rgba(79, 58, 44, 0.12) !important;
}
body.role-manager .admin-content-shell :is(.admin-detail-hero)::before,
body.role-manager .admin-content-shell :is(.admin-detail-hero) > .absolute {
    display: none !important;
}
body.role-manager .admin-content-shell :is(
    .admin-ticket-show-kicker,
    .admin-detail-hero p:first-child,
    .admin-community-detail-kicker,
    .admin-concern-record-hero p,
    .account-hero p,
    .access-form-hero p,
    .admin-announce-show-page .admin-detail-hero span[class*="tracking-"]
) {
    margin: 0 0 8px !important;
    color: rgba(255, 247, 234, 0.74) !important;
    font-size: 0.68rem !important;
    font-weight: 800 !important;
    letter-spacing: 0.18em !important;
    line-height: 1.2 !important;
    text-transform: uppercase !important;
}
body.role-manager .admin-content-shell :is(
    .admin-ticket-show-title,
    .admin-detail-hero h1,
    .account-hero h1,
    .admin-concern-title,
    .admin-community-detail-hero h1,
    .access-form-hero h1,
    .admin-announce-show-page .admin-detail-hero h1
) {
    margin: 0 !important;
    color: #fff7ea !important;
    font-family: 'Playfair Display', serif !important;
    font-size: clamp(2rem, 3.2vw, 3.15rem) !important;
    font-weight: 400 !important;
    letter-spacing: 0 !important;
    line-height: 1.08 !important;
}
body.role-manager .admin-content-shell :is(
    .admin-ticket-show-subtitle,
    .admin-detail-hero p:not(:first-child),
    .admin-detail-hero span:not(.admin-ticket-badge):not(.account-status):not(.admin-community-detail-status),
    .account-hero span,
    .admin-concern-subtitle,
    .access-form-hero span
) {
    display: block !important;
    max-width: 780px !important;
    margin-top: 8px !important;
    color: rgba(255, 247, 234, 0.78) !important;
    font-size: 0.9rem !important;
    font-weight: 400 !important;
    line-height: 1.55 !important;
}
body.role-manager .admin-content-shell :is(
    .admin-ticket-show-back,
    .admin-detail-hero a,
    .account-hero a,
    .admin-concern-record-hero a,
    .admin-community-detail-hero a,
    .access-form-hero a,
    .admin-announce-show-page .admin-detail-hero a
) {
    display: inline-flex !important;
    min-height: 45px !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 0 20px !important;
    border: 0 !important;
    border-radius: 777px !important;
    background: linear-gradient(90deg, #b8842f 0%, #d6a85b 100%) !important;
    color: #fff !important;
    box-shadow: 0 12px 28px rgba(199, 150, 69, 0.26) !important;
    font-size: 0.74rem !important;
    font-weight: 800 !important;
    letter-spacing: 0.075em !important;
    line-height: 1 !important;
    text-decoration: none !important;
    text-transform: uppercase !important;
    white-space: nowrap !important;
    transition: transform 0.22s ease, box-shadow 0.22s ease, filter 0.22s ease !important;
}
body.role-manager .admin-content-shell :is(
    .admin-ticket-show-back,
    .admin-detail-hero a,
    .account-hero a,
    .admin-concern-record-hero a,
    .admin-community-detail-hero a,
    .access-form-hero a,
    .admin-announce-show-page .admin-detail-hero a
):hover {
    transform: translateY(-2px) !important;
    filter: brightness(1.04) !important;
    box-shadow: 0 18px 36px rgba(199, 150, 69, 0.34) !important;
}
body.role-manager .admin-content-shell :is(.admin-detail-panel, .access-form-card, .access-control-panel) {
    overflow: hidden !important;
    padding: 0 !important;
    border: 1px solid rgba(107, 79, 58, 0.22) !important;
    border-radius: 14px !important;
    background: #6b4f3a !important;
    box-shadow: 0 14px 28px rgba(79, 58, 44, 0.12) !important;
    backdrop-filter: none !important;
}
body.role-manager .admin-content-shell :is(
    .admin-ticket-show-panel-head,
    .account-panel-head,
    .admin-concern-record-head,
    .admin-community-detail-head,
    .access-form-head,
    .access-control-head
) {
    margin: 0 !important;
    padding: 22px 28px 18px !important; 
    background: #6b4f3a !important;
}
body.role-manager .admin-content-shell :is(
    .admin-ticket-show-panel-head h2,
    .account-panel-head h2,
    .admin-concern-record-head h2,
    .admin-community-detail-head h2,
    .access-form-head h2,
    .access-control-head h2
) {
    margin: 0 !important;
    color: #fff7ea !important;
    font-family: 'Playfair Display', serif !important;
    font-size: 1.45rem !important;
    font-weight: 400 !important;
    letter-spacing: 0 !important;
    line-height: 1.18 !important;
}
body.role-manager .admin-content-shell :is(
    .admin-ticket-show-panel-head p,
    .account-panel-head p,
    .admin-concern-record-head p,
    .admin-community-detail-head p,
    .access-form-head p,
    .access-control-head p
) {
    margin: 5px 0 0 !important;
    color: rgba(255, 247, 234, 0.76) !important;
    font-size: 0.78rem !important;
    font-weight: 500 !important;
    letter-spacing: 0 !important;
    line-height: 1.5 !important;
    text-transform: none !important;
}
body.role-manager .admin-content-shell :is(
    .admin-ticket-show-badges,
    .account-panel-head > span,
    .admin-concern-record-head > span,
    .admin-community-detail-status
) {
    align-self: center !important;
}
body.role-manager .admin-content-shell :is(
    .admin-ticket-info-grid,
    .account-facts,
    .account-counts,
    .account-records,
    .account-timeline,
    .admin-concern-record-facts,
    .admin-community-detail-facts,
    .admin-community-detail-comments,
    .access-form-grid
) {
    overflow: hidden !important;
    margin: 22px !important;
    border: 1px solid rgba(227, 216, 202, 0.92) !important;
    border-radius: 8px !important;
    background: #fffdf9 !important;
}
body.role-manager .admin-content-shell .access-form {
    padding: 0 !important;
    gap: 0 !important;
    background: transparent !important;
}
body.role-manager .admin-content-shell :is(.access-form-warning, .access-form-help) {
    margin: 18px 22px 0 !important;
}
body.role-manager .admin-content-shell :is(.access-form-actions, .account-pagination) {
    margin: 0 22px 22px !important;
    padding-top: 0 !important;
}
body.role-manager .admin-content-shell :is(
    .admin-ticket-info-card,
    .account-facts > div,
    .account-counts > div,
    .account-records > a,
    .account-timeline > article,
    .admin-concern-record-facts > div,
    .admin-community-detail-facts > div,
    .admin-community-detail-comments > article,
    .access-form-grid label
) {
   
    border-right: 1px solid #e3d8ca !important;
    border-bottom: 1px solid #e3d8ca !important;
    border-radius: 0 !important;
    background: transparent !important;
    box-shadow: none !important;
}
body.role-manager .admin-content-shell :is(
    .admin-ticket-info-card:nth-child(2n),
    .account-facts > div:nth-child(2n),
    .admin-concern-record-facts > div:nth-child(2n),
    .admin-community-detail-facts > div:nth-child(2n)
) {
    border-right: 0 !important;
}
body.role-manager .admin-content-shell :is(
    .admin-ticket-info-card span,
    .account-facts span,
    .account-counts dt,
    .admin-concern-record-facts small,
    .admin-community-detail-facts span,
    .access-form-grid label > span,
    .admin-community-detail-copy span,
    .admin-concern-record-message small
) {
    display: block !important;
    margin: 0 0 7px !important;
    color: #8b7d70 !important;
    font-family: 'Inter', sans-serif !important;
    font-size: 0.67rem !important;
    font-weight: 800 !important;
    letter-spacing: 0.1em !important;
    line-height: 1.25 !important;
    text-transform: uppercase !important;
}
body.role-manager .admin-content-shell :is(
    .admin-ticket-info-card strong,
    .account-facts strong,
    .account-counts dd,
    .account-records strong,
    .account-timeline strong,
    .admin-concern-record-facts strong,
    .admin-community-detail-facts strong
) {
    color: #453b33 !important;
    font-family: 'Inter', sans-serif !important;
    font-size: 0.88rem !important;
    font-weight: 600 !important;
    letter-spacing: 0 !important;
    line-height: 1.5 !important;
}
body.role-manager .admin-content-shell :is(
    .admin-ticket-info-card p,
    .account-records span,
    .account-timeline p,
    .admin-concern-record-message p,
    .admin-community-detail-copy p,
    .admin-community-detail-comments p,
    .access-control-help
) {
    color: #786b60 !important;
    font-family: 'Inter', sans-serif !important;
    font-size: 0.84rem !important;
    font-weight: 400 !important;
    line-height: 1.6 !important;
}
body.role-manager .admin-content-shell :is(.admin-concern-record-message, .admin-community-detail-copy, .ticket-completion-note, .handyman-time-tracking) {
    margin: 0 22px 22px !important;
    padding: 16px !important;
    border: 1px solid #e3d8ca !important;
    border-radius: 8px !important;
    background: #fffdf9 !important;
}
body.role-manager .admin-content-shell :is(
    .admin-ticket-action,
    .admin-user-form-action,
    .account-control,
    .account-password-copy,
    .account-password-dismiss,
    .admin-concern-reply-form button
) {
    display: inline-flex !important;
    min-height: 45px !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 0 20px !important;
    border: 0 !important;
    border-radius: 777px !important;
    background: linear-gradient(90deg, #b8842f 0%, #d6a85b 100%) !important;
    color: #fff !important;
    box-shadow: 0 12px 28px rgba(199, 150, 69, 0.26) !important;
    font-size: 0.74rem !important;
    font-weight: 800 !important;
    letter-spacing: 0.075em !important;
    line-height: 1 !important;
    text-decoration: none !important;
    text-transform: uppercase !important;
    transition: transform 0.22s ease, box-shadow 0.22s ease, filter 0.22s ease !important;
}
body.role-manager .admin-content-shell :is(
    .admin-ticket-action:hover,
    .admin-user-form-action:hover,
    .account-control:hover,
    .account-password-copy:hover,
    .account-password-dismiss:hover,
    .admin-concern-reply-form button:hover
) {
    transform: translateY(-2px) !important;
    filter: brightness(1.04) !important;
    box-shadow: 0 18px 36px rgba(199, 150, 69, 0.34) !important;
}
body.role-manager .admin-content-shell :is(.admin-user-form-action-secondary, .account-password-dismiss) {
    border: 1px solid #d2ae7b !important;
    background: #f3e3cc !important;
    color: #68400f !important;
    box-shadow: none !important;
}
body.role-manager .admin-content-shell :is(.admin-ticket-action-danger, .account-control.is-danger) {
    border: 1px solid #dda29d !important;
    background: #f7dfdc !important;
    color: #8f342e !important;
    box-shadow: none !important;
}
body.role-manager .admin-content-shell .account-control.is-success {
    border: 1px solid #9fc6a8 !important;
    background: #deeee1 !important;
    color: #356140 !important;
    box-shadow: none !important;
}
@media (max-width:1180px) {
    body.role-manager .admin-content-shell .admin-compact-stats-5, body.role-manager .admin-content-shell .admin-compact-stats-4 {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }
}
@media (max-width:860px) {
    body.role-manager .admin-content-shell .admin-compact-stats-3 {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }
}
@media (max-width:620px) {
    body.role-manager .admin-content-shell .admin-compact-stats {
        grid-template-columns: 1fr !important;
    }
    body.role-manager .admin-content-shell .admin-ticket-info-grid {
        grid-template-columns: 1fr !important;
    }
}
/* Whole-system mobile repair pass:keeps every workflow usable on narrow phones. */
.app-main, .admin-main-content, .admin-content-shell, .app-main *, .admin-main-content *, .admin-content-shell * {
    min-width: 0;
}
.app-main img, .admin-main-content img, .app-main video, .admin-main-content video, .app-main canvas, .admin-main-content canvas {
    max-width: 100%;
}
.app-main input, .app-main select, .app-main textarea, .admin-main-content input, .admin-main-content select, .admin-main-content textarea {
    max-width: 100%;
    min-height: 44px;
    font-size: 16px;
}
.app-main button:not(.resident-booking-action), .app-main a[class*="btn"], .app-main a[class*="button"], .app-main a[class*="action"]:not(.resident-booking-action), .admin-main-content button, .admin-main-content a[class*="btn"], .admin-main-content a[class*="button"], .admin-main-content a[class*="action"] {
    min-height: 44px;
    max-width: 100%;
    align-items: center;
    justify-content: center;
    text-align: center;
    line-height: 1.2;
    white-space: normal;
    overflow-wrap: anywhere;
}
.app-main table, .admin-main-content table {
    width: 100%;
}
.app-main .overflow-x-auto, .admin-main-content .overflow-x-auto, .admin-main-content .da-table-wrap, .admin-main-content .calendar-table-wrap {
    max-width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}
@media (max-width:768px) {
    html, body {
        width: 100%;
        overflow-x: hidden;
    }
    .app-main, .app-main.full-bleed, .admin-main-content {
        padding: 16px 14px 38px !important;
    }
    .admin-content-shell, .admin-content-shell.dashboard-shell, body.role-manager .admin-content-shell:not(.dashboard-shell) {
        width: 100% !important;
        max-width: 100% !important;
        padding: 0 !important;
        border-radius: 0 !important;
    }
    .top-bg-image-layer {
        height: 300px !important;
        background-size: auto 100% !important;
    }
    .role-topbar-wrap {
        padding: 14px 14px 6px !important;
    }
    .role-mobile-panel {
        max-height: calc(100dvh - 86px);
        overflow-y: auto;
        overscroll-behavior: contain;
    }
    .role-mobile-panel .role-nav-link, .role-mobile-panel .role-action-btn, .role-mobile-notification-item {
        min-height: 46px;
        white-space: normal;
    }
    .app-main h1, .admin-main-content h1, .admin-content-shell h1 {
        max-width: 100%;
        font-size: clamp(1.75rem, 9vw, 2.45rem) !important;
        line-height: 1.12 !important;
        overflow-wrap: anywhere;
    }
    .app-main h2, .admin-main-content h2, .admin-content-shell h2 {
        font-size: clamp(1.18rem, 5.8vw, 1.65rem) !important;
        line-height: 1.18 !important;
        overflow-wrap: anywhere;
    }
    .app-main p, .admin-main-content p, .admin-content-shell p {
        overflow-wrap: anywhere;
    }
    .app-main > .relative, .admin-content-shell > .relative, .app-main [class*="hero"], .admin-main-content [class*="hero"], .app-main [class*="Hero"], .admin-main-content [class*="Hero"] {
        max-width: 100%;
    }
    .app-main .relative.z-10, .admin-main-content .relative.z-10 {
        padding: 22px 18px !important;
    }
    .app-main [class*="grid-cols-2"], .app-main [class*="grid-cols-3"], .app-main [class*="grid-cols-4"], .app-main [class*="grid-cols-5"], .app-main [class*="grid-cols-6"], .admin-main-content [class*="grid-cols-2"], .admin-main-content [class*="grid-cols-3"], .admin-main-content [class*="grid-cols-4"], .admin-main-content [class*="grid-cols-5"], .admin-main-content [class*="grid-cols-6"] {
        grid-template-columns: 1fr !important;
    }
    .app-main [class*="md:flex-row"], .admin-main-content [class*="md:flex-row"], .app-main [class*="lg:flex-row"], .admin-main-content [class*="lg:flex-row"] {
        align-items: stretch !important;
    }
    .app-main .flex, .admin-main-content .flex {
        min-width: 0;
    }
    .app-main .gap-6, .admin-main-content .gap-6, .app-main .gap-8, .admin-main-content .gap-8 {
        gap: 14px !important;
    }
    .app-main table, .admin-main-content table {
        min-width: 0;
    }
    .app-main th, .app-main td, .admin-main-content th, .admin-main-content td {
        white-space: normal;
    }
    .app-main form, .admin-main-content form {
        max-width: 100%;
    }
    .app-main form .flex, .admin-main-content form .flex, .app-main [class*="actions"], .admin-main-content [class*="actions"], .app-main [class*="Actions"], .admin-main-content [class*="Actions"] {
        flex-wrap: wrap;
    }
    .app-main [class*="actions"] > a, .app-main [class*="actions"] > button, .app-main [class*="Actions"] > a, .app-main [class*="Actions"] > button, .admin-main-content [class*="actions"] > a, .admin-main-content [class*="actions"] > button, .admin-main-content [class*="Actions"] > a, .admin-main-content [class*="Actions"] > button {
        flex: 1 1 180px;
    }
    .app-main [class*="card"], .app-main [class*="Card"], .app-main [class*="panel"], .app-main [class*="Panel"], .admin-main-content [class*="card"], .admin-main-content [class*="Card"], .admin-main-content [class*="panel"], .admin-main-content [class*="Panel"] {
        max-width: 100%;
        border-radius: 14px !important;
    }
    .app-toast-stack {
        top: 12px;
        right: 12px;
        left: 12px;
        width: auto;
    }
    .app-toast {
        width: 100%;
        max-width: none;
    }
}
@media (max-width:560px) {
    .app-main, .app-main.full-bleed, .admin-main-content {
        padding-inline: 12px !important;
    }
    .role-brand-title {
        font-size: 1.35rem !important;
    }
    .role-brand-subtitle {
        font-size: 0.58rem !important;
        letter-spacing: 0.14em !important;
    }
    .app-main button[type="submit"], .app-main input[type="submit"], .app-main input[type="button"], .admin-main-content button[type="submit"], .admin-main-content input[type="submit"], .admin-main-content input[type="button"] {
        width: 100%;
    }
    .app-main button[aria-label], .admin-main-content button[aria-label], .app-main .role-mobile-toggle, .admin-main-content .admin-burger-btn, .admin-main-content [data-toast-close] {
        width: 44px;
        min-width: 44px;
        flex: 0 0 44px;
    }
    .app-main table, .admin-main-content table {
        min-width: 0;
    }
    .app-main .relative.z-10, .admin-main-content .relative.z-10 {
        padding: 18px 14px !important;
    }
    .app-main [class*="px-8"], .admin-main-content [class*="px-8"], .app-main [class*="px-10"], .admin-main-content [class*="px-10"], .app-main [class*="px-12"], .admin-main-content [class*="px-12"], .app-main [class*="px-14"], .admin-main-content [class*="px-14"] {
        padding-left: 16px !important;
        padding-right: 16px !important;
    }
    .app-main [class*="py-10"], .admin-main-content [class*="py-10"], .app-main [class*="py-12"], .admin-main-content [class*="py-12"] {
        padding-top: 20px !important;
        padding-bottom: 20px !important;
    }
}
/* Mobile app redesign pass: mobile gets its own interaction rhythm while desktop remains untouched. */
@media (max-width: 768px) {
    :root {
        --mobile-page-pad: clamp(16px, 4.8vw, 20px);
        --mobile-section-gap: 24px;
        --mobile-card-gap: 14px;
        --mobile-card-radius: 16px;
        --mobile-card-pad: clamp(16px, 4.8vw, 20px);
        --mobile-touch: 48px;
        --mobile-shadow: 0 14px 28px rgba(35, 25, 15, 0.13);
    }
    html {
        -webkit-text-size-adjust: 100%;
        scroll-padding-top: 78px;
    }
    body {
        min-width: 320px;
    }
    .global-bg-glow {
        opacity: 0.72;
    }
    .top-bg-image-layer {
        height: 230px !important;
        opacity: 0.58 !important;
        background-size: cover !important;
        mask-image: linear-gradient(to bottom, black 20%, transparent 100%) !important;
        -webkit-mask-image: linear-gradient(to bottom, black 20%, transparent 100%) !important;
    }
    .app-main,
    .app-main.full-bleed,
    .app-main.app-main-handyman,
    .admin-main-content {
        width: 100% !important;
        max-width: 100% !important;
        padding: 78px var(--mobile-page-pad) 34px !important;
    }
    body.role-resident .app-main {
        padding-top: 14px !important;
    }
    .admin-content-shell,
    .admin-content-shell.dashboard-shell,
    .app-main > *,
    .admin-content-shell > * {
        max-width: 100% !important;
    }
    .admin-content-shell,
    .app-main,
    .app-main > :is(.space-y-4, .space-y-5, .space-y-6, .space-y-8),
    .admin-content-shell > :is(.space-y-4, .space-y-5, .space-y-6, .space-y-8),
    .dash-root,
    .hs-dashboard,
    .resident-dashboard-shell,
    .staff-page,
    .resident-page,
    .concern-page,
    .community-feed-page {
        display: flex !important;
        flex-direction: column !important;
        gap: var(--mobile-section-gap) !important;
    }
    .app-main > :is(.space-y-4, .space-y-5, .space-y-6, .space-y-8) > *,
    .admin-content-shell > :is(.space-y-4, .space-y-5, .space-y-6, .space-y-8) > *,
    .dash-root > *,
    .hs-dashboard > *,
    .resident-dashboard-shell > *,
    .staff-page > * {
        margin-top: 0 !important;
        margin-bottom: 0 !important;
    }
    .role-topbar-wrap {
        position: sticky;
        top: 0;
        z-index: 900;
        padding: 10px var(--mobile-page-pad) 8px !important;
        background: color-mix(in srgb, var(--app-bg) 86%, transparent);
        backdrop-filter: blur(16px);
        border-bottom: 1px solid rgba(214, 168, 91, 0.12);
    }
    body.role-manager .admin-nav-wrapper::before,
    body.role-handyman .staff-nav-wrapper::before {
        content: "HallSync";
        position: fixed;
        top: 12px;
        left: 74px;
        z-index: 190;
        color: var(--text-heading);
        font-family: 'Inter', sans-serif;
        font-size: 1.04rem;
        font-weight: 800;
        line-height: 1;
        letter-spacing: 0;
        pointer-events: none;
    }
    body.role-manager .admin-nav-wrapper::after,
    body.role-handyman .staff-nav-wrapper::after {
        content: "Operations";
        position: fixed;
        top: 31px;
        left: 75px;
        z-index: 190;
        color: var(--text-muted);
        font-size: 0.68rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        pointer-events: none;
    }
    .admin-burger-btn,
    .staff-burger-btn {
        top: 10px !important;
        left: var(--mobile-page-pad) !important;
        width: 48px !important;
        height: 48px !important;
        min-width: 48px !important;
        border-radius: 15px !important;
        z-index: 210 !important;
    }
    .admin-overlay,
    .staff-overlay {
        background: rgba(25, 20, 16, 0.46) !important;
        backdrop-filter: blur(8px) !important;
    }
    .admin-sidebar,
    .staff-sidebar {
        width: min(88vw, 340px) !important;
        max-width: 340px !important;
        padding-bottom: env(safe-area-inset-bottom) !important;
    }
    .sidebar-nav,
    .staff-sidebar-nav,
    .role-mobile-panel {
        gap: 10px !important;
    }
    .nav-item,
    .staff-nav-item,
    .role-mobile-panel .role-nav-link,
    .role-mobile-panel .role-action-btn {
        min-height: 48px !important;
        border-radius: 14px !important;
        font-size: 0.94rem !important;
    }
    .role-topbar {
        min-height: 52px;
    }
    .role-brand-title {
        font-size: 1.3rem !important;
        line-height: 1 !important;
    }
    .role-brand-subtitle {
        font-size: 0.62rem !important;
        letter-spacing: 0.12em !important;
    }
    .role-mobile-toggle,
    .role-notification-btn {
        position: absolute !important;
        right: var(--mobile-page-pad) !important;
        top: 10px !important;
        width: 48px !important;
        height: 48px !important;
        min-width: 48px !important;
        border-radius: 15px !important;
    }
    .role-mobile-panel {
        position: fixed;
        inset: 70px var(--mobile-page-pad) auto;
        max-height: calc(100dvh - 88px);
        padding: 14px !important;
        overflow-y: auto;
        overscroll-behavior: contain;
        border-radius: 18px !important;
        box-shadow: 0 22px 46px rgba(0, 0, 0, 0.28);
    }
    .app-main :is(h1, .resident-home-title, .resident-page-title, .resident-ticket-title, .resident-booking-title, .community-feed-title, .community-post-title, .concern-title),
    .admin-main-content :is(h1, .hs-title, .admin-ticket-title, .access-title),
    .staff-page :is(h1, .staff-title) {
        font-size: clamp(1.45rem, 7vw, 1.7rem) !important;
        line-height: 1.12 !important;
        letter-spacing: 0 !important;
        max-width: 100% !important;
    }
    .app-main :is(h2, .resident-section-title),
    .admin-main-content :is(h2, .hs-section-title),
    .staff-page h2 {
        font-size: clamp(1.15rem, 5.3vw, 1.3rem) !important;
        line-height: 1.18 !important;
        letter-spacing: 0 !important;
    }
    .app-main :is(p, li, label, input, select, textarea),
    .admin-main-content :is(p, li, label, input, select, textarea),
    .staff-page :is(p, li, label, input, select, textarea) {
        font-size: max(15px, 0.94rem);
        line-height: 1.55;
    }
    .app-main :is([class*="hero"], [class*="Hero"]),
    .admin-main-content :is([class*="hero"], [class*="Hero"]),
    .staff-page :is([class*="hero"], [class*="Hero"]) {
        display: flex !important;
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 14px !important;
        min-height: 0 !important;
        padding: var(--mobile-card-pad) !important;
        border-radius: var(--mobile-card-radius) !important;
    }
    .app-main :is(.grid, [class*="grid-cols-"], .hs-metrics, .hs-command-grid, .hs-analytics-grid, .hs-diagnostic-grid, .da-kpi-grid, .da-insight-grid, .da-two-col, .access-stats, .access-filters, .resident-content-grid, .resident-activity-grid, .resident-card-grid, .resident-meta-grid, .staff-metrics, .staff-grid),
    .admin-main-content :is(.grid, [class*="grid-cols-"], .hs-metrics, .hs-command-grid, .hs-analytics-grid, .hs-diagnostic-grid, .da-kpi-grid, .da-insight-grid, .da-two-col, .access-stats, .access-filters, .admin-compact-stats, .admin-ticket-info-grid),
    .staff-page :is(.grid, [class*="grid-cols-"], .staff-metrics, .staff-grid) {
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: var(--mobile-card-gap) !important;
    }
    .app-main :is(.flex-row, [class*="flex-row"]),
    .admin-main-content :is(.flex-row, [class*="flex-row"]),
    .staff-page :is(.flex-row, [class*="flex-row"]) {
        flex-direction: column !important;
        align-items: stretch !important;
    }
    .app-main :is([class*="card"], [class*="Card"], [class*="panel"], [class*="Panel"], .hs-card, .da-card, .da-kpi-card, .da-insight-card, .resident-surface-panel, .resident-page-panel, .concern-card, .community-feed-card, .staff-ticket-card),
    .admin-main-content :is([class*="card"], [class*="Card"], [class*="panel"], [class*="Panel"], .hs-card, .da-card, .da-kpi-card, .da-insight-card, .ticket-card-shell, .admin-status-card, .access-directory),
    .staff-page :is([class*="card"], [class*="Card"], [class*="panel"], [class*="Panel"], .staff-ticket-card) {
        width: 100% !important;
        max-width: 100% !important;
        min-height: 0 !important;
        padding: var(--mobile-card-pad) !important;
        border-radius: var(--mobile-card-radius) !important;
        box-shadow: var(--mobile-shadow) !important;
        overflow: hidden;
    }
    .app-main :is(.admin-compact-stat, .hs-metric-card, .da-kpi-card, .da-insight-card, .resident-hero-stat, .staff-metric-card) strong,
    .admin-main-content :is(.admin-compact-stat, .hs-metric-card, .da-kpi-card, .da-insight-card, .access-stats article) strong {
        display: block;
        font-size: clamp(1.55rem, 10vw, 2rem) !important;
        line-height: 1.05 !important;
        font-weight: 800 !important;
        letter-spacing: 0 !important;
    }
    .app-main :is(.admin-compact-stat, .hs-metric-card, .da-kpi-card, .da-insight-card, .resident-hero-stat, .staff-metric-card) span,
    .admin-main-content :is(.admin-compact-stat, .hs-metric-card, .da-kpi-card, .da-insight-card, .access-stats article) span {
        font-size: 0.82rem !important;
        line-height: 1.35 !important;
        letter-spacing: 0.01em !important;
        text-transform: none !important;
    }
    .app-main form,
    .admin-main-content form,
    .staff-page form {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }
    .app-main :is(input:not([type="checkbox"]):not([type="radio"]), select, textarea),
    .admin-main-content :is(input:not([type="checkbox"]):not([type="radio"]), select, textarea),
    .staff-page :is(input:not([type="checkbox"]):not([type="radio"]), select, textarea) {
        width: 100% !important;
        min-height: var(--mobile-touch) !important;
        border-radius: 14px !important;
        padding: 12px 14px !important;
    }
    .app-main :is(button, a[class*="btn"], a[class*="button"], a[class*="action"]),
    .admin-main-content :is(button, a[class*="btn"], a[class*="button"], a[class*="action"]),
    .staff-page :is(button, a[class*="btn"], a[class*="button"], a[class*="action"]) {
        min-height: var(--mobile-touch) !important;
        border-radius: 14px !important;
        gap: 8px !important;
    }
    .app-main :is(.actions, .Actions, [class*="actions"], [class*="Actions"]),
    .admin-main-content :is(.actions, .Actions, [class*="actions"], [class*="Actions"]),
    .staff-page :is(.actions, .Actions, [class*="actions"], [class*="Actions"]) {
        display: flex !important;
        flex-wrap: wrap !important;
        align-items: stretch !important;
        gap: 10px !important;
    }
    .app-main :is(.actions, .Actions, [class*="actions"], [class*="Actions"]) > *,
    .admin-main-content :is(.actions, .Actions, [class*="actions"], [class*="Actions"]) > *,
    .staff-page :is(.actions, .Actions, [class*="actions"], [class*="Actions"]) > * {
        flex: 1 1 150px !important;
    }
    .app-main :is(.overflow-x-auto, .da-table-scroll, .da-table-wrap, .access-table-wrap, .calendar-table-wrap),
    .admin-main-content :is(.overflow-x-auto, .da-table-scroll, .da-table-wrap, .access-table-wrap, .calendar-table-wrap),
    .staff-page :is(.overflow-x-auto, .da-table-scroll, .da-table-wrap) {
        width: 100% !important;
        max-width: 100% !important;
        overflow-x: auto !important;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
        border-radius: var(--mobile-card-radius) !important;
    }
    .app-main table,
    .admin-main-content table,
    .staff-page table {
        min-width: 0;
        width: 100%;
    }
    .app-main :is(th, td),
    .admin-main-content :is(th, td),
    .staff-page :is(th, td) {
        padding: 13px 14px !important;
        white-space: normal !important;
        vertical-align: top;
    }
    .admin-main-content .access-table-wrap,
    .admin-main-content .da-table-wrap {
        padding: 0 !important;
    }
    .admin-main-content .access-table,
    .admin-main-content .da-table {
        min-width: 0 !important;
    }
    .admin-ticket-modal-backdrop,
    .app-confirm-backdrop {
        align-items: flex-end !important;
        padding: 12px !important;
    }
    .admin-ticket-modal-backdrop > *,
    .app-confirm-dialog,
    [role="dialog"] {
        width: 100% !important;
        max-width: 100% !important;
        max-height: min(88dvh, 720px) !important;
        overflow-y: auto !important;
        border-radius: 22px 22px 16px 16px !important;
    }
    .admin-main-content :is(canvas, .hs-chart-body, .chart-container),
    .app-main :is(canvas, .hs-chart-body, .chart-container) {
        max-width: 100% !important;
    }
    .admin-main-content :is(.hs-chart-body, .chart-container),
    .app-main :is(.hs-chart-body, .chart-container) {
        min-height: 260px;
        overflow-x: auto;
    }
    .admin-main-content .admin-critical-brief {
        display: flex !important;
        flex-direction: column !important;
        gap: 16px !important;
        padding: 18px !important;
        border-radius: 18px !important;
    }
    .admin-main-content .admin-critical-summary,
    .admin-main-content .admin-critical-focus,
    .admin-main-content .admin-critical-card,
    .admin-main-content .admin-critical-actions {
        display: flex !important;
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 12px !important;
        width: 100% !important;
    }
    .admin-main-content .admin-critical-icon {
        width: 48px !important;
        height: 48px !important;
        border-radius: 15px !important;
    }
    .admin-main-content .admin-critical-kicker {
        font-size: 0.76rem !important;
        letter-spacing: 0.08em !important;
    }
    .admin-main-content .admin-critical-title {
        font-size: 1.2rem !important;
        line-height: 1.18 !important;
    }
    .admin-main-content .admin-critical-metrics,
    .admin-main-content .admin-critical-meta {
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: 10px !important;
    }
    .admin-main-content .ticket-card-layout,
    .admin-main-content .ticket-card-actions,
    .staff-page .staff-ticket-card {
        display: flex !important;
        flex-direction: column !important;
        align-items: stretch !important;
    }
    .admin-main-content .ticket-card-actions a,
    .admin-main-content .ticket-card-actions button {
        width: 100% !important;
    }
    .live-update-status {
        left: var(--mobile-page-pad);
        right: var(--mobile-page-pad);
        bottom: 12px;
        width: auto;
        justify-content: center;
        text-align: center;
    }
}
@media (max-width: 480px) {
    :root {
        --mobile-page-pad: 16px;
        --mobile-section-gap: 22px;
        --mobile-card-gap: 12px;
        --mobile-card-pad: 16px;
    }
    .app-main,
    .app-main.full-bleed,
    .app-main.app-main-handyman,
    .admin-main-content {
        padding-top: 76px !important;
        padding-bottom: 30px !important;
    }
    body.role-resident .app-main {
        padding-top: 12px !important;
    }
    body.role-manager .admin-nav-wrapper::before,
    body.role-handyman .staff-nav-wrapper::before {
        font-size: 0.98rem;
    }
    body.role-manager .admin-nav-wrapper::after,
    body.role-handyman .staff-nav-wrapper::after {
        font-size: 0.62rem;
    }
    .admin-sidebar,
    .staff-sidebar {
        width: min(92vw, 330px) !important;
    }
    .app-main table,
    .admin-main-content table,
    .staff-page table {
        min-width: 0;
    }
    .admin-main-content .access-table,
    .admin-main-content .da-table {
        min-width: 0 !important;
    }
}
body.role-manager .admin-content-shell .panel-heading,
body.role-manager .admin-content-shell .admin-panel-head,
body.role-manager .admin-content-shell .admin-brown-panel-head,
body.role-manager .admin-content-shell .admin-ticket-panel-head,
body.role-manager .admin-content-shell .admin-ticket-show-panel-head,
body.role-manager .admin-content-shell .admin-concern-panel-head,
body.role-manager .admin-content-shell .admin-concern-section-head,
body.role-manager .admin-content-shell .admin-user-table-head,
body.role-manager .admin-content-shell .account-panel-head,
body.role-manager .admin-content-shell .access-panel-head,
body.role-manager .admin-content-shell .access-form-head {
    border-bottom: 0 !important;
}
body.role-manager .admin-content-shell .booking-dashboard .app-soft-divider {
    display: none !important;
}
body.role-manager .admin-content-shell .booking-dashboard .booking-shell {
    gap: 18px !important;
}
body.role-manager .admin-content-shell .booking-dashboard .booking-weekly-panel .date-picker-label input {
    background: transparent !important;
    border: 0 !important;
    box-shadow: none !important;
}
body.role-manager .admin-content-shell :is(
    .booking-weekly-panel .week-strip,
    .booking-panel .calendar-legend,
    .booking-panel .summary-list,
    .booking-panel .history-list,
    .admin-ticket-panel:not(.admin-ticket-archive) [data-progressive-list],
    .admin-announcements-page .admin-panel-card > [data-progressive-list],
    .admin-community-review-panel > [data-progressive-list],
    .admin-concern-list
) {
    border: 0 !important;
}
body.role-manager .admin-content-shell .admin-community-page .admin-community-archive {
    overflow: hidden !important;
    padding: 0 !important;
    border: 1px solid rgba(107, 79, 58, 0.20) !important;
    border-radius: 14px !important;
    background: var(--manager-record-panel) !important;
    box-shadow: 0 12px 24px rgba(79, 58, 44, 0.09) !important;
}
body.role-manager .admin-content-shell .admin-community-page .admin-community-archive .admin-community-tabs {
    display: flex !important;
    flex-wrap: wrap !important;
    gap: 3px !important;
    margin: 0 !important;
    padding: 0 !important;
    border: 0 !important;
    border-bottom: 1px solid var(--manager-record-divider) !important;
    border-radius: 0 !important;
    background: var(--manager-record-panel) !important;
    box-shadow: none !important;
}
body.role-manager .admin-content-shell .admin-community-page .admin-community-archive .admin-community-tab {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 8px !important;
    min-height: 48px !important;
    padding: 12px 15px !important;
    border: 0 !important;
    border-bottom: 2px solid transparent !important;
    border-radius: 0 !important;
    background: transparent !important;
    color: var(--manager-content-body) !important;
    font-family: 'Inter', system-ui, sans-serif !important;
    font-size: 0.82rem !important;
    font-weight: 600 !important;
    box-shadow: none !important;
}
body.role-manager .admin-content-shell .admin-community-page .admin-community-archive .admin-community-tab svg {
    display: block !important;
    width: 16px !important;
    height: 16px !important;
    flex: 0 0 16px !important;
}
body.role-manager .admin-content-shell .admin-community-page .admin-community-archive .admin-community-tab span {
    color: var(--manager-content-muted) !important;
}
body.role-manager .admin-content-shell .admin-community-page .admin-community-archive .admin-community-tab:hover {
    background: rgba(15, 23, 42, 0.04) !important;
    color: var(--manager-content-title) !important;
}
body.role-manager .admin-content-shell .admin-community-page .admin-community-archive .admin-community-tab.is-active,
body.role-manager .admin-content-shell .admin-community-page .admin-community-archive .admin-community-tab.active {
    border-bottom-color: #D6A85B !important;
    background: transparent !important;
    color: #D6A85B !important;
}
body.role-manager .admin-content-shell .admin-community-page .admin-community-archive .admin-community-tab.is-active span,
body.role-manager .admin-content-shell .admin-community-page .admin-community-archive .admin-community-tab.active span {
    color: #D6A85B !important;
}
body.role-manager .admin-content-shell .admin-community-page .admin-community-archive .post-section {
    margin: 0 !important;
    background: transparent !important;
}
body.role-manager .admin-content-shell .admin-community-page .admin-community-archive .admin-community-review-panel {
    margin: 0 !important;
    padding: 0 !important;
    border: 0 !important;
    border-radius: 0 !important;
    background: transparent !important;
    box-shadow: none !important;
}
body.role-manager .admin-content-shell .admin-community-page .admin-community-archive .admin-brown-panel-head {
    margin: 0 !important;
    padding: 22px 24px 16px !important;
    border: 0 !important;
    background: #6B4F3A !important;
}
body.role-manager .admin-content-shell .admin-community-page .admin-community-archive .admin-brown-panel-head h2 {
    margin: 0 !important;
    color: #fff7ea !important;
    font-family: 'Playfair Display', serif !important;
    font-size: 1.55rem !important;
    font-weight: 400 !important;
    letter-spacing: -0.015em !important;
    line-height: 1.15 !important;
}
body.role-manager .admin-content-shell .admin-community-page .admin-community-archive .admin-brown-panel-head p {
    margin: 5px 0 0 !important;
    color: rgba(255, 247, 234, 0.78) !important;
    font-family: 'DM Sans', sans-serif !important;
    font-size: 0.82rem !important;
    font-weight: 400 !important;
    line-height: 1.5 !important;
}
body.role-manager .admin-content-shell .admin-community-page .admin-community-archive .admin-community-review-panel > div[style*="height:1px"] {
    display: none !important;
}
body.role-manager .admin-content-shell .admin-community-page .admin-community-archive .admin-community-review-panel > [data-progressive-list] {
    margin: 0 !important;
    border: 0 !important;
    border-radius: 0 !important;
    background: var(--manager-record-panel) !important;
}
body.role-manager .admin-content-shell .access-page .access-directory {
    overflow: hidden !important;
    padding: 0 !important;
    border: 1px solid rgba(107, 79, 58, 0.22) !important;
    border-radius: 14px !important;
    background: #6B4F3A !important;
    box-shadow: 0 14px 28px rgba(79, 58, 44, 0.12) !important;
}
body.role-manager .admin-content-shell .access-page .access-directory .access-directory-head {
    margin: 0 !important;
    padding: 22px 28px 18px !important;
    border: 0 !important;
    background: #6B4F3A !important;
}
body.role-manager .admin-content-shell .access-page .access-directory .access-eyebrow {
    margin: 0 0 8px !important;
    color: rgba(255, 247, 234, 0.74) !important;
}
body.role-manager .admin-content-shell .access-page .access-directory .access-directory-head h2 {
    margin: 0 !important;
    color: #fff7ea !important;
    font-family: 'Playfair Display', serif !important;
    font-size: 1.55rem !important;
    font-weight: 400 !important;
    letter-spacing: -0.015em !important;
    line-height: 1.15 !important;
}
body.role-manager .admin-content-shell .access-page .access-directory .access-panel-sub {
    margin: 6px 0 0 !important;
    color: rgba(255, 247, 234, 0.78) !important;
    font-family: 'DM Sans', sans-serif !important;
    font-size: .82rem !important;
    font-weight: 400 !important;
    line-height: 1.5 !important;
}
body.role-manager .admin-content-shell .access-page .access-directory .access-filters {
    display: grid !important;
    grid-template-columns: minmax(260px, 1fr) minmax(190px, 0.42fr) auto auto !important;
    align-items: center !important;
    gap: 8px !important;
    margin: 18px 22px 0 !important;
    padding: 12px 16px !important;
    border: 0 !important;
    border-radius: 8px !important;
    background: var(--manager-record-panel) !important;
}
body.role-manager .admin-content-shell .access-page .access-directory .access-field {
    display: flex !important;
    align-items: center !important;
    min-width: 0 !important;
}
body.role-manager .admin-content-shell .access-page .access-directory .access-select-field {
    display: flex !important;
    align-items: center !important;
    position: relative !important;
    min-width: 0 !important;
}
body.role-manager .admin-content-shell .access-page .access-directory .access-field input,
body.role-manager .admin-content-shell .access-page .access-directory .access-field select {
    display: block !important;
    box-sizing: border-box !important;
    width: 100% !important;
    height: 42px !important;
    min-height: 42px !important;
    margin: 0 !important;
    padding: 0 12px !important;
    border: 1px solid #dfd5c8 !important;
    border-radius: 6px !important;
    background: #fffdf9 !important;
    color: #453b33 !important;
    font-family: inherit !important;
    font-size: 0.88rem !important;
    font-weight: 400 !important;
    letter-spacing: 0 !important;
    line-height: 42px !important;
    text-transform: none !important;
}
body.role-manager .admin-content-shell .access-page .access-directory .access-native-select {
    position: absolute !important;
    width: 1px !important;
    height: 1px !important;
    overflow: hidden !important;
    opacity: 0 !important;
    pointer-events: none !important;
}
body.role-manager .admin-content-shell .access-page .access-directory .access-select-dropdown {
    position: relative !important;
    z-index: 30 !important;
    display: inline-flex !important;
    width: 100% !important;
    height: 42px !important;
}
body.role-manager .admin-content-shell .access-page .access-directory .access-select-dropdown.is-open {
    z-index: 80 !important;
}
body.role-manager .admin-content-shell .access-page .access-directory .access-select-trigger {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    box-sizing: border-box !important;
    gap: 6px !important;
    width: 100% !important;
    height: 42px !important;
    min-height: 42px !important;
    margin: 0 !important;
    padding: 0 12px !important;
    border: 1px solid #dfd5c8 !important;
    border-radius: 6px !important;
    background: #fffdf9 !important;
    color: #453b33 !important;
    box-shadow: none !important;
    cursor: pointer !important;
    font-family: inherit !important;
    font-size: 0.88rem !important;
    font-weight: 400 !important;
    letter-spacing: 0 !important;
    line-height: 42px !important;
    text-transform: none !important;
    white-space: nowrap !important;
    transition: border-color 0.18s ease, box-shadow 0.18s ease, background 0.18s ease !important;
}
body.role-manager .admin-content-shell .access-page .access-directory .access-select-trigger span {
    display: flex !important;
    align-items: center !important;
    height: 100% !important;
    color: #8f8174 !important;
    font-family: inherit !important;
    font-size: 0.88rem !important;
    font-weight: 400 !important;
    letter-spacing: 0 !important;
    line-height: 42px !important;
    text-transform: none !important;
}
body.role-manager .admin-content-shell .access-page .access-directory .access-select-trigger svg {
    display: block !important;
    width: 16px !important;
    height: 16px !important;
    flex: 0 0 16px !important;
    pointer-events: none !important;
    transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
}
body.role-manager .admin-content-shell .access-page .access-directory .access-select-trigger:hover,
body.role-manager .admin-content-shell .access-page .access-directory .access-select-trigger:focus-visible {
    outline: none !important;
    border-color: #c6954a !important;
    background: #ffffff !important;
    box-shadow: 0 0 0 3px rgba(214, 168, 91, 0.12) !important;
}
body.role-manager .admin-content-shell .access-page .access-directory .access-select-trigger:active {
    transform: none !important;
    box-shadow: 0 0 0 3px rgba(214, 168, 91, 0.12) !important;
}
body.role-manager .admin-content-shell .access-page .access-directory .access-select-dropdown.is-open .access-select-trigger {
    border-color: #c6954a !important;
    background: #ffffff !important;
    box-shadow: 0 0 0 3px rgba(214, 168, 91, 0.12) !important;
    transform: none !important;
    filter: none !important;
}
body.role-manager .admin-content-shell .access-page .access-directory .access-select-dropdown.is-open .access-select-trigger svg {
    transform: rotate(180deg) !important;
}
body.role-manager .admin-content-shell .access-page .access-directory .access-select-menu {
    position: absolute !important;
    top: calc(100% + 6px) !important;
    right: 0 !important;
    z-index: 90 !important;
    width: max(100%, 190px) !important;
    overflow: hidden !important;
    padding: 6px !important;
    border: 1px solid rgba(107, 79, 58, 0.16) !important;
    border-radius: 14px !important;
    background: #fffaf5 !important;
    box-shadow: 0 16px 32px rgba(47, 39, 31, 0.18) !important;
}
body.role-manager .admin-content-shell .access-page .access-directory .access-select-menu[hidden] {
    display: none !important;
}
body.role-manager .admin-content-shell .access-page .access-directory .access-select-menu button {
    display: flex !important;
    width: 100% !important;
    min-height: 36px !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 0 11px !important;
    border: 0 !important;
    border-radius: 9px !important;
    background: transparent !important;
    color: #4d3b2e !important;
    box-shadow: none !important;
    cursor: pointer !important;
    font-size: 0.78rem !important;
    font-weight: 700 !important;
    letter-spacing: 0 !important;
    line-height: 1 !important;
    text-align: center !important;
    text-transform: none !important;
    transform: none !important;
    transition: background-color 0.16s ease, color 0.16s ease !important;
}
body.role-manager .admin-content-shell .access-page .access-directory .access-select-menu button:hover,
body.role-manager .admin-content-shell .access-page .access-directory .access-select-menu button:focus-visible {
    background: #fff2e8 !important;
    color: #8f2929 !important;
    outline: none !important;
}
body.role-manager .admin-content-shell .access-page .access-directory .access-select-menu button[aria-selected="true"] {
    background: #f2dfd2 !important;
    color: #7a4f16 !important;
}
body.role-manager .admin-content-shell .access-page .access-directory .access-select-menu button[aria-label*="close"],
body.role-manager .admin-content-shell .access-page .access-directory .access-select-menu button[aria-label*="dismiss"],
body.role-manager .admin-content-shell .access-page .access-directory .access-select-menu button:has(> svg:first-child) {
    position: absolute !important;
    top: 6px !important;
    right: 12px !important;
    width: auto !important;
    min-width: 24px !important;
    height: 24px !important;
    padding: 0 !important;
    justify-content: center !important;
    z-index: 100 !important;
}
body.role-manager .admin-content-shell .access-page .access-directory .access-filter-submit,
body.role-manager .admin-content-shell .access-page .access-directory .access-bulk button,
body.role-manager .admin-content-shell .access-page .access-primary {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    min-height: 45px !important;
    padding: 0 20px !important;
    border: 0 !important;
    border-radius: 777px !important;
    background: linear-gradient(90deg, #B8842F 0%, #D6A85B 100%) !important;
    color: #fff !important;
    box-shadow: 0 12px 28px rgba(199, 150, 69, 0.3) !important;
    cursor: pointer !important;
    font-size: 0.74rem !important;
    font-weight: 800 !important;
    letter-spacing: 0.075em !important;
    line-height: 1 !important;
    text-decoration: none !important;
    text-transform: uppercase !important;
    white-space: nowrap !important;
    transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.3s ease, filter 0.3s ease !important;
}
body.role-manager .admin-content-shell .access-page .access-directory .access-filter-submit:hover,
body.role-manager .admin-content-shell .access-page .access-directory .access-bulk button:hover,
body.role-manager .admin-content-shell .access-page .access-primary:hover,
body.role-manager .admin-content-shell .access-page .access-directory .access-filter-submit:focus-visible,
body.role-manager .admin-content-shell .access-page .access-directory .access-bulk button:focus-visible,
body.role-manager .admin-content-shell .access-page .access-primary:focus-visible {
    outline: none !important;
    transform: translateY(-3px) !important;
    box-shadow: 0 20px 40px rgba(199, 150, 69, 0.4) !important;
    filter: brightness(1.05) !important;
}
body.role-manager .admin-content-shell .access-page .access-directory .access-filter-submit:active,
body.role-manager .admin-content-shell .access-page .access-directory .access-bulk button:active,
body.role-manager .admin-content-shell .access-page .access-primary:active {
    transform: translateY(-1px) !important;
    box-shadow: 0 8px 16px rgba(199, 150, 69, 0.3) !important;
}
body.role-manager .admin-content-shell .access-page .access-directory .access-bulk button:disabled {
    cursor: not-allowed !important;
    opacity: 0.55 !important;
    transform: none !important;
    filter: grayscale(0.15) !important;
    box-shadow: none !important;
}
body.role-manager .admin-content-shell .access-page .access-directory .access-filter-clear {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    min-height: 38px !important;
    padding: 0 12px !important;
    color: #fff7ea !important;
    font-size: 0.82rem !important;
}
body.role-manager .admin-content-shell .access-page .access-directory .access-role-tabs {
    margin: 18px 22px 0 !important;
    padding: 0 14px !important;
    border: 0 !important;
    border-radius: 8px 8px 0 0 !important;
    background: var(--manager-record-panel) !important;
}
body.role-manager .admin-content-shell .access-page .access-directory .access-role-tab {
    align-items: center !important;
    gap: 8px !important;
}
body.role-manager .admin-content-shell .access-page .access-directory .access-role-tab svg {
    display: block !important;
    width: 16px !important;
    height: 16px !important;
    flex: 0 0 16px !important;
}
body.role-manager .admin-content-shell .access-page .access-directory .access-table-wrap {
    overflow: hidden !important;
    margin: 0 22px 22px !important;
    border: 0 !important;
    border-radius: 0 0 8px 8px !important;
    background: var(--manager-record-panel) !important;
}
body.role-manager .admin-content-shell .access-page .access-directory .access-bulk select {
    min-height: 38px !important;
    border: 1px solid #dfd5c8 !important;
    border-radius: 7px !important;
    background: #fffdf9 !important;
    color: #453b33 !important;
}
body.role-manager .admin-content-shell .access-page .access-directory .access-bulk button {
    color: #fff7ea !important;
}
@media (max-width: 980px) {
    body.role-manager .admin-content-shell .access-page .access-directory .access-filters {
        grid-template-columns: 1fr !important;
    }
    body.role-manager .admin-content-shell .access-page .access-directory .access-field-wide {
        grid-column: 1 / -1 !important;
    }
}
@media (max-width: 640px) {
    body.role-manager .admin-content-shell .access-page .access-directory .access-filters {
        grid-template-columns: 1fr !important;
    }
    body.role-manager .admin-content-shell .access-page .access-directory .access-directory-head,
    body.role-manager .admin-content-shell .access-page .access-directory .access-bulk {
        align-items: flex-start !important;
    }
}

/* Staff UI now follows the Admin design system. */
body.role-handyman {
    --admin-bg-top: #f4efe7;
    --admin-bg-mid: #e8dfd3;
    --admin-bg-bottom: #f7f3ed;
    --manager-record-panel: #fffdf9;
    --manager-record-row: #fffdf9;
    --manager-record-divider: #e3d8ca;
    --manager-record-hover: #f7f2eb;
    --manager-content-title: #342a23;
    --manager-content-body: #5e554a;
    --manager-content-muted: #786b5d;
    --manager-content-accent: #b47721;
    --text-heading: #342a23;
    --text-body: #5e554a;
    --text-muted: #786b5d;
    background: radial-gradient(circle at top right, rgba(214, 168, 91, 0.11), transparent 28%), radial-gradient(circle at bottom left, rgba(120, 100, 75, 0.08), transparent 34%), linear-gradient(180deg, var(--admin-bg-top) 0%, var(--admin-bg-mid) 48%, var(--admin-bg-bottom) 100%) !important;
}
body.role-handyman .global-bg-glow {
    background: radial-gradient(circle at 84% 8%, rgba(214, 168, 91, 0.10), transparent 30%), radial-gradient(circle at 12% 86%, rgba(120, 100, 75, 0.07), transparent 34%), radial-gradient(circle at 50% 0%, rgba(255, 255, 255, 0.42), transparent 28%) !important;
}
body.role-handyman .top-bg-image-layer {
    height: 430px !important;
    background-image: radial-gradient(circle at 78% 20%, rgba(214, 168, 91, 0.12), transparent 24%), radial-gradient(circle at 16% 8%, rgba(120, 100, 75, 0.09), transparent 26%), linear-gradient(135deg, rgba(246, 241, 234, 0.96) 0%, rgba(232, 223, 211, 0.90) 42%, rgba(244, 238, 230, 0.86) 70%, rgba(250, 247, 242, 0.96) 100%) !important;
}
body.role-handyman .top-bg-image-layer::after {
    background: linear-gradient(90deg, rgba(244, 238, 230, 0.52) 0%, rgba(244, 238, 230, 0.28) 38%, rgba(244, 238, 230, 0.08) 66%, rgba(244, 238, 230, 0) 100%) !important;
}
body.role-handyman .app-main-handyman {
    max-width: 1580px !important;
    padding: 24px 24px 48px !important;
}
@media (min-width:768px) {
    body.role-handyman .app-main-handyman {
        padding: 28px 40px 56px !important;
    }
}
@media (min-width:1024px) {
    body.role-handyman .app-main-handyman {
        padding: 32px 64px 64px !important;
    }
}

/* Staff sidebar aliases to Admin sidebar language. */
body.role-handyman .staff-burger-btn {
    background: rgba(255, 249, 240, 0.86) !important;
    border: 1px solid rgba(94, 75, 55, 0.22) !important;
    border-radius: 14px !important;
    box-shadow: 0 10px 24px rgba(72, 48, 24, 0.16) !important;
    padding: 0 !important;
}
body.role-handyman .staff-burger-line {
    background: #5e4b37 !important;
}
body.role-handyman .staff-burger-btn:hover {
    background: rgba(255, 252, 246, 0.96) !important;
    border-color: rgba(138, 95, 43, 0.32) !important;
    box-shadow: 0 14px 28px rgba(72, 48, 24, 0.20) !important;
}
body.role-handyman .staff-burger-active {
    left: calc(280px - 68px) !important;
    background: rgba(52, 49, 44, 0.96) !important;
    border-color: rgba(214, 168, 91, 0.30) !important;
    box-shadow: 0 14px 34px rgba(0, 0, 0, 0.26) !important;
}
body.role-handyman .staff-burger-active .staff-burger-line {
    background: #d6a85b !important;
}
body.role-handyman .staff-burger-badge {
    position: absolute !important;
    top: -7px !important;
    right: -7px !important;
    display: inline-flex !important;
    min-width: 19px !important;
    height: 19px !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 0 5px !important;
    border: 2px solid rgba(255, 249, 240, 0.96) !important;
    border-radius: 999px !important;
    background: #c94f43 !important;
    color: #fff !important;
    font-size: 9px !important;
    font-weight: 700 !important;
    line-height: 1 !important;
    box-shadow: 0 4px 10px rgba(92, 34, 28, 0.26) !important;
}
body.role-handyman .staff-burger-active .staff-burger-badge {
    border-color: rgba(52, 49, 44, 0.96) !important;
}
body.role-handyman .staff-overlay {
    background: rgba(52, 42, 35, 0.34) !important;
    backdrop-filter: blur(6px) !important;
}
body.role-handyman .staff-sidebar {
    width: 280px !important;
    background: linear-gradient(180deg, #34312c 0%, #2f2c27 58%, #2a2621 100%) !important;
    border-right: 1px solid rgba(214, 168, 91, 0.16) !important;
    color: #f5f0e9 !important;
}
body.role-handyman .staff-sidebar-open {
    box-shadow: 18px 0 48px rgba(0, 0, 0, 0.28) !important;
}
body.role-handyman .staff-sidebar::before {
    background: radial-gradient(circle, rgba(214, 168, 91, 0.10) 0%, transparent 70%) !important;
}
body.role-handyman .staff-sidebar-header {
    padding: 32px 24px 24px !important;
    border-bottom: 1px solid rgba(214, 168, 91, 0.14) !important;
}
body.role-handyman .staff-brand-name,
body.role-handyman .staff-user-name {
    color: #f5f0e9 !important;
}
body.role-handyman .staff-brand-name span {
    color: #d6a85b !important;
}
body.role-handyman .staff-brand-tagline,
body.role-handyman .staff-nav-section-label,
body.role-handyman .staff-user-role {
    color: #c4bcb2 !important;
}
body.role-handyman .staff-role-chip {
    border-color: rgba(214, 168, 91, 0.20) !important;
    background: rgba(214, 168, 91, 0.10) !important;
    color: #d6a85b !important;
}
body.role-handyman .staff-nav-item {
    border: 1px solid transparent !important;
    border-radius: 12px !important;
    color: #c4bcb2 !important;
}
body.role-handyman .staff-nav-item:hover,
body.role-handyman .staff-nav-active {
    border-color: rgba(214, 168, 91, 0.22) !important;
    background: rgba(214, 168, 91, 0.10) !important;
    color: #f5f0e9 !important;
}
body.role-handyman .staff-nav-icon-wrap {
    border: 1px solid rgba(214, 168, 91, 0.16) !important;
    background: rgba(214, 168, 91, 0.08) !important;
    color: #d6a85b !important;
}
body.role-handyman .staff-nav-active .staff-nav-icon-wrap {
    background: linear-gradient(90deg, #b8842f 0%, #d6a85b 100%) !important;
    color: #fff !important;
}
body.role-handyman .staff-user-card {
    border-color: rgba(214, 168, 91, 0.16) !important;
    background: rgba(255, 255, 255, 0.05) !important;
}
body.role-handyman .staff-user-avatar {
    background: linear-gradient(90deg, #b8842f 0%, #d6a85b 100%) !important;
    color: #fff !important;
}
body.role-handyman .staff-logout-btn {
    border: 0 !important;
    border-radius: 777px !important;
    background: linear-gradient(90deg, #b8842f 0%, #d6a85b 100%) !important;
    color: #fff !important;
    box-shadow: 0 12px 28px rgba(199, 150, 69, 0.22) !important;
}
body.role-handyman .staff-sidebar :is(.sidebar-alerts, .sidebar-alerts-collapsible) {
    border-color: rgba(214, 168, 91, 0.14) !important;
    background: rgba(255, 255, 255, 0.04) !important;
    color: #f5f0e9 !important;
}
body.role-handyman .staff-sidebar :is(.sidebar-alerts-toggle, .sidebar-alerts-head, .sidebar-alerts-head a, .sidebar-alerts-view-all) {
    color: #f5f0e9 !important;
}
body.role-handyman .staff-sidebar :is(.sidebar-alerts-toggle:hover, .sidebar-alert-item:hover, .sidebar-alerts-view-all:hover) {
    background: rgba(214, 168, 91, 0.10) !important;
    color: #fff7ea !important;
}
body.role-handyman .staff-sidebar .sidebar-alert-item {
    border-color: rgba(214, 168, 91, 0.12) !important;
    color: #f5f0e9 !important;
}
body.role-handyman .staff-sidebar :is(.sidebar-alert-item span, .sidebar-alerts-empty) {
    color: #f5f0e9 !important;
}
body.role-handyman .staff-sidebar .sidebar-alert-item small {
    color: #c4bcb2 !important;
}
body.role-handyman .staff-sidebar .sidebar-alerts-toggle-end strong {
    display: inline-flex !important;
    min-width: 19px !important;
    height: 19px !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 0 5px !important;
    border: 2px solid rgba(52, 49, 44, 0.96) !important;
    border-radius: 999px !important;
    background: #c94f43 !important;
    color: #fff !important;
    font-size: 9px !important;
    font-weight: 700 !important;
    line-height: 1 !important;
    box-shadow: 0 4px 10px rgba(92, 34, 28, 0.26) !important;
}

/* Staff page and component system. */
body.role-handyman .staff-workspace,
body.role-handyman .handyman-ticket-show-shell {
    max-width: 1580px !important;
    margin: 0 auto !important;
    display: flex !important;
    flex-direction: column !important;
    gap: 18px !important;
    color: #342a23 !important;
}
body.role-handyman .admin-overview-hero,
body.role-handyman .handyman-ticket-hero {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 24px !important;
    overflow: hidden !important;
    padding: 28px 30px !important;
    border: 1px solid rgba(107, 79, 58, 0.18) !important;
    border-radius: 14px !important;
    background: rgba(255, 253, 249, 0.72) !important;
    box-shadow: 0 12px 28px rgba(79, 58, 44, 0.10) !important;
}
body.role-handyman .admin-overview-hero__kicker,
body.role-handyman .handyman-ticket-kicker {
    margin: 0 0 8px !important;
    color: #8a6a49 !important;
    font-size: 0.68rem !important;
    font-weight: 800 !important;
    letter-spacing: 0.18em !important;
    text-transform: uppercase !important;
}
body.role-handyman .admin-overview-hero__title,
body.role-handyman .handyman-ticket-title {
    margin: 0 !important;
    color: #342a23 !important;
    font-family: 'Playfair Display', serif !important;
    font-size: clamp(2.25rem, 4vw, 3.75rem) !important;
    font-weight: 400 !important;
    line-height: 1.02 !important;
}
body.role-handyman .admin-overview-hero__title span,
body.role-handyman .handyman-ticket-title span {
    color: #b47721 !important;
}
body.role-handyman .admin-overview-hero__subtitle,
body.role-handyman .handyman-ticket-subtitle {
    display: block !important;
    margin-top: 8px !important;
    color: #786b60 !important;
    font-size: 0.92rem !important;
    line-height: 1.6 !important;
}
body.role-handyman .handyman-ticket-glow {
    display: none !important;
}
body.role-handyman .handyman-ticket-hero-inner {
    display: contents !important;
}
body.role-handyman .staff-metric-link {
    display: block !important;
    min-width: 0 !important;
    color: inherit !important;
    text-decoration: none !important;
}
body.role-handyman .app-main-handyman .admin-compact-stats {
    --compact-stat-card-min: 200px;
    --compact-stat-min-height: 88px;
    --compact-stat-padding: 14px 16px;
    --compact-stat-main-gap: 8px;
    --compact-stat-value-size: clamp(1.45rem, 1.7vw, 1.86rem);
    display: grid !important;
    align-items: stretch !important;
    gap: 14px 18px !important;
    grid-template-columns: repeat(auto-fit, minmax(min(100%, var(--compact-stat-card-min)), 1fr)) !important;
}
body.role-handyman .app-main-handyman .admin-compact-stats-3 {
    --compact-stat-card-min: 220px;
    --compact-stat-min-height: 92px;
    --compact-stat-padding: 15px 18px;
    --compact-stat-main-gap: 9px;
    --compact-stat-value-size: clamp(1.5rem, 1.9vw, 1.95rem);
}
body.role-handyman .app-main-handyman .admin-compact-stats-4 {
    --compact-stat-card-min: 210px;
    --compact-stat-min-height: 90px;
    --compact-stat-padding: 15px 17px;
    --compact-stat-main-gap: 8px;
    --compact-stat-value-size: clamp(1.48rem, 1.8vw, 1.9rem);
}
body.role-handyman .app-main-handyman .admin-compact-stats-5 {
    --compact-stat-card-min: 176px;
    --compact-stat-min-height: 88px;
    --compact-stat-padding: 14px 16px;
    --compact-stat-main-gap: 8px;
    --compact-stat-value-size: clamp(1.45rem, 1.55vw, 1.78rem);
    gap: 12px 14px !important;
}
body.role-handyman .app-main-handyman .admin-compact-stat {
    --compact-stat-accent: #b47721;
    --compact-stat-border: rgba(180, 119, 33, 0.22);
    --compact-stat-glow: rgba(180, 119, 33, 0.12);
    --compact-stat-start: #fff8ed;
    --compact-stat-end: #ffffff;
    position: relative !important;
    isolation: isolate !important;
    overflow: hidden !important;
    display: flex !important;
    width: 100% !important;
    max-width: none !important;
    min-height: var(--compact-stat-min-height) !important;
    flex-direction: column !important;
    align-items: flex-start !important;
    justify-content: flex-start !important;
    gap: 8px !important;
    padding: var(--compact-stat-padding) !important;
    border: 1px solid var(--compact-stat-border) !important;
    border-radius: 12px !important;
    background: linear-gradient(135deg, var(--compact-stat-start) 0%, var(--compact-stat-end) 72%) !important;
    box-shadow: 0 3px 10px rgba(84, 61, 37, 0.035) !important;
    color: #342a23 !important;
    transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease !important;
}
body.role-handyman .app-main-handyman .admin-compact-stat::after {
    content: "" !important;
    position: absolute !important;
    top: -28px !important;
    right: -18px !important;
    z-index: -1 !important;
    width: 64px !important;
    height: 64px !important;
    border-radius: 50% !important;
    background: radial-gradient(circle, var(--compact-stat-glow), transparent 70%) !important;
    pointer-events: none !important;
}
body.role-handyman .app-main-handyman .admin-compact-stat:hover {
    transform: translateY(-2px) !important;
    border-color: var(--compact-stat-accent) !important;
    box-shadow: 0 7px 16px var(--compact-stat-glow) !important;
}
body.role-handyman .app-main-handyman .admin-compact-stat-blue {
    --compact-stat-accent: #52788c;
    --compact-stat-border: rgba(82, 120, 140, 0.22);
    --compact-stat-glow: rgba(82, 120, 140, 0.12);
    --compact-stat-start: #f1f7f9;
    --compact-stat-end: #ffffff;
}
body.role-handyman .app-main-handyman .admin-compact-stat-green {
    --compact-stat-accent: #4f805c;
    --compact-stat-border: rgba(79, 128, 92, 0.22);
    --compact-stat-glow: rgba(79, 128, 92, 0.12);
    --compact-stat-start: #f1f8f2;
    --compact-stat-end: #ffffff;
}
body.role-handyman .app-main-handyman .admin-compact-stat-red {
    --compact-stat-accent: #bd5349;
    --compact-stat-border: rgba(189, 83, 73, 0.22);
    --compact-stat-glow: rgba(189, 83, 73, 0.12);
    --compact-stat-start: #fdf2f1;
    --compact-stat-end: #ffffff;
}
body.role-handyman .app-main-handyman .admin-compact-stat-icon {
    display: none !important;
}
body.role-handyman .app-main-handyman .admin-compact-stat-icon svg {
    width: 17px !important;
    height: 17px !important;
    fill: none !important;
    stroke: currentColor !important;
    stroke-linecap: round !important;
    stroke-linejoin: round !important;
    stroke-width: 1.7 !important;
}
body.role-handyman .app-main-handyman .admin-compact-stat-main {
    display: flex !important;
    min-width: 0 !important;
    flex-direction: column !important;
    gap: var(--compact-stat-main-gap) !important;
}
body.role-handyman .app-main-handyman .admin-compact-stat-main span {
    order: 1 !important;
    display: block !important;
    min-width: 0 !important;
    color: #806f5c !important;
    font-size: clamp(0.6rem, 0.62vw, 0.66rem) !important;
    font-weight: 800 !important;
    letter-spacing: 0.13em !important;
    line-height: 1.32 !important;
    text-transform: uppercase !important;
}
body.role-handyman .app-main-handyman .admin-compact-stat-main strong {
    order: 2 !important;
    display: block !important;
    color: var(--compact-stat-accent) !important;
    font-family: 'Playfair Display', serif !important;
    font-size: var(--compact-stat-value-size) !important;
    font-weight: 700 !important;
    letter-spacing: 0 !important;
    line-height: 0.95 !important;
}
body.role-handyman .app-main-handyman .admin-compact-stat small {
    display: block !important;
    margin: 0 !important;
    color: #806f5c !important;
    font-size: clamp(0.68rem, 0.72vw, 0.74rem) !important;
    line-height: 1.35 !important;
    text-align: left !important;
}
body.role-handyman .staff-metric-link .admin-compact-stat {
    height: 100% !important;
}
body.role-handyman .staff-overview-stack {
    display: grid !important;
    gap: 14px !important;
}

body.role-handyman :is(.staff-panel, .handyman-ticket-panel) {
    overflow: hidden !important;
    padding: 0 !important;
    border: 1px solid rgba(107, 79, 58, 0.22) !important;
    border-radius: 14px !important;
    background: #6b4f3a !important;
    box-shadow: 0 14px 28px rgba(79, 58, 44, 0.12) !important;
}
body.role-handyman :is(.staff-panel-head, .handyman-ticket-panel-head, .handyman-ticket-side-head) {
    margin: 0 !important;
    padding: 22px 28px 18px !important;
    background: #6b4f3a !important;
}
body.role-handyman :is(.staff-panel-head h2, .handyman-ticket-panel-title, .handyman-ticket-panel-title-side) {
    margin: 0 !important;
    color: #fff7ea !important;
    font-family: 'Playfair Display', serif !important;
    font-size: 1.55rem !important;
    font-weight: 400 !important;
    letter-spacing: -0.015em !important;
    line-height: 1.15 !important;
}
body.role-handyman :is(.staff-panel-head p, .handyman-ticket-panel-sub) {
    margin: 5px 0 0 !important;
    color: rgba(255, 247, 234, 0.78) !important;
    font-size: 0.82rem !important;
    line-height: 1.5 !important;
}
body.role-handyman :is(.staff-preview-list, .staff-urgent-list, .staff-ticket-list, .staff-completed-list, .handyman-ticket-meta-list, .handyman-ticket-note-list, .handyman-ticket-section, .handyman-time-tracking) {
    display: flex !important;
    flex-direction: column !important;
    gap: 0 !important;
    overflow: hidden !important;
    margin: 0 22px 22px !important;
    border: 1px solid rgba(227, 216, 202, 0.92) !important;
    border-radius: 8px !important;
    background: #fffdf9 !important;
}
body.role-handyman .staff-panel-head + :is(.staff-preview-list, .staff-urgent-list, .staff-ticket-list, .staff-completed-list),
body.role-handyman .handyman-ticket-divider + :is(.handyman-ticket-section, .handyman-ticket-meta-list, .handyman-ticket-note-list) {
    margin-top: 22px !important;
}
body.role-handyman :is(.staff-preview-card, .staff-urgent-item, .staff-ticket-card, .staff-completed-card, .handyman-ticket-meta-item, .handyman-ticket-note-item) {
    margin: 0 !important;
    padding: 16px 18px !important;
    border: 0 !important;
    border-bottom: 1px solid #e3d8ca !important;
    border-radius: 0 !important;
    background: #fffdf9 !important;
    box-shadow: none !important;
}
body.role-handyman :is(.staff-preview-card:last-child, .staff-urgent-item:last-child, .staff-ticket-card:last-child, .staff-completed-card:last-child, .handyman-ticket-meta-item:last-child, .handyman-ticket-note-item:last-child) {
    border-bottom: 0 !important;
}
body.role-handyman :is(.staff-preview-card:hover, .staff-urgent-item:hover, .staff-ticket-card:hover, .staff-completed-card:hover) {
    background: #f7f2eb !important;
}
body.role-handyman :is(.staff-preview-card, .staff-urgent-item, .staff-completed-card) {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 14px !important;
}
body.role-handyman :is(.staff-preview-card > div, .staff-urgent-item > div, .staff-completed-card > div) {
    min-width: 0 !important;
    flex: 1 1 auto !important;
}
body.role-handyman .staff-urgent-item.is-hidden-critical {
    display: none !important;
}
body.role-handyman .staff-ticket-card {
    display: flex !important;
}
body.role-handyman [data-progressive-item].app-progressive-hidden {
    display: none !important;
}
body.role-handyman .staff-ticket-body {
    display: flex !important;
    flex: 1 1 auto !important;
    align-items: center !important;
    padding: 0 !important;
    gap: 14px !important;
}
body.role-handyman .staff-ticket-main {
    min-width: 0 !important;
    flex: 1 1 auto !important;
}
body.role-handyman .staff-ticket-top {
    display: flex !important;
    flex-wrap: wrap !important;
    align-items: center !important;
    gap: 8px 10px !important;
    margin-bottom: 6px !important;
}
body.role-handyman .staff-ticket-meta {
    display: flex !important;
    flex-wrap: wrap !important;
    gap: 7px 14px !important;
    margin-top: 10px !important;
}
body.role-handyman :is(.staff-preview-card h3, .staff-urgent-item h3, .staff-ticket-main h3, .staff-completed-card h3) {
    margin: 0 !important;
    color: #342a23 !important;
    font-family: 'Inter', sans-serif !important;
    font-size: 0.95rem !important;
    font-weight: 500 !important;
    line-height: 1.35 !important;
}
body.role-handyman :is(.staff-preview-card p, .staff-urgent-item p, .staff-ticket-main p, .staff-completed-card p, .staff-ticket-meta, .handyman-ticket-copy) {
    margin: 5px 0 0 !important;
    color: #786b60 !important;
    font-size: 0.82rem !important;
    line-height: 1.55 !important;
}
body.role-handyman :is(.staff-ticket-id, .handyman-ticket-section-label, .handyman-ticket-meta-label, .handyman-time-label) {
    color: #9b8d81 !important;
    font-size: 0.68rem !important;
    font-weight: 800 !important;
    letter-spacing: 0.10em !important;
    text-transform: uppercase !important;
}
body.role-handyman :is(.staff-ticket-status-chip, .handyman-status-badge, .handyman-priority-badge) {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    min-height: 28px !important;
    padding: 0 10px !important;
    border-radius: 999px !important;
    border: 1px solid #d2ae7b !important;
    background: #f3e3cc !important;
    color: #68400f !important;
    font-size: 0.68rem !important;
    font-weight: 800 !important;
    letter-spacing: 0.08em !important;
    text-transform: uppercase !important;
}
body.role-handyman :is(.status-in_progress, .priority-critical, .staff-metric-card-alert strong) {
    border-color: #dda29d !important;
    background: #f7dfdc !important;
    color: #8f342e !important;
}
body.role-handyman .status-in_progress {
    border-color: rgba(82, 120, 140, 0.28) !important;
    background: #e6eff3 !important;
    color: #345984 !important;
}
body.role-handyman :is(.status-resolved, .status-closed, .priority-low) {
    border-color: #9fc6a8 !important;
    background: #deeee1 !important;
    color: #356140 !important;
}
body.role-handyman :is(.staff-action-btn, .staff-panel-link, .staff-preview-card a, .staff-urgent-item a, .staff-completed-card a, .handyman-ticket-btn, .staff-show-more-btn) {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    min-height: 38px !important;
    min-width: 98px !important;
    padding: 0 14px !important;
    border: 1px solid #d2ae7b !important;
    border-radius: 8px !important;
    background: #f3e3cc !important;
    color: #68400f !important;
    box-shadow: none !important;
    cursor: pointer !important;
    font-size: 0.78rem !important;
    font-weight: 800 !important;
    line-height: 1 !important;
    text-decoration: none !important;
    white-space: nowrap !important;
    transition: transform 0.18s ease, background 0.18s ease, border-color 0.18s ease !important;
}
body.role-handyman :is(.staff-action-btn:hover, .staff-panel-link:hover, .staff-preview-card a:hover, .staff-urgent-item a:hover, .staff-completed-card a:hover, .handyman-ticket-btn:hover, .staff-show-more-btn:hover) {
    border-color: #c79745 !important;
    background: #ecd6b8 !important;
    transform: translateY(-1px) !important;
}
/* Maintenance approve button - green style matching admin */
body.role-handyman :is(.handyman-ticket-approve, .staff-action-complete, .handyman-ticket-btn-primary) {
    border: 1px solid #9fc6a8 !important;
    background: #deeee1 !important;
    color: #356140 !important;
    box-shadow: none !important;
}
body.role-handyman :is(.handyman-ticket-approve:hover, .staff-action-complete:hover, .handyman-ticket-btn-primary:hover) {
    border-color: #7ead89 !important;
    background: #cce4d1 !important;
    color: #285332 !important;
    transform: translateY(-1px) !important;
}

/* Maintenance reject button - red style matching admin */
body.role-handyman :is(.handyman-ticket-reject, .staff-action-reject) {
    border: 1px solid #dda29d !important;
    background: #f7dfdc !important;
    color: #8f342e !important;
    box-shadow: none !important;
}
body.role-handyman :is(.handyman-ticket-reject:hover, .staff-action-reject:hover) {
    border-color: #cc7f78 !important;
    background: #efc8c4 !important;
    color: #742720 !important;
    transform: translateY(-1px) !important;
}

body.role-handyman :is(.staff-filter, .handyman-completion-note-wrap textarea) {
    min-height: 42px !important;
    padding: 0 12px !important;
    border: 1px solid #dfd5c8 !important;
    border-radius: 7px !important;
    background: #fffdf9 !important;
    color: #453b33 !important;
    font: inherit !important;
    font-size: 0.88rem !important;
}
body.role-handyman .handyman-completion-note-wrap {
    padding: 14px !important;
    border: 1px solid #e3d8ca !important;
    border-radius: 8px !important;
    background: #fffdf9 !important;
}
body.role-handyman .handyman-ticket-grid {
    display: grid !important;
    grid-template-columns: minmax(0, 1fr) minmax(300px, 0.36fr) !important;
    gap: 18px !important;
}
body.role-handyman .handyman-ticket-divider,
body.role-handyman .handyman-ticket-side-icon {
    display: none !important;
}
body.role-handyman .handyman-ticket-hero-actions,
body.role-handyman .staff-ticket-actions {
    display: flex !important;
    flex-wrap: wrap !important;
    gap: 8px !important;
    justify-content: flex-end !important;
}
body.role-handyman .staff-ticket-actions {
    align-items: center !important;
    margin-left: auto !important;
}
body.role-handyman .staff-ticket-actions form {
    display: flex !important;
    width: auto !important;
    margin: 0 !important;
}
body.role-handyman .staff-show-more-btn {
    align-self: flex-end !important;
    margin: 14px 16px 16px !important;
}
body.role-handyman :is(.staff-ticket-list, .staff-completed-list):has(+ .app-progressive-action) {
    margin-bottom: 0 !important;
    border-bottom-right-radius: 0 !important;
    border-bottom-left-radius: 0 !important;
}
body.role-handyman .staff-panel > .app-progressive-action {
    justify-content: center !important;
    margin: 0 22px 22px !important;
    padding: 12px 16px !important;
    border: 1px solid #e3d8ca !important;
    border-top: 0 !important;
    border-radius: 0 0 8px 8px !important;
    background: #fffdf9 !important;
}
body.role-handyman .staff-panel > .app-progressive-action .app-progressive-toggle {
    min-height: 38px !important;
    padding: 0 14px !important;
    border: 1px solid #d2ae7b !important;
    border-radius: 777px !important;
    background: #f3e3cc !important;
    color: #68400f !important;
    box-shadow: none !important;
    font-size: 0.72rem !important;
    font-weight: 800 !important;
    letter-spacing: 0.04em !important;
    text-transform: uppercase !important;
}
body.role-handyman .staff-panel > .app-progressive-action .app-progressive-toggle:hover {
    border-color: #c79745 !important;
    background: #ecd6b8 !important;
    transform: translateY(-1px) !important;
}
body.role-handyman .staff-empty-copy {
    margin: 0 22px 22px !important;
    padding: 24px !important;
    border: 1px dashed rgba(227, 216, 202, 0.92) !important;
    border-radius: 8px !important;
    background: #fffdf9 !important;
    color: #786b60 !important;
    text-align: center !important;
}
@media (max-width: 980px) {
    body.role-handyman .admin-overview-hero,
    body.role-handyman .handyman-ticket-hero {
        align-items: flex-start !important;
        flex-direction: column !important;
    }
    body.role-handyman .handyman-ticket-grid {
        grid-template-columns: 1fr !important;
    }
    body.role-handyman .staff-ticket-body {
        flex-direction: column !important;
        align-items: stretch !important;
    }
    body.role-handyman .staff-ticket-actions,
    body.role-handyman .handyman-ticket-hero-actions {
        justify-content: flex-start !important;
    }
}
@media (max-width: 640px) {
    body.role-handyman .staff-preview-card,
    body.role-handyman .staff-urgent-item,
    body.role-handyman .staff-completed-card {
        align-items: flex-start !important;
        flex-direction: column !important;
    }
    body.role-handyman :is(.staff-action-btn, .staff-preview-card a, .staff-urgent-item a, .staff-completed-card a, .handyman-ticket-btn) {
        width: 100% !important;
    }
}
</style>

<script>
    (function () {
        const storageKey = 'hallsync-theme';
        const root = document.documentElement;
        const role = @json($role);

        if (role === 'resident') {
            root.classList.remove('theme-light');
        }

        const syncThemeControls = () => {
            const isLight = root.classList.contains('theme-light');
            document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
                const label = isLight ? 'Switch to dark mode' : 'Switch to light mode';
                button.setAttribute('aria-label', label);
                button.setAttribute('title', label);
                button.querySelector('[data-theme-label]')?.replaceChildren(document.createTextNode(label));
            });
        };

        document.addEventListener('click', (event) => {
            const button = event.target.closest('[data-theme-toggle]');
            if (!button) {
                return;
            }

            if (role === 'resident') {
                root.classList.remove('theme-light');
                try {
                    localStorage.setItem(storageKey, 'dark');
                } catch (error) {}
                syncThemeControls();
                return;
            }

            const isLight = root.classList.toggle('theme-light');
            try {
                localStorage.setItem(storageKey, isLight ? 'light' : 'dark');
            } catch (error) {}
            syncThemeControls();
        });

        syncThemeControls();
        window.addEventListener('pageshow', syncThemeControls);
    })();

    const toastDurations = {
        success: 3000,
        info: 3000,
        warning: 5600,
        error: 5600,
    };
    const maxVisibleToasts = 3;

    const toastTypeFor = (toast) => {
        return ['success', 'info', 'warning', 'error'].find((type) => toast.classList.contains(`app-toast-${type}`)) || 'info';
    };

    const closeToast = (toast) => {
        if (!toast?.isConnected || toast.dataset.closing === 'true') {
            return;
        }

        toast.dataset.closing = 'true';
        window.clearTimeout(Number(toast.dataset.dismissTimer));
        toast.classList.add('is-leaving');
        window.setTimeout(() => toast.remove(), 240);
    };

    const makeRoomForToast = (stack) => {
        const visibleToasts = Array.from(stack.querySelectorAll('[data-toast]:not(.is-leaving)'));

        if (visibleToasts.length < maxVisibleToasts) {
            return;
        }

        closeToast(visibleToasts[0]);
    };

    const registerToast = (toast) => {
        const close = () => closeToast(toast);
        const closeButton = toast.querySelector('[data-toast-close]');
        const type = toastTypeFor(toast);
        const dismissTimer = window.setTimeout(close, toastDurations[type] ?? toastDurations.info);

        toast.dataset.dismissTimer = String(dismissTimer);

        closeButton?.addEventListener('click', (event) => {
            event.stopPropagation();
            close();
        });
        toast.addEventListener('click', close);
    };

    document.querySelectorAll('[data-toast]').forEach(registerToast);

    const normalizeToastType = (type, message) => {
        const safeType = ['success', 'error', 'info', 'warning'].includes(type) ? type : 'info';
        const isResident = document.body.classList.contains('role-resident');
        const isManagement = document.body.classList.contains('role-manager') || document.body.classList.contains('role-handyman');

        const destructiveResidentMessage = /\b(ticket|booking|post|comment|concern|request|reservation)\b.*\b(deleted|delete|removed|remove|cancelled|canceled|cancel|rejected|declined)\b|\b(deleted|delete|cancelled|canceled|rejected|declined)\b.*\b(ticket|booking|post|comment|concern|request|reservation)\b/i;
        const destructiveManagementMessage = /\b(user|account|resident|staff|admin|ticket|booking|post|comment|announcement|concern|request|reservation)\b.*\b(deleted|delete|deactivated|disabled|archived|removed|remove|move-out|cancelled|canceled|cancel|rejected|declined)\b|\b(deleted|delete|deactivated|disabled|archived|removed|remove|cancelled|canceled|rejected|declined)\b.*\b(user|account|resident|staff|admin|ticket|booking|post|comment|announcement|concern|request|reservation)\b|^deleted\s+/i;

        if (safeType === 'success'
            && ((isResident && destructiveResidentMessage.test(String(message)))
                || (isManagement && destructiveManagementMessage.test(String(message))))) {
            return 'warning';
        }

        return safeType;
    };

    const splitToastMessage = (message) => {
        const parts = String(message).match(/^(.+?[.!])(?:\s+(.+))$/);

        return {
            title: parts ? parts[1].replace(/[.!]+$/, '') : String(message).replace(/[.!]+$/, ''),
            detail: parts?.[2] || '',
        };
    };

    window.appToast = (type, message) => {
        const stack = document.querySelector('.app-toast-stack');

        if (!stack || !message) {
            return;
        }

        const toast = document.createElement('div');
        const icon = document.createElement('span');
        const copy = document.createElement('div');
        const title = document.createElement('strong');
        const detail = document.createElement('span');
        const closeButton = document.createElement('button');
        const safeType = normalizeToastType(type, message);

        toast.className = `app-toast app-toast-${safeType}`;
        toast.dataset.toast = '';
        icon.className = 'app-toast-icon';
        icon.setAttribute('aria-hidden', 'true');
        copy.className = 'app-toast-copy';

        if (safeType === 'success') {
            const parsed = splitToastMessage(message);
            title.textContent = parsed.title;
            detail.textContent = parsed.detail;
        } else if (safeType === 'warning') {
            title.textContent = 'Warning';
            detail.textContent = message;
        } else if (safeType === 'error') {
            title.textContent = 'Error';
            detail.textContent = message;
        } else {
            title.textContent = 'Notice';
            detail.textContent = message;
        }

        copy.append(title);
        if (detail.textContent) {
            copy.append(detail);
        }

        closeButton.type = 'button';
        closeButton.dataset.toastClose = '';
        closeButton.setAttribute('aria-label', 'Dismiss notification');
        closeButton.innerHTML = '&times;';
        toast.append(icon, copy, closeButton);
        makeRoomForToast(stack);
        stack.append(toast);
        registerToast(toast);
    };

    window.addEventListener('app:toast', (event) => {
        window.appToast(event.detail?.type, event.detail?.message);
    });

    window.addEventListener('load', () => {
        document.documentElement.classList.remove('is-loading');
    });

    document.querySelectorAll('[data-progressive-list]').forEach((list) => {
        const limit = Number.parseInt(list.dataset.progressiveLimit || '3', 10);
        const items = Array.from(list.querySelectorAll(':scope > [data-progressive-item]'));
        const extraItems = items.slice(limit);

        if (extraItems.length === 0) {
            return;
        }

        extraItems.forEach((item) => item.classList.add('app-progressive-hidden'));

        const action = document.createElement('div');
        const button = document.createElement('button');

        action.className = 'app-progressive-action';
        button.type = 'button';
        button.className = 'app-progressive-toggle';
        button.textContent = `Show more (${extraItems.length})`;
        button.setAttribute('aria-expanded', 'false');

        button.addEventListener('click', () => {
            const expanded = button.getAttribute('aria-expanded') === 'true';

            extraItems.forEach((item) => {
                item.classList.toggle('app-progressive-hidden', expanded);
            });

            button.setAttribute('aria-expanded', expanded ? 'false' : 'true');
            button.textContent = expanded ? `Show more (${extraItems.length})` : 'Show less';
        });

        action.append(button);
        list.insertAdjacentElement('afterend', action);
    });

    document.querySelectorAll('[data-filter-scope]').forEach((scope) => {
        const inputs = scope.querySelectorAll('[data-filter-input], [data-filter-select]');
        const cards = scope.querySelectorAll('[data-filter-card]');
        const empty = scope.querySelector('[data-filter-empty]');
        const moreLists = scope.querySelectorAll('.resident-more-list');
        const seeMoreButtons = scope.querySelectorAll('.resident-see-more-btn');

        const applyFilters = () => {
            const terms = {};
            let hasFilter = false;

            inputs.forEach((input) => {
                const key = input.dataset.filterKey || 'search';
                const value = input.value.trim().toLowerCase();
                terms[key] = value;
                hasFilter = hasFilter || value !== '';
            });

            let visibleCount = 0;

            moreLists.forEach((list) => {
                if (hasFilter) {
                    list.dataset.previousDisplay = list.style.display || 'none';
                    list.style.display = 'flex';
                } else if (list.dataset.previousDisplay) {
                    list.style.display = list.dataset.previousDisplay;
                    delete list.dataset.previousDisplay;
                }
            });

            seeMoreButtons.forEach((button) => {
                button.style.display = hasFilter ? 'none' : '';
            });

            cards.forEach((card) => {
                const matches = Object.entries(terms).every(([key, value]) => {
                    if (!value) {
                        return true;
                    }

                    const haystack = (card.dataset[key] || '').toLowerCase();
                    return haystack.includes(value);
                });

                card.style.display = matches ? '' : 'none';
                if (matches) {
                    visibleCount += 1;
                }
            });

            empty?.classList.toggle('is-visible', hasFilter && visibleCount === 0);
        };

        inputs.forEach((input) => {
            input.addEventListener('input', applyFilters);
            input.addEventListener('change', applyFilters);
        });
    });

    (function () {
        const modal = document.querySelector('[data-confirm-modal]');
        const messageNode = modal?.querySelector('[data-confirm-message]');
        const cancelButton = modal?.querySelector('[data-confirm-cancel]');
        const acceptButton = modal?.querySelector('[data-confirm-accept]');
        let pendingForm = null;

        if (!modal || !messageNode || !cancelButton || !acceptButton) {
            return;
        }

        const close = () => {
            modal.classList.remove('is-active');
            modal.setAttribute('aria-hidden', 'true');
            if (pendingForm) {
                delete pendingForm.dataset.submitting;
                delete pendingForm.dataset.confirmed;
            }
            pendingForm = null;
        };

        document.addEventListener('submit', (event) => {
            const form = event.target;

            const submitter = event.submitter instanceof HTMLElement ? event.submitter : null;
            const confirmMessage = submitter?.dataset.confirm || form.dataset.confirmMessage;

            if (!(form instanceof HTMLFormElement) || !confirmMessage || form.dataset.confirmed === 'true') {
                return;
            }

            event.preventDefault();
            pendingForm = form;
            messageNode.textContent = confirmMessage;
            modal.classList.add('is-active');
            modal.setAttribute('aria-hidden', 'false');
            acceptButton.focus();
        });

        cancelButton.addEventListener('click', close);
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                close();
            }
        });

        acceptButton.addEventListener('click', () => {
            if (!pendingForm) {
                close();
                return;
            }

            pendingForm.dataset.confirmed = 'true';
            pendingForm.requestSubmit();
            close();
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && modal.classList.contains('is-active')) {
                close();
            }
        });
    })();

    document.addEventListener('submit', function (event) {
        const form = event.target;

        if (!(form instanceof HTMLFormElement) || !form.hasAttribute('data-prevent-double-submit')) {
            return;
        }

        // If this form requires confirmation and hasn't been confirmed yet, the
        // confirm-dialog listener will intercept and prevent the submit — don't
        // start the "submitting" state until the user actually confirms.
        const submitter = event.submitter instanceof HTMLElement ? event.submitter : null;
        if ((form.dataset.confirmMessage || submitter?.dataset.confirm) && form.dataset.confirmed !== 'true') {
            return;
        }

        if (form.dataset.submitting === 'true') {
            event.preventDefault();
            return;
        }

        form.dataset.submitting = 'true';

        const submittingText = form.getAttribute('data-submitting-text') || 'Submitting...';
        const internalButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
        const externalButtons = form.id
            ? document.querySelectorAll(`button[type="submit"][form="${CSS.escape(form.id)}"], input[type="submit"][form="${CSS.escape(form.id)}"]`)
            : [];
        const submitButtons = [...internalButtons, ...externalButtons];

        submitButtons.forEach((button) => {
            if (!button.dataset.originalLabel) {
                button.dataset.originalLabel = button.tagName === 'INPUT' ? button.value : button.innerHTML;
            }

            button.disabled = true;
            button.setAttribute('aria-busy', 'true');

            if (button.tagName === 'INPUT') {
                button.value = submittingText;
            } else {
                const spinner = document.createElement('span');
                const label = document.createElement('span');
                spinner.className = 'app-button-spinner';
                spinner.setAttribute('aria-hidden', 'true');
                label.textContent = submittingText;
                button.replaceChildren(spinner, label);
                button.style.gap = '8px';
            }

            button.style.opacity = '0.7';
            button.style.cursor = 'not-allowed';
        });
    }, true);

    // When the browser restores a page from the back-forward cache (BFcache),
    // forms may still have disabled/spinner buttons from the previous submission.
    // Reset them so the user can submit again.
    window.addEventListener('pageshow', (event) => {
        if (!event.persisted) return;

        document.querySelectorAll('[data-prevent-double-submit]').forEach((form) => {
            delete form.dataset.submitting;
            delete form.dataset.confirmed;

            form.querySelectorAll('button[type="submit"], input[type="submit"]').forEach((button) => {
                button.disabled = !!button.dataset.bookingSubmit;
                button.removeAttribute('aria-busy');
                button.style.opacity = '';
                button.style.cursor = '';
                button.style.gap = '';

                if (button.dataset.originalLabel) {
                    if (button.tagName === 'INPUT') {
                        button.value = button.dataset.originalLabel;
                    } else {
                        button.innerHTML = button.dataset.originalLabel;
                    }
                    delete button.dataset.originalLabel;
                }
            });
        });
    });

    /* ── Page navigation progress bar ─────────────────────────────── */
    (function () {
        const bar = document.getElementById('app-nav-progress');
        if (!bar) return;

        let timer = null;

        const start = () => {
            clearTimeout(timer);
            bar.style.transition = 'width 10s cubic-bezier(0.1, 0.4, 0.8, 1)';
            bar.style.width = '80%';
            bar.style.opacity = '1';
        };

        const done = () => {
            clearTimeout(timer);
            bar.style.transition = 'width 0.18s ease, opacity 0.28s ease 0.18s';
            bar.style.width = '100%';
            timer = setTimeout(() => {
                bar.style.opacity = '0';
                timer = setTimeout(() => { bar.style.width = '0%'; }, 300);
            }, 180);
        };

        document.addEventListener('click', (e) => {
            const link = e.target.closest('a[href]');
            if (!link) return;
            const href = link.getAttribute('href');
            if (!href || href.startsWith('#') || href.startsWith('javascript') || link.target === '_blank' || e.ctrlKey || e.metaKey || e.shiftKey) return;
            start();
        });

        window.addEventListener('load', done);
        window.addEventListener('pageshow', done);
    })();

    /* ── Unsaved changes warning ───────────────────────────────────── */
    (function () {
        let dirty = false;

        document.addEventListener('input', (e) => {
            if (e.target.closest('form[data-unsaved-check]')) dirty = true;
        });

        document.addEventListener('change', (e) => {
            if (e.target.closest('form[data-unsaved-check]')) dirty = true;
        });

        document.addEventListener('submit', () => { dirty = false; });

        document.addEventListener('click', (e) => {
            if (e.target.closest('[data-unsaved-reset]')) dirty = false;
        });

        window.addEventListener('beforeunload', (e) => {
            if (!dirty) return;
            e.preventDefault();
            e.returnValue = '';
        });
    })();

    /* ── Auto-dismiss inline banners ──────────────────────────────── */
    document.querySelectorAll('[data-auto-dismiss]').forEach((el) => {
        const delay = Number(el.dataset.dismissDelay ?? 4800);
        setTimeout(() => {
            el.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            el.style.opacity = '0';
            el.style.transform = 'translateY(-6px)';
            setTimeout(() => el.remove(), 420);
        }, delay);
    });
</script>
@include('layouts.partials.validation-bubble')
</body>
</html>

