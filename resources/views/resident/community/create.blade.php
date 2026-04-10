<x-app-layout>
    <div class="community-create-page">
        <section class="community-create-hero">
            <div class="community-create-hero-copy">
                <p class="community-create-kicker">Resident Community Hub</p>
                <h1 class="community-create-title">Create a Post</h1>
                <p class="community-create-subtitle">
                    Start a new conversation, share a neighborhood update, or post something helpful for fellow residents in a cleaner, more professional composer.
                </p>

                <div class="community-create-pills">
                    <span class="community-create-pill">Discussion-first layout</span>
                    <span class="community-create-pill">Photo or video supported</span>
                    <span class="community-create-pill">Reviewed before publishing</span>
                </div>
            </div>

            <div class="community-create-hero-actions">
                <a href="{{ route('community.index') }}" class="community-create-btn community-create-btn-secondary">Back to Community</a>
            </div>
        </section>

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

            <aside class="community-create-sidebar">
                <section class="community-create-panel">
                    <div class="community-create-head community-create-head-simple">
                        <div>
                            <h2>Posting Notes</h2>
                            <p>Keep the board useful, clear, and welcoming for everyone.</p>
                        </div>
                    </div>

                    <div class="community-create-divider"></div>

                    <div class="community-create-note-list">
                        <div class="community-create-note-item">Choose the category that best fits your post so residents can scan the board more easily.</div>
                        <div class="community-create-note-item">Keep the content specific and respectful, especially for community concerns or requests.</div>
                        <div class="community-create-note-item">Use media only when it adds useful context to the discussion.</div>
                    </div>
                </section>

                <section class="community-create-panel">
                    <div class="community-create-head community-create-head-simple">
                        <div>
                            <h2>Visibility</h2>
                            <p>What happens after you submit your post.</p>
                        </div>
                    </div>

                    <div class="community-create-divider"></div>

                    <div class="community-create-meta-list">
                        <div class="community-create-meta-item">
                            <span>Review</span>
                            <strong>Posts are reviewed before appearing on the board.</strong>
                        </div>
                        <div class="community-create-meta-item">
                            <span>Reach</span>
                            <strong>Approved posts become visible to fellow residents.</strong>
                        </div>
                        <div class="community-create-meta-item">
                            <span>Best Practice</span>
                            <strong>Use a strong title and a focused message for better engagement.</strong>
                        </div>
                    </div>
                </section>
            </aside>
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
            max-width: 1600px;
            margin: 0 auto;
            padding: 24px 16px 32px;
            display: flex;
            flex-direction: column;
            gap: 22px;
        }

        .community-create-hero,
        .community-create-panel,
        .community-create-error {
            border: 1px solid rgba(214,168,91,0.14);
            box-shadow: 0 12px 24px rgba(0,0,0,0.14);
        }

        .community-create-hero {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 20px;
            padding: 28px 30px;
            border-radius: 36px;
            background: linear-gradient(115deg, #1F2023 0%, #24262B 38%, #2C2C2F 62%, #3B3023 100%);
            box-shadow: 0 18px 50px rgba(0, 0, 0, 0.18);
        }

        .community-create-hero-copy {
            max-width: 860px;
        }

        .community-create-kicker {
            margin: 0 0 10px;
            color: #D2A04C;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.30em;
        }

        .community-create-title {
            margin: 0;
            color: #F8F3EA;
            font-family: 'Playfair Display', serif;
            font-size: clamp(2.4rem, 4.6vw, 3.8rem);
            line-height: 1.05;
        }

        .community-create-subtitle {
            margin: 12px 0 0;
            color: rgba(255,255,255,0.82);
            font-size: 1.02rem;
            line-height: 1.7;
            max-width: 760px;
        }

        .community-create-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 20px;
        }

        .community-create-pill {
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.08);
            color: #E9D8BD;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .community-create-hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
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
            display: grid;
            grid-template-columns: minmax(0, 1.18fr) minmax(320px, 0.82fr);
            gap: 24px;
            align-items: start;
        }

        .community-create-sidebar {
            display: flex;
            flex-direction: column;
            gap: 24px;
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

        .community-create-subpanel,
        .community-create-note-item,
        .community-create-meta-item {
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

        .community-create-note-list,
        .community-create-meta-list {
            display: grid;
            gap: 12px;
        }

        .community-create-note-item,
        .community-create-meta-item {
            padding: 14px 16px;
        }

        .community-create-note-item {
            color: #B8AB98;
            font-size: 0.88rem;
            line-height: 1.75;
        }

        .community-create-meta-item span {
            display: block;
            color: #8A7A66;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            margin-bottom: 8px;
        }

        .community-create-meta-item strong {
            color: #F0E9DF;
            font-size: 0.92rem;
            line-height: 1.6;
            font-weight: 600;
        }

        @media (max-width: 1024px) {
            .community-create-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .community-create-page {
                padding: 18px 0 28px;
            }

            .community-create-hero,
            .community-create-panel {
                padding: 22px;
            }

            .community-create-hero {
                flex-direction: column;
                align-items: flex-start;
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
