<x-app-layout>
    @php
        $oldType = old('type', $communityPost->type);
    @endphp

    <div class="community-edit-page">
        <div class="community-edit-topbar">
            <a href="{{ route('community.show', $communityPost) }}" class="community-edit-back">Back to Post</a>
        </div>

        @if ($errors->any())
            <div class="community-edit-error">
                <div class="community-edit-error-title">Please fix the following:</div>
                <ul class="community-edit-error-list">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="community-edit-panel">
            <div class="community-edit-head">
                <div>
                    <h1>Edit Post</h1>
                    <p>Update your message and save it back to review.</p>
                </div>
                <span class="community-edit-eyebrow">Resident Edit</span>
            </div>

            <div class="community-edit-divider"></div>

            <form method="POST" action="{{ route('community.update', $communityPost) }}" enctype="multipart/form-data" id="postForm" class="community-edit-form">
                @csrf
                @method('PATCH')

                <div class="community-edit-composer">
                    <div class="community-edit-avatar">
                        @if(auth()->user()->profile_photo_url)
                            <img src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}">
                        @else
                            {{ auth()->user()->profile_initials }}
                        @endif
                    </div>
                    <div class="community-edit-composer-copy">
                        <strong>{{ auth()->user()->name }}</strong>
                        <span>Your profile photo stays attached to this post after approval.</span>
                    </div>
                </div>

                <div>
                    <label class="community-edit-label">Category</label>
                    <div class="community-category-grid">
                        @foreach ([
                            'discussion' => ['Discussion', 'Open a general conversation with neighbors.'],
                            'lost_found' => ['Lost & Found', 'Post missing items or things that were recovered.'],
                            'buy_sell' => ['Buy & Sell', 'Share items for sale or things you are looking for.'],
                            'event' => ['Event', 'Invite residents to activities and shared gatherings.'],
                            'other' => ['Other', 'Use your own title for a custom community topic.'],
                        ] as $type => [$label, $copy])
                            <label class="community-category-card" data-type="{{ $type }}">
                                <input type="radio" name="type" value="{{ $type }}" {{ $oldType === $type ? 'checked' : '' }}>
                                <span class="community-category-name">{{ $label }}</span>
                                <span class="community-category-copy">{{ $copy }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="community-edit-label" for="title">Post Title</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $communityPost->title) }}" class="community-edit-input" placeholder="What is this post about?">
                </div>

                <div>
                    <label class="community-edit-label" for="content">What's on your mind?</label>
                    <textarea name="content" id="content" rows="7" class="community-edit-input community-edit-textarea" placeholder="Share your thoughts, updates, or questions with the community...">{{ old('content', $communityPost->content) }}</textarea>
                </div>

                <div class="community-edit-subpanel">
                    <div class="community-edit-subhead">
                        <h2>Attachments</h2>
                        <p>Upload replacement media only if you want to change the current attachment.</p>
                    </div>

                    <div class="community-upload-row">
                        <label class="community-upload-trigger">
                            <span>Choose Replacement Media</span>
                            <input type="file" name="attachment" id="attachment" accept="image/*,video/*">
                        </label>

                        <div id="selectedFile" class="community-selected-file">
                            <span id="fileName"></span>
                            <button type="button" onclick="clearAttachment()">Remove</button>
                        </div>

                        <input type="file" name="image" id="imageInput" hidden accept="image/*">
                        <input type="file" name="video" id="videoInput" hidden accept="video/*">
                    </div>

                    @if($communityPost->image_path || $communityPost->video_path)
                        <div class="community-edit-current-media">
                            <span class="community-edit-current-media-label">Current attachment</span>
                            @if($communityPost->image_path)
                                <img src="{{ Storage::url($communityPost->image_path) }}" alt="{{ $communityPost->title }}">
                            @endif
                            @if($communityPost->video_path)
                                <video controls>
                                    <source src="{{ Storage::url($communityPost->video_path) }}">
                                </video>
                            @endif
                        </div>
                    @endif

                    <div id="previewArea" class="community-preview-area">
                        <div id="imagePreview" class="community-preview-block">
                            <img id="previewImage" alt="Selected image preview">
                        </div>
                        <div id="videoPreview" class="community-preview-block">
                            <video id="previewVideo" controls></video>
                        </div>
                    </div>
                </div>

                <div class="community-edit-form-actions">
                    <button type="submit" id="submitBtn" class="community-edit-btn community-edit-btn-primary">Save Changes</button>
                    <a href="{{ route('community.show', $communityPost) }}" class="community-edit-btn community-edit-btn-secondary">Cancel</a>
                </div>

                <p class="community-edit-footer-note">Edited approved posts go back to admin review before they appear on the resident board again.</p>
            </form>
        </section>
    </div>

    <script>
        function updateCategoryStyles() {
            document.querySelectorAll('.community-category-card').forEach((card) => {
                const radio = card.querySelector('input[type="radio"]');
                card.classList.toggle('is-active', !!radio.checked);
            });
        }

        document.querySelectorAll('.community-category-card').forEach((card) => {
            card.addEventListener('click', function () {
                const radio = this.querySelector('input[type="radio"]');
                if (radio) {
                    radio.checked = true;
                    updateCategoryStyles();
                }
            });
        });

        updateCategoryStyles();

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

        document.getElementById('postForm').addEventListener('submit', function () {
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.textContent = 'Saving Changes...';
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.7';
        });
    </script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap');

        .community-edit-page {
            max-width: 980px;
            margin: 0 auto;
            padding: 24px 16px 32px;
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .community-edit-topbar {
            display: flex;
            justify-content: flex-start;
        }

        .community-edit-back {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            padding: 0 16px;
            border-radius: 999px;
            border: 1px solid rgba(214,168,91,0.18);
            background: rgba(214,168,91,0.10);
            color: #D6A85B;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            box-shadow: 0 10px 24px rgba(0,0,0,0.14);
        }

        .community-edit-panel,
        .community-edit-error {
            border: 1px solid rgba(214,168,91,0.14);
            box-shadow: 0 12px 24px rgba(0,0,0,0.14);
        }

        .community-edit-error {
            background: linear-gradient(180deg, rgba(53, 38, 35, 0.92) 0%, rgba(42, 31, 29, 0.92) 100%);
            border-radius: 20px;
            padding: 18px 22px;
            color: #F0B3A9;
        }

        .community-edit-error-title {
            font-weight: 700;
            margin-bottom: 8px;
            color: #FFB2A7;
        }

        .community-edit-error-list {
            margin: 0;
            padding-left: 18px;
            line-height: 1.7;
        }

        .community-edit-panel {
            padding: 26px 28px;
            border-radius: 20px;
            background: rgba(42,44,48,0.78);
            backdrop-filter: blur(10px);
        }

        .community-edit-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }

        .community-edit-head h1,
        .community-edit-subhead h2 {
            margin: 0;
            color: #F0E9DF;
            font-size: 1.5rem;
            font-family: 'Playfair Display', serif;
        }

        .community-edit-subhead h2 {
            font-size: 1.1rem;
        }

        .community-edit-head p,
        .community-edit-subhead p {
            margin: 4px 0 0;
            color: #8A7A66;
            font-size: 0.95rem;
        }

        .community-edit-eyebrow {
            color: #D6A85B;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
        }

        .community-edit-divider {
            height: 1px;
            background: linear-gradient(to right, rgba(214,168,91,0.3), rgba(214,168,91,0.05), transparent);
            margin-bottom: 20px;
        }

        .community-edit-form {
            display: flex;
            flex-direction: column;
            gap: 22px;
        }

        .community-edit-composer {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px 18px;
            border-radius: 18px;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.05);
        }

        .community-edit-avatar {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: linear-gradient(135deg, rgba(214, 168, 91, 0.26), rgba(190,147,96,0.08));
            color: #F4DEB5;
            font-weight: 700;
            flex-shrink: 0;
        }

        .community-edit-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .community-edit-composer-copy strong {
            display: block;
            color: #F0E9DF;
            font-size: 1rem;
        }

        .community-edit-composer-copy span {
            display: block;
            margin-top: 4px;
            color: #8A7A66;
            font-size: 0.9rem;
            line-height: 1.6;
        }

        .community-edit-label {
            display: block;
            font-weight: 700;
            margin-bottom: 10px;
            color: #D0C8B8;
            font-size: 14px;
            letter-spacing: 0.02em;
        }

        .community-edit-input {
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

        .community-edit-input:focus {
            border-color: rgba(214,168,91,0.38);
            box-shadow: 0 0 0 4px rgba(214,168,91,0.08);
        }

        .community-edit-textarea {
            resize: vertical;
            min-height: 180px;
            line-height: 1.75;
        }

        .community-category-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .community-category-card {
            position: relative;
            display: flex;
            flex-direction: column;
            gap: 8px;
            padding: 16px 18px;
            border-radius: 16px;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.06);
            cursor: pointer;
        }

        .community-category-card input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .community-category-card.is-active {
            background: rgba(214,168,91,0.10);
            border-color: rgba(214,168,91,0.28);
        }

        .community-category-name {
            color: #F0E9DF;
            font-size: 0.96rem;
            font-weight: 700;
        }

        .community-category-copy {
            color: #B8AB98;
            font-size: 0.85rem;
            line-height: 1.65;
        }

        .community-edit-subpanel {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 16px;
            padding: 18px;
        }

        .community-upload-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 12px;
            margin-top: 14px;
        }

        .community-upload-trigger {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 18px;
            border-radius: 999px;
            background: rgba(214,168,91,0.12);
            color: #E9D8BD;
            font-size: 0.85rem;
            font-weight: 700;
            cursor: pointer;
        }

        .community-upload-trigger input {
            display: none;
        }

        .community-selected-file {
            display: none;
            align-items: center;
            gap: 10px;
            color: #D0C8B8;
            font-size: 0.84rem;
        }

        .community-selected-file button {
            border: none;
            background: transparent;
            color: #D6A85B;
            font-size: 0.8rem;
            cursor: pointer;
        }

        .community-edit-current-media {
            display: grid;
            gap: 12px;
            margin-top: 16px;
        }

        .community-edit-current-media-label {
            color: #D6A85B;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .community-edit-current-media img,
        .community-edit-current-media video,
        .community-preview-block img,
        .community-preview-block video {
            width: 100%;
            max-height: 320px;
            object-fit: cover;
            border-radius: 16px;
        }

        .community-preview-area {
            display: none;
            gap: 12px;
            margin-top: 16px;
        }

        .community-preview-block {
            display: none;
        }

        .community-edit-form-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .community-edit-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 22px;
            border-radius: 999px;
            text-decoration: none;
            font-size: 0.92rem;
            font-weight: 700;
            transition: transform 0.2s ease;
        }

        .community-edit-btn-primary {
            border: none;
            cursor: pointer;
            background: linear-gradient(95deg, #b8842f, #d6a85b);
            color: #17120d;
        }

        .community-edit-btn-secondary {
            background: rgba(255,255,255,0.04);
            color: #D0C8B8;
            border: 1px solid rgba(214,168,91,0.14);
        }

        .community-edit-footer-note {
            margin: 8px 0 0;
            color: #8A7A66;
            font-size: 0.8rem;
            line-height: 1.7;
            text-align: center;
        }

        @media (max-width: 768px) {
            .community-edit-page {
                padding: 18px 0 28px;
            }

            .community-edit-panel {
                padding: 22px;
            }

            .community-category-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</x-app-layout>
