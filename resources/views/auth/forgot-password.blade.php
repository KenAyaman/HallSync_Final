<x-guest-layout>
    <div class="rex-auth-shell">
        <header class="rex-auth-head">
            <span class="rex-auth-kicker">Account Recovery</span>
            <h1 class="rex-auth-title">Reset your password</h1>
            <p class="rex-auth-subtitle">
                Enter your HallSync account email and we will send you a reset link.
            </p>
        </header>

        <x-auth-session-status class="rex-auth-status" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="rex-auth-form" data-prevent-double-submit data-submitting-text="Sending link...">
            @csrf

            <div class="rex-auth-field">
                <label for="email" class="rex-auth-label">Email address</label>
                <input id="email" class="rex-auth-input" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="name@example.com">
                <x-input-error :messages="$errors->get('email')" class="rex-auth-error" />
            </div>

            <button type="submit" class="rex-auth-btn">Email password reset link</button>
        </form>
    </div>
</x-guest-layout>
