<x-app-layout>
    @php
        $totalComments = $posts->sum('comments_count');
        $totalLikes = $posts->sum('likes_count');
        $ownReviewPosts = $ownReviewPosts ?? collect();
    @endphp

    <div class="community-feed-page">
        <section class="community-feed-hero resident-hero-botanical">
            <div class="community-feed-hero-copy">
                <p class="community-feed-kicker">Resident Community Hub</p>
                <h1 class="community-feed-title">Community Board</h1>
                <p class="community-feed-subtitle">
                    A live Rexhall feed for updates, open discussions, and resident-to-resident conversations.
                </p>
            </div>

            @if($ownReviewPosts->isNotEmpty())
                <button type="button"
                        class="community-review-drawer-trigger"
                        data-community-review-open
                        aria-controls="community-review-drawer"
                        aria-expanded="false">
                    <span class="community-review-drawer-trigger-icon" aria-hidden="true">&#9203;</span>
                    View Pending Posts ({{ $ownReviewPosts->count() }})
                </button>
            @endif
        </section>

        <section class="community-composer-card">
            <div class="community-composer-main">
                <div class="community-composer-avatar">
                    @if(auth()->user()?->profile_photo_url)
                        <img src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}">
                    @else
                        {{ auth()->user()?->profile_initials ?? 'R' }}
                    @endif
                </div>
                <a href="{{ route('community.create') }}" class="community-composer-trigger">
                    What's on your mind?
                </a>
            </div>

            <div class="community-composer-actions">
                <a href="{{ route('community.create') }}">Create Post</a>
            </div>
        </section>

        @if(session('success') || session('warning') || session('error'))
            @php
                $communityFlashType = session('error') ? 'error' : (session('warning') ? 'warning' : 'success');
                $communityFlashMessage = session('error') ?? session('warning') ?? session('success');
            @endphp
            <div class="community-feedback-banner community-feedback-banner-{{ $communityFlashType }}"
                 data-community-flash
                 data-toast-type="{{ $communityFlashType }}"
                 data-toast-message="{{ $communityFlashMessage }}"
                 data-auto-dismiss>
                {{ $communityFlashMessage }}
            </div>
        @endif

        <section data-filter-scope>
            <div class="community-filter-bar">
                <input type="search" class="community-filter-input" placeholder="Search posts, authors, or content" data-filter-input data-filter-key="search">
                <select class="community-filter-select" data-filter-select data-filter-key="type">
                    <option value="">All post types</option>
                    <option value="discussion">Discussion</option>
                    <option value="lost_found">Lost &amp; Found</option>
                    <option value="buy_sell">Buy &amp; Sell</option>
                    <option value="event">Event</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <div class="community-feed-list" data-feature-skeleton>
                @for($i = 0; $i < 3; $i++)
                    <article class="community-feed-card feature-skeleton-card">
                        <div class="feature-skeleton-post-head">
                            <span class="feature-skeleton-avatar"></span>
                            <div style="flex: 1;">
                                <span class="feature-skeleton-line title"></span>
                                <span class="feature-skeleton-line short"></span>
                            </div>
                        </div>
                        <span class="feature-skeleton-line medium" style="margin-top: 18px;"></span>
                        <span class="feature-skeleton-line long"></span>
                        <span class="feature-skeleton-line long"></span>
                        <div class="feature-skeleton-post-actions">
                            <span class="feature-skeleton-box"></span>
                            <span class="feature-skeleton-box"></span>
                        </div>
                    </article>
                @endfor
            </div>

            <section class="community-feed-list" data-skeleton-content data-progressive-list>
            @forelse($posts as $post)
                <article class="community-feed-card"
                         data-progressive-item
                         data-filter-card
                         data-search="{{ Str::lower($post->title . ' ' . $post->content . ' ' . ($post->user->name ?? '')) }}"
                         data-type="{{ $post->type }}">
                    <div class="community-feed-card-head">
                        <div class="community-feed-author">
                            <div class="community-feed-avatar">
                                @if($post->user->profile_photo_url)
                                    <img src="{{ $post->user->profile_photo_url }}" alt="{{ $post->user->name }}">
                                @else
                                    {{ $post->user->profile_initials }}
                                @endif
                            </div>

                            <div class="community-feed-author-copy">
                                <strong>{{ $post->user->name }}</strong>
                                <span>{{ $post->created_at->diffForHumans() }} · {{ str_replace('_', ' ', ucfirst($post->type)) }}</span>
                            </div>
                        </div>

                        <div class="community-feed-head-actions">
                            @if(auth()->check() && auth()->id() === $post->user_id && $post->status !== 'approved')
                                <span class="community-status-chip community-status-chip-{{ $post->status }}">
                                    {{ ucfirst($post->status) }}
                                </span>
                            @endif

                            @if(auth()->check() && auth()->id() === $post->user_id)
                                <details class="community-post-menu">
                                    <summary aria-label="Post settings">
                                        <span></span><span></span><span></span>
                                    </summary>
                                    <div class="community-post-menu-list">
                                        <a href="{{ route('community.edit', $post) }}">Edit post</a>
                                        <form method="POST"
                                              action="{{ route('community.destroy', $post) }}"
                                              data-confirm-message="Delete this post? This action cannot be undone."
                                              data-prevent-double-submit
                                              data-submitting-text="Deleting Post...">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit">Delete post</button>
                                        </form>
                                    </div>
                                </details>
                            @endif
                        </div>
                    </div>

                    <div class="community-feed-copy">
                        <h3>{{ $post->title }}</h3>
                        <p>{{ $post->content }}</p>
                    </div>

                    @if($post->image_path)
                        <a href="{{ route('community.show', $post) }}" class="community-feed-media">
                            <img src="{{ $post->image_url }}" alt="{{ $post->title }}">
                        </a>
                    @endif

                    @if($post->video_path)
                        <a href="{{ route('community.show', $post) }}" class="community-feed-media community-feed-media-video">
                            <div class="community-feed-video-pill">Video post</div>
                        </a>
                    @endif

                    <div class="community-feed-stats-row">
                        <span class="community-like-count" data-likes-count>{{ $post->likes_count }} {{ \Illuminate\Support\Str::plural('like', $post->likes_count) }}</span>
                        <span>{{ $post->comments_count }} {{ \Illuminate\Support\Str::plural('comment', $post->comments_count) }}</span>
                    </div>

                    <div class="community-feed-actions">
                        <form method="POST" action="{{ route('community.like', $post) }}" class="community-like-form">
                            @csrf
                            <button type="submit" class="community-action-btn community-like-button {{ $post->isLikedBy(auth()->user()) ? 'is-active' : '' }}">
                                Like
                            </button>
                        </form>
                        <a href="{{ route('community.show', $post) }}" class="community-action-btn">
                            Comment
                        </a>
                    </div>
                </article>
            @empty
                <x-resident-empty-state icon="community" title="No posts yet" description="Be the first resident to share an update with the community." :action-href="route('community.create')" action-label="Create a Post" />
            @endforelse
            </section>

            <div class="community-filter-empty" data-filter-empty>No community posts match your filters.</div>
        </section>

        @if($posts->hasMorePages())
            <div class="community-pagination-link">
                <a href="{{ $posts->nextPageUrl() }}">Load more posts</a>
            </div>
        @endif
    </div>

    @if($ownReviewPosts->isNotEmpty())
        <div class="community-review-drawer-backdrop" data-community-review-backdrop hidden></div>

        <aside id="community-review-drawer"
               class="community-review-drawer"
               data-community-review-drawer
               aria-hidden="true"
               aria-labelledby="community-review-drawer-title">
            <div class="community-review-drawer-head">
                <div>
                    <p class="community-feed-kicker">Posts Awaiting Review</p>
                    <h2 id="community-review-drawer-title">Track your submitted posts.</h2>
                    <p>Review posts that are awaiting approval or need a revision before publication.</p>
                </div>
                <button type="button"
                        class="community-review-drawer-close"
                        data-community-review-close
                        aria-label="Close pending posts">
                    &times;
                </button>
            </div>

            <div class="community-review-list" data-progressive-list>
                @foreach($ownReviewPosts as $reviewPost)
                    <article class="community-review-card" data-progressive-item>
                        <div class="community-review-card-status">
                            <span class="community-status-chip community-status-chip-{{ $reviewPost->status }}">
                                {{ $reviewPost->status === 'rejected' ? 'Needs Revision' : 'Awaiting Review' }}
                            </span>
                            <span>{{ $reviewPost->updated_at->diffForHumans() }}</span>
                        </div>

                        <div class="community-review-top">
                            <div>
                                <h3>{{ $reviewPost->title }}</h3>
                                <p>{{ Str::limit($reviewPost->content, 110) }}</p>
                            </div>
                        </div>

                        <div class="community-review-meta">
                            <span>{{ $reviewPost->comments_count }} comments</span>
                            <span>{{ str_replace('_', ' ', ucfirst($reviewPost->type)) }}</span>
                        </div>

                        @if($reviewPost->image_path)
                            <a href="{{ route('community.show', $reviewPost) }}" class="community-review-media">
                                <img src="{{ $reviewPost->image_url }}" alt="{{ $reviewPost->title }}">
                            </a>
                        @endif

                        @if($reviewPost->video_path)
                            <a href="{{ route('community.show', $reviewPost) }}" class="community-review-media community-review-media-video">
                                <span>Video attached</span>
                            </a>
                        @endif

                        @if($reviewPost->status === 'rejected' && $reviewPost->rejection_reason)
                            <div class="community-review-note"><strong>Admin Feedback:</strong> {{ $reviewPost->rejection_reason }}</div>
                        @endif

                        <div class="community-review-actions">
                            @if($reviewPost->status === 'rejected')
                                <a href="{{ route('community.edit', $reviewPost) }}">Revise Post</a>
                            @endif
                            <form method="POST"
                                  action="{{ route('community.destroy', $reviewPost) }}"
                                  data-confirm-message="Delete this post? This action cannot be undone."
                                  data-prevent-double-submit
                                  data-submitting-text="Deleting Post...">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="community-review-delete">Delete post</button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </div>
        </aside>
    @endif

    <script>
        const reviewDrawer = document.querySelector('[data-community-review-drawer]');
        const reviewDrawerOpen = document.querySelector('[data-community-review-open]');
        const reviewDrawerClose = document.querySelector('[data-community-review-close]');
        const reviewDrawerBackdrop = document.querySelector('[data-community-review-backdrop]');

        if (reviewDrawerBackdrop) {
            document.body.appendChild(reviewDrawerBackdrop);
        }

        if (reviewDrawer) {
            document.body.appendChild(reviewDrawer);
        }

        const setReviewDrawerOpen = (isOpen) => {
            if (!reviewDrawer || !reviewDrawerBackdrop || !reviewDrawerOpen) {
                return;
            }

            reviewDrawer.classList.toggle('is-open', isOpen);
            reviewDrawerBackdrop.classList.toggle('is-open', isOpen);
            reviewDrawerBackdrop.hidden = !isOpen;
            reviewDrawer.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
            reviewDrawerOpen.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            document.body.classList.toggle('community-review-drawer-active', isOpen);

            if (isOpen) {
                reviewDrawerClose?.focus();
            } else {
                reviewDrawerOpen.focus();
            }
        };

        reviewDrawerOpen?.addEventListener('click', () => setReviewDrawerOpen(true));
        reviewDrawerClose?.addEventListener('click', () => setReviewDrawerOpen(false));
        reviewDrawerBackdrop?.addEventListener('click', () => setReviewDrawerOpen(false));

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && reviewDrawer?.classList.contains('is-open')) {
                setReviewDrawerOpen(false);
            }
        });

        document.querySelectorAll('[data-auto-dismiss]').forEach((flash) => {
            setTimeout(() => {
                flash.style.transition = 'opacity 0.35s ease, transform 0.35s ease';
                flash.style.opacity = '0';
                flash.style.transform = 'translateY(-6px)';
                setTimeout(() => flash.remove(), 360);
            }, 3200);
        });

        document.querySelectorAll('[data-community-flash]').forEach((flash) => {
            window.appToast?.(flash.dataset.toastType || 'info', flash.dataset.toastMessage || flash.textContent.trim());
        });

        document.querySelectorAll('.community-like-form').forEach((form) => {
            form.addEventListener('submit', async (event) => {
                event.preventDefault();

                const button = form.querySelector('.community-like-button');
                const card = form.closest('.community-feed-card');
                const countNode = card?.querySelector('[data-likes-count]');

                if (!button || !countNode || button.disabled) {
                    return;
                }

                button.disabled = true;

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: new FormData(form),
                    });

                    if (!response.ok) {
                        throw new Error('Like request failed.');
                    }

                    const data = await response.json();
                    button.classList.toggle('is-active', !!data.liked);
                    countNode.textContent = `${data.likes_count} ${data.likes_count === 1 ? 'like' : 'likes'}`;
                    window.appToast?.('success', data.liked ? 'Post liked.' : 'Like removed.');
                } catch (error) {
                    window.appToast?.('error', 'Like update failed. Please check your connection and try again.');
                } finally {
                    setTimeout(() => {
                        button.disabled = false;
                    }, 1500);
                }
            });
        });
    </script>

    <style>
.community-feed-page {
    width: min(100%, 980px);
    margin-left: auto;
    margin-right: auto;
    padding: 24px 16px 36px;
    display: flex;
    flex-direction: column;
    gap: 18px;
}
.community-feedback-banner, .community-feed-hero, .community-review-strip, .community-composer-card, .community-feed-card, .community-empty-state {
    border: 1px solid rgba(214, 168, 91, 0.14);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.14);
}
.community-feedback-banner {
    padding: 16px 18px;
    border-radius: 18px;
    background: linear-gradient(180deg, rgba(46, 58, 41, 0.92) 0%, rgba(34, 46, 31, 0.92) 100%);
    border-color: rgba(157, 195, 117, 0.18);
    color: #D5E3BE;
    font-size: 0.92rem;
    font-weight: 600;
}
.community-feedback-banner-warning {
    background: linear-gradient(180deg, rgba(74, 48, 32, 0.92) 0%, rgba(54, 37, 29, 0.92) 100%);
    border-color: rgba(217, 119, 6, 0.24);
    color: #F4C98C;
}
.community-feedback-banner-error {
    background: linear-gradient(180deg, rgba(76, 31, 35, 0.92) 0%, rgba(54, 28, 31, 0.92) 100%);
    border-color: rgba(220, 38, 38, 0.24);
    color: #F5B7B7;
}
.community-feed-hero {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
    padding: 28px 30px;
    border-radius: 34px;
    background: linear-gradient(115deg, #1F2023 0%, #24262B 38%, #2C2C2F 62%, #3B3023 100%);
}
.community-feed-hero-copy {
    width: min(100%, 760px);
}
.community-feed-kicker {
    margin: 0 0 10px;
    color: #D2A04C;
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.30em;
}
.community-feed-title {
    margin: 0;
    color: #F8F3EA;
    font-family: 'Playfair Display', serif;
    font-size: clamp(2.2rem, 4.2vw, 3.5rem);
    line-height: 1.05;
}
.community-feed-subtitle {
    margin: 12px 0 0;
    color: rgba(255, 255, 255, 0.82);
    line-height: 1.7;
    max-width: 720px;
}
.community-review-drawer-trigger {
    display: inline-flex;
    align-items: center;
    gap: 9px;
    flex-shrink: 0;
    padding: 12px 16px;
    border-radius: 999px;
    border: 1px solid rgba(214, 168, 91, 0.28);
    background: rgba(214, 168, 91, 0.10);
    color: #E3B867;
    font: inherit;
    font-size: 0.84rem;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
}
.community-review-drawer-trigger:hover {
    background: rgba(214, 168, 91, 0.18);
    border-color: rgba(214, 168, 91, 0.42);
    transform: translateY(-1px);
}
.community-review-drawer-trigger-icon {
    font-size: 0.95rem;
    line-height: 1;
}
body.community-review-drawer-active {
    overflow: hidden;
}
.community-review-drawer-backdrop {
    position: fixed;
    inset: 0;
    z-index: 110;
    background: rgba(10, 10, 11, 0.18);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    opacity: 0;
    transition: opacity 0.28s ease;
}
.community-review-drawer-backdrop.is-open {
    opacity: 1;
}
.community-review-drawer {
    position: fixed;
    top: 0;
    right: 0;
    z-index: 111;
    width: min(92vw, 400px);
    height: 100vh;
    padding: 22px 20px;
    overflow-y: auto;
    border-left: 1px solid rgba(214, 168, 91, 0.24);
    background: rgba(36, 38, 41, 0.98);
    box-shadow: -20px 0 46px rgba(0, 0, 0, 0.32);
    transform: translateX(104%);
    transition: transform 0.3s ease, visibility 0s linear 0.3s;
    visibility: hidden;
}
.community-review-drawer.is-open {
    transform: translateX(0);
    transition: transform 0.3s ease, visibility 0s linear 0s;
    visibility: visible;
}
.community-review-drawer-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 18px;
    padding-bottom: 18px;
    border-bottom: 1px solid rgba(214, 168, 91, 0.16);
}
.community-review-drawer-head h2 {
    margin: 0;
    color: #F8F3EA;
    font-family: 'Inter', system-ui, sans-serif;
    font-size: 1.48rem;
    font-weight: 700;
    line-height: 1.2;
    letter-spacing: -0.02em;
}
.community-review-drawer-head p:last-child {
    margin: 8px 0 0;
    color: rgba(248, 243, 234, 0.82);
    font-size: 0.92rem;
    line-height: 1.75;
}
.community-review-drawer-close {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    flex-shrink: 0;
    border: 1px solid rgba(214, 168, 91, 0.20);
    border-radius: 10px;
    background: transparent;
    color: #E0C38D;
    font: inherit;
    font-size: 1.35rem;
    line-height: 1;
    cursor: pointer;
}
.community-feed-stats {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 20px;
}
.community-feed-stat {
    min-width: 120px;
    padding: 12px 14px;
    border-radius: 16px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.07);
}
.community-feed-stat span {
    display: block;
    color: #A89376;
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 0.14em;
    font-weight: 700;
}
.community-feed-stat strong {
    display: block;
    margin-top: 6px;
    color: #F0E9DF;
    font-size: 1.15rem;
    font-weight: 700;
}
.community-review-strip, .community-composer-card, .community-feed-card, .community-empty-state {
    border-radius: 22px;
    background: rgba(42, 44, 48, 0.82);
    backdrop-filter: blur(10px);
}
.community-review-strip, .community-composer-card, .community-feed-list, .community-pagination-link {
    width: 100%;
}
.community-review-strip, .community-composer-card, .community-feed-card {
    padding: 20px 22px;
}
.community-section-head h2 {
    margin: 0;
    color: #F0E9DF;
    font-family: 'Playfair Display', serif;
    font-size: 1.32rem;
}
.community-section-head p {
    margin: 6px 0 0;
    color: #8A7A66;
    font-size: 0.9rem;
}
.community-review-list {
    display: grid;
    gap: 12px;
    margin-top: 18px;
}
body.role-resident .community-review-drawer .community-review-card {
    padding: 14px !important;
    border-radius: 12px !important;
    background: rgba(255, 255, 255, 0.025) !important;
    border: 1px solid rgba(255, 255, 255, 0.075) !important;
    box-shadow: none !important;
}
body.role-resident .community-review-drawer .community-review-card:first-child {
    padding-top: 14px !important;
    border-top: 1px solid rgba(255, 255, 255, 0.075) !important;
}
.community-review-card-status {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 10px;
    color: #928571;
    font-size: 0.76rem;
}
.community-review-card .community-status-chip {
    min-height: 0 !important;
    padding: 3px 0 !important;
    border: 0;
    border-radius: 0;
    background: transparent;
    box-shadow: none;
    font-size: 0.64rem !important;
    letter-spacing: 0.12em !important;
}
.community-review-card .community-status-chip::before {
    content: '';
    width: 6px;
    height: 6px;
    margin-right: 7px;
    border-radius: 50%;
    background: currentColor;
    opacity: 0.82;
}
.community-review-top {
    display: block;
}
.community-review-top h3 {
    margin: 0;
    color: #F0E9DF;
    font-size: 1rem;
}
.community-review-top p {
    margin: 8px 0 0;
    color: #B8AB98;
    line-height: 1.65;
}
.community-review-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 10px 14px;
    margin-top: 10px;
    color: #9E8E79;
    font-size: 0.8rem;
}
.community-review-media {
    display: inline-flex;
    margin-top: 12px;
    width: 124px;
    height: 92px;
    overflow: hidden;
    border-radius: 14px;
    border: 1px solid rgba(255, 255, 255, 0.06);
    background: rgba(18, 20, 23, 0.55);
    text-decoration: none;
}
.community-review-media img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.community-review-media-video {
    align-items: center;
    justify-content: center;
    padding: 0 12px;
}
.community-review-media-video span {
    color: #D6A85B;
    font-size: 0.78rem;
    font-weight: 700;
    text-align: center;
}
.community-review-note {
    margin-top: 12px;
    padding: 12px 14px;
    border-radius: 14px;
    background: rgba(224, 112, 96, 0.10);
    border: 1px solid rgba(224, 112, 96, 0.16);
    color: #F0B3A9;
    font-size: 0.86rem;
    line-height: 1.65;
}
.community-review-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 12px;
    padding-top: 10px;
    border-top: 1px solid rgba(255, 255, 255, 0.06);
}
body.role-resident .community-review-drawer .community-review-actions a, body.role-resident .community-review-drawer .community-review-actions button {
    padding: 4px 0 !important;
    border: 0 !important;
    border-radius: 0 !important;
    background: transparent !important;
    color: #D6A85B;
    font-weight: 700;
    text-decoration: none;
    cursor: pointer;
}
body.role-resident .community-review-drawer .community-review-actions button::before {
    content: '×';
    margin-right: 6px;
    font-size: 1rem;
    line-height: 0;
}
.community-review-actions form {
    margin: 0;
}
.community-review-actions .community-review-delete {
    color: #F0A89E;
}
.community-review-actions .community-review-delete:hover {
    color: #FFD0C9;
}
.community-composer-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
}
.community-composer-main {
    display: flex;
    align-items: center;
    gap: 14px;
    flex: 1;
}
.community-composer-avatar, .community-feed-avatar {
    width: 52px;
    height: 52px;
    border-radius: 999px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    background: linear-gradient(135deg, rgba(214, 168, 91, 0.26), rgba(190, 147, 96, 0.08));
    color: #F4DEB5;
    font-weight: 700;
    flex-shrink: 0;
}
.community-composer-avatar img, .community-feed-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.community-composer-trigger {
    flex: 1;
    min-height: 54px;
    display: flex;
    align-items: center;
    padding: 0 18px;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.06);
    color: #AFA392;
    text-decoration: none;
    font-size: 1rem;
}
.community-composer-actions a, .community-pagination-link a {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 12px 20px;
    border-radius: 999px;
    background: linear-gradient(95deg, #b8842f, #d6a85b);
    color: #17120d;
    text-decoration: none;
    font-weight: 700;
}
.community-composer-secondary-action {
    background: rgba(255, 255, 255, 0.05) !important;
    color: #E8DFD0 !important;
    border: 1px solid rgba(214, 168, 91, 0.14);
}
.community-feed-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
}
.community-feed-card-head {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 14px;
}
.community-feed-author {
    display: flex;
    gap: 12px;
    min-width: 0;
}
.community-feed-author-copy strong {
    display: block;
    color: #F0E9DF;
    font-size: 1rem;
}
.community-feed-author-copy span {
    display: block;
    margin-top: 4px;
    color: #8A7A66;
    font-size: 0.86rem;
}
.community-feed-head-actions {
    display: flex;
    align-items: center;
    gap: 10px;
}
.community-status-chip {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 34px;
    padding: 0 12px;
    border-radius: 999px;
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    line-height: 1;
    white-space: nowrap;
}
.community-status-chip-pending {
    background: rgba(214, 168, 91, 0.10);
    border: 1px solid rgba(214, 168, 91, 0.22);
    color: #F0D8AA;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.04);
}
.community-status-chip-rejected {
    background: rgba(224, 112, 96, 0.12);
    border: 1px solid rgba(224, 112, 96, 0.18);
    color: #F0B3A9;
}
.community-post-menu {
    position: relative;
}
.community-post-menu summary {
    list-style: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    width: 38px;
    height: 38px;
    justify-content: center;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.04);
    cursor: pointer;
}
.community-post-menu summary::-webkit-details-marker {
    display: none;
}
.community-post-menu summary span {
    width: 4px;
    height: 4px;
    border-radius: 999px;
    background: #D6A85B;
}
.community-post-menu-list {
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    min-width: 150px;
    padding: 8px;
    border-radius: 14px;
    background: rgba(29, 31, 34, 0.98);
    border: 1px solid rgba(214, 168, 91, 0.14);
    box-shadow: 0 16px 30px rgba(0, 0, 0, 0.22);
    z-index: 3;
}
.community-post-menu-list a, .community-post-menu-list button {
    width: 100%;
    display: block;
    padding: 10px 12px;
    border-radius: 10px;
    border: none;
    background: transparent;
    color: #F0E9DF;
    text-align: left;
    text-decoration: none;
    cursor: pointer;
}
.community-post-menu-list a:hover, .community-post-menu-list button:hover {
    background: rgba(255, 255, 255, 0.05);
}
.community-post-menu-list form {
    margin: 0;
}
.community-feed-copy {
    margin-top: 16px;
}
.community-feed-copy h3 {
    margin: 0 0 10px;
    color: #F0E9DF;
    font-size: 1.24rem;
    font-weight: 700;
}
.community-feed-copy p {
    margin: 0;
    color: #C4B8A8;
    line-height: 1.8;
    white-space: pre-wrap;
}
.community-feed-media {
    display: block;
    margin-top: 16px;
    overflow: hidden;
    aspect-ratio: 16 / 9;
    border-radius: 18px;
    border: 1px solid rgba(255, 255, 255, 0.06);
    background: rgba(18, 20, 23, 0.55);
    text-decoration: none;
}
.community-feed-media img {
    width: 100%;
    height: 100%;
    aspect-ratio: 16 / 9;
    object-fit: cover;
    display: block;
}
.community-feed-media-video {
    min-height: 180px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.community-feed-video-pill {
    padding: 12px 16px;
    border-radius: 999px;
    background: rgba(214, 168, 91, 0.12);
    border: 1px solid rgba(214, 168, 91, 0.18);
    color: #D6A85B;
    font-weight: 700;
}
.community-feed-stats-row {
    margin-top: 14px;
    display: flex;
    justify-content: space-between;
    gap: 12px;
    color: #9E8E79;
    font-size: 0.86rem;
}
.community-feed-actions {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px;
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px solid rgba(255, 255, 255, 0.05);
}
.community-feed-actions form {
    margin: 0;
}
.community-action-btn {
    width: 100%;
    min-height: 44px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.06);
    background: rgba(255, 255, 255, 0.03);
    color: #D6C9B4;
    text-decoration: none;
    font-weight: 700;
    cursor: pointer;
}
.community-action-btn.is-active {
    background: rgba(214, 168, 91, 0.12);
    border-color: rgba(214, 168, 91, 0.18);
    color: #EACB8E;
}
.community-empty-state {
    padding: 28px;
    text-align: center;
}
.community-empty-state h3 {
    margin: 0;
    color: #F0E9DF;
    font-size: 1.1rem;
}
.community-empty-state p {
    margin: 10px 0 0;
    color: #B8AB98;
    line-height: 1.7;
}
.community-empty-state a {
    display: inline-flex;
    margin-top: 16px;
    color: #D6A85B;
    text-decoration: none;
    font-weight: 700;
}
.community-pagination-link {
    display: flex;
    justify-content: center;
}
@media (max-width:768px) {
    .community-feed-page {
        padding: 18px 0 28px;
    }
    .community-feed-hero, .community-review-strip, .community-composer-card, .community-feed-card, .community-empty-state {
        padding: 18px;
    }
    .community-composer-card {
        flex-direction: column;
        align-items: stretch;
    }
    .community-feed-hero {
        align-items: flex-start;
        flex-direction: column;
    }
    .community-feed-card-head, .community-review-top {
        flex-direction: column;
    }
}
@media (max-width:560px) {
    .community-feed-title {
        font-size: 2rem;
    }
    .community-feed-subtitle {
        font-size: 0.95rem;
    }
    .community-feed-hero, .community-review-strip, .community-composer-card, .community-feed-card, .community-empty-state {
        border-radius: 18px;
    }
    .community-composer-main {
        align-items: flex-start;
    }
    .community-composer-trigger {
        min-height: 50px;
        padding: 0 14px;
        font-size: 0.92rem;
    }
    .community-composer-actions {
        display: grid;
        grid-template-columns: 1fr;
        gap: 10px;
        width: 100%;
    }
    .community-composer-actions a {
        width: 100%;
    }
    .community-feed-actions {
        grid-template-columns: 1fr;
    }
    .community-feed-stats-row, .community-review-meta {
        flex-direction: column;
        align-items: flex-start;
    }
    .community-review-actions {
        display: grid;
        grid-template-columns: 1fr;
    }
    .community-review-actions a, .community-review-actions form, .community-review-actions button {
        width: 100%;
    }
    .community-review-actions form {
        display: flex;
    }
    .community-feed-head-actions {
        width: 100%;
        justify-content: space-between;
    }
    .community-review-drawer {
        width: 100vw;
        padding: 20px 18px;
    }
}
</style>
</x-app-layout>
