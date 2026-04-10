<x-app-layout>
<div class="handyman-ticket-show-shell">
    <section class="handyman-ticket-hero">
        <div class="handyman-ticket-glow handyman-ticket-glow-primary"></div>
        <div class="handyman-ticket-glow handyman-ticket-glow-soft"></div>

        <div class="handyman-ticket-hero-inner">
            <div class="handyman-ticket-hero-copy">
                <span class="handyman-ticket-kicker">Maintenance Operations</span>
                <h1 class="handyman-ticket-title">Ticket <span>#{{ $ticket->ticket_id ?? $ticket->id }}</span></h1>
                <p class="handyman-ticket-subtitle">
                    Review the request details, track priority, and update progress as work moves from assignment to completion.
                </p>
            </div>

            <div class="handyman-ticket-hero-actions">
                <a href="{{ route('dashboard') }}" class="handyman-ticket-btn handyman-ticket-btn-secondary">Back to Work Queue</a>
                @if($ticket->status === 'assigned')
                    <form method="POST" action="{{ route('tickets.update-status', $ticket) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="in_progress">
                        <button type="submit" class="handyman-ticket-btn handyman-ticket-btn-primary">Start Work</button>
                    </form>
                @elseif($ticket->status === 'in_progress')
                    <form method="POST" action="{{ route('tickets.update-status', $ticket) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="completed">
                        <button type="submit" class="handyman-ticket-btn handyman-ticket-btn-primary">Mark Complete</button>
                    </form>
                @endif
            </div>
        </div>
    </section>

    <div class="handyman-ticket-grid">
        <section class="handyman-ticket-panel handyman-ticket-main-panel">
            <div class="handyman-ticket-panel-head">
                <div>
                    <h2 class="handyman-ticket-panel-title">{{ $ticket->title }}</h2>
                    <p class="handyman-ticket-panel-sub">Assigned maintenance request for on-site action.</p>
                </div>

                <div class="handyman-ticket-badge-row">
                    <span class="handyman-status-badge status-{{ $ticket->status }}">{{ str_replace('_', ' ', ucfirst($ticket->status)) }}</span>
                    <span class="handyman-priority-badge priority-{{ $ticket->priority }}">{{ ucfirst($ticket->priority) }} Priority</span>
                </div>
            </div>

            <div class="handyman-ticket-divider"></div>

            <div class="handyman-ticket-section">
                <span class="handyman-ticket-section-label">Resident Issue</span>
                <div class="handyman-ticket-copy">{{ $ticket->description }}</div>
            </div>

            @if($ticket->image_path || $ticket->video_path)
                <div class="handyman-ticket-section">
                    <span class="handyman-ticket-section-label">Attachments</span>
                    <div class="handyman-ticket-attachments">
                        @if($ticket->image_path)
                            <a href="{{ asset('storage/' . $ticket->image_path) }}" target="_blank" class="handyman-ticket-media-card">
                                <img src="{{ asset('storage/' . $ticket->image_path) }}" alt="Ticket attachment">
                                <span>Open image</span>
                            </a>
                        @endif

                        @if($ticket->video_path)
                            <a href="{{ asset('storage/' . $ticket->video_path) }}" target="_blank" class="handyman-ticket-media-card handyman-ticket-media-card-video">
                                <div class="handyman-ticket-video-icon">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m15.75 10.5 4.72-2.36a.75.75 0 0 1 1.08.67v6.38a.75.75 0 0 1-1.08.67l-4.72-2.36m-10.5 3.75h9a2.25 2.25 0 0 0 2.25-2.25v-6a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 3 9v6a2.25 2.25 0 0 0 2.25 2.25Z" />
                                    </svg>
                                </div>
                                <span>Open video</span>
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </section>

        <aside class="handyman-ticket-side">
            <section class="handyman-ticket-panel">
                <div class="handyman-ticket-side-head">
                    <div class="handyman-ticket-side-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3.75 21h16.5M4.5 3h15a.75.75 0 0 1 .75.75v16.5H3.75V3.75A.75.75 0 0 1 4.5 3Zm4.5 4.5h6m-6 4.5h6m-6 4.5h3" />
                        </svg>
                    </div>
                    <h2 class="handyman-ticket-panel-title handyman-ticket-panel-title-side">Ticket Snapshot</h2>
                </div>

                <div class="handyman-ticket-divider"></div>

                <div class="handyman-ticket-meta-list">
                    <div class="handyman-ticket-meta-item">
                        <span class="handyman-ticket-meta-label">Resident</span>
                        <span class="handyman-ticket-meta-value">{{ $ticket->user->name ?? 'Resident' }}</span>
                    </div>
                    <div class="handyman-ticket-meta-item">
                        <span class="handyman-ticket-meta-label">Location</span>
                        <span class="handyman-ticket-meta-value">{{ $ticket->location ?? 'Location pending' }}</span>
                    </div>
                    <div class="handyman-ticket-meta-item">
                        <span class="handyman-ticket-meta-label">Category</span>
                        <span class="handyman-ticket-meta-value">{{ $ticket->category ? ucfirst($ticket->category) : 'General maintenance' }}</span>
                    </div>
                    <div class="handyman-ticket-meta-item">
                        <span class="handyman-ticket-meta-label">Created</span>
                        <span class="handyman-ticket-meta-value">{{ $ticket->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                </div>
            </section>

            <section class="handyman-ticket-panel">
                <div class="handyman-ticket-side-head">
                    <div class="handyman-ticket-side-icon handyman-ticket-side-icon-soft">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11.25 9.75h1.5v4.5h-1.5v-4.5Zm0 6h1.5v1.5h-1.5v-1.5ZM12 3.75a8.25 8.25 0 1 0 0 16.5 8.25 8.25 0 0 0 0-16.5Z" />
                        </svg>
                    </div>
                    <h2 class="handyman-ticket-panel-title handyman-ticket-panel-title-side">Work Guidance</h2>
                </div>

                <div class="handyman-ticket-divider"></div>

                <div class="handyman-ticket-note-list">
                    <div class="handyman-ticket-note-item">Start work only when you are ready to actively handle the request so status updates stay trustworthy.</div>
                    <div class="handyman-ticket-note-item">If the location or issue details are incomplete, coordinate with management before marking progress.</div>
                    <div class="handyman-ticket-note-item">Complete the ticket only after the issue has been resolved and the task no longer needs follow-up.</div>
                </div>
            </section>
        </aside>
    </div>
</div>

<style>
.handyman-ticket-show-shell { display: flex; flex-direction: column; gap: 28px; }
.handyman-ticket-hero { position: relative; overflow: hidden; border-radius: 36px; border: 1px solid rgba(214, 168, 91, 0.16); background: linear-gradient(115deg, #1f2023 0%, #24262b 38%, #2c2c2f 62%, #3b3023 100%); box-shadow: 0 22px 56px rgba(0, 0, 0, 0.2); }
.handyman-ticket-glow { position: absolute; border-radius: 999px; filter: blur(55px); pointer-events: none; }
.handyman-ticket-glow-primary { top: -90px; right: 8%; width: 300px; height: 300px; background: rgba(199, 151, 69, 0.28); }
.handyman-ticket-glow-soft { bottom: -110px; left: 12%; width: 230px; height: 230px; background: rgba(255, 255, 255, 0.08); }
.handyman-ticket-hero-inner { position: relative; z-index: 1; display: flex; justify-content: space-between; gap: 24px; align-items: flex-start; padding: 34px 36px; }
.handyman-ticket-kicker { display: inline-block; margin-bottom: 12px; color: #d6a85b; font-size: 0.76rem; font-weight: 700; letter-spacing: 0.28em; text-transform: uppercase; }
.handyman-ticket-title { margin: 0; color: #f8f3ea; font-size: clamp(2.2rem, 4vw, 3.6rem); line-height: 1.04; font-family: 'Playfair Display', serif; }
.handyman-ticket-title span { color: #f3e5cf; }
.handyman-ticket-subtitle { max-width: 760px; margin: 14px 0 0; color: rgba(255, 255, 255, 0.8); line-height: 1.7; }
.handyman-ticket-hero-actions { display: flex; gap: 12px; align-items: center; flex-wrap: wrap; }
.handyman-ticket-btn { display: inline-flex; align-items: center; justify-content: center; min-height: 48px; padding: 0 22px; border-radius: 999px; border: 1px solid transparent; text-decoration: none; font-weight: 700; font-size: 0.9rem; transition: 0.2s ease; cursor: pointer; }
.handyman-ticket-btn-primary { background: linear-gradient(90deg, #b8842f 0%, #d6a85b 100%); color: #fff; box-shadow: 0 12px 28px rgba(199, 150, 69, 0.28); }
.handyman-ticket-btn-primary:hover { transform: translateY(-1px); box-shadow: 0 16px 32px rgba(199, 150, 69, 0.32); }
.handyman-ticket-btn-secondary { background: rgba(255, 255, 255, 0.05); color: #e8dfd1; border-color: rgba(214, 168, 91, 0.16); }
.handyman-ticket-btn-secondary:hover { background: rgba(255, 255, 255, 0.08); color: #fff6e7; }
.handyman-ticket-grid { display: grid; grid-template-columns: minmax(0, 1.15fr) minmax(320px, 0.85fr); gap: 28px; align-items: start; }
.handyman-ticket-panel { border-radius: 24px; padding: 26px; background: rgba(42, 44, 48, 0.8); border: 1px solid rgba(214, 168, 91, 0.14); backdrop-filter: blur(10px); box-shadow: 0 14px 30px rgba(0, 0, 0, 0.14); }
.handyman-ticket-panel-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; flex-wrap: wrap; }
.handyman-ticket-panel-title { margin: 0; color: #f0e9df; font-size: 1.55rem; font-family: 'Playfair Display', serif; }
.handyman-ticket-panel-title-side { font-size: 1.35rem; }
.handyman-ticket-panel-sub { margin: 6px 0 0; color: #8a7a66; font-size: 0.95rem; }
.handyman-ticket-badge-row { display: flex; gap: 10px; flex-wrap: wrap; }
.handyman-status-badge, .handyman-priority-badge { display: inline-flex; align-items: center; justify-content: center; padding: 8px 12px; border-radius: 999px; font-size: 0.7rem; font-weight: 800; letter-spacing: 0.12em; text-transform: uppercase; }
.status-assigned { background: rgba(214, 168, 91, 0.12); border: 1px solid rgba(214, 168, 91, 0.18); color: #d6a85b; }
.status-in_progress { background: rgba(90, 138, 90, 0.12); border: 1px solid rgba(90, 138, 90, 0.2); color: #78b17f; }
.status-completed { background: rgba(103, 176, 216, 0.12); border: 1px solid rgba(103, 176, 216, 0.18); color: #87c9ef; }
.priority-urgent { background: rgba(224, 112, 96, 0.14); border: 1px solid rgba(224, 112, 96, 0.2); color: #f0a195; }
.priority-high { background: rgba(240, 165, 80, 0.14); border: 1px solid rgba(240, 165, 80, 0.2); color: #efb066; }
.priority-medium, .priority-low { background: rgba(190, 147, 96, 0.14); border: 1px solid rgba(190, 147, 96, 0.2); color: #d3ac78; }
.handyman-ticket-divider { height: 1px; margin: 22px 0; background: linear-gradient(to right, rgba(214, 168, 91, 0.28), rgba(214, 168, 91, 0.05), transparent); }
.handyman-ticket-section + .handyman-ticket-section { margin-top: 24px; }
.handyman-ticket-section-label { display: block; margin-bottom: 12px; color: #d0c8b8; font-size: 0.8rem; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; }
.handyman-ticket-copy { color: #e5ddd1; line-height: 1.85; font-size: 0.97rem; white-space: pre-wrap; }
.handyman-ticket-attachments { display: grid; gap: 14px; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); }
.handyman-ticket-media-card { overflow: hidden; display: flex; flex-direction: column; text-decoration: none; border-radius: 18px; border: 1px solid rgba(255, 255, 255, 0.06); background: rgba(255, 255, 255, 0.03); }
.handyman-ticket-media-card img { width: 100%; height: 180px; object-fit: cover; }
.handyman-ticket-media-card span { padding: 14px 16px; color: #f0e9df; font-weight: 600; }
.handyman-ticket-media-card-video { min-height: 220px; align-items: center; justify-content: center; }
.handyman-ticket-video-icon { width: 64px; height: 64px; display: inline-flex; align-items: center; justify-content: center; border-radius: 18px; background: rgba(214, 168, 91, 0.12); color: #d6a85b; margin-top: 28px; }
.handyman-ticket-video-icon svg { width: 28px; height: 28px; }
.handyman-ticket-side { display: grid; gap: 24px; }
.handyman-ticket-side-head { display: flex; align-items: center; gap: 12px; }
.handyman-ticket-side-icon { width: 42px; height: 42px; border-radius: 14px; display: inline-flex; align-items: center; justify-content: center; background: rgba(214, 168, 91, 0.12); color: #d6a85b; flex-shrink: 0; }
.handyman-ticket-side-icon-soft { background: rgba(190, 147, 96, 0.12); color: #be9360; }
.handyman-ticket-side-icon svg { width: 20px; height: 20px; }
.handyman-ticket-meta-list, .handyman-ticket-note-list { display: grid; gap: 12px; }
.handyman-ticket-meta-item, .handyman-ticket-note-item { padding: 14px 16px; border-radius: 16px; background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.05); }
.handyman-ticket-meta-label { display: block; margin-bottom: 6px; color: #8a7a66; font-size: 0.74rem; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; }
.handyman-ticket-meta-value { color: #f0e9df; line-height: 1.6; }
.handyman-ticket-note-item { color: #b8ab98; line-height: 1.75; }
@media (max-width: 1100px) { .handyman-ticket-grid { grid-template-columns: 1fr; } }
@media (max-width: 768px) { .handyman-ticket-hero-inner { flex-direction: column; padding: 24px; } .handyman-ticket-panel { padding: 22px; } }
@media (max-width: 560px) { .handyman-ticket-hero-actions, .handyman-ticket-hero-actions form, .handyman-ticket-btn { width: 100%; } .handyman-ticket-btn { justify-content: center; } }
</style>
</x-app-layout>
