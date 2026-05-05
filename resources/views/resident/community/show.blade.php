<x-app-layout>
    <div class="community-show-page">
        @if (session('success'))
            <div class="community-show-feedback-banner" data-auto-dismiss>
                {{ session('success') }}
            </div>
        @endif

        <div class="community-show-topbar">
            <a href="{{ route('community.index') }}" class="community-show-back">Back to Community</a>
        </div>

        <section class="community-show-panel">
            <div class="community-post-card">
                <div class="community-post-head">
                    <div class="community-post-author">
                        <div class="community-post-avatar">
                            @if($post->user->profile_photo_url)
                                <img src="{{ $post->user->profile_photo_url }}" alt="{{ $post->user->name }}">
                            @else
                                {{ $post->user->profile_initials }}
                            @endif
                        </div>
                        <div>
                            <strong>{{ $post->user->name }}</strong>
                            <span>{{ $post->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>

                    <div class="community-post-badges">
                        @if(auth()->check() && auth()->id() === $post->user_id && $post->status !== 'approved')
                            <span class="community-post-status community-post-status-{{ $post->status }}">
                                {{ ucfirst($post->status) }}
                            </span>
                        @endif
                        <span class="community-post-type">{{ str_replace('_', ' ', ucfirst($post->type)) }}</span>
                    </div>
                </div>

                <div class="community-post-copy">
                    <h2>{{ $post->title }}</h2>
                    <p>{{ $post->content }}</p>
                </div>

                @if($post->image_path)
                    <div class="community-post-media">
                        <img src="{{ Storage::url($post->image_path) }}" alt="{{ $post->title }}">
                    </div>
                @endif

                @if($post->video_path)
                    <div class="community-post-media">
                        <video controls>
                            <source src="{{ Storage::url($post->video_path) }}">
                        </video>
                    </div>
                @endif

                @if($post->status === 'rejected' && $post->rejection_reason)
                    <div class="community-post-rejection">
                        Rejected: {{ $post->rejection_reason }}
                    </div>
                @endif

                @if(auth()->check() && auth()->id() === $post->user_id)
                    <div class="community-post-owner-actions">
                        <a href="{{ route('community.edit', $post) }}" class="community-post-owner-btn">Edit Post</a>
                        <form method="POST" action="{{ route('community.destroy', $post) }}" data-confirm-message="Delete this post? This action cannot be undone.">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="community-post-owner-btn community-post-owner-btn-delete">Delete Post</button>
                        </form>
                    </div>
                @endif

                <div class="community-thread-summary-grid">
                    <div class="community-show-meta-item">
                        <span>Comments</span>
                        <strong>{{ $post->comments->count() }}</strong>
                    </div>
                    <div class="community-show-meta-item">
                        <span>Category</span>
                        <strong>{{ str_replace('_', ' ', ucfirst($post->type)) }}</strong>
                    </div>
                    <div class="community-show-meta-item">
                        <span>Posted</span>
                        <strong>{{ $post->created_at->diffForHumans() }}</strong>
                    </div>
                    <div class="community-show-meta-item">
                        <span>Status</span>
                        <strong>{{ ucfirst($post->status) }}</strong>
                    </div>
                </div>
            </div>
        </section>

        <section class="community-show-panel">
            <div class="community-show-panel-head">
                <div>
                    <h2>Comments</h2>
                    <p>{{ $post->comments->count() }} replies in this discussion.</p>
                </div>
            </div>

            <div class="community-show-divider"></div>

            @auth
                <form method="POST" action="{{ route('community.comment', $post) }}" class="community-comment-form" data-prevent-double-submit data-submitting-text="Posting Comment...">
                    @csrf
                    <div class="community-comment-composer">
                        <div class="community-comment-avatar">
                            @if(auth()->user()->profile_photo_url)
                                <img src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}">
                            @else
                                {{ auth()->user()->profile_initials }}
                            @endif
                        </div>
                        <div class="community-comment-compose-body">
                            <div class="community-comment-compose-meta">
                                <strong>{{ auth()->user()->name }}</strong>
                                <span>Join the discussion</span>
                            </div>
                            <textarea name="content" rows="3" class="community-comment-input" placeholder="Write a comment..."></textarea>
                        </div>
                    </div>
                    <button type="submit" class="community-comment-btn">Post Comment</button>
                </form>
            @endauth

            <div class="community-comment-list">
                @forelse($post->comments as $comment)
                    <div class="community-comment-item" x-data="{ editing: false }">
                        <div class="community-comment-avatar">
                            @if($comment->user->profile_photo_url)
                                <img src="{{ $comment->user->profile_photo_url }}" alt="{{ $comment->user->name }}">
                            @else
                                {{ $comment->user->profile_initials }}
                            @endif
                        </div>
                        <div class="community-comment-body">
                            <div class="community-comment-meta">
                                <strong>{{ $comment->user->name }}</strong>
                                <span>
                                    {{ $comment->created_at->diffForHumans() }}
                                    @if($comment->updated_at && $comment->updated_at->gt($comment->created_at))
                                        • Edited
                                    @endif
                                </span>
                            </div>

                            <div class="community-comment-content" x-show="!editing">
                                <p>{{ $comment->content }}</p>
                            </div>

                            @auth
                                @if(auth()->id() === $comment->user_id || auth()->user()->role === 'manager')
                                    <form method="POST"
                                          action="{{ route('community.comments.update', $comment) }}"
                                          class="community-comment-edit-form"
                                          x-show="editing"
                                          style="display: none;"
                                          data-prevent-double-submit
                                          data-submitting-text="Saving...">
                                        @csrf
                                        @method('PATCH')
                                        <textarea name="content" rows="3" class="community-comment-input" required>{{ old('content', $comment->content) }}</textarea>
                                        <div class="community-comment-actions">
                                            <button type="submit" class="community-comment-action-btn community-comment-action-btn-primary">Save</button>
                                            <button type="button" class="community-comment-action-btn" @click="editing = false">Cancel</button>
                                        </div>
                                    </form>

                                    <div class="community-comment-actions" x-show="!editing">
                                        <button type="button" class="community-comment-action-btn" @click="editing = true">Edit</button>
                                        <form method="POST" action="{{ route('community.comments.destroy', $comment) }}" data-confirm-message="Delete this comment?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="community-comment-action-btn community-comment-action-btn-danger">Delete</button>
                                        </form>
                                    </div>
                                @endif
                            @endauth
                        </div>
                    </div>
                @empty
                    <div class="community-empty-state">No comments yet. Be the first to reply.</div>
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
        .community-show-page {
            max-width: 980px;
            margin: 0 auto;
            padding: 24px 16px 32px;
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .community-show-feedback-banner {
            padding: 16px 18px;
            border-radius: 18px;
            background: linear-gradient(180deg, rgba(46, 58, 41, 0.92) 0%, rgba(34, 46, 31, 0.92) 100%);
            border: 1px solid rgba(157, 195, 117, 0.18);
            color: #D5E3BE;
            font-size: 0.92rem;
            font-weight: 600;
            box-shadow: 0 12px 24px rgba(0,0,0,0.14);
        }

        .community-show-panel {
            border: 1px solid rgba(214,168,91,0.14);
            box-shadow: 0 12px 24px rgba(0,0,0,0.14);
        }

        .community-show-topbar {
            display: flex;
            justify-content: flex-start;
        }

        .community-show-back {
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

        .community-show-panel {
            padding: 26px 28px;
            border-radius: 20px;
            background: rgba(42,44,48,0.78);
            backdrop-filter: blur(10px);
        }

        .community-show-panel-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 16px;
        }

        .community-show-panel-head h2 {
            margin: 0;
            color: #F0E9DF;
            font-size: 1.5rem;
            font-family: 'Playfair Display', serif;
        }

        .community-show-panel-head p {
            margin: 4px 0 0;
            color: #8A7A66;
            font-size: 0.95rem;
        }

        .community-show-divider {
            height: 1px;
            background: linear-gradient(to right, rgba(214,168,91,0.3), rgba(214,168,91,0.05), transparent);
            margin-bottom: 18px;
        }

        .community-post-card,
        .community-show-meta-item,
        .community-comment-item {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 18px;
        }

        .community-post-card {
            padding: 22px;
        }

        .community-post-head,
        .community-comment-item {
            display: flex;
            gap: 14px;
        }

        .community-post-head {
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .community-post-author {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .community-post-avatar,
        .community-comment-avatar {
            width: 48px;
            height: 48px;
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

        .community-post-avatar img,
        .community-comment-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .community-post-author strong,
        .community-comment-meta strong {
            display: block;
            color: #F0E9DF;
            font-size: 0.98rem;
        }

        .community-post-author span,
        .community-comment-meta span {
            color: #8A7A66;
            font-size: 0.86rem;
        }

        .community-post-type,
        .community-post-status {
            padding: 8px 14px;
            border-radius: 999px;
            font-size: 0.76rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .community-post-type {
            background: rgba(214,168,91,0.12);
            border: 1px solid rgba(214,168,91,0.18);
            color: #D6A85B;
        }

        .community-post-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }

        .community-post-status-pending {
            background: rgba(214,168,91,0.12);
            border: 1px solid rgba(214,168,91,0.18);
            color: #E9D8BD;
        }

        .community-post-status-rejected {
            background: rgba(224,112,96,0.12);
            border: 1px solid rgba(224,112,96,0.18);
            color: #F0B3A9;
        }

        .community-post-copy h2 {
            margin: 0 0 12px;
            color: #F0E9DF;
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
        }

        .community-post-copy p,
        .community-comment-body p {
            margin: 0;
            color: #B8AB98;
            line-height: 1.8;
            font-size: 0.96rem;
        }

        .community-post-media {
            margin-top: 18px;
        }

        .community-post-media img,
        .community-post-media video {
            width: 100%;
            border-radius: 18px;
            border: 1px solid rgba(255,255,255,0.06);
            background: rgba(18,20,23,0.55);
        }

        .community-post-rejection {
            margin-top: 18px;
            padding: 14px 16px;
            border-radius: 16px;
            background: rgba(224,112,96,0.10);
            border: 1px solid rgba(224,112,96,0.18);
            color: #F0B3A9;
            line-height: 1.7;
        }

        .community-post-owner-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 18px;
        }

        .community-post-owner-actions form {
            margin: 0;
        }

        .community-post-owner-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 16px;
            border-radius: 999px;
            border: 1px solid rgba(214,168,91,0.18);
            background: rgba(255,255,255,0.03);
            color: #D6A85B;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.84rem;
            cursor: pointer;
        }

        .community-post-owner-btn-delete {
            border-color: rgba(224,112,96,0.20);
            color: #F0B3A9;
            background: rgba(224,112,96,0.06);
        }

        .community-thread-summary-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin-top: 18px;
        }

        .community-show-meta-item {
            padding: 16px 18px;
        }

        .community-show-meta-item span {
            display: block;
            color: #8A7A66;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            font-weight: 700;
        }

        .community-show-meta-item strong {
            display: block;
            margin-top: 8px;
            color: #F0E9DF;
            font-size: 0.94rem;
            font-weight: 600;
        }

        .community-comment-form {
            display: flex;
            flex-direction: column;
            gap: 14px;
            margin-bottom: 20px;
        }

        .community-comment-composer {
            display: flex;
            gap: 14px;
            padding: 18px;
            border-radius: 18px;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.05);
        }

        .community-comment-compose-body {
            flex: 1;
        }

        .community-comment-compose-meta strong {
            display: block;
            color: #F0E9DF;
        }

        .community-comment-compose-meta span {
            display: block;
            margin-top: 2px;
            color: #8A7A66;
            font-size: 0.86rem;
        }

        .community-comment-input {
            width: 100%;
            margin-top: 12px;
            padding: 14px 16px;
            border-radius: 14px;
            border: 1px solid rgba(214,168,91,0.14);
            background: rgba(32,34,37,0.82);
            color: #F0E9DF;
            resize: vertical;
            min-height: 100px;
        }

        .community-comment-btn {
            align-self: flex-start;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 22px;
            border: none;
            border-radius: 999px;
            background: linear-gradient(95deg, #b8842f, #d6a85b);
            color: #17120d;
            font-size: 0.92rem;
            font-weight: 700;
            cursor: pointer;
        }

        .community-comment-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .community-comment-item {
            padding: 18px;
        }

        .community-comment-body {
            flex: 1;
        }

        .community-comment-content {
            margin-bottom: 10px;
        }

        .community-comment-meta {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
        }

        .community-comment-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .community-comment-actions form {
            margin: 0;
        }

        .community-comment-edit-form {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .community-comment-action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 9px 14px;
            border-radius: 999px;
            border: 1px solid rgba(214,168,91,0.18);
            background: rgba(255,255,255,0.03);
            color: #d6a85b;
            font-size: 0.8rem;
            font-weight: 700;
            cursor: pointer;
        }

        .community-comment-action-btn-primary {
            background: linear-gradient(95deg, #b8842f, #d6a85b);
            color: #17120d;
            border: none;
        }

        .community-comment-action-btn-danger {
            border-color: rgba(224,112,96,0.22);
            color: #f0b3a9;
            background: rgba(224,112,96,0.08);
        }

        .community-empty-state {
            padding: 20px;
            border-radius: 18px;
            background: rgba(255,255,255,0.03);
            border: 1px dashed rgba(214,168,91,0.18);
            text-align: center;
            color: #B8AB98;
        }

        @media (max-width: 900px) {
            .community-thread-summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 768px) {
            .community-show-page {
                padding: 18px 0 28px;
            }

            .community-show-panel {
                padding: 22px;
            }

            .community-post-head {
                align-items: flex-start;
            }
        }

        @media (max-width: 560px) {
            .community-show-back {
                width: 100%;
            }

            .community-post-copy h2 {
                font-size: 1.5rem;
            }

            .community-show-panel-head h2 {
                font-size: 1.25rem;
            }

            .community-post-badges,
            .community-comment-meta {
                flex-wrap: wrap;
            }

            .community-thread-summary-grid {
                grid-template-columns: 1fr;
            }

            .community-post-owner-actions {
                flex-direction: column;
            }

            .community-post-owner-actions a,
            .community-post-owner-actions form,
            .community-post-owner-actions button {
                width: 100%;
            }

            .community-post-owner-actions form {
                display: flex;
            }

            .community-comment-composer {
                flex-direction: column;
            }

            .community-comment-btn {
                width: 100%;
            }
        }
    </style>
</x-app-layout>
