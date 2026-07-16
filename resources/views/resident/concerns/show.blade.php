<x-app-layout>
<div class="resident-concern-record">
    <div class="resident-concern-record-topbar">
        <a href="{{ route('concerns.index') }}" class="resident-back-link resident-create-back">← Back</a>
    </div>

    <section class="resident-concern-hero resident-hero-lamp-glow">
        <div>
            <p>Private Concern</p>
            <h1>{{ $concern->subject }}</h1>
            <span>{{ $concern->concern_id }} &middot; {{ $concern->category_label }} &middot; Submitted {{ $concern->created_at->format('M d, Y') }}</span>
        </div>
    </section>

    <section class="resident-concern-panel">
        <div class="resident-concern-head">
            <div><p>Your Complaint</p><h2>Concern Details</h2></div>
            <span>{{ $concern->admin_reply ? 'Replied' : 'Awaiting Reply' }}</span>
        </div>
        <div class="resident-concern-facts">
            <div><small>Category</small><strong>{{ $concern->category_label }}</strong></div>
            <div><small>Location</small><strong>{{ $concern->location }}</strong></div>
            <div><small>Status</small><strong>{{ $concern->status_label }}</strong></div>
            <div>
                <small>Expected Response</small>
                @if($concern->due_at)
                    @php
                        $isOverdue = $concern->due_at->isPast() && !in_array($concern->status, ['resolved', 'closed']);
                        $dueLabel  = $isOverdue
                            ? 'Overdue by ' . $concern->due_at->diffForHumans(['parts' => 1, 'short' => true])
                            : 'By ' . $concern->due_at->format('M d, Y');
                    @endphp
                    <strong class="{{ $isOverdue ? 'resident-concern-overdue' : '' }}">{{ $dueLabel }}</strong>
                @else
                    <strong>Pending assignment</strong>
                @endif
            </div>
        </div>
        <div class="resident-concern-message"><small>Complaint</small><p>{{ $concern->details }}</p></div>
    </section>

    <section class="resident-concern-panel">
        <div class="resident-concern-head"><div><p>Administration</p><h2>Reply</h2></div></div>
        @if($concern->admin_reply)
            <div class="resident-concern-message resident-concern-reply">
                <p>{{ $concern->admin_reply }}</p>
                <small>Sent {{ optional($concern->replied_at)->format('M d, Y h:i A') }}</small>
            </div>
        @else
            <div class="resident-concern-waiting-block">
                <p class="resident-concern-waiting">Administration has received your complaint. A reply will appear here once it has been reviewed.</p>
                @if($concern->due_at && !$concern->due_at->isPast())
                    <p class="resident-concern-sla-note">Expected response by {{ $concern->due_at->format('l, M d, Y') }} &mdash; {{ $concern->due_at->diffForHumans() }}.</p>
                @endif
            </div>
        @endif
    </section>
</div>

<style>
.resident-concern-record {
    max-width: 1080px;
    margin: 0 auto;
    display: grid;
    gap: 18px;
    color: #f0e9df
}
.resident-concern-record-topbar {
    display: flex;
    align-items: center;
    justify-content: flex-start
}
.resident-concern-hero, .resident-concern-panel {
    border: 1px solid rgba(214, 168, 91, .14);
    border-radius: 20px
}
.resident-concern-hero {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 18px;
    padding: 25px 27px;
    background: linear-gradient(115deg, #1f2023, #24262b 42%, #3b3023)
}
.resident-concern-hero p, .resident-concern-head p {
    margin: 0 0 7px;
    color: #d6a85b;
    font-size: .7rem;
    font-weight: 800;
    letter-spacing: .18em;
    text-transform: uppercase
}
.resident-concern-hero h1, .resident-concern-head h2 {
    margin: 0;
    font-family: 'Playfair Display', serif
}
.resident-concern-hero h1 {
    font-size: clamp(2rem, 4vw, 3rem)
}
.resident-concern-hero span {
    display: block;
    margin-top: 8px;
    color: #b8ab98;
    font-size: .86rem
}
.resident-concern-panel {
    padding: 19px;
    background: rgba(42, 44, 48, .86)
}
.resident-concern-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 14px
}
.resident-concern-head h2 {
    font-size: 1.4rem
}
.resident-concern-head>span {
    padding: 6px 9px;
    border-radius: 999px;
    background: rgba(214, 168, 91, .14);
    color: #e2b45f;
    font-size: .68rem;
    font-weight: 800;
    text-transform: uppercase
}
.resident-concern-facts {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    border-top: 1px solid rgba(255, 255, 255, .06);
    border-left: 1px solid rgba(255, 255, 255, .06)
}
.resident-concern-facts div, .resident-concern-message {
    padding: 13px 14px;
    border-right: 1px solid rgba(255, 255, 255, .06);
    border-bottom: 1px solid rgba(255, 255, 255, .06)
}
.resident-concern-message {
    border-left: 1px solid rgba(255, 255, 255, .06)
}
.resident-concern-facts small, .resident-concern-message small {
    display: block;
    color: #9f927f;
    font-size: .68rem;
    font-weight: 700;
    letter-spacing: .09em;
    text-transform: uppercase
}
.resident-concern-facts strong {
    display: block;
    margin-top: 6px;
    color: #eee3d4;
    font-size: .86rem
}
.resident-concern-message p, .resident-concern-waiting {
    margin: 0;
    color: #cfc4b5;
    font-size: .88rem;
    line-height: 1.7;
    white-space: pre-line
}
.resident-concern-reply {
    border-color: rgba(90, 138, 90, .24);
    background: rgba(90, 138, 90, .08)
}
.resident-concern-reply small {
    margin-top: 10px;
    color: #9fc69f
}
.resident-concern-overdue {
    color: #f0a195!important
}
.resident-concern-waiting-block {
    display: grid;
    gap: 6px
}
.resident-concern-sla-note {
    margin: 0;
    color: #9f927f;
    font-size: .78rem;
    font-style: italic
}
@media(max-width:700px) {
    .resident-concern-hero {
        align-items: flex-start;
        flex-direction: column
    }
    .resident-concern-facts {
        grid-template-columns: 1fr
    }
}
</style>
</x-app-layout>
