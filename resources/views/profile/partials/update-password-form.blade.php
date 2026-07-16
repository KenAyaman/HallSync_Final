<section class="profile-section" id="profile-security">
    <header class="profile-section-head">
        <div>
            <h2 class="profile-section-title">{{ __('Update Password') }}</h2>
            <p class="profile-section-copy">
                {{ __('Use a strong password to keep your HallSync account protected.') }}
            </p>
        </div>
        <span class="profile-section-chip">Security</span>
    </header>

    <div class="profile-section-divider"></div>

    <form method="post" action="{{ route('password.update') }}" class="profile-form-grid profile-security-form" data-prevent-double-submit data-submitting-text="Updating Password...">
        @csrf
        @method('put')

        <div class="profile-security-fields">
        <div class="profile-security-field">
            <label for="update_password_current_password" class="profile-label">Current Password</label>
            <div class="profile-password-wrap">
                <x-text-input id="update_password_current_password" name="current_password" type="password" class="profile-input profile-input-password" autocomplete="current-password" />
                <button type="button" class="profile-password-toggle" data-toggle-password="update_password_current_password" aria-controls="update_password_current_password" aria-pressed="false">Show</button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div class="profile-security-field">
            <label for="update_password_password" class="profile-label">New Password</label>
            <div class="profile-password-wrap">
                <x-text-input id="update_password_password" name="password" type="password" class="profile-input profile-input-password" autocomplete="new-password" />
                <button type="button" class="profile-password-toggle" data-toggle-password="update_password_password" aria-controls="update_password_password" aria-pressed="false">Show</button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div class="profile-security-field">
            <label for="update_password_password_confirmation" class="profile-label">Confirm Password</label>
            <div class="profile-password-wrap">
                <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="profile-input profile-input-password" autocomplete="new-password" />
                <button type="button" class="profile-password-toggle" data-toggle-password="update_password_password_confirmation" aria-controls="update_password_password_confirmation" aria-pressed="false">Show</button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>
        </div>

        <div class="profile-form-actions">
            <button type="submit" class="profile-primary-btn">{{ __('Update Password') }}</button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="profile-status-copy"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>

    <style>
.profile-security-form {
    max-width: 720px;
}
.profile-security-fields {
    display: grid;
    gap: 15px;
    padding: 16px;
    border: 1px solid rgba(214, 168, 91, 0.10);
    border-radius: 16px;
    background: rgba(255, 255, 255, 0.025);
}
.profile-security-field {
    min-width: 0;
}
.profile-password-wrap {
    position: relative;
}
.profile-input-password {
    width: 100%;
    min-height: 44px;
    padding: 10px 78px 10px 13px;
    border: 1px solid rgba(214, 168, 91, 0.16);
    border-radius: 12px;
    background: rgba(37, 39, 42, 0.94);
    color: #F8F3EA;
    font-size: 0.92rem;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.025);
    transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
}
.profile-input-password:focus {
    border-color: rgba(214, 168, 91, 0.46);
    background: rgba(31, 33, 36, 0.98);
    box-shadow: 0 0 0 4px rgba(214, 168, 91, 0.09);
    outline: none;
    --tw-ring-color: transparent;
}
.profile-input-password:-webkit-autofill {
    -webkit-text-fill-color: #F8F3EA;
    box-shadow: 0 0 0 1000px #25272A inset;
}
.profile-password-toggle {
    position: absolute;
    top: 50%;
    right: 14px;
    transform: translateY(-50%);
    border: none;
    background: none;
    color: #D6A85B;
    font-size: 0.8rem;
    font-weight: 700;
    cursor: pointer;
    transition: color 0.2s ease;
}
.profile-password-toggle:hover, .profile-password-toggle:focus-visible {
    color: #F2D39B;
    outline: none;
}
@media (max-width:560px) {
    .profile-security-fields {
        padding: 14px;
    }
}
</style>

    <script>
        document.querySelectorAll('[data-toggle-password]').forEach((button) => {
            button.addEventListener('click', () => {
                const field = document.getElementById(button.dataset.togglePassword);

                if (!field) {
                    return;
                }

                const isPassword = field.type === 'password';
                field.type = isPassword ? 'text' : 'password';
                button.textContent = isPassword ? 'Hide' : 'Show';
                button.setAttribute('aria-pressed', isPassword ? 'true' : 'false');
            });
        });
    </script>
</section>
