<x-app-layout>
@php($editing = $concern->exists)
<div class="resident-concern-form-page">
    <div class="resident-concern-form-topbar">
        <a href="{{ $editing ? route('concerns.show', $concern) : route('concerns.index') }}" class="resident-back-link resident-create-back">← Back</a>
    </div>

    <section class="resident-concern-form-hero resident-hero-lamp-glow">
        <div>
            <p>Private Resident Support</p>
            <h1>{{ $editing ? 'Update Concern' : 'Report a Concern' }}</h1>
            <span>Send a private complaint to administration. Include the details needed for a clear reply.</span>
        </div>
    </section>

    <section class="resident-concern-form-panel">
        <div class="resident-concern-form-head"><h2>Complaint Details</h2><p>Your complaint is private and visible only to authorized administration staff.</p></div>
        <form method="POST"
              action="{{ $editing ? route('concerns.update', $concern) : route('concerns.store') }}"
              class="resident-concern-form"
              data-prevent-double-submit
              data-submitting-text="{{ $editing ? 'Saving Concern...' : 'Submitting Concern...' }}">
            @csrf
            @if($editing) @method('PATCH') @endif
            <div class="resident-concern-form-grid">
                <label><span>Category</span><select name="category" required>@foreach($categories as $value => $label)<option value="{{ $value }}" @selected(old('category', $prefillCategory) === $value)>{{ $label }}</option>@endforeach</select></label>
                <label><span>Subject</span><input name="subject" value="{{ old('subject', $concern->subject) }}" required maxlength="180" placeholder="Brief summary of the concern"></label>
                <label class="resident-concern-form-wide"><span>Location</span><input name="location" value="{{ old('location', $prefillLocation) }}" required placeholder="Room, hallway, floor, or shared area"></label>
            </div>
            <label><span>Description</span><textarea name="details" rows="7" required placeholder="Describe what happened and include any details administration should know.">{{ old('details', $prefillDetails) }}</textarea></label>
            <button type="submit">{{ $editing ? 'Save Changes' : 'Submit Concern' }}</button>
        </form>
    </section>
</div>

<style>
.resident-concern-form-page {
    max-width: 980px;
    margin: 0 auto;
    display: grid;
    gap: 18px;
    color: #f0e9df
}
.resident-concern-form-topbar {
    display: flex;
    justify-content: flex-start;
    align-items: center
}
.resident-concern-form-hero, .resident-concern-form-panel {
    border: 1px solid rgba(214, 168, 91, .14);
    border-radius: 22px
}
.resident-concern-form-hero {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 18px;
    padding: 27px 29px;
    background: linear-gradient(115deg, #1f2023, #24262b 42%, #3b3023)
}
.resident-concern-form-hero p {
    margin: 0 0 8px;
    color: #d6a85b;
    font-size: .7rem;
    font-weight: 800;
    letter-spacing: .2em;
    text-transform: uppercase
}
.resident-concern-form-hero h1 {
    margin: 0;
    font: 600 clamp(2.1rem, 4vw, 3.2rem)/1.05 'Playfair Display', serif
}
.resident-concern-form-hero span, .resident-concern-form-head p {
    display: block;
    margin-top: 8px;
    color: #b8ab98;
    line-height: 1.65
}
.resident-concern-form-panel {
    padding: 22px;
    background: rgba(42, 44, 48, .86)
}
.resident-concern-form-head {
    margin-bottom: 18px
}
.resident-concern-form-head h2 {
    margin: 0;
    font: 600 1.45rem 'Playfair Display', serif
}
.resident-concern-form-head p {
    font-size: .86rem
}
.resident-concern-form {
    display: grid;
    gap: 15px
}
.resident-concern-form-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 13px
}
.resident-concern-form-wide {
    grid-column: 1/-1
}
.resident-concern-form label span {
    display: block;
    margin-bottom: 7px;
    color: #d7c8b4;
    font-size: .84rem;
    font-weight: 700
}
.resident-concern-form input, .resident-concern-form select, .resident-concern-form textarea {
    width: 100%;
    padding: 12px 13px;
    border: 1px solid rgba(214, 168, 91, .14);
    border-radius: 11px;
    background: #25272a;
    color: #f8f3ea
}
.resident-concern-form textarea {
    resize: vertical
}
.resident-concern-form button {
    width: max-content;
    padding: 11px 17px;
    border: 1px solid rgba(214, 168, 91, .18);
    border-radius: 999px;
    background: linear-gradient(135deg, #c79745, #d6a85b);
    color: #1b150f;
    font-weight: 800;
    cursor: pointer
}
@media(max-width:680px) {
    .resident-concern-form-hero {
        align-items: flex-start;
        flex-direction: column
    }
    .resident-concern-form-grid {
        grid-template-columns: 1fr
    }
    .resident-concern-form-wide {
        grid-column: auto
    }
}
</style>
</x-app-layout>
