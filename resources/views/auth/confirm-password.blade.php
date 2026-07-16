<x-guest-layout>
    <div class="rex-auth-shell">
        <header class="rex-auth-head">
            <span class="rex-auth-kicker">Secure Access</span>
            <h1 class="rex-auth-title">Confirm your password</h1>
            <p class="rex-auth-subtitle">
                This is a protected area. Re-enter your password to continue.
            </p>
        </header>

        <form method="POST" action="{{ route('password.confirm') }}" class="rex-auth-form" data-prevent-double-submit data-submitting-text="Confirming...">
            @csrf

            <div class="rex-auth-field">
                <label for="password" class="rex-auth-label">Password</label>
                <div class="rex-auth-password-wrap">
                    <input id="password" class="rex-auth-input rex-auth-input-password" type="password" name="password" required autocomplete="current-password" placeholder="Enter your password">
                    <button type="button" class="rex-auth-password-toggle" data-toggle-password="password" aria-label="Show password">Show</button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="rex-auth-error" />
            </div>

            <button type="submit" class="rex-auth-btn">Confirm</button>
        </form>
    </div>
</x-guest-layout>
