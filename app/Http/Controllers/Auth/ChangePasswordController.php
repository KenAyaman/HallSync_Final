<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\UserActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ChangePasswordController extends Controller
{
    public function show(): View
    {
        return view('auth.change-password');
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', 'min:8', 'different:current_password'],
        ]);

        $user = $request->user();

        if (! Hash::check($validated['current_password'], $user->password)) {
            return back()
                ->withInput()
                ->withErrors(['current_password' => 'The temporary password you entered is incorrect.']);
        }

        $user->forceFill([
            'password' => Hash::make($validated['password']),
            'temporary_password' => null,
            'must_change_password' => false,
            'password_reset_at' => null,
        ])->save();

        UserActivityLog::record(
            'password.changed',
            'Changed password after first login.',
            $user,
            $user
        );

        return redirect()->route($user->isHandyman() ? 'staff.overview' : 'dashboard');
    }
}
