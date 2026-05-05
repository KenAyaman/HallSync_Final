<x-app-layout>
@php
    $prefillCategory = old('category', $prefillCategory ?? 'other');
    $prefillLocation = old('location', $prefillLocation ?? '');
    $prefillInvolvedPerson = old('involved_person', $prefillInvolvedPerson ?? '');
    $prefillDetails = old('details', $prefillDetails ?? '');
@endphp
<div class="concern-page">
    <section class="concern-hero">
        <div>
            <p class="concern-kicker">Resident Community Support</p>
            <h1 class="concern-title">Report a Concern</h1>
            <p class="concern-subtitle">Send a private concern to administration about roommate conflicts, noise, safety, shared spaces, or policy issues.</p>
        </div>
        <div class="concern-hero-actions">
            <a href="{{ route('concerns.index') }}" class="concern-btn concern-btn-secondary concern-btn-back">Back to My Reports</a>
            <a href="{{ route('community.index') }}" class="concern-btn concern-btn-secondary">Back to Community</a>
        </div>
    </section>

    @if ($errors->any())
        <div class="concern-alert concern-alert-error">
            <strong>Please review the concern form.</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(!empty($contextTitle))
        <div class="concern-alert concern-alert-context">
            <strong>Follow-up request</strong>
            This report will reference the {{ $contextType ?: 'item' }}: "{{ $contextTitle }}".
        </div>
    @endif

    <section class="concern-card">
        <div class="concern-card-head">
            <div>
                <h2>Concern Details</h2>
                <p>Keep the report factual, specific, and clear so management can review it properly.</p>
            </div>
            <span class="concern-badge">Private to Admin</span>
        </div>

        <form method="POST" action="{{ route('concerns.store') }}" class="concern-form" data-prevent-double-submit data-submitting-text="Submitting Concern...">
            @csrf

            <div class="concern-grid">
                <div>
                    <label class="concern-label" for="category">Concern Type</label>
                    <select id="category" name="category" class="concern-input" required>
                        <option value="">Select concern type</option>
                        <option value="roommate" @selected($prefillCategory === 'roommate')>Roommate Conflict</option>
                        <option value="noise" @selected($prefillCategory === 'noise')>Noise or Disturbance</option>
                        <option value="cleanliness" @selected($prefillCategory === 'cleanliness')>Cleanliness or Hygiene</option>
                        <option value="safety" @selected($prefillCategory === 'safety')>Safety Concern</option>
                        <option value="policy" @selected($prefillCategory === 'policy')>Policy Violation</option>
                        <option value="shared_space" @selected($prefillCategory === 'shared_space')>Shared Space Issue</option>
                        <option value="other" @selected($prefillCategory === 'other')>Other</option>
                    </select>
                </div>

                <div>
                    <label class="concern-label" for="involved_person">Person Involved (Optional)</label>
                    <input id="involved_person" type="text" name="involved_person" class="concern-input" value="{{ $prefillInvolvedPerson }}" placeholder="Optional name, roommate, or resident">
                </div>

                <div>
                    <label class="concern-label" for="location">Location</label>
                    <input id="location" type="text" name="location" class="concern-input" value="{{ $prefillLocation }}" placeholder="Room, hallway, study room, or dorm area">
                </div>
            </div>

            <div>
                <label class="concern-label" for="details">Full Description</label>
                <textarea id="details" name="details" rows="8" class="concern-input concern-textarea" placeholder="Describe what happened, when it happened, and any details that would help administration understand the situation." required>{{ $prefillDetails }}</textarea>
            </div>

            <div class="concern-form-actions">
                <button type="submit" class="concern-btn concern-btn-primary">Submit Concern</button>
                <a href="{{ route('concerns.index') }}" class="concern-btn concern-btn-secondary concern-btn-back">View Reports</a>
            </div>
        </form>
    </section>
</div>

<style>
.concern-page { max-width: 980px; margin: 0 auto; display: flex; flex-direction: column; gap: 18px; color: #f0e9df; }
.concern-hero, .concern-card, .concern-alert { border-radius: 22px; border: 1px solid rgba(214,168,91,0.14); box-shadow: 0 12px 24px rgba(0,0,0,0.14); }
.concern-hero { display: flex; justify-content: space-between; gap: 24px; align-items: center; padding: 28px 30px; background: linear-gradient(115deg, #1F2023 0%, #24262B 38%, #2C2C2F 62%, #3B3023 100%); }
.concern-kicker { margin: 0 0 10px; color: #d6a85b; font-size: 0.82rem; font-weight: 700; letter-spacing: 0.18em; text-transform: uppercase; }
.concern-title { margin: 0; font-family: 'Playfair Display', serif; font-size: clamp(2.2rem, 4vw, 3.3rem); line-height: 1.04; }
.concern-subtitle { margin: 12px 0 0; max-width: 760px; color: rgba(240,233,223,0.74); font-size: 1rem; line-height: 1.7; }
.concern-hero-actions, .concern-form-actions { display: flex; gap: 12px; flex-wrap: wrap; }
.concern-hero-actions { align-items: center; justify-content: flex-end; }
.concern-btn { display: inline-flex; align-items: center; justify-content: center; padding: 13px 22px; border-radius: 999px; text-decoration: none; font-weight: 700; border: 1px solid rgba(214,168,91,0.16); white-space: nowrap; }
.concern-btn-primary { background: linear-gradient(135deg, #c79745 0%, #d6a85b 100%); color: #1b150f; }
.concern-btn-secondary { background: rgba(255,255,255,0.04); color: #e8e0d3; }
.concern-btn-back { min-width: 188px; }
.concern-alert { padding: 18px 22px; background: rgba(224,112,96,0.12); color: #f4d0c9; }
.concern-alert-context { background: rgba(214,168,91,0.12); color: #f0e9df; }
.concern-alert ul { margin: 10px 0 0; padding-left: 18px; }
.concern-card { padding: 20px 22px; background: rgba(42,44,48,0.82); backdrop-filter: blur(10px); }
.concern-card-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 22px; }
.concern-card-head h2 { margin: 0; font-size: 1.45rem; font-family: 'Playfair Display', serif; }
.concern-card-head p { margin: 6px 0 0; color: #b8ab98; }
.concern-badge { padding: 8px 12px; border-radius: 999px; background: rgba(214,168,91,0.10); color: #d6a85b; font-size: 0.74rem; font-weight: 700; letter-spacing: 0.14em; text-transform: uppercase; }
.concern-form { display: flex; flex-direction: column; gap: 18px; }
.concern-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
.concern-label { display: block; margin-bottom: 10px; font-size: 0.88rem; font-weight: 700; color: #d7c8b4; }
.concern-input { width: 100%; padding: 14px 16px; border-radius: 16px; border: 1px solid rgba(214,168,91,0.14); background: rgba(37,39,42,0.90); color: #f8f3ea; font-size: 0.95rem; }
.concern-input:focus { outline: none; border-color: rgba(214,168,91,0.34); box-shadow: 0 0 0 4px rgba(214,168,91,0.08); }
.concern-textarea { min-height: 180px; resize: vertical; line-height: 1.7; }
@media (max-width: 900px) { .concern-hero { flex-direction: column; align-items: flex-start; padding: 24px; } .concern-hero-actions { width: 100%; justify-content: flex-start; } .concern-grid { grid-template-columns: 1fr; } .concern-card { padding: 22px; } }
</style>
</x-app-layout>
