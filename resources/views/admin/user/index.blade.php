<x-app-layout>
<div class="admin-user-page space-y-6">

    {{-- HEADER --}}
    <div class="admin-user-hero flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="admin-user-kicker">Access Control</div>
            <h1 class="text-2xl md:text-3xl font-bold" style="color: #F8F3EA; font-family: 'Playfair Display', serif;">
                Resident Directory
            </h1>
            <p class="text-sm text-gray-400 mt-1">Manage residents, staff, administrators, rooms, and account access from one clean workspace.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" 
           class="admin-user-primary-action inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition"
           style="background: linear-gradient(135deg, #B8842F, #D6A85B); color: white; text-decoration: none;"
           onmouseover="this.style.transform='translateY(-1px)'"
           onmouseout="this.style.transform='translateY(0)'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add New User
        </a>
    </div>

    {{-- STATS CARDS --}}
    <div class="admin-user-stats grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="admin-user-stat rounded-lg p-3" style="background: #1F2023; border: 1px solid #2A2C30;">
            <div class="admin-user-stat-top">
                <span class="admin-user-stat-icon">R</span>
                <div class="text-xl font-bold" style="color: #D6A85B;">{{ $totalResidents }}</div>
            </div>
            <div class="text-xs text-gray-500">Total Residents</div>
        </div>
        <div class="admin-user-stat rounded-lg p-3" style="background: #1F2023; border: 1px solid #2A2C30;">
            <div class="admin-user-stat-top">
                <span class="admin-user-stat-icon">S</span>
                <div class="text-xl font-bold" style="color: #F0A550;">{{ $totalHandymen }}</div>
            </div>
            <div class="text-xs text-gray-500">Staff Accounts</div>
        </div>
        <div class="admin-user-stat rounded-lg p-3" style="background: #1F2023; border: 1px solid #2A2C30;">
            <div class="admin-user-stat-top">
                <span class="admin-user-stat-icon">A</span>
                <div class="text-xl font-bold" style="color: #E07060;">{{ $totalAdmins }}</div>
            </div>
            <div class="text-xs text-gray-500">Administrators</div>
        </div>
        <div class="admin-user-stat rounded-lg p-3" style="background: #1F2023; border: 1px solid #2A2C30;">
            <div class="admin-user-stat-top">
                <span class="admin-user-stat-icon">ON</span>
                <div class="text-xl font-bold" style="color: #5A8A5A;">{{ $activeUsers }}</div>
            </div>
            <div class="text-xs text-gray-500">Active Users</div>
        </div>
    </div>

    {{-- SEARCH AND FILTER --}}
    <div class="admin-user-toolbar rounded-lg overflow-hidden" style="background: #1F2023; border: 1px solid #2A2C30;">
        <div class="p-4 flex flex-col lg:flex-row lg:items-center gap-3">
            <form method="GET" action="{{ route('admin.users') }}" class="flex-1 flex gap-2">
                <input type="text" name="search" value="{{ $search ?? '' }}" 
                       placeholder="Search by name, email, or room..." 
                       class="flex-1 px-4 py-2 rounded-lg text-sm"
                       style="background: #2A2C30; border: 1px solid #3A342D; color: #F8F3EA;">
                <button type="submit" class="px-4 py-2 rounded-lg text-sm font-medium transition"
                        style="background: rgba(214,168,91,0.15); color: #D6A85B;">
                    Search
                </button>
            </form>
            <div class="admin-user-filter-tabs flex gap-2">
                <a href="{{ route('admin.users', ['role' => 'all']) }}" 
                   class="px-3 py-2 rounded-lg text-xs transition {{ !$roleFilter || $roleFilter === 'all' ? 'bg-[rgba(214,168,91,0.2)] text-[#D6A85B]' : 'text-gray-400' }}"
                   style="text-decoration: none;">All</a>
                <a href="{{ route('admin.users', ['role' => 'resident']) }}" 
                   class="px-3 py-2 rounded-lg text-xs transition {{ $roleFilter === 'resident' ? 'bg-[rgba(214,168,91,0.2)] text-[#D6A85B]' : 'text-gray-400' }}"
                   style="text-decoration: none;">Residents</a>
                <a href="{{ route('admin.users', ['role' => 'handyman']) }}" 
                   class="px-3 py-2 rounded-lg text-xs transition {{ $roleFilter === 'handyman' ? 'bg-[rgba(214,168,91,0.2)] text-[#D6A85B]' : 'text-gray-400' }}"
                   style="text-decoration: none;">Handymen</a>
                <a href="{{ route('admin.users', ['role' => 'manager']) }}" 
                   class="px-3 py-2 rounded-lg text-xs transition {{ $roleFilter === 'manager' ? 'bg-[rgba(214,168,91,0.2)] text-[#D6A85B]' : 'text-gray-400' }}"
                   style="text-decoration: none;">Admins</a>
            </div>
        </div>
    </div>

    {{-- USERS TABLE --}}
    <div class="admin-user-table-card rounded-xl overflow-hidden" style="background: #1F2023; border: 1px solid #2A2C30;">
        <div class="admin-user-table-head">
            <div>
                <h2>Accounts</h2>
                <p>{{ $users->total() }} user{{ $users->total() === 1 ? '' : 's' }} found</p>
            </div>
            <span class="admin-user-table-badge">{{ ucfirst($roleFilter && $roleFilter !== 'all' ? $roleFilter : 'All Roles') }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="admin-user-table w-full text-sm">
                <thead>
                    <tr style="border-bottom: 1px solid #2A2C30;">
                        <th class="p-3 text-left text-xs font-medium text-gray-500">Room / Info</th>
                        <th class="p-3 text-left text-xs font-medium text-gray-500">Name</th>
                        <th class="p-3 text-left text-xs font-medium text-gray-500">Email</th>
                        <th class="p-3 text-left text-xs font-medium text-gray-500">Role</th>
                        <th class="p-3 text-left text-xs font-medium text-gray-500">Status</th>
                        <th class="p-3 text-left text-xs font-medium text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr style="border-bottom: 1px solid #2A2C30;">
                            <td class="p-3">
                                @if($user->role === 'resident')
                                    <div class="flex items-center gap-2">
                                        <span class="text-lg">🏠</span>
                                        <span class="font-mono text-sm" style="color: #D6A85B;">{{ $user->room_number ?? '—' }}</span>
                                    </div>
                                @elseif($user->role === 'handyman')
                                    <div class="flex items-center gap-2">
                                        <span class="text-lg">🔧</span>
                                        <span class="text-sm text-gray-400">Staff</span>
                                    </div>
                                @else
                                    <div class="flex items-center gap-2">
                                        <span class="text-lg">👑</span>
                                        <span class="text-sm text-gray-400">Admin</span>
                                    </div>
                                @endif
                            </td>
                            <td class="p-3">
                                <div class="admin-user-identity">
                                    <span class="admin-user-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                    <span>
                                        <span class="font-medium text-white">{{ $user->name }}</span>
                                        <span class="text-xs text-gray-500">ID: {{ $user->id }}</span>
                                    </span>
                                </div>
                            </td>
                            <td class="p-3">
                                <div class="text-gray-300">{{ $user->email }}</div>
                                <div class="text-xs text-gray-500">Joined {{ $user->created_at->format('M d, Y') }}</div>
                            </td>
                            <td class="p-3">
                                <span class="text-xs px-2 py-1 rounded-full"
                                      style="background: {{ 
                                          $user->role === 'manager' ? 'rgba(214,168,91,0.15)' : 
                                          ($user->role === 'handyman' ? 'rgba(240,165,80,0.15)' : 
                                          'rgba(90,138,90,0.15)') 
                                      }}; color: {{ 
                                          $user->role === 'manager' ? '#D6A85B' : 
                                          ($user->role === 'handyman' ? '#F0A550' : '#5A8A5A') 
                                      }};">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="p-3">
                                @if($user->is_active)
                                    <span class="text-xs text-[#5A8A5A]">● Active</span>
                                @else
                                    <span class="text-xs text-[#E07060]">○ Inactive</span>
                                @endif
                            </td>
                            <td class="p-3">
                                <div class="flex gap-2">
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                       title="Edit user"
                                       aria-label="Edit {{ $user->name }}"
                                       class="p-1.5 rounded-lg transition hover:bg-[rgba(214,168,91,0.1)]"
                                       style="color: #D6A85B; text-decoration: none;">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    @if($user->id !== auth()->id())
                                        <button onclick="openResetModal({{ $user->id }}, '{{ $user->name }}')"
                                                title="Reset password"
                                                aria-label="Reset password for {{ $user->name }}"
                                                class="p-1.5 rounded-lg transition hover:bg-[rgba(240,165,80,0.1)]"
                                                style="color: #F0A550;">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v-2l4.257-4.257A6 6 0 0121 9z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v-2l4.257-4.257A6 6 0 0121 9z"></path>
                                            </svg>
                                        </button>
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" 
                                              onsubmit="return confirm('Delete {{ $user->name }}? This will also delete their tickets and bookings.')"
                                              style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    title="Delete user"
                                                    aria-label="Delete {{ $user->name }}"
                                                    class="p-1.5 rounded-lg transition hover:bg-[rgba(224,112,96,0.1)]"
                                                    style="color: #E07060;">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-gray-500">
                                No users found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t" style="border-color: #2A2C30;">
            {{ $users->appends(request()->query())->links() }}
        </div>
    </div>

</div>

{{-- RESET PASSWORD MODAL --}}
<div id="resetModal" class="fixed inset-0 bg-black/70 hidden items-center justify-center z-50 backdrop-blur-sm" style="display: none;">
    <div style="background: linear-gradient(135deg, #2A2C30 0%, #1F2023 100%); border: 1px solid #3A342D; border-radius: 24px; padding: 28px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); max-width: 90vw; width: 380px;">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-white font-['Playfair_Display']">Reset Password</h3>
            <button onclick="closeResetModal()" class="text-2xl text-gray-500 hover:text-white transition">&times;</button>
        </div>
        <p class="text-sm text-gray-400 mb-4">Reset password for <span id="resetUserName" class="text-[#D6A85B]"></span></p>
        <p class="text-xs text-gray-500 mb-4">Password will be reset to: <span class="text-[#D6A85B]">password123</span></p>
        <div class="flex gap-3">
            <button onclick="closeResetModal()" class="flex-1 px-4 py-2 rounded-lg font-medium transition" style="background: rgba(168,159,145,0.1); color: #B0A898;">Cancel</button>
            <form id="resetForm" method="POST" style="flex: 1;">
                @csrf
                <button type="submit" class="w-full px-4 py-2 rounded-lg font-medium transition" style="background: linear-gradient(135deg, #B8842F, #D6A85B); color: white;">Reset Password</button>
            </form>
        </div>
    </div>
</div>

<script>
function openResetModal(userId, userName) {
    document.getElementById('resetUserName').textContent = userName;
    document.getElementById('resetForm').action = `/admin/users/${userId}/reset-password`;
    document.getElementById('resetModal').style.display = 'flex';
}

function closeResetModal() {
    document.getElementById('resetModal').style.display = 'none';
}

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') closeResetModal();
});
</script>

<style>
.admin-user-page {
    max-width: 1580px;
    margin: 0 auto;
}

.admin-user-hero {
    padding: 28px 32px;
    border-radius: 20px;
    background: linear-gradient(120deg, #15120d 0%, #211c15 52%, #2c2419 100%);
    border: 1px solid rgba(214,168,91,0.22);
    box-shadow: 0 18px 36px rgba(72,48,24,0.16);
}

.admin-user-kicker {
    margin-bottom: 8px;
    color: #d6a85b;
    font-size: 0.78rem;
    font-weight: 800;
    letter-spacing: 0.18em;
    text-transform: uppercase;
}

.admin-user-hero h1,
.admin-user-hero p {
    color: #f8f3ea !important;
}

.admin-user-primary-action {
    min-height: 44px;
    border-radius: 999px !important;
    box-shadow: 0 12px 24px rgba(184,132,47,0.22);
}

.admin-user-stat {
    min-height: 112px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    border-radius: 18px !important;
    padding: 18px !important;
}

.admin-user-stat-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}

.admin-user-stat-icon {
    width: 38px;
    height: 38px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: rgba(214,168,91,0.12);
    color: #d6a85b;
    font-size: 0.72rem;
    font-weight: 800;
    letter-spacing: 0.06em;
}

.admin-user-toolbar {
    border-radius: 18px !important;
}

.admin-user-filter-tabs {
    flex-wrap: wrap;
}

.admin-user-filter-tabs a {
    border: 1px solid rgba(214,168,91,0.12);
    min-height: 38px;
    display: inline-flex;
    align-items: center;
}

.admin-user-table-card {
    border-radius: 20px !important;
}

.admin-user-table-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 14px;
    padding: 20px 22px;
    border-bottom: 1px solid rgba(214,168,91,0.12);
}

.admin-user-table-head h2 {
    margin: 0;
    color: #f8f3ea;
    font-family: 'Playfair Display', serif;
    font-size: 1.35rem;
}

.admin-user-table-head p {
    margin: 4px 0 0;
    color: #afa18f;
    font-size: 0.86rem;
}

.admin-user-table-badge {
    padding: 8px 12px;
    border-radius: 999px;
    background: rgba(214,168,91,0.10);
    border: 1px solid rgba(214,168,91,0.16);
    color: #d6a85b;
    font-size: 0.72rem;
    font-weight: 800;
    letter-spacing: 0.12em;
    text-transform: uppercase;
}

.admin-user-table th {
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.admin-user-table tbody tr {
    transition: background 0.18s ease;
}

.admin-user-table tbody tr:hover {
    background: rgba(214,168,91,0.055) !important;
}

.admin-user-identity {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 190px;
}

.admin-user-identity > span:last-child {
    display: flex;
    flex-direction: column;
    min-width: 0;
}

.admin-user-avatar {
    width: 34px;
    height: 34px;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    background: linear-gradient(135deg, rgba(214,168,91,0.26), rgba(214,168,91,0.10));
    border: 1px solid rgba(214,168,91,0.18);
    color: #f8f3ea;
    font-size: 0.82rem;
    font-weight: 800;
}

@media (max-width: 640px) {
    .admin-user-hero,
    .admin-user-table-head {
        padding: 20px;
    }

    .admin-user-table-head {
        align-items: flex-start;
        flex-direction: column;
    }
}
</style>
</x-app-layout>
