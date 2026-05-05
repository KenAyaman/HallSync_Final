<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'HallSync') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
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
            background:
                radial-gradient(circle at top right, rgba(88, 135, 165, 0.14), transparent 26%),
                radial-gradient(circle at bottom left, rgba(214, 168, 91, 0.08), transparent 30%),
                linear-gradient(180deg, #1a242b 0%, #1f2b33 42%, #24323a 100%);
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
            background:
                radial-gradient(circle at top right, rgba(214, 168, 91, 0.11), transparent 28%),
                radial-gradient(circle at bottom left, rgba(120, 100, 75, 0.08), transparent 34%),
                linear-gradient(180deg, var(--admin-bg-top) 0%, var(--admin-bg-mid) 48%, var(--admin-bg-bottom) 100%);
        }


        * {
            box-sizing: border-box;
        }

        /* GLOBAL BACKGROUND GLOW */
        .global-bg-glow {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            background:
                radial-gradient(circle at 85% 5%, rgba(214, 168, 91, 0.10), transparent 30%),
                radial-gradient(circle at 10% 90%, rgba(196, 154, 108, 0.08), transparent 32%);
        }

        body.role-resident .global-bg-glow,
        body.role-guest .global-bg-glow {
            background:
                radial-gradient(circle at 85% 5%, rgba(214, 168, 91, 0.10), transparent 30%),
                radial-gradient(circle at 10% 90%, rgba(196, 154, 108, 0.08), transparent 32%);
        }

        body.role-handyman .global-bg-glow {
            background:
                radial-gradient(circle at 84% 8%, rgba(88, 135, 165, 0.14), transparent 28%),
                radial-gradient(circle at 14% 88%, rgba(214, 168, 91, 0.08), transparent 30%);
        }

        body.role-manager .global-bg-glow {
            background:
                radial-gradient(circle at 84% 8%, rgba(214, 168, 91, 0.10), transparent 30%),
                radial-gradient(circle at 12% 86%, rgba(120, 100, 75, 0.07), transparent 34%),
                radial-gradient(circle at 50% 0%, rgba(255,255,255,0.42), transparent 28%);
        }

        /* TOP HERO IMAGE */
        .top-bg-image-layer {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 360px;
            pointer-events: none;
            z-index: 1;
            background-image: url('{{ asset('1.1.png') }}');
            background-repeat: no-repeat;
            background-position: top center;
            background-size: 100% auto;
            opacity: 0.9;

            mask-image:
                linear-gradient(to bottom, black 40%, transparent 100%),
                linear-gradient(to right, transparent 0%, black 40%);
            mask-composite: intersect;
        }

        .top-bg-image-layer::after {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(90deg, rgba(19, 16, 13, 0.76) 0%, rgba(19, 16, 13, 0.54) 32%, rgba(19, 16, 13, 0.18) 62%, rgba(19, 16, 13, 0) 100%);
        }

        body.role-resident .top-bg-image-layer {
            background-image: url('{{ asset('1.1.png') }}');
            background-repeat: no-repeat;
            background-position: top center;
            background-size: 100% auto;
            opacity: 0.9;
        }

        body.role-resident .top-bg-image-layer::after {
            background:
                linear-gradient(90deg, rgba(19, 16, 13, 0.76) 0%, rgba(19, 16, 13, 0.54) 32%, rgba(19, 16, 13, 0.18) 62%, rgba(19, 16, 13, 0) 100%);
        }

        html.theme-light body.role-resident {
            background:
                radial-gradient(circle at 82% 4%, rgba(255,255,255,0.45), transparent 24%),
                linear-gradient(180deg, #f7efe5 0%, #f4eadc 48%, #ead9c2 100%);
        }

        html.theme-light body.role-resident .top-bg-image-layer {
            height: 540px;
            background-image: url('{{ asset('White1.jpg') }}');
            background-repeat: no-repeat;
            background-position: top center;
            background-size: auto 100%;
            opacity: 0.92;
            filter: saturate(1.02) contrast(1);
            mask-image:
                linear-gradient(to bottom, black 42%, transparent 100%),
                linear-gradient(to right, transparent 0%, black 34%);
            mask-composite: intersect;
            -webkit-mask-image:
                linear-gradient(to bottom, black 42%, transparent 100%),
                linear-gradient(to right, transparent 0%, black 34%);
            -webkit-mask-composite: source-in;
        }

        html.theme-light body.role-resident .top-bg-image-layer::after {
            background:
                linear-gradient(180deg, rgba(251,247,240,0.08) 0%, rgba(251,247,240,0.28) 58%, rgba(251,247,240,0.92) 100%),
                linear-gradient(90deg, rgba(251,247,240,0.88) 0%, rgba(251,247,240,0.58) 32%, rgba(251,247,240,0.16) 66%, rgba(251,247,240,0) 100%);
        }

        body.role-guest .top-bg-image-layer {
            background-image: url('{{ asset('1.1.png') }}');
            background-repeat: no-repeat;
            background-position: top center;
            background-size: 100% auto;
            opacity: 0.9;
        }

        body.role-guest .top-bg-image-layer::after {
            background:
                linear-gradient(90deg, rgba(19, 16, 13, 0.76) 0%, rgba(19, 16, 13, 0.54) 32%, rgba(19, 16, 13, 0.18) 62%, rgba(19, 16, 13, 0) 100%);
        }

        body.role-handyman .top-bg-image-layer {
            height: 420px;
            background-image:
                radial-gradient(circle at top right, rgba(214,168,91,0.12), transparent 24%),
                linear-gradient(135deg, rgba(31,42,49,0.82) 0%, rgba(36,49,57,0.78) 38%, rgba(42,57,66,0.70) 68%, rgba(33,46,54,0.84) 100%);
            background-repeat: no-repeat;
            background-position: top center;
            background-size: cover;
            opacity: 1;
            mask-image: linear-gradient(to bottom, black 42%, transparent 100%);
        }

        body.role-handyman .top-bg-image-layer::after {
            background:
                linear-gradient(90deg, rgba(28, 38, 45, 0.62) 0%, rgba(28, 38, 45, 0.34) 36%, rgba(28, 38, 45, 0.10) 64%, rgba(28, 38, 45, 0) 100%);
        }

        body.role-manager .top-bg-image-layer {
            height: 430px;
            background-image:
                radial-gradient(circle at 78% 20%, rgba(214,168,91,0.12), transparent 24%),
                radial-gradient(circle at 16% 8%, rgba(120,100,75,0.09), transparent 26%),
                linear-gradient(135deg, rgba(246,241,234,0.96) 0%, rgba(232,223,211,0.90) 42%, rgba(244,238,230,0.86) 70%, rgba(250,247,242,0.96) 100%);
            background-repeat: no-repeat;
            background-position: top center;
            background-size: cover;
            opacity: 1;
            mask-image: linear-gradient(to bottom, black 44%, transparent 100%);
        }

        body.role-manager .top-bg-image-layer::after {
            background:
                linear-gradient(90deg, rgba(244,238,230,0.52) 0%, rgba(244,238,230,0.28) 38%, rgba(244,238,230,0.08) 66%, rgba(244,238,230,0) 100%);
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
            background:
                radial-gradient(circle at 18% 8%, rgba(214,168,91,0.10), transparent 24%),
                radial-gradient(circle at 82% 16%, rgba(120,100,75,0.08), transparent 26%),
                linear-gradient(var(--admin-grid-line) 1px, transparent 1px),
                linear-gradient(90deg, var(--admin-grid-line-soft) 1px, transparent 1px);
            background-size: auto, auto, 128px 128px, 128px 128px;
            mask-image: linear-gradient(to bottom, rgba(0,0,0,0.9), transparent 58%);
        }

        .admin-content-shell {
            width: 100%;
            max-width: 1580px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        body.role-manager .admin-content-shell {
            color: var(--admin-ink);
        }

        body.role-manager .admin-content-shell > .space-y-6 h1,
        body.role-manager .admin-content-shell > .space-y-8 h1,
        body.role-manager .admin-content-shell > .space-y-6 > div:not([style*="background"]) h2,
        body.role-manager .admin-content-shell > .space-y-8 > div:not([style*="background"]) h2 {
            color: var(--admin-ink) !important;
        }

        body.role-manager .admin-content-shell > .space-y-6 p,
        body.role-manager .admin-content-shell > .space-y-8 p,
        body.role-manager .admin-content-shell > .space-y-6 .text-gray-400,
        body.role-manager .admin-content-shell > .space-y-8 .text-gray-400,
        body.role-manager .admin-content-shell > .space-y-6 .text-gray-500,
        body.role-manager .admin-content-shell > .space-y-8 .text-gray-500 {
            color: var(--admin-ink-soft) !important;
        }

        body.role-manager .admin-content-shell > .space-y-6 > div[style*="background: #1F2023"],
        body.role-manager .admin-content-shell > .space-y-6 > div[style*="background:#1F2023"],
        body.role-manager .admin-content-shell > .space-y-6 > div[style*="background: #2A2C30"],
        body.role-manager .admin-content-shell > .space-y-6 > div[style*="background:#2A2C30"],
        body.role-manager .admin-content-shell > .space-y-8 > div[style*="background: #1F2023"],
        body.role-manager .admin-content-shell > .space-y-8 > div[style*="background:#1F2023"],
        body.role-manager .admin-content-shell > .space-y-8 > div[style*="background: #2A2C30"],
        body.role-manager .admin-content-shell > .space-y-8 > div[style*="background:#2A2C30"] {
            background: linear-gradient(180deg, rgba(43,42,39,0.95) 0%, rgba(31,31,29,0.95) 100%) !important;
            border-color: rgba(214,168,91,0.18) !important;
            box-shadow: 0 18px 36px rgba(72,48,24,0.16);
        }

        body.role-manager .admin-content-shell > .space-y-6 input,
        body.role-manager .admin-content-shell > .space-y-6 select,
        body.role-manager .admin-content-shell > .space-y-6 textarea,
        body.role-manager .admin-content-shell > .space-y-8 input,
        body.role-manager .admin-content-shell > .space-y-8 select,
        body.role-manager .admin-content-shell > .space-y-8 textarea {
            background: rgba(48,45,40,0.95) !important;
            border-color: rgba(214,168,91,0.20) !important;
            color: #f8f3ea !important;
        }

        body.role-manager .admin-content-shell > .space-y-6 table .text-white,
        body.role-manager .admin-content-shell > .space-y-8 table .text-white {
            color: #f8f3ea !important;
        }

        body.role-manager .admin-content-shell > .space-y-6 > div:not([style*="background"]) .text-white,
        body.role-manager .admin-content-shell > .space-y-8 > div:not([style*="background"]) .text-white,
        body.role-manager .admin-content-shell > .space-y-6 > div:not([style*="background"]) .text-gray-300,
        body.role-manager .admin-content-shell > .space-y-8 > div:not([style*="background"]) .text-gray-300 {
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
        @media (min-width: 768px) {
            .app-main,
            .admin-main-content {
                padding: 28px 40px 56px;
            }

            .app-main.app-main-handyman {
                padding-top: 20px;
            }

            .top-bg-image-layer {
                height: 520px;
            }
        }

        @media (min-width: 1024px) {
            .app-main,
            .admin-main-content {
                padding: 32px 64px 64px;
            }

            .app-main.app-main-handyman {
                padding-top: 22px;
            }
        }

        @media (max-width: 768px) {
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
            text-shadow: 0 8px 20px rgba(0,0,0,0.16);
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
            border: 1px solid rgba(255,255,255,0.09);
            backdrop-filter: blur(18px);
            box-shadow: 0 18px 42px rgba(0,0,0,0.22);
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
            background: rgba(255,255,255,0.06);
        }

        .role-nav-link.is-active {
            background: linear-gradient(180deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.06) 100%);
            color: #fff6e7;
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.05);
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
            border: 1px solid rgba(255,255,255,0.08);
            color: var(--text-heading);
            backdrop-filter: blur(18px);
            text-decoration: none;
            box-shadow: 0 16px 34px rgba(0,0,0,0.2);
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
            border: 1px solid rgba(255,255,255,0.08);
            color: var(--text-heading);
            backdrop-filter: blur(18px);
            box-shadow: 0 16px 34px rgba(0,0,0,0.2);
            cursor: pointer;
            transition: transform 0.2s ease, background 0.2s ease, border-color 0.2s ease;
        }

        .role-theme-toggle:hover {
            transform: translateY(-1px);
            background: rgba(255,255,255,0.06);
        }

        .role-theme-icon {
            width: 19px;
            height: 19px;
        }

        .role-theme-icon-moon,
        html.theme-light .role-theme-icon-sun {
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
            border: 1px solid rgba(255,255,255,0.08);
            color: var(--text-heading);
            backdrop-filter: blur(18px);
            box-shadow: 0 16px 34px rgba(0,0,0,0.2);
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
            background: linear-gradient(135deg, #c79745 0%, #d6a85b 100%);
            color: #1a1714;
            font-size: 0.72rem;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 20px rgba(199, 151, 69, 0.28);
        }

        .role-notification-panel {
            position: absolute;
            top: calc(100% + 12px);
            right: 0;
            width: min(360px, 90vw);
            padding: 14px;
            border-radius: 20px;
            background: rgba(24, 21, 18, 0.96);
            border: 1px solid rgba(255,255,255,0.08);
            box-shadow: 0 18px 40px rgba(0,0,0,0.24);
        }

        .role-notification-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }

        .role-notification-head strong {
            color: #f0e9df;
            font-size: 0.95rem;
        }

        .role-notification-head span,
        .role-notification-item small,
        .role-mobile-notification-head span {
            color: #9f927f;
            font-size: 0.8rem;
        }

        .role-notification-list {
            display: grid;
            gap: 10px;
        }

        .role-notification-item,
        .role-mobile-notification-item {
            display: block;
            padding: 12px 14px;
            border-radius: 16px;
            text-decoration: none;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.05);
        }

        .role-notification-item:hover,
        .role-mobile-notification-item:hover {
            background: rgba(255,255,255,0.05);
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

        .role-notification-item strong,
        .role-mobile-notification-item strong {
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

        .role-notification-empty,
        .role-mobile-notification-empty {
            padding: 12px 14px;
            border-radius: 16px;
            background: rgba(255,255,255,0.03);
            border: 1px dashed rgba(214,168,91,0.18);
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
            border: 1px solid rgba(255,255,255,0.08);
            color: #f0e9df;
            font-size: 0.8rem;
            font-weight: 600;
            opacity: 0;
            pointer-events: none;
            transform: translateY(-4px);
            transition: 0.18s ease;
            box-shadow: 0 16px 34px rgba(0,0,0,0.22);
        }

        .role-user-chip:hover .role-user-tooltip,
        .role-user-chip:focus-visible .role-user-tooltip {
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
            border: 1px solid rgba(255,255,255,0.1);
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
            border-color: rgba(214,168,91,0.25);
        }

        .role-mobile-toggle {
            display: none;
            width: 44px;
            height: 44px;
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,0.1);
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
            border: 1px solid rgba(255,255,255,0.08);
            box-shadow: 0 18px 40px rgba(0,0,0,0.24);
        }

        .role-mobile-panel .role-nav-link,
        .role-mobile-panel .role-action-btn {
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
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.05);
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

        @media (max-width: 980px) {
            .role-nav-shell,
            .role-topbar-actions.auth-only-desktop {
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

<div class="global-bg-glow"></div>

@if($showHero)
    <div class="top-bg-image-layer"></div>
@endif

@auth
    @if(Auth::user()->role === 'manager')
        @include('layouts.admin-nav')
        <main class="admin-main-content">
            <div class="admin-content-shell {{ $isDashboard ? 'dashboard-shell' : '' }}">
                {{ $slot }}
            </div>
        </main>

    @elseif(Auth::user()->role === 'handyman')
        @include('layouts.handyman-nav')
        <main class="app-main app-main-handyman">
            {{ $slot }}
        </main>

    @else
        @include('layouts.navigation')
        <main class="app-main {{ $isDashboard ? 'full-bleed' : '' }}">
            {{ $slot }}
        </main>
    @endif
@else
    @include('layouts.navigation')
    <main class="app-main">
        {{ $slot }}
    </main>
@endauth

@php
    $toastMessages = collect([
        session('success') ? ['type' => 'success', 'message' => session('success')] : null,
        session('error') ? ['type' => 'error', 'message' => session('error')] : null,
        session('status') ? ['type' => 'info', 'message' => session('status')] : null,
    ])->filter();
@endphp

<div class="app-loading-overlay" data-loading-overlay aria-hidden="true">
    <div class="app-loading-panel">
        <div class="app-skeleton-line app-skeleton-line-title"></div>
        <div class="app-skeleton-line"></div>
        <div class="app-skeleton-grid">
            <span></span><span></span><span></span>
        </div>
    </div>
</div>

<div class="app-toast-stack" aria-live="polite" aria-atomic="true">
    @foreach($toastMessages as $toast)
        <div class="app-toast app-toast-{{ $toast['type'] }}" data-toast>
            <span class="app-toast-icon" aria-hidden="true"></span>
            <p>{{ $toast['message'] }}</p>
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
    html.theme-light body.role-resident .role-brand-title,
    html.theme-light body.role-resident .role-notification-head strong,
    html.theme-light body.role-resident .role-notification-item strong,
    html.theme-light body.role-resident .role-mobile-notification-head strong,
    html.theme-light body.role-resident .role-mobile-notification-item strong {
        color: #2f271f;
    }

    html.theme-light body.role-resident .role-brand-subtitle,
    html.theme-light body.role-resident .role-mobile-notification-item span,
    html.theme-light body.role-resident .role-notification-empty,
    html.theme-light body.role-resident .role-mobile-notification-empty {
        color: #766856;
    }

    html.theme-light body.role-resident .role-nav-shell,
    html.theme-light body.role-resident .role-action-btn,
    html.theme-light body.role-resident .role-theme-toggle,
    html.theme-light body.role-resident .role-user-chip,
    html.theme-light body.role-resident .role-notification-btn,
    html.theme-light body.role-resident .role-mobile-toggle,
    html.theme-light body.role-resident .role-mobile-panel,
    html.theme-light body.role-resident .role-notification-panel {
        background: rgba(255, 253, 248, 0.86);
        border-color: rgba(76, 62, 46, 0.14);
        color: #40352a;
        box-shadow: 0 18px 36px rgba(94, 73, 45, 0.12);
    }

    html.theme-light body.role-resident .role-nav-link {
        color: #5c4b3b;
    }

    html.theme-light body.role-resident .role-nav-link:hover,
    html.theme-light body.role-resident .role-nav-link.is-active {
        background: rgba(185, 130, 47, 0.13);
        color: #2f271f;
    }

    html.theme-light body.role-resident .role-action-btn:hover {
        background: #ffffff;
        color: #2f271f;
    }

    html.theme-light body.role-resident .role-notification-item,
    html.theme-light body.role-resident .role-mobile-notification-block,
    html.theme-light body.role-resident .role-mobile-notification-item {
        background: rgba(80, 62, 42, 0.045);
        border-color: rgba(76, 62, 46, 0.10);
    }

    html.theme-light body.role-resident .resident-dashboard-shell,
    html.theme-light body.role-resident .resident-page,
    html.theme-light body.role-resident .resident-ticket-create-page,
    html.theme-light body.role-resident .resident-booking-create-page,
    html.theme-light body.role-resident .community-feed-page,
    html.theme-light body.role-resident .concern-page {
        color: #2f271f;
    }

    html.theme-light body.role-resident .resident-page-hero,
    html.theme-light body.role-resident .resident-ticket-create-hero,
    html.theme-light body.role-resident .resident-booking-create-hero,
    html.theme-light body.role-resident .community-feed-hero,
    html.theme-light body.role-resident .concern-hero {
        background:
            linear-gradient(120deg, rgba(255,253,248,0.94) 0%, rgba(248,240,229,0.94) 54%, rgba(235,222,205,0.92) 100%) !important;
        border-color: rgba(76,62,46,0.16) !important;
        box-shadow: 0 18px 38px rgba(94,73,45,0.13) !important;
    }

    html.theme-light body.role-resident .resident-activity-card,
    html.theme-light body.role-resident .resident-surface-panel,
    html.theme-light body.role-resident .resident-page-panel,
    html.theme-light body.role-resident .resident-ticket-create-panel,
    html.theme-light body.role-resident .resident-booking-create-panel,
    html.theme-light body.role-resident .community-review-strip,
    html.theme-light body.role-resident .community-composer-card,
    html.theme-light body.role-resident .community-feed-card,
    html.theme-light body.role-resident .community-empty-state,
    html.theme-light body.role-resident .concern-card {
        background: rgba(255,253,248,0.88) !important;
        border-color: rgba(76,62,46,0.13) !important;
        box-shadow: 0 14px 30px rgba(94,73,45,0.10) !important;
    }

    html.theme-light body.role-resident .resident-stack-item,
    html.theme-light body.role-resident .resident-notice-card,
    html.theme-light body.role-resident .resident-community-entry,
    html.theme-light body.role-resident .resident-card,
    html.theme-light body.role-resident .resident-meta-box,
    html.theme-light body.role-resident .resident-empty-state,
    html.theme-light body.role-resident .resident-ticket-upload-panel,
    html.theme-light body.role-resident .resident-ticket-priority-card,
    html.theme-light body.role-resident .resident-booking-slot,
    html.theme-light body.role-resident .community-review-card,
    html.theme-light body.role-resident .community-composer-trigger,
    html.theme-light body.role-resident .community-action-btn {
        background: rgba(80,62,42,0.045) !important;
        border-color: rgba(76,62,46,0.11) !important;
        color: #3a3026 !important;
    }

    html.theme-light body.role-resident .resident-home-kicker,
    html.theme-light body.role-resident .resident-page-kicker,
    html.theme-light body.role-resident .resident-ticket-create-kicker,
    html.theme-light body.role-resident .resident-booking-create-kicker,
    html.theme-light body.role-resident .community-feed-kicker,
    html.theme-light body.role-resident .concern-kicker,
    html.theme-light body.role-resident .resident-page-eyebrow,
    html.theme-light body.role-resident .resident-booking-create-eyebrow,
    html.theme-light body.role-resident .resident-ticket-create-chip,
    html.theme-light body.role-resident .concern-badge,
    html.theme-light body.role-resident .community-review-actions a,
    html.theme-light body.role-resident .community-review-actions button,
    html.theme-light body.role-resident .resident-card-links a,
    html.theme-light body.role-resident .resident-card-links button,
    html.theme-light body.role-resident .resident-see-more-btn,
    html.theme-light body.role-resident .resident-surface-head a,
    html.theme-light body.role-resident .resident-empty-state a,
    html.theme-light body.role-resident .community-empty-state a {
        color: #9b641d !important;
    }

    html.theme-light body.role-resident .resident-home-title,
    html.theme-light body.role-resident .resident-section-title,
    html.theme-light body.role-resident .resident-surface-head h2,
    html.theme-light body.role-resident .resident-surface-head h3,
    html.theme-light body.role-resident .resident-stack-item-row h3,
    html.theme-light body.role-resident .resident-notice-title h3,
    html.theme-light body.role-resident .resident-community-entry h3,
    html.theme-light body.role-resident .resident-page-title,
    html.theme-light body.role-resident .resident-page-panel-head h2,
    html.theme-light body.role-resident .resident-card-heading h3,
    html.theme-light body.role-resident .resident-day-heading,
    html.theme-light body.role-resident .resident-ticket-section-head h3,
    html.theme-light body.role-resident .resident-ticket-create-title,
    html.theme-light body.role-resident .resident-ticket-create-panel-head h2,
    html.theme-light body.role-resident .resident-ticket-upload-head h3,
    html.theme-light body.role-resident .resident-ticket-priority-name,
    html.theme-light body.role-resident .resident-booking-create-title,
    html.theme-light body.role-resident .resident-booking-create-head h2,
    html.theme-light body.role-resident .community-feed-title,
    html.theme-light body.role-resident .community-section-head h2,
    html.theme-light body.role-resident .community-review-top h3,
    html.theme-light body.role-resident .community-feed-copy h3,
    html.theme-light body.role-resident .community-feed-author-copy strong,
    html.theme-light body.role-resident .concern-title,
    html.theme-light body.role-resident .concern-card-head h2 {
        color: #2f271f !important;
    }

    html.theme-light body.role-resident .resident-home-subtitle,
    html.theme-light body.role-resident .resident-page-subtitle,
    html.theme-light body.role-resident .resident-ticket-create-subtitle,
    html.theme-light body.role-resident .resident-booking-create-subtitle,
    html.theme-light body.role-resident .community-feed-subtitle,
    html.theme-light body.role-resident .concern-subtitle,
    html.theme-light body.role-resident .resident-surface-head p,
    html.theme-light body.role-resident .resident-stack-item-row p,
    html.theme-light body.role-resident .resident-notice-card p,
    html.theme-light body.role-resident .resident-community-entry p,
    html.theme-light body.role-resident .resident-card-description,
    html.theme-light body.role-resident .resident-page-panel-head p,
    html.theme-light body.role-resident .resident-ticket-create-panel-head p,
    html.theme-light body.role-resident .resident-ticket-priority-copy,
    html.theme-light body.role-resident .resident-ticket-upload-head p,
    html.theme-light body.role-resident .resident-ticket-upload-note,
    html.theme-light body.role-resident .resident-booking-create-head p,
    html.theme-light body.role-resident .resident-booking-create-help,
    html.theme-light body.role-resident .community-section-head p,
    html.theme-light body.role-resident .community-review-top p,
    html.theme-light body.role-resident .community-feed-copy p,
    html.theme-light body.role-resident .community-feed-author-copy span,
    html.theme-light body.role-resident .community-feed-stats-row,
    html.theme-light body.role-resident .concern-card-head p {
        color: #655747 !important;
    }

    html.theme-light body.role-resident .resident-hero-stat,
    html.theme-light body.role-resident .resident-booking-create-stat,
    html.theme-light body.role-resident .community-feed-stat {
        background: rgba(255,255,255,0.62) !important;
        border-color: rgba(76,62,46,0.10) !important;
    }

    html.theme-light body.role-resident .resident-hero-stat span,
    html.theme-light body.role-resident .resident-booking-create-stat span,
    html.theme-light body.role-resident .community-feed-stat span,
    html.theme-light body.role-resident .resident-meta-box span,
    html.theme-light body.role-resident .resident-stack-meta,
    html.theme-light body.role-resident .resident-community-entry-time,
    html.theme-light body.role-resident .community-review-meta {
        color: #806f5c !important;
    }

    html.theme-light body.role-resident .resident-hero-stat strong,
    html.theme-light body.role-resident .resident-booking-create-stat strong,
    html.theme-light body.role-resident .community-feed-stat strong,
    html.theme-light body.role-resident .resident-meta-box strong {
        color: #2f271f !important;
    }

    html.theme-light body.role-resident .resident-ticket-create-input,
    html.theme-light body.role-resident .resident-ticket-create-input-file,
    html.theme-light body.role-resident .resident-booking-create-input,
    html.theme-light body.role-resident .concern-input {
        background: rgba(255,255,255,0.86) !important;
        border-color: rgba(76,62,46,0.16) !important;
        color: #2f271f !important;
    }

    html.theme-light body.role-resident .resident-ticket-create-label,
    html.theme-light body.role-resident .resident-booking-create-label,
    html.theme-light body.role-resident .concern-label {
        color: #574839 !important;
    }

    html.theme-light body.role-resident .resident-home-btn-secondary,
    html.theme-light body.role-resident .resident-ticket-create-btn-secondary,
    html.theme-light body.role-resident .resident-booking-create-btn-secondary,
    html.theme-light body.role-resident .concern-btn-secondary,
    html.theme-light body.role-resident .community-composer-secondary-action {
        background: rgba(255,255,255,0.74) !important;
        border-color: rgba(76,62,46,0.14) !important;
        color: #40352a !important;
    }

    html.theme-light body.role-resident .resident-status-chip-completed,
    html.theme-light body.role-resident .resident-badge-status-approved,
    html.theme-light body.role-resident .resident-badge-status-completed,
    html.theme-light body.role-resident .resident-badge-priority-low {
        background: rgba(76,128,76,0.13) !important;
        color: #426f3d !important;
    }

    html.theme-light body.role-resident .resident-badge-status-rejected,
    html.theme-light body.role-resident .resident-badge-status-cancelled,
    html.theme-light body.role-resident .resident-badge-priority-critical,
    html.theme-light body.role-resident .community-status-chip-rejected {
        background: rgba(172,70,54,0.12) !important;
        color: #9b3e31 !important;
    }

    html.theme-light body.role-resident .resident-status-chip-in_progress,
    html.theme-light body.role-resident .resident-badge-status-assigned,
    html.theme-light body.role-resident .resident-badge-status-in_progress {
        background: rgba(57,111,136,0.13) !important;
        color: #336b83 !important;
    }

    @media (max-width: 1200px) and (min-width: 981px) {
        .role-nav-link {
            padding-inline: 13px;
            font-size: 0.86rem;
        }
    }

    body.role-resident .resident-page-hero,
    body.role-resident .resident-ticket-create-hero,
    body.role-resident .resident-booking-create-hero,
    body.role-resident .community-feed-hero,
    body.role-resident .concern-hero {
        padding: 20px 24px !important;
        border-radius: 24px !important;
        min-height: 0 !important;
        align-items: center !important;
    }

    body.role-resident .resident-page-title,
    body.role-resident .resident-ticket-create-title,
    body.role-resident .resident-booking-create-title,
    body.role-resident .community-feed-title,
    body.role-resident .concern-title {
        font-size: clamp(1.9rem, 3vw, 2.65rem) !important;
        line-height: 1.08 !important;
    }

    body.role-resident .resident-page-kicker,
    body.role-resident .resident-ticket-create-kicker,
    body.role-resident .resident-booking-create-kicker,
    body.role-resident .community-feed-kicker,
    body.role-resident .concern-kicker {
        margin-bottom: 7px !important;
        letter-spacing: 0.18em !important;
    }

    body.role-resident .resident-page-subtitle,
    body.role-resident .resident-ticket-create-subtitle,
    body.role-resident .resident-booking-create-subtitle,
    body.role-resident .community-feed-subtitle,
    body.role-resident .concern-subtitle {
        margin-top: 8px !important;
        max-width: 700px !important;
        font-size: 0.96rem !important;
        line-height: 1.55 !important;
    }

    body.role-resident .resident-hero-stat-row,
    body.role-resident .resident-booking-create-stats,
    body.role-resident .community-feed-stats {
        margin-top: 14px !important;
        gap: 10px !important;
    }

    body.role-resident .resident-hero-stat,
    body.role-resident .resident-booking-create-stat,
    body.role-resident .community-feed-stat {
        min-width: 96px !important;
        padding: 9px 12px !important;
        border-radius: 12px !important;
    }

    body.role-resident .resident-hero-stat strong,
    body.role-resident .community-feed-stat strong {
        font-size: 1.05rem !important;
    }

    body.role-resident .resident-page-btn,
    body.role-resident .resident-ticket-create-btn,
    body.role-resident .resident-booking-create-btn,
    body.role-resident .concern-btn {
        padding: 11px 18px !important;
    }

    body.role-resident .resident-ticket-create-page,
    body.role-resident .resident-booking-create-page,
    body.role-resident .resident-page,
    body.role-resident .community-feed-page,
    body.role-resident .concern-page {
        gap: 18px !important;
    }

    @media (max-width: 768px) {
        body.role-resident .resident-page-hero,
        body.role-resident .resident-ticket-create-hero,
        body.role-resident .resident-booking-create-hero,
        body.role-resident .community-feed-hero,
        body.role-resident .concern-hero {
            padding: 18px !important;
            border-radius: 20px !important;
            align-items: flex-start !important;
        }

        body.role-resident .resident-page-title,
        body.role-resident .resident-ticket-create-title,
        body.role-resident .resident-booking-create-title,
        body.role-resident .community-feed-title,
        body.role-resident .concern-title {
            font-size: clamp(1.75rem, 8vw, 2.25rem) !important;
        }
    }
    }

    .app-toast-stack {
        position: fixed;
        right: 18px;
        bottom: 18px;
        z-index: 120;
        display: grid;
        gap: 12px;
        width: min(380px, calc(100vw - 32px));
        pointer-events: none;
    }

    .app-toast {
        display: grid;
        grid-template-columns: 14px 1fr auto;
        align-items: center;
        gap: 12px;
        padding: 14px 16px;
        border-radius: 18px;
        border: 1px solid rgba(255,255,255,0.08);
        background: rgba(24,21,18,0.96);
        color: #f0e9df;
        box-shadow: 0 18px 40px rgba(0,0,0,0.24);
        pointer-events: auto;
        transform: translateY(8px);
        opacity: 0;
        animation: toast-in 0.24s ease forwards;
    }

    .app-toast p {
        margin: 0;
        color: inherit;
        font-size: 0.92rem;
        line-height: 1.45;
        font-weight: 650;
    }

    .app-toast button {
        border: none;
        background: transparent;
        color: inherit;
        cursor: pointer;
        font-size: 1.1rem;
        line-height: 1;
        opacity: 0.72;
    }

    .app-toast-icon {
        width: 12px;
        height: 12px;
        border-radius: 999px;
        background: #d6a85b;
        box-shadow: 0 0 0 5px rgba(214,168,91,0.12);
    }

    .app-toast-success .app-toast-icon {
        background: #8ab879;
        box-shadow: 0 0 0 5px rgba(138,184,121,0.14);
    }

    .app-toast-error .app-toast-icon {
        background: #dc7868;
        box-shadow: 0 0 0 5px rgba(220,120,104,0.14);
    }

    .app-toast.is-leaving {
        animation: toast-out 0.22s ease forwards;
    }

    @keyframes toast-in {
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes toast-out {
        to { opacity: 0; transform: translateY(8px); }
    }

    .app-loading-overlay {
        position: fixed;
        inset: 0;
        z-index: 115;
        display: none;
        align-items: flex-start;
        justify-content: center;
        padding: 120px 18px 0;
        background: rgba(18, 15, 12, 0.28);
        backdrop-filter: blur(4px);
        pointer-events: none;
    }

    .app-loading-overlay.is-active {
        display: flex;
    }

    .app-loading-panel {
        width: min(540px, 100%);
        padding: 22px;
        border-radius: 22px;
        border: 1px solid rgba(214,168,91,0.16);
        background: rgba(30,27,23,0.94);
        box-shadow: 0 24px 60px rgba(0,0,0,0.26);
    }

    .app-skeleton-line,
    .app-skeleton-grid span,
    .feature-skeleton-line,
    .feature-skeleton-pill,
    .feature-skeleton-avatar,
    .feature-skeleton-box,
    .feature-skeleton-button {
        display: block;
        border-radius: 999px;
        background: linear-gradient(90deg, rgba(255,255,255,0.06), rgba(255,255,255,0.16), rgba(255,255,255,0.06));
        background-size: 220% 100%;
        animation: skeleton-shimmer 1.15s ease-in-out infinite;
    }

    .app-skeleton-line {
        height: 12px;
        margin-top: 12px;
    }

    .app-skeleton-line-title {
        width: 54%;
        height: 18px;
        margin-top: 0;
    }

    .app-skeleton-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        margin-top: 18px;
    }

    .app-skeleton-grid span {
        height: 52px;
        border-radius: 14px;
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
        border: 1px solid rgba(255,255,255,0.05);
        background: rgba(255,255,255,0.03);
        min-height: 178px;
    }

    .resident-card.feature-skeleton-card,
    .community-feed-card.feature-skeleton-card {
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
        border-top: 1px solid rgba(255,255,255,0.05);
    }

    .feature-skeleton-post-actions .feature-skeleton-box {
        height: 44px;
        border-radius: 12px;
    }

    @keyframes skeleton-shimmer {
        0% { background-position: 120% 0; }
        100% { background-position: -120% 0; }
    }

    .app-confirm-backdrop {
        position: fixed;
        inset: 0;
        z-index: 130;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 18px;
        background: rgba(12,10,8,0.52);
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
        border: 1px solid rgba(214,168,91,0.18);
        background: rgba(30,27,23,0.98);
        box-shadow: 0 26px 70px rgba(0,0,0,0.32);
    }

    .app-confirm-icon {
        width: 42px;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
        background: rgba(214,168,91,0.13);
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
        border: 1px solid rgba(214,168,91,0.16);
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
        background: rgba(255,255,255,0.04);
        color: #e8e0d3;
    }

    .resident-filter-bar,
    .community-filter-bar {
        display: grid;
        grid-template-columns: minmax(220px, 1fr) repeat(2, minmax(150px, 0.35fr));
        gap: 12px;
        margin-bottom: 18px;
    }

    .community-filter-bar {
        grid-template-columns: minmax(220px, 1fr) minmax(150px, 0.35fr);
    }

    .resident-filter-input,
    .resident-filter-select,
    .community-filter-input,
    .community-filter-select {
        width: 100%;
        min-height: 44px;
        border-radius: 14px;
        border: 1px solid rgba(214,168,91,0.14);
        background: rgba(37,39,42,0.90);
        color: #f8f3ea;
        padding: 0 14px;
        font: inherit;
        outline: none;
    }

    .resident-filter-input:focus,
    .resident-filter-select:focus,
    .community-filter-input:focus,
    .community-filter-select:focus {
        border-color: rgba(214,168,91,0.38);
        box-shadow: 0 0 0 4px rgba(214,168,91,0.08);
    }

    .resident-filter-empty,
    .community-filter-empty {
        display: none;
        padding: 22px;
        border-radius: 18px;
        text-align: center;
        color: #b8ab98;
        background: rgba(255,255,255,0.03);
        border: 1px dashed rgba(214,168,91,0.18);
    }

    .resident-filter-empty.is-visible,
    .community-filter-empty.is-visible {
        display: block;
    }

    .app-field-error {
        display: block;
        margin-top: 7px;
        color: #f0b3a9;
        font-size: 0.8rem;
        line-height: 1.35;
        font-weight: 650;
    }

    html.theme-light body.role-resident .app-field-error {
        color: #9b3e31;
    }

    body.role-resident .resident-flash[data-auto-dismiss],
    body.role-resident .community-feedback-banner[data-auto-dismiss] {
        display: none !important;
    }

    body.role-resident .resident-empty-state::before,
    body.role-resident .community-empty-state::before {
        content: "";
        width: 42px;
        height: 42px;
        display: inline-flex;
        margin: 0 auto 12px;
        border-radius: 14px;
        background:
            linear-gradient(135deg, rgba(214,168,91,0.24), rgba(214,168,91,0.08)),
            radial-gradient(circle at center, rgba(255,255,255,0.12), transparent 55%);
        border: 1px solid rgba(214,168,91,0.18);
    }

    html.theme-light body.role-resident .app-toast,
    html.theme-light body.role-resident .app-loading-panel,
    html.theme-light body.role-resident .app-confirm-dialog {
        background: rgba(255,253,248,0.98);
        border-color: rgba(76,62,46,0.14);
        color: #2f271f;
        box-shadow: 0 22px 54px rgba(94,73,45,0.16);
    }

    html.theme-light body.role-resident .app-loading-overlay,
    html.theme-light body.role-resident .app-confirm-backdrop {
        background: rgba(52,40,25,0.22);
    }

    html.theme-light body.role-resident .app-confirm-dialog h2 {
        color: #2f271f;
    }

    html.theme-light body.role-resident .app-confirm-dialog p,
    html.theme-light body.role-resident .resident-filter-empty,
    html.theme-light body.role-resident .community-filter-empty {
        color: #655747;
    }

    html.theme-light body.role-resident .resident-filter-input,
    html.theme-light body.role-resident .resident-filter-select,
    html.theme-light body.role-resident .community-filter-input,
    html.theme-light body.role-resident .community-filter-select {
        background: rgba(255,255,255,0.86);
        border-color: rgba(76,62,46,0.16);
        color: #2f271f;
    }

    html.theme-light body.role-resident .feature-skeleton-card {
        background: rgba(80,62,42,0.045);
        border-color: rgba(76,62,46,0.11);
    }

    html.theme-light body.role-resident .feature-skeleton-line,
    html.theme-light body.role-resident .feature-skeleton-pill,
    html.theme-light body.role-resident .feature-skeleton-avatar,
    html.theme-light body.role-resident .feature-skeleton-box,
    html.theme-light body.role-resident .feature-skeleton-button {
        background: linear-gradient(90deg, rgba(80,62,42,0.06), rgba(80,62,42,0.16), rgba(80,62,42,0.06));
        background-size: 220% 100%;
    }

    @media (max-width: 760px) {
        .resident-filter-bar,
        .community-filter-bar {
            grid-template-columns: 1fr;
        }

        .app-toast-stack {
            right: 12px;
            bottom: 12px;
        }

        .app-confirm-dialog {
            grid-template-columns: 1fr;
        }

        .app-confirm-actions {
            display: grid;
            grid-template-columns: 1fr;
        }

        .feature-skeleton-top,
        .feature-skeleton-actions {
            flex-direction: column;
            align-items: stretch;
        }

        .feature-skeleton-meta,
        .feature-skeleton-post-actions {
            grid-template-columns: 1fr;
        }
    }

    @media not all {
    /* Resident consistency pass: keeps resident pages using one palette despite page-local CSS. */
    body.role-resident {
        --resident-page-title: #f3ece2;
        --resident-page-body: #d2c8bb;
        --resident-page-muted: #a29382;
        --resident-accent: #e0ad55;
        --resident-accent-soft: rgba(224,173,85,0.14);
        --resident-border: rgba(224,173,85,0.18);
        --resident-border-soft: rgba(255,244,225,0.09);
        --resident-border-strong: rgba(224,173,85,0.24);
        --resident-radius-panel: 22px;
        --resident-radius-card: 16px;
        --resident-radius-control: 14px;
        --resident-radius-pill: 999px;
        --resident-hero: linear-gradient(115deg, #1b1e1e 0%, #252727 48%, #2b2924 72%, #372a19 100%);
        --resident-surface: linear-gradient(180deg, rgba(42,44,42,0.94) 0%, rgba(34,36,35,0.94) 100%);
        --resident-surface-inner: rgba(255,244,225,0.055);
        --resident-input: rgba(21,23,23,0.94);
        --resident-shadow: 0 18px 42px rgba(0,0,0,0.24);
        --resident-success-bg: rgba(111,160,111,0.16);
        --resident-success-text: #a8d39a;
        --resident-danger-bg: rgba(224,112,96,0.14);
        --resident-danger-text: #f0b3a9;
        --resident-info-bg: rgba(104,145,171,0.14);
        --resident-info-text: #a9c9dc;
        --resident-warning-bg: rgba(224,173,85,0.16);
        --resident-warning-text: #e9c783;
    }

    html.theme-light body.role-resident {
        --resident-page-title: #2f271f;
        --resident-page-body: #5d5043;
        --resident-page-muted: #806f5c;
        --resident-accent: #9b641d;
        --resident-accent-soft: rgba(185,130,47,0.13);
        --resident-border: rgba(76,62,46,0.14);
        --resident-border-soft: rgba(76,62,46,0.10);
        --resident-border-strong: rgba(185,130,47,0.22);
        --resident-hero: linear-gradient(120deg, rgba(255,253,248,0.94) 0%, rgba(248,240,229,0.94) 54%, rgba(235,222,205,0.92) 100%);
        --resident-surface: rgba(255,253,248,0.88);
        --resident-surface-inner: rgba(80,62,42,0.045);
        --resident-input: rgba(255,255,255,0.86);
        --resident-shadow: 0 14px 30px rgba(94,73,45,0.10);
        --resident-success-bg: rgba(76,128,76,0.13);
        --resident-success-text: #426f3d;
        --resident-danger-bg: rgba(172,70,54,0.12);
        --resident-danger-text: #9b3e31;
        --resident-info-bg: rgba(57,111,136,0.13);
        --resident-info-text: #336b83;
        --resident-warning-bg: rgba(185,130,47,0.13);
        --resident-warning-text: #7c541d;
    }

    body.role-resident .resident-page-hero,
    body.role-resident .resident-ticket-create-hero,
    body.role-resident .resident-booking-create-hero,
    body.role-resident .resident-booking-hero,
    body.role-resident .resident-ticket-hero,
    body.role-resident .community-feed-hero,
    body.role-resident .community-post-hero,
    body.role-resident .concern-hero {
        background: var(--resident-hero) !important;
        border-color: var(--resident-border) !important;
        border-width: 1px !important;
        border-style: solid !important;
        border-radius: var(--resident-radius-panel) !important;
        box-shadow: var(--resident-shadow) !important;
    }

    body.role-resident .resident-activity-card,
    body.role-resident .resident-surface-panel,
    body.role-resident .resident-page-panel,
    body.role-resident .resident-ticket-create-panel,
    body.role-resident .resident-booking-create-panel,
    body.role-resident .resident-ticket-panel,
    body.role-resident .resident-booking-panel,
    body.role-resident .resident-booking-detail-panel,
    body.role-resident .community-review-strip,
    body.role-resident .community-composer-card,
    body.role-resident .community-feed-card,
    body.role-resident .community-post-panel,
    body.role-resident .community-comment-card,
    body.role-resident .community-empty-state,
    body.role-resident .concern-card,
    body.role-resident .concern-alert-context {
        background: var(--resident-surface) !important;
        border-color: var(--resident-border) !important;
        border-width: 1px !important;
        border-style: solid !important;
        border-radius: var(--resident-radius-panel) !important;
        color: var(--resident-page-title) !important;
        box-shadow: var(--resident-shadow) !important;
    }

    body.role-resident .resident-card,
    body.role-resident .resident-stack-item,
    body.role-resident .resident-notice-card,
    body.role-resident .resident-community-entry,
    body.role-resident .resident-meta-box,
    body.role-resident .resident-ticket-upload-panel,
    body.role-resident .resident-ticket-priority-card,
    body.role-resident .resident-booking-slot,
    body.role-resident .community-review-card,
    body.role-resident .community-composer-trigger,
    body.role-resident .community-action-btn,
    body.role-resident .community-comment,
    body.role-resident .resident-empty-state,
    body.role-resident .resident-filter-empty,
    body.role-resident .community-filter-empty {
        background: var(--resident-surface-inner) !important;
        border-color: var(--resident-border-soft) !important;
        border-width: 1px !important;
        border-style: solid !important;
        border-radius: var(--resident-radius-card) !important;
        color: var(--resident-page-title) !important;
    }

    body.role-resident .resident-empty-state,
    body.role-resident .community-empty-state,
    body.role-resident .resident-filter-empty,
    body.role-resident .community-filter-empty {
        border-style: dashed !important;
        border-color: var(--resident-border-strong) !important;
    }

    body.role-resident .resident-home-title,
    body.role-resident .resident-section-title,
    body.role-resident .resident-page-title,
    body.role-resident .resident-ticket-create-title,
    body.role-resident .resident-booking-create-title,
    body.role-resident .resident-booking-title,
    body.role-resident .resident-ticket-title,
    body.role-resident .community-feed-title,
    body.role-resident .community-post-title,
    body.role-resident .concern-title,
    body.role-resident .resident-surface-head h2,
    body.role-resident .resident-page-panel-head h2,
    body.role-resident .resident-ticket-create-panel-head h2,
    body.role-resident .resident-booking-create-head h2,
    body.role-resident .resident-card-heading h3,
    body.role-resident .resident-stack-item-row h3,
    body.role-resident .resident-notice-title h3,
    body.role-resident .resident-community-entry h3,
    body.role-resident .community-section-head h2,
    body.role-resident .community-review-top h3,
    body.role-resident .community-feed-copy h3,
    body.role-resident .community-feed-author-copy strong,
    body.role-resident .concern-card-head h2 {
        color: var(--resident-page-title) !important;
    }

    body.role-resident .resident-home-subtitle,
    body.role-resident .resident-page-subtitle,
    body.role-resident .resident-ticket-create-subtitle,
    body.role-resident .resident-booking-create-subtitle,
    body.role-resident .resident-booking-subtitle,
    body.role-resident .resident-ticket-subtitle,
    body.role-resident .community-feed-subtitle,
    body.role-resident .community-post-subtitle,
    body.role-resident .concern-subtitle,
    body.role-resident .resident-card-description,
    body.role-resident .resident-stack-item-row p,
    body.role-resident .resident-notice-card p,
    body.role-resident .resident-community-entry p,
    body.role-resident .community-feed-copy p,
    body.role-resident .community-review-top p,
    body.role-resident .concern-card-head p {
        color: var(--resident-page-body) !important;
    }

    body.role-resident .resident-surface-head p,
    body.role-resident .resident-page-panel-head p,
    body.role-resident .resident-meta-box span,
    body.role-resident .resident-stack-meta,
    body.role-resident .resident-community-entry-time,
    body.role-resident .resident-hero-stat span,
    body.role-resident .resident-booking-create-stat span,
    body.role-resident .community-feed-stat span,
    body.role-resident .community-feed-author-copy span,
    body.role-resident .community-feed-stats-row,
    body.role-resident .community-review-meta,
    body.role-resident .resident-ticket-upload-note,
    body.role-resident .resident-booking-create-help {
        color: var(--resident-page-muted) !important;
    }

    body.role-resident .resident-home-kicker,
    body.role-resident .resident-page-kicker,
    body.role-resident .resident-ticket-create-kicker,
    body.role-resident .resident-booking-create-kicker,
    body.role-resident .resident-booking-kicker,
    body.role-resident .resident-ticket-kicker,
    body.role-resident .community-feed-kicker,
    body.role-resident .community-post-kicker,
    body.role-resident .concern-kicker,
    body.role-resident .resident-page-eyebrow,
    body.role-resident .resident-booking-create-eyebrow,
    body.role-resident .resident-ticket-create-chip,
    body.role-resident .concern-badge,
    body.role-resident .resident-surface-head a,
    body.role-resident .resident-see-more-btn,
    body.role-resident .resident-empty-state a,
    body.role-resident .community-empty-state a,
    body.role-resident .community-review-actions a,
    body.role-resident .community-review-actions button,
    body.role-resident .resident-card-links a,
    body.role-resident .resident-card-links button {
        color: var(--resident-accent) !important;
    }

    body.role-resident .resident-page-btn-primary,
    body.role-resident .resident-ticket-create-btn-primary,
    body.role-resident .resident-booking-create-btn-primary,
    body.role-resident .community-composer-actions a:not(.community-composer-secondary-action),
    body.role-resident .community-pagination-link a,
    body.role-resident .concern-btn-primary,
    body.role-resident .resident-home-btn-primary {
        background: linear-gradient(95deg, #c9953f, #e0ad55) !important;
        color: #17120d !important;
        border-color: rgba(224,173,85,0.28) !important;
    }

    body.role-resident .resident-home-btn-secondary,
    body.role-resident .resident-ticket-create-btn-secondary,
    body.role-resident .resident-booking-create-btn-secondary,
    body.role-resident .resident-page-btn-secondary,
    body.role-resident .community-composer-secondary-action,
    body.role-resident .concern-btn-secondary {
        background: var(--resident-surface-inner) !important;
        color: var(--resident-page-title) !important;
        border-color: var(--resident-border) !important;
    }

    body.role-resident .resident-ticket-create-input,
    body.role-resident .resident-ticket-create-input-file,
    body.role-resident .resident-booking-create-input,
    body.role-resident .concern-input,
    body.role-resident .resident-filter-input,
    body.role-resident .resident-filter-select,
    body.role-resident .community-filter-input,
    body.role-resident .community-filter-select,
    body.role-resident .community-post-input,
    body.role-resident .community-comment-input {
        background: var(--resident-input) !important;
        border-color: var(--resident-border) !important;
        border-width: 1px !important;
        border-style: solid !important;
        border-radius: var(--resident-radius-control) !important;
        color: var(--resident-page-title) !important;
    }

    body.role-resident .resident-surface-divider,
    body.role-resident .resident-page-divider,
    body.role-resident .resident-ticket-create-divider,
    body.role-resident .resident-booking-create-divider,
    body.role-resident .community-post-divider {
        background: linear-gradient(to right, var(--resident-border), rgba(224,173,85,0.06), transparent) !important;
    }

    body.role-resident .resident-badge-status-approved,
    body.role-resident .resident-badge-status-completed,
    body.role-resident .resident-status-chip-completed,
    body.role-resident .resident-badge-priority-low {
        background: var(--resident-success-bg) !important;
        color: var(--resident-success-text) !important;
    }

    body.role-resident .resident-badge-status-rejected,
    body.role-resident .resident-badge-status-cancelled,
    body.role-resident .resident-badge-priority-critical,
    body.role-resident .community-status-chip-rejected,
    body.role-resident .resident-flash-error,
    body.role-resident .concern-alert-error,
    body.role-resident .community-review-note {
        background: var(--resident-danger-bg) !important;
        border-color: rgba(224,112,96,0.20) !important;
        color: var(--resident-danger-text) !important;
    }

    body.role-resident .resident-badge-status-assigned,
    body.role-resident .resident-badge-status-in_progress,
    body.role-resident .resident-status-chip-in_progress {
        background: var(--resident-info-bg) !important;
        color: var(--resident-info-text) !important;
    }

    body.role-resident .resident-badge-status-received,
    body.role-resident .resident-badge-status-pending,
    body.role-resident .resident-badge-priority-medium,
    body.role-resident .community-status-chip-pending,
    body.role-resident .resident-status-chip-pending {
        background: var(--resident-warning-bg) !important;
        color: var(--resident-warning-text) !important;
    }

    body.role-resident .resident-page-btn,
    body.role-resident .resident-home-btn,
    body.role-resident .resident-ticket-create-btn,
    body.role-resident .resident-booking-create-btn,
    body.role-resident .concern-btn,
    body.role-resident .community-composer-actions a,
    body.role-resident .community-pagination-link a,
    body.role-resident .community-action-btn,
    body.role-resident .community-review-actions a,
    body.role-resident .community-review-actions button,
    body.role-resident .resident-card-links a,
    body.role-resident .resident-card-links button {
        border-width: 1px !important;
        border-style: solid !important;
        border-radius: var(--resident-radius-pill) !important;
    }

    body.role-resident .resident-badge,
    body.role-resident .resident-status-chip,
    body.role-resident .community-status-chip,
    body.role-resident .resident-ticket-create-chip,
    body.role-resident .resident-page-eyebrow,
    body.role-resident .concern-badge {
        border-width: 1px !important;
        border-style: solid !important;
        border-color: color-mix(in srgb, var(--resident-accent) 26%, transparent) !important;
        border-radius: var(--resident-radius-pill) !important;
    }

    body.role-resident .resident-ticket-thumb-link,
    body.role-resident .resident-ticket-thumb,
    body.role-resident .community-feed-media,
    body.role-resident .community-review-media,
    body.role-resident .resident-ticket-preview-media {
        border-width: 1px !important;
        border-style: solid !important;
        border-color: var(--resident-border-soft) !important;
        border-radius: var(--resident-radius-card) !important;
    }

    body.role-resident .resident-card:hover,
    body.role-resident .resident-stack-item:hover,
    body.role-resident .resident-community-entry:hover,
    body.role-resident .community-feed-card:hover,
    body.role-resident .community-review-card:hover {
        border-color: var(--resident-border-strong) !important;
    }

    /* Resident redesign experiment: calmer system-style layout with less marketing weight. */
    body.role-resident .top-bg-image-layer {
        height: 420px;
        opacity: 0.58;
        filter: saturate(0.86) contrast(0.96);
        mask-image: linear-gradient(to bottom, black 34%, transparent 100%);
    }

    html.theme-light body.role-resident .top-bg-image-layer {
        height: 540px;
        background-image: url('{{ asset('White1.jpg') }}');
        background-repeat: no-repeat;
        background-position: top center;
        background-size: auto 100%;
        opacity: 0.92;
        filter: saturate(1.02) contrast(1);
        mask-image:
            linear-gradient(to bottom, black 42%, transparent 100%),
            linear-gradient(to right, transparent 0%, black 34%);
        mask-composite: intersect;
        -webkit-mask-image:
            linear-gradient(to bottom, black 42%, transparent 100%),
            linear-gradient(to right, transparent 0%, black 34%);
        -webkit-mask-composite: source-in;
    }

    body.role-resident .top-bg-image-layer::after {
        background:
            linear-gradient(180deg, rgba(20,21,21,0.18) 0%, rgba(20,21,21,0.74) 70%, rgba(20,21,21,0.98) 100%),
            linear-gradient(90deg, rgba(20,21,21,0.86) 0%, rgba(20,21,21,0.58) 34%, rgba(20,21,21,0.18) 70%, rgba(20,21,21,0.04) 100%);
    }

    html.theme-light body.role-resident .top-bg-image-layer::after {
        background:
            linear-gradient(180deg, rgba(251,247,240,0.08) 0%, rgba(251,247,240,0.28) 58%, rgba(251,247,240,0.92) 100%),
            linear-gradient(90deg, rgba(251,247,240,0.88) 0%, rgba(251,247,240,0.58) 32%, rgba(251,247,240,0.16) 66%, rgba(251,247,240,0) 100%);
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

    body.role-resident .role-action-btn,
    body.role-resident .role-theme-toggle,
    body.role-resident .role-notification-btn,
    body.role-resident .role-user-chip {
        height: 42px;
        min-height: 42px;
    }

    body.role-resident .role-theme-toggle,
    body.role-resident .role-notification-btn,
    body.role-resident .role-user-chip {
        width: 42px;
    }

    body.role-resident .app-main {
        max-width: 1500px;
        padding-top: 18px;
    }

    body.role-resident .app-main.full-bleed {
        max-width: 1500px;
    }

    body.role-resident .resident-dashboard-shell,
    body.role-resident .resident-page,
    body.role-resident .resident-ticket-create-page,
    body.role-resident .resident-booking-create-page,
    body.role-resident .community-feed-page,
    body.role-resident .concern-page,
    body.role-resident .resident-ticket-page,
    body.role-resident .resident-booking-page,
    body.role-resident .community-post-page {
        max-width: 1500px !important;
        gap: 16px !important;
    }

    body.role-resident .community-feed-page,
    body.role-resident .concern-page {
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

    body.role-resident .resident-home-btn,
    body.role-resident .resident-page-btn,
    body.role-resident .resident-ticket-create-btn,
    body.role-resident .resident-booking-create-btn,
    body.role-resident .concern-btn {
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

    body.role-resident .resident-page-hero,
    body.role-resident .resident-ticket-create-hero,
    body.role-resident .resident-booking-create-hero,
    body.role-resident .resident-ticket-hero,
    body.role-resident .resident-booking-hero,
    body.role-resident .community-feed-hero,
    body.role-resident .community-post-hero,
    body.role-resident .concern-hero {
        padding: 18px 20px !important;
        border-radius: 18px !important;
        align-items: center !important;
    }

    body.role-resident .resident-page-title,
    body.role-resident .resident-ticket-create-title,
    body.role-resident .resident-booking-create-title,
    body.role-resident .resident-ticket-title,
    body.role-resident .resident-booking-title,
    body.role-resident .community-feed-title,
    body.role-resident .community-post-title,
    body.role-resident .concern-title {
        font-size: clamp(1.65rem, 2.35vw, 2.15rem) !important;
        line-height: 1.08 !important;
        letter-spacing: 0 !important;
    }

    body.role-resident .resident-page-subtitle,
    body.role-resident .resident-ticket-create-subtitle,
    body.role-resident .resident-booking-create-subtitle,
    body.role-resident .resident-ticket-subtitle,
    body.role-resident .resident-booking-subtitle,
    body.role-resident .community-feed-subtitle,
    body.role-resident .community-post-subtitle,
    body.role-resident .concern-subtitle {
        max-width: 720px;
        margin-top: 7px !important;
        font-size: 0.9rem !important;
        line-height: 1.48 !important;
    }

    body.role-resident .resident-hero-stat-row,
    body.role-resident .resident-ticket-hero-stats,
    body.role-resident .resident-booking-create-stats,
    body.role-resident .community-feed-stats {
        margin-top: 12px !important;
        gap: 8px !important;
    }

    body.role-resident .resident-hero-stat,
    body.role-resident .resident-ticket-hero-stat,
    body.role-resident .resident-booking-create-stat,
    body.role-resident .community-feed-stat {
        min-width: 92px !important;
        padding: 8px 10px !important;
        border-radius: 12px !important;
    }

    body.role-resident .resident-hero-stat strong,
    body.role-resident .resident-ticket-hero-stat strong,
    body.role-resident .resident-booking-create-stat strong,
    body.role-resident .community-feed-stat strong {
        font-size: 1rem !important;
    }

    body.role-resident .resident-content-grid {
        grid-template-columns: minmax(0, 1.5fr) minmax(320px, 0.6fr);
        gap: 14px;
    }

    body.role-resident .resident-surface-panel,
    body.role-resident .resident-page-panel,
    body.role-resident .resident-ticket-create-panel,
    body.role-resident .resident-booking-create-panel,
    body.role-resident .resident-ticket-panel,
    body.role-resident .resident-booking-panel,
    body.role-resident .community-review-strip,
    body.role-resident .community-composer-card,
    body.role-resident .community-feed-card,
    body.role-resident .community-post-panel,
    body.role-resident .concern-card {
        padding: 18px 20px !important;
        border-radius: 16px !important;
    }

    body.role-resident .resident-surface-head,
    body.role-resident .resident-page-panel-head,
    body.role-resident .resident-ticket-create-panel-head,
    body.role-resident .resident-booking-create-head,
    body.role-resident .community-section-head,
    body.role-resident .concern-card-head {
        margin-bottom: 12px !important;
    }

    body.role-resident .resident-surface-head h2,
    body.role-resident .resident-page-panel-head h2,
    body.role-resident .resident-ticket-create-panel-head h2,
    body.role-resident .resident-booking-create-head h2,
    body.role-resident .community-section-head h2,
    body.role-resident .concern-card-head h2 {
        font-family: 'Inter', sans-serif !important;
        font-size: 1.08rem !important;
        font-weight: 800 !important;
    }

    body.role-resident .resident-surface-head p,
    body.role-resident .resident-page-panel-head p,
    body.role-resident .resident-ticket-create-panel-head p,
    body.role-resident .resident-booking-create-head p,
    body.role-resident .community-section-head p,
    body.role-resident .concern-card-head p {
        font-size: 0.84rem !important;
    }

    body.role-resident .resident-card,
    body.role-resident .resident-stack-item,
    body.role-resident .resident-notice-card,
    body.role-resident .resident-community-entry,
    body.role-resident .community-review-card,
    body.role-resident .community-comment,
    body.role-resident .resident-empty-state,
    body.role-resident .community-empty-state {
        border-radius: 14px !important;
        padding: 14px 16px !important;
    }

    body.role-resident .resident-card-heading h3,
    body.role-resident .resident-stack-item-row h3,
    body.role-resident .resident-notice-title h3,
    body.role-resident .resident-community-entry h3,
    body.role-resident .community-feed-copy h3,
    body.role-resident .community-review-top h3 {
        font-size: 0.98rem !important;
    }

    body.role-resident .resident-card-description,
    body.role-resident .resident-stack-item-row p,
    body.role-resident .resident-notice-card p,
    body.role-resident .resident-community-entry p,
    body.role-resident .community-feed-copy p,
    body.role-resident .community-review-top p {
        font-size: 0.86rem !important;
        line-height: 1.55 !important;
    }

    body.role-resident .resident-card-meta-grid,
    body.role-resident .resident-booking-schedule-grid,
    body.role-resident .resident-ticket-hero-stats {
        gap: 10px !important;
    }

    body.role-resident .resident-meta-box {
        padding: 10px 12px !important;
        border-radius: 12px !important;
    }

    body.role-resident .resident-filter-bar,
    body.role-resident .community-filter-bar {
        gap: 10px;
        margin-bottom: 14px;
    }

    body.role-resident .resident-filter-input,
    body.role-resident .resident-filter-select,
    body.role-resident .community-filter-input,
    body.role-resident .community-filter-select,
    body.role-resident .resident-ticket-create-input,
    body.role-resident .resident-ticket-create-input-file,
    body.role-resident .resident-booking-create-input,
    body.role-resident .concern-input {
        min-height: 42px;
        border-radius: 12px !important;
        font-size: 0.9rem !important;
    }

    body.role-resident .community-composer-card {
        align-items: stretch;
    }

    body.role-resident .community-composer-avatar,
    body.role-resident .community-feed-avatar {
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

    body.role-resident .resident-badge,
    body.role-resident .resident-status-chip,
    body.role-resident .community-status-chip {
        min-height: 26px;
        padding: 5px 9px !important;
        font-size: 0.66rem !important;
        letter-spacing: 0.06em !important;
    }

    @media (max-width: 980px) {
        body.role-resident .app-main,
        body.role-resident .app-main.full-bleed {
            padding-inline: 18px;
        }

        body.role-resident .resident-content-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 640px) {
        body.role-resident .app-main,
        body.role-resident .app-main.full-bleed {
            padding-inline: 14px;
        }

        body.role-resident .resident-home-title {
            font-size: 2.15rem !important;
        }

        body.role-resident .resident-home-actions,
        body.role-resident .resident-page-actions,
        body.role-resident .resident-ticket-create-hero-actions,
        body.role-resident .resident-booking-create-actions,
        body.role-resident .concern-hero-actions {
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
    /* Resident polish pass: keeps the original layout, but gives the surfaces clearer hierarchy. */
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
        --resident-polish-glow: 0 18px 42px rgba(0,0,0,0.22), 0 0 28px rgba(214,168,91,0.055);
        --resident-polish-glow-strong: 0 20px 48px rgba(0,0,0,0.26), 0 0 42px rgba(214,168,91,0.10);
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

    body.role-resident .resident-page-hero,
    body.role-resident .resident-ticket-create-hero,
    body.role-resident .resident-booking-create-hero,
    body.role-resident .resident-booking-hero,
    body.role-resident .resident-ticket-hero,
    body.role-resident .community-feed-hero,
    body.role-resident .community-post-hero,
    body.role-resident .concern-hero {
        padding: 24px 28px !important;
        min-height: 0 !important;
        border-radius: 20px !important;
        background:
            radial-gradient(circle at 84% 16%, rgba(224,173,85,0.16), transparent 34%),
            linear-gradient(135deg, rgba(48, 52, 53, 0.82) 0%, rgba(28, 32, 34, 0.74) 58%, rgba(72, 52, 25, 0.58) 100%) !important;
        backdrop-filter: blur(18px) saturate(1.08);
        -webkit-backdrop-filter: blur(18px) saturate(1.08);
        border: 1px solid var(--resident-polish-border) !important;
        box-shadow: var(--resident-polish-glow-strong) !important;
    }

    body.role-resident .resident-page-title,
    body.role-resident .resident-ticket-create-title,
    body.role-resident .resident-booking-create-title,
    body.role-resident .resident-booking-title,
    body.role-resident .resident-ticket-title,
    body.role-resident .community-feed-title,
    body.role-resident .community-post-title,
    body.role-resident .concern-title {
        color: var(--resident-polish-title) !important;
        font-size: clamp(2rem, 3.4vw, 3rem) !important;
        line-height: 1.08 !important;
        text-wrap: balance;
    }

    body.role-resident .resident-page-subtitle,
    body.role-resident .resident-ticket-create-subtitle,
    body.role-resident .resident-booking-create-subtitle,
    body.role-resident .resident-booking-subtitle,
    body.role-resident .resident-ticket-subtitle,
    body.role-resident .community-feed-subtitle,
    body.role-resident .community-post-subtitle,
    body.role-resident .concern-subtitle {
        max-width: 720px;
        color: var(--resident-polish-body) !important;
        font-size: 0.98rem !important;
        line-height: 1.58 !important;
    }

    body.role-resident .resident-page-kicker,
    body.role-resident .resident-ticket-create-kicker,
    body.role-resident .resident-booking-create-kicker,
    body.role-resident .resident-booking-kicker,
    body.role-resident .resident-ticket-kicker,
    body.role-resident .community-feed-kicker,
    body.role-resident .community-post-kicker,
    body.role-resident .concern-kicker {
        color: var(--resident-polish-accent) !important;
    }

    body.role-resident .resident-activity-card,
    body.role-resident .resident-surface-panel,
    body.role-resident .resident-page-panel,
    body.role-resident .resident-ticket-create-panel,
    body.role-resident .resident-booking-create-panel,
    body.role-resident .resident-ticket-panel,
    body.role-resident .resident-booking-panel,
    body.role-resident .resident-booking-detail-panel,
    body.role-resident .community-review-strip,
    body.role-resident .community-composer-card,
    body.role-resident .community-feed-card,
    body.role-resident .community-post-panel,
    body.role-resident .community-comment-card,
    body.role-resident .community-empty-state,
    body.role-resident .concern-card,
    body.role-resident .concern-alert-context {
        background:
            radial-gradient(circle at 92% 8%, rgba(214,168,91,0.10), transparent 28%),
            linear-gradient(180deg, rgba(45,50,52,0.76) 0%, var(--resident-polish-panel) 100%) !important;
        backdrop-filter: blur(16px) saturate(1.06);
        -webkit-backdrop-filter: blur(16px) saturate(1.06);
        border: 1px solid var(--resident-polish-border) !important;
        border-radius: 18px !important;
        box-shadow: var(--resident-polish-glow) !important;
    }

    body.role-resident .resident-activity-card {
        background:
            radial-gradient(circle at 88% 12%, rgba(224,173,85,0.13), transparent 30%),
            linear-gradient(180deg, rgba(52,57,59,0.82), var(--resident-polish-panel-strong)) !important;
        box-shadow: var(--resident-polish-glow) !important;
    }

    body.role-resident .resident-page-panel,
    body.role-resident .resident-ticket-create-panel,
    body.role-resident .resident-booking-create-panel,
    body.role-resident .resident-ticket-panel,
    body.role-resident .resident-booking-panel,
    body.role-resident .resident-booking-detail-panel,
    body.role-resident .community-composer-card,
    body.role-resident .community-feed-card,
    body.role-resident .community-post-panel,
    body.role-resident .community-comment-card,
    body.role-resident .concern-card {
        background: linear-gradient(180deg, #262b2d 0%, #222729 100%) !important;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
        box-shadow: 0 14px 30px rgba(0,0,0,0.17) !important;
    }

    body.role-resident .resident-card,
    body.role-resident .resident-stack-item,
    body.role-resident .resident-notice-card,
    body.role-resident .resident-community-entry,
    body.role-resident .resident-ticket-upload-panel,
    body.role-resident .resident-ticket-priority-card,
    body.role-resident .resident-booking-slot,
    body.role-resident .community-review-card,
    body.role-resident .community-composer-trigger,
    body.role-resident .community-action-btn,
    body.role-resident .community-comment,
    body.role-resident .resident-empty-state,
    body.role-resident .resident-filter-empty,
    body.role-resident .community-filter-empty {
        background: var(--resident-polish-card) !important;
        backdrop-filter: none;
        -webkit-backdrop-filter: none;
        border: 1px solid var(--resident-polish-border-soft) !important;
        border-radius: 14px !important;
        box-shadow: 0 10px 22px rgba(0,0,0,0.13);
    }

    body.role-resident .resident-meta-box,
    body.role-resident .resident-hero-stat,
    body.role-resident .resident-booking-create-stat,
    body.role-resident .community-feed-stat,
    body.role-resident .resident-filter-bar,
    body.role-resident .community-filter-bar {
        background: var(--resident-polish-card-warm) !important;
        backdrop-filter: none;
        -webkit-backdrop-filter: none;
        border: 1px solid rgba(214,168,91,0.13) !important;
        border-radius: 14px !important;
    }

    body.role-resident .resident-card:hover,
    body.role-resident .resident-stack-item:hover,
    body.role-resident .resident-community-entry:hover,
    body.role-resident .community-feed-card:hover,
    body.role-resident .community-review-card:hover {
        border-color: var(--resident-polish-border-hover) !important;
    }

    body.role-resident .resident-surface-head h2,
    body.role-resident .resident-surface-head h3,
    body.role-resident .resident-page-panel-head h2,
    body.role-resident .resident-card-heading h3,
    body.role-resident .resident-stack-item-row h3,
    body.role-resident .resident-notice-title h3,
    body.role-resident .resident-community-entry h3,
    body.role-resident .resident-activity-card-top strong,
    body.role-resident .community-section-head h2,
    body.role-resident .community-review-top h3,
    body.role-resident .community-feed-copy h3,
    body.role-resident .community-feed-author-copy strong,
    body.role-resident .concern-card-head h2 {
        color: var(--resident-polish-title) !important;
    }

    body.role-resident .resident-surface-head p,
    body.role-resident .resident-page-panel-head p,
    body.role-resident .resident-card-description,
    body.role-resident .resident-stack-item-row p,
    body.role-resident .resident-notice-card p,
    body.role-resident .resident-community-entry p,
    body.role-resident .community-feed-copy p,
    body.role-resident .community-review-top p,
    body.role-resident .concern-card-head p {
        color: var(--resident-polish-body) !important;
    }

    body.role-resident .resident-meta-box span,
    body.role-resident .resident-stack-meta,
    body.role-resident .resident-community-entry-time,
    body.role-resident .community-review-meta,
    body.role-resident .resident-ticket-upload-note,
    body.role-resident .resident-booking-create-help {
        color: var(--resident-polish-muted) !important;
    }

    body.role-resident .resident-filter-input,
    body.role-resident .resident-filter-select,
    body.role-resident .community-filter-input,
    body.role-resident .community-filter-select,
    body.role-resident .resident-ticket-create-input,
    body.role-resident .resident-ticket-create-input-file,
    body.role-resident .resident-booking-create-input,
    body.role-resident .concern-input,
    body.role-resident .community-post-input,
    body.role-resident .community-comment-input {
        background: #15191a !important;
        border-color: rgba(255,244,225,0.11) !important;
        color: var(--resident-polish-title) !important;
    }

    @media (max-width: 760px) {
        body.role-resident .resident-home-title {
            font-size: clamp(2.15rem, 11vw, 3rem);
        }

        body.role-resident .resident-page-hero,
        body.role-resident .resident-ticket-create-hero,
        body.role-resident .resident-booking-create-hero,
        body.role-resident .community-feed-hero,
        body.role-resident .concern-hero {
            padding: 22px !important;
        }
    }
    }

    /* Resident refinement pass: solid surfaces, clearer nested cards, smaller feature-page heroes. */
    body.role-resident {
        --resident-solid-panel: #2a2d30;
        --resident-solid-panel-alt: #26292b;
        --resident-solid-card: #191b1d;
        --resident-solid-card-alt: #1f2123;
        --resident-solid-border: rgba(224,173,85,0.20);
        --resident-solid-border-soft: rgba(255,244,225,0.10);
        --resident-solid-title: #f4eadc;
        --resident-solid-body: #c8bdad;
        --resident-solid-muted: #9a8c79;
        --resident-solid-accent: #d6a85b;
    }

    body.role-resident .resident-activity-card,
    body.role-resident .resident-surface-panel,
    body.role-resident .resident-page-panel,
    body.role-resident .resident-ticket-create-panel,
    body.role-resident .resident-booking-create-panel,
    body.role-resident .resident-ticket-panel,
    body.role-resident .resident-booking-panel,
    body.role-resident .resident-booking-detail-panel,
    body.role-resident .community-review-strip,
    body.role-resident .community-composer-card,
    body.role-resident .community-feed-card,
    body.role-resident .community-post-panel,
    body.role-resident .community-comment-card,
    body.role-resident .community-empty-state,
    body.role-resident .concern-card,
    body.role-resident .concern-alert-context {
        background: linear-gradient(180deg, var(--resident-solid-panel) 0%, var(--resident-solid-panel-alt) 100%) !important;
        border: 1px solid var(--resident-solid-border) !important;
        box-shadow: 0 14px 30px rgba(0,0,0,0.24) !important;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
    }

    body.role-resident .resident-card,
    body.role-resident .resident-stack-item,
    body.role-resident .resident-notice-card,
    body.role-resident .resident-community-entry,
    body.role-resident .resident-ticket-upload-panel,
    body.role-resident .resident-ticket-priority-card,
    body.role-resident .resident-booking-slot,
    body.role-resident .community-review-card,
    body.role-resident .community-composer-trigger,
    body.role-resident .community-action-btn,
    body.role-resident .community-comment,
    body.role-resident .resident-empty-state,
    body.role-resident .resident-filter-empty,
    body.role-resident .community-filter-empty {
        background: linear-gradient(180deg, var(--resident-solid-card-alt) 0%, var(--resident-solid-card) 100%) !important;
        border: 1px solid var(--resident-solid-border-soft) !important;
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.025), 0 8px 18px rgba(0,0,0,0.20) !important;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
    }

    body.role-resident .resident-page-hero,
    body.role-resident .resident-ticket-create-hero,
    body.role-resident .resident-booking-create-hero,
    body.role-resident .resident-ticket-hero,
    body.role-resident .resident-booking-hero,
    body.role-resident .community-feed-hero,
    body.role-resident .community-post-hero,
    body.role-resident .concern-hero {
        min-height: 0 !important;
        padding: 18px 22px !important;
        border-radius: 18px !important;
        display: flex !important;
        align-items: center !important;
        gap: 18px !important;
        background: linear-gradient(180deg, var(--resident-solid-panel) 0%, var(--resident-solid-panel-alt) 100%) !important;
        border: 1px solid var(--resident-solid-border) !important;
        box-shadow: 0 14px 30px rgba(0,0,0,0.24) !important;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
    }

    body.role-resident .resident-page-title,
    body.role-resident .resident-ticket-create-title,
    body.role-resident .resident-booking-create-title,
    body.role-resident .resident-ticket-title,
    body.role-resident .resident-booking-title,
    body.role-resident .community-feed-title,
    body.role-resident .community-post-title,
    body.role-resident .concern-title {
        font-size: clamp(1.7rem, 2.6vw, 2.45rem) !important;
        line-height: 1.08 !important;
        letter-spacing: 0 !important;
    }

    body.role-resident .resident-page-subtitle,
    body.role-resident .resident-ticket-create-subtitle,
    body.role-resident .resident-booking-create-subtitle,
    body.role-resident .resident-ticket-subtitle,
    body.role-resident .resident-booking-subtitle,
    body.role-resident .community-feed-subtitle,
    body.role-resident .community-post-subtitle,
    body.role-resident .concern-subtitle {
        max-width: 760px !important;
        margin-top: 8px !important;
        font-size: 0.92rem !important;
        line-height: 1.45 !important;
    }

    body.role-resident .resident-page-kicker,
    body.role-resident .resident-ticket-create-kicker,
    body.role-resident .resident-booking-create-kicker,
    body.role-resident .resident-ticket-kicker,
    body.role-resident .resident-booking-kicker,
    body.role-resident .community-feed-kicker,
    body.role-resident .community-post-kicker,
    body.role-resident .concern-kicker {
        margin-bottom: 6px !important;
        font-size: 0.72rem !important;
        letter-spacing: 0.14em !important;
    }

    body.role-resident .resident-hero-stat-row,
    body.role-resident .resident-booking-create-stats,
    body.role-resident .community-feed-stats {
        margin-top: 14px !important;
        gap: 10px !important;
    }

    body.role-resident .resident-hero-stat,
    body.role-resident .resident-booking-create-stat,
    body.role-resident .community-feed-stat {
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

    body.role-resident .resident-activity-card-icon,
    body.role-resident .resident-stack-item-icon {
        background: #2b251c !important;
        border: 1px solid rgba(214,168,91,0.26) !important;
        color: #e2bd78 !important;
    }

    body.role-resident .resident-activity-card-gold .resident-activity-card-icon {
        background: linear-gradient(135deg, #9f6b24 0%, #c79039 100%) !important;
        color: #fff3dc !important;
        border-color: rgba(236,195,126,0.34) !important;
    }

    body.role-resident .resident-surface-head h2,
    body.role-resident .resident-section-title,
    body.role-resident .resident-stack-item-row h3,
    body.role-resident .resident-notice-title h3,
    body.role-resident .resident-community-entry h3,
    body.role-resident .resident-activity-card-top strong {
        color: var(--resident-solid-title) !important;
    }

    body.role-resident .resident-surface-head p,
    body.role-resident .resident-stack-item-row p,
    body.role-resident .resident-notice-card p,
    body.role-resident .resident-community-entry p,
    body.role-resident .resident-activity-card-copy p {
        color: var(--resident-solid-body) !important;
    }

    body.role-resident .resident-stack-meta,
    body.role-resident .resident-community-entry-time {
        color: var(--resident-solid-muted) !important;
    }

    body.role-resident .resident-surface-head a {
        color: var(--resident-solid-accent) !important;
    }

    body.role-resident .resident-surface-divider {
        background: linear-gradient(to right, rgba(214,168,91,0.38), rgba(214,168,91,0.10), transparent) !important;
    }

    body.role-resident .resident-status-chip {
        border-color: rgba(255,244,225,0.10) !important;
    }

    body.role-resident .resident-status-chip-completed {
        background: rgba(93,132,83,0.24) !important;
        color: #d9eccd !important;
    }

    body.role-resident .resident-status-chip-in_progress,
    body.role-resident .resident-status-chip-assigned {
        background: rgba(214,168,91,0.18) !important;
        color: #efd59e !important;
    }

    html.theme-light body.role-resident {
        --resident-light-bg: #f4eadc;
        --resident-light-panel: #decab0;
        --resident-light-panel-2: #d5bea0;
        --resident-light-card: #c6aa86;
        --resident-light-card-2: #b99870;
        --resident-light-border: rgba(111,78,45,0.28);
        --resident-light-border-soft: rgba(111,78,45,0.18);
        --resident-light-title: #2f2419;
        --resident-light-body: #5c4834;
        --resident-light-muted: #765f45;
        --resident-light-accent: #8f5f22;
        --resident-light-accent-strong: #a86f24;
        background:
            radial-gradient(circle at 82% 4%, rgba(255,255,255,0.45), transparent 24%),
            linear-gradient(180deg, #f7efe5 0%, var(--resident-light-bg) 48%, #ead9c2 100%) !important;
        color: var(--resident-light-title) !important;
    }

    html.theme-light body.role-resident .role-nav-shell,
    html.theme-light body.role-resident .role-action-btn,
    html.theme-light body.role-resident .role-theme-toggle,
    html.theme-light body.role-resident .role-user-chip,
    html.theme-light body.role-resident .role-notification-btn,
    html.theme-light body.role-resident .role-mobile-toggle,
    html.theme-light body.role-resident .role-mobile-panel,
    html.theme-light body.role-resident .role-notification-panel {
        background: rgba(226,208,184,0.90) !important;
        border-color: var(--resident-light-border) !important;
        color: var(--resident-light-title) !important;
        box-shadow: 0 16px 34px rgba(111,78,45,0.14) !important;
    }

    html.theme-light body.role-resident .role-nav-link {
        color: var(--resident-light-body) !important;
    }

    html.theme-light body.role-resident .role-nav-link:hover,
    html.theme-light body.role-resident .role-nav-link.is-active {
        background: rgba(143,95,34,0.14) !important;
        color: var(--resident-light-title) !important;
    }

    html.theme-light body.role-resident .resident-activity-card,
    html.theme-light body.role-resident .resident-surface-panel,
    html.theme-light body.role-resident .resident-page-hero,
    html.theme-light body.role-resident .resident-ticket-create-hero,
    html.theme-light body.role-resident .resident-booking-create-hero,
    html.theme-light body.role-resident .resident-ticket-hero,
    html.theme-light body.role-resident .resident-booking-hero,
    html.theme-light body.role-resident .community-feed-hero,
    html.theme-light body.role-resident .community-post-hero,
    html.theme-light body.role-resident .concern-hero,
    html.theme-light body.role-resident .resident-page-panel,
    html.theme-light body.role-resident .resident-ticket-create-panel,
    html.theme-light body.role-resident .resident-booking-create-panel,
    html.theme-light body.role-resident .resident-ticket-panel,
    html.theme-light body.role-resident .resident-booking-panel,
    html.theme-light body.role-resident .resident-booking-detail-panel,
    html.theme-light body.role-resident .community-review-strip,
    html.theme-light body.role-resident .community-composer-card,
    html.theme-light body.role-resident .community-feed-card,
    html.theme-light body.role-resident .community-post-panel,
    html.theme-light body.role-resident .community-comment-card,
    html.theme-light body.role-resident .community-empty-state,
    html.theme-light body.role-resident .concern-card,
    html.theme-light body.role-resident .concern-alert-context {
        background: linear-gradient(180deg, var(--resident-light-panel) 0%, var(--resident-light-panel-2) 100%) !important;
        border-color: var(--resident-light-border) !important;
        box-shadow: 0 16px 34px rgba(111,78,45,0.15) !important;
        color: var(--resident-light-title) !important;
    }

    html.theme-light body.role-resident .resident-card,
    html.theme-light body.role-resident .resident-stack-item,
    html.theme-light body.role-resident .resident-notice-card,
    html.theme-light body.role-resident .resident-community-entry,
    html.theme-light body.role-resident .resident-meta-box,
    html.theme-light body.role-resident .resident-ticket-upload-panel,
    html.theme-light body.role-resident .resident-ticket-priority-card,
    html.theme-light body.role-resident .resident-booking-slot,
    html.theme-light body.role-resident .community-review-card,
    html.theme-light body.role-resident .community-composer-trigger,
    html.theme-light body.role-resident .community-action-btn,
    html.theme-light body.role-resident .community-comment,
    html.theme-light body.role-resident .resident-empty-state,
    html.theme-light body.role-resident .resident-filter-empty,
    html.theme-light body.role-resident .community-filter-empty {
        background: linear-gradient(180deg, rgba(198,170,134,0.88) 0%, rgba(185,152,112,0.88) 100%) !important;
        border-color: var(--resident-light-border-soft) !important;
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.22), 0 8px 18px rgba(111,78,45,0.12) !important;
        color: var(--resident-light-title) !important;
    }

    html.theme-light body.role-resident .resident-home-title,
    html.theme-light body.role-resident .resident-section-title,
    html.theme-light body.role-resident .resident-surface-head h2,
    html.theme-light body.role-resident .resident-surface-head h3,
    html.theme-light body.role-resident .resident-stack-item-row h3,
    html.theme-light body.role-resident .resident-notice-title h3,
    html.theme-light body.role-resident .resident-community-entry h3,
    html.theme-light body.role-resident .resident-page-title,
    html.theme-light body.role-resident .resident-page-panel-head h2,
    html.theme-light body.role-resident .resident-card-heading h3,
    html.theme-light body.role-resident .resident-day-heading,
    html.theme-light body.role-resident .resident-ticket-section-head h3,
    html.theme-light body.role-resident .resident-ticket-create-title,
    html.theme-light body.role-resident .resident-ticket-create-panel-head h2,
    html.theme-light body.role-resident .resident-ticket-upload-head h3,
    html.theme-light body.role-resident .resident-ticket-priority-name,
    html.theme-light body.role-resident .resident-booking-create-title,
    html.theme-light body.role-resident .resident-booking-create-head h2,
    html.theme-light body.role-resident .community-feed-title,
    html.theme-light body.role-resident .community-section-head h2,
    html.theme-light body.role-resident .community-review-top h3,
    html.theme-light body.role-resident .community-feed-copy h3,
    html.theme-light body.role-resident .community-feed-author-copy strong,
    html.theme-light body.role-resident .concern-title,
    html.theme-light body.role-resident .concern-card-head h2 {
        color: var(--resident-light-title) !important;
    }

    html.theme-light body.role-resident .resident-home-subtitle,
    html.theme-light body.role-resident .resident-page-subtitle,
    html.theme-light body.role-resident .resident-ticket-create-subtitle,
    html.theme-light body.role-resident .resident-booking-create-subtitle,
    html.theme-light body.role-resident .resident-ticket-subtitle,
    html.theme-light body.role-resident .resident-booking-subtitle,
    html.theme-light body.role-resident .community-feed-subtitle,
    html.theme-light body.role-resident .concern-subtitle,
    html.theme-light body.role-resident .resident-surface-head p,
    html.theme-light body.role-resident .resident-stack-item-row p,
    html.theme-light body.role-resident .resident-notice-card p,
    html.theme-light body.role-resident .resident-community-entry p,
    html.theme-light body.role-resident .resident-card-description,
    html.theme-light body.role-resident .resident-page-panel-head p,
    html.theme-light body.role-resident .resident-ticket-create-panel-head p,
    html.theme-light body.role-resident .resident-ticket-priority-copy,
    html.theme-light body.role-resident .resident-ticket-upload-head p,
    html.theme-light body.role-resident .resident-ticket-upload-note,
    html.theme-light body.role-resident .resident-booking-create-head p,
    html.theme-light body.role-resident .resident-booking-create-help,
    html.theme-light body.role-resident .community-section-head p,
    html.theme-light body.role-resident .community-review-top p,
    html.theme-light body.role-resident .community-feed-copy p,
    html.theme-light body.role-resident .community-feed-author-copy span,
    html.theme-light body.role-resident .community-feed-stats-row,
    html.theme-light body.role-resident .concern-card-head p {
        color: var(--resident-light-body) !important;
    }

    html.theme-light body.role-resident .resident-stack-meta,
    html.theme-light body.role-resident .resident-community-entry-time,
    html.theme-light body.role-resident .resident-meta-box span,
    html.theme-light body.role-resident .community-review-meta {
        color: var(--resident-light-muted) !important;
    }

    html.theme-light body.role-resident .resident-home-kicker,
    html.theme-light body.role-resident .resident-page-kicker,
    html.theme-light body.role-resident .resident-ticket-create-kicker,
    html.theme-light body.role-resident .resident-booking-create-kicker,
    html.theme-light body.role-resident .resident-ticket-kicker,
    html.theme-light body.role-resident .resident-booking-kicker,
    html.theme-light body.role-resident .community-feed-kicker,
    html.theme-light body.role-resident .concern-kicker,
    html.theme-light body.role-resident .resident-surface-head a,
    html.theme-light body.role-resident .resident-see-more-btn,
    html.theme-light body.role-resident .resident-empty-state a,
    html.theme-light body.role-resident .community-empty-state a,
    html.theme-light body.role-resident .community-review-actions a,
    html.theme-light body.role-resident .community-review-actions button,
    html.theme-light body.role-resident .resident-card-links a,
    html.theme-light body.role-resident .resident-card-links button {
        color: var(--resident-light-accent) !important;
    }

    html.theme-light body.role-resident .resident-home-btn-primary,
    html.theme-light body.role-resident .resident-page-btn-primary,
    html.theme-light body.role-resident .resident-ticket-create-btn-primary,
    html.theme-light body.role-resident .resident-booking-create-btn-primary,
    html.theme-light body.role-resident .concern-btn-primary,
    html.theme-light body.role-resident .community-composer-actions a:not(.community-composer-secondary-action),
    html.theme-light body.role-resident .community-pagination-link a {
        background: linear-gradient(135deg, #a66d24 0%, #c98a35 100%) !important;
        border-color: rgba(111,78,45,0.30) !important;
        color: #fff7ea !important;
        box-shadow: 0 12px 24px rgba(111,78,45,0.18) !important;
    }

    html.theme-light body.role-resident .resident-home-btn-secondary,
    html.theme-light body.role-resident .resident-page-btn-secondary,
    html.theme-light body.role-resident .resident-ticket-create-btn-secondary,
    html.theme-light body.role-resident .resident-booking-create-btn-secondary,
    html.theme-light body.role-resident .concern-btn-secondary,
    html.theme-light body.role-resident .community-composer-secondary-action,
    html.theme-light body.role-resident .role-action-btn {
        background: rgba(226,208,184,0.92) !important;
        border-color: var(--resident-light-border) !important;
        color: var(--resident-light-title) !important;
    }

    html.theme-light body.role-resident .resident-activity-card-icon,
    html.theme-light body.role-resident .resident-stack-item-icon {
        background: rgba(143,95,34,0.18) !important;
        border-color: rgba(111,78,45,0.24) !important;
        color: var(--resident-light-accent) !important;
    }

    html.theme-light body.role-resident .resident-activity-card-gold .resident-activity-card-icon {
        background: linear-gradient(135deg, #a66d24 0%, #c98a35 100%) !important;
        color: #fff7ea !important;
    }

    html.theme-light body.role-resident .resident-filter-input,
    html.theme-light body.role-resident .resident-filter-select,
    html.theme-light body.role-resident .community-filter-input,
    html.theme-light body.role-resident .community-filter-select,
    html.theme-light body.role-resident .resident-ticket-create-input,
    html.theme-light body.role-resident .resident-ticket-create-input-file,
    html.theme-light body.role-resident .resident-booking-create-input,
    html.theme-light body.role-resident .concern-input,
    html.theme-light body.role-resident .community-post-input,
    html.theme-light body.role-resident .community-comment-input {
        background: rgba(246,237,224,0.92) !important;
        border-color: var(--resident-light-border) !important;
        color: var(--resident-light-title) !important;
    }

    html.theme-light body.role-resident .resident-surface-divider,
    html.theme-light body.role-resident .resident-page-divider,
    html.theme-light body.role-resident .resident-ticket-create-divider,
    html.theme-light body.role-resident .resident-booking-create-divider,
    html.theme-light body.role-resident .community-post-divider {
        background: linear-gradient(to right, rgba(111,78,45,0.32), rgba(111,78,45,0.10), transparent) !important;
    }

    html.theme-light body.role-resident .resident-status-chip-completed,
    html.theme-light body.role-resident .resident-badge-status-approved,
    html.theme-light body.role-resident .resident-badge-status-completed {
        background: rgba(92,116,79,0.16) !important;
        color: #3f5d35 !important;
    }

    html.theme-light body.role-resident .resident-status-chip-in_progress,
    html.theme-light body.role-resident .resident-badge-status-assigned,
    html.theme-light body.role-resident .resident-badge-status-in_progress {
        background: rgba(143,95,34,0.18) !important;
        color: #6b461a !important;
    }

    @media (max-width: 640px) {
        body.role-resident .resident-page-hero,
        body.role-resident .resident-ticket-create-hero,
        body.role-resident .resident-booking-create-hero,
        body.role-resident .resident-ticket-hero,
        body.role-resident .resident-booking-hero,
        body.role-resident .community-feed-hero,
        body.role-resident .community-post-hero,
        body.role-resident .concern-hero {
            padding: 16px !important;
            align-items: flex-start !important;
            flex-direction: column !important;
        }

        body.role-resident .concern-hero-actions {
            width: 100% !important;
            min-width: 0 !important;
            flex: none !important;
        }

        body.role-resident .resident-page-title,
        body.role-resident .resident-ticket-create-title,
        body.role-resident .resident-booking-create-title,
        body.role-resident .resident-ticket-title,
        body.role-resident .resident-booking-title,
        body.role-resident .community-feed-title,
        body.role-resident .community-post-title,
        body.role-resident .concern-title {
            font-size: clamp(1.55rem, 7vw, 2rem) !important;
        }
    }

    /* Late admin-only theme pass. Keep dark surfaces readable while the admin shell uses a cream background. */
    body.role-manager .admin-content-shell {
        --admin-card: #fffdf9;
        --admin-card-strong: #ffffff;
        --admin-card-border: rgba(52, 49, 44, 0.42);
        --admin-dark-card: #34312c;
        --admin-dark-card-2: #2d2a26;
        --admin-dark-border: rgba(214,168,91,0.16);
        --admin-gold: #b9822f;
        --admin-gold-soft: rgba(185,130,47,0.14);
        --admin-hero: linear-gradient(120deg, #34312c 0%, #3d3932 52%, #2d2a26 100%);
        --admin-hero-border: rgba(185,130,47,0.22);
        --admin-hero-grid: rgba(255,244,225,0.035);
        --admin-hero-title: #f8f3ea;
        --admin-hero-muted: #d2c5b5;
        --admin-surface: linear-gradient(180deg, var(--admin-dark-card) 0%, var(--admin-dark-card-2) 100%);
        --admin-surface-soft: linear-gradient(180deg, #3d3932 0%, #34312c 100%);
        --admin-surface-inner: rgba(255,255,255,0.04);
        --admin-surface-border: var(--admin-dark-border);
        --admin-surface-border-soft: rgba(255,255,255,0.07);
        --admin-surface-title: #f8f3ea;
        --admin-surface-body: #d8cbbb;
        --admin-surface-muted: #b6a896;
        --admin-page-title: #2f251d;
        --admin-page-body: #5d4a3a;
        --admin-page-muted: #806a55;
    }

    body.role-manager .admin-content-shell > .space-y-6 > :not([style*="background"]):not(.rounded-lg):not(.rounded-xl) h1,
    body.role-manager .admin-content-shell > .space-y-8 > :not([style*="background"]):not(.rounded-lg):not(.rounded-xl) h1,
    body.role-manager .admin-content-shell > .space-y-6 > :not([style*="background"]):not(.rounded-lg):not(.rounded-xl) h2,
    body.role-manager .admin-content-shell > .space-y-8 > :not([style*="background"]):not(.rounded-lg):not(.rounded-xl) h2,
    body.role-manager .admin-content-shell .section-title {
        color: var(--admin-page-title) !important;
    }

    body.role-manager .admin-content-shell > .space-y-6 > :not([style*="background"]):not(.rounded-lg):not(.rounded-xl) p,
    body.role-manager .admin-content-shell > .space-y-8 > :not([style*="background"]):not(.rounded-lg):not(.rounded-xl) p,
    body.role-manager .admin-content-shell .section-sub {
        color: var(--admin-page-muted) !important;
    }

    body.role-manager .admin-panel-card,
    body.role-manager .admin-main-panel,
    body.role-manager .admin-form-panel,
    body.role-manager .admin-ticket-panel,
    body.role-manager .admin-ticket-show-panel,
    body.role-manager .admin-concern-card,
    body.role-manager .admin-concern-stat,
    body.role-manager .booking-panel,
    body.role-manager .chart-card,
    body.role-manager .panel,
    body.role-manager .admin-content-shell > .space-y-6 > .rounded-lg,
    body.role-manager .admin-content-shell > .space-y-6 > .rounded-xl,
    body.role-manager .admin-content-shell > .space-y-8 > .rounded-lg,
    body.role-manager .admin-content-shell > .space-y-8 > .rounded-xl,
    body.role-manager .admin-content-shell > .space-y-6 > div[style*="background: #1F2023"],
    body.role-manager .admin-content-shell > .space-y-6 > div[style*="background: #2A2C30"],
    body.role-manager .admin-content-shell > .space-y-8 > div[style*="background: #1F2023"],
    body.role-manager .admin-content-shell > .space-y-8 > div[style*="background: #2A2C30"],
    body.role-manager .admin-content-shell > .space-y-6 > div[style*="linear-gradient(180deg, #2A2C30"],
    body.role-manager .admin-content-shell > .space-y-8 > div[style*="linear-gradient(180deg, #2A2C30"] {
        background: var(--admin-surface) !important;
        border-color: var(--admin-surface-border) !important;
        color: var(--admin-surface-body) !important;
        box-shadow: 0 18px 36px rgba(72,48,24,0.16) !important;
    }

    body.role-manager .booking-dashboard .stat-card,
    body.role-manager .metric-card,
    body.role-manager .admin-concern-stat,
    body.role-manager .admin-user-stat,
    body.role-manager .announcement-standards-panel,
    body.role-manager .admin-feature-stat-grid > div {
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

    body.role-manager .metric-card,
    body.role-manager .admin-metric-card,
    body.role-manager .booking-dashboard .stat-card,
    body.role-manager .admin-concern-stat,
    body.role-manager .admin-user-stat,
    body.role-manager .admin-feature-stat-grid > div {
        min-height: 92px !important;
        padding: 18px 20px !important;
        border-radius: 16px !important;
        gap: 8px !important;
    }

    body.role-manager .booking-dashboard .stat-card,
    body.role-manager .admin-concern-stat,
    body.role-manager .admin-user-stat,
    body.role-manager .admin-feature-stat-grid > div {
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

    body.role-manager .stat-card-main .stat-label,
    body.role-manager .stat-card-main span {
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
        border-color: rgba(52,49,44,0.56) !important;
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

    body.role-manager .admin-feature-stat-grid [class*="w-11"],
    body.role-manager .admin-feature-stat-grid [class*="w-12"] {
        width: 44px !important;
        height: 44px !important;
        border-radius: 12px !important;
        background: rgba(52,49,44,0.08) !important;
        border: 1px solid rgba(52,49,44,0.10) !important;
    }

    body.role-manager .admin-feature-stat-grid svg {
        width: 20px !important;
        height: 20px !important;
        color: #9b6b25 !important;
    }

    body.role-manager .admin-panel-card h2,
    body.role-manager .admin-panel-card h3,
    body.role-manager .admin-main-panel h2,
    body.role-manager .admin-main-panel h3,
    body.role-manager .admin-ticket-panel h2,
    body.role-manager .admin-ticket-panel h3,
    body.role-manager .admin-ticket-show-panel h2,
    body.role-manager .admin-ticket-show-panel h3,
    body.role-manager .admin-concern-card h2,
    body.role-manager .admin-concern-card h3,
    body.role-manager .booking-panel h2,
    body.role-manager .booking-panel h3,
    body.role-manager .metric-card .metric-value,
    body.role-manager .booking-dashboard .stat-card .stat-value,
    body.role-manager .admin-concern-stat strong,
    body.role-manager .admin-user-stat .text-xl,
    body.role-manager .admin-metric-value,
    body.role-manager .admin-metric-value-alert,
    body.role-manager .admin-metric-value-success,
    body.role-manager .announcement-standards-panel div,
    body.role-manager .announcement-standards-panel strong,
    body.role-manager .announcement-standards-panel .font-bold,
    body.role-manager .announcement-standards-panel .font-semibold,
    body.role-manager .announcement-standards-panel [style*="font-weight: 700"],
    body.role-manager .chart-card .chart-title,
    body.role-manager .panel .panel-title,
    body.role-manager .admin-status-card strong,
    body.role-manager .admin-ticket-info-card strong,
    body.role-manager .admin-ticket-summary-row strong,
    body.role-manager .admin-detail-item strong,
    body.role-manager .admin-meta-value,
    body.role-manager .summary-row h3,
    body.role-manager .history-row h3,
    body.role-manager .admin-content-shell table .text-white,
    body.role-manager .admin-content-shell table td,
    body.role-manager .admin-content-shell table .font-medium {
        color: var(--admin-surface-title) !important;
    }

    body.role-manager .metric-card .metric-value,
    body.role-manager .booking-dashboard .stat-card .stat-value,
    body.role-manager .admin-concern-stat strong,
    body.role-manager .admin-user-stat .text-xl,
    body.role-manager .admin-metric-value,
    body.role-manager .admin-metric-value-alert,
    body.role-manager .admin-metric-value-success,
    body.role-manager .announcement-standards-panel div,
    body.role-manager .announcement-standards-panel strong,
    body.role-manager .announcement-standards-panel .font-bold,
    body.role-manager .announcement-standards-panel .font-semibold,
    body.role-manager .announcement-standards-panel [style*="font-weight: 700"] {
        color: var(--admin-page-title) !important;
    }

    body.role-manager .admin-panel-card p,
    body.role-manager .admin-main-panel p,
    body.role-manager .admin-ticket-panel p,
    body.role-manager .admin-ticket-show-panel p,
    body.role-manager .admin-concern-card p,
    body.role-manager .booking-panel p,
    body.role-manager .metric-card .metric-label,
    body.role-manager .metric-card .metric-sub,
    body.role-manager .booking-dashboard .stat-card .stat-label,
    body.role-manager .booking-dashboard .stat-card .stat-note,
    body.role-manager .admin-concern-stat span,
    body.role-manager .admin-user-stat .text-xs,
    body.role-manager .admin-metric-label,
    body.role-manager .admin-metric-sub,
    body.role-manager .admin-metric-sub-alert,
    body.role-manager .announcement-standards-panel p,
    body.role-manager .announcement-standards-panel span,
    body.role-manager .chart-card .chart-desc,
    body.role-manager .chart-card .chart-legend,
    body.role-manager .panel .panel-sub,
    body.role-manager .panel .list-item-meta,
    body.role-manager .panel .empty-state,
    body.role-manager .admin-ticket-panel-sub,
    body.role-manager .admin-status-card p,
    body.role-manager .admin-status-meta,
    body.role-manager .admin-ticket-info-card span,
    body.role-manager .admin-ticket-info-card p,
    body.role-manager .admin-ticket-summary-row span,
    body.role-manager .admin-detail-item span,
    body.role-manager .admin-meta-label,
    body.role-manager .summary-row p,
    body.role-manager .history-row p,
    body.role-manager .empty-copy,
    body.role-manager .booking-chip-time,
    body.role-manager .calendar-legend-item,
    body.role-manager .time-cell,
    body.role-manager .admin-content-shell table .text-gray-300,
    body.role-manager .admin-content-shell table .text-gray-400,
    body.role-manager .admin-content-shell table .text-gray-500 {
        color: var(--admin-surface-muted) !important;
    }

    body.role-manager .metric-card .metric-label,
    body.role-manager .metric-card .metric-sub,
    body.role-manager .booking-dashboard .stat-card .stat-label,
    body.role-manager .booking-dashboard .stat-card .stat-note,
    body.role-manager .admin-concern-stat span,
    body.role-manager .admin-user-stat .text-xs,
    body.role-manager .admin-metric-label,
    body.role-manager .admin-metric-sub,
    body.role-manager .admin-metric-sub-alert,
    body.role-manager .announcement-standards-panel p,
    body.role-manager .announcement-standards-panel span {
        color: var(--admin-page-muted) !important;
    }

    body.role-manager .admin-metric-icon,
    body.role-manager .admin-metric-icon-alert,
    body.role-manager .admin-metric-icon-success,
    body.role-manager .metric-icon,
    body.role-manager .admin-user-stat-icon {
        background: rgba(52,49,44,0.08) !important;
        border: 1px solid rgba(52,49,44,0.10) !important;
        color: #9b6b25 !important;
    }

    body.role-manager .metric-card .metric-value,
    body.role-manager .admin-metric-value,
    body.role-manager .booking-dashboard .stat-card .stat-value,
    body.role-manager .admin-concern-stat strong,
    body.role-manager .admin-user-stat .text-xl,
    body.role-manager .admin-feature-stat-grid [class*="text-4xl"],
    body.role-manager .admin-feature-stat-grid [class*="text-[34px]"],
    body.role-manager .admin-community-page .admin-feature-stat-grid > div > div:first-child > div:first-child,
    body.role-manager .admin-concern-stat .stat-card-main strong {
        color: #332c24 !important;
        font-weight: 800 !important;
        letter-spacing: 0 !important;
        font-size: 2.35rem !important;
        line-height: 1 !important;
    }

    body.role-manager .metric-card .metric-label,
    body.role-manager .admin-metric-label,
    body.role-manager .booking-dashboard .stat-card .stat-label,
    body.role-manager .admin-concern-stat span,
    body.role-manager .admin-user-stat .text-xs,
    body.role-manager .admin-feature-stat-grid .font-semibold.uppercase,
    body.role-manager .admin-community-page .admin-feature-stat-grid > div > div:nth-child(2),
    body.role-manager .admin-concern-stat .stat-card-main span {
        color: #725f4c !important;
        font-size: 0.78rem !important;
        font-weight: 650 !important;
        letter-spacing: 0.09em !important;
        text-transform: uppercase !important;
    }

    body.role-manager .metric-card .metric-sub,
    body.role-manager .admin-metric-sub,
    body.role-manager .admin-metric-sub-alert,
    body.role-manager .booking-dashboard .stat-card .stat-note,
    body.role-manager .admin-feature-stat-grid .text-xs.mt-1,
    body.role-manager .admin-community-page .admin-feature-stat-grid > div > div:nth-child(3),
    body.role-manager .admin-concern-stat small {
        color: #8a7a68 !important;
        font-size: 0.86rem !important;
        line-height: 1.35 !important;
    }

    body.role-manager .admin-metric-card-alert .admin-metric-icon {
        color: #a0681f !important;
        background: rgba(185,130,47,0.12) !important;
        border-color: rgba(185,130,47,0.18) !important;
    }

    body.role-manager .admin-metric-card-success .admin-metric-icon {
        color: #5c744f !important;
        background: rgba(92,116,79,0.12) !important;
        border-color: rgba(92,116,79,0.18) !important;
    }


    body.role-manager .admin-status-card,
    body.role-manager .admin-ticket-info-card,
    body.role-manager .admin-ticket-summary-row,
    body.role-manager .admin-ticket-note,
    body.role-manager .admin-detail-item,
    body.role-manager .admin-detail-block,
    body.role-manager .admin-meta-item,
    body.role-manager .summary-row,
    body.role-manager .history-row,
    body.role-manager .calendar-legend,
    body.role-manager .week-day,
    body.role-manager .empty-slot,
    body.role-manager .booking-chip,
    body.role-manager .admin-concern-row,
    body.role-manager .admin-content-shell tbody tr {
        background: var(--admin-surface-inner) !important;
        border-color: var(--admin-surface-border-soft) !important;
    }

    body.role-manager .admin-content-shell input,
    body.role-manager .admin-content-shell select,
    body.role-manager .admin-content-shell textarea,
    body.role-manager .admin-input,
    body.role-manager .admin-filter-select,
    body.role-manager .admin-ticket-form select,
    body.role-manager .admin-ticket-form textarea,
    body.role-manager .admin-form-input,
    body.role-manager .date-picker-label,
    body.role-manager .mode-pill,
    body.role-manager .nav-pill,
    body.role-manager .date-badge {
        background: rgba(48,45,40,0.96) !important;
        border-color: rgba(185,130,47,0.20) !important;
        color: var(--admin-surface-title) !important;
    }

    body.role-manager .admin-content-shell label,
    body.role-manager .admin-label,
    body.role-manager .admin-ticket-form label {
        color: var(--admin-surface-body) !important;
    }

    body.role-manager .admin-secondary-btn,
    body.role-manager .admin-ticket-show-back,
    body.role-manager .admin-ticket-action-secondary,
    body.role-manager .admin-status-link {
        background: rgba(255,255,255,0.055) !important;
        border-color: rgba(214,168,91,0.18) !important;
        color: var(--admin-surface-title) !important;
    }

    body.role-manager .admin-content-shell .filter-tab:not(.active) {
        color: var(--admin-page-body) !important;
    }

    body.role-manager .admin-content-shell .filter-tab.active {
        color: #6f461b !important;
        background: var(--admin-gold-soft) !important;
        border-color: rgba(185,130,47,0.24) !important;
    }

    body.role-manager .admin-concern-hero,
    body.role-manager .admin-ticket-show-hero,
    body.role-manager .dash-hero,
    body.role-manager .admin-shell > div:first-of-type,
    body.role-manager .admin-ticket-page > div:first-of-type,
    body.role-manager .booking-hero {
        background: var(--admin-hero) !important;
        border-color: var(--admin-hero-border) !important;
        color: var(--admin-surface-title) !important;
        box-shadow: 0 18px 38px rgba(72,48,24,0.14) !important;
    }

    body.role-manager .admin-concern-hero::before,
    body.role-manager .admin-ticket-show-hero::before,
    body.role-manager .dash-hero .hero-grid-overlay,
    body.role-manager .admin-shell > div:first-of-type::before,
    body.role-manager .admin-ticket-page > div:first-of-type::before,
    body.role-manager .booking-hero::before {
        background-image:
            linear-gradient(var(--admin-hero-grid) 1px, transparent 1px),
            linear-gradient(90deg, var(--admin-hero-grid) 1px, transparent 1px) !important;
        background-size: 64px 64px !important;
    }

    body.role-manager .admin-concern-hero h1,
    body.role-manager .admin-ticket-show-hero h1,
    body.role-manager .dash-hero .hero-title,
    body.role-manager .admin-shell > div:first-of-type h1,
    body.role-manager .admin-ticket-page > div:first-of-type h1,
    body.role-manager .booking-hero h1,
    body.role-manager .admin-concern-title,
    body.role-manager .admin-ticket-show-title,
    body.role-manager .booking-title {
        color: var(--admin-hero-title) !important;
    }

    body.role-manager .admin-concern-hero p:not(.admin-concern-kicker),
    body.role-manager .admin-ticket-show-hero p:not(.admin-ticket-show-kicker),
    body.role-manager .dash-hero .hero-sub,
    body.role-manager .admin-shell > div:first-of-type p,
    body.role-manager .admin-ticket-page > div:first-of-type p,
    body.role-manager .booking-hero p:not(.booking-kicker) {
        color: var(--admin-hero-muted) !important;
    }

    body.role-manager .admin-ticket-page > div:first-of-type,
    body.role-manager .admin-announcements-page > div:first-of-type,
    body.role-manager .admin-community-page > div:first-of-type {
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

    body.role-manager .admin-ticket-page > div:first-of-type > div.relative,
    body.role-manager .admin-announcements-page > div:first-of-type > div.relative,
    body.role-manager .admin-community-page > div:first-of-type > div.relative {
        padding: 36px 44px !important;
        width: 100% !important;
    }

    body.role-manager .admin-announcements-page > div:first-of-type > div.relative > div,
    body.role-manager .admin-community-page > div:first-of-type > div.relative > div,
    body.role-manager .admin-ticket-page > div:first-of-type > div.relative > div {
        width: 100% !important;
        align-items: center !important;
    }

    body.role-manager .admin-ticket-page > div:first-of-type > *,
    body.role-manager .admin-announcements-page > div:first-of-type > *,
    body.role-manager .admin-community-page > div:first-of-type > * {
        text-align: left !important;
    }

    body.role-manager .admin-ticket-page > div:first-of-type h1,
    body.role-manager .admin-announcements-page > div:first-of-type h1,
    body.role-manager .admin-community-page > div:first-of-type h1 {
        font-size: clamp(2rem, 2.75vw, 2.65rem) !important;
        line-height: 1.08 !important;
        margin-bottom: 10px !important;
        letter-spacing: 0 !important;
    }

    body.role-manager .admin-ticket-page > div:first-of-type p,
    body.role-manager .admin-announcements-page > div:first-of-type p,
    body.role-manager .admin-community-page > div:first-of-type p {
        font-size: 1rem !important;
        line-height: 1.48 !important;
        max-width: 760px !important;
    }

    body.role-manager .admin-announcements-page > div:first-of-type .shrink-0,
    body.role-manager .admin-community-page > div:first-of-type .shrink-0,
    body.role-manager .admin-ticket-page > div:first-of-type .shrink-0 {
        margin-left: auto !important;
        align-self: center !important;
    }

    body.role-manager .admin-announcements-page > div:first-of-type .shrink-0 a {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        white-space: nowrap !important;
    }

    @media (max-width: 768px) {
        body.role-manager .admin-ticket-page > div:first-of-type,
        body.role-manager .admin-announcements-page > div:first-of-type,
        body.role-manager .admin-community-page > div:first-of-type {
            min-height: 0 !important;
        }

        body.role-manager .admin-ticket-page > div:first-of-type {
            padding: 24px !important;
            align-items: flex-start !important;
            flex-direction: column !important;
        }

        body.role-manager .admin-announcements-page > div:first-of-type > div.relative,
        body.role-manager .admin-community-page > div:first-of-type > div.relative {
            padding: 24px !important;
        }

        body.role-manager .admin-ticket-page > div:first-of-type h1,
        body.role-manager .admin-announcements-page > div:first-of-type h1,
        body.role-manager .admin-community-page > div:first-of-type h1 {
            font-size: clamp(2rem, 10vw, 2.65rem) !important;
        }
    }

    body.role-manager .admin-shell > div[style*="#2A2C30"] h2,
    body.role-manager .admin-shell > div[style*="#1F2023"] h2,
    body.role-manager .admin-shell > div[style*="#2C2C2F"] h2,
    body.role-manager .admin-shell > div[style*="#25272A"] h2,
    body.role-manager .admin-shell > div[style*="#2A2C30"] h3,
    body.role-manager .admin-shell > div[style*="#1F2023"] h3,
    body.role-manager .admin-shell > div[style*="#2C2C2F"] h3,
    body.role-manager .admin-shell > div[style*="#25272A"] h3,
    body.role-manager .admin-shell div[style*="#2C2C2F"] h3,
    body.role-manager .admin-shell div[style*="#25272A"] h3 {
        color: var(--admin-surface-title) !important;
    }

    body.role-manager .admin-shell > div[style*="#2A2C30"] p,
    body.role-manager .admin-shell > div[style*="#1F2023"] p,
    body.role-manager .admin-shell > div[style*="#2C2C2F"] p,
    body.role-manager .admin-shell > div[style*="#25272A"] p,
    body.role-manager .admin-shell div[style*="#2C2C2F"] div,
    body.role-manager .admin-shell div[style*="#25272A"] div {
        color: var(--admin-surface-muted) !important;
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

    document.querySelectorAll('[data-toast]').forEach((toast) => {
        const close = () => {
            toast.classList.add('is-leaving');
            setTimeout(() => toast.remove(), 240);
        };

        toast.querySelector('[data-toast-close]')?.addEventListener('click', close);
        setTimeout(close, 4200);
    });

    window.addEventListener('load', () => {
        document.documentElement.classList.remove('is-loading');
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
            pendingForm = null;
        };

        document.addEventListener('submit', (event) => {
            const form = event.target;

            if (!(form instanceof HTMLFormElement) || !form.dataset.confirmMessage || form.dataset.confirmed === 'true') {
                return;
            }

            event.preventDefault();
            pendingForm = form;
            messageNode.textContent = form.dataset.confirmMessage;
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

    (function () {
        const overlay = document.querySelector('[data-loading-overlay]');

        if (!overlay) {
            return;
        }

        const showLoader = () => {
            overlay.classList.add('is-active');
        };

        document.addEventListener('click', (event) => {
            const link = event.target.closest('a[href]');

            if (!link || link.target || link.hasAttribute('download') || link.href.startsWith('javascript:')) {
                return;
            }

            const url = new URL(link.href, window.location.href);
            if (url.origin === window.location.origin && url.href !== window.location.href) {
                showLoader();
            }
        });

        document.addEventListener('submit', (event) => {
            const form = event.target;
            if (form instanceof HTMLFormElement && !form.classList.contains('community-like-form')) {
                setTimeout(showLoader, 80);
            }
        });

        window.addEventListener('pageshow', () => {
            overlay.classList.remove('is-active');
        });
    })();

    document.querySelectorAll('form input[required], form select[required], form textarea[required]').forEach((field) => {
        const messageFor = () => {
            if (field.validity.valueMissing) {
                return 'This field is required.';
            }

            if (field.validity.tooShort) {
                return `Please enter at least ${field.minLength} characters.`;
            }

            if (field.validity.typeMismatch) {
                return 'Please enter a valid value.';
            }

            return '';
        };

        const render = () => {
            let error = field.parentElement?.querySelector(`.app-field-error[data-for="${field.id || field.name}"]`);
            const message = messageFor();

            if (!message) {
                error?.remove();
                return;
            }

            if (!error) {
                error = document.createElement('span');
                error.className = 'app-field-error';
                error.dataset.for = field.id || field.name;
                field.insertAdjacentElement('afterend', error);
            }

            error.textContent = message;
        };

        field.addEventListener('blur', render);
        field.addEventListener('input', () => {
            if (field.parentElement?.querySelector('.app-field-error')) {
                render();
            }
        });
        field.addEventListener('change', render);
    });

    document.addEventListener('submit', function (event) {
        const form = event.target;

        if (!(form instanceof HTMLFormElement) || !form.hasAttribute('data-prevent-double-submit')) {
            return;
        }

        if (form.dataset.submitting === 'true') {
            event.preventDefault();
            return;
        }

        form.dataset.submitting = 'true';

        const submittingText = form.getAttribute('data-submitting-text') || 'Submitting...';
        const submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');

        submitButtons.forEach((button) => {
            if (!button.dataset.originalLabel) {
                button.dataset.originalLabel = button.tagName === 'INPUT' ? button.value : button.innerHTML;
            }

            button.disabled = true;

            if (button.tagName === 'INPUT') {
                button.value = submittingText;
            } else {
                button.innerHTML = submittingText;
            }

            button.style.opacity = '0.7';
            button.style.cursor = 'not-allowed';
        });
    }, true);
</script>
</body>
</html>
