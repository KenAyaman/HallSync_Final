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
            max-width: 1600px;
            padding-top: 18px;
        }

        /* ================= ADMIN (FIXED) ================= */
        .admin-main-content {
            width: 100%;
            min-height: 100vh;
            padding: 24px 24px 48px;

            /* 🔥 FIX: SAME AS RESIDENT */
            background: var(--app-bg);

            position: relative;
            z-index: 2;
        }

        .admin-content-shell {
            width: 100%;
            max-width: 1580px;
            margin: 0 auto;
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
            flex-shrink: 0;
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
            flex-shrink: 0;
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
        }
    </style>
@php
    $isDashboard = request()->routeIs('dashboard');
    $showHero = Auth::check();
    $role = Auth::check() ? Auth::user()->role : 'guest';
@endphp

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
        <main class="app-main app-main-handyman {{ $isDashboard ? 'full-bleed' : '' }}">
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

</body>
</html>
