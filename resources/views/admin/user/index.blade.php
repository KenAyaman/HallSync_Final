<x-app-layout>
@php
    $role = $filters['role'] ?? 'all';
    $status = $filters['status'] ?? 'all';
    $activeFilterCount = collect([
        $filters['search'] ?? null,
        ($filters['role'] ?? 'all') !== 'all' ? $filters['role'] : null,
        ($filters['status'] ?? 'all') !== 'all' ? $filters['status'] : null,
    ])->filter()->count();

    $sortableColumns = [
        'name'       => 'Account',
        'email'      => 'Contact',
        'role'       => 'Role',
        'status'     => 'Status',
        'last_login' => 'Last login',
    ];

    $sortLink = function (string $col) use ($sort, $direction, $filters, $role) {
        $newDir = ($sort === $col && $direction === 'asc') ? 'desc' : 'asc';
        return route('admin.users', array_merge(
            array_filter($filters),
            ['sort' => $col, 'direction' => $newDir, 'role' => $role]
        ));
    };
@endphp

<div class="access-page">
    <section class="admin-overview-hero">
        <div>
            <p class="admin-overview-hero__kicker">HallSync Admin</p>
            <h1 class="admin-overview-hero__title">User <span>Directory</span></h1>
            <span class="admin-overview-hero__subtitle">Manage resident, staff, and administrator access with clear account history and reversible lifecycle controls.</span>
        </div>
        <div class="admin-overview-hero__actions">
            <a href="{{ route('admin.users.create') }}" class="admin-user-primary-action access-primary">Add User</a>
        </div>
    </section>

    <section class="access-stats admin-compact-stats admin-compact-stats-5">
        <x-admin-compact-stat icon="users" :value="$totalResidents + $totalHandymen + $totalAdmins" label="All Accounts" note="Directory total" />
        <x-admin-compact-stat icon="user" :value="$totalResidents" label="Residents" note="Hall occupants" tone="green" />
        <x-admin-compact-stat icon="users" :value="$totalHandymen" label="Staff" note="Operations accounts" tone="blue" />
        <x-admin-compact-stat icon="shield" :value="$totalAdmins" label="Administrators" note="Control access" />
        <x-admin-compact-stat icon="alert" :value="$inactiveUsers" label="Inactive" note="Restricted accounts" tone="red" />
    </section>

    <div data-feature-skeleton class="access-panel feature-skeleton-stack" style="gap:0;padding:0;" aria-hidden="true">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 18px;border-bottom:1px solid #e3d8ca;">
            <div>
                <div class="feature-skeleton-line short" style="width:90px;height:10px;margin:0 0 8px;"></div>
                <div class="feature-skeleton-line title" style="width:140px;height:16px;margin:0;"></div>
            </div>
            <div class="feature-skeleton-button" style="width:160px;height:34px;border-radius:6px;"></div>
        </div>
        <div style="padding:0 18px;border-bottom:1px solid #e3d8ca;display:flex;gap:2px;padding-top:0;padding-bottom:0;">
            @for($i=0;$i<4;$i++)
                <div class="feature-skeleton-line" style="width:90px;height:40px;border-radius:0;margin:0;"></div>
            @endfor
        </div>
        @for($i=0;$i<6;$i++)
            <div style="display:flex;align-items:center;gap:14px;padding:13px 18px;border-top:{{ $i===0?'none':'1px solid #e3d8ca' }};">
                <div class="feature-skeleton-line" style="width:18px;height:18px;border-radius:4px;flex-shrink:0;margin:0;"></div>
                <div class="feature-skeleton-avatar" style="width:32px;height:32px;flex-shrink:0;border-radius:50%;"></div>
                <div style="flex:2;min-width:0;">
                    <div class="feature-skeleton-line" style="width:55%;height:11px;margin:0 0 6px;"></div>
                    <div class="feature-skeleton-line short" style="width:38%;height:9px;margin:0;"></div>
                </div>
                <div style="flex:1.5;"><div class="feature-skeleton-line" style="width:72%;height:10px;margin:0;"></div></div>
                <div style="flex:0.8;"><div class="feature-skeleton-pill" style="width:56px;height:22px;margin:0;"></div></div>
                <div style="flex:0.8;"><div class="feature-skeleton-pill" style="width:48px;height:22px;margin:0;"></div></div>
                <div style="flex:1;"><div class="feature-skeleton-line" style="width:68%;height:10px;margin:0;"></div></div>
                <div style="flex:0 0 180px;display:flex;gap:6px;">
                    <div class="feature-skeleton-button" style="width:48px;height:28px;border-radius:5px;"></div>
                    <div class="feature-skeleton-button" style="width:44px;height:28px;border-radius:5px;"></div>
                    <div class="feature-skeleton-button" style="width:76px;height:28px;border-radius:5px;"></div>
                </div>
            </div>
        @endfor
    </div>

    <section class="access-panel access-directory" data-skeleton-content>
        <div class="access-panel-head access-directory-head">
            <div>
                <p class="access-eyebrow">Directory Controls</p>
                <h2>Find an account</h2>
                <p class="access-panel-sub">{{ $users->total() }} account{{ $users->total() === 1 ? '' : 's' }} &middot; Search, filter, and manage user access.</p>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.users') }}" class="user-filters">
            <label class="user-filter-field user-filter-wide">
                <span class="sr-only">Search accounts</span>
                <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Name, resident ID, email, phone, or room" class="user-filter-input">
            </label>
            
            {{-- Status Dropdown --}}
            <div class="user-priority-dropdown" data-priority-dropdown>
                <select name="status" class="user-filter-select-native" aria-hidden="true" tabindex="-1">
                    <option value="all" @selected($status === 'all')>All statuses</option>
                    <option value="active" @selected($status === 'active')>Active</option>
                    <option value="inactive" @selected($status === 'inactive')>Inactive</option>
                </select>
                <button type="button"
                        class="user-operations-filter"
                        aria-haspopup="listbox"
                        aria-expanded="false"
                        data-priority-trigger>
                    <span data-priority-label>
                        @if($status === 'all') All statuses
                        @elseif($status === 'active') Active
                        @elseif($status === 'inactive') Inactive
                        @endif
                    </span>
                    <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="m6 9 6 6 6-6" />
                    </svg>
                </button>
                <div class="user-priority-menu" role="listbox" data-priority-menu hidden>
                    <button type="button" role="option" aria-selected="{{ $status === 'all' ? 'true' : 'false' }}" data-priority-option value="all">All statuses</button>
                    <button type="button" role="option" aria-selected="{{ $status === 'active' ? 'true' : 'false' }}" data-priority-option value="active">Active</button>
                    <button type="button" role="option" aria-selected="{{ $status === 'inactive' ? 'true' : 'false' }}" data-priority-option value="inactive">Inactive</button>
                </div>
            </div>
            
            <input type="hidden" name="role" value="{{ $role }}">
            <button type="submit" class="user-filter-btn">Apply filters</button>
            @if($activeFilterCount)
                <a href="{{ route('admin.users') }}" class="user-filter-clear">Clear</a>
            @endif
        </form>

        <nav class="access-role-tabs" aria-label="Account type" role="tablist">
            @foreach([
                'all' => ['label' => 'All Accounts', 'icon' => 'archive'],
                'resident' => ['label' => 'Residents', 'icon' => 'user'],
                'handyman' => ['label' => 'Staff', 'icon' => 'briefcase'],
                'manager' => ['label' => 'Admin', 'icon' => 'shield'],
            ] as $tabRole => $tab)
                <button type="button"
                        class="access-role-tab {{ $role === $tabRole ? 'is-active' : '' }}"
                        role="tab"
                        aria-selected="{{ $role === $tabRole ? 'true' : 'false' }}"
                        data-account-role-tab="{{ $tabRole }}">
                    @switch($tab['icon'])
                        @case('user')
                            <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 21a8 8 0 0 1 16 0" />
                            </svg>
                            @break
                        @case('briefcase')
                            <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6h4a2 2 0 0 1 2 2v1H8V8a2 2 0 0 1 2-2Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 9h16v9a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V9Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6" />
                            </svg>
                            @break
                        @case('shield')
                            <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3 5 6v5c0 4.5 2.9 8.4 7 10 4.1-1.6 7-5.5 7-10V6l-7-3Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 12 2 2 4-4" />
                            </svg>
                            @break
                        @default
                            <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7h3a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h3" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5h6v4H9z" />
                            </svg>
                    @endswitch
                    {{ $tab['label'] }}
                </button>
            @endforeach
        </nav>

        <div class="access-table-wrap">
            <table class="access-table">
                <thead>
                    <tr>
                        <th><input type="checkbox" data-check-all aria-label="Select all visible accounts"></th>
                        @foreach($sortableColumns as $col => $label)
                            <th>
                                <a href="{{ $sortLink($col) }}"
                                   class="access-sort-link {{ $sort === $col ? 'is-active' : '' }}"
                                   aria-sort="{{ $sort === $col ? ($direction === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                                    {{ $label }}
                                    <span class="access-sort-icon" aria-hidden="true">
                                        @if($sort === $col)
                                            {!! $direction === 'asc' ? '↑' : '↓' !!}
                                        @else
                                            <span class="access-sort-icon-idle">↕</span>
                                        @endif
                                    </span>
                                </a>
                            </th>
                        @endforeach
                        <th class="access-actions-col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr data-account-role="{{ $user->role }}">
                            <td><input form="bulkUserForm" type="checkbox" name="user_ids[]" value="{{ $user->id }}" aria-label="Select {{ $user->name }}"></td>
                            <td>
                                <a href="{{ route('admin.users.show', $user) }}" class="access-person">
                                    <span class="access-avatar">{{ $user->profile_initials }}</span>
                                    <span>
                                        <strong>{{ $user->name }}</strong>
                                        @if($user->must_change_password)
                                            <span class="access-pending-badge">Pending setup</span>
                                        @endif
                                        <small>{{ $user->role === 'resident' ? 'ID '.($user->resident_number ?: 'Unassigned').' · Room '.($user->room_number ?: 'Unassigned') : 'Account #'.$user->id }}</small>
                                    </span>
                                </a>
                            </td>
                            <td>
                                <span class="access-contact">{{ $user->email }}</span>
                                <small>{{ $user->phone_number ?: 'No phone number' }}</small>
                            </td>
                            <td><span class="access-role-text">{{ $user->role_label }}</span></td>
                            <td>
                                <span class="access-status-text {{ $user->is_active ? 'is-active' : 'is-inactive' }}">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <span>{{ $user->last_login_at?->format('M d, Y') ?? 'Never' }}</span>
                                <small>{{ $user->last_login_at?->format('h:i A') ?? 'No sign-in recorded' }}</small>
                            </td>
                            <td>
                                <div class="access-row-actions admin-user-actions-plain">
                                    <a href="{{ route('admin.users.show', $user) }}" class="admin-user-action-link" title="View" aria-label="View {{ $user->name }}">View</a>
                                    <a href="{{ route('admin.users.edit', $user) }}" class="admin-user-action-link" title="Edit" aria-label="Edit {{ $user->name }}">Edit</a>
                                    @if($user->id !== auth()->id())
                                        <form method="POST"
                                              action="{{ route('admin.users.update-status', $user) }}"
                                              data-confirm-message="{{ $user->is_active ? 'Deactivate this account and revoke its active sessions?' : 'Reactivate this account?' }}"
                                              data-prevent-double-submit
                                              data-submitting-text="{{ $user->is_active ? 'Deactivating...' : 'Reactivating...' }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="{{ $user->is_active ? 'inactive' : 'active' }}">
                                            <button type="submit" class="admin-user-action-link {{ $user->is_active ? 'admin-user-action-danger' : '' }}" title="{{ $user->is_active ? 'Deactivate account' : 'Reactivate account' }}">
                                                {{ $user->is_active ? 'Deactivate' : 'Reactivate' }}
                                            </button>
                                        </form>
                                    @else
                                        <span class="admin-user-action-placeholder" aria-hidden="true"></span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <x-admin-empty-state compact icon="archive" title="No matching accounts" description="Adjust the filters or create a new account to begin." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="access-pagination">{{ $users->links() }}</div>
        @endif
    </section>
</div>


<style>
/* Poppins Font Import */
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap');
/* Admin skeleton overrides:use the light admin palette */
body.role-manager [data-feature-skeleton] .feature-skeleton-line, body.role-manager [data-feature-skeleton] .feature-skeleton-pill, body.role-manager [data-feature-skeleton] .feature-skeleton-avatar, body.role-manager [data-feature-skeleton] .feature-skeleton-button {
    background: linear-gradient(90deg, rgba(87, 72, 55, 0.07), rgba(87, 72, 55, 0.15), rgba(87, 72, 55, 0.07));
    background-size: 220% 100%;
    animation: skeleton-shimmer 1.15s ease-in-out infinite;
}
body.role-manager [data-feature-skeleton] {
    border: 1px solid #4f4336;
    border-radius: 12px;
    background: #ffffff;
    box-shadow: 0 4px 14px rgba(84, 61, 37, 0.10);
    overflow: hidden;
}
body.role-manager:has(.access-page) {
    --admin-bg-top: #ede6db;
    --admin-bg-mid: #e7ded1;
    --admin-bg-bottom: #ede6db;
}
.access-page {
    max-width: 1580px;
    margin: 0 auto;
    display: grid;
    gap: 18px;
}
.access-hero, .access-panel, .access-credential {
    border: 1px solid #e3d8ca;
    border-radius: 14px;
}
.access-hero {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
    padding: 24px 26px;
    background: linear-gradient(115deg, #241c14, #493522);
}
.access-kicker, .access-eyebrow {
    margin: 0 0 6px;
    color: #b97925;
    font-size: .68rem;
    font-weight: 800;
    letter-spacing: .18em;
    text-transform: uppercase;
}
.access-hero h1, .access-panel h2 {
    margin: 0;
    font-family: 'Playfair Display', serif;
}
.admin-user-primary-action.access-primary {
     /* --- Layout & Structure (From Main Button) --- */
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 45px;
    padding: 0 20px;
    border: none; /* Removed the harsh red/brown border to favor the gradient */
    border-radius: 777px;
    white-space: nowrap;

    /* --- Typography (From Main Button) --- */
    font-size: .74rem;
    font-weight: 800;
    letter-spacing: .075em;
    line-height: 1;
    text-transform: uppercase;
    text-decoration: none;

    /* --- Color & Depth (From "Better" Button) --- */
    background: linear-gradient(90deg, #B8842F 0%, #D6A85B 100%);
    color: #FFFFFF;
    box-shadow: 0 12px 28px rgba(199, 150, 69, 0.3);

    /* --- Smooth Transitions (From "Better" Button) --- */
    transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.3s ease;
}

/* --- The Combined Premium Hover State --- */
.admin-user-primary-action.access-primary:hover,
.admin-user-primary-action.access-primary:focus-visible {
    outline: none;
    /* Clean CSS alternative to the JS onmouseover */
    transform: translateY(-3px); 
    box-shadow: 0 20px 40px rgba(199, 150, 69, 0.4);
    
    /* Optional: If you want a slight color shifting glow on hover, 
       we can subtly shift the gradient look by brightening it slightly */
    filter: brightness(1.05); 
}

/* --- Clean Reset for Active (Click) State --- */
.admin-user-primary-action.access-primary:active {
    transform: translateY(-1px);
    box-shadow: 0 8px 16px rgba(199, 150, 69, 0.3);
}


.access-hero h1 {
    color: #F8F3EA;
    font-size: clamp(2.25rem, 4vw, 3.75rem);
    line-height: 1.02;
    margin-bottom: 1.25rem;
}
.access-hero p:not(.access-kicker) {
    max-width: 760px;
    margin: 8px 0 0;
    color: rgba(255, 255, 255, 0.82);
    line-height: 1.6;
}
.access-primary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 auto;
    padding: 0 18px;
    text-decoration: none;
}
.access-credential {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 18px;
    padding: 14px 16px;
    background: #f7eedf;
    color: #68400f;
}
.access-credential p {
    margin: 3px 0 0;
    font-size: .84rem;
}
.access-credential code {
    padding: 8px 12px;
    border: 1px solid #d2ae7b;
    border-radius: 8px;
    background: #fffdf9;
    font-weight: 800;
}
.access-stats {
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    gap: 10px;
}
.access-stats article {
    padding: 14px 16px;
    border: 1px solid #4f4336;
    border-radius: 10px;
    background: #ffffff;
    box-shadow: 0 4px 14px rgba(84, 61, 37, 0.10);
}
.access-stats span, .access-table small {
    display: block;
    color: #817467;
    font-size: .74rem;
}
.access-stats strong {
    display: block;
    margin-top: 5px;
    color: #3c342d;
    font-size: 1.4rem;
}
.access-panel {
    overflow: hidden;
    background: #f7f6f3;
    box-shadow: 0 4px 14px rgba(84, 61, 37, 0.10);
}
.access-panel-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 16px 18px;
    border-bottom: 1px solid #e3d8ca;
}
.access-panel h2 {
    color: #3c342d;
    font-size: 1.35rem;
}
.access-clear {
    color: #825213;
    font-size: .82rem;
    font-weight: 700;
}
.access-role-tabs {
    display: flex;
    gap: 3px;
    overflow-x: auto;
    padding: 0 18px;
    border-bottom: 1px solid #e3d8ca;
}
.access-role-tab {
    display: inline-flex;
    min-width: max-content;
    padding: 12px 14px 10px;
    border: 0;
    border-bottom: 2px solid transparent;
    background: transparent;
    color: #786b60;
    cursor: pointer;
    font-family: inherit;
    font-size: .78rem;
    font-weight: 600;
    text-decoration: none;
}
.access-role-tab:hover {
    color: #68400f;
    background: rgba(180, 119, 33, .04);
}
.access-role-tab.is-active {
    border-bottom-color: #b47721;
    color: #b47721;
}
.access-table-wrap {
    overflow-x: auto;
}
.access-table {
    width: 100%;
    border-collapse: collapse;
    color: #4d443c;
    font-size: .82rem;
}
.access-table th {
    padding: 10px 12px;
    background: #f3ede5;
    color: #75695d;
    font-size: .67rem;
    letter-spacing: .1em;
    text-align: left;
    text-transform: uppercase;
}
.access-sort-link {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    color: inherit;
    text-decoration: none;
    white-space: nowrap;
}
.access-sort-link:hover {
    color: #3c342d;
}
.access-sort-link.is-active {
    color: #68400f;
}
.access-sort-icon {
    font-size: .8rem;
    opacity: 0.9;
}
.access-sort-icon-idle {
    opacity: 0.35;
    font-size: .75rem;
}
.access-table td {
    padding: 11px 12px;
    border-top: 1px solid #e3d8ca;
    vertical-align: middle;
}
.access-table tbody tr:hover {
    background: #ede6db;
}
.access-person {
    display: flex;
    align-items: center;
    gap: 9px;
    color: inherit;
    text-decoration: none;
    min-width: 180px;
}
.access-person strong {
    display: block;
    color: #3c342d;
}
.access-pending-badge {
    display: inline-flex;
    align-items: center;
    padding: 1px 7px;
    margin-left: 6px;
    border-radius: 999px;
    background: #fdf3d0;
    border: 1px solid #e2c97e;
    color: #7a5c1e;
    font-size: .65rem;
    font-weight: 700;
    vertical-align: middle;
}
.access-avatar {
    display: grid;
    width: 32px;
    height: 32px;
    place-items: center;
    border-radius: 50%;
    background: #ead8bd;
    color: #68400f;
    font-size: .7rem;
    font-weight: 800;
}
.access-contact {
    display: block;
    min-width: 175px;
    color: #4d443c;
}
.access-role-text, .access-status-text {
    color: #5e554a;
    font-size: .78rem;
    font-weight: 500;
    white-space: nowrap;
}
.access-status-text.is-active {
    color: #356140;
}
.access-status-text.is-inactive {
    color: #8f342e;
}
.access-actions-col {
    min-width: 236px;
}
.access-row-actions {
    display: flex;
    flex-wrap: nowrap;
    align-items: center;
    gap: 6px;
    min-width: 236px;
}
.access-row-actions form {
    display: inline-flex;
}
.admin-user-row-action {
    display: inline-flex;
    min-height: 30px;
    align-items: center;
    padding: 0 8px !important;
    text-decoration: none;
    white-space: nowrap;
}
.admin-user-row-action--clean {
    padding: 0 !important;
    font-weight: 400;
    background: transparent !important;
    border: 0 !important;
}
.access-action-danger {
    border-color: #dda29d !important;
    background: #f7dfdc !important;
    color: #8f342e !important;
}
.access-action-success {
    border-color: #9fc6a8 !important;
    background: #deeee1 !important;
    color: #356140 !important;
}
.access-action-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 98px;
    padding: 6px 10px !important;
    border-radius: 999px;
}
/* Plain-text action links for Account Registry */
.admin-user-action-link {
    display: inline-flex;
    width: 100%;
    min-height: 30px;
    align-items: center;
    justify-content: flex-start;
    background: none;
    border: none;
    padding: 4px 6px;
    margin: 0;
    color: #4a4a4a;
    font-family: 'Poppins', sans-serif;
    font-size: 0.85rem;
    font-weight: 400;
    line-height: 1.2;
    text-decoration: none;
    cursor: pointer;
    border-radius: 4px;
    transition: all 0.2s ease;
    white-space: nowrap;
}
.admin-user-action-link:hover {
    background: #f0f0f0;
    /* ← Subtle background on hover */
    color: #000;
}
.admin-user-action-danger {
    color: #c92a2a;
}
.admin-user-action-danger:hover {
    background: #fee;
    /* ← Light red background on hover */
    color: #a00;
}
.admin-user-actions-plain {
    display: grid;
    grid-template-columns: 58px 58px minmax(92px, 1fr);
    align-items: center;
    column-gap: 8px;
    min-width: 240px;
}
.admin-user-actions-plain form {
    display: block;
    margin: 0;
}
.admin-user-actions-plain form .admin-user-action-link {
    font-family: inherit;
}
.admin-user-action-placeholder {
    display: block;
    min-height: 30px;
}
.access-empty {
    padding: 42px 20px;
    text-align: center;
}
.access-empty strong {
    color: #3c342d;
    font-size: 1rem;
}
.access-empty p {
    margin: 5px 0 0;
    color: #817467;
}
.access-pagination {
    padding: 14px 18px;
    border-top: 1px solid #e3d8ca;
}
@media (max-width:980px) {

}
@media (max-width:760px) {
    .access-hero, .access-panel-head, .access-credential {
        align-items: flex-start;
        flex-direction: column;
    }
    .access-primary {
        width: 100%;
    }
    .access-stats {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    .access-directory-head {
        align-items: stretch;
    }
}
@media (max-width:768px) {
    .access-page {
        gap: 18px;
    }
    .access-directory {
        padding: 16px !important;
    }
    .access-panel-head {
        gap: 8px;
        margin-bottom: 14px;
    }
    .access-panel-head h2 {
        font-size: 1.22rem;
        line-height: 1.15;
    }
    .access-panel-sub {
        font-size: 0.9rem;
        line-height: 1.45;
    }
    .access-role-tabs {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px;
        padding: 0;
        border: 0;
        margin: 14px 0;
    }
    .access-role-tab {
        min-height: 46px;
        justify-content: center;
        border: 1px solid #e5d8c8;
        border-radius: 14px;
        background: #fffaf2;
        font-size: 0.82rem;
    }
    .access-role-tab.is-active {
        border-color: rgba(180, 119, 33, 0.44);
        background: #f6ead6;
    }
    .access-table-wrap {
        overflow: visible;
        border-radius: 0;
    }
    .access-table,
    .access-table thead,
    .access-table tbody,
    .access-table tr,
    .access-table td {
        display: block;
        width: 100%;
    }
    .access-table {
        min-width: 0 !important;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 0.92rem;
    }
    .access-table thead {
        position: absolute;
        width: 1px;
        height: 1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
    }
    .access-table tbody {
        display: grid;
        gap: 12px;
    }
    .access-table tbody tr {
        position: relative;
        display: grid;
        gap: 10px;
        padding: 16px;
        border: 1px solid #e5d8c8;
        border-radius: 16px;
        background: #fffdf9;
        box-shadow: 0 10px 22px rgba(79, 58, 44, 0.08);
    }
    .access-table tbody tr:hover {
        background: #fffdf9;
    }
    .access-table td {
        padding: 0 !important;
        border: 0;
    }
    .access-table td:first-child {
        position: absolute;
        top: 14px;
        right: 14px;
        width: auto;
    }
    .access-table td:not(:first-child):not(:last-child)::before {
        display: block;
        margin-bottom: 4px;
        color: #8b7d70;
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }
    .access-table td:nth-child(2)::before {
        content: "Account";
    }
    .access-table td:nth-child(3)::before {
        content: "Contact";
    }
    .access-table td:nth-child(4)::before {
        content: "Role";
    }
    .access-table td:nth-child(5)::before {
        content: "Status";
    }
    .access-table td:nth-child(6)::before {
        content: "Last login";
    }
    .access-person {
        min-width: 0;
        padding-right: 34px;
    }
    .access-avatar {
        width: 42px;
        height: 42px;
        font-size: 0.78rem;
    }
    .access-person strong {
        font-size: 1rem;
        line-height: 1.2;
    }
    .access-contact,
    .access-role-text,
    .access-status-text {
        min-width: 0;
        white-space: normal;
        font-size: 0.92rem;
    }
    .admin-user-actions-plain {
        display: grid;
        grid-template-columns: 1fr;
        gap: 8px;
        min-width: 0;
        margin-top: 4px;
    }
    .admin-user-action-link {
        min-height: 44px;
        justify-content: center;
        border: 1px solid #e5d8c8;
        border-radius: 12px;
        background: #fff8ee;
        color: #4d443c;
        font-size: 0.9rem;
        font-weight: 700;
    }
    .admin-user-action-danger {
        border-color: #edc4bf;
        background: #fff0ee;
        color: #9e342d;
    }
    .access-pagination {
        margin-top: 12px;
        padding: 12px 0 0;
        border-top: 0;
    }
}

/* ==========================================================================
   USER PAGE SEARCH AND DROPDOWN (Matching Concern Page Style)
   ========================================================================== */

/* Search and filter bar */
/* Update this block in your <style> section */
/* Update your .user-filters styles to this */
.user-filters {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    padding: 12px 18px;
    
    /* Center it layout-wise with proper margins */
    margin: 0 23px 2px 23px; /* Adds 18px margins to match .access-role-tabs */
    
    border: 1px solid #e3d8ca; /* Lighter border from your reference panel */
    border-radius: 10px;
    background: #fbf8f3;
    box-shadow: 0 4px 14px rgba(84, 61, 37, 0.06);
    align-items: center;
    position: relative;
    z-index: 100;
}

.user-filter-field {
    display: flex;
    flex-direction: column;
}

.user-filter-wide {
    flex: 1 1 240px;
}

.user-filter-input {
    min-height: 34px;
    padding: 0 10px;
    border: 1px solid #dfd5c8;
    border-radius: 7px;
    background: #fffdf9;
    color: #453b33;
    font: inherit;
    font-size: .8rem;
    outline: none;
}

.user-filter-input:focus {
    border-color: #c6954a;
    box-shadow: 0 0 0 3px rgba(198, 149, 74, .12);
}

.user-filter-select-native {
    position: absolute;
    width: 1px;
    height: 1px;
    overflow: hidden;
    opacity: 0;
    pointer-events: none;
}

/* Dropdown wrapper */
.user-filters .user-priority-dropdown {
    position: relative;
    display: inline-flex;
    z-index: 40;
}

/* Dropdown button */
.user-filters .user-operations-filter {
    display: inline-flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    min-height: 45px;           /* Strict match with apply button */
    padding: 0 20px;
    border: 1px solid #dfd5c8;
    border-radius: 777px;       /* Uniform fully rounded pill shapes */
    white-space: nowrap;
    font-size: .74rem;
    font-weight: 800;
    letter-spacing: .075em;
    text-transform: uppercase;
    background: #fffdf9;
    color: #453b33;
    cursor: pointer;
    transition: all 0.2s ease;
}

/* Dropdown button chevron */
.user-filters .user-operations-filter svg {
    width: 16px;
    height: 16px;
    flex: 0 0 auto;
    fill: currentColor;
    pointer-events: none;
    transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Dropdown button hover/focus */
.user-filters .user-operations-filter:hover,
.user-filters .user-operations-filter:focus-visible {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(199, 150, 69, 0.18);
    border-color: #c6954a;
    background: #fffdf9;
    color: #453b33;
    outline: none;
}

.user-filters .user-operations-filter:hover svg {
    transform: translateY(1px);
}

/* Dropdown button active */
.user-filters .user-operations-filter:active {
    transform: translateY(0);
    box-shadow: 0 2px 8px rgba(199, 150, 69, 0.12);
    border-color: #b8842f;
}

/* Dropdown open state */
.user-filters .user-priority-dropdown.is-open .user-operations-filter {
    transform: translateY(-1px);
    background: #fffdf9;
    color: #453b33;
    box-shadow: 0 4px 12px rgba(199, 150, 69, 0.18);
    border-color: #c6954a;
    filter: brightness(1);
}

.user-filters .user-priority-dropdown.is-open .user-operations-filter svg {
    transform: rotate(180deg) !important;
}

/* Dropdown menu */
.user-filters .user-priority-menu {
    position: absolute;
    top: calc(100% + 6px);
    right: 0;
    z-index: 50;
    width: 190px;
    overflow: hidden;
    padding: 6px;
    border: 1px solid rgba(107, 79, 58, 0.16);
    border-radius: 14px;
    background: #fffaf5;
    box-shadow: 0 16px 32px rgba(47, 39, 31, 0.18);
}

.user-filters .user-priority-menu[hidden] {
    display: none;
}

.user-filters .user-priority-menu button {
    display: flex;
    width: 100%;
    min-height: 36px;
    align-items: center;
    padding: 0 11px;
    border: 0;
    border-radius: 9px;
    background: transparent;
    color: #4d3b2e;
    font-size: 0.78rem;
    font-weight: 700;
    text-align: left;
    cursor: pointer;
    transition: background-color 0.16s ease, color 0.16s ease;
}

.user-filters .user-priority-menu button:hover,
.user-filters .user-priority-menu button:focus-visible {
    background: #fff2e8;
    color: #8f2929;
    outline: none;
}

.user-filters .user-priority-menu button[aria-selected="true"] {
    background: #f2dfd2;
    color: #7a4f16;
}

/* Apply button - using same style as concern page */
.user-filter-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 45px;
    padding: 0 20px;
    border: none;
    border-radius: 777px;
    white-space: nowrap;
    font-size: .74rem;
    font-weight: 800;
    letter-spacing: .075em;
    line-height: 1;
    text-transform: uppercase;
    text-decoration: none;
    background: linear-gradient(90deg, #B8842F 0%, #D6A85B 100%);
    color: #FFFFFF;
    box-shadow: 0 12px 28px rgba(199, 150, 69, 0.3);
    transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.3s ease;
    cursor: pointer;
}

.user-filter-btn:hover,
.user-filter-btn:focus-visible {
    outline: none;
    transform: translateY(-3px);
    box-shadow: 0 20px 40px rgba(199, 150, 69, 0.4);
    filter: brightness(1.05);
}

.user-filter-btn:active {
    transform: translateY(-1px);
    box-shadow: 0 8px 16px rgba(199, 150, 69, 0.3);
}

/* Clear link */
.user-filter-clear {
    color: #825213;
    font-size: .82rem;
    font-weight: 700;
    text-decoration: none;
    padding: 12px 16px;
}

.user-filter-clear:hover {
    text-decoration: underline;
}

/* Responsive styles for user filters */
@media(max-width:680px) {
    .user-filters {
        flex-direction: column;
    }
    .user-filter-wide {
        width: 100%;
    }
}

</style>

<script>
const accountRows = Array.from(document.querySelectorAll('[data-account-role]'));
const accountTabs = document.querySelectorAll('[data-account-role-tab]');
const checkAllAccounts = document.querySelector('[data-check-all]');
const accessFiltersForm = document.querySelector('.user-filters');
const roleFilterInput = accessFiltersForm?.querySelector('input[name="role"]');
const bulkActionSelect = document.querySelector('#bulkUserForm select[name="bulk_action"]');

const visibleAccountRows = () => accountRows.filter((row) => !row.hidden);

checkAllAccounts?.addEventListener('change', (event) => {
    visibleAccountRows().forEach((row) => {
        row.querySelector('input[name="user_ids[]"]').checked = event.target.checked;
    });
});

// User page dropdown functionality
document.querySelectorAll('.user-priority-dropdown').forEach((dropdown) => {
    const trigger = dropdown.querySelector('[data-priority-trigger]');
    const menu = dropdown.querySelector('[data-priority-menu]');
    const label = dropdown.querySelector('[data-priority-label]');
    const hiddenSelect = dropdown.querySelector('.user-filter-select-native');
    const options = dropdown.querySelectorAll('[data-priority-option]');
    
    if (!trigger || !menu || !label || !hiddenSelect) return;
    
    // Toggle dropdown
    trigger.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const isOpen = dropdown.classList.contains('is-open');
        
        // Close all other dropdowns
        document.querySelectorAll('.user-priority-dropdown.is-open').forEach(d => {
            if (d !== dropdown) {
                d.classList.remove('is-open');
                d.querySelector('[data-priority-menu]').hidden = true;
                d.querySelector('[aria-expanded]').setAttribute('aria-expanded', 'false');
            }
        });
        
        // Toggle current dropdown
        if (isOpen) {
            dropdown.classList.remove('is-open');
            menu.hidden = true;
            trigger.setAttribute('aria-expanded', 'false');
        } else {
            dropdown.classList.add('is-open');
            menu.hidden = false;
            trigger.setAttribute('aria-expanded', 'true');
        }
    });
    
    // Handle option selection
    options.forEach(option => {
        option.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const value = this.getAttribute('value');
            const text = this.textContent.trim();
            
            // Update the label
            label.textContent = text;
            
            // Update the hidden select value
            hiddenSelect.value = value;
            
            // Update aria-selected on all options
            options.forEach(opt => {
                opt.setAttribute('aria-selected', opt.getAttribute('value') === value ? 'true' : 'false');
            });
            
            // Close the dropdown
            dropdown.classList.remove('is-open');
            menu.hidden = true;
            trigger.setAttribute('aria-expanded', 'false');
            
            // Submit the form
            trigger.closest('form').submit();
        });
    });
});

// Close dropdowns when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.user-priority-dropdown')) {
        document.querySelectorAll('.user-priority-dropdown.is-open').forEach(dropdown => {
            dropdown.classList.remove('is-open');
            dropdown.querySelector('[data-priority-menu]').hidden = true;
            dropdown.querySelector('[aria-expanded]').setAttribute('aria-expanded', 'false');
        });
    }
});

const applyRoleFilter = (role) => {
    if (roleFilterInput) {
        roleFilterInput.value = role;
    }
    accountTabs.forEach((tab) => {
        const active = tab.dataset.accountRoleTab === role;
        tab.classList.toggle('is-active', active);
        tab.setAttribute('aria-selected', active ? 'true' : 'false');
    });
    accountRows.forEach((row) => {
        row.hidden = role !== 'all' && row.dataset.accountRole !== role;
        const checkbox = row.querySelector('input[name="user_ids[]"]');
        if (checkbox && row.hidden) {
            checkbox.checked = false;
        }
    });
    if (checkAllAccounts) {
        checkAllAccounts.checked = false;
    }
    syncBulkBtn();
};

accountTabs.forEach((tab) => {
    tab.addEventListener('click', (event) => {
        // Explicitly prevent all default behavior and propagation
        event.preventDefault();
        event.stopImmediatePropagation();
        event.stopPropagation();
        
        const role = tab.dataset.accountRoleTab || 'all';
        applyRoleFilter(role);
        return false;
    });
});

const bulkApplyBtn = document.querySelector('#bulkUserForm [type="submit"]');
const syncBulkBtn = () => {
    const anyChecked = visibleAccountRows().some(row =>
        row.querySelector('input[name="user_ids[]"]')?.checked
    );
    if (bulkApplyBtn) bulkApplyBtn.disabled = !anyChecked || !bulkActionSelect?.value;
};
if (bulkApplyBtn) bulkApplyBtn.disabled = true;
document.querySelectorAll('input[name="user_ids[]"]').forEach(cb => cb.addEventListener('change', syncBulkBtn));
checkAllAccounts?.addEventListener('change', syncBulkBtn);
bulkActionSelect?.addEventListener('change', syncBulkBtn);
applyRoleFilter(roleFilterInput?.value || 'all');
</script>
</x-app-layout>
