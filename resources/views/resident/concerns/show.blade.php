<x-app-layout>
<div class="concern-page">
    <section class="concern-hero">
        <div>
            <p class="concern-kicker">Resident Community Support</p>
            <h1 class="concern-title">{{ ucfirst(str_replace('_', ' ', $concern->category)) }}</h1>
            <p class="concern-subtitle">Private conversation between you and administration regarding this concern.</p>
        </div>
        <div class="concern-hero-actions">
            <a href="{{ route('concerns.index') }}" class="concern-btn concern-btn-secondary concern-btn-back">Back to My Reports</a>
            <span class="concern-status concern-status-{{ $concern->status }}">{{ $concern->status_label }}</span>
        </div>
    </section>

    @if (session('success'))
        <div class="concern-alert">{{ session('success') }}</div>
    @endif

    <section class="concern-card">
        <div class="detail-grid">
            <div class="detail-item"><span>Category</span><strong>{{ ucfirst(str_replace('_', ' ', $concern->category)) }}</strong></div>
            <div class="detail-item"><span>Submitted</span><strong>{{ $concern->created_at->format('M d, Y h:i A') }}</strong></div>
            <div class="detail-item"><span>Location</span><strong>{{ $concern->location ?: 'Not specified' }}</strong></div>
            <div class="detail-item"><span>Person Involved</span><strong>{{ $concern->involved_person ?: 'Not specified' }}</strong></div>
        </div>

        <div class="detail-block">
            <span>Description</span>
            <p>{{ $concern->details }}</p>
        </div>

        @if($concern->admin_reply)
            <div class="reply-block">
                <div class="reply-head">
                    <h2>Administration Response</h2>
                    <span>{{ optional($concern->replied_at)->format('M d, Y h:i A') }}</span>
                </div>
                <p>{{ $concern->admin_reply }}</p>
            </div>
        @else
            <div class="reply-block reply-block-empty">
                <h2>No admin reply yet</h2>
                <p>Your report has been submitted. Administration will review it privately and reply here.</p>
            </div>
        @endif
    </section>
</div>

<style>
.concern-page { max-width: 980px; margin: 0 auto; display: flex; flex-direction: column; gap: 18px; color: #f0e9df; }
.concern-hero, .concern-card, .concern-alert { border-radius: 22px; border: 1px solid rgba(214,168,91,0.14); box-shadow: 0 12px 24px rgba(0,0,0,0.14); }
.concern-hero { display: flex; justify-content: space-between; gap: 24px; align-items: center; padding: 28px 30px; background: linear-gradient(115deg, #1F2023 0%, #24262B 38%, #2C2C2F 62%, #3B3023 100%); }
.concern-kicker { margin: 0 0 10px; color: #d6a85b; font-size: 0.82rem; font-weight: 700; letter-spacing: 0.18em; text-transform: uppercase; }
.concern-title { margin: 0; font-family: 'Playfair Display', serif; font-size: clamp(2rem, 4vw, 3rem); line-height: 1.08; }
.concern-subtitle { margin: 12px 0 0; max-width: 720px; color: rgba(240,233,223,0.74); line-height: 1.7; }
.concern-hero-actions { display: flex; gap: 12px; flex-wrap: wrap; align-items: center; }
.concern-btn { display: inline-flex; align-items: center; justify-content: center; min-height: 48px; padding: 0 22px; border-radius: 999px; text-decoration: none; font-weight: 700; font-size: 0.9rem; border: 1px solid rgba(214,168,91,0.18); background: linear-gradient(180deg, rgba(42,44,48,0.92) 0%, rgba(33,35,38,0.92) 100%); color: #f0e9df; box-shadow: 0 12px 24px rgba(0,0,0,0.14); transition: transform 0.2s ease, box-shadow 0.2s ease; }
.concern-btn:hover { transform: translateY(-1px); box-shadow: 0 14px 28px rgba(0,0,0,0.18); }
.concern-btn-back { min-width: 188px; }
.concern-status { padding: 10px 14px; border-radius: 999px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; font-size: 0.76rem; }
.concern-status-submitted { background: rgba(214,168,91,0.16); color: #d6a85b; }
.concern-status-in_review { background: rgba(103,138,196,0.16); color: #93afd8; }
.concern-status-responded { background: rgba(90,138,90,0.16); color: #8bc18b; }
.concern-status-closed { background: rgba(255,255,255,0.10); color: #d8d0c6; }
.concern-alert { padding: 16px 20px; background: rgba(90,138,90,0.16); color: #d8edd8; }
.concern-card { padding: 20px 22px; background: rgba(42,44,48,0.82); backdrop-filter: blur(10px); display: flex; flex-direction: column; gap: 22px; }
.detail-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
.detail-item { padding: 16px 18px; border-radius: 18px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); }
.detail-item span, .detail-block span { display: block; color: #9f927f; font-size: 0.78rem; letter-spacing: 0.12em; text-transform: uppercase; margin-bottom: 8px; }
.detail-item strong { font-size: 1rem; color: #f0e9df; }
.detail-block { padding: 20px 22px; border-radius: 22px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); }
.detail-block p, .reply-block p { margin: 0; color: #ddd1c2; line-height: 1.8; white-space: pre-line; }
.reply-block { padding: 22px; border-radius: 22px; background: rgba(214,168,91,0.08); border: 1px solid rgba(214,168,91,0.14); }
.reply-head { display: flex; justify-content: space-between; gap: 12px; align-items: center; margin-bottom: 12px; }
.reply-head h2, .reply-block-empty h2 { margin: 0; font-size: 1.25rem; font-family: 'Playfair Display', serif; }
.reply-head span { color: #b8ab98; font-size: 0.88rem; }
.reply-block-empty { background: rgba(255,255,255,0.03); border-color: rgba(255,255,255,0.05); }
.reply-block-empty p { color: #b8ab98; margin-top: 10px; }
@media (max-width: 900px) { .concern-hero { flex-direction: column; align-items: flex-start; padding: 24px; } .detail-grid { grid-template-columns: 1fr; } .reply-head { flex-direction: column; align-items: flex-start; } }
</style>
</x-app-layout>
