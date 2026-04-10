<x-guest-layout>
    <div class="rex-auth-shell">
        <div class="rex-auth-head">
            <span class="rex-auth-kicker">Account Recovery</span>
            <h2 class="rex-auth-title">Reset your password</h2>
            <p class="rex-auth-subtitle">
                Enter your Rexhall account email and we’ll send you a reset link.
            </p>
        </div>

        <x-auth-session-status class="rex-auth-status" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="rex-auth-form">
            @csrf

            <div class="rex-auth-field">
                <label for="email" class="rex-auth-label">Email Address</label>
                <input id="email" class="rex-auth-input" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="you@rexhall.com">
                <x-input-error :messages="$errors->get('email')" class="rex-auth-error" />
            </div>

            <button type="submit" class="rex-auth-btn">
                Email Password Reset Link
            </button>
        </form>
    </div>

    <style>
        .rex-auth-shell { width: 100%; max-width: 460px; margin: 0 auto; color: #f3ece2; }
        .rex-auth-head { margin-bottom: 22px; }
        .rex-auth-kicker { display: inline-block; margin-bottom: 10px; color: #d6a85b; font-size: 0.74rem; font-weight: 700; letter-spacing: 0.22em; text-transform: uppercase; }
        .rex-auth-title { margin: 0; color: #f8f3ea; font-family: 'Playfair Display', serif; font-size: clamp(2rem, 4vw, 2.7rem); line-height: 1.05; }
        .rex-auth-subtitle { margin: 12px 0 0; color: #bfae95; line-height: 1.75; font-size: 0.96rem; }
        .rex-auth-status { margin-bottom: 16px; padding: 13px 14px; border-radius: 16px; background: rgba(90, 138, 90, 0.14); border: 1px solid rgba(90, 138, 90, 0.22); color: #b7d3b7; font-size: 0.9rem; }
        .rex-auth-form { display: grid; gap: 18px; }
        .rex-auth-field { display: grid; gap: 9px; }
        .rex-auth-label { color: #d2c7b6; font-size: 0.84rem; font-weight: 700; letter-spacing: 0.04em; }
        .rex-auth-input { width: 100%; min-height: 54px; padding: 0 16px; border-radius: 16px; border: 1px solid rgba(214, 168, 91, 0.14); background: rgba(37, 39, 42, 0.9); color: #f8f3ea; outline: none; transition: 0.18s ease; box-shadow: inset 0 1px 0 rgba(255,255,255,0.02); }
        .rex-auth-input::placeholder { color: #87765f; }
        .rex-auth-input:focus { border-color: rgba(214, 168, 91, 0.38); box-shadow: 0 0 0 4px rgba(214, 168, 91, 0.08); }
        .rex-auth-btn { min-height: 54px; border: 0; border-radius: 999px; background: linear-gradient(90deg, #b8842f 0%, #d6a85b 100%); color: #ffffff; font-size: 0.96rem; font-weight: 800; cursor: pointer; box-shadow: 0 14px 30px rgba(199, 150, 69, 0.28); transition: 0.2s ease; }
        .rex-auth-btn:hover { transform: translateY(-1px); box-shadow: 0 18px 34px rgba(199, 150, 69, 0.32); }
        .rex-auth-error { color: #f0bbb3; font-size: 0.88rem; }
    </style>
</x-guest-layout>
