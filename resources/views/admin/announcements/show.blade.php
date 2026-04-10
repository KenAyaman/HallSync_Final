<x-app-layout>
<div class="admin-shell admin-announce-show-page">
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
                <div class="max-w-3xl">
                    <div class="mb-3">
                        <span class="inline-block text-[11px] tracking-[0.30em] uppercase"
                              style="color: #D2A04C; font-weight: 700;">
                            Announcement Center
                        </span>
                    </div>

                    <h1 class="text-4xl md:text-5xl font-bold leading-[1.05] mb-4"
                        style="font-family: 'Playfair Display', serif; color: #F8F3EA;">
                        Announcement<br>
                        <span style="color: #F3E5CF;">Details</span>
                    </h1>

                    <p class="text-base md:text-lg leading-relaxed max-w-2xl"
                       style="color: rgba(255,255,255,0.82);">
                        Review the published content, priority level, and visibility status before making further changes.
                    </p>
                </div>

                <div class="shrink-0">
                    <a href="{{ route('announcements.index') }}"
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
                        ">
                        Back to Announcements
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="admin-panel-card admin-main-panel">
            <div class="admin-panel-head">
                <div>
                    <h2 class="admin-panel-title">{{ $announcement->title }}</h2>
                    <p class="admin-panel-sub">Resident-facing announcement content and publishing summary.</p>
                </div>

                <span class="admin-panel-badge priority-{{ $announcement->priority }}">
                    {{ ucfirst($announcement->priority) }}
                </span>
            </div>

            <div class="admin-panel-divider"></div>

            <div class="admin-announcement-meta-grid">
                <div class="admin-meta-item">
                    <span class="admin-meta-label">Created</span>
                    <span class="admin-meta-value">{{ $announcement->created_at->format('M d, Y h:i A') }}</span>
                </div>
                <div class="admin-meta-item">
                    <span class="admin-meta-label">Updated</span>
                    <span class="admin-meta-value">{{ $announcement->updated_at->format('M d, Y h:i A') }}</span>
                </div>
                <div class="admin-meta-item">
                    <span class="admin-meta-label">Visibility</span>
                    <span class="admin-meta-value">{{ $announcement->is_active ? 'Visible to residents' : 'Hidden from residents' }}</span>
                </div>
            </div>

            <div class="admin-announcement-body">
                {!! nl2br(e($announcement->content)) !!}
            </div>

            <div class="admin-form-actions">
                <a href="{{ route('announcements.edit', $announcement) }}" class="admin-primary-btn admin-action-link">
                    Edit Announcement
                </a>
                <form action="{{ route('announcements.destroy', $announcement) }}" method="POST" onsubmit="return confirm('Delete this announcement?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="admin-secondary-btn admin-danger-btn">Delete</button>
                </form>
            </div>
    </div>
</div>

<style>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap');

.admin-shell.admin-announce-show-page {
    font-family: 'Inter', sans-serif;
    color: #c4b8a8;
    min-height: 100vh;
    padding: 0;
    max-width: 1580px;
    width: 100%;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 28px;
}

.admin-panel-card {
    background: rgba(42,44,48,0.78);
    border-radius: 20px;
    padding: 24px;
    border: 1px solid rgba(214,168,91,0.14);
    backdrop-filter: blur(10px);
    box-shadow: 0 12px 24px rgba(0,0,0,0.14);
}

.admin-main-panel {
    padding: 26px 28px;
}

.admin-panel-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    margin-bottom: 16px;
    flex-wrap: wrap;
}

.admin-panel-title {
    margin: 0;
    color: #F0E9DF;
    font-size: 1.5rem;
    font-weight: 600;
    font-family: 'Playfair Display', serif;
}

.admin-panel-sub {
    margin-top: 4px;
    color: #8A7A66;
    font-size: 0.95rem;
}

.admin-panel-badge {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.16em;
    text-transform: uppercase;
    padding: 8px 12px;
    border-radius: 999px;
}

.admin-panel-badge.priority-normal {
    color: #C4B6A1;
    background: rgba(168,159,145,0.10);
    border: 1px solid rgba(168,159,145,0.16);
}

.admin-panel-badge.priority-important {
    color: #D6A85B;
    background: rgba(214,168,91,0.10);
    border: 1px solid rgba(214,168,91,0.16);
}

.admin-panel-badge.priority-urgent {
    color: #F0B3A9;
    background: rgba(224,112,96,0.10);
    border: 1px solid rgba(224,112,96,0.16);
}

.admin-panel-divider {
    height: 1px;
    background: linear-gradient(to right, rgba(214,168,91,0.3), rgba(214,168,91,0.05), transparent);
    margin-bottom: 24px;
}

.admin-announcement-body {
    margin-top: 18px;
    color: #E5DACE;
    font-size: 1rem;
    line-height: 1.9;
    padding: 20px;
    border-radius: 18px;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.05);
}

.admin-form-actions {
    padding-top: 18px;
    margin-top: 18px;
    border-top: 1px solid rgba(214,168,91,0.10);
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.admin-primary-btn,
.admin-secondary-btn {
    padding: 14px 24px;
    border-radius: 999px;
    font-weight: 700;
    border: none;
    cursor: pointer;
    text-decoration: none;
}

.admin-action-link {
    background: linear-gradient(90deg, #B8842F 0%, #D6A85B 100%);
    color: #FFFFFF;
}

.admin-secondary-btn {
    background: rgba(255,255,255,0.04);
    color: #D0C8B8;
    border: 1px solid rgba(214,168,91,0.14);
}

.admin-danger-btn {
    background: rgba(224,112,96,0.12);
    color: #F0B3A9;
    border-color: rgba(224,112,96,0.20);
}

.admin-announcement-meta-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 12px;
}

.admin-meta-item {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.05);
    border-radius: 16px;
    padding: 14px 16px;
}

.admin-meta-label {
    display: block;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.12em;
    color: #8A7A66;
    margin-bottom: 6px;
    font-weight: 700;
}

.admin-meta-value {
    color: #F0E9DF;
    font-size: 14px;
    line-height: 1.6;
}

@media (max-width: 1024px) {
    .admin-announcement-meta-grid {
        grid-template-columns: 1fr;
    }
}
</style>
</x-app-layout>
