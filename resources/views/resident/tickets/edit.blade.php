<x-app-layout>
    <div class="resident-ticket-edit-page">
        <section class="resident-ticket-edit-hero">
            <div class="resident-ticket-edit-copy">
                <p class="resident-ticket-edit-kicker">Maintenance Ticket Workspace</p>
                <h1 class="resident-ticket-edit-title">Edit Ticket</h1>
                <p class="resident-ticket-edit-subtitle">
                    Refine your description, adjust the urgency, and update attachments with a cleaner, more professional request form.
                </p>

                <div class="resident-ticket-edit-stats">
                    <div class="resident-ticket-edit-stat">
                        <span>Ticket ID</span>
                        <strong>{{ $ticket->ticket_id }}</strong>
                    </div>
                    <div class="resident-ticket-edit-stat">
                        <span>Status</span>
                        <strong>{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</strong>
                    </div>
                    <div class="resident-ticket-edit-stat">
                        <span>Priority</span>
                        <strong>{{ old('priority', $ticket->priority_label) === 'low' ? 'Low Priority' : (old('priority') === 'critical' ? 'Critical' : $ticket->priority_label) }}</strong>
                    </div>
                </div>
            </div>

            <div class="resident-ticket-edit-actions">
                <a href="{{ route('tickets.show', $ticket) }}" class="resident-ticket-edit-btn resident-ticket-edit-btn-secondary">Back to Ticket</a>
            </div>
        </section>

        @if ($errors->any())
            <div class="resident-ticket-edit-error">
                <div class="resident-ticket-edit-error-title">Please fix the following:</div>
                <ul class="resident-ticket-edit-error-list">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="resident-ticket-edit-grid">
            <section class="resident-ticket-edit-panel resident-ticket-edit-main-panel">
                <div class="resident-ticket-edit-head">
                    <div>
                        <h2>Update Request</h2>
                        <p>Improve your maintenance details while keeping the original concern clear for staff review.</p>
                    </div>

                    <span class="resident-ticket-edit-eyebrow">Resident Update Form</span>
                </div>

                <div class="resident-ticket-edit-divider"></div>

                <form method="POST" action="{{ route('tickets.update', $ticket) }}" enctype="multipart/form-data" id="ticketForm" class="resident-ticket-edit-form">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="resident-ticket-edit-label">Title</label>
                        <input
                            type="text"
                            value="{{ $ticket->title }}"
                            class="resident-ticket-edit-input resident-ticket-edit-input-readonly"
                            disabled
                        >
                        <p class="resident-ticket-edit-help">Title stays locked after submission so your request keeps one consistent record.</p>
                    </div>

                    <div>
                        <label class="resident-ticket-edit-label">Description</label>
                        <textarea
                            name="description"
                            rows="7"
                            class="resident-ticket-edit-input resident-ticket-edit-textarea"
                            placeholder="Please provide detailed information about the issue...">{{ old('description', $ticket->description) }}</textarea>
                    </div>

                    <div>
                        <label class="resident-ticket-edit-label">Priority</label>
                        <div class="resident-ticket-priority-grid">
                            <label class="resident-ticket-priority-card" data-priority="low">
                                <input type="radio" name="priority" value="low" {{ old('priority', $ticket->priority) === 'low' ? 'checked' : '' }}>
                                <span class="resident-ticket-priority-name">Low Priority</span>
                                <span class="resident-ticket-priority-copy">For minor concerns that do not disrupt your stay.</span>
                            </label>

                            <label class="resident-ticket-priority-card" data-priority="medium">
                                <input type="radio" name="priority" value="medium" {{ old('priority', $ticket->priority) === 'medium' ? 'checked' : '' }}>
                                <span class="resident-ticket-priority-name">Medium</span>
                                <span class="resident-ticket-priority-copy">For concerns that should be addressed soon.</span>
                            </label>

                            <label class="resident-ticket-priority-card" data-priority="critical">
                                <input type="radio" name="priority" value="critical" {{ old('priority', $ticket->normalized_priority) === 'critical' ? 'checked' : '' }}>
                                <span class="resident-ticket-priority-name">Critical</span>
                                <span class="resident-ticket-priority-copy">Use only for issues that need immediate action.</span>
                            </label>
                        </div>
                    </div>

                    @if($ticket->image_path || $ticket->video_path)
                        <div class="resident-ticket-edit-subpanel">
                            <div class="resident-ticket-edit-subhead">
                                <h3>Current Attachments</h3>
                                <p>Review your uploaded files and remove any that are no longer needed.</p>
                            </div>

                            <div class="resident-ticket-edit-attachment-stack">
                                @if($ticket->image_path)
                                    <div class="resident-ticket-edit-attachment-card">
                                        <img src="{{ asset('storage/' . $ticket->image_path) }}" alt="Current ticket image" class="resident-ticket-edit-attachment-thumb">
                                        <div class="resident-ticket-edit-attachment-copy">
                                            <strong>Current image attached</strong>
                                            <label class="resident-ticket-edit-check">
                                                <input type="checkbox" name="remove_image" value="1">
                                                <span>Remove this image</span>
                                            </label>
                                        </div>
                                    </div>
                                @endif

                                @if($ticket->video_path)
                                    <div class="resident-ticket-edit-attachment-card">
                                        <video class="resident-ticket-edit-attachment-thumb" muted>
                                            <source src="{{ asset('storage/' . $ticket->video_path) }}">
                                        </video>
                                        <div class="resident-ticket-edit-attachment-copy">
                                            <strong>Current video attached</strong>
                                            <label class="resident-ticket-edit-check">
                                                <input type="checkbox" name="remove_video" value="1">
                                                <span>Remove this video</span>
                                            </label>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="resident-ticket-edit-subpanel">
                        <div class="resident-ticket-edit-subhead">
                            <h3>Replace Attachments</h3>
                            <p>Add a new image or video if you need clearer proof of the concern.</p>
                        </div>

                        <div class="resident-ticket-upload-row">
                            <label class="resident-ticket-upload-trigger">
                                <span>Choose Photo or Video</span>
                                <input type="file" name="attachment" id="attachment" accept="image/*,video/*">
                            </label>

                            <div id="selectedFile" class="resident-ticket-selected-file">
                                <span id="fileName"></span>
                                <button type="button" onclick="clearAttachment()">Remove</button>
                            </div>

                            <input type="file" name="image" id="imageInput" hidden accept="image/*">
                            <input type="file" name="video" id="videoInput" hidden accept="video/*">
                        </div>

                        <p class="resident-ticket-edit-help">Images up to 2MB and videos up to 10MB are supported.</p>

                        <div id="previewArea" class="resident-ticket-preview-area">
                            <div id="imagePreview" class="resident-ticket-preview-block">
                                <img id="previewImage" alt="New image preview">
                            </div>
                            <div id="videoPreview" class="resident-ticket-preview-block">
                                <video id="previewVideo" controls></video>
                            </div>
                        </div>
                    </div>

                    <div class="resident-ticket-edit-form-actions">
                        <button type="submit" id="submitBtn" class="resident-ticket-edit-btn resident-ticket-edit-btn-primary">
                            Save Ticket Changes
                        </button>
                        <a href="{{ route('tickets.show', $ticket) }}" class="resident-ticket-edit-btn resident-ticket-edit-btn-secondary">
                            Cancel
                        </a>
                    </div>

                    <div class="resident-ticket-edit-inline-meta">
                        <div class="resident-ticket-edit-meta-item">
                            <span>Status</span>
                            <strong>{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</strong>
                        </div>
                        <div class="resident-ticket-edit-meta-item">
                            <span>Original Priority</span>
                            <strong>{{ $ticket->priority_label }}</strong>
                        </div>
                        <div class="resident-ticket-edit-meta-item">
                            <span>Last Updated</span>
                            <strong>{{ $ticket->updated_at->format('M d, Y h:i A') }}</strong>
                        </div>
                    </div>
                </form>
            </section>
        </div>
    </div>
    <script>
        function updatePriorityStyles() {
            document.querySelectorAll('.resident-ticket-priority-card').forEach((card) => {
                const radio = card.querySelector('input[type="radio"]');
                card.classList.toggle('is-active', !!radio.checked);
            });
        }

        document.querySelectorAll('.resident-ticket-priority-card').forEach((card) => {
            card.addEventListener('click', function () {
                const radio = this.querySelector('input[type="radio"]');
                if (radio) {
                    radio.checked = true;
                    updatePriorityStyles();
                }
            });
        });

        updatePriorityStyles();

        const attachmentInput = document.getElementById('attachment');
        const imageInput = document.getElementById('imageInput');
        const videoInput = document.getElementById('videoInput');
        const selectedFileDiv = document.getElementById('selectedFile');
        const fileNameSpan = document.getElementById('fileName');
        const previewArea = document.getElementById('previewArea');
        const imagePreview = document.getElementById('imagePreview');
        const videoPreview = document.getElementById('videoPreview');
        const previewImage = document.getElementById('previewImage');
        const previewVideo = document.getElementById('previewVideo');

        attachmentInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (!file) return;

            if (file.type.startsWith('image/') && file.size > 2 * 1024 * 1024) {
                alert('Image must be less than 2MB');
                attachmentInput.value = '';
                return;
            }

            if (file.type.startsWith('video/') && file.size > 10 * 1024 * 1024) {
                alert('Video must be less than 10MB');
                attachmentInput.value = '';
                return;
            }

            imageInput.value = '';
            videoInput.value = '';
            imagePreview.style.display = 'none';
            videoPreview.style.display = 'none';

            fileNameSpan.textContent = file.name;
            selectedFileDiv.style.display = 'inline-flex';
            previewArea.style.display = 'grid';

            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function (event) {
                    previewImage.src = event.target.result;
                    imagePreview.style.display = 'block';
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    imageInput.files = dataTransfer.files;
                };
                reader.readAsDataURL(file);
            } else if (file.type.startsWith('video/')) {
                const url = URL.createObjectURL(file);
                previewVideo.src = url;
                videoPreview.style.display = 'block';
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                videoInput.files = dataTransfer.files;
            }
        });

        function clearAttachment() {
            attachmentInput.value = '';
            imageInput.value = '';
            videoInput.value = '';
            selectedFileDiv.style.display = 'none';
            previewArea.style.display = 'none';
            imagePreview.style.display = 'none';
            videoPreview.style.display = 'none';
            previewImage.src = '';
            previewVideo.src = '';
        }

        document.getElementById('ticketForm').addEventListener('submit', function (e) {
            const description = document.querySelector('textarea[name="description"]').value.trim();
            const submitBtn = document.getElementById('submitBtn');

            if (!description) {
                e.preventDefault();
                alert('Please describe the issue.');
                return;
            }

            submitBtn.textContent = 'Saving Changes...';
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.7';
        });
    </script>

    <style>
        .resident-ticket-edit-page {
            max-width: 1600px;
            margin: 0 auto;
            padding: 24px 16px 32px;
            display: flex;
            flex-direction: column;
            gap: 22px;
        }

        .resident-ticket-edit-hero,
        .resident-ticket-edit-panel,
        .resident-ticket-edit-error {
            border: 1px solid rgba(214,168,91,0.14);
            box-shadow: 0 12px 24px rgba(0,0,0,0.14);
        }

        .resident-ticket-edit-hero {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 20px;
            padding: 28px 30px;
            border-radius: 36px;
            background: linear-gradient(115deg, #1F2023 0%, #24262B 38%, #2C2C2F 62%, #3B3023 100%);
            box-shadow: 0 18px 50px rgba(0, 0, 0, 0.18);
        }

        .resident-ticket-edit-copy {
            max-width: 860px;
        }

        .resident-ticket-edit-kicker {
            margin: 0 0 10px;
            color: #D2A04C;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.30em;
        }

        .resident-ticket-edit-title {
            margin: 0;
            color: #F8F3EA;
            font-family: 'Playfair Display', serif;
            font-size: clamp(2.4rem, 4.6vw, 3.8rem);
            line-height: 1.05;
        }

        .resident-ticket-edit-subtitle {
            margin: 12px 0 0;
            color: rgba(255,255,255,0.82);
            font-size: 1.02rem;
            line-height: 1.7;
            max-width: 760px;
        }

        .resident-ticket-edit-stats {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            margin-top: 22px;
        }

        .resident-ticket-edit-stat {
            min-width: 130px;
            padding: 12px 14px;
            border-radius: 16px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.07);
        }

        .resident-ticket-edit-stat span {
            display: block;
            color: #A89376;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.14em;
            font-weight: 700;
        }

        .resident-ticket-edit-stat strong {
            display: block;
            margin-top: 6px;
            color: #F0E9DF;
            font-size: 1rem;
            font-weight: 700;
        }

        .resident-ticket-edit-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .resident-ticket-edit-error {
            background: linear-gradient(180deg, rgba(53, 38, 35, 0.92) 0%, rgba(42, 31, 29, 0.92) 100%);
            border-radius: 20px;
            padding: 18px 22px;
            color: #F0B3A9;
            border-color: rgba(224,112,96,0.22);
        }

        .resident-ticket-edit-error-title {
            font-weight: 700;
            margin-bottom: 8px;
            color: #FFB2A7;
        }

        .resident-ticket-edit-error-list {
            margin: 0;
            padding-left: 18px;
            color: #E7C3BD;
            line-height: 1.7;
        }

        .resident-ticket-edit-grid {
            display: block;
        }

        .resident-ticket-edit-panel {
            padding: 26px 28px;
            border-radius: 20px;
            background: rgba(42,44,48,0.78);
            backdrop-filter: blur(10px);
        }

        .resident-ticket-edit-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }

        .resident-ticket-edit-head h2,
        .resident-ticket-edit-subhead h3 {
            margin: 0;
            color: #F0E9DF;
            font-size: 1.5rem;
            font-family: 'Playfair Display', serif;
        }

        .resident-ticket-edit-subhead h3 {
            font-size: 1.1rem;
        }

        .resident-ticket-edit-head p,
        .resident-ticket-edit-subhead p {
            margin: 4px 0 0;
            color: #8A7A66;
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .resident-ticket-edit-eyebrow {
            color: #D6A85B;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.16em;
        }

        .resident-ticket-edit-divider {
            height: 1px;
            background: linear-gradient(to right, rgba(214,168,91,0.3), rgba(214,168,91,0.05), transparent);
            margin-bottom: 20px;
        }

        .resident-ticket-edit-form {
            display: flex;
            flex-direction: column;
            gap: 22px;
        }

        .resident-ticket-edit-label {
            display: block;
            font-weight: 700;
            margin-bottom: 10px;
            color: #D0C8B8;
            font-size: 14px;
            letter-spacing: 0.02em;
        }

        .resident-ticket-edit-input {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid rgba(214,168,91,0.14);
            border-radius: 16px;
            font-size: 15px;
            color: #F8F3EA;
            background: rgba(37,39,42,0.90);
            outline: none;
            transition: all 0.2s ease;
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.02);
            font-family: inherit;
        }

        .resident-ticket-edit-input:focus {
            border-color: rgba(214,168,91,0.38);
            box-shadow: 0 0 0 4px rgba(214,168,91,0.08);
        }

        .resident-ticket-edit-input-readonly {
            color: #B8AB98;
            background: rgba(255,255,255,0.03);
        }

        .resident-ticket-edit-textarea {
            resize: vertical;
            min-height: 180px;
            line-height: 1.75;
        }

        .resident-ticket-edit-help {
            margin: 8px 0 0;
            color: #8A7A66;
            font-size: 0.8rem;
            line-height: 1.7;
        }

        .resident-ticket-priority-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .resident-ticket-priority-card {
            position: relative;
            display: flex;
            flex-direction: column;
            gap: 8px;
            padding: 16px 18px;
            border-radius: 16px;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.06);
            cursor: pointer;
            transition: transform 0.18s ease, border-color 0.18s ease, background 0.18s ease;
        }

        .resident-ticket-priority-card:hover {
            transform: translateY(-1px);
            border-color: rgba(214,168,91,0.22);
        }

        .resident-ticket-priority-card input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .resident-ticket-priority-card.is-active[data-priority="low"] {
            background: rgba(120, 170, 120, 0.10);
            border-color: rgba(120, 170, 120, 0.32);
        }

        .resident-ticket-priority-card.is-active[data-priority="medium"] {
            background: rgba(199, 151, 69, 0.10);
            border-color: rgba(214,168,91,0.32);
        }

        .resident-ticket-priority-card.is-active[data-priority="critical"] {
            background: rgba(185, 106, 93, 0.10);
            border-color: rgba(185, 106, 93, 0.32);
        }

        .resident-ticket-priority-name {
            color: #F0E9DF;
            font-size: 0.96rem;
            font-weight: 700;
        }

        .resident-ticket-priority-copy {
            color: #B8AB98;
            font-size: 0.85rem;
            line-height: 1.65;
        }

        .resident-ticket-edit-subpanel,
        .resident-ticket-edit-meta-item,
        .resident-ticket-edit-attachment-card {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 16px;
        }

        .resident-ticket-edit-subpanel {
            padding: 18px;
        }

        .resident-ticket-edit-attachment-stack,
        .resident-ticket-edit-inline-meta {
            display: grid;
            gap: 12px;
        }

        .resident-ticket-edit-inline-meta {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .resident-ticket-edit-attachment-card {
            display: flex;
            gap: 14px;
            align-items: center;
            padding: 14px;
        }

        .resident-ticket-edit-attachment-thumb {
            width: 84px;
            height: 84px;
            object-fit: cover;
            border-radius: 12px;
            background: rgba(23,18,13,0.45);
            flex-shrink: 0;
        }

        .resident-ticket-edit-attachment-copy strong,
        .resident-ticket-edit-meta-item strong {
            display: block;
            color: #F0E9DF;
            font-size: 0.92rem;
            line-height: 1.6;
            font-weight: 600;
        }

        .resident-ticket-edit-check {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 8px;
            color: #F0B3A9;
            font-size: 0.84rem;
            cursor: pointer;
        }

        .resident-ticket-upload-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 12px;
        }

        .resident-ticket-upload-trigger {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 18px;
            border-radius: 999px;
            background: rgba(214,168,91,0.10);
            border: 1px solid rgba(214,168,91,0.20);
            color: #D6A85B;
            font-size: 0.88rem;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .resident-ticket-upload-trigger:hover {
            transform: translateY(-1px);
        }

        .resident-ticket-upload-trigger input {
            display: none;
        }

        .resident-ticket-selected-file {
            display: none;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            border-radius: 999px;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.06);
            color: #F0E9DF;
            font-size: 0.84rem;
        }

        .resident-ticket-selected-file button {
            background: none;
            border: none;
            color: #F0B3A9;
            font-weight: 700;
            cursor: pointer;
        }

        .resident-ticket-preview-area {
            display: none;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin-top: 16px;
        }

        .resident-ticket-preview-block {
            display: none;
            padding: 10px;
            border-radius: 14px;
            background: rgba(255,255,255,0.02);
            border: 1px solid rgba(255,255,255,0.05);
        }

        .resident-ticket-preview-block img,
        .resident-ticket-preview-block video {
            width: 100%;
            max-height: 220px;
            object-fit: cover;
            border-radius: 12px;
        }

        .resident-ticket-edit-form-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            padding-top: 8px;
            border-top: 1px solid rgba(214,168,91,0.10);
        }

        .resident-ticket-edit-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 13px 22px;
            border-radius: 999px;
            text-decoration: none;
            font-size: 0.92rem;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .resident-ticket-edit-btn:hover {
            transform: translateY(-1px);
        }

        .resident-ticket-edit-btn-primary {
            background: linear-gradient(95deg, #b8842f, #d6a85b);
            color: #17120d;
            box-shadow: 0 10px 24px rgba(199, 150, 69, 0.28);
        }

        .resident-ticket-edit-btn-secondary {
            background: rgba(255,255,255,0.04);
            color: #D0C8B8;
            border: 1px solid rgba(214,168,91,0.14);
        }

        .resident-ticket-edit-meta-item {
            padding: 14px 16px;
        }

        .resident-ticket-edit-meta-item span {
            display: block;
            color: #8A7A66;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            margin-bottom: 8px;
        }

        @media (max-width: 768px) {
            .resident-ticket-edit-page {
                padding: 18px 0 28px;
            }

            .resident-ticket-edit-hero,
            .resident-ticket-edit-panel {
                padding: 22px;
            }

            .resident-ticket-edit-hero {
                flex-direction: column;
                align-items: flex-start;
            }

            .resident-ticket-priority-grid,
            .resident-ticket-preview-area,
            .resident-ticket-edit-inline-meta {
                grid-template-columns: 1fr;
            }

            .resident-ticket-edit-attachment-card {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        @media (max-width: 560px) {
            .resident-ticket-edit-btn {
                width: 100%;
            }

            .resident-ticket-edit-form-actions {
                flex-direction: column;
            }
        }
    </style>
</x-app-layout>
