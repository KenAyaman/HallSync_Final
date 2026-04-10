<x-app-layout>
    <div class="resident-ticket-create-page">
        <section class="resident-ticket-create-hero">
            <div class="resident-ticket-create-copy">
                <p class="resident-ticket-create-kicker">Resident Maintenance Hub</p>
                <h1 class="resident-ticket-create-title">Create Maintenance Ticket</h1>
                <p class="resident-ticket-create-subtitle">
                    Report a dorm issue clearly, attach helpful media if needed, and send it into the HallSync review queue with complete details.
                </p>
            </div>

            <div class="resident-ticket-create-hero-actions">
                <a href="{{ route('tickets.index') }}" class="resident-ticket-create-btn resident-ticket-create-btn-secondary">Back to My Tickets</a>
            </div>

            <div class="resident-ticket-create-pills">
                <span>Category-based routing</span>
                <span>Photo or video supported</span>
                <span>Reviewed before dispatch</span>
            </div>
        </section>

        @if ($errors->any())
            <div class="resident-ticket-create-error">
                <div class="resident-ticket-create-error-title">Please fix the following:</div>
                <ul class="resident-ticket-create-error-list">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="resident-ticket-create-grid">
            <section class="resident-ticket-create-panel">
                <div class="resident-ticket-create-panel-head">
                    <div>
                        <h2>Ticket Details</h2>
                        <p>Give the maintenance team the context they need to help quickly.</p>
                    </div>
                    <span class="resident-ticket-create-chip">New Request</span>
                </div>

                <div class="resident-ticket-create-divider"></div>

                <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data" id="ticketForm" class="resident-ticket-create-form">
                    @csrf

                    <div>
                        <label for="title" class="resident-ticket-create-label">Title</label>
                        <input id="title" name="title" type="text" class="resident-ticket-create-input" value="{{ old('title') }}" placeholder="Example: Sink leak in Room 204" required>
                    </div>

                    <div>
                        <label for="category" class="resident-ticket-create-label">Category</label>
                        <select id="category" name="category" class="resident-ticket-create-input resident-ticket-create-select" required>
                            <option value="">Select a category</option>
                            <option value="plumbing" @selected(old('category') === 'plumbing')>Plumbing</option>
                            <option value="electrical" @selected(old('category') === 'electrical')>Electrical</option>
                            <option value="furniture" @selected(old('category') === 'furniture')>Furniture</option>
                            <option value="hvac" @selected(old('category') === 'hvac')>HVAC</option>
                            <option value="other" @selected(old('category') === 'other')>Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="resident-ticket-create-label">Priority</label>
                        <div class="resident-ticket-priority-grid">
                            @foreach ([
                                'low' => 'Routine issue',
                                'medium' => 'Needs attention soon',
                                'critical' => 'Needs immediate action',
                            ] as $priority => $copy)
                                <label class="resident-ticket-priority-card {{ old('priority', 'medium') === $priority ? 'is-active' : '' }}">
                                    <input type="radio" name="priority" value="{{ $priority }}" @checked(old('priority', 'medium') === $priority)>
                                    <span class="resident-ticket-priority-name">{{ $priority === 'low' ? 'Low Priority' : ucfirst($priority) }}</span>
                                    <span class="resident-ticket-priority-copy">{{ $copy }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label for="description" class="resident-ticket-create-label">Description</label>
                        <textarea id="description" name="description" rows="7" class="resident-ticket-create-input resident-ticket-create-textarea" placeholder="Explain what is happening, when it started, and anything the staff should know." required>{{ old('description') }}</textarea>
                    </div>

                    <div class="resident-ticket-upload-panel">
                        <div class="resident-ticket-upload-head">
                            <div>
                                <h3>Attachments</h3>
                                <p>Upload a photo or a short video if it helps explain the issue.</p>
                            </div>
                        </div>

                        <div class="resident-ticket-upload-grid">
                            <div>
                                <label for="image" class="resident-ticket-create-label">Photo</label>
                                <input id="image" name="image" type="file" accept="image/*" class="resident-ticket-create-input-file">
                                <p class="resident-ticket-upload-note">Up to 2MB</p>
                            </div>

                            <div>
                                <label for="video" class="resident-ticket-create-label">Video</label>
                                <input id="video" name="video" type="file" accept="video/*" class="resident-ticket-create-input-file">
                                <p class="resident-ticket-upload-note">Up to 10MB</p>
                            </div>
                        </div>

                        <div class="resident-ticket-preview-grid">
                            <img id="imagePreview" class="resident-ticket-preview-media" style="display:none;" alt="Image preview">
                            <video id="videoPreview" class="resident-ticket-preview-media" style="display:none;" controls></video>
                        </div>
                    </div>

                    <div class="resident-ticket-create-actions">
                        <button type="submit" class="resident-ticket-create-btn resident-ticket-create-btn-primary">Submit Request</button>
                        <a href="{{ route('tickets.index') }}" class="resident-ticket-create-btn resident-ticket-create-btn-secondary">Cancel</a>
                    </div>
                </form>
            </section>
        </div>
    </div>

    <script>
        document.querySelectorAll('.resident-ticket-priority-card').forEach((card) => {
            card.addEventListener('click', () => {
                document.querySelectorAll('.resident-ticket-priority-card').forEach((item) => item.classList.remove('is-active'));
                card.classList.add('is-active');
                const radio = card.querySelector('input[type="radio"]');
                if (radio) {
                    radio.checked = true;
                }
            });
        });

        const imageInput = document.getElementById('image');
        const videoInput = document.getElementById('video');
        const imagePreview = document.getElementById('imagePreview');
        const videoPreview = document.getElementById('videoPreview');

        imageInput.addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (!file) {
                imagePreview.style.display = 'none';
                imagePreview.src = '';
                return;
            }

            imagePreview.src = URL.createObjectURL(file);
            imagePreview.style.display = 'block';
        });

        videoInput.addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (!file) {
                videoPreview.style.display = 'none';
                videoPreview.src = '';
                return;
            }

            videoPreview.src = URL.createObjectURL(file);
            videoPreview.style.display = 'block';
        });
    </script>

    <style>
        .resident-ticket-create-page {
            max-width: 1580px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .resident-ticket-create-hero,
        .resident-ticket-create-panel,
        .resident-ticket-create-error {
            border: 1px solid rgba(214,168,91,0.14);
            box-shadow: 0 12px 24px rgba(0,0,0,0.14);
        }

        .resident-ticket-create-hero {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 20px;
            padding: 28px 30px;
            border-radius: 36px;
            background: linear-gradient(115deg, #1F2023 0%, #24262B 38%, #2C2C2F 62%, #3B3023 100%);
            box-shadow: 0 18px 50px rgba(0, 0, 0, 0.18);
        }

        .resident-ticket-create-copy {
            max-width: 860px;
        }

        .resident-ticket-create-kicker {
            margin: 0 0 10px;
            color: #D2A04C;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.30em;
        }

        .resident-ticket-create-title {
            margin: 0;
            color: #F8F3EA;
            font-family: 'Playfair Display', serif;
            font-size: clamp(2.4rem, 4.6vw, 3.8rem);
            line-height: 1.05;
        }

        .resident-ticket-create-subtitle {
            margin: 12px 0 0;
            color: rgba(255,255,255,0.82);
            font-size: 1.02rem;
            line-height: 1.7;
            max-width: 760px;
        }

        .resident-ticket-create-hero-actions {
            display: flex;
            justify-content: flex-end;
            width: 100%;
        }

        .resident-ticket-create-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .resident-ticket-create-pills span {
            padding: 10px 14px;
            border-radius: 999px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            color: #E9D8BD;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .resident-ticket-create-error {
            background: linear-gradient(180deg, rgba(53, 38, 35, 0.92) 0%, rgba(42, 31, 29, 0.92) 100%);
            border-radius: 20px;
            padding: 18px 22px;
            color: #F0B3A9;
        }

        .resident-ticket-create-error-title {
            font-weight: 700;
            margin-bottom: 8px;
        }

        .resident-ticket-create-error-list {
            margin: 0;
            padding-left: 18px;
            line-height: 1.7;
        }

        .resident-ticket-create-grid {
            display: block;
        }

        .resident-ticket-create-panel {
            padding: 26px 28px;
            border-radius: 20px;
            background: rgba(42,44,48,0.78);
            backdrop-filter: blur(10px);
        }

        .resident-ticket-create-panel-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 16px;
        }

        .resident-ticket-create-panel-head h2 {
            margin: 0;
            color: #F0E9DF;
            font-size: 1.5rem;
            font-family: 'Playfair Display', serif;
        }

        .resident-ticket-create-panel-head p {
            margin: 4px 0 0;
            color: #8A7A66;
            font-size: 0.95rem;
        }

        .resident-ticket-create-chip {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: #D6A85B;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(214,168,91,0.10);
            border: 1px solid rgba(214,168,91,0.16);
        }

        .resident-ticket-create-divider {
            height: 1px;
            background: linear-gradient(to right, rgba(214,168,91,0.3), rgba(214,168,91,0.05), transparent);
            margin-bottom: 24px;
        }

        .resident-ticket-create-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .resident-ticket-create-label {
            display: block;
            font-weight: 700;
            margin-bottom: 10px;
            color: #D0C8B8;
            font-size: 14px;
        }

        .resident-ticket-create-input,
        .resident-ticket-create-input-file {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid rgba(214,168,91,0.14);
            border-radius: 16px;
            font-size: 15px;
            color: #F8F3EA;
            background: rgba(37,39,42,0.90);
            outline: none;
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.02);
        }

        .resident-ticket-create-textarea {
            resize: vertical;
            min-height: 180px;
            line-height: 1.7;
        }

        .resident-ticket-create-select {
            appearance: none;
        }

        .resident-ticket-priority-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .resident-ticket-priority-card {
            position: relative;
            display: flex;
            flex-direction: column;
            gap: 6px;
            padding: 16px;
            border-radius: 18px;
            border: 1px solid rgba(214,168,91,0.12);
            background: rgba(255,255,255,0.03);
            cursor: pointer;
            transition: 0.2s ease;
        }

        .resident-ticket-priority-card input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .resident-ticket-priority-card.is-active {
            border-color: rgba(214,168,91,0.34);
            background: rgba(214,168,91,0.08);
            transform: translateY(-1px);
        }

        .resident-ticket-priority-name {
            color: #F0E9DF;
            font-weight: 700;
        }

        .resident-ticket-priority-copy,
        .resident-ticket-upload-note {
            color: #B8AB98;
            font-size: 0.9rem;
            line-height: 1.7;
        }

        .resident-ticket-upload-panel {
            border-radius: 18px;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.05);
            padding: 18px;
        }

        .resident-ticket-upload-head h3 {
            margin: 0;
            color: #F0E9DF;
            font-size: 1.05rem;
            font-weight: 700;
        }

        .resident-ticket-upload-head p {
            margin: 6px 0 0;
            color: #8A7A66;
            font-size: 0.92rem;
        }

        .resident-ticket-upload-grid,
        .resident-ticket-preview-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
            margin-top: 16px;
        }

        .resident-ticket-preview-media {
            width: 100%;
            max-height: 220px;
            object-fit: cover;
            border-radius: 16px;
            border: 1px solid rgba(214,168,91,0.12);
        }

        .resident-ticket-create-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            padding-top: 8px;
            border-top: 1px solid rgba(214,168,91,0.10);
        }

        .resident-ticket-create-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 14px 24px;
            border-radius: 999px;
            font-weight: 700;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }

        .resident-ticket-create-btn-primary {
            background: linear-gradient(95deg, #b8842f, #d6a85b);
            color: #17120d;
        }

        .resident-ticket-create-btn-secondary {
            background: rgba(255,255,255,0.04);
            color: #D0C8B8;
            border: 1px solid rgba(214,168,91,0.14);
        }

        @media (max-width: 768px) {
            .resident-ticket-create-hero {
                flex-direction: column;
                align-items: flex-start;
            }

            .resident-ticket-create-hero-actions {
                justify-content: flex-start;
            }

            .resident-ticket-priority-grid,
            .resident-ticket-upload-grid,
            .resident-ticket-preview-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</x-app-layout>
