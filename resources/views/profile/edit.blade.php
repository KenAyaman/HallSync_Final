<x-app-layout>
    <div class="profile-page">
        <section class="profile-hero">
            <div class="profile-hero-copy">
                <p class="profile-kicker">Personal Account Settings</p>
                <h1 class="profile-title">My Profile</h1>
                <p class="profile-subtitle">
                    Manage your account details, refresh your profile photo, and keep your HallSync identity secure and up to date.
                </p>
            </div>
        </section>

        <div class="profile-sections">
            <div class="profile-panel">
                @include('profile.partials.update-profile-information-form')
            </div>

            <div class="profile-panel">
                @include('profile.partials.update-password-form')
            </div>

            @if(auth()->user()->role === 'manager')
                <div class="profile-panel profile-panel-danger">
                    @include('profile.partials.delete-user-form')
                </div>
            @endif
        </div>
    </div>

    <style>
        .profile-page {
            max-width: 1580px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .profile-hero,
        .profile-panel {
            border: 1px solid rgba(214,168,91,0.14);
            box-shadow: 0 12px 24px rgba(0,0,0,0.14);
        }

        .profile-hero {
            position: relative;
            overflow: hidden;
            border-radius: 36px;
            padding: 28px 30px;
            background: linear-gradient(115deg, #1F2023 0%, #24262B 38%, #2C2C2F 62%, #3B3023 100%);
            box-shadow: 0 18px 50px rgba(0, 0, 0, 0.18);
        }

        .profile-hero-copy {
            max-width: 760px;
        }

        .profile-kicker {
            margin: 0 0 10px;
            color: #D2A04C;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.30em;
        }

        .profile-title {
            margin: 0;
            color: #F8F3EA;
            font-family: 'Playfair Display', serif;
            font-size: clamp(2.5rem, 4.8vw, 4rem);
            line-height: 1.02;
        }

        .profile-subtitle {
            margin: 12px 0 0;
            color: rgba(255,255,255,0.82);
            font-size: 1rem;
            line-height: 1.75;
        }

        .profile-sections {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .profile-panel {
            background: rgba(42,44,48,0.78);
            border-radius: 20px;
            padding: 26px 28px;
            backdrop-filter: blur(10px);
        }

        .profile-panel-danger {
            border-color: rgba(224,112,96,0.22);
            background: linear-gradient(180deg, rgba(53, 38, 35, 0.72) 0%, rgba(42, 31, 29, 0.78) 100%);
        }

        @media (max-width: 768px) {
            .profile-page {
                gap: 20px;
            }

            .profile-hero,
            .profile-panel {
                padding: 22px;
            }
        }
    </style>
</x-app-layout>
