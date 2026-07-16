<x-app-layout>
<x-admin-breadcrumb :crumbs="[
    ['label' => 'User Directory', 'route' => 'admin.users'],
    ['label' => $user->name, 'route' => 'admin.users.show', 'params' => $user],
    ['label' => 'Edit'],
]" />

@php
    $recordTotal = collect($operationalCounts)->sum();
    $canDelete = $user->id !== auth()->id() && ! $user->isLastActiveManager() && $recordTotal === 0;
@endphp

<div class="access-form-page">
    <section class="access-form-hero">
        <div>
            <p>Access Control</p>
            <h1>Edit User</h1>
            <span>Update {{ $user->name }} while preserving a clear administrative audit trail.</span>
        </div>
        <a href="{{ route('admin.users') }}" class="access-form-back">Back to Users</a>
    </section>

    <div class="access-edit-layout">
        <section class="access-form-card">
            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="access-form" data-prevent-double-submit data-submitting-text="Saving changes..." data-unsaved-check>
                @csrf
                @method('PATCH')
                <div class="access-form-head">
                    <div><h2>Account profile</h2><p>Role changes take effect immediately after saving.</p></div>
                    <span>{{ $user->role_label }}</span>
                </div>
                @if($user->must_change_password)
                    <div class="access-form-warning">
                        <strong>Pending setup</strong>
                        <p>This user has not yet changed their temporary password. They cannot fully access the system until they do.</p>
                    </div>
                @endif
                <div class="access-form-grid">
                    <label><span>Full name *</span><input name="name" value="{{ old('name', $user->name) }}" required autocomplete="name">@error('name')<small class="access-form-error">{{ $message }}</small>@enderror</label>
                    <label><span>Email address *</span><input type="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="email">@error('email')<small class="access-form-error">{{ $message }}</small>@enderror<small class="access-form-hint">Must use the official hall email domain.</small></label>
                    <label><span>Phone number</span><input name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" autocomplete="tel">@error('phone_number')<small class="access-form-error">{{ $message }}</small>@enderror<small class="access-form-hint">Format: +63 9XX XXX XXXX</small></label>
                    <label><span>Role *</span><select name="role" required><option value="resident" @selected(old('role', $user->role) === 'resident')>Resident</option><option value="handyman" @selected(old('role', $user->role) === 'handyman')>Staff</option><option value="manager" @selected(old('role', $user->role) === 'manager')>Administrator</option></select>@error('role')<small class="access-form-error">{{ $message }}</small>@enderror</label>
                    <label data-resident-only><span>Room number <em>Residents only</em></span><input name="room_number" value="{{ old('room_number', $user->room_number) }}" placeholder="Required for resident accounts">@error('room_number')<small class="access-form-error">{{ $message }}</small>@enderror</label>
                </div>
                <div class="access-form-actions"><button class="admin-user-form-action admin-user-form-action-primary" type="submit">Save changes</button><a class="admin-user-form-action admin-user-form-action-secondary" href="{{ route('admin.users.show', $user) }}" data-unsaved-reset>Cancel</a></div>
            </form>
        </section>

        <aside class="access-edit-sidebar">
            <section class="access-control-panel">
                <div class="access-control-head">
                    <p>Lifecycle</p>
                    <h2>Account controls</h2>
                </div>
                @if($user->id === auth()->id())
                    <p class="access-control-help">Use profile security settings for your own administrator account.</p>
                @else
                    <form method="POST" action="{{ route('admin.users.update-status', $user) }}" data-confirm-message="{{ $user->is_active ? 'Deactivate this account and revoke active sessions?' : 'Reactivate this account?' }}" data-prevent-double-submit data-submitting-text="{{ $user->is_active ? 'Deactivating Account...' : 'Reactivating Account...' }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="{{ $user->is_active ? 'inactive' : 'active' }}">
                        <div class="account-control-row">
                            <button type="submit" class="account-control {{ $user->is_active ? 'is-danger' : 'is-success' }}" data-confirm="{{ $user->is_active ? 'Are you sure you want to deactivate this account?' : 'Are you sure you want to activate this account?' }}">{{ $user->is_active ? 'Temporarily Deactivate' : 'Reactivate Account' }}</button>
                            <button
                                type="button"
                                class="account-control-help"
                                aria-label="{{ $user->is_active ? 'About temporary deactivation: Blocks login and signs the user out. The account can be reactivated later.' : 'About reactivation: Restores login access to this account.' }}"
                                data-tooltip="{{ $user->is_active ? 'Blocks login and signs the user out. The account can be reactivated later.' : 'Restores login access to this account.' }}"
                            >?</button>
                        </div>
                    </form>
                    <form method="POST" action="{{ route('admin.users.reset-password', $user) }}" data-confirm-message="Revoke active sessions and generate a new temporary password?" data-prevent-double-submit data-submitting-text="Generating Password...">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="account-control">Generate temporary password</button>
                    </form>
                    @if($user->isResident() && $user->residency_status !== 'moved_out')
                        <form method="POST" action="{{ route('admin.users.move-out', $user) }}" data-confirm-message="Archive this resident after move-out, cancel future bookings, and disable login access?" data-prevent-double-submit data-submitting-text="Processing Move-Out...">
                            @csrf
                            @method('PATCH')
                            <div class="account-control-row">
                                <button type="submit" class="account-control is-danger" data-confirm="Are you sure you want to move out this resident? This action cannot be undone.">Archive as Moved Out</button>
                                <button
                                    type="button"
                                    class="account-control-help"
                                    aria-label="About move-out: Marks the resident as moved out, disables login, and cancels future bookings. This cannot be undone."
                                    data-tooltip="Marks the resident as moved out, disables login, and cancels future bookings. This cannot be undone."
                                >?</button>
                            </div>
                        </form>
                    @endif
                @endif
            </section>

            @if($user->id !== auth()->id())
                <section class="access-control-panel access-control-danger">
                    <div class="access-control-head">
                        <p>Restricted Action</p>
                        <h2>Delete account</h2>
                    </div>
                    @if($canDelete)
                        <p class="access-control-help">This unused account has no operational history and can be permanently deleted.</p>
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" data-confirm-message="Permanently delete this unused account? This action cannot be undone." data-prevent-double-submit data-submitting-text="Deleting Account...">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="account-control is-danger">Delete account</button>
                        </form>
                    @else
                        <p class="access-control-help">Deletion is unavailable because this account is protected or has operational history. Deactivate it to preserve attribution.</p>
                    @endif
                </section>
            @endif
        </aside>
    </div>
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
    gap: 24px;
}
.access-form-hero > div {
    min-width: 0;
}
.access-edit-layout {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 370px;
    gap: 18px;
    align-items: start;
}
.access-edit-sidebar {
    display: grid;
    gap: 18px;
}
.access-form-back {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 40px;
    padding: 0 16px;
    border: 1px solid #ead8bd;
    border-radius: 8px;
    background: #f8f1e7;
    color: #b06b12;
    font-size: .78rem;
    font-weight: 800;
    text-decoration: none;
    white-space: nowrap;
}
.access-form-back:hover, .access-form-back:focus-visible {
    border-color: #dfbc86;
    background: #fff8ed;
    color: #8b520d;
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
.access-form-warning {
    padding: 12px 16px;
    margin-bottom: 16px;
    border: 1px solid #e2c97e;
    border-radius: 8px;
    background: #fdf8ec;
    color: #7a5c1e;
    font-size: .83rem;
}
.access-form-warning strong {
    display: block;
    margin-bottom: 4px;
    font-weight: 700;
}
.access-form-warning p {
    margin: 0;
}
.access-control-panel {
    border: 1px solid #4f4336;
    border-radius: 12px;
    background: #ffffff;
    box-shadow: 0 4px 14px rgba(84, 61, 37, 0.10);
}
.access-control-danger {
    border-color: #dda29d;
}
.access-control-head {
    padding: 16px 18px;
    border-bottom: 1px solid #e3d8ca;
}
.access-control-head p {
    margin: 0 0 6px;
    color: #b97925;
    font-size: .68rem;
    font-weight: 800;
    letter-spacing: .18em;
    text-transform: uppercase;
}
.access-control-head h2 {
    margin: 0;
    color: #3c342d;
    font-family: 'Playfair Display', serif;
    font-size: 1.35rem;
    font-weight: 400;
}
.access-control-help {
    margin: 0;
    padding: 16px 18px;
    border-bottom: 1px solid #e3d8ca;
    color: #817467;
    font-size: .82rem;
    line-height: 1.6;
}
.access-control-panel form {
    display: grid;
    gap: 9px;
    padding: 12px 18px;
    border-bottom: 1px solid #e3d8ca;
}
.access-control-panel form:last-child, .access-control-help:last-child {
    border-bottom: 0;
}
.account-control-row {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 20px;
    align-items: center;
    gap: 8px;
}
.account-control {
    width: 100%;
    min-height: 40px;
    padding: 0 11px;
    border: 1px solid #d2ae7b;
    border-radius: 7px;
    background: #f3e3cc;
    color: #68400f;
    font-size: .78rem;
    font-weight: 800;
    cursor: pointer;
}
.account-control.is-danger {
    border-color: #dda29d;
    background: #f7dfdc;
    color: #8f342e;
}
.account-control.is-success {
    border-color: #9fc6a8;
    background: #deeee1;
    color: #356140;
}
.account-control-help {
    position: relative;
    display: inline-grid;
    width: 18px;
    min-width: 18px;
    height: 18px;
    min-height: 18px;
    place-items: center;
    padding: 0;
    border: 1px solid #c7b8a7;
    border-radius: 50%;
    background: #f8f5f0;
    color: #62574d;
    font: inherit;
    font-size: .68rem;
    font-weight: 900;
    line-height: 1;
    cursor: help;
}
.account-control-help::before {
    content: attr(data-tooltip);
    position: absolute;
    right: -4px;
    bottom: calc(100% + 10px);
    z-index: 20;
    width: 245px;
    padding: 9px 11px;
    border-radius: 8px;
    background: #342a23;
    color: #fffaf2;
    box-shadow: 0 12px 28px rgba(52, 42, 35, .2);
    font-size: .74rem;
    font-weight: 600;
    line-height: 1.45;
    text-align: left;
    opacity: 0;
    pointer-events: none;
    transform: translateY(4px);
    transition: opacity .16s ease, transform .16s ease;
}
.account-control-help:hover, .account-control-help:focus-visible {
    border-color: #8f6a3d;
    background: #fff;
    color: #8b520d;
    outline: none;
    box-shadow: 0 0 0 3px rgba(180, 119, 33, .16);
}
.account-control-help:hover::before, .account-control-help:focus-visible::before {
    opacity: 1;
    transform: translateY(0);
}
@media (max-width:1080px) {
    .access-edit-layout {
        grid-template-columns: 1fr;
    }
    .access-edit-sidebar {
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    }
}
@media (max-width:720px) {
    .access-form-hero {
        align-items: flex-start;
        flex-direction: column;
    }
    .access-form-back {
        width: 100%;
    }
    .access-edit-sidebar {
        grid-template-columns: 1fr;
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
