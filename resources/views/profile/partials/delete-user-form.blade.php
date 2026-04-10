<section class="profile-section space-y-6">
    <header class="profile-section-head">
        <div>
            <h2 class="profile-section-title">{{ __('Delete Account') }}</h2>
            <p class="profile-section-copy">
                {{ __('Permanently remove your account and all associated HallSync data.') }}
            </p>
        </div>
        <span class="profile-section-chip profile-section-chip-danger">Danger Zone</span>
    </header>

    <div class="profile-section-divider"></div>

    <button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        type="button"
        class="profile-danger-btn"
    >
        {{ __('Delete Account') }}
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="profile-modal-title">
                {{ __('Are you sure you want to delete your account?') }}
            </h2>

            <p class="profile-modal-copy">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Enter your password to confirm.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />
                <x-text-input id="password" name="password" type="password" class="profile-input mt-1 block w-full" placeholder="{{ __('Password') }}" />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')" class="rounded-full border-[rgba(214,168,91,0.14)] bg-[rgba(255,255,255,0.04)] text-[#D0C8B8] hover:bg-[rgba(255,255,255,0.08)]">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <button type="submit" class="profile-danger-btn">
                    {{ __('Delete Account') }}
                </button>
            </div>
        </form>
    </x-modal>

    <style>
        .profile-section-chip-danger {
            color: #F0B3A9;
            background: rgba(224,112,96,0.12);
            border-color: rgba(224,112,96,0.22);
        }

        .profile-danger-btn {
            background: linear-gradient(90deg, #A14C42 0%, #B96A5D 100%);
            color: #FFFFFF;
            padding: 12px 24px;
            border-radius: 999px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            box-shadow: 0 10px 24px rgba(161, 76, 66, 0.20);
        }

        .profile-modal-title {
            font-size: 24px;
            font-weight: 600;
            color: #2F2A27;
            font-family: 'Playfair Display', serif;
        }

        .profile-modal-copy {
            margin-top: 10px;
            font-size: 14px;
            line-height: 1.8;
            color: #7B746B;
        }
    </style>
</section>
