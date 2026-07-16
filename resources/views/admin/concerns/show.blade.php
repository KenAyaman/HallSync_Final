<x-app-layout>
<div class="admin-concern-record admin-detail-page">
    <x-admin-breadcrumb :crumbs="[
        ['label' => 'Concern Management', 'route' => 'admin.concerns.index'],
        ['label' => $concern->subject],
    ]" />

    <section class="admin-concern-record-hero admin-detail-hero">
        <div>
            <p>Admin Control Panel</p>
            <h1>
                {{ $concern->subject }}
                @if($concern->is_anonymous)
                    <span class="concern-anonymous-badge">
                        Anonymous Submission
                    </span>
                @endif
            </h1>
            <span>{{ $concern->concern_id }} &middot; {{ $concern->category_label }} &middot; Submitted {{ $concern->created_at->format('M d, Y') }}</span>
        </div>
        <a href="{{ route('admin.concerns.index') }}">Back to Concerns</a>
    </section>

    <div class="admin-concern-record-layout">
        <main class="admin-concern-record-main">
            <section class="admin-concern-record-panel admin-detail-panel">
                <div class="admin-concern-record-head">
                    <div><p>Complaint Record</p><h2>Concern Details</h2></div>
                    <span class="admin-concern-status-chip admin-concern-status-chip-{{ $concern->status }}">
                        {{ $concern->status_label }}
                    </span>
                </div>
                <div class="admin-concern-record-facts">
                    <div><small>Resident</small><strong>{{ $concern->is_anonymous ? 'Anonymous Resident' : $concern->user->name }}</strong></div>
                    <div><small>Location</small><strong>{{ $concern->location }}</strong></div>
                    <div><small>Category</small><strong>{{ $concern->category_label }}</strong></div>
                    <div><small>Submitted</small><strong>{{ $concern->created_at->format('M d, Y h:i A') }}</strong></div>
                </div>
                <div class="admin-concern-record-message">
                    <small>Resident Complaint</small>
                    <p>{{ $concern->details }}</p>
                </div>
            </section>

            @if($concern->admin_reply)
                <section class="admin-concern-record-panel admin-detail-panel">
                    <div class="admin-concern-record-head"><div><p>Administration</p><h2>Latest Reply</h2></div></div>
                    <div class="admin-concern-record-message admin-concern-record-reply">
                        <p>{{ $concern->admin_reply }}</p>
                        <small>Sent {{ optional($concern->replied_at)->format('M d, Y h:i A') }}</small>
                    </div>
                </section>
            @endif
        </main>

        <aside class="admin-concern-record-panel admin-detail-panel">
            <div class="admin-concern-record-head">
                <div><p>Respond</p><h2>{{ $concern->admin_reply ? 'Send Another Reply' : 'Reply to Resident' }}</h2></div>
            </div>
            <form method="POST"
                  action="{{ route('admin.concerns.update', $concern) }}"
                  class="admin-concern-reply-form"
                  data-prevent-double-submit
                  data-submitting-text="Sending Reply...">
                @csrf
                @method('PATCH')
                <label>
                    <span>Message</span>
                    <textarea name="admin_reply" rows="7" required placeholder="Write a clear reply for the resident.">{{ old('admin_reply') }}</textarea>
                </label>
                <button type="submit">Send Reply</button>
            </form>
        </aside>
    </div>
</div>

<style>
.concern-anonymous-badge {
    display: inline-block;
    font-size: .70rem;
    font-weight: 700;
    letter-spacing: .07em;
    text-transform: uppercase;
    background: #f3ede5;
    color: #786b60;
    border: 1px solid #d4c8b8;
    border-radius: 4px;
    padding: 2px 8px;
    margin-left: 8px;
    vertical-align: middle;
}
.admin-concern-record {
    max-width: 1580px;
    margin: 0 auto;
    display: grid;
    gap: 16px
}
.admin-concern-record-hero {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 18px
}
.admin-concern-record-hero p, .admin-concern-record-head p {
    margin: 0 0 6px;
    color: #c06f00;
    font-size: .68rem;
    font-weight: 800;
    letter-spacing: .16em;
    text-transform: uppercase
}
.admin-concern-record-hero h1, .admin-concern-record-head h2 {
    margin: 0;
    color: #342a23;
    font-family: 'Playfair Display', serif;
    font-weight: 400
}
.admin-concern-record-hero h1 {
    font-size: clamp(2rem, 3.4vw, 3rem)
}
.admin-concern-record-hero span {
    display: block;
    margin-top: 8px;
    color: #786b60;
    font-size: .86rem
}
.admin-concern-record-hero a, .admin-concern-reply-form button {
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
.admin-concern-status-chip {
    padding: 6px 10px;
    border-radius: 999px;
    font-size: .68rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .08em
}
.admin-concern-status-chip-submitted, .admin-concern-status-chip-draft {
    border: 1px solid #d2ae7b;
    background: #f3e3cc;
    color: #68400f
}
.admin-concern-status-chip-under_review, .admin-concern-status-chip-investigation_ongoing {
    border: 1px solid #a8bedf;
    background: #e1eafa;
    color: #345984
}
.admin-concern-status-chip-resolved {
    border: 1px solid #9fc6a8;
    background: #deeee1;
    color: #356140
}
.admin-concern-status-chip-closed {
    border: 1px solid #c8bdae;
    background: #eee9e2;
    color: #5e554a
}
.admin-concern-status-chip-reopened {
    border: 1px solid #e0b070;
    background: #fdf0dd;
    color: #7c4e0a
}
.admin-concern-status-chip-rejected {
    border: 1px solid #dda29d;
    background: #f7dfdc;
    color: #8f342e
}
.admin-concern-record-layout {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 340px;
    gap: 16px;
    align-items: start
}
.admin-concern-record-main {
    display: grid;
    gap: 16px
}
.admin-concern-record-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 14px
}
.admin-concern-record-head h2 {
    font-size: 1.45rem
}
.admin-concern-record-head>span {
    padding: 6px 9px;
    border: 1px solid #ead6b8;
    border-radius: 999px;
    background: #fbf3e4;
    color: #8b5b1d;
    font-size: .68rem;
    font-weight: 800;
    text-transform: uppercase
}
.admin-concern-record-facts {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    border-top: 1px solid #e3d8ca;
    border-left: 1px solid #e3d8ca
}
.admin-concern-record-facts div, .admin-concern-record-message {
    padding: 15px 16px;
    border-right: 1px solid #e3d8ca;
    border-bottom: 1px solid #e3d8ca
}
.admin-concern-record-message {
    border-left: 1px solid #e3d8ca
}
.admin-concern-record-facts small, .admin-concern-record-message small, .admin-concern-reply-form span {
    display: block;
    color: #7a6c5f;
    font-size: .72rem;
    font-weight: 700;
    letter-spacing: .1em;
    text-transform: uppercase
}
.admin-concern-record-facts strong {
    display: block;
    margin-top: 7px;
    color: #2c2419;
    font-size: .92rem;
    font-weight: 600
}
.admin-concern-record-message p {
    margin: 0;
    color: #63574e;
    font-size: .94rem;
    line-height: 1.75;
    white-space: pre-line
}
.admin-concern-record-reply {
    border: 1px solid #d7eadb;
    background: #f5faf6
}
.admin-concern-record-reply small {
    margin-top: 10px;
    color: #63816a
}
.admin-concern-reply-form {
    display: grid;
    gap: 12px
}
.admin-concern-reply-form span {
    margin-bottom: 6px
}
.admin-concern-reply-form textarea {
    width: 100%;
    padding: 11px;
    border: 1px solid #e3d8ca;
    border-radius: 8px;
    background: #fffdf9;
    color: #342a23;
    font-size: .84rem;
    line-height: 1.6;
    resize: vertical
}
.admin-concern-reply-form button {
    width: 100%
}
@media(max-width:900px) {
    .admin-concern-record-layout {
        grid-template-columns: 1fr
    }
}
@media(max-width:620px) {
    .admin-concern-record-hero {
        align-items: flex-start;
        flex-direction: column
    }
    .admin-concern-record-facts {
        grid-template-columns: 1fr
    }
}
</style>
</x-app-layout>
