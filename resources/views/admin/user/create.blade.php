<x-app-layout>
<div class="access-form-page">
    <section class="access-form-hero">
        <div>
            <p>Access Control</p>
            <h1>Create Account</h1>
            <span>Add the account details and assign the correct access role.</span>
        </div>
        <a href="{{ route('admin.users') }}" class="access-form-back">Back to Users</a>
    </section>

    <section class="access-form-card">
        <form method="POST" action="{{ route('admin.users.store') }}" class="access-form" data-prevent-double-submit data-submitting-text="Creating account..." data-unsaved-check>
            @csrf
            <div class="access-form-head">
                <div><h2>Account details</h2><p>Enter the information needed for access and account recovery.</p></div>
            </div>
            <div class="access-form-grid">
                <label><span>Full name *</span><input name="name" value="{{ old('name') }}" required autocomplete="name">@error('name')<small class="access-form-error">{{ $message }}</small>@enderror</label>
                <label><span>Email address *</span><input type="email" name="email" value="{{ old('email') }}" required autocomplete="email">@error('email')<small class="access-form-error">{{ $message }}</small>@enderror<small class="access-form-hint">Must use the official hall email domain.</small></label>
                <label><span>Phone number</span><input name="phone_number" value="{{ old('phone_number') }}" autocomplete="tel">@error('phone_number')<small class="access-form-error">{{ $message }}</small>@enderror<small class="access-form-hint">Format: +63 9XX XXX XXXX</small></label>
                <label><span>Role *</span><select name="role" required><option value="resident" @selected(old('role', 'resident') === 'resident')>Resident</option><option value="handyman" @selected(old('role') === 'handyman')>Staff</option><option value="manager" @selected(old('role') === 'manager')>Administrator</option></select>@error('role')<small class="access-form-error">{{ $message }}</small>@enderror</label>
                <label data-resident-only><span>Room number *</span><input name="room_number" value="{{ old('room_number') }}" placeholder="Example: B-204">@error('room_number')<small class="access-form-error">{{ $message }}</small>@enderror</label>
            </div>
            <p class="access-form-help">A temporary password will be shown after creation. The user must change it after first sign-in.</p>
            <div class="access-form-actions"><button class="admin-user-form-action admin-user-form-action-primary" type="submit">Create account</button><a class="admin-user-form-action admin-user-form-action-secondary" href="{{ route('admin.users') }}" data-unsaved-reset>Cancel</a></div>
        </form>
    </section>
</div>
@include('admin.user.partials.form-styles')
<style>
.access-form-page {
    width: 100%;
    max-width: none;
}
.access-form-hero {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: var(--hs-space-6);
    padding: var(--hs-space-8) var(--hs-space-9);
    background: linear-gradient(135deg, #fffaf2 0%, #f8f3eb 58%, #f1e7d8 100%);
    border: 1px solid #4f4336;
    border-radius: var(--hs-radius-lg);
}
.access-form-hero p {
    margin: 0 0 var(--hs-space-2);
    color: #b47721;
    font-size: var(--hs-font-caption);
    font-weight: var(--hs-font-extrabold);
    letter-spacing: .18em;
    text-transform: uppercase;
}
.access-form-hero h1 {
    color: #342a23;
    font-size: clamp(2rem, 3.4vw, 3rem);
    font-weight: var(--hs-font-normal);
}
.access-form-hero span {
    color: #786b60;
    font-size: var(--hs-font-section);
}
.access-form-back {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 40px;
    padding: 0 var(--hs-space-4);
    border: 1px solid #ead8bd;
    border-radius: var(--hs-radius-sm);
    background: #f8f1e7;
    color: #b06b12;
    font-size: var(--hs-font-caption);
    font-weight: var(--hs-font-extrabold);
    text-decoration: none;
    white-space: nowrap;
}
.access-form-back:hover, .access-form-back:focus-visible {
    border-color: #dfbc86;
    background: #fff8ed;
    color: #8b520d;
}
.access-form-card {
    background: #ffffff;
}
.access-form {
    padding: 22px;
}
.access-form-grid {
    gap: 16px;
}
.access-form-grid label > span {
    display: block;
    margin-bottom: 6px;
    color: #62574d;
    font-size: .72rem;
    font-weight: 800;
    letter-spacing: .08em;
    text-transform: uppercase;
}
.access-form-hint {
    display: block;
    margin-top: 4px;
    color: #917b65;
    font-size: .72rem;
}
.access-form-error {
    display: block;
    margin-top: 4px;
    color: #8f342e;
    font-size: .72rem;
    font-weight: 600;
}
.access-form-help {
    margin: 16px 0 0;
    color: #786b60;
    font-size: .82rem;
}
@media (max-width:720px) {
    .access-form-hero {
        align-items: flex-start;
        flex-direction: column;
        padding: 26px 24px;
    }
}
</style>
<script>
const accountRole = document.querySelector('select[name="role"]');
const residentOnlyFields = document.querySelectorAll('[data-resident-only]');

const syncResidentFields = () => {
    const resident = accountRole?.value === 'resident';

    residentOnlyFields.forEach((field) => {
        field.hidden = !resident;
    });

    document.querySelector('input[name="room_number"]')?.toggleAttribute('required', resident);
};

accountRole?.addEventListener('change', syncResidentFields);
syncResidentFields();
</script>
</x-app-layout>
