<x-guest-layout>
    <div class="login-brand">
        <div class="login-brand-name">Hall<span>Sync</span></div>
        <p>Residence Management Portal</p>
    </div>

    <header class="login-head">
        <h1>Welcome back</h1>
        <p>Log in with your assigned account to continue.</p>
    </header>

    <x-auth-session-status class="login-status" :status="session('status')" />


    <form method="POST" action="{{ route('login') }}" class="login-form" data-prevent-double-submit data-submitting-text="Logging in...">
        @csrf

        <div class="login-field">
            <label for="email">Email address</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="name@example.com"
                required
                autofocus
                autocomplete="username"
                @error('email') aria-invalid="true" @enderror>
        </div>

        <div class="login-field">
            <label for="password">Password</label>
            <div class="login-password-wrap">
                <input
                    id="password"
                    type="password"
                    name="password"
                    placeholder="Enter your password"
                    required
                    autocomplete="current-password">
                <button type="button" class="login-password-toggle" data-toggle-password="password" aria-label="Show password">
                    <svg class="icon-eye" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M1.5 12s3.5-7 10.5-7 10.5 7 10.5 7-3.5 7-10.5 7-10.5-7-10.5-7Z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    <svg class="icon-eye-off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M3 3l18 18"></path>
                        <path d="M10.6 10.6a3 3 0 0 0 4.24 4.24"></path>
                        <path d="M6.6 6.7C4.2 8.2 2.4 10.4 1.5 12c1.4 2.6 4.9 7 10.5 7 1.8 0 3.4-.4 4.8-1.1M17.9 17.9C20 16.4 21.6 14.2 22.5 12c-1-1.9-2.7-4.2-5.1-5.7C16 5.4 14.4 5 12.5 5c-.6 0-1.2.05-1.7.14"></path>
                    </svg>
                </button>
            </div>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="login-forgot-link">Forgot password?</a>
            @endif
        </div>

        <label for="remember_me" class="login-check">
            <input id="remember_me" type="checkbox" name="remember">
            <span>Remember me</span>
        </label>

        <button type="submit" class="login-submit">Log In</button>
    </form>

    <p class="login-security">
        <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M12 3 5 6v5c0 4.6 2.8 8.2 7 10 4.2-1.8 7-5.4 7-10V6l-7-3Z"></path>
            <path d="m9.5 12 1.6 1.6 3.6-3.8"></path>
        </svg>
        Secure access for authorized residents and staff.
    </p>

    <style>
.login-brand {
    margin-bottom: 30px;
    text-align: center;
}
.login-brand-name {
    color: #332b22;
    font-family: 'Playfair Display', serif;
    font-size: 2.25rem;
    font-weight: 700;
    letter-spacing: -0.06em;
    line-height: 1;
}
.login-brand-name span {
    color: var(--guest-accent-deep);
}
.login-brand p {
    color: var(--guest-muted);
    font-size: 0.68rem;
    font-weight: 800;
    letter-spacing: 0.18em;
    margin: 10px 0 0;
    text-transform: uppercase;
}
.login-head {
    margin-bottom: 22px;
}
.login-head h1 {
    color: var(--guest-ink);
    font-family: 'Playfair Display', serif;
    font-size: 1.8rem;
    font-weight: 600;
    margin: 0;
}
.login-head p {
    color: var(--guest-body);
    font-size: 0.88rem;
    line-height: 1.6;
    margin: 7px 0 0;
}
.login-status {
    border-radius: var(--hs-radius-md);
    font-size: var(--hs-font-caption);
    line-height: 1.5;
    margin-bottom: var(--hs-space-4);
    padding: var(--hs-space-3) var(--hs-space-3);
    background: #eff8f1;
    border: 1px solid #d5e8d9;
    color: #397050;
}
.login-form {
    display: grid;
    gap: var(--hs-space-4);
}
.login-field {
    display: grid;
    gap: var(--hs-space-2);
}
.login-field label {
    color: #4c443b;
    font-size: var(--hs-font-caption);
    font-weight: var(--hs-font-bold);
}
.login-field a {
    color: #996d22;
    font-size: var(--hs-font-caption);
    font-weight: var(--hs-font-bold);
    text-decoration: none;
}
.login-field a:hover {
    color: #704b12;
    text-decoration: underline;
}
.login-field input {
    background: rgba(255, 255, 255, 0.76);
    border: 1px solid #ddd5c9;
    border-radius: var(--hs-radius-md);
    color: #332d27;
    font: inherit;
    font-size: var(--hs-font-body);
    min-height: 49px;
    outline: none;
    padding: 0 var(--hs-space-3);
    transition: background 0.18s ease, border-color 0.18s ease, box-shadow 0.18s ease;
    width: 100%;
}
.login-field input::placeholder {
    color: #aaa095;
}
.login-field input:focus {
    background: #fff;
    border-color: #c3913c;
    box-shadow: 0 0 0 4px rgba(212, 164, 76, 0.14);
}
.login-field input[aria-invalid="true"] {
    border-color: #c96d65;
}
.login-password-wrap {
    position: relative;
}
.login-password-wrap input {
    padding-right: 48px;
}
.login-password-toggle {
    background: transparent;
    border: 0;
    color: #996d22;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: var(--hs-space-2);
    position: absolute;
    right: 4px;
    top: 50%;
    transform: translateY(-50%);
    border-radius: var(--hs-radius-sm);
}
.login-password-toggle svg {
    width: 19px;
    height: 19px;
}
.login-password-toggle .icon-eye-off {
    display: none;
}
.login-password-toggle.is-visible .icon-eye {
    display: none;
}
.login-password-toggle.is-visible .icon-eye-off {
    display: block;
}
.login-password-toggle:hover {
    color: #704b12;
}
.login-forgot-link {
    display: block;
    text-align: end;
    color: #996d22;
    font-size: var(--hs-font-caption);
    font-weight: var(--hs-font-bold);
    text-decoration: none;
    margin-top: 5px;
}
.login-forgot-link:hover {
    color: #704b12;
    text-decoration: underline;
}
.login-password-toggle:focus-visible, .login-forgot-link:focus-visible {
    border-radius: var(--hs-radius-sm);
    outline: 3px solid rgba(212, 164, 76, 0.3);
    outline-offset: 2px;
}
.login-check {
    align-items: center;
    color: var(--guest-body);
    cursor: pointer;
    display: inline-flex;
    font-size: var(--hs-font-caption);
    gap: var(--hs-space-2);
    width: fit-content;
}
.login-check input {
    accent-color: var(--guest-accent-deep);
    height: 15px;
    width: 15px;
}
.login-submit {
    background: linear-gradient(90deg, #bd8a34, var(--guest-accent));
    border: 0;
    border-radius: var(--hs-radius-md);
    box-shadow: var(--hs-shadow-md);
    color: #fff;
    cursor: pointer;
    font: inherit;
    font-size: var(--hs-font-body);
    font-weight: var(--hs-font-extrabold);
    min-height: 50px;
    transition: box-shadow 0.18s ease, transform 0.18s ease;
}
.login-submit:hover {
    box-shadow: 0 14px 24px rgba(165, 114, 36, 0.28);
    transform: translateY(-1px);
}
.login-submit:focus-visible {
    outline: 4px solid rgba(212, 164, 76, 0.3);
    outline-offset: 3px;
}
.login-submit:disabled {
    cursor: wait;
    opacity: 0.72;
}
.login-security {
    align-items: center;
    color: var(--guest-muted);
    display: flex;
    font-size: 0.7rem;
    gap: 6px;
    justify-content: center;
    line-height: 1.5;
    margin: 19px 0 0;
    text-align: center;
}
.login-security svg {
    fill: none;
    flex: 0 0 auto;
    height: 15px;
    stroke: #a77a2f;
    stroke-linecap: round;
    stroke-linejoin: round;
    stroke-width: 1.8;
    width: 15px;
}
</style>
</x-guest-layout>
