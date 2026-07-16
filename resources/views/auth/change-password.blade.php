<x-guest-layout>
    <div class="change-password-shell">
        <div class="login-brand">
            <div class="login-brand-name">Hall<span>Sync</span></div>
            <p>Account Setup</p>
        </div>

        <header class="change-password-head">
            <h1>Set Your Password</h1>
            <p>Your account was created with a temporary password. You must set a new one before continuing.</p>
        </header>

        <form method="POST" action="{{ route('password.change.update') }}" class="change-password-form" data-prevent-double-submit data-submitting-text="Setting password...">
            @csrf

            <label class="change-password-field">
                <span>Current Password</span>
                <input type="password" name="current_password" required autocomplete="current-password" @error('current_password') aria-invalid="true" @enderror>
                @error('current_password')<small class="access-form-error">{{ $message }}</small>@enderror
            </label>

            <label class="change-password-field">
                <span>New Password</span>
                <input type="password" name="password" required autocomplete="new-password" @error('password') aria-invalid="true" @enderror>
                @error('password')<small class="access-form-error">{{ $message }}</small>@enderror
            </label>

            <label class="change-password-field">
                <span>Confirm New Password</span>
                <input type="password" name="password_confirmation" required autocomplete="new-password">
            </label>

            <button type="submit" class="change-password-submit">Set Password</button>
        </form>
    </div>

    <style>
.change-password-shell {
    margin: 0 auto;
    max-width: 430px;
    width: 100%;
}
.login-brand {
    margin-bottom: 26px;
    text-align: center;
}
.login-brand-name {
    color: #332b22;
    font-family: 'Playfair Display', serif;
    font-size: 2.25rem;
    font-weight: 700;
    line-height: 1;
}
.login-brand-name span {
    color: var(--guest-accent-deep);
}
.login-brand p {
    color: var(--guest-muted);
    font-size: .68rem;
    font-weight: 800;
    letter-spacing: .18em;
    margin: 10px 0 0;
    text-transform: uppercase;
}
.change-password-head {
    margin-bottom: 22px;
    text-align: center;
}
.change-password-head h1 {
    color: var(--guest-ink);
    font-family: 'Playfair Display', serif;
    font-size: 1.85rem;
    font-weight: 600;
    margin: 0;
}
.change-password-head p {
    color: var(--guest-body);
    font-size: .88rem;
    line-height: 1.6;
    margin: 8px 0 0;
}
.change-password-form {
    display: grid;
    gap: 16px;
}
.change-password-field {
    display: grid;
    gap: 7px;
}
.change-password-field span {
    color: #4c443b;
    font-size: .78rem;
    font-weight: 700;
}
.change-password-field input {
    background: rgba(255, 255, 255, .76);
    border: 1px solid #ddd5c9;
    border-radius: 10px;
    color: #332d27;
    font: inherit;
    font-size: .88rem;
    min-height: 49px;
    outline: none;
    padding: 0 13px;
    transition: background .18s ease, border-color .18s ease, box-shadow .18s ease;
    width: 100%;
}
.change-password-field input:focus {
    background: #fff;
    border-color: #c3913c;
    box-shadow: 0 0 0 4px rgba(212, 164, 76, .14);
}
.change-password-field input[aria-invalid="true"] {
    border-color: #c96d65;
}
.access-form-error {
    display: block;
    margin-top: 4px;
    color: #8f342e;
    font-size: .72rem;
    font-weight: 600;
}
.change-password-submit {
    background: linear-gradient(90deg, #bd8a34, var(--guest-accent));
    border: 0;
    border-radius: 10px;
    box-shadow: 0 10px 20px rgba(165, 114, 36, .2);
    color: #fff;
    cursor: pointer;
    font: inherit;
    font-size: .88rem;
    font-weight: 800;
    min-height: 50px;
    transition: box-shadow .18s ease, transform .18s ease;
}
.change-password-submit:hover {
    box-shadow: 0 14px 24px rgba(165, 114, 36, .28);
    transform: translateY(-1px);
}
</style>
</x-guest-layout>
