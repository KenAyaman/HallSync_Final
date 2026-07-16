<x-app-layout>
    @php
        $urgentCount = $announcements->where('priority', 'urgent')->count();
        $importantCount = $announcements->where('priority', 'important')->count();
        $latestAnnouncement = $announcements->first();
    @endphp

    <div class="resident-page resident-page-wide">
        <section class="resident-page-hero resident-hero-paper-waves">
            <div class="resident-page-hero-copy">
                <p class="resident-page-kicker">Community Updates</p>
                <h1 class="resident-page-title">Community Announcements</h1>
                <p class="resident-page-subtitle">
                    Stay informed with resident notices, urgent updates, and important reminders from management.
                </p>

                <div class="resident-hero-stat-row">
                    <div class="resident-hero-stat">
                        <span>Urgent</span>
                        <strong>{{ $urgentCount }}</strong>
                    </div>
                    <div class="resident-hero-stat">
                        <span>Important</span>
                        <strong>{{ $importantCount }}</strong>
                    </div>
                    <div class="resident-hero-stat">
                        <span>Latest</span>
                        <strong>{{ $latestAnnouncement ? $latestAnnouncement->created_at->diffForHumans() : 'None' }}</strong>
                    </div>
                </div>
            </div>

            @if(auth()->user()->role === 'manager')
                <div class="resident-page-actions">
                    <a href="{{ route('announcements.create') }}" class="resident-page-btn resident-page-btn-primary">Post Announcement</a>
                </div>
            @endif
        </section>

        <section class="resident-page-panel">
            <div class="resident-page-panel-head">
                <div>
                    <h2>Latest Announcements</h2>
                    <p>The newest updates shared with residents.</p>
                </div>
                <span class="resident-page-eyebrow">Notice Board</span>
            </div>

            <div class="resident-page-divider"></div>

            @if($latestAnnouncement)
                <article class="announcement-spotlight">
                    <div>
                        <div class="announcement-spotlight-label">Spotlight Notice</div>
                        <h3>{{ $latestAnnouncement->title }}</h3>
                        <p>{{ Str::limit($latestAnnouncement->content, 170) }}</p>
                    </div>
                    <a class="resident-announcement-action-btn" href="{{ route('announcements.show', $latestAnnouncement) }}">Open Notice</a>
                </article>
            @endif

            <div class="resident-page-list" data-progressive-list>
                @forelse($announcements as $announcement)
                    <article class="resident-card resident-card-accent resident-card-accent-{{ $announcement->priority }}" data-progressive-item>
                        <div class="resident-card-top">
                            <div>
                                <div class="resident-card-heading">
                                    <h3>{{ $announcement->title }}</h3>
                                    <span class="resident-badge resident-badge-priority-{{ $announcement->priority }}">
                                        {{ $announcement->is_pinned ? 'Pinned Notice' : ($announcement->is_temporary ? 'Temporary Notice' : ucfirst($announcement->priority)) }}
                                    </span>
                                </div>
                                <p class="resident-card-description">{{ Str::limit($announcement->content, 200) }}</p>
                            </div>

                            <div class="resident-card-links resident-card-single-action">
                                <a class="resident-announcement-action-btn" href="{{ route('announcements.show', $announcement) }}">Read More</a>
                            </div>
                        </div>

                        <div class="resident-card-meta-grid">
                            <div class="resident-meta-box">
                                <span>Posted On</span>
                                <strong>{{ $announcement->created_at->format('M d, Y') }}</strong>
                            </div>
                            <div class="resident-meta-box">
                                <span>Notice Level</span>
                                <strong>{{ ucfirst($announcement->priority) }}</strong>
                            </div>
                        </div>
                    </article>
                @empty
                    <x-resident-empty-state icon="announcement" title="No active announcements" description="Check back later for updates from management." />
                @endforelse
            </div>
        </section>
    </div>

    <script>
        document.querySelectorAll('[data-auto-dismiss]').forEach((flash) => {
            setTimeout(() => {
                flash.style.transition = 'opacity 0.35s ease, transform 0.35s ease';
                flash.style.opacity = '0';
                flash.style.transform = 'translateY(-6px)';
                setTimeout(() => flash.remove(), 360);
            }, 3200);
        });
    </script>

    <style>
.resident-page {
    max-width: 1600px;
    margin: 0 auto;
    padding: 24px 16px 32px;
    display: flex;
    flex-direction: column;
    gap: 22px;
}
.resident-page-wide {
    max-width: 1700px;
}
.resident-page-hero, .resident-page-panel, .resident-flash {
    border: 1px solid rgba(214, 168, 91, 0.14);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.14);
}
.resident-page-hero {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    gap: 20px;
    padding: 28px 30px;
    border-radius: 36px;
    background: linear-gradient(115deg, #1F2023 0%, #24262B 38%, #2C2C2F 62%, #3B3023 100%);
    box-shadow: 0 18px 50px rgba(0, 0, 0, 0.18);
}
.resident-page-hero-copy {
    max-width: 860px;
}
.resident-page-kicker {
    margin: 0 0 10px;
    color: #D2A04C;
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.30em;
}
.resident-page-title {
    margin: 0;
    color: #F8F3EA;
    font-family: 'Playfair Display', serif;
    font-size: clamp(2.4rem, 4.6vw, 3.8rem);
    line-height: 1.05;
}
.resident-page-subtitle {
    margin: 12px 0 0;
    color: rgba(255, 255, 255, 0.82);
    font-size: 1.02rem;
    line-height: 1.7;
    max-width: 760px;
}
.resident-hero-stat-row {
    display: flex;
    flex-wrap: wrap;
    gap: 14px;
    margin-top: 22px;
}
.resident-hero-stat {
    min-width: 110px;
    padding: 12px 14px;
    border-radius: 16px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.07);
}
.resident-hero-stat span {
    display: block;
    color: #A89376;
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 0.14em;
    font-weight: 700;
}
.resident-hero-stat strong {
    display: block;
    margin-top: 6px;
    color: #F0E9DF;
    font-size: 1.05rem;
    font-weight: 700;
}
.resident-page-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 12px 22px;
    border-radius: 999px;
    text-decoration: none;
    font-size: 0.92rem;
    font-weight: 700;
    transition: transform 0.2s ease;
    background: linear-gradient(95deg, #b8842f, #d6a85b);
    color: #17120d;
}
.resident-page-btn:hover {
    transform: translateY(-1px);
}
.resident-flash {
    padding: 16px 20px;
    border-radius: 20px;
    font-size: 0.95rem;
    font-weight: 600;
    background: rgba(42, 44, 48, 0.78);
    color: #F0E9DF;
    backdrop-filter: blur(10px);
}
.resident-announcement-support-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 22px;
}
.resident-page-panel {
    padding: 26px 28px;
    border-radius: 20px;
    background: rgba(42, 44, 48, 0.78);
    backdrop-filter: blur(10px);
}
.resident-support-panel {
    padding: 14px 16px;
}
.resident-page-panel-head {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 16px;
    margin-bottom: 16px;
}
.resident-page-panel-head h2 {
    margin: 0;
    color: #F0E9DF;
    font-size: 1.5rem;
    font-family: 'Playfair Display', serif;
}
.resident-support-panel .resident-page-panel-head h2 {
    font-size: 1.18rem;
}
.resident-page-panel-head p {
    margin: 4px 0 0;
    color: #8A7A66;
    font-size: 0.95rem;
}
.resident-support-panel .resident-page-panel-head p {
    font-size: 0.84rem;
}
.resident-page-eyebrow {
    color: #D6A85B;
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.16em;
}
.resident-page-divider {
    height: 1px;
    background: linear-gradient(to right, rgba(214, 168, 91, 0.3), rgba(214, 168, 91, 0.05), transparent);
    margin-bottom: 18px;
}
.resident-page-list {
    display: grid;
    gap: 14px;
}
.resident-note-list {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}
.announcement-spotlight {
    position: relative;
    overflow: hidden;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 24px;
    margin-bottom: 18px;
    padding: 22px 24px;
    border-radius: 20px;
    background: radial-gradient(circle at 88% 18%, rgba(214, 168, 91, 0.18), transparent 32%), linear-gradient(135deg, rgba(68, 59, 46, 0.96), rgba(48, 48, 46, 0.94));
    border: 1px solid rgba(214, 168, 91, 0.24);
    box-shadow: 0 12px 26px rgba(0, 0, 0, 0.16);
}
.announcement-spotlight::after {
    content: '';
    position: absolute;
    top: -54px;
    right: -34px;
    width: 142px;
    height: 142px;
    border: 1px solid rgba(214, 168, 91, 0.18);
    border-radius: 999px;
    box-shadow: -78px 52px 0 -42px rgba(0, 0, 0, 0), -78px 52px 0 -41px rgba(214, 168, 91, 0.14), -132px 86px 0 -56px rgba(0, 0, 0, 0), -132px 86px 0 -55px rgba(214, 168, 91, 0.10), -26px 126px 0 -50px rgba(0, 0, 0, 0), -26px 126px 0 -49px rgba(214, 168, 91, 0.12);
    pointer-events: none;
}
.announcement-spotlight-label {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #D6A85B;
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.18em;
}
.announcement-spotlight-label::before {
    content: '';
    width: 8px;
    height: 8px;
    border-radius: 999px;
    background: #d6a85b;
    box-shadow: 0 0 18px rgba(214, 168, 91, 0.8);
}
.announcement-spotlight h3 {
    margin: 10px 0 0;
    color: #F0E9DF;
    font-family: 'Playfair Display', serif;
    font-size: 1.7rem;
}
.announcement-spotlight p {
    margin: 12px 0 0;
    color: #B8AB98;
    line-height: 1.8;
    max-width: 780px;
}
.resident-announcement-action-btn {
    position: relative;
    z-index: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 36px;
    padding: 0 16px;
    border-radius: 999px;
    text-decoration: none;
    background: rgba(255, 255, 255, 0.035);
    border: 1px solid rgba(214, 168, 91, 0.16);
    color: rgba(232, 202, 156, 0.86);
    font-size: 0.74rem;
    font-weight: 650;
    letter-spacing: 0.07em;
    line-height: 1;
    text-transform: uppercase;
    white-space: nowrap;
    transition: background 0.16s ease, border-color 0.16s ease, color 0.16s ease, transform 0.16s ease;
}
.resident-announcement-action-btn:hover {
    background: rgba(214, 168, 91, 0.075);
    border-color: rgba(214, 168, 91, 0.26);
    color: #F0D7A0;
    transform: translateY(-1px);
}
.resident-card {
    width: 100%;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: 16px;
    padding: 16px 18px;
}
.resident-card-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 18px;
}
.resident-card-heading {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 10px;
}
.resident-card-heading h3 {
    margin: 0;
    color: #f0e9df;
    font-size: 1.05rem;
    font-weight: 700;
}
.resident-card-description {
    margin: 12px 0 0;
    color: #B8AB98;
    line-height: 1.7;
    font-size: 0.92rem;
}
.resident-card-single-action {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    flex: 0 0 auto;
}
.resident-card-meta-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
    margin-top: 16px;
}
.resident-meta-box, .resident-note-item {
    padding: 14px 16px;
    border-radius: 14px;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.04);
}
.resident-meta-box span, .resident-note-item strong {
    display: block;
    color: #8A7A66;
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.12em;
}
.resident-meta-box strong {
    display: block;
    margin-top: 8px;
    color: #F0E9DF;
    font-size: 0.92rem;
    font-weight: 600;
}
.resident-note-item span {
    display: block;
    margin-top: 8px;
    color: #B8AB98;
    line-height: 1.7;
    font-size: 0.9rem;
}
.resident-support-panel .resident-note-item {
    flex: 1 1 220px;
    min-width: 0;
    padding: 10px 12px;
}
.resident-support-panel .resident-note-item strong {
    font-size: 0.64rem;
}
.resident-support-panel .resident-note-item span {
    font-size: 0.78rem;
    line-height: 1.5;
}
.resident-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 6px 11px;
    border-radius: 999px;
    font-size: 0.68rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.08em;
}
.resident-badge-priority-normal {
    background: rgba(214, 168, 91, 0.12);
    color: #E9D8BD;
}
.resident-badge-priority-important {
    background: rgba(190, 147, 96, 0.14);
    color: #E4C58E;
}
.resident-badge-priority-urgent {
    background: rgba(224, 112, 96, 0.14);
    color: #F0B3A9;
}
.resident-card-accent-normal {
    border-color: rgba(214, 168, 91, 0.10);
}
.resident-card-accent-important {
    border-color: rgba(190, 147, 96, 0.16);
}
.resident-card-accent-urgent {
    border-color: rgba(224, 112, 96, 0.18);
}
.resident-empty-state {
    padding: 22px 24px 16px;
    border-radius: 18px;
    background: #6B4F3A;
    border: 1px dashed rgba(214, 168, 91, 0.18);
    text-align: center;
}
.resident-empty-state h3 {
    margin: 0;
    color: #F0E9DF;
    font-size: 1.1rem;
}
.resident-empty-state p {
    margin: 10px 0 0;
    color: #B8AB98;
    line-height: 1.7;
}
@media (max-width:900px) {
    .resident-announcement-support-grid, .resident-card-meta-grid {
        grid-template-columns: 1fr;
    }
    .announcement-spotlight, .resident-card-top {
        flex-direction: column;
        align-items: flex-start;
    }
}
@media (max-width:768px) {
    .resident-page {
        padding: 18px 0 28px;
    }
    .resident-page-hero, .resident-page-panel {
        padding: 22px;
    }
    .resident-page-hero {
        flex-direction: column;
        align-items: flex-start;
    }
}
@media (max-width:560px) {
    .resident-page-title {
        font-size: 2.15rem;
    }
    .resident-page-subtitle {
        font-size: 0.95rem;
    }
    .announcement-spotlight {
        padding: 18px;
    }
    .announcement-spotlight h3 {
        font-size: 1.35rem;
    }
    .resident-announcement-action-btn {
        width: 100%;
    }
    .resident-card, .resident-page-panel, .resident-page-hero {
        border-radius: 22px;
    }
    .resident-card-links {
        width: 100%;
    }
}
</style>
</x-app-layout>
