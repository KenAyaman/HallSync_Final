<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MaintenanceTicket;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:manager']);
    }

    public function index(Request $request)
    {
        $search = $request->get('search');
        $roleFilter = $request->get('role');
        
        $query = User::query();
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('room_number', 'like', "%{$search}%");
            });
        }
        
        if ($roleFilter && $roleFilter !== 'all') {
            $query->where('role', $roleFilter);
        }
        
        $users = $query->orderBy('role')->orderBy('name')->paginate(20);
        
        // Get statistics
        $totalResidents = User::where('role', 'resident')->count();
        $totalHandymen = User::where('role', 'handyman')->count();
        $totalAdmins = User::where('role', 'manager')->count();
        $activeUsers = User::where('is_active', true)->count();
        
        return view('admin.users.index', compact('users', 'totalResidents', 'totalHandymen', 'totalAdmins', 'activeUsers', 'search', 'roleFilter'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'role' => 'required|in:resident,handyman,manager',
            'room_number' => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make('password123'),
            'role' => $request->role,
            'room_number' => $request->room_number,
            'is_active' => true,
        ]);

        return redirect()->route('admin.users')
            ->with('success', "User created! Temporary password: password123");
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:resident,handyman,manager',
            'room_number' => 'nullable|string|max:20',
        ]);

        $user->update($request->only(['name', 'email', 'role', 'room_number']));

        return redirect()->route('admin.users')
            ->with('success', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }
        
        $user->delete();

        return redirect()->route('admin.users')
            ->with('success', 'User deleted successfully');
    }
    
    public function toggleStatus(User $user)
    {
        $user->is_active = !$user->is_active;
        $user->save();
        
        $status = $user->is_active ? 'activated' : 'deactivated';
        return redirect()->route('admin.users')
            ->with('success', "User {$status} successfully");
    }
    
    public function resetPassword(User $user)
    {
        $newPassword = 'password123';
        $user->password = Hash::make($newPassword);
        $user->save();
        
        return redirect()->route('admin.users')
            ->with('success', "Password reset to: {$newPassword}");
    }
}