<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    private const ROLES = ['resident', 'handyman', 'manager'];

    private const SORTS = [
        'name' => 'name',
        'email' => 'email',
        'role' => 'role',
        'status' => 'is_active',
        'created' => 'created_at',
        'last_login' => 'last_login_at',
    ];

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'role' => ['nullable', Rule::in(array_merge(['all'], self::ROLES))],
            'status' => ['nullable', Rule::in(['all', 'active', 'inactive'])],
            'created_from' => ['nullable', 'date'],
            'created_to' => ['nullable', 'date', 'after_or_equal:created_from'],
            'sort' => ['nullable', Rule::in(array_keys(self::SORTS))],
            'direction' => ['nullable', Rule::in(['asc', 'desc'])],
            'per_page' => ['nullable', 'integer', Rule::in([15, 25, 50])],
        ]);

        $sort = $filters['sort'] ?? 'created';
        $direction = $filters['direction'] ?? 'desc';
        $perPage = (int) ($filters['per_page'] ?? 15);
        $search = trim((string) ($filters['search'] ?? ''));
        $roleFilter = $filters['role'] ?? 'all';
        $statusFilter = $filters['status'] ?? 'all';

        $query = User::query();

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('resident_number', 'like', "%{$search}%")
                    ->orWhere('room_number', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        if ($roleFilter !== 'all') {
            $query->where('role', $roleFilter);
        }

        if ($statusFilter !== 'all') {
            $query->where('is_active', $statusFilter === 'active');
        }

        if (! empty($filters['created_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_from']);
        }

        if (! empty($filters['created_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_to']);
        }

        $users = $query
            ->orderBy(self::SORTS[$sort], $direction)
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.user.index', [
            'users' => $users,
            'filters' => $filters,
            'sort' => $sort,
            'direction' => $direction,
            'perPage' => $perPage,
            'totalResidents' => User::where('role', 'resident')->count(),
            'totalHandymen' => User::where('role', 'handyman')->count(),
            'totalAdmins' => User::where('role', 'manager')->count(),
            'activeUsers' => User::where('is_active', true)->count(),
            'inactiveUsers' => User::where('is_active', false)->count(),
        ]);
    }

    public function create(): View
    {
        return view('admin.user.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateUser($request);
        $temporaryPassword = $this->generateTemporaryPassword();

        $user = DB::transaction(function () use ($validated, $temporaryPassword, $request) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => Str::lower($validated['email']),
                'phone_number' => $validated['phone_number'] ?? null,
                'password' => Hash::make($temporaryPassword),
                'temporary_password' => $temporaryPassword,
                'role' => $validated['role'],
                'room_number' => $validated['role'] === 'resident' ? $validated['room_number'] : null,
                'resident_number' => null,
                'is_active' => true,
                'must_change_password' => true,
                'password_reset_at' => now(),
            ]);

            if ($user->isResident()) {
                $user->forceFill([
                    'resident_number' => $this->generateResidentNumber($user),
                ])->save();
            }

            UserActivityLog::record(
                'account.created',
                'Created the account.',
                $user,
                $request->user(),
                ['role' => $user->role]
            );

            return $user;
        });

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'Account successfully made.')
            ->with('temporary_password', $temporaryPassword);
    }

    public function show(User $user): View
    {
        return view('admin.user.show', [
            'user' => $user,
            'activityLogs' => $user->activityLogs()->with('actor')->latest()->paginate(12),
            'residentTickets' => $user->maintenanceTickets()->latest()->limit(8)->get(),
            'assignedTickets' => $user->assignedTickets()->latest()->limit(8)->get(),
            'recentBookings' => $user->bookings()->latest('booking_date')->limit(8)->get(),
            'operationalCounts' => $user->operationalRecordCounts(),
        ]);
    }

    public function edit(User $user): View
    {
        return view('admin.user.edit', [
            'user' => $user,
            'operationalCounts' => $user->operationalRecordCounts(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $this->validateUser($request, $user);
        $newRole = $validated['role'];

        if ($user->id === $request->user()->id && $newRole !== 'manager') {
            return back()->withInput()->with('error', 'You cannot remove your own administrator access.');
        }

        if ($user->isLastActiveManager() && $newRole !== 'manager') {
            return back()->withInput()->with('error', 'Keep at least one active administrator account.');
        }

        if ($user->isHandyman() && $newRole !== 'handyman' && $this->hasActiveAssignments($user)) {
            return back()->withInput()->with('error', 'Reassign this staff member\'s active tickets before changing their role.');
        }

        $before = $user->only(['name', 'email', 'phone_number', 'room_number', 'resident_number', 'role']);

        $user->update([
            'name' => $validated['name'],
            'email' => Str::lower($validated['email']),
            'phone_number' => $validated['phone_number'] ?? null,
            'room_number' => $newRole === 'resident' ? $validated['room_number'] : null,
            'resident_number' => $newRole === 'resident'
                ? ($user->resident_number ?: $this->generateResidentNumber($user))
                : null,
            'role' => $newRole,
        ]);

        $changes = collect($user->only(array_keys($before)))
            ->filter(fn ($value, $key) => $before[$key] !== $value)
            ->keys()
            ->values()
            ->all();

        UserActivityLog::record(
            'account.updated',
            $changes === [] ? 'Reviewed the account without changing profile fields.' : 'Updated account information.',
            $user,
            $request->user(),
            ['changed_fields' => $changes]
        );

        return redirect()->route('admin.users.show', $user)->with('success', 'User information updated.');
    }

    public function updateStatus(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);
        $activate = $validated['status'] === 'active';

        if (! $activate && $user->id === $request->user()->id) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        if (! $activate && $user->isLastActiveManager()) {
            return back()->with('error', 'Keep at least one active administrator account.');
        }

        if (! $activate && $user->isHandyman() && $this->hasActiveAssignments($user)) {
            return back()->with('error', 'Reassign this staff member\'s active tickets before deactivating their account.');
        }

        if ($user->is_active === $activate) {
            return back()->with('success', "The account is already {$validated['status']}.");
        }

        DB::transaction(function () use ($user, $activate) {
            $user->forceFill([
                'is_active' => $activate,
                'deactivated_at' => $activate ? null : now(),
                'remember_token' => Str::random(60),
            ])->save();

            if (! $activate && $user->isResident()) {
                $this->cancelFutureBookings($user);
            }
        });

        if (! $activate) {
            $this->revokeSessions($user);
        }

        UserActivityLog::record(
            $activate ? 'account.reactivated' : 'account.deactivated',
            $activate ? 'Reactivated the account.' : 'Deactivated the account and revoked active sessions.',
            $user,
            $request->user()
        );

        return back()->with('success', $activate ? 'User reactivated successfully.' : 'User deactivated successfully.');
    }

    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Use your profile security settings to change your own password.');
        }

        if ($user->password_reset_at && $user->password_reset_at->diffInMinutes(now()) < 5) {
            return back()->with('error', 'Please wait at least 5 minutes before resetting this password again.');
        }

        $temporaryPassword = $this->generateTemporaryPassword();

        $user->forceFill([
            'password' => Hash::make($temporaryPassword),
            'temporary_password' => $temporaryPassword,
            'must_change_password' => true,
            'password_reset_at' => now(),
            'remember_token' => Str::random(60),
        ])->save();
        $this->revokeSessions($user);

        UserActivityLog::record(
            'password.reset',
            'Reset the password and revoked active sessions.',
            $user,
            $request->user()
        );

        return back()
            ->with('success', 'Temporary password generated. Share the temporary password shown below with the user.')
            ->with('temporary_password', $temporaryPassword);
    }

    public function moveOut(Request $request, User $user): RedirectResponse
    {
        if (! $user->isResident()) {
            return back()->with('error', 'Only resident accounts can be archived through move-out.');
        }

        if ($user->residency_status === 'moved_out') {
            return back()->with('success', 'This resident account is already archived.');
        }

        DB::transaction(function () use ($user) {
            $this->cancelFutureBookings($user);

            $user->forceFill([
                'is_active'        => false,
                'residency_status' => 'moved_out',
                'moved_out_at'     => now(),
                'deactivated_at'   => now(),
                'remember_token'   => Str::random(60),
            ])->save();

            // ResidentRosterEntry has no creation path yet; skip the update to avoid a silent no-op (H-12).
        });

        $this->revokeSessions($user);

        UserActivityLog::record(
            'account.moved_out',
            'Archived a resident account after move-out and cancelled future reservations.',
            $user,
            $request->user()
        );

        return back()->with('success', 'Resident move-out completed. Future bookings were cancelled and login access was disabled.');
    }

    public function bulkUpdate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bulk_action' => ['required', Rule::in(['activate', 'deactivate'])],
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $activate = $validated['bulk_action'] === 'activate';
        $updated = 0;
        $skipped = 0;

        User::whereKey($validated['user_ids'])->get()->each(function (User $user) use ($request, $activate, &$updated, &$skipped) {
            if ((! $activate && $user->id === $request->user()->id) || (! $activate && $user->isLastActiveManager())) {
                $skipped++;
                return;
            }

            if (! $activate && $user->isHandyman() && $this->hasActiveAssignments($user)) {
                $skipped++;
                return;
            }

            if ($user->is_active === $activate) {
                return;
            }

            DB::transaction(function () use ($user, $activate) {
                $user->forceFill([
                    'is_active' => $activate,
                    'deactivated_at' => $activate ? null : now(),
                    'remember_token' => Str::random(60),
                ])->save();

                if (! $activate && $user->isResident()) {
                    $this->cancelFutureBookings($user);
                }
            });

            if (! $activate) {
                $this->revokeSessions($user);
            }

            UserActivityLog::record(
                $activate ? 'account.reactivated' : 'account.deactivated',
                $activate ? 'Reactivated the account through a bulk action.' : 'Deactivated the account through a bulk action.',
                $user,
                $request->user(),
                ['bulk_action' => true]
            );
            $updated++;
        });

        $message = "{$updated} account".($updated === 1 ? '' : 's')." {$validated['bulk_action']}d.";

        if ($skipped > 0) {
            $message .= " {$skipped} protected account".($skipped === 1 ? ' was' : 's were').' skipped.';
        }

        return back()->with('success', $message);
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if ($user->isLastActiveManager()) {
            return back()->with('error', 'Keep at least one active administrator account.');
        }

        $counts = collect($user->operationalRecordCounts())->filter();

        if ($counts->isNotEmpty()) {
            return back()->with(
                'error',
                'This account has operational history and cannot be deleted. Deactivate it instead.'
            );
        }

        $email = $user->email;
        UserActivityLog::record(
            'account.deleted',
            'Deleted an unused account.',
            $user,
            $request->user(),
            ['email' => $email, 'role' => $user->role]
        );
        $user->delete();

        return redirect()->route('admin.users')->with('success', "Deleted {$email}.");
    }

    private function validateUser(Request $request, ?User $user = null): array
    {
        $officialDomain = Str::lower(ltrim((string) config('hallsync.official_email_domain'), '@'));
        $emailRules = [
            'required',
            'email',
            'max:255',
            Rule::unique('users', 'email')->ignore($user),
        ];

        if ($officialDomain !== '') {
            $emailRules[] = "ends_with:@{$officialDomain}";
        }

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => $emailRules,
            'phone_number' => ['nullable', 'string', 'max:30'],
            'role' => ['required', Rule::in(self::ROLES)],
            'room_number' => ['nullable', 'required_if:role,resident', 'string', 'max:20'],
            'resident_number' => ['prohibited'],
        ]);
    }

    private function generateResidentNumber(User $user): string
    {
        $base = 'RES-'.str_pad((string) $user->id, 6, '0', STR_PAD_LEFT);
        $residentNumber = $base;
        $suffix = 2;

        while (User::where('resident_number', $residentNumber)->whereKeyNot($user->id)->exists()) {
            $residentNumber = "{$base}-{$suffix}";
            $suffix++;
        }

        return $residentNumber;
    }

    private function generateTemporaryPassword(): string
    {
        return 'RexHall-'.Str::upper(Str::random(8));
    }

    private function revokeSessions(User $user): void
    {
        DB::table('sessions')->where('user_id', $user->id)->delete();
    }

    private function hasActiveAssignments(User $user): bool
    {
        return $user->assignedTickets()
            ->whereNotIn('status', ['resolved', 'closed', 'cancelled', 'completed', 'rejected'])
            ->exists();
    }

    private function cancelFutureBookings(User $user): void
    {
        $bookingIds = $user->bookings()
            ->where('status', 'approved')
            ->where('end_time', '>', now())
            ->pluck('id');

        if ($bookingIds->isEmpty()) {
            return;
        }

        DB::table('booking_slot_claims')->whereIn('booking_id', $bookingIds)->delete();
        $user->bookings()->whereKey($bookingIds)->update(['status' => 'cancelled']);
    }

}
