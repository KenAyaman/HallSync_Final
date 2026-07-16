<x-app-layout>
    <div class="profile-page">
        <div class="profile-topbar">
            <a href="{{ route('dashboard') }}" class="resident-back-link resident-create-back">← Back</a>
        </div>

        <section class="profile-hero">
            <div class="profile-hero-copy">
                <p class="profile-kicker">Account Settings</p>
                <h1 class="profile-title">My Profile</h1>
                <p class="profile-subtitle">
                    Review your assigned account details, update your profile photo, and manage account security in one place.
                </p>
            </div>

        </section>

        <div class="profile-sections">
            <div class="profile-panel profile-panel-identity">
                @include('profile.partials.update-profile-information-form')
            </div>

            <div class="profile-panel profile-panel-security">
                @include('profile.partials.update-password-form')
            </div>

        </div>
    </div>

    <style>
.profile-page {
    max-width: 980px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 18px;
}
.profile-topbar {
    display: flex;
    align-items: center;
    justify-content: flex-start;
}
.profile-hero, .profile-panel {
    border: 1px solid rgba(214, 168, 91, 0.14);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.14);
}
.profile-hero {
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
    border-radius: 22px;
    padding: 28px 30px;
    background: radial-gradient(circle at 86% 44%, rgba(240, 208, 152, 0.18) 0 8%, rgba(214, 168, 91, 0.10) 18%, transparent 38%), linear-gradient(115deg, #1F2023 0%, #24262B 38%, #2C2C2F 62%, #3B3023 100%);
}
.profile-hero::after {
    content: "";
    position: absolute;
    right: 52px;
    bottom: -76px;
    width: 150px;
    height: 150px;
    border: 1px solid rgba(240, 208, 152, 0.14);
    border-radius: 50%;
    background: radial-gradient(circle, rgba(240, 208, 152, 0.12), rgba(214, 168, 91, 0.04) 48%, transparent 72%);
    box-shadow: 0 0 38px rgba(214, 168, 91, 0.09);
    pointer-events: none;
}
.profile-hero-copy {
    position: relative;
    z-index: 1;
    max-width: 680px;
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
    font-size: clamp(2.2rem, 4vw, 3.3rem);
    font-weight: 600;
    line-height: 1.04;
}
.profile-subtitle {
    margin: 12px 0 0;
    color: rgba(255, 255, 255, 0.82);
    font-size: 1rem;
    line-height: 1.75;
}
.profile-sections {
    display: flex;
    flex-direction: column;
    gap: 18px;
}
.profile-panel {
    background: #2A2C30;
    border-radius: 20px;
    padding: 20px 22px;
}
@media (max-width:768px) {
    .profile-page {
        gap: 20px;
    }
    .profile-hero {
        align-items: flex-start;
        flex-direction: column;
        padding: 24px;
    }
    .profile-panel {
        padding: 22px;
    }
}
@media (max-width:560px) {
    .profile-title {
        font-size: 2.1rem;
    }
    .profile-subtitle {
        font-size: 0.94rem;
    }
    .profile-topbar .resident-back-link {
        width: 100%;
    }
}
</style>
</x-app-layout>
