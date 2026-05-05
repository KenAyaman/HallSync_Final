<x-app-layout>
<div class="admin-concern-page">
    <section class="admin-concern-hero">
        <div>
            <p class="admin-concern-kicker">Resident Support Desk</p>
            <h1 class="admin-concern-title">{{ $concern->subject }}</h1>
            <p class="admin-concern-subtitle">Review the report, adjust its status, and send a professional private response to the resident.</p>
        </div>
        <div class="admin-concern-hero-actions">
            <a href="{{ route('admin.concerns.index') }}" class="admin-concern-btn admin-concern-btn-secondary">Back to Concerns</a>
            <span class="admin-concern-status admin-concern-status-{{ $concern->status }}">{{ $concern->status_label }}</span>
        </div>
    </section>

    @if (session('success'))
        <div class="admin-concern-alert">{{ session('success') }}</div>
    @endif

    <section class="admin-concern-stack">
        <div class="admin-concern-card admin-concern-card-primary">
            <div class="admin-concern-section-head">
                <div>
                    <h2>Resident Report</h2>
                    <p>Review the full concern details before responding.</p>
                </div>
            </div>

            <div class="admin-detail-grid">
                <div class="admin-detail-item"><span>Resident</span><strong>{{ $concern->user->name }}</strong></div>
                <div class="admin-detail-item"><span>Category</span><strong>{{ ucfirst(str_replace('_', ' ', $concern->category)) }}</strong></div>
                <div class="admin-detail-item"><span>Submitted</span><strong>{{ $concern->created_at->format('M d, Y h:i A') }}</strong></div>
                <div class="admin-detail-item"><span>Location</span><strong>{{ $concern->location ?: 'Not specified' }}</strong></div>
                <div class="admin-detail-item"><span>Person Involved</span><strong>{{ $concern->involved_person ?: 'Not specified' }}</strong></div>
                <div class="admin-detail-item"><span>Incident Time</span><strong>{{ $concern->incident_at ? $concern->incident_at->format('M d, Y h:i A') : 'Not specified' }}</strong></div>
            </div>

            <div class="admin-detail-block">
                <span>Resident Statement</span>
                <p>{{ $concern->details }}</p>
            </div>

            @if($concern->admin_reply)
                <div class="admin-detail-block admin-detail-block-reply">
                    <span>Latest Admin Reply</span>
                    <p>{{ $concern->admin_reply }}</p>
                    <small>Responded by {{ $concern->handler->name ?? 'Admin' }}{{ $concern->replied_at ? ' • ' . $concern->replied_at->format('M d, Y h:i A') : '' }}</small>
                </div>
            @endif
        </div>

        <div class="admin-concern-card admin-concern-card-primary">
            <div class="admin-concern-section-head admin-form-head">
                <h2>Update Concern</h2>
                <p>Move the concern through review and send a reply when needed.</p>
            </div>

            <form method="POST" action="{{ route('admin.concerns.update', $concern) }}" class="admin-concern-form" data-prevent-double-submit data-submitting-text="Saving Reply...">
                @csrf
                @method('PATCH')

                <div>
                    <label class="admin-form-label" for="admin_reply">Reply to Resident</label>
                    <textarea id="admin_reply" name="admin_reply" rows="9" class="admin-form-input admin-form-textarea" placeholder="Write a private, professional reply for the resident.">{{ old('admin_reply', $concern->admin_reply) }}</textarea>
                </div>

                <button type="submit" class="admin-concern-btn admin-concern-btn-primary">Save Concern Update</button>
            </form>
        </div>
    </section>
</div>

<style>
.admin-concern-page { max-width: 1580px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px; color: #f0e9df; }
.admin-concern-hero, .admin-concern-card, .admin-concern-alert { border-radius: 28px; border: 1px solid rgba(214,168,91,0.16); box-shadow: 0 24px 48px rgba(0,0,0,0.20); }
.admin-concern-hero { display: flex; justify-content: space-between; gap: 24px; align-items: center; padding: 34px 38px; background: linear-gradient(135deg, rgba(20,16,13,0.96) 0%, rgba(30,23,18,0.90) 55%, rgba(25,19,15,0.96) 100%); }
.admin-concern-kicker { display: inline-flex; align-items: center; gap: 8px; margin: 0 0 8px; color: #d6a85b; font-size: 0.875rem; font-weight: 700; letter-spacing: 0.18em; line-height: 1.2; text-transform: uppercase; }
.admin-concern-kicker::before { content: ''; width: 6px; height: 6px; border-radius: 999px; background: #d6a85b; display: inline-block; flex-shrink: 0; }
.admin-concern-title { margin: 0; font-family: 'Playfair Display', serif; font-size: clamp(2.3rem, 4vw, 3.3rem); line-height: 1.06; }
.admin-concern-subtitle { margin: 8px 0 0; max-width: 760px; color: rgba(240,233,223,0.72); line-height: 1.65; }
.admin-concern-hero-actions { display: flex; gap: 12px; align-items: center; flex-wrap: wrap; }
.admin-concern-btn { display: inline-flex; align-items: center; justify-content: center; padding: 13px 22px; border-radius: 999px; text-decoration: none; font-weight: 700; border: 1px solid rgba(214,168,91,0.16); }
.admin-concern-btn-primary { background: linear-gradient(135deg, #c79745 0%, #d6a85b 100%); color: #1b150f; }
.admin-concern-btn-secondary { background: rgba(255,255,255,0.04); color: #e8e0d3; }
.admin-concern-status { padding: 10px 14px; border-radius: 999px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; font-size: 0.76rem; }
.admin-concern-status-submitted { background: rgba(214,168,91,0.16); color: #d6a85b; }
.admin-concern-status-in_review { background: rgba(103,138,196,0.16); color: #93afd8; }
.admin-concern-status-responded { background: rgba(90,138,90,0.16); color: #8bc18b; }
.admin-concern-status-closed { background: rgba(255,255,255,0.10); color: #d8d0c6; }
.admin-concern-alert { padding: 16px 20px; background: rgba(90,138,90,0.16); color: #d8edd8; }
.admin-concern-stack { display: flex; flex-direction: column; gap: 20px; }
.admin-concern-card { padding: 28px; background: rgba(36,33,31,0.82); backdrop-filter: blur(12px); display: flex; flex-direction: column; gap: 20px; }
.admin-concern-card-primary { background: rgba(42,44,48,0.82); }
.admin-concern-section-head h2 { margin: 0; font-size: 1.45rem; font-family: 'Playfair Display', serif; }
.admin-concern-section-head p { margin: 6px 0 0; color: #b8ab98; }
.admin-detail-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
.admin-detail-item, .admin-detail-block { padding: 18px 20px; border-radius: 20px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); }
.admin-detail-item span, .admin-detail-block span { display: block; color: #9f927f; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.12em; margin-bottom: 8px; }
.admin-detail-item strong { color: #f0e9df; font-size: 1rem; }
.admin-detail-block p { margin: 0; color: #ddd1c2; line-height: 1.8; white-space: pre-line; }
.admin-detail-block-reply { background: rgba(214,168,91,0.08); border-color: rgba(214,168,91,0.14); }
.admin-detail-block-reply small { display: block; margin-top: 12px; color: #b8ab98; }
.admin-concern-form { display: flex; flex-direction: column; gap: 18px; }
.admin-form-label { display: block; margin-bottom: 10px; color: #d7c8b4; font-size: 0.88rem; font-weight: 700; }
.admin-form-input { width: 100%; padding: 14px 16px; border-radius: 16px; border: 1px solid rgba(214,168,91,0.14); background: rgba(24,22,20,0.88); color: #f8f3ea; font-size: 0.95rem; }
.admin-form-input:focus { outline: none; border-color: rgba(214,168,91,0.34); box-shadow: 0 0 0 4px rgba(214,168,91,0.08); }
.admin-form-textarea { min-height: 220px; resize: vertical; line-height: 1.7; }
@media (max-width: 980px) { .admin-concern-hero { display: flex; flex-direction: column; padding: 24px; align-items: flex-start; } .admin-detail-grid { grid-template-columns: 1fr; } }
@media (max-width: 560px) { .admin-concern-page { gap: 18px; } .admin-concern-hero, .admin-concern-card, .admin-concern-alert { border-radius: 22px; } .admin-concern-title { font-size: 2rem; } .admin-concern-subtitle { font-size: 0.94rem; } .admin-concern-btn, .admin-concern-hero-actions { width: 100%; } .admin-concern-card { padding: 20px; } .admin-detail-item, .admin-detail-block { padding: 16px; } }
</style>
</x-app-layout>
