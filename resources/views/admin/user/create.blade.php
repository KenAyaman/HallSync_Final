<x-app-layout>
<div class="admin-user-form-page max-w-2xl mx-auto">

    <div class="admin-user-form-hero mb-6">
        <a href="{{ route('admin.users') }}" class="text-sm text-[#D6A85B] hover:underline">← Back to Users</a>
        <div class="admin-user-kicker">Access Control</div>
        <h1 class="text-2xl font-bold mt-2" style="color: #F8F3EA; font-family: 'Playfair Display', serif;">
            Add New User
        </h1>
        <p class="text-sm text-gray-400 mt-1">Default temporary password: <span class="text-[#D6A85B]">password123</span></p>
    </div>

    <div class="admin-user-form-card rounded-xl p-6" style="background: #1F2023; border: 1px solid #2A2C30;">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Full Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full px-4 py-2 rounded-lg"
                           style="background: #2A2C30; border: 1px solid #3A342D; color: #F8F3EA;">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Email Address *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-4 py-2 rounded-lg"
                           style="background: #2A2C30; border: 1px solid #3A342D; color: #F8F3EA;">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Room Number (for residents)</label>
                    <input type="text" name="room_number" value="{{ old('room_number') }}"
                           class="w-full px-4 py-2 rounded-lg"
                           style="background: #2A2C30; border: 1px solid #3A342D; color: #F8F3EA;">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Role *</label>
                    <select name="role" class="w-full px-4 py-2 rounded-lg"
                            style="background: #2A2C30; border: 1px solid #3A342D; color: #F8F3EA;">
                        <option value="resident" {{ old('role') === 'resident' ? 'selected' : '' }}>Resident</option>
                        <option value="handyman" {{ old('role') === 'handyman' ? 'selected' : '' }}>Staff</option>
                        <option value="manager" {{ old('role') === 'manager' ? 'selected' : '' }}>Administrator</option>
                    </select>
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="submit" class="px-6 py-2 rounded-lg font-medium transition"
                            style="background: linear-gradient(135deg, #B8842F, #D6A85B); color: white;">
                        Create User
                    </button>
                    <a href="{{ route('admin.users') }}" class="px-6 py-2 rounded-lg font-medium transition text-center"
                       style="background: rgba(168,159,145,0.1); color: #B0A898; text-decoration: none;">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>

</div>
<style>
.admin-user-form-page { width: 100%; }
.admin-user-form-hero,
.admin-user-form-card {
    border-radius: 20px;
    border: 1px solid rgba(214,168,91,0.18);
    box-shadow: 0 18px 36px rgba(72,48,24,0.14);
}
.admin-user-form-hero {
    padding: 24px 28px;
    background: linear-gradient(120deg, #15120d 0%, #211c15 52%, #2c2419 100%);
}
.admin-user-form-hero h1,
.admin-user-form-hero p { color: #f8f3ea !important; }
.admin-user-kicker {
    margin-top: 14px;
    color: #d6a85b;
    font-size: 0.76rem;
    font-weight: 800;
    letter-spacing: 0.18em;
    text-transform: uppercase;
}
.admin-user-form-hero a {
    display: inline-flex;
    text-decoration: none !important;
    font-weight: 700;
}
.admin-user-form-card {
    background: linear-gradient(180deg, rgba(43,42,39,0.95) 0%, rgba(31,31,29,0.95) 100%) !important;
    padding: 28px !important;
}
.admin-user-form-card label { color: #d4c8b8 !important; }
.admin-user-form-card input,
.admin-user-form-card select { min-height: 44px; }
.admin-user-form-card .flex.gap-3 {
    border-top: 1px solid rgba(214,168,91,0.12);
}
@media (max-width: 560px) {
    .admin-user-form-hero,
    .admin-user-form-card { padding: 20px !important; }
    .admin-user-form-card .flex.gap-3 { flex-direction: column; }
}
</style>
</x-app-layout>
