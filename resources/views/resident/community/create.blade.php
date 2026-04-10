<x-app-layout>
    <div class="community-create-page">
        <div class="community-create-topbar">
            <a href="{{ route('community.index') }}" class="community-create-back">Back to Community</a>
        </div>

        @if ($errors->any())
            <div class="community-create-error">
                <div class="community-create-error-title">Please fix the following:</div>
                <ul class="community-create-error-list">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="community-create-grid">
            <section class="community-create-panel">
                <div class="community-create-head">
                    <div>
                        <h2>Post Composer</h2>
                        <p>Choose a category, write your message clearly, and attach supporting media if needed.</p>
                    </div>

                    <span class="community-create-eyebrow">Resident Submission</span>
                </div>

                <div class="community-create-divider"></div>

                <form method="POST" action="{{ route('community.store') }}" enctype="multipart/form-data" id="postForm" class="community-create-form">
                    @csrf

                    <div class="community-create-composer">
                        <div class="community-create-avatar">
                            @if(auth()->user()->profile_photo_url)
                                <img src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}">
                            @else
                                {{ auth()->user()->profile_initials }}
                            @endif
                        </div>
                        <div class="community-create-composer-copy">
                            <strong>{{ auth()->user()->name }}</strong>
                            <span>Your current profile photo will appear with this post once it is published.</span>
                        </div>
                    </div>

                    <div>
                        <label class="community-create-label">Category</label>
                        <div class="community-category-grid">
                            <label class="community-category-card" data-type="discussion">
                                <input type="radio" name="type" value="discussion" {{ old('type', 'discussion') === 'discussion' ? 'checked' : '' }}>
                                <span class="community-category-name">Discussion</span>
                                <span class="community-category-copy">Open a general conversation with neighbors.</span>
                            </label>

                            <label class="community-category-card" data-type="lost_found">
                                <input type="radio" name="type" value="lost_found" {{ old('type') === 'lost_found' ? 'checked' : '' }}>
                                <span class="community-category-name">Lost &amp; Found</span>
                                <span class="community-category-copy">Post missing items or things that were recovered.</span>
                            </label>

                            <label class="community-category-card" data-type="buy_sell">
                                <input type="radio" name="type" value="buy_sell" {{ old('type') === 'buy_sell' ? 'checked' : '' }}>
                                <span class="community-category-name">Buy &amp; Sell</span>
                                <span class="community-category-copy">Share items for sale or things you are looking for.</span>
                            </label>

                            <label class="community-category-card" data-type="event">
                                <input type="radio" name="type" value="event" {{ old('type') === 'event' ? 'checked' : '' }}>
                                <span class="community-category-name">Event</span>
                                <span class="community-category-copy">Invite residents to activities and shared gatherings.</span>
                            </label>

                            <label class="community-category-card" data-type="other">
                                <input type="radio" name="type" value="other" {{ old('type') === 'other' ? 'checked' : '' }}>
                                <span class="community-category-name">Other</span>
                                <span class="community-category-copy">Use your own title for a custom community topic.</span>
                            </label>
                        </div>
                    </div>

                    <div id="customTitleContainer" class="community-create-custom-title" style="display: {{ old('type') === 'other' ? 'block' : 'none' }};">
                        <label class="community-create-label" for="customTitle">Post Title</label>
                        <input
                            type="text"
                            name="custom_title"
                            id="customTitle"
                            value="{{ old('custom_title') }}"
                            class="community-create-input"
                            placeholder="What is this post about?"
                        >
                        <p class="community-create-help">Use a clear title so residents can understand the topic right away.</p>
                    </div>

                    <input type="hidden" name="title" id="autoTitle">

                    <div>
                        <label class="community-create-label" for="content">What's on your mind?</label>
                        <textarea
                            name="content"
                            id="content"
                            rows="7"
                            class="community-create-input community-create-textarea"
                            placeholder="Share your thoughts, updates, or questions with the community...">{{ old('content') }}</textarea>
                    </div>

                    <div class="community-create-subpanel">
                        <div class="community-create-subhead">
                            <h3>Attachments</h3>
                            <p>Add a photo or video if it helps explain your post.</p>
                        </div>

                        <div class="community-upload-row">
                            <label class="community-upload-trigger">
                                <span>Choose Photo or Video</span>
                                <input type="file" name="attachment" id="attachment" accept="image/*,video/*">
                            </label>

                            <div id="selectedFile" class="community-selected-file">
                                <span id="fileName"></span>
                                <button type="button" onclick="clearAttachment()">Remove</button>
                            </div>

                            <input type="file" name="image" id="imageInput" hidden accept="image/*">
                            <input type="file" name="video" id="videoInput" hidden accept="video/*">
                        </div>

                        <p class="community-create-help">Images up to 2MB and videos up to 10MB are supported.</p>

                        <div id="previewArea" class="community-preview-area">
                            <div id="imagePreview" class="community-preview-block">
                                <img id="previewImage" alt="Selected image preview">
                            </div>
                            <div id="videoPreview" class="community-preview-block">
                                <video id="previewVideo" controls></video>
                            </div>
                        </div>
                    </div>

                    <div class="community-create-form-actions">
                        <button type="submit" id="submitBtn" class="community-create-btn community-create-btn-primary">Post to Community</button>
                        <a href="{{ route('community.index') }}" class="community-create-btn community-create-btn-secondary">Cancel</a>
                    </div>

                    <p class="community-create-footer-note">Your post will be reviewed by admin before it appears publicly.</p>
                </form>
            </section>
        </div>
    </div>

    <script>
        function updateCategoryStyles() {
            document.querySelectorAll('.community-category-card').forEach((card) => {
                const radio = card.querySelector('input[type="radio"]');
                card.classList.toggle('is-active', !!radio.checked);
            });
        }

        function updateTitle() {
            const selectedType = document.querySelector('input[name="type"]:checked');
            const customTitleContainer = document.getElementById('customTitleContainer');
            const customTitleInput = document.getElementById('customTitle');

            const categoryLabels = {
                discussion: 'Discussion',
                lost_found: 'Lost & Found',
                buy_sell: 'Buy & Sell',
                event: 'Event',
                other: null
            };

            if (selectedType) {
                if (selectedType.value === 'other') {
                    customTitleContainer.style.display = 'block';
                    document.getElementById('autoTitle').value = customTitleInput.value.trim() || '';
                } else {
                    customTitleContainer.style.display = 'none';
                    document.getElementById('autoTitle').value = categoryLabels[selectedType.value];
                }
            }

            updateCategoryStyles();
        }

        document.querySelectorAll('.community-category-card').forEach((card) => {
            card.addEventListener('click', function () {
                const radio = this.querySelector('input[type="radio"]');
                if (radio) {
                    radio.checked = true;
                    updateTitle();
                }
            });
        });

        const customTitleInput = document.getElementById('customTitle');
        if (customTitleInput) {
            customTitleInput.addEventListener('input', function () {
                if (document.querySelector('input[name="type"]:checked')?.value === 'other') {
                    document.getElementById('autoTitle').value = this.value.trim() || '';
                }
            });
        }

        updateTitle();

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

        document.getElementById('postForm').addEventListener('submit', function (e) {
            const selectedType = document.querySelector('input[name="type"]:checked');
            const content = document.getElementById('content').value.trim();
            const submitBtn = document.getElementById('submitBtn');

            if (!selectedType) {
                e.preventDefault();
                alert('Please select a category.');
                return;
            }

            if (selectedType.value === 'other') {
                const customTitle = document.getElementById('customTitle').value.trim();
                if (!customTitle) {
                    e.preventDefault();
                    alert('Please enter a title for your post.');
                    return;
                }
            }

            if (!content) {
                e.preventDefault();
                alert('Please write something before posting.');
                return;
            }

            submitBtn.textContent = 'Submitting Post...';
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.7';
        });
    </script>

    <style>
        .community-create-page {
            max-width: 980px;
            margin: 0 auto;
            padding: 24px 16px 32px;
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .community-create-panel,
        .community-create-error {
            border: 1px solid rgba(214,168,91,0.14);
            box-shadow: 0 12px 24px rgba(0,0,0,0.14);
        }

        .community-create-topbar {
            display: flex;
            justify-content: flex-start;
        }

        .community-create-back {
            display: inline-flex;
            color: #D6A85B;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .community-create-error {
            background: linear-gradient(180deg, rgba(53, 38, 35, 0.92) 0%, rgba(42, 31, 29, 0.92) 100%);
            border-radius: 20px;
            padding: 18px 22px;
            color: #F0B3A9;
            border-color: rgba(224,112,96,0.22);
        }

        .community-create-error-title {
            font-weight: 700;
            margin-bottom: 8px;
            color: #FFB2A7;
        }

        .community-create-error-list {
            margin: 0;
            padding-left: 18px;
            color: #E7C3BD;
            line-height: 1.7;
        }

        .community-create-grid {
            display: block;
        }

        .community-create-panel {
            padding: 26px 28px;
            border-radius: 20px;
            background: rgba(42,44,48,0.78);
            backdrop-filter: blur(10px);
        }

        .community-create-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }

        .community-create-head h2,
        .community-create-subhead h3 {
            margin: 0;
            color: #F0E9DF;
            font-size: 1.5rem;
            font-family: 'Playfair Display', serif;
        }

        .community-create-subhead h3 {
            font-size: 1.1rem;
        }

        .community-create-head p,
        .community-create-subhead p {
            margin: 4px 0 0;
            color: #8A7A66;
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .community-create-eyebrow {
            color: #D6A85B;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.16em;
        }

        .community-create-divider {
            height: 1px;
            background: linear-gradient(to right, rgba(214,168,91,0.3), rgba(214,168,91,0.05), transparent);
            margin-bottom: 20px;
        }

        .community-create-form {
            display: flex;
            flex-direction: column;
            gap: 22px;
        }

        .community-create-composer {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px 18px;
            border-radius: 18px;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.05);
        }

        .community-create-avatar {
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

        .community-create-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .community-create-composer-copy strong {
            display: block;
            color: #F0E9DF;
            font-size: 1rem;
        }

        .community-create-composer-copy span {
            display: block;
            margin-top: 4px;
            color: #8A7A66;
            font-size: 0.9rem;
            line-height: 1.6;
        }

        .community-create-label {
            display: block;
            font-weight: 700;
            margin-bottom: 10px;
            color: #D0C8B8;
            font-size: 14px;
            letter-spacing: 0.02em;
        }

        .community-create-input {
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

        .community-create-input:focus {
            border-color: rgba(214,168,91,0.38);
            box-shadow: 0 0 0 4px rgba(214,168,91,0.08);
        }

        .community-create-textarea {
            resize: vertical;
            min-height: 180px;
            line-height: 1.75;
        }

        .community-create-help,
        .community-create-footer-note {
            margin: 8px 0 0;
            color: #8A7A66;
            font-size: 0.8rem;
            line-height: 1.7;
        }

        .community-create-footer-note {
            text-align: center;
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
            transition: transform 0.18s ease, border-color 0.18s ease, background 0.18s ease;
        }

        .community-category-card:hover {
            transform: translateY(-1px);
            border-color: rgba(214,168,91,0.22);
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

        .community-create-subpanel {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 16px;
        }

        .community-create-subpanel {
            padding: 18px;
        }

        .community-upload-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 12px;
        }

        .community-upload-trigger {
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

        .community-upload-trigger:hover {
            transform: translateY(-1px);
        }

        .community-upload-trigger input {
            display: none;
        }

        .community-selected-file {
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

        .community-selected-file button {
            background: none;
            border: none;
            color: #F0B3A9;
            font-weight: 700;
            cursor: pointer;
        }

        .community-preview-area {
            display: none;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin-top: 16px;
        }

        .community-preview-block {
            display: none;
            padding: 10px;
            border-radius: 14px;
            background: rgba(255,255,255,0.02);
            border: 1px solid rgba(255,255,255,0.05);
        }

        .community-preview-block img,
        .community-preview-block video {
            width: 100%;
            max-height: 220px;
            object-fit: cover;
            border-radius: 12px;
        }

        .community-create-form-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            padding-top: 8px;
            border-top: 1px solid rgba(214,168,91,0.10);
        }

        .community-create-btn {
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

        .community-create-btn:hover {
            transform: translateY(-1px);
        }

        .community-create-btn-primary {
            background: linear-gradient(95deg, #b8842f, #d6a85b);
            color: #17120d;
            box-shadow: 0 10px 24px rgba(199, 150, 69, 0.28);
        }

        .community-create-btn-secondary {
            background: rgba(255,255,255,0.04);
            color: #D0C8B8;
            border: 1px solid rgba(214,168,91,0.14);
        }

        @media (max-width: 768px) {
            .community-create-page {
                padding: 18px 0 28px;
            }

            .community-create-panel {
                padding: 22px;
            }

            .community-category-grid,
            .community-preview-area {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 560px) {
            .community-create-btn {
                width: 100%;
            }

            .community-create-form-actions {
                flex-direction: column;
            }
        }
    </style>
</x-app-layout>
