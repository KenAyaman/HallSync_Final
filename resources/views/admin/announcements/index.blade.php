<x-app-layout>
<div class="space-y-8 admin-shell admin-announcements-page">
    <section class="admin-overview-hero">
        <div>
            <p class="admin-overview-hero__kicker">HallSync Admin</p>
            <h1 class="admin-overview-hero__title">Community <span>Announcements</span></h1>
            <span class="admin-overview-hero__subtitle">Create important updates, set priority levels, and keep residents informed.</span>
        </div>
        <div class="admin-overview-hero__actions">
            <a href="{{ route('announcements.create') }}" class="admin-hero-action admin-hero-action-primary">New Announcement</a>
        </div>
    </section>

    <div class="announcement-standards-panel">
        <div class="announcement-standards-layout">
            <div>
                <div class="announcement-standards-kicker">
                    Publishing Standards
                </div>
                <p class="announcement-standards-copy">
                    Keep notices concise, use Important for facility-impacting updates, reserve Urgent for immediate action, and keep titles clear enough to scan at a glance.
                </p>
            </div>
        </div>
    </div>

    {{-- ANNOUNCEMENTS LIST --}}
    <div class="admin-panel-card" style="
        background: #6B4F3A;
        border-radius: var(--hs-radius-lg);
        padding: 0;
        box-shadow: var(--hs-shadow-md);
        border: 1px solid rgba(107, 79, 58, 0.22);
    ">
        <div class="admin-brown-panel-head announcement-panel-head" style="display: flex; justify-content: space-between; align-items: center; gap: var(--hs-space-5); margin-bottom: 0; flex-wrap: wrap;">
            <div>
                <h2 style="
                    font-size: var(--hs-font-title);
                    color: #F8F3EA;
                    font-family: 'Playfair Display', serif;
                    margin: 0;
                ">
                    Published Announcements
                </h2>
                <p style="margin: var(--hs-space-2) 0 0; color: #9F9383; font-size: var(--hs-font-body);">
                    Resident-facing updates, maintenance notices, and community advisories.
                </p>
            </div>
        </div>

        <div class="announcement-list-shell">
            <div data-progressive-list>
            @forelse($announcements ?? collect() as $announcement)
                <article class="announcement-card" data-progressive-item>
                    <div class="announcement-card__body">
                        <div class="announcement-card__content">
                            <small class="announcement-card__clean-meta">
                                ANNOUNCEMENT #{{ $announcement->id }} &middot; {{ strtoupper($announcement->priority) }} &middot; {{ $announcement->created_at->format('M d, Y') }}
                            </small>
                            <h3 class="announcement-card__title">
                                {{ $announcement->title }}
                            </h3>

                            <p class="announcement-card__excerpt">
                                {{ Str::limit($announcement->content, 280) }}
                            </p>
                        </div>

                        <div class="announcement-card__actions" aria-label="Announcement actions">
                            <a href="{{ route('announcements.show', $announcement) }}" class="announcement-action announcement-action--details">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                View Details
                            </a>
                            <a href="{{ route('announcements.edit', $announcement) }}" class="announcement-action announcement-action--edit">
                                Edit
                            </a>
                            <form action="{{ route('announcements.destroy', $announcement) }}"
                                  method="POST"
                                  data-confirm-message="Delete this announcement? This cannot be undone."
                                  data-prevent-double-submit
                                  data-submitting-text="Deleting Announcement...">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="announcement-action announcement-action--delete">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m-7 0h8m-5-3h2a1 1 0 011 1v2H9V5a1 1 0 011-1z"></path>
                                    </svg>
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </article>
            @empty
                <x-admin-empty-state icon="announcement" title="No announcements yet" description="Be the first to inform residents about important updates, events, or maintenance schedules." :action-href="route('announcements.create')" action-label="Create First Announcement" />
            @endforelse
            </div>
        </div>
    </div>

</div>

<style>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600;700&display=swap');
.admin-shell {
    max-width: 1580px;
    width: 100%;
    margin: 0 auto;
}
.admin-shell > div:first-of-type {
    position: relative !important;
    overflow: hidden !important;
    border-radius: 20px !important;
    background: linear-gradient(120deg, #111009 0%, #1C1A12 50%, #201E14 100%) !important;
    border: 1px solid rgba(214, 168, 91, 0.18) !important;
    box-shadow: none !important;
}
.admin-shell > div:first-of-type::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image: linear-gradient(rgba(214, 168, 91, 0.04) 1px, transparent 1px), linear-gradient(90deg, rgba(214, 168, 91, 0.04) 1px, transparent 1px);
    background-size: 48px 48px;
    pointer-events: none;
}
.admin-shell > div:first-of-type > div.absolute:first-child {
    top: -60px !important;
    right: -40px !important;
    width: 280px !important;
    height: 280px !important;
    background: radial-gradient(circle, rgba(214, 168, 91, 0.15) 0%, transparent 70%) !important;
    opacity: 1 !important;
    filter: none !important;
}
.admin-shell > div:first-of-type > div.absolute:nth-child(2) {
    display: none !important;
}
.admin-shell > div:first-of-type > div.relative {
    padding: 36px 44px !important;
}
.admin-shell > div:first-of-type > div.relative > div {
    align-items: center !important;
}
.admin-shell > div:first-of-type .mb-3 {
    margin-bottom: 12px !important;
}
.admin-shell > div:first-of-type .mb-3 span {
    display: inline-flex !important;
    align-items: center !important;
    gap: 8px !important;
    font-size: 0.875rem !important;
    letter-spacing: 0.18em !important;
    text-transform: uppercase !important;
    color: #d6a85b !important;
    font-weight: 700 !important;
}
.admin-shell > div:first-of-type .mb-3 span::before {
    content: '';
    width: 6px;
    height: 6px;
    border-radius: 999px;
    background: #d6a85b;
    display: inline-block;
}
.admin-shell > div:first-of-type h1 {
    color: #f0e9df !important;
    font-family: 'Playfair Display', serif !important;
    font-size: clamp(2.5rem, 4vw, 3.5rem) !important;
    font-weight: 700 !important;
    line-height: 1.12 !important;
    margin-bottom: 12px !important;
}
.admin-shell > div:first-of-type p {
    color: rgba(255, 255, 255, 0.62) !important;
    font-size: 1.125rem !important;
    max-width: 760px !important;
}
.announcement-standards-panel {
    color: #4d4135;
    padding: 16px 0 18px;
    border-top: 1px solid #e3d8ca;
    border-bottom: 1px solid rgba(227, 216, 202, 0.58);
    background: transparent !important;
    box-shadow: none !important;
}
.announcement-standards-layout {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
}
.announcement-standards-kicker {
    margin-bottom: 8px;
    color: #4d4135 !important;
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.18em;
    line-height: 1;
    text-transform: uppercase;
}
.announcement-standards-copy {
    max-width: 760px;
    margin: 0;
    color: #6f6255 !important;
    font-size: 0.86rem;
    line-height: 1.55;
    font-weight: 400 !important;
}
.announcement-standards-panel span {
    color: #5f5143 !important;
}
.announcement-priority-legend {
    display: grid;
    gap: 6px;
    min-width: 190px;
}
.announcement-priority-key {
    position: relative;
    display: block;
    padding-left: 14px;
    color: #6f6255 !important;
    font-size: 0.74rem;
    font-weight: 600;
    line-height: 1.35;
}
.announcement-priority-key::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    width: 5px;
    height: 16px;
    border-radius: 999px;
    background: #8f877c;
    transform: translateY(-50%);
}
.announcement-priority-key-important::before {
    background: #b47721;
}
.announcement-priority-key-urgent::before {
    background: #bd5349;
}
.announcement-panel-head {
    padding: 28px 30px 22px;
}
.announcement-list-shell {
    margin: 0 24px 24px;
    padding: 0;
    overflow: hidden;
    border-radius: 10px;
    background: #fffdf8;
    border: 0;
    box-shadow: none;
}
.admin-hero-action-primary{
    /* --- Layout & Structure (From Main Button) --- */
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 45px;
    padding: 0 20px;
    border: none; /* Removed the harsh red/brown border to favor the gradient */
    border-radius: 777px;
    white-space: nowrap;

    /* --- Typography (From Main Button) --- */
    font-size: .74rem;
    font-weight: 800;
    letter-spacing: .075em;
    line-height: 1;
    text-transform: uppercase;
    text-decoration: none;

    /* --- Color & Depth (From "Better" Button) --- */
    background: linear-gradient(90deg, #B8842F 0%, #D6A85B 100%);
    color: #FFFFFF;
    box-shadow: 0 12px 28px rgba(199, 150, 69, 0.3);

    /* --- Smooth Transitions (From "Better" Button) --- */
    transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.3s ease;
}

/* --- The Combined Premium Hover State --- */
.admin-hero-action-primary:hover,
.admin-hero-action-primary:focus-visible {
    outline: none;
    /* Clean CSS alternative to the JS onmouseover */
    transform: translateY(-3px); 
    box-shadow: 0 20px 40px rgba(199, 150, 69, 0.4);
    
    /* Optional: If you want a slight color shifting glow on hover, 
       we can subtly shift the gradient look by brightening it slightly */
    filter: brightness(1.05); 
}

/* --- Clean Reset for Active (Click) State --- */
.admin-hero-action-primary:active {
    transform: translateY(-1px);
    box-shadow: 0 8px 16px rgba(199, 150, 69, 0.3);
}

.announcement-card {
    background: #fbf8f3;
    border: 0;
    border-radius: 0;
    padding: 22px 20px;
    margin-bottom: 0;
    box-shadow: none;
    transition: background-color 0.18s ease, border-color 0.18s ease;
}
.announcement-card + .announcement-card {
    border-top: 1px solid #e3d8ca;
}
.announcement-card:hover {
    background: #fffdf9;
    box-shadow: none;
}
.announcement-card__topline, .announcement-card__body, .announcement-card__meta, .announcement-card__record, .announcement-card__actions {
    display: flex;
}
.announcement-card__topline {
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 20px;
}
.announcement-card__record {
    align-items: center;
    gap: 12px;
    color: #AFA394;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.14em;
    text-transform: uppercase;
}
.announcement-card__dot {
    width: 9px;
    height: 9px;
    border-radius: 999px;
    display: inline-block;
    box-shadow: 0 0 0 4px rgba(214, 168, 91, 0.06);
}
.announcement-card__date {
    color: #A89F91;
    font-size: 13px;
    white-space: nowrap;
}
.announcement-card__body {
    align-items: center;
    justify-content: space-between;
    gap: 18px;
}
.announcement-card__content {
    min-width: 0;
    max-width: 880px;
}
.announcement-card__meta {
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 14px;
}
.announcement-card__priority {
    border: 1px solid;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.10em;
    line-height: 1;
    padding: 8px 15px;
    text-transform: uppercase;
}
.announcement-card__age {
    color: #A89F91;
    font-size: 13px;
}
.announcement-card__clean-meta {
    color: #9b8d81;
    font-size: .68rem;
    font-weight: 700;
    text-transform: uppercase;
}
.announcement-card__title {
    color: #342a23;
    font-family: 'Inter', sans-serif;
    font-size: .95rem;
    font-weight: 400;
    line-height: 1.35;
    margin: 7px 0 4px;
}
.announcement-card__excerpt {
    color: #786b60;
    font-size: .8rem;
    line-height: 1.55;
    margin: 0;
}
.announcement-card__actions {
    align-items: center;
    flex-shrink: 0;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: flex-end;
}
.announcement-card__actions form {
    margin: 0;
}
.announcement-action {
    align-items: center;
    border: 1px solid;
    border-radius: 8px;
    cursor: pointer;
    display: inline-flex;
    font-family: inherit;
    font-size: .74rem;
    font-weight: 700;
    gap: 8px;
    justify-content: center;
    line-height: 1.2;
    min-height: 38px;
    min-width: 105px;
    padding: 0.5rem 1rem;
    text-decoration: none;
    transition: background 0.18s ease, border-color 0.18s ease, color 0.18s ease, transform 0.18s ease;
    white-space: nowrap;
}
.announcement-action svg {
    display: block;
    width: 14px;
    height: 14px;
    flex: 0 0 14px;
}
.announcement-action:hover {
    transform: translateY(-1px);
}
.announcement-action--details {
    min-width: 130px;
    background: rgba(214, 168, 91, 0.16);
    border-color: rgba(214, 168, 91, 0.46);
    color: #7a4f16;
}
.announcement-action--details:hover {
    background: rgba(214, 168, 91, 0.24);
    border-color: rgba(214, 168, 91, 0.62);
    color: #65400f;
}
.announcement-action--edit {
    background: linear-gradient(135deg, #c79745 0%, #d6a85b 100%);
    border-color: rgba(199, 151, 69, 0.55);
    color: #1a1714;
}
.announcement-action--edit:hover {
    background: linear-gradient(135deg, #b8842f 0%, #c79745 100%);
    border-color: rgba(184, 132, 47, 0.65);
}
.announcement-action--delete {
    min-width: 95px;
    background: #fde8e6;
    border-color: rgba(224, 112, 96, 0.34);
    color: #9d3129;
}
.announcement-action--delete:hover {
    background: #fbd8d5;
    border-color: rgba(224, 112, 96, 0.48);
    color: #842820;
}
body.role-manager .admin-content-shell .admin-announcements-page .announcement-card .announcement-action--edit {
    min-height: 38px !important;
    padding: 0.5rem 1rem !important;
    border-color: rgba(199, 151, 69, 0.55) !important;
    background: linear-gradient(135deg, #c79745 0%, #d6a85b 100%) !important;
    color: #1a1714 !important;
}
body.role-manager .admin-content-shell .admin-announcements-page .announcement-card .announcement-action--edit:hover {
    border-color: rgba(184, 132, 47, 0.65) !important;
    background: linear-gradient(135deg, #b8842f 0%, #c79745 100%) !important;
}
body.role-manager .admin-content-shell .admin-announcements-page .announcement-card .announcement-action--details {
    min-height: 38px !important;
    min-width: 130px !important;
    padding: 0.5rem 1rem !important;
    border-color: rgba(214, 168, 91, 0.46) !important;
    background: rgba(214, 168, 91, 0.16) !important;
    color: #7a4f16 !important;
}
body.role-manager .admin-content-shell .admin-announcements-page .announcement-card .announcement-action--details:hover {
    border-color: rgba(214, 168, 91, 0.62) !important;
    background: rgba(214, 168, 91, 0.24) !important;
    color: #65400f !important;
}
body.role-manager .admin-content-shell .admin-announcements-page .announcement-card .announcement-action--delete {
    min-height: 38px !important;
    min-width: 95px !important;
    padding: 0.5rem 1rem !important;
    border-color: rgba(224, 112, 96, 0.34) !important;
    background: #fde8e6 !important;
    color: #9d3129 !important;
}
body.role-manager .admin-content-shell .admin-announcements-page .announcement-card .announcement-action--delete:hover {
    border-color: rgba(224, 112, 96, 0.48) !important;
    background: #fbd8d5 !important;
    color: #842820 !important;
}
body.role-manager .admin-content-shell .admin-announcements-page .announcement-standards-panel {
    padding: 16px 0 18px !important;
    border: 0 !important;
    border-top: 1px solid #e3d8ca !important;
    border-bottom: 1px solid rgba(227, 216, 202, 0.58) !important;
    background: transparent !important;
    box-shadow: none !important;
}
body.role-manager .admin-content-shell .admin-announcements-page .announcement-standards-kicker {
    color: #4d4135 !important;
    font-weight: 700 !important;
}
body.role-manager .admin-content-shell .admin-announcements-page .announcement-standards-copy,
body.role-manager .admin-content-shell .admin-announcements-page .announcement-priority-key {
    color: #6f6255 !important;
    font-weight: 400 !important;
}
body.role-manager .admin-content-shell .admin-announcements-page .announcement-priority-key {
    font-weight: 600 !important;
}
body.role-manager .admin-content-shell .admin-announcements-page .admin-panel-card {
    background: #6B4F3A !important;
    border-color: rgba(107, 79, 58, 0.22) !important;
    border-radius: 14px !important;
    padding: 0 !important;
    box-shadow: 0 14px 28px rgba(79, 58, 44, 0.12) !important;
}
body.role-manager .admin-content-shell .admin-announcements-page .announcement-list-shell {
    margin: 0 24px 24px !important;
    padding: 0 !important;
    overflow: hidden !important;
    border: 0 !important;
    border-radius: 10px !important;
    background: #fffdf8 !important;
}
body.role-manager .admin-content-shell .admin-announcements-page .announcement-card {
    border: 0 !important;
    border-radius: 0 !important;
    margin-bottom: 0 !important;
    padding: 22px 20px !important;
}
body.role-manager .admin-content-shell .admin-announcements-page .announcement-card + .announcement-card {
    border-top: 1px solid #e3d8ca !important;
}
body.role-manager .admin-content-shell .admin-announcements-page .announcement-panel-head {
    padding: 28px 30px 22px !important;
}
@media (max-width:1024px) {
    .announcement-standards-layout {
        align-items: flex-start;
        flex-direction: column;
    }
    .announcement-card__body {
        align-items: stretch;
        flex-direction: column;
    }
    .announcement-card__actions {
        justify-content: flex-start;
    }
}
@media (max-width:768px) {
    .admin-shell > div:first-of-type > div.relative {
        padding: 24px !important;
    }
    .announcement-card {
        padding: 22px;
    }
    .announcement-card__topline {
        align-items: flex-start;
        flex-direction: column;
        gap: 10px;
    }
    .announcement-card__date {
        white-space: normal;
    }
    .announcement-card__actions, .announcement-card__actions form, .announcement-action {
        width: 100%;
    }
}
::-webkit-scrollbar {
    width: 6px;
}
::-webkit-scrollbar-track {
    background: #2A2C30;
    border-radius: 10px;
}
::-webkit-scrollbar-thumb {
    background: linear-gradient(#D6A85B, #B8842F);
    border-radius: 10px;
}
::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(#C49A4A, #A37222);
}
</style>
</x-app-layout>
