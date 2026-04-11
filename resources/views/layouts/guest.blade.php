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

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --guest-border: rgba(214, 168, 91, 0.16);
                --guest-border-soft: rgba(255, 255, 255, 0.08);
                --guest-gold: #d6a85b;
                --guest-text: #f3ece2;
                --guest-text-soft: #c8b9a4;
                --guest-text-muted: #8f7d67;
            }

            * {
                box-sizing: border-box;
            }

            html,
            body {
                margin: 0;
                min-height: 100%;
                font-family: 'Inter', sans-serif;
                color: var(--guest-text);
                background:
                    radial-gradient(circle at top right, rgba(214, 168, 91, 0.15), transparent 25%),
                    radial-gradient(circle at bottom left, rgba(142, 110, 63, 0.1), transparent 26%),
                    linear-gradient(180deg, #181512 0%, #141210 100%);
            }

            body {
                min-height: 100vh;
                position: relative;
                overflow-x: hidden;
            }

            body::before {
                content: "";
                position: fixed;
                inset: 0;
                pointer-events: none;
                background:
                    linear-gradient(90deg, rgba(12, 10, 8, 0.78) 0%, rgba(12, 10, 8, 0.36) 42%, rgba(12, 10, 8, 0.14) 100%),
                    url('{{ asset('1.1.png') }}') top center / cover no-repeat;
                opacity: 0.48;
                mask-image: linear-gradient(to bottom, black 0%, rgba(0, 0, 0, 0.86) 34%, transparent 100%);
            }

            .guest-auth-shell {
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 28px 18px;
                position: relative;
                z-index: 1;
            }

            .guest-auth-card {
                width: min(1120px, 100%);
                display: grid;
                grid-template-columns: minmax(0, 0.95fr) minmax(360px, 0.9fr);
                border-radius: 34px;
                overflow: hidden;
                border: 1px solid var(--guest-border);
                background: rgba(19, 17, 15, 0.92);
                box-shadow: 0 24px 70px rgba(0, 0, 0, 0.32);
                backdrop-filter: blur(16px);
            }

            .guest-auth-brand {
                position: relative;
                padding: 44px 42px;
                border-right: 1px solid var(--guest-border-soft);
                background:
                    linear-gradient(145deg, rgba(29, 28, 26, 0.94) 0%, rgba(20, 20, 18, 0.7) 58%, rgba(46, 37, 26, 0.88) 100%);
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                gap: 28px;
            }

            .guest-auth-brand::before,
            .guest-auth-brand::after {
                content: "";
                position: absolute;
                border-radius: 999px;
                filter: blur(56px);
                pointer-events: none;
            }

            .guest-auth-brand::before {
                width: 280px;
                height: 280px;
                top: -90px;
                right: -40px;
                background: rgba(214, 168, 91, 0.18);
            }

            .guest-auth-brand::after {
                width: 210px;
                height: 210px;
                bottom: -70px;
                left: 10%;
                background: rgba(255, 255, 255, 0.05);
            }

            .guest-brand-main,
            .guest-brand-bottom {
                position: relative;
                z-index: 1;
            }

            .guest-brand-mark {
                display: inline-block;
                margin-bottom: 14px;
                color: var(--guest-gold);
                font-size: 0.76rem;
                font-weight: 700;
                letter-spacing: 0.3em;
                text-transform: uppercase;
            }

            .guest-brand-title {
                margin: 0;
                font-family: 'Playfair Display', serif;
                font-size: clamp(2.8rem, 4vw, 4.4rem);
                line-height: 0.95;
                color: var(--guest-text);
            }

            .guest-brand-title span {
                color: #f0d8af;
            }

            .guest-brand-subtitle {
                margin: 18px 0 0;
                max-width: 480px;
                color: var(--guest-text-soft);
                font-size: 1rem;
                line-height: 1.8;
            }

            .guest-brand-stack {
                display: grid;
                gap: 12px;
                margin-top: 28px;
            }

            .guest-brand-line {
                display: flex;
                align-items: center;
                gap: 12px;
                color: #eadfce;
                font-size: 0.94rem;
                font-weight: 600;
            }

            .guest-brand-dot {
                width: 8px;
                height: 8px;
                border-radius: 999px;
                background: var(--guest-gold);
                box-shadow: 0 0 0 4px rgba(214, 168, 91, 0.12);
                flex-shrink: 0;
            }

            .guest-brand-bottom {
                padding-top: 24px;
                border-top: 1px solid rgba(255, 255, 255, 0.08);
            }

            .guest-brand-location {
                color: var(--guest-gold);
                font-size: 0.74rem;
                font-weight: 700;
                letter-spacing: 0.22em;
                text-transform: uppercase;
                margin-bottom: 10px;
            }

            .guest-brand-caption {
                max-width: 460px;
                color: var(--guest-text-muted);
                line-height: 1.75;
                font-size: 0.9rem;
            }

            .guest-auth-form-shell {
                background: linear-gradient(180deg, rgba(22, 20, 18, 0.95) 0%, rgba(19, 18, 16, 0.98) 100%);
                padding: 42px 38px;
                display: flex;
                align-items: center;
            }

            .guest-auth-form-shell > * {
                width: 100%;
            }

            @media (max-width: 980px) {
                .guest-auth-card {
                    grid-template-columns: 1fr;
                }

                .guest-auth-brand {
                    border-right: 0;
                    border-bottom: 1px solid var(--guest-border-soft);
                    padding: 34px 28px;
                }

                .guest-auth-form-shell {
                    padding: 30px 24px;
                }
            }

            @media (max-width: 640px) {
                .guest-auth-shell {
                    padding: 16px;
                }

                .guest-auth-card {
                    border-radius: 26px;
                }

                .guest-auth-brand,
                .guest-auth-form-shell {
                    padding-left: 20px;
                    padding-right: 20px;
                }
            }
        </style>
    </head>
    <body>
        <div class="guest-auth-shell">
            <div class="guest-auth-card">
                <section class="guest-auth-brand">
                    <div class="guest-brand-main">
                        <span class="guest-brand-mark">Rexhall Dorm System</span>
                        <h1 class="guest-brand-title">Hall<span>Sync</span></h1>
                        <p class="guest-brand-subtitle">
                            The dedicated system for Rexhall residents and staff to manage daily dorm life in one place.
                        </p>

                        <div class="guest-brand-stack">
                            <div class="guest-brand-line">
                                <span class="guest-brand-dot"></span>
                                <span>Maintenance concerns and room issues</span>
                            </div>
                            <div class="guest-brand-line">
                                <span class="guest-brand-dot"></span>
                                <span>Bookings for shared dorm spaces</span>
                            </div>
                            <div class="guest-brand-line">
                                <span class="guest-brand-dot"></span>
                                <span>Resident announcements and updates</span>
                            </div>
                        </div>
                    </div>

                    <div class="guest-brand-bottom">
                        <div class="guest-brand-location">Baguio City</div>
                        <div class="guest-brand-caption">
                            Built specifically for Rexhall so the experience stays focused on your dorm, your spaces, and your community.
                        </div>
                    </div>
                </section>

                <section class="guest-auth-form-shell">
                    {{ $slot }}
                </section>
            </div>
        </div>
        <script>
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
