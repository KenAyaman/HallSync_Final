<x-app-layout>
    <x-admin-breadcrumb :crumbs="[
        ['label' => 'Announcements', 'route' => 'announcements.index'],
        ['label' => Str::limit($announcement->title, 48)],
    ]" />
    
    <div class="admin-ticket-show admin-detail-page">
        <section class="admin-ticket-show-hero admin-detail-hero">
            <div>
                <p class="admin-ticket-show-kicker">Announcement Center</p>
                <h1 class="admin-ticket-show-title">Announcement Details</h1>
                <p class="admin-ticket-show-subtitle">
                    Review the published content, priority level, and visibility status before making further changes.
                </p>
            </div>
            <a href="{{ route('announcements.index') }}" class="admin-ticket-show-back">Back to Announcements</a>
        </section>

        <div class="admin-ticket-show-grid admin-ticket-show-grid-single">
            <section class="admin-ticket-show-panel admin-detail-panel">
                <div class="admin-ticket-show-panel-head">
                    <div>
                        <h2>{{ $announcement->title }}</h2>
                        <p>Resident-facing announcement content and publishing summary.</p>
                    </div>
                    <div class="admin-ticket-show-badges">
                        <span class="admin-ticket-badge admin-ticket-badge-priority-{{ $announcement->priority }}">
                            {{ ucfirst($announcement->priority) }}
                        </span>
                        <span class="admin-ticket-badge admin-ticket-badge-status-{{ $announcement->is_active ? 'approved' : 'pending_approval' }}">
                            {{ $announcement->is_active ? 'Visible' : 'Hidden' }}
                        </span>
                    </div>
                </div>

                <div class="admin-ticket-info-grid">
                    <article class="admin-ticket-info-card">
                        <span>Announcement ID</span>
                        <strong>#{{ $announcement->id }}</strong>
                    </article>

                    <article class="admin-ticket-info-card">
                        <span>Priority</span>
                        <strong>{{ ucfirst($announcement->priority) }}</strong>
                    </article>

                    <article class="admin-ticket-info-card admin-ticket-info-card-wide">
                        <span>Created</span>
                        <strong>{{ $announcement->created_at->format('F d, Y h:i A') }}</strong>
                    </article>

                    <article class="admin-ticket-info-card admin-ticket-info-card-wide">
                        <span>Last Updated</span>
                        <strong>{{ $announcement->updated_at->format('F d, Y h:i A') }}</strong>
                    </article>

                    <article class="admin-ticket-info-card admin-ticket-info-card-wide">
                        <span>Visibility</span>
                        <strong>{{ $announcement->is_active ? 'Visible to residents' : 'Hidden from residents' }}</strong>
                    </article>

                    <article class="admin-ticket-info-card admin-ticket-info-card-wide">
                        <span>Content</span>
                        <p>{{ $announcement->content }}</p>
                    </article>
                </div>

                <div class="admin-ticket-panel-divider"></div>
            </section>
        </div>
    </div>

    <style>
.admin-ticket-show {
    max-width: 1580px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 24px;
}
.admin-ticket-show-hero, .admin-ticket-show-panel {
    border: 1px solid rgba(214, 168, 91, 0.14);
    box-shadow: 0 14px 28px rgba(0, 0, 0, 0.14);
}
.admin-ticket-show-hero {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    gap: 24px;
    padding: 32px 36px;
    border-radius: 20px;
    background: linear-gradient(120deg, #111009 0%, #1c1a12 50%, #201e14 100%);
    position: relative;
    overflow: hidden;
}
.admin-ticket-show-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image: linear-gradient(rgba(214, 168, 91, 0.04) 1px, transparent 1px), linear-gradient(90deg, rgba(214, 168, 91, 0.04) 1px, transparent 1px);
    background-size: 48px 48px;
    pointer-events: none;
}
.admin-ticket-show-kicker {
    margin: 0 0 10px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #d6a85b;
    font-size: 0.875rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.18em;
    position: relative;
    z-index: 1;
}
.admin-ticket-show-kicker::before {
    content: '';
    width: 6px;
    height: 6px;
    border-radius: 999px;
    background: #d6a85b;
}
.admin-ticket-show-title {
    margin: 0;
    color: #f0e9df;
    font-family: 'Playfair Display', serif;
    font-size: clamp(2.3rem, 4vw, 3.3rem);
    line-height: 1.12;
    position: relative;
    z-index: 1;
}
.admin-ticket-show-subtitle {
    margin: 12px 0 0;
    color: rgba(255, 255, 255, 0.62);
    font-size: 1.05rem;
    max-width: 760px;
    position: relative;
    z-index: 1;
}
.admin-ticket-show-back {
    position: relative;
    z-index: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 12px 18px;
    border-radius: 999px;
    border: 1px solid rgba(214, 168, 91, 0.18);
    background: rgba(255, 255, 255, 0.04);
    color: #f0e9df;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 700;
}
.admin-ticket-show-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.15fr) minmax(300px, 0.85fr);
    gap: 24px;
}
.admin-ticket-show-grid-single {
    grid-template-columns: 1fr;
}
.admin-ticket-show-panel {
    border-radius: 20px;
    padding: 24px;
    background: rgba(42, 44, 48, 0.78);
    backdrop-filter: blur(10px);
}
.admin-ticket-show-panel-head {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 16px;
    margin-bottom: 18px;
}
.admin-ticket-show-panel-head h2 {
    margin: 0;
    color: #f0e9df;
    font-family: 'Playfair Display', serif;
    font-size: 1.45rem;
}
.admin-ticket-show-panel-head p {
    margin: 6px 0 0;
    color: #8a7a66;
    font-size: 0.95rem;
}
.admin-ticket-show-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}
.admin-ticket-badge {
    padding: 6px 10px;
    border-radius: 999px;
    font-size: 0.72rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.08em;
}
.admin-ticket-badge-priority-normal {
    background: rgba(168, 159, 145, 0.16);
    color: #a89f91;
}
.admin-ticket-badge-priority-important {
    background: rgba(214, 168, 91, 0.16);
    color: #d6a85b;
}
.admin-ticket-badge-priority-urgent {
    background: rgba(224, 112, 96, 0.16);
    color: #e07060;
}
.admin-ticket-badge-status-approved, .admin-ticket-badge-status-visible {
    background: rgba(90, 138, 90, 0.16);
    color: #5a8a5a;
}
.admin-ticket-badge-status-pending_approval, .admin-ticket-badge-status-hidden {
    background: rgba(214, 168, 91, 0.16);
    color: #d6a85b;
}
.admin-ticket-info-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
}
.admin-ticket-info-card {
    padding: 18px;
    border-radius: 18px;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.05);
}
.admin-ticket-info-card-wide {
    grid-column: 1 / -1;
}
.admin-ticket-info-card span {
    display: block;
    margin-bottom: 8px;
    color: #8a7a66;
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.12em;
}
.admin-ticket-info-card strong {
    color: #f0e9df;
    font-size: 1rem;
    line-height: 1.6;
}
.admin-ticket-info-card p {
    margin: 0;
    color: #c4b8a8;
    line-height: 1.7;
    font-size: 0.95rem;
}
.admin-ticket-panel-divider {
    height: 1px;
    background: linear-gradient(to right, rgba(214, 168, 91, 0.3), rgba(214, 168, 91, 0.05), transparent);
    margin: 20px 0;
}
.admin-ticket-form-actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    padding-top: 16px;
}
.admin-ticket-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 12px 16px;
    border-radius: 14px;
    text-decoration: none;
    border: 1px solid rgba(214, 168, 91, 0.14);
    font-size: 0.9rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s ease;
}
.admin-ticket-action:hover {
    transform: translateY(-1px);
}
.admin-ticket-action-primary {
    background: linear-gradient(135deg, #c79745, #d6a85b);
    color: #18130e;
    border-color: rgba(199, 151, 69, 0.55);
}
.admin-ticket-action-primary:hover {
    background: linear-gradient(135deg, #b8842f, #c79745);
    border-color: rgba(184, 132, 47, 0.65);
}
.admin-ticket-action-danger {
    background: rgba(224, 112, 96, 0.12);
    border-color: rgba(224, 112, 96, 0.18);
    color: #f0b2a7;
}
.admin-ticket-action-danger:hover {
    background: rgba(224, 112, 96, 0.18);
    border-color: rgba(224, 112, 96, 0.28);
    color: #e07060;
}
.admin-ticket-action form {
    margin: 0;
}
.admin-ticket-action button {
    width: 100%;
    background: transparent;
    border: none;
    font: inherit;
    cursor: inherit;
    padding: 0;
    margin: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
@media (max-width:980px) {
    .admin-ticket-show-grid {
        grid-template-columns: 1fr;
    }
}
@media (max-width:768px) {
    .admin-ticket-show {
        gap: 16px;
    }
    .admin-ticket-show-hero {
        padding: 24px;
        flex-direction: column;
        align-items: flex-start;
    }
    .admin-ticket-info-grid {
        grid-template-columns: 1fr;
    }
    .admin-ticket-form-actions {
        flex-direction: column;
    }
    .admin-ticket-action {
        width: 100%;
    }
}
@media (max-width:560px) {
    .admin-ticket-show-panel-head h2 {
        font-size: 1.25rem;
    }
    .admin-ticket-show-panel-head p,
    .admin-ticket-info-card strong,
    .admin-ticket-info-card p {
        font-size: 0.92rem;
    }
}
    </style>
</x-app-layout>
