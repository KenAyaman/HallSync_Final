<x-app-layout>
<div class="hs-ticket-page">
<div class="hs-ticket-topbar">
            <a href="{{ route('tickets.index') }}" class="resident-back-link resident-create-back">← Back</a>
        </div>

<section class="hs-ticket-hero">
            <div class="resident-ticket-hero-copy">
                <p class="resident-ticket-kicker">Maintenance Request Record</p>
                <h1 class="resident-ticket-title">Ticket Details</h1>
                <p class="resident-ticket-subtitle">Review the request information, attached files, and available record actions in one place.</p>

                <div class="resident-ticket-hero-stats">
                    <div class="resident-ticket-hero-stat"><span>Status</span><strong>{{ $ticket->status_label }}</strong></div>
                    <div class="resident-ticket-hero-stat"><span>Priority</span><strong>{{ $ticket->priority_label }}</strong></div>
                    <div class="resident-ticket-hero-stat"><span>Ticket ID</span><strong>{{ $ticket->ticket_id }}</strong></div>
                </div>
            </div>

            <div class="resident-ticket-hero-actions">
                <a href="{{ route('tickets.track', $ticket) }}" class="resident-ticket-btn resident-ticket-btn-primary">Track Progress</a>

                @if(in_array($ticket->status, ['approved', 'assigned', 'in_progress'], true) && ! $ticket->cancellation_requested_at)
                    <button type="button"
                            class="resident-ticket-btn resident-ticket-btn-danger"
                            data-cancellation-toggle
                            aria-expanded="false"
                            aria-controls="cancellation-form-panel">
                        Request Cancellation
                    </button>
                @endif
            </div>
        </section>

<div class="hs-ticket-grid">
<section class="hs-ticket-panel">
                <div class="resident-ticket-panel-head">
                    <div>
                        <h2>Ticket Information</h2>
                        <p>The full details of your maintenance request and attached files.</p>
                    </div>

                    <div class="resident-ticket-chip-row">
                        <span class="resident-ticket-badge resident-ticket-badge-status-{{ $ticket->status }}">{{ $ticket->status_label }}</span>
                        <span class="resident-ticket-badge resident-ticket-badge-priority-{{ $ticket->normalized_priority }}">{{ $ticket->priority_label }}</span>
                    </div>
                </div>

                <div class="resident-ticket-divider"></div>

                <div class="resident-ticket-detail-list">
                    <div class="resident-ticket-detail-box"><span>Ticket ID</span><strong>{{ $ticket->ticket_id }}</strong></div>
                    <div class="resident-ticket-detail-box"><span>Title</span><strong>{{ $ticket->title }}</strong></div>
                    <div class="resident-ticket-detail-box"><span>Description</span><p>{{ $ticket->description }}</p></div>

                    @if($ticket->status === 'rejected' && $ticket->rejection_reason)
                        <div class="resident-ticket-detail-box resident-ticket-rejection-box">
                            <span>Rejection Reason</span>
                            <p>{{ $ticket->rejection_reason }}</p>
                        </div>
                    @endif

                    @if($ticket->cancellation_requested_at && $ticket->status !== 'cancelled')
                        <div class="resident-ticket-detail-box resident-ticket-cancellation-note">
                            <span>Cancellation Requested</span>
                            <p>{{ $ticket->cancellation_reason }}</p>
                            <small>Submitted {{ $ticket->cancellation_requested_at->format('M d, Y h:i A') }}</small>
                        </div>
                    @endif

                    <div class="resident-ticket-detail-box"><span>Location</span><strong>{{ $ticket->location ?: 'Not specified' }}</strong></div>
                    <div class="resident-ticket-detail-box"><span>Submitted</span><strong>{{ $ticket->created_at->format('M d, Y h:i A') }}</strong></div>
                    @if($ticket->assignedTo && in_array($ticket->status, ['assigned', 'in_progress', 'resolved', 'closed']))
                        <div class="resident-ticket-detail-box">
                            <span>Assigned Staff</span>
                            <strong>{{ $ticket->assignedTo->name }}</strong>
                        </div>
                    @endif
                </div>

                @if(in_array($ticket->status, ['in_progress', 'completed', 'resolved'], true))
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
                @endif

                @if($ticket->completion_note && in_array($ticket->status, ['resolved', 'closed']))
                    <div class="resident-ticket-completion-note">
                        <span>What was done</span>
                        <p>{{ $ticket->completion_note }}</p>
                    </div>
                @endif

                @if(in_array($ticket->status, ['resolved', 'closed']))
                    @if($ticket->satisfaction_rated_at)
                        <div class="resident-ticket-rated">
                            <span>Your Rating</span>
                            <div class="resident-ticket-stars">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="{{ $i <= $ticket->satisfaction_rating ? 'star-filled' : 'star-empty' }}">&#9733;</span>
                                @endfor
                            </div>
                            @if($ticket->satisfaction_note)
                                <p>{{ $ticket->satisfaction_note }}</p>
                            @endif
                        </div>
                    @else
                        <form method="POST"
                              action="{{ route('tickets.rate', $ticket) }}"
                              class="resident-ticket-rate-form"
                              x-data="{ rating: {{ (int) old('satisfaction_rating', 0) }} }">
                            @csrf
                            <span>How satisfied are you with the repair?</span>
                            <div class="resident-ticket-star-picker">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button"
                                            @click="rating = {{ $i }}"
                                            :class="{ 'is-active': rating >= {{ $i }} }"
                                            aria-label="{{ $i }} star">&#9733;</button>
                                @endfor
                                <input type="hidden" name="satisfaction_rating" :value="rating">
                            </div>
                            <textarea name="satisfaction_note"
                                      maxlength="280"
                                      placeholder="Optional comment about the repair...">{{ old('satisfaction_note') }}</textarea>
                            <button type="submit"
                                    x-show="rating > 0"
                                    class="resident-page-btn-primary">
                                Submit Rating
                            </button>
                        </form>
                    @endif
                @endif
            </section>

<aside class="hs-ticket-panel">
                <div class="resident-ticket-panel-head">
                    <div>
                        <h2>Progress Snapshot</h2>
                        <p>Current staff and timing context.</p>
                    </div>
                </div>

                <div class="resident-ticket-divider"></div>

                <div class="resident-ticket-side-list">
                    <div class="resident-ticket-detail-box"><span>Last Updated</span><strong>{{ $ticket->updated_at->diffForHumans() }}</strong></div>
                    <div class="resident-ticket-detail-box"><span>Work Duration</span><strong>{{ $ticket->task_duration_label }}</strong></div>
                </div>
            </aside>
        </div>

        @if($ticket->image_path || $ticket->video_path)
            <section class="resident-ticket-panel">
                <div class="resident-ticket-panel-head">
                    <div>
                        <h2>Attachments</h2>
                        <p>Resident-uploaded reference media for this request.</p>
                    </div>
                </div>

                <div class="resident-ticket-divider"></div>

                <div class="resident-ticket-attachment-grid">
                    @if($ticket->image_path)
                        <a href="{{ $ticket->image_url }}" target="_blank" class="resident-ticket-attachment">
                            <img src="{{ $ticket->image_url }}" alt="Ticket attachment">
                            <span>Open Image</span>
                        </a>
                    @endif

                    @if($ticket->video_path)
                        <a href="{{ $ticket->video_url }}" target="_blank" class="resident-ticket-attachment resident-ticket-video-attachment">
                            <span>Video Attachment</span>
                            <strong>Open Video</strong>
                        </a>
                    @endif
                </div>
            </section>
        @endif

        @if(in_array($ticket->status, ['approved', 'assigned', 'in_progress'], true) && ! $ticket->cancellation_requested_at)
            <section class="resident-ticket-panel resident-ticket-cancellation-panel" id="cancellation-form-panel" hidden>
                <div class="resident-ticket-panel-head">
                    <div>
                        <h2>Request Cancellation</h2>
                        <p>Send a cancellation request to administration for review.</p>
                    </div>
                </div>

                <div class="resident-ticket-divider"></div>

                <form method="POST" action="{{ route('tickets.request-cancellation', $ticket) }}" class="resident-ticket-cancellation-form" data-prevent-double-submit data-submitting-text="Submitting...">
                    @csrf
                    @method('PATCH')
                    <label for="cancellation_reason">Cancellation Reason</label>
                    <textarea id="cancellation_reason" name="cancellation_reason" rows="4" required maxlength="500" placeholder="Describe why you need to cancel this request.">{{ old('cancellation_reason') }}</textarea>
                    @error('cancellation_reason')
                        <p class="app-field-error">{{ $message }}</p>
                    @enderror
                    <div class="resident-ticket-cancellation-actions">
                        <button type="submit" class="resident-ticket-btn resident-ticket-btn-danger">Submit Request</button>
                        <button type="button" class="resident-ticket-btn resident-ticket-btn-ghost" data-cancellation-cancel>Never mind</button>
                    </div>
                </form>
            </section>
        @endif
    </div>

    <script>
        const cancellationToggle = document.querySelector('[data-cancellation-toggle]');
        const cancellationPanel = document.getElementById('cancellation-form-panel');
        const cancellationCancel = document.querySelector('[data-cancellation-cancel]');

        if (cancellationToggle && cancellationPanel) {
            cancellationToggle.addEventListener('click', () => {
                const isOpen = !cancellationPanel.hidden;
                cancellationPanel.hidden = isOpen;
                cancellationToggle.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
                if (!isOpen) {
                    cancellationPanel.querySelector('textarea')?.focus();
                }
            });
        }

        if (cancellationCancel && cancellationPanel) {
            cancellationCancel.addEventListener('click', () => {
                cancellationPanel.hidden = true;
                cancellationToggle?.setAttribute('aria-expanded', 'false');
                cancellationToggle?.focus();
            });
        }
    </script>

</x-app-layout>

