<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'HallSync') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
:root {
    --guest-accent: #d4a44c;
    --guest-accent-deep: #b7822d;
    --guest-ink: #302a23;
    --guest-body: #71685d;
    --guest-muted: #978c80;
    --guest-border: rgba(111, 95, 75, 0.16);
}
* {
    box-sizing: border-box;
}
html, body {
    margin: 0;
    min-height: 100%;
    font-family: 'Inter', sans-serif;
    color: var(--guest-ink);
    background: #28251f;
}
body {
    min-height: 100vh;
    overflow-x: hidden;
    position: relative;
}
body::before, body::after {
    content: "";
    inset: 0;
    pointer-events: none;
    position: fixed;
}
body::before {
    background:url('{{ asset('chair.png') }}') center / cover no-repeat;
    filter: blur(1.5px) saturate(0.96) brightness(1.04);
    inset: -8px;
    transform: scale(1.015);
}
body::after {
    background: radial-gradient(circle at top right, rgba(212, 164, 76, 0.12), transparent 32%), linear-gradient(135deg, rgba(24, 22, 19, 0.58), rgba(31, 29, 25, 0.38));
}
.guest-auth-shell {
    align-items: center;
    display: flex;
    justify-content: center;
    min-height: 100vh;
    padding: 28px 18px;
    position: relative;
    z-index: 1;
}
.guest-auth-card {
    animation: guest-card-enter 0.45s cubic-bezier(0.22, 1, 0.36, 1) both;
    background: rgba(255, 253, 249, 0.93);
    border: 1px solid rgba(255, 255, 255, 0.5);
    border-radius: 24px;
    box-shadow: 0 22px 60px rgba(10, 9, 7, 0.24);
    max-width: 460px;
    padding: 34px;
    width: 100%;
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
}
.rex-auth-shell {
    color: var(--guest-ink);
    margin: 0 auto;
    max-width: 460px;
    width: 100%;
}
.rex-auth-head {
    margin-bottom: 22px;
}
.rex-auth-kicker {
    color: var(--guest-accent-deep);
    display: inline-block;
    font-size: 0.7rem;
    font-weight: 800;
    letter-spacing: 0.18em;
    margin-bottom: 10px;
    text-transform: uppercase;
}
.rex-auth-title {
    color: var(--guest-ink);
    font-family: 'Playfair Display', serif;
    font-size: clamp(1.85rem, 4vw, 2.35rem);
    line-height: 1.06;
    margin: 0;
}
.rex-auth-subtitle {
    color: var(--guest-body);
    font-size: 0.88rem;
    line-height: 1.65;
    margin: 10px 0 0;
}
.rex-auth-status {
    background: #eff8f1;
    border: 1px solid #d5e8d9;
    border-radius: 10px;
    color: #397050;
    font-size: 0.8rem;
    line-height: 1.5;
    margin-bottom: 15px;
    padding: 11px 12px;
}
.rex-auth-form, .rex-auth-field {
    display: grid;
}
.rex-auth-form {
    gap: 16px;
}
.rex-auth-field {
    gap: 7px;
}
.rex-auth-label {
    color: #4c443b;
    font-size: 0.78rem;
    font-weight: 700;
}
.rex-auth-input {
    background: rgba(255, 255, 255, 0.76);
    border: 1px solid #ddd5c9;
    border-radius: 10px;
    color: #332d27;
    font: inherit;
    font-size: 0.88rem;
    min-height: 49px;
    outline: none;
    padding: 0 13px;
    transition: background 0.18s ease, border-color 0.18s ease, box-shadow 0.18s ease;
    width: 100%;
}
.rex-auth-input::placeholder {
    color: #aaa095;
}
.rex-auth-input:focus {
    background: #fff;
    border-color: #c3913c;
    box-shadow: 0 0 0 4px rgba(212, 164, 76, 0.14);
}
.rex-auth-password-wrap {
    position: relative;
}
.rex-auth-input-password {
    padding-right: 66px;
}
.rex-auth-password-toggle {
    background: transparent;
    border: 0;
    color: #996d22;
    cursor: pointer;
    font-size: 0.75rem;
    font-weight: 800;
    padding: 8px;
    position: absolute;
    right: 6px;
    top: 50%;
    transform: translateY(-50%);
}
.rex-auth-btn {
    background: linear-gradient(90deg, #bd8a34, var(--guest-accent));
    border: 0;
    border-radius: 10px;
    box-shadow: 0 10px 20px rgba(165, 114, 36, 0.2);
    color: #fff;
    cursor: pointer;
    font: inherit;
    font-size: 0.88rem;
    font-weight: 800;
    min-height: 50px;
    transition: box-shadow 0.18s ease, transform 0.18s ease;
}
.rex-auth-btn:hover {
    box-shadow: 0 14px 24px rgba(165, 114, 36, 0.28);
    transform: translateY(-1px);
}
.rex-auth-error {
    color: #a54942;
    font-size: 0.78rem;
}
.app-toast-stack {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    display: grid;
    gap: 10px;
    width: min(360px, calc(100vw - 32px));
    pointer-events: none;
}
.app-toast {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 15px 18px;
    border-radius: 12px;
    background: #3b82f6;
    color: #ffffff;
    box-shadow: 0 6px 24px rgba(59, 130, 246, 0.40);
    pointer-events: auto;
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
    line-height: 1.4;
}
.app-toast button {
    border: none;
    background: rgba(255, 255, 255, 0.22);
    color: #ffffff;
    cursor: pointer;
    font-size: 1rem;
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
.app-toast-error {
    background: #dc2626;
    box-shadow: 0 6px 24px rgba(220, 38, 38, 0.40);
}
.app-toast-error .app-toast-icon::after {
    content: '✕';
    font-style: normal;
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
    from {
        opacity: 1;
        transform: translateY(0);
    }
    to {
        opacity: 0;
        transform: translateY(-8px);
    }
}
@keyframes guest-card-enter {
    from {
        opacity: 0;
        transform: translateY(12px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
@media (prefers-reduced-motion:reduce) {
    .guest-auth-card {
        animation: none;
    }
}
@media (max-width:768px) {
    :root {
        --m-space-2: 8px;
        --m-space-3: 12px;
        --m-space-4: 16px;
        --m-page-pad: clamp(16px, 4.8vw, 20px);
        --m-radius-md: 14px;
        --m-radius-lg: 16px;
        --m-radius-xl: 20px;
        --m-touch: 48px;
        --m-font-title: clamp(1.42rem, 6.2vw, 1.72rem);
        --m-font-body: 0.94rem;
        --m-shadow-card: 0 10px 22px rgba(44, 30, 18, 0.16);
        --m-shadow-raised: 0 18px 38px rgba(10, 9, 7, 0.22);
    }
    html,
    body {
        width: 100%;
        overflow-x: hidden;
    }
    body::before {
        filter: blur(1px) saturate(0.96) brightness(0.98);
        inset: -4px;
        transform: scale(1.01);
    }
    body::after {
        background: linear-gradient(180deg, rgba(24, 22, 19, 0.54), rgba(24, 22, 19, 0.42));
    }
    .guest-auth-shell {
        align-items: flex-start;
        min-height: 100dvh;
        padding: var(--m-page-pad);
    }
    .guest-auth-card {
        width: 100%;
        max-width: 460px;
        margin: 0 auto;
        border-radius: var(--m-radius-xl);
        padding: var(--m-space-4);
        box-shadow: var(--m-shadow-raised);
    }
    .guest-auth-card, .guest-auth-card * {
        min-width: 0;
    }
    .rex-auth-head,
    .change-password-head,
    .login-head {
        margin-bottom: var(--m-space-4) !important;
    }
    .rex-auth-title,
    .change-password-title,
    .login-title {
        font-size: var(--m-font-title) !important;
        line-height: 1.12 !important;
        letter-spacing: 0 !important;
    }
    .rex-auth-subtitle,
    .change-password-subtitle,
    .login-subtitle {
        font-size: var(--m-font-body) !important;
        line-height: 1.45 !important;
    }
    .rex-auth-kicker,
    .change-password-kicker,
    .login-kicker {
        font-size: 0.72rem !important;
        letter-spacing: 0.08em !important;
        margin-bottom: var(--m-space-2) !important;
    }
    .rex-auth-form,
    .change-password-form,
    .login-form {
        gap: var(--m-space-3) !important;
    }
    .guest-auth-card input,
    .guest-auth-card select,
    .guest-auth-card textarea,
    .guest-auth-card button,
    .guest-auth-card a[class*="btn"],
    .guest-auth-card a[class*="button"] {
        max-width: 100%;
        min-height: var(--m-touch);
        border-radius: var(--m-radius-md) !important;
        font-size: 16px !important;
    }
    .rex-auth-input,
    .change-password-field input,
    .login-field input {
        padding: 0 14px !important;
        box-shadow: none !important;
    }
    .rex-auth-input-password,
    .login-password-wrap input {
        padding-right: 70px !important;
    }
    .rex-auth-password-toggle,
    .login-password-wrap button {
        min-width: 54px;
        min-height: 40px !important;
        right: 5px !important;
        border-radius: 12px !important;
        background: rgba(212, 164, 76, 0.10) !important;
        color: var(--guest-accent-deep) !important;
    }
    .guest-auth-card button[type="submit"], .guest-auth-card input[type="submit"] {
        width: 100%;
        justify-content: center;
        text-align: center;
        white-space: normal;
    }
    .rex-auth-btn,
    .change-password-submit,
    .login-submit {
        min-height: 50px !important;
        border-radius: var(--m-radius-md) !important;
        font-size: 0.92rem !important;
        letter-spacing: 0 !important;
        box-shadow: 0 10px 22px rgba(165, 114, 36, 0.18) !important;
    }
    .login-options,
    .login-footer {
        gap: var(--m-space-2) !important;
    }
    .app-toast-stack {
        top: 12px;
        right: var(--m-page-pad);
        left: var(--m-page-pad);
        width: auto;
    }
    .app-toast {
        width: 100%;
        border-radius: var(--m-radius-lg);
        padding: 14px;
    }
}
@media (max-width:414px) {
    :root {
        --m-page-pad: 16px;
    }
}
@media (max-width:375px) {
    :root {
        --m-space-4: 14px;
        --m-font-title: clamp(1.34rem, 6.4vw, 1.56rem);
    }
}
@media (max-width:320px) {
    :root {
        --m-page-pad: 14px;
        --m-space-4: 12px;
        --m-touch: 46px;
    }
}
</style>
    </head>
    <body>
        @if($errors->any())
            <div class="app-toast-stack" aria-live="polite" aria-atomic="true">
                @foreach($errors->all() as $msg)
                    <div class="app-toast app-toast-error" data-toast role="status">
                        <span class="app-toast-icon" aria-hidden="true"></span>
                        <div class="app-toast-copy">
                            <strong>{{ rtrim($msg, '.!') }}</strong>
                        </div>
                        <button type="button" data-toast-close aria-label="Dismiss notification">×</button>
                    </div>
                @endforeach
            </div>
        @endif

        <main class="guest-auth-shell">
            <section class="guest-auth-card" aria-label="HallSync account access">
                {{ $slot }}
            </section>
        </main>

        <script>
            document.querySelectorAll('[data-toggle-password]').forEach((button) => {
                button.addEventListener('click', () => {
                    const field = document.getElementById(button.dataset.togglePassword);

                    if (!field) {
                        return;
                    }

                    const isPassword = field.type === 'password';
                    field.type = isPassword ? 'text' : 'password';

                    // Icon-based toggle (e.g. login page): swap icon via class instead of text.
                    if (button.querySelector('.icon-eye, .icon-eye-off')) {
                        button.classList.toggle('is-visible', isPassword);
                    } else {
                        button.textContent = isPassword ? 'Hide' : 'Show';
                    }

                    button.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
                    if (button.hasAttribute('aria-pressed')) {
                        button.setAttribute('aria-pressed', isPassword ? 'true' : 'false');
                    }
                });
            });

            document.addEventListener('submit', (event) => {
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

                form.querySelectorAll('button[type="submit"], input[type="submit"]').forEach((button) => {
                    button.disabled = true;
                    button.setAttribute('aria-busy', 'true');

                    if (button.tagName === 'INPUT') {
                        button.value = submittingText;
                    } else {
                        button.textContent = submittingText;
                    }
                });
            }, true);
        </script>
        @include('layouts.partials.validation-bubble')
        <script>
            (() => {
                const registerToast = (toast) => {
                    const close = () => {
                        if (!toast.isConnected) return;
                        toast.classList.add('is-leaving');
                        setTimeout(() => toast.remove(), 240);
                    };
                    toast.querySelector('[data-toast-close]')?.addEventListener('click', close);
                    setTimeout(close, 5600);
                };
                document.querySelectorAll('[data-toast]').forEach(registerToast);
            })();
        </script>
    </body>
</html>
