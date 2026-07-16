<x-app-layout>
<div class="admin-community-detail admin-detail-page">
    <section class="admin-community-detail-hero admin-detail-hero">
        <div>
            <p class="admin-community-detail-kicker">Community Moderation</p>
            <h1>{{ $post->title }}</h1>
            <p>Review the resident submission, publishing status, and discussion history from one clear record.</p>
        </div>
        <div class="admin-community-detail-hero-actions">
            <a href="{{ route('admin.community') }}">Back to Community</a>
            <span class="admin-community-detail-status admin-community-detail-status-{{ $post->status }}">{{ ucfirst($post->status) }}</span>
        </div>
    </section>

    <div class="admin-community-detail-layout">
        <main class="admin-community-detail-main">
            <section class="admin-community-detail-panel admin-detail-panel">
                <div class="admin-community-detail-head">
                    <div>
                        <h2>Post Details</h2>
                        <p>The complete resident-facing submission.</p>
                    </div>
                </div>
                <div class="admin-community-detail-facts">
                    <div><span>Submitted By</span><strong>{{ $post->user->name ?? 'Resident' }}</strong></div>
                    <div><span>Category</span><strong>{{ str_replace('_', ' ', ucfirst($post->type)) }}</strong></div>
                    <div><span>Submitted</span><strong>{{ $post->created_at->format('M d, Y h:i A') }}</strong></div>
                    <div><span>Last Updated</span><strong>{{ $post->updated_at->format('M d, Y h:i A') }}</strong></div>
                </div>
                <div class="admin-community-detail-copy">
                    <span>Post Content</span>
                    <p>{{ $post->content }}</p>
                </div>

                @if($post->image_path)
                    <img src="{{ $post->image_url }}" alt="{{ $post->title }}" class="admin-community-detail-media">
                @endif
                @if($post->video_path)
                    <video controls class="admin-community-detail-media"><source src="{{ $post->video_url }}"></video>
                @endif
            </section>

            <section class="admin-community-detail-panel admin-detail-panel">
                <div class="admin-community-detail-head">
                    <div>
                        <h2>Discussion History</h2>
                        <p>{{ $post->comments->count() }} comment{{ $post->comments->count() === 1 ? '' : 's' }} recorded on this post.</p>
                    </div>
                </div>
                <div class="admin-community-detail-comments">
                    @forelse($post->comments as $comment)
                        <article>
                            <strong>{{ $comment->user->name ?? 'Resident' }}</strong>
                            <small>{{ $comment->created_at->format('M d, Y h:i A') }}</small>
                            <p>{{ $comment->content }}</p>
                        </article>
                    @empty
                        <p class="admin-community-detail-empty">No comments have been recorded.</p>
                    @endforelse
                </div>
            </section>
        </main>

    </div>
</div>

<style>
.admin-community-detail {
    max-width: 1580px;
    margin: 0 auto;
    display: grid;
    gap: 18px
}
.admin-community-detail-hero {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 18px
}
.admin-community-detail-kicker {
    margin: 0 0 7px;
    color: #b47721;
    font-size: .68rem;
    font-weight: 800;
    letter-spacing: .16em;
    text-transform: uppercase
}
.admin-community-detail-hero h1 {
    margin: 0;
    color: #342a23;
    font: 400 clamp(2rem, 3.4vw, 3rem)/1.05 'Playfair Display', serif
}
.admin-community-detail-hero p, .admin-community-detail-head p {
    margin: 7px 0 0;
    color: #786b60;
    font-size: .84rem;
    line-height: 1.55
}
.admin-community-detail-hero-actions {
    display: flex;
    align-items: center;
    gap: 9px;
    flex-wrap: wrap
}
.admin-community-detail-hero-actions a, .admin-community-detail-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 9px 12px;
    border: 1px solid #ead6b8;
    border-radius: 8px;
    background: #fbf3e4;
    color: #8b5b1d;
    font-size: .76rem;
    font-weight: 700;
    text-decoration: none;
    cursor: pointer
}
.admin-community-detail-status {
    padding: 8px 10px;
    border: 1px solid #ead6b8;
    border-radius: 8px;
    background: #fbf3e4;
    color: #8b5b1d;
    font-size: .68rem;
    font-weight: 800;
    text-transform: uppercase
}
.admin-community-detail-layout {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 330px;
    gap: 16px;
    align-items: start
}
.admin-community-detail-main, .admin-community-detail-side {
    display: grid;
    gap: 16px
}
.admin-community-detail-head h2 {
    margin: 0;
    color: #342a23;
    font: 400 1.4rem/1.15 'Playfair Display', serif
}
.admin-community-detail-facts {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 9px;
    margin-top: 14px
}
.admin-community-detail-facts div, .admin-community-detail-copy, .admin-community-detail-comments article, .admin-community-detail-note {
    padding: 12px;
    border: 1px solid #eee4d7;
    border-radius: 9px;
    background: #fbf8f3
}
.admin-community-detail-copy {
    margin-top: 9px
}
.admin-community-detail-facts span, .admin-community-detail-copy span, .admin-community-detail-note span, .admin-community-detail-form label span {
    display: block;
    margin-bottom: 5px;
    color: #8b7d70;
    font-size: .68rem;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase
}
.admin-community-detail-facts strong, .admin-community-detail-note strong, .admin-community-detail-comments strong {
    color: #342a23;
    font-size: .84rem
}
.admin-community-detail-copy p, .admin-community-detail-note p, .admin-community-detail-comments p, .admin-community-detail-empty {
    margin: 0;
    color: #786b60;
    font-size: .82rem;
    line-height: 1.65;
    white-space: pre-line
}
.admin-community-detail-media {
    width: 100%;
    margin-top: 10px;
    border: 1px solid #eee4d7;
    border-radius: 9px
}
.admin-community-detail-comments {
    display: grid;
    gap: 8px;
    margin-top: 14px
}
.admin-community-detail-comments small {
    display: block;
    margin-top: 4px;
    color: #9b8d81;
    font-size: .68rem
}
.admin-community-detail-comments p {
    margin-top: 7px
}
.admin-community-detail-form {
    display: grid;
    gap: 8px;
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px solid #eee4d7
}
.admin-community-detail-form textarea {
    width: 100%;
    padding: 10px 11px;
    border: 1px solid #e3d8ca;
    border-radius: 8px;
    background: #fffdf9;
    color: #342a23;
    font-size: .8rem;
    resize: vertical
}
.admin-community-detail-btn-danger {
    border-color: #ecc9c5;
    background: #fcf0ef;
    color: #a3423b
}
@media(max-width:900px) {
    .admin-community-detail-layout {
        grid-template-columns: 1fr
    }
}
@media(max-width:620px) {
    .admin-community-detail-hero {
        align-items: flex-start;
        flex-direction: column
    }
    .admin-community-detail-facts {
        grid-template-columns: 1fr
    }
}
.admin-community-detail-layout {
    grid-template-columns: 1fr
}
</style>
</x-app-layout>
