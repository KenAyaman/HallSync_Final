<x-app-layout>
<div class="handyman-ticket-show-shell">
    <section class="handyman-ticket-hero">
        <div class="handyman-ticket-glow handyman-ticket-glow-primary"></div>
        <div class="handyman-ticket-glow handyman-ticket-glow-soft"></div>

        <div class="handyman-ticket-hero-inner">
            <div class="handyman-ticket-hero-copy">
                <span class="handyman-ticket-kicker">Staff Operations Desk</span>
                <h1 class="handyman-ticket-title">Ticket <span>#{{ $ticket->ticket_id ?? $ticket->id }}</span></h1>
                <p class="handyman-ticket-subtitle">
                    Review the request details, track priority, and move assigned work through a clearer staff workflow.
                </p>
            </div>

            <div class="handyman-ticket-hero-actions">
                <a href="{{ route('staff.queue') }}" class="handyman-ticket-btn handyman-ticket-btn-secondary">Back to Work Queue</a>
                @if($ticket->status === 'assigned')
                    <form method="POST"
                          action="{{ route('tickets.update-status', $ticket) }}"
                          data-prevent-double-submit
                          data-submitting-text="Starting Work...">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="in_progress">
                        <button type="submit" class="handyman-ticket-btn handyman-ticket-btn-primary">Start Work</button>
                    </form>
                @elseif($ticket->status === 'in_progress')
                    <form method="POST"
                          action="{{ route('tickets.update-status', $ticket) }}"
                          data-prevent-double-submit
                          data-submitting-text="Marking Resolved...">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="resolved">
                        <div class="handyman-completion-note-wrap">
                            <label for="completion_note">
                                <span>Completion Note</span>
                                <small>Optional - describe what was done.</small>
                            </label>
                            <textarea
                                id="completion_note"
                                name="completion_note"
                                rows="3"
                                maxlength="600"
                                placeholder="e.g. Replaced faucet washer. Tested for leaks. Area cleaned.">{{ old('completion_note') }}</textarea>
                        </div>
                        <button type="submit" class="handyman-ticket-btn handyman-ticket-btn-primary">Mark Resolved</button>
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
                    <span class="handyman-priority-badge priority-{{ $ticket->normalized_priority }}">{{ $ticket->priority_label }}</span>
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
                            <a href="{{ $ticket->image_url }}" target="_blank" class="handyman-ticket-media-card">
                                <img src="{{ $ticket->image_url }}" alt="Ticket attachment">
                                <span>Open image</span>
                            </a>
                        @endif

                        @if($ticket->video_path)
                            <a href="{{ $ticket->video_url }}" target="_blank" class="handyman-ticket-media-card handyman-ticket-media-card-video">
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
                    <div class="handyman-ticket-meta-item">
                        <span class="handyman-ticket-meta-label">Task Started</span>
                        <span class="handyman-ticket-meta-value">{{ $ticket->task_started_at?->format('M d, Y h:i A') ?? 'Not started yet' }}</span>
                    </div>
                    <div class="handyman-ticket-meta-item">
                        <span class="handyman-ticket-meta-label">Task Completed</span>
                        <span class="handyman-ticket-meta-value">{{ $ticket->task_completed_at?->format('M d, Y h:i A') ?? 'Not completed yet' }}</span>
                    </div>
                    <div class="handyman-ticket-meta-item">
                        <span class="handyman-ticket-meta-label">Task Duration</span>
                        <span class="handyman-ticket-meta-value">{{ $ticket->task_duration_label }}</span>
                    </div>
                </div>

                @if($ticket->work_started_at || $ticket->task_completed_at)
                <div class="handyman-time-tracking">
                    <h4 class="handyman-time-heading">Time Tracking</h4>
                    <div class="handyman-time-grid">

                        @if($ticket->work_started_at)
                        <div class="handyman-time-item">
                            <span class="handyman-time-label">Work Started</span>
                            <strong class="handyman-time-value">
                                {{ $ticket->work_started_at->format('M d, Y') }}
                            </strong>
                            <span class="handyman-time-sub">
                                {{ $ticket->work_started_at->format('h:i A') }}
                            </span>
                        </div>
                        @endif

                        @if($ticket->task_completed_at)
                        <div class="handyman-time-item">
                            <span class="handyman-time-label">Completed</span>
                            <strong class="handyman-time-value">
                                {{ $ticket->task_completed_at->format('M d, Y') }}
                            </strong>
                            <span class="handyman-time-sub">
                                {{ $ticket->task_completed_at->format('h:i A') }}
                            </span>
                        </div>
                        @endif

                        @if($ticket->work_started_at && $ticket->task_completed_at)
                        <div class="handyman-time-item handyman-time-duration">
                            <span class="handyman-time-label">Time Taken</span>
                            @php
                                $diff = $ticket->work_started_at
                                               ->diff($ticket->task_completed_at);
                                $hours   = ($diff->days * 24) + $diff->h;
                                $minutes = $diff->i;
                            @endphp
                            <strong class="handyman-time-value">
                                @if($hours > 0)
                                    {{ $hours }}h {{ $minutes }}m
                                @else
                                    {{ $minutes }}m
                                @endif
                            </strong>
                            <span class="handyman-time-sub">
                                from start to completion
                            </span>
                        </div>
                        @endif

                    </div>
                </div>
                @endif
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
.handyman-completion-note-wrap {
    display: grid;
    gap: 7px;
    min-width: min(360px, 80vw)
}
.handyman-completion-note-wrap label span {
    display: block;
    color: #6f5a43;
    font-size: .75rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .08em
}
.handyman-completion-note-wrap label small {
    display: block;
    margin-top: 3px;
    color: #8a7a66
}
.handyman-completion-note-wrap textarea {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid rgba(180, 119, 33, .24);
    border-radius: 10px;
    background: #fffaf2;
    color: #342a23;
    font: inherit;
    resize: vertical
}
.handyman-ticket-show-shell {
    display: flex;
    flex-direction: column;
    gap: 28px;
}
.handyman-ticket-hero {
    position: relative;
    overflow: hidden;
    border-radius: 36px;
    border: 1px solid rgba(180, 119, 33, 0.20);
    background: radial-gradient(circle at top right, rgba(214, 168, 91, 0.18), transparent 30%), linear-gradient(135deg, #fffaf2 0%, #f4ebdf 46%, #e9dccb 100%);
    box-shadow: 0 18px 42px rgba(87, 65, 38, 0.12);
}
.handyman-ticket-glow {
    position: absolute;
    border-radius: 999px;
    filter: blur(55px);
    pointer-events: none;
}
.handyman-ticket-glow-primary {
    top: -90px;
    right: 8%;
    width: 300px;
    height: 300px;
    background: rgba(214, 168, 91, 0.20);
}
.handyman-ticket-glow-soft {
    bottom: -110px;
    left: 12%;
    width: 230px;
    height: 230px;
    background: rgba(88, 135, 165, 0.16);
}
.handyman-ticket-hero-inner {
    position: relative;
    z-index: 1;
    display: flex;
    justify-content: space-between;
    gap: 24px;
    align-items: center;
    padding: 34px 36px;
}
.handyman-ticket-kicker {
    display: inline-block;
    margin-bottom: 12px;
    color: #d6a85b;
    font-size: 0.76rem;
    font-weight: 700;
    letter-spacing: 0.28em;
    text-transform: uppercase;
}
.handyman-ticket-title {
    margin: 0;
    color: #342a23;
    font-size: clamp(2.2rem, 4vw, 3.6rem);
    line-height: 1.04;
    font-family: 'Playfair Display', serif;
}
.handyman-ticket-title span {
    color: #b47721;
}
.handyman-ticket-subtitle {
    max-width: 760px;
    margin: 14px 0 0;
    color: #63574e;
    line-height: 1.7;
}
.handyman-ticket-hero-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    align-self: center;
    gap: 12px;
    flex-wrap: wrap;
    margin-left: auto;
}
.handyman-ticket-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 48px;
    padding: 0 22px;
    border-radius: 999px;
    border: 1px solid transparent;
    text-decoration: none;
    font-weight: 700;
    font-size: 0.9rem;
    transition: 0.2s ease;
    cursor: pointer;
}
.handyman-ticket-btn-primary {
    background: linear-gradient(90deg, #b8842f 0%, #d6a85b 100%);
    color: #fff;
    box-shadow: 0 12px 28px rgba(199, 150, 69, 0.28);
}
.handyman-ticket-btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 16px 32px rgba(199, 150, 69, 0.32);
}
.handyman-ticket-btn-secondary {
    background: rgba(255, 255, 255, 0.62);
    color: #8a4f0e;
    border-color: rgba(180, 119, 33, 0.18);
}
.handyman-ticket-btn-secondary:hover {
    background: #fffdf9;
    color: #6f3f0b;
}
.handyman-ticket-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 24px;
    align-items: start;
}
.handyman-ticket-panel {
    border-radius: 24px;
    padding: 26px;
    background: linear-gradient(180deg, #fffdf9 0%, #f7f0e6 100%);
    border: 1px solid rgba(180, 119, 33, 0.18);
    backdrop-filter: blur(10px);
    box-shadow: 0 14px 30px rgba(87, 65, 38, 0.10);
}
.handyman-ticket-panel-head {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 16px;
    flex-wrap: wrap;
}
.handyman-ticket-panel-title {
    margin: 0;
    color: #342a23;
    font-size: 1.55rem;
    font-family: 'Playfair Display', serif;
}
.handyman-ticket-panel-title-side {
    font-size: 1.35rem;
}
.handyman-ticket-panel-sub {
    margin: 6px 0 0;
    color: #8a7a66;
    font-size: 0.95rem;
}
.handyman-ticket-badge-row {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}
.handyman-status-badge, .handyman-priority-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 8px 12px;
    border-radius: 999px;
    font-size: 0.7rem;
    font-weight: 800;
    letter-spacing: 0.12em;
    text-transform: uppercase;
}
.status-assigned {
    background: rgba(214, 168, 91, 0.12);
    border: 1px solid rgba(214, 168, 91, 0.18);
    color: #d6a85b;
}
.status-in_progress {
    background: rgba(90, 138, 90, 0.12);
    border: 1px solid rgba(90, 138, 90, 0.2);
    color: #78b17f;
}
.status-completed, .status-resolved {
    background: rgba(103, 176, 216, 0.12);
    border: 1px solid rgba(103, 176, 216, 0.18);
    color: #87c9ef;
}
.priority-critical {
    background: rgba(224, 112, 96, 0.14);
    border: 1px solid rgba(224, 112, 96, 0.2);
    color: #f0a195;
}
.priority-medium, .priority-low {
    background: rgba(190, 147, 96, 0.14);
    border: 1px solid rgba(190, 147, 96, 0.2);
    color: #d3ac78;
}
.handyman-ticket-divider {
    height: 1px;
    margin: 22px 0;
    background: linear-gradient(to right, rgba(214, 168, 91, 0.28), rgba(214, 168, 91, 0.05), transparent);
}
.handyman-ticket-section + .handyman-ticket-section {
    margin-top: 24px;
}
.handyman-ticket-section-label {
    display: block;
    margin-bottom: 12px;
    color: #8a4f0e;
    font-size: 0.8rem;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
}
.handyman-ticket-copy {
    color: #4b4038;
    line-height: 1.85;
    font-size: 0.97rem;
    white-space: pre-wrap;
}
.handyman-ticket-attachments {
    display: grid;
    gap: 14px;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
}
.handyman-ticket-media-card {
    overflow: hidden;
    display: flex;
    flex-direction: column;
    text-decoration: none;
    border-radius: 18px;
    border: 1px solid rgba(180, 119, 33, 0.16);
    background: rgba(255, 255, 255, 0.68);
}
.handyman-ticket-media-card img {
    width: 100%;
    height: 180px;
    object-fit: cover;
}
.handyman-ticket-media-card span {
    padding: 14px 16px;
    color: #8a4f0e;
    font-weight: 600;
}
.handyman-ticket-media-card-video {
    min-height: 220px;
    align-items: center;
    justify-content: center;
}
.handyman-ticket-video-icon {
    width: 64px;
    height: 64px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 18px;
    background: rgba(214, 168, 91, 0.12);
    color: #d6a85b;
    margin-top: 28px;
}
.handyman-ticket-video-icon svg {
    width: 28px;
    height: 28px;
}
.handyman-ticket-side {
    display: grid;
    gap: 24px;
}
.handyman-ticket-side-head {
    display: flex;
    align-items: center;
    gap: 12px;
}
.handyman-ticket-side-icon {
    width: 42px;
    height: 42px;
    border-radius: 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: rgba(214, 168, 91, 0.12);
    color: #d6a85b;
    flex-shrink: 0;
}
.handyman-ticket-side-icon-soft {
    background: rgba(190, 147, 96, 0.12);
    color: #be9360;
}
.handyman-ticket-side-icon svg {
    width: 20px;
    height: 20px;
}
.handyman-ticket-meta-list, .handyman-ticket-note-list {
    display: grid;
    gap: 12px;
}
.handyman-ticket-meta-item, .handyman-ticket-note-item {
    padding: 14px 16px;
    border-radius: 16px;
    background: rgba(255, 255, 255, 0.64);
    border: 1px solid rgba(180, 119, 33, 0.14);
}
.handyman-ticket-meta-label {
    display: block;
    margin-bottom: 6px;
    color: #8a7a66;
    font-size: 0.74rem;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
}
.handyman-ticket-meta-value {
    color: #342a23;
    line-height: 1.6;
}
.handyman-ticket-note-item {
    color: #5f5146;
    line-height: 1.75;
}
.handyman-time-tracking {
    margin-top: 24px;
    padding: 18px;
    border: 1px solid rgba(214, 168, 91, 0.18);
    border-radius: 18px;
    background: rgba(214, 168, 91, 0.08);
    box-shadow: 0 14px 30px rgba(87, 65, 38, 0.08);
}
.handyman-time-heading {
    font-size: .78rem;
    font-weight: 800;
    letter-spacing: .09em;
    text-transform: uppercase;
    color: #8a4f0e;
    margin-bottom: 14px;
}
.handyman-time-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 12px;
}
.handyman-time-item {
    display: flex;
    flex-direction: column;
    gap: 5px;
    min-height: 106px;
    padding: 14px;
    border: 1px solid rgba(180, 119, 33, 0.14);
    border-radius: 14px;
    background: rgba(255, 255, 255, 0.62);
}
.handyman-time-label {
    font-size: .70rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .07em;
    color: #8b7d70;
}
.handyman-time-value {
    font-size: 1.08rem;
    font-weight: 800;
    color: #342a23;
    line-height: 1.35;
}
.handyman-time-sub {
    font-size: .8rem;
    color: #786b60;
}
.handyman-time-duration {
    border-color: rgba(79, 128, 92, 0.22);
    background: rgba(79, 128, 92, 0.08);
}
.handyman-time-duration .handyman-time-value {
    color: #356140;
    font-size: 1.28rem;
}
@media (max-width:768px) {
    .handyman-ticket-hero-inner {
        flex-direction: column;
        align-items: flex-start;
        padding: 24px;
    }
    .handyman-ticket-hero-actions {
        width: 100%;
        justify-content: flex-start;
        margin-left: 0;
    }
    .handyman-ticket-panel {
        padding: 22px;
    }
}
@media (max-width:480px) {
    .handyman-time-grid {
        grid-template-columns: 1fr;
    }
}
@media (max-width:560px) {
    .handyman-ticket-hero-actions, .handyman-ticket-hero-actions form, .handyman-ticket-btn {
        width: 100%;
    }
    .handyman-ticket-btn {
        justify-content: center;
    }
}
</style>
</x-app-layout>
