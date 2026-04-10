<section class="profile-section">
    <header class="profile-section-head">
        <div>
            <h2 class="profile-section-title">{{ __('Profile Information') }}</h2>
            <p class="profile-section-copy">
                {{ __("Update your account details, email address, and profile photo.") }}
            </p>
        </div>
        <span class="profile-section-chip">Public Identity</span>
    </header>

    <div class="profile-section-divider"></div>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="profile-form-grid">
        @csrf
        @method('patch')

        <div class="profile-avatar-panel">
            <div class="profile-avatar-shell">
                @if ($user->profile_photo_url)
                    <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="profile-avatar-image">
                @else
                    <span class="profile-avatar-fallback">{{ $user->profile_initials }}</span>
                @endif
            </div>

            <div class="profile-avatar-copy">
                <strong>Profile Photo</strong>
                <p>Use a clear photo so your profile looks more personal and polished across HallSync.</p>
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

            <div>
                <label for="name" class="profile-label">Name</label>
                <x-text-input
                    id="name"
                    name="name"
                    type="text"
                    class="profile-input"
                    :value="old('name', $user->name)"
                    required
                    autofocus
                    autocomplete="name"
                />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div>
                <label for="email" class="profile-label">Email</label>
                <x-text-input
                    id="email"
                    name="email"
                    type="email"
                    class="profile-input"
                    :value="old('email', $user->email)"
                    required
                    autocomplete="username"
                />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="profile-verification-box">
                        <p>
                            {{ __('Your email address is unverified.') }}
                            <button form="send-verification" class="profile-inline-link">
                                {{ __('Click here to re-send the verification email.') }}
                            </button>
                        </p>

                        @if (session('status') === 'verification-link-sent')
                            <p class="profile-success-copy">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </p>
                        @endif
                    </div>
                @endif
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
            background: rgba(214,168,91,0.10);
            border: 1px solid rgba(214,168,91,0.16);
        }

        .profile-section-divider {
            height: 1px;
            background: linear-gradient(to right, rgba(214,168,91,0.3), rgba(214,168,91,0.05), transparent);
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
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.05);
        }

        .profile-avatar-shell {
            width: 92px;
            height: 92px;
            border-radius: 24px;
            overflow: hidden;
            flex-shrink: 0;
            background: linear-gradient(135deg, rgba(214,168,91,0.22), rgba(255,255,255,0.05));
            border: 1px solid rgba(214,168,91,0.16);
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
            gap: 18px;
        }

        .profile-label {
            display: block;
            font-weight: 700;
            margin-bottom: 10px;
            color: #D0C8B8;
            font-size: 14px;
            letter-spacing: 0.02em;
        }

        .profile-input,
        .profile-input-file {
            width: 100%;
            border-radius: 16px;
            border: 1px solid rgba(214,168,91,0.14);
            background: rgba(37,39,42,0.90);
            color: #F8F3EA;
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.02);
        }

        .profile-input {
            padding: 14px 16px;
        }

        .profile-input-file {
            padding: 12px 14px;
        }

        .profile-input:focus,
        .profile-input-file:focus {
            border-color: rgba(214,168,91,0.38);
            box-shadow: 0 0 0 4px rgba(214,168,91,0.08);
        }

        .profile-checkbox-row {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: #B8AB98;
            font-size: 0.9rem;
            cursor: pointer;
        }

        .profile-verification-box {
            margin-top: 12px;
            padding: 14px 16px;
            border-radius: 16px;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.05);
            color: #B8AB98;
            line-height: 1.7;
            font-size: 0.9rem;
        }

        .profile-inline-link {
            margin-left: 4px;
            background: none;
            border: none;
            color: #D6A85B;
            cursor: pointer;
            font-weight: 700;
            text-decoration: underline;
        }

        .profile-success-copy,
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
            padding-top: 8px;
            border-top: 1px solid rgba(214,168,91,0.10);
        }

        .profile-primary-btn {
            background: linear-gradient(90deg, #B8842F 0%, #D6A85B 100%);
            color: #FFFFFF;
            padding: 12px 24px;
            border-radius: 999px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            box-shadow: 0 10px 24px rgba(199, 150, 69, 0.20);
        }

        @media (max-width: 768px) {
            .profile-avatar-panel {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</section>
