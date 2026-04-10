<x-app-layout>
<div class="max-w-2xl mx-auto">

    <div class="mb-6">
        <a href="{{ route('admin.users') }}" class="text-sm text-[#D6A85B] hover:underline">← Back to Users</a>
        <h1 class="text-2xl font-bold mt-2" style="color: #F8F3EA; font-family: 'Playfair Display', serif;">
            Edit User
        </h1>
    </div>

    <div class="rounded-xl p-6" style="background: #1F2023; border: 1px solid #2A2C30;">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Full Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full px-4 py-2 rounded-lg"
                           style="background: #2A2C30; border: 1px solid #3A342D; color: #F8F3EA;">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full px-4 py-2 rounded-lg"
                           style="background: #2A2C30; border: 1px solid #3A342D; color: #F8F3EA;">
                </div>

                @if($user->role === 'resident')
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Room Number</label>
                    <input type="text" name="room_number" value="{{ old('room_number', $user->room_number) }}"
                           class="w-full px-4 py-2 rounded-lg"
                           style="background: #2A2C30; border: 1px solid #3A342D; color: #F8F3EA;">
                    <p class="text-xs text-gray-500 mt-1">Optional: Assign a room number to this resident</p>
                </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Role</label>
                    <select name="role" class="w-full px-4 py-2 rounded-lg"
                            style="background: #2A2C30; border: 1px solid #3A342D; color: #F8F3EA;">
                        <option value="resident" {{ $user->role === 'resident' ? 'selected' : '' }}>Resident</option>
                        <option value="handyman" {{ $user->role === 'handyman' ? 'selected' : '' }}>Staff</option>
                        <option value="manager" {{ $user->role === 'manager' ? 'selected' : '' }}>Administrator</option>
                    </select>
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="submit" class="px-6 py-2 rounded-lg font-medium transition"
                            style="background: linear-gradient(135deg, #B8842F, #D6A85B); color: white;">
                        Save Changes
                    </button>
                    <a href="{{ route('admin.users') }}" class="px-6 py-2 rounded-lg font-medium transition text-center"
                       style="background: rgba(168,159,145,0.1); color: #B0A898; text-decoration: none;">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Danger Zone --}}
    @if($user->id !== auth()->id())
    <div class="mt-6 rounded-xl p-6" style="background: rgba(224,112,96,0.05); border: 1px solid rgba(224,112,96,0.2);">
        <h3 class="text-sm font-semibold text-[#E07060] mb-2">Danger Zone</h3>
        <p class="text-xs text-gray-400 mb-3">Once deleted, all tickets and bookings by this user will also be removed.</p>
        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Are you sure? This cannot be undone.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-4 py-2 rounded-lg text-sm font-medium transition"
                    style="background: rgba(224,112,96,0.15); color: #E07060;">
                Delete User
            </button>
        </form>
    </div>
    @endif

</div>
</x-app-layout>
