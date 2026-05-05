<x-app-layout>
<div class="space-y-8 admin-shell admin-announcements-page">
    {{-- PAGE HEADER --}}
    <div class="relative overflow-hidden rounded-[36px] border border-[#3A342D]"
         style="
            background:
                linear-gradient(115deg,
                    #1F2023 0%,
                    #24262B 38%,
                    #2C2C2F 62%,
                    #3B3023 100%);
            box-shadow: 0 18px 50px rgba(0, 0, 0, 0.18);
         ">

        <div class="absolute top-[-90px] right-[10%] w-[320px] h-[320px] rounded-full blur-3xl opacity-20"
             style="background: rgba(199, 151, 69, 0.35);"></div>

        <div class="absolute bottom-[-120px] left-[18%] w-[260px] h-[260px] rounded-full blur-3xl opacity-10"
             style="background: rgba(255,255,255,0.18);"></div>

        <div class="relative z-10 px-8 py-10 md:px-14 md:py-12">
            <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                <div class="max-w-2xl">
                    <div class="mb-3">
                        <span class="inline-block text-[11px] tracking-[0.30em] uppercase"
                              style="color: #D2A04C; font-weight: 700;">
                            Announcement Center
                        </span>
                    </div>

                    <h1 class="text-4xl md:text-5xl font-bold leading-[1.05] mb-4"
                        style="font-family: 'Playfair Display', serif; color: #F8F3EA;">
                        Community<br>
                        <span style="color: #F3E5CF;">Announcements</span>
                    </h1>

                    <p class="text-base md:text-lg leading-relaxed max-w-xl"
                       style="color: rgba(255,255,255,0.82);">
                        Create important updates, set priority levels for urgent notices,
                        and keep residents informed about facility changes and events.
                    </p>
                </div>

                <div class="shrink-0">
                    <a href="{{ route('announcements.create') }}"
                       style="
                            background: linear-gradient(90deg, #B8842F 0%, #D6A85B 100%);
                            color: #FFFFFF;
                            padding: 15px 32px;
                            border-radius: 999px;
                            font-weight: 700;
                            text-decoration: none;
                            box-shadow: 0 12px 28px rgba(199, 150, 69, 0.3);
                            transition: all 0.3s ease;
                            display: inline-block;
                        "
                       onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 20px 40px rgba(199,150,69,0.4)';"
                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 12px 28px rgba(199,150,69,0.3)';">
                        New Announcement
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="announcement-standards-panel" style="
        background: linear-gradient(135deg, rgba(226, 211, 190, 0.92), rgba(238, 228, 214, 0.88));
        border: 1px solid rgba(130, 98, 62, 0.24);
        border-radius: 24px;
        padding: 22px 26px;
        box-shadow: none;
    ">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <div style="font-size: 11px; letter-spacing: 0.18em; text-transform: uppercase; color: #D6A85B; font-weight: 700; margin-bottom: 8px;">
                    Publishing Standards
                </div>
                <p style="color: #D8CEC0; font-size: 14px; line-height: 1.7; margin: 0; max-width: 780px;">
                    Keep notices concise, use <span style="color:#E4C58E;">Important</span> for facility-impacting updates,
                    reserve <span style="color:#F0B3A9;">Urgent</span> for immediate action, and keep titles clear enough to scan at a glance.
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <span style="padding: 9px 14px; border-radius: 999px; background: rgba(168,159,145,0.12); color: #C4B6A1; border: 1px solid rgba(168,159,145,0.18); font-size: 12px; font-weight: 600;">
                    Standard updates
                </span>
                <span style="padding: 9px 14px; border-radius: 999px; background: rgba(214,168,91,0.12); color: #E4C58E; border: 1px solid rgba(214,168,91,0.18); font-size: 12px; font-weight: 600;">
                    Important notices
                </span>
                <span style="padding: 9px 14px; border-radius: 999px; background: rgba(224,112,96,0.12); color: #F0B3A9; border: 1px solid rgba(224,112,96,0.18); font-size: 12px; font-weight: 600;">
                    Urgent alerts
                </span>
            </div>
        </div>
    </div>

    {{-- ANNOUNCEMENTS LIST --}}
    <div style="
        background: linear-gradient(180deg, #2A2C30 0%, #1F2023 100%);
        border-radius: 32px;
        padding: 40px;
        box-shadow: 0 16px 48px rgba(0,0,0,0.4);
        border: 1px solid #3A342D;
    ">
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 18px; margin-bottom: 32px; flex-wrap: wrap;">
            <div>
                <h2 style="
                    font-size: 28px;
                    font-weight: 600;
                    color: #F8F3EA;
                    font-family: 'Playfair Display', serif;
                    margin: 0;
                ">
                    Published Announcements
                </h2>
                <p style="margin: 8px 0 0; color: #9F9383; font-size: 14px;">
                    Resident-facing updates, maintenance notices, and community advisories.
                </p>
            </div>
            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <span style="
                    background: rgba(90,138,90,0.12);
                    color: #B9D9B9;
                    padding: 12px 18px;
                    border-radius: 999px;
                    font-weight: 700;
                    border: 1px solid rgba(90,138,90,0.22);
                    font-size: 13px;
                ">
                    {{ $activeCount ?? 0 }} Active
                </span>
                <span style="
                    background: rgba(168,159,145,0.12);
                    color: #D4C8B8;
                    padding: 12px 18px;
                    border-radius: 999px;
                    font-weight: 700;
                    border: 1px solid rgba(168,159,145,0.22);
                    font-size: 13px;
                ">
                    {{ $draftCount ?? 0 }} Hidden
                </span>
            </div>
        </div>

        <div style="height: 1px; background: linear-gradient(to right, rgba(214,168,91,0.4), transparent); margin-bottom: 32px;"></div>

        @forelse($announcements ?? collect() as $announcement)
            <article class="announcement-card">
                <div class="announcement-card__topline">
                    <div class="announcement-card__record">
                        <span class="announcement-card__dot" style="background:
                            {{ $announcement->priority === 'urgent' ? '#E07060' : ($announcement->priority === 'important' ? '#D6A85B' : '#8F877C') }};">
                        </span>
                        <span>Notice Record</span>
                    </div>
                    <time class="announcement-card__date" datetime="{{ $announcement->created_at->toIso8601String() }}">
                        Published {{ $announcement->created_at->format('M d, Y h:i A') }}
                    </time>
                </div>

                <div class="announcement-card__body">
                    <div class="announcement-card__content">
                        <div class="announcement-card__meta">
                            <span class="announcement-card__priority" style="background:
                                {{ $announcement->priority === 'urgent' ? 'rgba(224,112,96,0.12)' : ($announcement->priority === 'important' ? 'rgba(214,168,91,0.12)' : 'rgba(168,159,145,0.12)') }}; color:
                                {{ $announcement->priority === 'urgent' ? '#F0B3A9' : ($announcement->priority === 'important' ? '#E4C58E' : '#C4B6A1') }}; border-color:
                                {{ $announcement->priority === 'urgent' ? 'rgba(224,112,96,0.22)' : ($announcement->priority === 'important' ? 'rgba(214,168,91,0.22)' : 'rgba(168,159,145,0.22)') }};">
                                {{ ucfirst($announcement->priority) }}
                            </span>
                            <span class="announcement-card__age">
                                Live since {{ $announcement->created_at->diffForHumans() }}
                            </span>
                        </div>

                        <h3 class="announcement-card__title">
                            {{ $announcement->title }}
                        </h3>

                        <p class="announcement-card__excerpt">
                            {{ Str::limit($announcement->content, 280) }}
                        </p>
                    </div>

                    <div class="announcement-card__actions" aria-label="Announcement actions">
                        <a href="{{ route('announcements.show', $announcement) }}" class="announcement-action announcement-action--details">
                            View Details
                        </a>
                        <a href="{{ route('announcements.edit', $announcement) }}" class="announcement-action announcement-action--edit">
                            Edit
                        </a>
                        <form action="{{ route('announcements.destroy', $announcement) }}" method="POST" onsubmit="return confirm('Delete this announcement?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="announcement-action announcement-action--delete">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </article>
        @empty
            <div style="text-align: center; padding: 80px 60px; background: linear-gradient(135deg, rgba(37,39,42,0.6), rgba(31,32,35,0.6)); border: 1px dashed rgba(214,168,91,0.25); border-radius: 32px;">
                <div style="width: 88px; height: 88px; border-radius: 999px; margin: 0 auto 32px; display:flex; align-items:center; justify-content:center; background: rgba(214,168,91,0.15); font-size: 44px;">
                    A
                </div>
                <h3 style="font-size: 32px; font-weight: 700; color: #F8F3EA; margin-bottom: 16px; font-family: 'Playfair Display', serif;">
                    No announcements yet
                </h3>
                <p style="color: #D0C8B8; font-size: 18px; margin-bottom: 40px; line-height: 1.6;">
                    Be the first to inform residents about important updates, events, or maintenance schedules.
                </p>
                <a href="{{ route('announcements.create') }}" style="
                    background: linear-gradient(90deg, #B8842F 0%, #D6A85B 100%);
                    color: white;
                    padding: 16px 40px;
                    border-radius: 999px;
                    font-weight: 700;
                    text-decoration: none;
                    font-size: 16px;
                    box-shadow: 0 12px 36px rgba(199,151,69,0.4);
                    transition: all 0.3s;
                " onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                    Create First Announcement
                </a>
            </div>
        @endforelse
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
    border: 1px solid rgba(214,168,91,0.18) !important;
    box-shadow: none !important;
}

.admin-shell > div:first-of-type::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(rgba(214,168,91,0.04) 1px, transparent 1px),
        linear-gradient(90deg, rgba(214,168,91,0.04) 1px, transparent 1px);
    background-size: 48px 48px;
    pointer-events: none;
}

.admin-shell > div:first-of-type > div.absolute:first-child {
    top: -60px !important;
    right: -40px !important;
    width: 280px !important;
    height: 280px !important;
    background: radial-gradient(circle, rgba(214,168,91,0.15) 0%, transparent 70%) !important;
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
    color: rgba(255,255,255,0.62) !important;
    font-size: 1.125rem !important;
    max-width: 760px !important;
}

.announcement-standards-panel {
    color: #4d4135;
}

.announcement-standards-panel p {
    color: #5f5143 !important;
}

.announcement-standards-panel span {
    color: #805928 !important;
}

.announcement-standards-panel .flex.flex-wrap.gap-2 span {
    background: rgba(255, 246, 233, 0.45) !important;
    border-color: rgba(130, 98, 62, 0.18) !important;
}

.announcement-card {
    background:
        linear-gradient(135deg, rgba(49, 50, 54, 0.96) 0%, rgba(38, 40, 43, 0.98) 100%);
    border: 1px solid rgba(214,168,91,0.14);
    border-radius: 20px;
    padding: 24px 28px;
    margin-bottom: 18px;
    box-shadow: 0 10px 28px rgba(0,0,0,0.24);
    transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease;
}

.announcement-card:hover {
    transform: translateY(-2px);
    border-color: rgba(214,168,91,0.26);
    box-shadow: 0 16px 34px rgba(0,0,0,0.32);
}

.announcement-card__topline,
.announcement-card__body,
.announcement-card__meta,
.announcement-card__record,
.announcement-card__actions {
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
    box-shadow: 0 0 0 4px rgba(214,168,91,0.06);
}

.announcement-card__date {
    color: #A89F91;
    font-size: 13px;
    white-space: nowrap;
}

.announcement-card__body {
    align-items: end;
    justify-content: space-between;
    gap: 28px;
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

.announcement-card__title {
    color: #F8F3EA;
    font-family: 'Playfair Display', serif;
    font-size: clamp(1.35rem, 1.5vw, 1.625rem);
    font-weight: 700;
    line-height: 1.2;
    margin: 0 0 12px;
}

.announcement-card__excerpt {
    color: #D0C8B8;
    font-size: 15px;
    line-height: 1.65;
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
    border-radius: 999px;
    cursor: pointer;
    display: inline-flex;
    font-family: inherit;
    font-size: 13px;
    font-weight: 700;
    justify-content: center;
    line-height: 1;
    min-height: 42px;
    padding: 0 18px;
    text-decoration: none;
    transition: background 0.18s ease, border-color 0.18s ease, color 0.18s ease, transform 0.18s ease;
    white-space: nowrap;
}

.announcement-action:hover {
    transform: translateY(-1px);
}

.announcement-action--details {
    background: rgba(255,255,255,0.045);
    border-color: rgba(255,255,255,0.09);
    color: #F0E9DF;
}

.announcement-action--details:hover {
    background: rgba(255,255,255,0.085);
    border-color: rgba(255,255,255,0.15);
}

.announcement-action--edit {
    background: rgba(214,168,91,0.12);
    border-color: rgba(214,168,91,0.24);
    color: #E4C58E;
}

.announcement-action--edit:hover {
    background: rgba(214,168,91,0.18);
}

.announcement-action--delete {
    background: rgba(224,112,96,0.12);
    border-color: rgba(224,112,96,0.24);
    color: #F0B3A9;
}

.announcement-action--delete:hover {
    background: rgba(224,112,96,0.18);
}

@media (max-width: 1024px) {
    .announcement-card__body {
        align-items: stretch;
        flex-direction: column;
    }

    .announcement-card__actions {
        justify-content: flex-start;
    }
}

@media (max-width: 768px) {
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

    .announcement-card__actions,
    .announcement-card__actions form,
    .announcement-action {
        width: 100%;
    }
}

::-webkit-scrollbar { width: 6px; }
::-webkit-scrollbar-track { background: #2A2C30; border-radius: 10px; }
::-webkit-scrollbar-thumb { background: linear-gradient(#D6A85B, #B8842F); border-radius: 10px; }
::-webkit-scrollbar-thumb:hover { background: linear-gradient(#C49A4A, #A37222); }
</style>
</x-app-layout>
