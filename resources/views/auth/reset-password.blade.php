<x-guest-layout>
    <div class="rex-auth-shell">
        <div class="rex-auth-head">
            <span class="rex-auth-kicker">Password Reset</span>
            <h2 class="rex-auth-title">Choose a new password</h2>
            <p class="rex-auth-subtitle">
                Set a new password for your Rexhall HallSync account.
            </p>
        </div>

        <form method="POST" action="{{ route('password.store') }}" class="rex-auth-form" data-prevent-double-submit data-submitting-text="Resetting Password...">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="rex-auth-field">
                <label for="email" class="rex-auth-label">Email Address</label>
                <input id="email" class="rex-auth-input" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" placeholder="you@rexhall.com">
                <x-input-error :messages="$errors->get('email')" class="rex-auth-error" />
            </div>

            <div class="rex-auth-field">
                <label for="password" class="rex-auth-label">New Password</label>
                <div class="rex-auth-password-wrap">
                    <input id="password" class="rex-auth-input rex-auth-input-password" type="password" name="password" required autocomplete="new-password" placeholder="Create a new password">
                    <button type="button" class="rex-auth-password-toggle" data-toggle-password="password">Show</button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="rex-auth-error" />
            </div>

            <div class="rex-auth-field">
                <label for="password_confirmation" class="rex-auth-label">Confirm Password</label>
                <div class="rex-auth-password-wrap">
                    <input id="password_confirmation" class="rex-auth-input rex-auth-input-password" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm your new password">
                    <button type="button" class="rex-auth-password-toggle" data-toggle-password="password_confirmation">Show</button>
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" class="rex-auth-error" />
            </div>

            <button type="submit" class="rex-auth-btn">Reset Password</button>
        </form>
    </div>

    <style>
        .rex-auth-shell { width: 100%; max-width: 460px; margin: 0 auto; color: #f3ece2; }
        .rex-auth-head { margin-bottom: 22px; }
        .rex-auth-kicker { display: inline-block; margin-bottom: 10px; color: #d6a85b; font-size: 0.74rem; font-weight: 700; letter-spacing: 0.22em; text-transform: uppercase; }
        .rex-auth-title { margin: 0; color: #f8f3ea; font-family: 'Playfair Display', serif; font-size: clamp(2rem, 4vw, 2.7rem); line-height: 1.05; }
        .rex-auth-subtitle { margin: 12px 0 0; color: #bfae95; line-height: 1.75; font-size: 0.96rem; }
        .rex-auth-form { display: grid; gap: 18px; }
        .rex-auth-field { display: grid; gap: 9px; }
        .rex-auth-label { color: #d2c7b6; font-size: 0.84rem; font-weight: 700; letter-spacing: 0.04em; }
        .rex-auth-input { width: 100%; min-height: 54px; padding: 0 16px; border-radius: 16px; border: 1px solid rgba(214, 168, 91, 0.14); background: rgba(37, 39, 42, 0.9); color: #f8f3ea; outline: none; transition: 0.18s ease; box-shadow: inset 0 1px 0 rgba(255,255,255,0.02); }
        .rex-auth-input::placeholder { color: #87765f; }
        .rex-auth-input:focus { border-color: rgba(214, 168, 91, 0.38); box-shadow: 0 0 0 4px rgba(214, 168, 91, 0.08); }
        .rex-auth-password-wrap { position: relative; }
        .rex-auth-input-password { padding-right: 76px; }
        .rex-auth-password-toggle { position: absolute; top: 50%; right: 14px; transform: translateY(-50%); border: none; background: none; color: #d6a85b; font-size: 0.8rem; font-weight: 700; cursor: pointer; }
        .rex-auth-btn { min-height: 54px; border: 0; border-radius: 999px; background: linear-gradient(90deg, #b8842f 0%, #d6a85b 100%); color: #ffffff; font-size: 0.96rem; font-weight: 800; cursor: pointer; box-shadow: 0 14px 30px rgba(199, 150, 69, 0.28); transition: 0.2s ease; }
        .rex-auth-btn:hover { transform: translateY(-1px); box-shadow: 0 18px 34px rgba(199, 150, 69, 0.32); }
        .rex-auth-error { color: #f0bbb3; font-size: 0.88rem; }
    </style>

    <script>
        document.querySelectorAll('[data-toggle-password]').forEach((button) => {
            button.addEventListener('click', () => {
                const field = document.getElementById(button.dataset.togglePassword);
                const isPassword = field.type === 'password';
                field.type = isPassword ? 'text' : 'password';
                button.textContent = isPassword ? 'Hide' : 'Show';
            });
        });
    </script>
</x-guest-layout>
