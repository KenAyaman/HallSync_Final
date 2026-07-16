<x-app-layout>
@php
    $recordTotal = collect($operationalCounts)->sum();
    $canDelete = $user->id !== auth()->id() && ! $user->isLastActiveManager() && $recordTotal === 0;
    $temporaryPassword = session('temporary_password') ?: $user->temporary_password;
    $passwordDisplay = $temporaryPassword
        ?: ($user->must_change_password ? 'Temporary password unavailable. Generate a new one.' : 'Hidden after user change');
@endphp

<x-admin-breadcrumb :crumbs="[
    ['label' => 'User Directory', 'route' => 'admin.users'],
    ['label' => $user->name],
]" />

<div class="account-page admin-detail-page">
    <section class="account-hero admin-detail-hero">
        <div>
            <p>Account Record</p>
            <h1>{{ $user->name }}</h1>
            <span>Review access, contact information, history, and related operations from one account record.</span>
        </div>
        <div class="account-hero-actions">
            <a href="{{ route('admin.users') }}" class="account-hero-back">Back to directory</a>
            <a href="{{ route('admin.users.edit', $user) }}" class="account-hero-edit">Edit profile</a>
        </div>
    </section>

    <section class="account-layout">
        <div class="account-main">
            <section class="account-panel admin-detail-panel">
                <div class="account-panel-head">
                    <div><p>Profile</p><h2>Account information</h2></div>
                    <span class="account-status {{ $user->is_active ? 'is-active' : 'is-inactive' }}">{{ $user->is_active ? 'Active' : 'Inactive' }}</span>
                </div>
                <div class="account-facts">
                    <div><span>Full name</span><strong>{{ $user->name }}</strong></div>
                    <div><span>Role</span><strong>{{ $user->role_label }}</strong></div>
                    <div><span>Email</span><strong>{{ $user->email }}</strong></div>
                    <div><span>Phone</span><strong>{{ $user->phone_number ?: 'Not provided' }}</strong></div>
                    <div><span>Room</span><strong>{{ $user->room_number ?: 'Not assigned' }}</strong></div>
                    <div><span>Resident ID</span><strong>{{ $user->resident_number ?: 'Not assigned' }}</strong></div>
                    <div><span>Created</span><strong>{{ $user->created_at->format('M d, Y h:i A') }}</strong></div>
                    <div><span>Last login</span><strong>{{ $user->last_login_at?->format('M d, Y h:i A') ?? 'Never signed in' }}</strong></div>
                    <div><span>Password state</span><strong>{{ $user->must_change_password ? 'Change required at next sign-in' : 'Current' }}</strong></div>
                    <div>
                        <span>Password</span>
                        <strong>{{ $passwordDisplay }}</strong>
                    </div>
                </div>
            </section>

            <section class="account-panel admin-detail-panel">
                <div class="account-panel-head">
                    <div><p>Activity History</p><h2>Account timeline</h2></div>
                    <span>{{ $activityLogs->total() }} event{{ $activityLogs->total() === 1 ? '' : 's' }}</span>
                </div>
                <div class="account-timeline">
                    @forelse($activityLogs as $log)
                        <article>
                            <i></i>
                            <div>
                                <strong>{{ $log->description }}</strong>
                                <p>{{ $log->actor?->name ?? 'System' }} <span>{{ $log->created_at->format('M d, Y h:i A') }}</span></p>
                            </div>
                            <code>{{ $log->action }}</code>
                        </article>
                    @empty
                        <div class="account-empty">No recorded account activity yet.</div>
                    @endforelse
                </div>
                @if($activityLogs->hasPages())<div class="account-pagination">{{ $activityLogs->links() }}</div>@endif
            </section>

            @if($user->isResident())
                <section class="account-panel admin-detail-panel">
                    <div class="account-panel-head"><div><p>Resident Operations</p><h2>Maintenance requests</h2></div><span>{{ $operationalCounts['maintenance requests'] }}</span></div>
                    <div class="account-records">
                        @forelse($residentTickets as $ticket)
                            <a href="{{ route('tickets.show', $ticket) }}"><strong>{{ $ticket->title }}</strong><span>{{ $ticket->ticket_id }} · {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span></a>
                        @empty
                            <div class="account-empty">No maintenance requests from this resident.</div>
                        @endforelse
                    </div>
                </section>

                <section class="account-panel admin-detail-panel">
                    <div class="account-panel-head"><div><p>Resident Operations</p><h2>Recent bookings</h2></div><span>{{ $operationalCounts['bookings'] }}</span></div>
                    <div class="account-records">
                        @forelse($recentBookings as $booking)
                            <a href="{{ route('bookings.show', $booking) }}"><strong>{{ $booking->facility_name }}</strong><span>{{ $booking->booking_date->format('M d, Y h:i A') }} · {{ ucfirst($booking->status) }}</span></a>
                        @empty
                            <div class="account-empty">No facility bookings from this resident.</div>
                        @endforelse
                    </div>
                </section>
            @elseif($user->isHandyman())
                <section class="account-panel admin-detail-panel">
                    <div class="account-panel-head"><div><p>Staff Operations</p><h2>Assigned requests</h2></div><span>{{ $operationalCounts['assigned requests'] }}</span></div>
                    <div class="account-records">
                        @forelse($assignedTickets as $ticket)
                            <a href="{{ route('tickets.show', $ticket) }}"><strong>{{ $ticket->title }}</strong><span>{{ $ticket->ticket_id }} · {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span></a>
                        @empty
                            <div class="account-empty">No maintenance requests assigned to this staff member.</div>
                        @endforelse
                    </div>
                </section>
            @endif
        </div>

        <aside class="account-sidebar">
            <section class="account-panel admin-detail-panel">
                <div class="account-panel-head"><div><p>Operational History</p><h2>Linked records</h2></div><span>{{ $recordTotal }}</span></div>
                <dl class="account-counts">
                    @foreach($operationalCounts as $label => $count)
                        <div><dt>{{ ucfirst($label) }}</dt><dd>{{ $count }}</dd></div>
                    @endforeach
                </dl>
            </section>
        </aside>
    </section>
</div>

@if($temporaryPassword)
    <div class="account-password-modal" data-temporary-password-modal role="dialog" aria-modal="true" aria-labelledby="temporary-password-title">
        <div class="account-password-modal-backdrop" data-temporary-password-dismiss></div>
        <section class="account-password-modal-card">
            <p>First Login Credential</p>
            <h2 id="temporary-password-title">Temporary password ready</h2>
            <span>Share this with {{ $user->email }}. The user must change it after first sign-in.</span>
            <label class="account-password-field">
                <span class="sr-only">Temporary password</span>
                <input id="temporary-password-value" type="text" value="{{ $temporaryPassword }}" readonly>
            </label>
            <strong class="account-password-warning">This temporary password will be hidden after the user changes it.</strong>
            <div class="account-password-actions">
                <button type="button" class="account-password-copy" data-copy-temporary-password data-copy-target="temporary-password-value">Copy Password</button>
                <button type="button" class="account-password-dismiss" data-temporary-password-dismiss>I've noted this down</button>
            </div>
        </section>
    </div>
@endif

<style>
body.role-manager:has(.account-page) {
    --admin-bg-top: #ede6db;
    --admin-bg-mid: #e7ded1;
    --admin-bg-bottom: #ede6db;
}
.account-page {
    max-width: 1580px;
    margin: 0 auto;
    display: grid;
    gap: 18px;
}
.account-hero, .account-panel, .account-credential {
    border: 1px solid #4f4336;
    border-radius: 12px;
}
.account-hero {
    display: flex;
    justify-content: space-between;
    gap: 20px;
    padding: 23px 25px;
    background: linear-gradient(115deg, #241c14, #493522);
}
.account-hero a {
    font-size: .75rem;
    font-weight: 800;
    letter-spacing: .08em;
    text-decoration: none;
    text-transform: uppercase;
}
.account-hero p, .account-panel-head p {
    margin: 16px 0 5px;
    color: #e5b66e;
    font-size: .66rem;
    font-weight: 800;
    letter-spacing: .17em;
    text-transform: uppercase;
}
.account-hero h1, .account-panel h2 {
    margin: 0;
    font-family: 'Playfair Display', serif;
}
.account-hero h1 {
    color: #fffaf3;
    font-size: clamp(2rem, 3vw, 3rem);
}
.account-hero span {
    display: block;
    margin-top: 7px;
    color: #eadfce;
    line-height: 1.6;
}
.account-hero-actions {
    display: flex;
    align-items: center;
    align-self: flex-start;
    gap: 10px;
}
.account-hero-back, .account-hero-edit {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 40px;
    padding: 0 14px;
    border-radius: 8px;
    white-space: nowrap;
}
.account-hero-back {
    border: 1px solid #ead8bd;
    background: #f8f1e7;
    color: #b06b12;
}
.account-hero-back:hover, .account-hero-back:focus-visible {
    border-color: #dfbc86;
    background: #fff8ed;
    color: #8b520d;
}
.account-hero-edit {
    border: 1px solid #d2ae7b;
    background: linear-gradient(90deg, #bd8a34, #d4a44c);
    box-shadow: 0 10px 20px rgba(165, 114, 36, .2);
    color: #fffaf3;
}
.account-hero-edit:hover, .account-hero-edit:focus-visible {
    background: linear-gradient(90deg, #a86f1e, #c3913c);
    color: #fffaf3;
}
.account-credential {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 18px;
    padding: 18px 20px;
    background: linear-gradient(135deg, #fff7e8, #f1dfbf);
    color: #68400f;
    box-shadow: 0 14px 30px rgba(151, 94, 16, .12);
}
.account-credential p {
    margin: 0 0 5px;
    color: #b97925;
    font-size: .68rem;
    font-weight: 900;
    letter-spacing: .16em;
    text-transform: uppercase;
}
.account-credential h2 {
    margin: 0;
    color: #3c2812;
    font-family: 'Playfair Display', serif;
    font-size: 1.35rem;
}
.account-credential span {
    display: block;
    margin-top: 5px;
    color: #68400f;
    font-size: .84rem;
    line-height: 1.5;
}
.account-credential-secret {
    display: flex;
    align-items: center;
    gap: 10px;
    flex: 0 0 auto;
}
.account-credential code {
    padding: 11px 14px;
    border: 1px solid #d2ae7b;
    border-radius: 9px;
    background: #fffdf9;
    color: #3c2812;
    font-size: 1rem;
    font-weight: 900;
    letter-spacing: .04em;
    word-break: break-all;
    overflow-wrap: anywhere;
}
.account-credential button {
    min-height: 40px;
    padding: 0 14px;
    border: 1px solid #d2ae7b;
    border-radius: 8px;
    background: #d58c22;
    color: #fffaf3;
    cursor: pointer;
    font-size: .78rem;
    font-weight: 900;
}
.account-credential button:hover, .account-credential button:focus-visible {
    background: #b77417;
}
.account-layout {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 340px;
    gap: 18px;
    align-items: start;
}
.account-main, .account-sidebar {
    display: grid;
    gap: 18px;
}
.account-panel {
    overflow: hidden;
    background: #ffffff;
    box-shadow: 0 4px 14px rgba(84, 61, 37, 0.10);
}
.account-panel-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 14px 16px;
    border-bottom: 1px solid #e3d8ca;
}
.account-panel-head p {
    margin: 0 0 4px;
    color: #b97925;
}
.account-panel h2 {
    color: #3c342d;
    font-size: 1.2rem;
}
.account-panel-head > span {
    color: #75695d;
    font-size: .75rem;
    font-weight: 800;
}
.account-status {
    padding: 5px 9px;
    border: 1px solid;
    border-radius: 999px;
    font-size: .68rem;
    font-weight: 800;
}
.account-status.is-active {
    border-color: #9fc6a8;
    background: #deeee1;
    color: #356140;
}
.account-status.is-inactive {
    border-color: #dda29d;
    background: #f7dfdc;
    color: #8f342e;
}
.account-facts {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
}
.account-facts div {
    padding: 13px 16px;
    border-bottom: 1px solid #e3d8ca;
}
.account-facts div:nth-child(odd) {
    border-right: 1px solid #e3d8ca;
}
.account-facts span {
    display: block;
    color: #817467;
    font-size: .68rem;
    font-weight: 800;
    letter-spacing: .08em;
    text-transform: uppercase;
}
.account-facts strong {
    display: block;
    margin-top: 5px;
    color: #453b33;
    font-size: .86rem;
}
.account-timeline article {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    border-bottom: 1px solid #e3d8ca;
}
.account-timeline i {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #b97925;
}
.account-timeline strong {
    color: #453b33;
    font-size: .84rem;
}
.account-timeline p {
    margin: 3px 0 0;
    color: #817467;
    font-size: .74rem;
}
.account-timeline p span {
    margin-left: 8px;
}
.account-timeline code {
    margin-left: auto;
    color: #825213;
    font-size: .68rem;
}
.account-records a {
    display: flex;
    justify-content: space-between;
    gap: 12px;
    padding: 12px 16px;
    border-bottom: 1px solid #e3d8ca;
    color: #453b33;
    text-decoration: none;
}
.account-records a:hover {
    background: #ede6db;
}
.account-records span {
    color: #817467;
    font-size: .76rem;
    text-align: right;
}
.account-empty, .account-help {
    margin: 0;
    padding: 16px;
    color: #817467;
    font-size: .82rem;
    line-height: 1.6;
}
.account-pagination {
    padding: 12px 16px;
    border-top: 1px solid #e3d8ca;
}
.account-sidebar form {
    display: grid;
    gap: 9px;
    padding: 12px 16px;
    border-top: 1px solid #e3d8ca;
}
.account-control {
    min-height: 36px;
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
.account-check {
    display: flex;
    align-items: center;
    gap: 7px;
    color: #62574d;
    font-size: .78rem;
}
.account-counts {
    margin: 0;
}
.account-counts div {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    padding: 9px 16px;
    border-bottom: 1px solid #e3d8ca;
    color: #62574d;
    font-size: .78rem;
}
.account-counts dd {
    margin: 0;
    color: #453b33;
    font-weight: 800;
}
.account-danger {
    border-color: #dda29d;
}
.account-password-modal {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 10000;
    display: grid;
    width: 100vw;
    height: 100vh;
    height: 100dvh;
    place-items: center;
    padding: 18px;
}
body.account-password-modal-open {
    overflow: hidden;
}
.account-password-modal[hidden] {
    display: none;
}
.account-password-modal-backdrop {
    position: fixed;
    inset: 0;
    width: 100vw;
    height: 100vh;
    height: 100dvh;
    background: rgba(36, 28, 20, .62);
    backdrop-filter: blur(3px);
}
.account-password-modal-card {
    position: relative;
    z-index: 1;
    width: min(480px, 100%);
    padding: 24px;
    border: 1px solid #e3d8ca;
    border-radius: 12px;
    background: #fffdf9;
    box-shadow: 0 24px 70px rgba(36, 28, 20, .32);
    color: #453b33;
}
.account-password-modal-card p {
    margin: 0 0 7px;
    color: #b97925;
    font-size: .68rem;
    font-weight: 900;
    letter-spacing: .16em;
    text-transform: uppercase;
}
.account-password-modal-card h2 {
    margin: 0;
    color: #3c2812;
    font-family: 'Playfair Display', serif;
    font-size: 1.55rem;
}
.account-password-modal-card > span {
    display: block;
    margin-top: 8px;
    color: #68400f;
    font-size: .86rem;
    line-height: 1.55;
}
.account-password-field {
    display: block;
    margin-top: 16px;
}
.account-password-field input {
    width: 100%;
    min-height: 44px;
    padding: 0 13px;
    border: 1px solid #d2ae7b;
    border-radius: 8px;
    background: #fff7e8;
    color: #3c2812;
    font: inherit;
    font-size: 1rem;
    font-weight: 900;
    letter-spacing: .04em;
}
.account-password-warning {
    display: block;
    margin-top: 12px;
    color: #8f342e;
    font-size: .84rem;
}
.account-password-actions {
    display: flex;
    justify-content: flex-end;
    gap: 9px;
    margin-top: 18px;
}
.account-password-copy, .account-password-dismiss {
    min-height: 40px;
    padding: 0 14px;
    border-radius: 8px;
    cursor: pointer;
    font: inherit;
    font-size: .78rem;
    font-weight: 900;
}
.account-password-copy {
    border: 1px solid #d2ae7b;
    background: #d58c22;
    color: #fffaf3;
}
.account-password-copy:hover, .account-password-copy:focus-visible {
    background: #b77417;
}
.account-password-dismiss {
    border: 1px solid #ead8bd;
    background: #f8f1e7;
    color: #8b520d;
}
.account-password-dismiss:hover, .account-password-dismiss:focus-visible {
    background: #fff8ed;
}
.account-sidebar {
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
}
@media(max-width:980px) {
    .account-layout {
        grid-template-columns: 1fr;
    }
    .account-sidebar {
        grid-template-columns: 1fr;
    }
}
@media(max-width:620px) {
    .account-hero, .account-credential, .account-credential-secret {
        align-items: flex-start;
        flex-direction: column;
    }
    .account-hero-actions {
        align-items: stretch;
        flex-direction: column;
        width: 100%;
    }
    .account-hero-back, .account-hero-edit {
        width: 100%;
    }
    .account-credential-secret {
        width: 100%;
    }
    .account-credential code, .account-credential button {
        width: 100%;
    }
    .account-password-actions {
        align-items: stretch;
        flex-direction: column;
    }
    .account-facts {
        grid-template-columns: 1fr;
    }
    .account-facts div:nth-child(odd) {
        border-right: 0;
    }
    .account-timeline article, .account-records a {
        align-items: flex-start;
        flex-direction: column;
    }
    .account-timeline code {
        margin-left: 18px;
    }
    .account-records span {
        text-align: left;
    }
}
</style>
<script>
const temporaryPasswordModal = document.querySelector('[data-temporary-password-modal]');
if (temporaryPasswordModal && temporaryPasswordModal.parentElement !== document.body) {
    document.body.appendChild(temporaryPasswordModal);
    document.body.classList.add('account-password-modal-open');
}

document.querySelector('[data-copy-temporary-password]')?.addEventListener('click', async (event) => {
    const button = event.currentTarget;
    const field = document.getElementById(button.dataset.copyTarget);
    const value = field?.value?.trim();

    if (!value) return;

    try {
        await navigator.clipboard.writeText(value);
        button.textContent = 'Copied';
        window.appToast?.('success', 'Temporary password copied. Share it securely with the user.');
    } catch (error) {
        field?.select();
        button.textContent = 'Select text';
        window.appToast?.('warning', 'Copy failed. Select the password manually.');
    }
});

document.querySelectorAll('[data-temporary-password-dismiss]').forEach((control) => {
    control.addEventListener('click', () => {
        document.querySelector('[data-temporary-password-modal]')?.setAttribute('hidden', '');
        document.body.classList.remove('account-password-modal-open');
    });
});
</script>
</x-app-layout>
