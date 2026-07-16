<section class="profile-section" id="profile-information">
    <header class="profile-section-head">
        <div>
            <h2 class="profile-section-title">{{ __('Profile Information') }}</h2>
            <p class="profile-section-copy">
                {{ __("Update your phone number and profile photo. Name, email, room, and role are managed by administration.") }}
            </p>
        </div>
        <span class="profile-section-chip">Account Record</span>
    </header>

    <div class="profile-section-divider"></div>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="profile-form-grid" data-prevent-double-submit data-submitting-text="Saving Photo...">
        @csrf
        @method('patch')

        <div class="profile-avatar-panel">
            <div class="profile-avatar-shell">
                @if ($user->profile_photo_url)
                    <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="profile-avatar-image" id="profileAvatarPreview">
                @else
                    <img src="" alt="{{ $user->name }}" class="profile-avatar-image" id="profileAvatarPreview" style="display:none;">
                    <span class="profile-avatar-fallback" id="profileAvatarFallback">{{ $user->profile_initials }}</span>
                @endif
            </div>

            <div class="profile-avatar-copy">
                <strong>Profile Photo</strong>
                <p>This is the only profile detail you can change directly. Use a clear photo so your account is easy to recognize.</p>
            </div>
        </div>

        <div class="profile-form-fields">
            <div>
                <label for="profile_photo" class="profile-label">Upload New Photo</label>
                <input id="profile_photo" name="profile_photo" type="file" accept="image/*" class="profile-input-file">
                <x-input-error class="mt-2" :messages="$errors->get('profile_photo')" />
            </div>

            @if ($user->profile_photo_path)
                <label class="profile-checkbox-row">
                    <input type="checkbox" name="remove_profile_photo" value="1">
                    <span>Remove current profile photo</span>
                </label>
            @endif

            {{-- Phone number is self-managed (M-16) --}}
            <div>
                <label for="phone_number" class="profile-label">Phone Number</label>
                <input id="phone_number"
                       name="phone_number"
                       type="tel"
                       value="{{ old('phone_number', $user->phone_number) }}"
                       maxlength="30"
                       placeholder="e.g. +63 900 000 0000"
                       class="profile-input-text"
                       autocomplete="tel">
                <x-input-error class="mt-2" :messages="$errors->get('phone_number')" />
            </div>

            <div class="profile-official-panel">
                <div class="profile-official-head">
                    <div>
                        <strong>Official Account Details</strong>
                        <p>These details are maintained by residence administration to keep room ownership and operational records accurate.</p>
                    </div>
                    <span>Read only</span>
                </div>
                <dl class="profile-official-grid">
                    <div><dt>Full name</dt><dd>{{ $user->name }}</dd></div>
                    <div><dt>Assigned email</dt><dd>{{ $user->email }}</dd></div>
                    <div><dt>Account role</dt><dd>{{ $user->role_label }}</dd></div>
                    @if ($user->isResident())
                        <div><dt>Resident ID</dt><dd>{{ $user->resident_number ?: 'Not assigned' }}</dd></div>
                        <div><dt>Room number</dt><dd>{{ $user->room_number ?: 'Not assigned' }}</dd></div>
                    @endif
                </dl>
                <p class="profile-official-note">Contact residence administration if any official detail needs to be corrected.</p>
            </div>
        </div>

        <div class="profile-form-actions">
            <button type="submit" class="profile-primary-btn">{{ __('Save Changes') }}</button>

            @if (session('status') === 'profile-updated')
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
.profile-section {
    scroll-margin-top: 20px;
    color: #F0E9DF;
}
.profile-section-head {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 16px;
    flex-wrap: wrap;
}
.profile-section-title {
    margin: 0;
    color: #F0E9DF;
    font-size: 1.5rem;
    font-weight: 600;
    font-family: 'Playfair Display', serif;
}
.profile-section-copy {
    margin: 4px 0 0;
    color: #8A7A66;
    font-size: 0.95rem;
    line-height: 1.7;
    max-width: 760px;
}
.profile-section-chip {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.16em;
    text-transform: uppercase;
    color: #D6A85B;
    padding: 8px 12px;
    border-radius: 999px;
    background: rgba(214, 168, 91, 0.10);
    border: 1px solid rgba(214, 168, 91, 0.16);
}
.profile-section-divider {
    height: 1px;
    background: linear-gradient(to right, rgba(214, 168, 91, 0.3), rgba(214, 168, 91, 0.05), transparent);
    margin: 18px 0 22px;
}
.profile-form-grid {
    display: flex;
    flex-direction: column;
    gap: 22px;
}
.profile-avatar-panel {
    display: flex;
    align-items: center;
    gap: 18px;
    padding: 18px;
    border-radius: 18px;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.05);
}
.profile-avatar-shell {
    width: 92px;
    height: 92px;
    border-radius: 24px;
    overflow: hidden;
    flex-shrink: 0;
    background: linear-gradient(135deg, rgba(214, 168, 91, 0.22), rgba(255, 255, 255, 0.05));
    border: 1px solid rgba(214, 168, 91, 0.16);
    display: flex;
    align-items: center;
    justify-content: center;
}
.profile-avatar-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.profile-avatar-fallback {
    color: #F8F3EA;
    font-size: 1.7rem;
    font-weight: 800;
    letter-spacing: 0.04em;
}
.profile-avatar-copy strong {
    display: block;
    color: #F0E9DF;
    font-size: 1rem;
    font-weight: 700;
}
.profile-avatar-copy p {
    margin: 8px 0 0;
    color: #B8AB98;
    line-height: 1.7;
    font-size: 0.9rem;
}
.profile-form-fields {
    display: flex;
    flex-direction: column;
    gap: 14px;
}
.profile-label {
    display: block;
    font-weight: 700;
    margin-bottom: 7px;
    color: #D0C8B8;
    font-size: 0.82rem;
    letter-spacing: 0.02em;
}
.profile-input-file {
    width: 100%;
    min-height: 42px;
    border-radius: 12px;
    border: 1px solid rgba(214, 168, 91, 0.14);
    background: rgba(37, 39, 42, 0.90);
    color: #F8F3EA;
    font-size: 0.9rem;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.02);
}
.profile-input-file {
    padding: 9px 11px;
}
.profile-input-file:focus, .profile-input-text:focus {
    border-color: rgba(214, 168, 91, 0.38);
    box-shadow: 0 0 0 4px rgba(214, 168, 91, 0.08);
    outline: none;
}
.profile-input-text {
    width: 100%;
    padding: 10px 14px;
    border-radius: 12px;
    border: 1px solid rgba(214, 168, 91, 0.14);
    background: rgba(37, 39, 42, 0.90);
    color: #F8F3EA;
    font-size: 0.9rem;
    font-family: inherit;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.02);
    box-sizing: border-box;
}
.profile-input-text::placeholder {
    color: #6A5E50;
}
.profile-checkbox-row {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    color: #B8AB98;
    font-size: 0.9rem;
    cursor: pointer;
}
.profile-official-panel {
    overflow: hidden;
    border: 1px solid rgba(214, 168, 91, 0.12);
    border-radius: 16px;
    background: rgba(255, 255, 255, 0.025);
}
.profile-official-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    padding: 15px 16px;
    border-bottom: 1px solid rgba(214, 168, 91, 0.10);
}
.profile-official-head strong {
    color: #F0E9DF;
    font-size: 0.94rem;
}
.profile-official-head p, .profile-official-note {
    margin: 5px 0 0;
    color: #A99C8B;
    font-size: 0.84rem;
    line-height: 1.6;
}
.profile-official-head span {
    flex: 0 0 auto;
    color: #D6A85B;
    font-size: 0.68rem;
    font-weight: 800;
    letter-spacing: 0.12em;
    text-transform: uppercase;
}
.profile-official-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    margin: 0;
}
.profile-official-grid div {
    padding: 12px 16px;
    border-bottom: 1px solid rgba(214, 168, 91, 0.08);
}
.profile-official-grid dt {
    color: #8A7A66;
    font-size: 0.68rem;
    font-weight: 800;
    letter-spacing: 0.09em;
    text-transform: uppercase;
}
.profile-official-grid dd {
    margin: 5px 0 0;
    color: #E6DDD0;
    font-size: 0.9rem;
    overflow-wrap: anywhere;
}
.profile-official-note {
    margin: 0;
    padding: 12px 16px;
    color: #C9B48E;
}
.profile-status-copy {
    color: #98c48b;
    font-size: 0.9rem;
    font-weight: 600;
}
.profile-form-actions {
    display: flex;
    align-items: center;
    gap: 14px;
    flex-wrap: wrap;
    padding-top: 6px;
    border-top: 1px solid rgba(214, 168, 91, 0.10);
}
.profile-primary-btn {
    background: linear-gradient(90deg, #B8842F 0%, #D6A85B 100%);
    color: #FFFFFF;
    padding: 10px 18px;
    border-radius: 999px;
    font-weight: 700;
    border: none;
    cursor: pointer;
    box-shadow: 0 10px 24px rgba(199, 150, 69, 0.20);
}
@media (max-width:768px) {
    .profile-avatar-panel {
        flex-direction: column;
        align-items: flex-start;
    }
    .profile-official-grid {
        grid-template-columns: 1fr;
    }
}
</style>

    <script>
        (() => {
            const input = document.getElementById('profile_photo');
            const preview = document.getElementById('profileAvatarPreview');
            const fallback = document.getElementById('profileAvatarFallback');

            if (!input || !preview) {
                return;
            }

            input.addEventListener('change', (event) => {
                const file = event.target.files[0];

                if (!file) {
                    return;
                }

                preview.src = URL.createObjectURL(file);
                preview.style.display = 'block';

                if (fallback) {
                    fallback.style.display = 'none';
                }
            });
        })();
    </script>
</section>
