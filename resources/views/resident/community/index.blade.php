<x-app-layout>
    @php
        $featuredPost = $posts->first();
        $totalComments = $posts->sum(fn ($post) => $post->comments->count());
    @endphp
    <div class="resident-page community-page">
        <section class="resident-page-hero">
            <div class="resident-page-hero-copy">
                <p class="resident-page-kicker">Resident Community Hub</p>
                <h1 class="resident-page-title">Community Board</h1>
                <p class="resident-page-subtitle">
                    Share updates, connect with neighbors, and follow conversations happening around your residence.
                </p>

                <div class="resident-hero-stat-row">
                    <div class="resident-hero-stat">
                        <span>Total Posts</span>
                        <strong>{{ $posts->total() }}</strong>
                    </div>
                    <div class="resident-hero-stat">
                        <span>Comments</span>
                        <strong>{{ $totalComments }}</strong>
                    </div>
                    <div class="resident-hero-stat">
                        <span>Community</span>
                        <strong>Resident-led</strong>
                    </div>
                </div>
            </div>

            <div class="resident-page-actions">
                <a href="{{ route('community.create') }}" class="resident-page-btn resident-page-btn-primary">Create Post</a>
            </div>
        </section>

        <section class="resident-page-panel">
            <div class="resident-page-panel-head">
                <div>
                    <h2>Resident Posts</h2>
                    <p>A live community feed for updates, questions, and shared neighborhood moments.</p>
                </div>
                <span class="resident-page-eyebrow">Community Feed</span>
            </div>

            <div class="resident-page-divider"></div>

            @if($featuredPost)
                <article class="community-featured-card">
                    <div class="community-featured-copy">
                        <div class="community-featured-label">Featured Conversation</div>
                        <h3>{{ $featuredPost->title }}</h3>
                        <p>{{ Str::limit($featuredPost->content, 190) }}</p>

                        <div class="community-featured-meta">
                            <span>{{ $featuredPost->user->name }}</span>
                            <span>{{ $featuredPost->created_at->diffForHumans() }}</span>
                            <span>{{ $featuredPost->comments->count() }} comments</span>
                        </div>
                    </div>

                    <a href="{{ route('community.show', $featuredPost) }}" class="community-featured-link">Open Discussion</a>
                </article>
            @endif

            <div class="community-feed">
                @forelse($posts as $post)
                    <article class="community-card">
                        <div class="community-card-head">
                            <div class="community-author">
                                <div class="community-avatar">{{ strtoupper(substr($post->user->name, 0, 1)) }}</div>
                                <div>
                                    <strong>{{ $post->user->name }}</strong>
                                    <span>{{ $post->created_at->diffForHumans() }}</span>
                                </div>
                            </div>

                            <span class="community-type">{{ str_replace('_', ' ', ucfirst($post->type)) }}</span>
                        </div>

                        <div class="community-copy">
                            <h3>{{ $post->title }}</h3>
                            <p>{{ $post->content }}</p>
                        </div>

                        @if($post->image_path)
                            <div class="community-media">
                                <img src="{{ Storage::url($post->image_path) }}" alt="{{ $post->title }}">
                            </div>
                        @endif

                        <div class="community-actions">
                            <div class="community-action-group">
                                <a href="{{ route('community.show', $post) }}">View Discussion</a>
                                <span>{{ $post->comments->count() }} Comments</span>
                            </div>
                            <div class="community-reaction-strip">
                                <span>Helpful</span>
                                <span>Neighbor Reply</span>
                                <span>Active Thread</span>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="resident-empty-state">
                        <h3>No posts yet</h3>
                        <p>Be the first resident to share an update with the community.</p>
                        <a href="{{ route('community.create') }}">Create a Post</a>
                    </div>
                @endforelse
            </div>

            <div class="community-pagination">
                {{ $posts->links() }}
            </div>
        </section>
    </div>

    <style>
        .resident-page {
            max-width: 1600px;
            margin: 0 auto;
            padding: 24px 16px 32px;
            display: flex;
            flex-direction: column;
            gap: 22px;
        }

        .resident-page-hero,
        .resident-page-panel {
            border: 1px solid rgba(214,168,91,0.14);
            box-shadow: 0 12px 24px rgba(0,0,0,0.14);
        }

        .resident-page-hero {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 20px;
            padding: 28px 30px;
            border-radius: 36px;
            background: linear-gradient(115deg, #1F2023 0%, #24262B 38%, #2C2C2F 62%, #3B3023 100%);
            box-shadow: 0 18px 50px rgba(0, 0, 0, 0.18);
        }

        .resident-page-hero-copy {
            max-width: 860px;
        }

        .resident-page-kicker {
            margin: 0 0 10px;
            color: #D2A04C;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.30em;
        }

        .resident-page-title {
            margin: 0;
            color: #F8F3EA;
            font-family: 'Playfair Display', serif;
            font-size: clamp(2.4rem, 4.6vw, 3.8rem);
            line-height: 1.05;
        }

        .resident-page-subtitle {
            margin: 12px 0 0;
            color: rgba(255,255,255,0.82);
            font-size: 1.02rem;
            line-height: 1.7;
            max-width: 760px;
        }

        .resident-page-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 22px;
            border-radius: 999px;
            text-decoration: none;
            font-size: 0.92rem;
            font-weight: 700;
            transition: transform 0.2s ease;
            background: linear-gradient(95deg, #b8842f, #d6a85b);
            color: #17120d;
        }

        .resident-page-btn:hover {
            transform: translateY(-1px);
        }

        .resident-hero-stat-row {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            margin-top: 22px;
        }

        .resident-hero-stat {
            min-width: 128px;
            padding: 12px 14px;
            border-radius: 16px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.07);
        }

        .resident-hero-stat span {
            display: block;
            color: #A89376;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.14em;
            font-weight: 700;
        }

        .resident-hero-stat strong {
            display: block;
            margin-top: 6px;
            color: #F0E9DF;
            font-size: 1.25rem;
            font-weight: 700;
        }

        .resident-page-panel {
            padding: 26px 28px;
            border-radius: 20px;
            background: rgba(42,44,48,0.78);
            backdrop-filter: blur(10px);
        }

        .resident-page-panel-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 16px;
        }

        .resident-page-panel-head h2 {
            margin: 0;
            color: #F0E9DF;
            font-size: 1.5rem;
            font-family: 'Playfair Display', serif;
        }

        .resident-page-panel-head p {
            margin: 4px 0 0;
            color: #8A7A66;
            font-size: 0.95rem;
        }

        .resident-page-eyebrow {
            color: #D6A85B;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.16em;
        }

        .resident-page-divider {
            height: 1px;
            background: linear-gradient(to right, rgba(214,168,91,0.3), rgba(214,168,91,0.05), transparent);
            margin-bottom: 18px;
        }

        .community-feed {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .community-featured-card {
            display: flex;
            justify-content: space-between;
            gap: 18px;
            align-items: flex-end;
            padding: 22px 24px;
            margin-bottom: 18px;
            border-radius: 18px;
            background:
                linear-gradient(135deg, rgba(214,168,91,0.10), rgba(255,255,255,0.03));
            border: 1px solid rgba(214,168,91,0.16);
        }

        .community-featured-copy {
            max-width: 760px;
        }

        .community-featured-label {
            color: #D6A85B;
            font-size: 0.72rem;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .community-featured-card h3 {
            margin: 0;
            color: #F0E9DF;
            font-size: 1.45rem;
            font-family: 'Playfair Display', serif;
        }

        .community-featured-card p {
            margin: 10px 0 0;
            color: #C4B6A3;
            line-height: 1.7;
        }

        .community-featured-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            margin-top: 14px;
            color: #9E8E79;
            font-size: 0.82rem;
        }

        .community-featured-link {
            white-space: nowrap;
            color: #F8F3EA;
            text-decoration: none;
            font-weight: 700;
            padding: 12px 18px;
            border-radius: 999px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(214,168,91,0.16);
        }

        .community-card {
            padding: 20px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: transform 0.18s ease, border-color 0.18s ease, box-shadow 0.18s ease;
        }

        .community-card:hover {
            transform: translateY(-2px);
            border-color: rgba(214,168,91,0.18);
            box-shadow: 0 16px 30px rgba(0,0,0,0.12);
        }

        .community-card-head,
        .community-actions {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: center;
            flex-wrap: wrap;
        }

        .community-author {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .community-avatar {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(214, 168, 91, 0.26), rgba(190,147,96,0.08));
            color: #F4DEB5;
            font-weight: 700;
        }

        .community-author strong,
        .community-copy h3 {
            display: block;
            color: #f0e9df;
        }

        .community-author span,
        .community-actions span {
            color: #b39a78;
            font-size: 0.82rem;
        }

        .community-type {
            padding: 6px 11px;
            border-radius: 999px;
            background: rgba(214, 168, 91, 0.10);
            color: #e6c895;
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .community-copy {
            margin-top: 18px;
        }

        .community-copy h3 {
            margin: 0;
            font-size: 1.08rem;
            font-weight: 700;
        }

        .community-copy p {
            margin: 10px 0 0;
            color: #B8AB98;
            line-height: 1.65;
            font-size: 0.92rem;
        }

        .community-media {
            margin-top: 18px;
        }

        .community-media img {
            width: 100%;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .community-actions {
            margin-top: 18px;
            padding-top: 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        .community-action-group,
        .community-reaction-strip {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
        }

        .community-reaction-strip span {
            padding: 6px 10px;
            border-radius: 999px;
            background: rgba(255,255,255,0.04);
            color: #A99884;
            font-size: 0.72rem;
            font-weight: 600;
        }

        .community-actions a,
        .resident-empty-state a {
            color: #d7b07a;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .resident-empty-state {
            padding: 34px 20px;
            border-radius: 16px;
            text-align: center;
            border: 1px solid rgba(255,255,255,0.05);
            background: rgba(255, 255, 255, 0.03);
        }

        .resident-empty-state h3 {
            margin: 0;
            color: #f0e9df;
            font-size: 1.3rem;
            font-family: 'Playfair Display', serif;
        }

        .resident-empty-state p {
            margin: 10px 0 18px;
            color: #B8AB98;
            font-size: 0.92rem;
        }

        .community-pagination {
            margin-top: 20px;
        }

        @media (max-width: 768px) {
            .resident-page {
                padding: 18px 0 28px;
            }

            .resident-page-hero,
            .resident-page-panel {
                padding: 22px;
            }

            .resident-page-hero {
                flex-direction: column;
                align-items: flex-start;
            }

            .community-featured-card,
            .community-actions {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</x-app-layout>
