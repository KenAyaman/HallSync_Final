<section class="profile-section">
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

    <form method="post" action="{{ route('password.update') }}" class="profile-form-grid">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="profile-label">Current Password</label>
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="profile-input" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <label for="update_password_password" class="profile-label">New Password</label>
            <x-text-input id="update_password_password" name="password" type="password" class="profile-input" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <label for="update_password_password_confirmation" class="profile-label">Confirm Password</label>
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="profile-input" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="profile-form-actions">
            <button type="submit" class="profile-primary-btn">{{ __('Save Changes') }}</button>

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
</section>
