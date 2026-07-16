<x-guest-layout>
    <div class="rex-auth-shell">
        <header class="rex-auth-head">
            <span class="rex-auth-kicker">Password Reset</span>
            <h1 class="rex-auth-title">Choose a new password</h1>
            <p class="rex-auth-subtitle">Set a new password for your HallSync account.</p>
        </header>

        <form method="POST" action="{{ route('password.store') }}" class="rex-auth-form" data-prevent-double-submit data-submitting-text="Resetting password...">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="rex-auth-field">
                <label for="email" class="rex-auth-label">Email address</label>
                <input id="email" class="rex-auth-input" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" placeholder="name@example.com">
                <x-input-error :messages="$errors->get('email')" class="rex-auth-error" />
            </div>

            <div class="rex-auth-field">
                <label for="password" class="rex-auth-label">New password</label>
                <div class="rex-auth-password-wrap">
                    <input id="password" class="rex-auth-input rex-auth-input-password" type="password" name="password" required autocomplete="new-password" placeholder="Create a new password">
                    <button type="button" class="rex-auth-password-toggle" data-toggle-password="password" aria-label="Show password">Show</button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="rex-auth-error" />
            </div>

            <div class="rex-auth-field">
                <label for="password_confirmation" class="rex-auth-label">Confirm password</label>
                <div class="rex-auth-password-wrap">
                    <input id="password_confirmation" class="rex-auth-input rex-auth-input-password" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm your new password">
                    <button type="button" class="rex-auth-password-toggle" data-toggle-password="password_confirmation" aria-label="Show password">Show</button>
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" class="rex-auth-error" />
            </div>

            <button type="submit" class="rex-auth-btn">Reset password</button>
        </form>
    </div>
</x-guest-layout>
