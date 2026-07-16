<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\UserActivityLog;
use App\Notifications\BookingStatusChangedNotification;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Carbon\Carbon;

class BookingController extends Controller
{
    protected const STANDARD_BOOKING_SLOTS = [
        '08:00', '09:00', '10:00', '11:00',
        '12:00', '13:00', '14:00', '15:00',
        '16:00', '17:00', '18:00', '19:00',
        '20:00', '21:00',
    ];

    protected array $facilities = [
        'Study Room 1' => [
            'capacity' => 25,
            'hours' => '8:00 AM - 10:00 PM',
            'tags' => ['silent', 'monitor'],
            'slots' => self::STANDARD_BOOKING_SLOTS,
        ],
        'Study Room 2' => [
            'capacity' => 25,
            'hours' => '8:00 AM - 10:00 PM',
            'tags' => ['silent'],
            'slots' => self::STANDARD_BOOKING_SLOTS,
        ],
        'Conference Room' => [
            'capacity' => 12,
            'hours' => '8:00 AM - 10:00 PM',
            'tags' => ['group', 'monitor'],
            'slots' => self::STANDARD_BOOKING_SLOTS,
        ],
        'Gym' => [
            'capacity' => 10,
            'hours' => '8:00 AM - 10:00 PM',
            'tags' => ['group'],
            'slots' => self::STANDARD_BOOKING_SLOTS,
        ],
    ];

    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'manager') {
            return redirect()->route('admin.bookings.calendar');
        }

        if ($user->role === 'handyman') {
            abort(403, 'Handymen cannot access facility bookings.');
        }

        // Upcoming: approved bookings that haven't ended yet (H-09).
        $bookings = Booking::where('user_id', $user->id)
            ->where('status', Booking::STATUS_APPROVED)
            ->active()
            ->orderBy('booking_date')
            ->get();

        // History: past approved bookings and all cancelled bookings (H-09).
        $pastBookings = Booking::where('user_id', $user->id)
            ->where(function ($q) {
                $q->where(function ($inner) {
                    $inner->where('status', Booking::STATUS_APPROVED)
                          ->where('end_time', '<=', now());
                })->orWhere('status', Booking::STATUS_CANCELLED);
            })
            ->orderByDesc('booking_date')
            ->limit(30)
            ->get();

        return view('resident.bookings.index', compact('bookings', 'pastBookings'));
    }

    public function create()
    {
        $user = Auth::user();

        if ($user->role !== 'resident') {
            abort(403, 'Only residents can create facility bookings.');
        }

        $facilities = $this->facilities;

        return view('resident.bookings.create', compact('facilities'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'resident') {
            abort(403, 'Only residents can create facility bookings.');
        }

        $request->validate([
            'facility_name' => ['required', 'string', 'in:' . implode(',', array_keys($this->facilities))],
            'booking_date' => ['required', 'date', 'after_or_equal:today'],
            'booking_time' => ['required', 'date_format:H:i'],
            'notes' => ['nullable', 'string', 'max:500'],
            'group_members' => ['nullable', 'string', 'max:500'],
        ], [
            'booking_date.after_or_equal' => 'You cannot book a facility for a past date.',
            'booking_time.required' => 'Please select a time slot.',
        ]);

        $facility = $this->facilities[$request->facility_name];

        if (!in_array($request->booking_time, $facility['slots'], true)) {
            return back()
                ->withErrors(['booking_time' => 'That time slot is not valid for the selected facility.'])
                ->withInput()
                ->with('error', 'That time slot is not valid for the selected facility.');
        }

        $startDateTime = Carbon::parse($request->booking_date . ' ' . $request->booking_time);
        $endDateTime = (clone $startDateTime)->addHour();

        if ($startDateTime->lt(Carbon::now()->startOfMinute())) {
            return back()
                ->withErrors(['booking_date' => 'You cannot book a past date or time slot.'])
                ->withInput()
                ->with('error', 'You cannot book a past date or time slot.');
        }

        try {
            $booking = Cache::lock($this->residentBookingLockKey(Auth::id()), 10)
                ->block(3, function () use ($request, $startDateTime, $endDateTime) {
                    return Cache::lock($this->slotLockKey($request->facility_name, $startDateTime), 10)
                        ->block(3, function () use ($request, $startDateTime, $endDateTime) {
                            if (Booking::where('user_id', Auth::id())->active()->where('status', 'approved')->count() >= 3) {
                                return 'booking_limit';
                            }

                            if ($this->facilitySlotIsFull($request->facility_name, $startDateTime, $endDateTime)) {
                                return 'facility_conflict';
                            }

                            if ($this->residentApprovedOverlapExists(Auth::id(), $startDateTime, $endDateTime)) {
                                return 'resident_conflict';
                            }

                            return DB::transaction(function () use ($request, $startDateTime, $endDateTime) {
                                $booking = Booking::create([
                                    'user_id' => Auth::id(),
                                    'facility_name' => $request->facility_name,
                                    'booking_date' => $startDateTime,
                                    'end_time' => $endDateTime,
                                    'notes' => $request->notes,
                                    'group_members' => $request->group_members,
                                    'status' => 'approved',
                                ]);

                                $this->claimSlot($booking);
                                UserActivityLog::record(
                                    'booking.created',
                                    'Created a confirmed facility booking.',
                                    Auth::user(),
                                    Auth::user(),
                                    ['booking_id' => $booking->id, 'facility' => $booking->facility_name, 'starts_at' => $booking->booking_date->toIso8601String()]
                                );

                                return $booking;
                            });
                        });
                });
        } catch (LockTimeoutException|QueryException) {
            return $this->conflictResponse('This slot is being reserved by another resident. Please choose another available time.');
        }

        if ($booking === 'booking_limit') {
            return back()
                ->withErrors(['booking_date' => 'You already have 3 upcoming bookings. Please cancel one before reserving another slot.'])
                ->withInput()
                ->with('warning', 'You already have 3 upcoming bookings. Cancel one before reserving another slot.');
        }

        if ($booking === 'facility_conflict') {
            return $this->conflictResponse('That time slot is already full for this facility.');
        }

        if ($booking === 'resident_conflict') {
            return $this->conflictResponse('You already have another booking during that time. Please choose a different slot.');
        }

        return redirect()
            ->route('bookings.index')
            ->with('success', 'Booking confirmed. Your reservation for ' . $booking->facility_name . ' on ' . $booking->booking_date->format('M d, Y h:i A') . ' is now in My Bookings.');
    }

    public function show(Booking $booking)
    {
        $user = Auth::user();

        $this->authorize('view', $booking);

        return view('resident.bookings.show', compact('booking'));
    }

    public function edit(Booking $booking)
    {
        $user = Auth::user();

        $this->authorize('update', $booking);

        $facilities = $this->facilities;
        $selectedTime = $booking->booking_date->format('H:i');
        $selectedDate = $booking->booking_date->format('Y-m-d');

        return view('resident.bookings.edit', compact('booking', 'facilities', 'selectedTime', 'selectedDate'));
    }

    public function update(Request $request, Booking $booking)
    {
        $user = Auth::user();

        $this->authorize('update', $booking);

        $request->validate([
            'facility_name' => ['required', 'string', 'in:' . implode(',', array_keys($this->facilities))],
            'booking_date' => ['required', 'date', 'after_or_equal:today'],
            'booking_time' => ['required', 'date_format:H:i'],
            'notes' => ['nullable', 'string', 'max:500'],
            'group_members' => ['nullable', 'string', 'max:500'],
        ]);

        $facility = $this->facilities[$request->facility_name];

        if (!in_array($request->booking_time, $facility['slots'], true)) {
            return back()
                ->withErrors(['booking_time' => 'That time slot is not valid for the selected facility.'])
                ->withInput()
                ->with('error', 'That time slot is not valid for the selected facility.');
        }

        $startDateTime = Carbon::parse($request->booking_date . ' ' . $request->booking_time);
        $endDateTime = (clone $startDateTime)->addHour();

        if ($startDateTime->lt(Carbon::now()->startOfMinute())) {
            return back()
                ->withErrors(['booking_date' => 'You cannot book a past date or time slot.'])
                ->withInput()
                ->with('error', 'You cannot book a past date or time slot.');
        }

        try {
            $updated = Cache::lock($this->residentBookingLockKey($user->id), 10)
                ->block(3, function () use ($request, $booking, $startDateTime, $endDateTime, $user) {
                    return Cache::lock($this->slotLockKey($request->facility_name, $startDateTime), 10)
                        ->block(3, function () use ($request, $booking, $startDateTime, $endDateTime, $user) {
                            if ($this->facilitySlotIsFull($request->facility_name, $startDateTime, $endDateTime, $booking->id)) {
                                return 'facility_conflict';
                            }

                            if ($this->residentApprovedOverlapExists($user->id, $startDateTime, $endDateTime, $booking->id)) {
                                return 'resident_conflict';
                            }

                            return DB::transaction(function () use ($request, $booking, $startDateTime, $endDateTime) {
                                $updated = $booking->update([
                                    'facility_name' => $request->facility_name,
                                    'booking_date' => $startDateTime,
                                    'end_time' => $endDateTime,
                                    'notes' => $request->notes,
                                    'group_members' => $request->group_members,
                                ]);

                                $this->claimSlot($booking);
                                UserActivityLog::record(
                                    'booking.updated',
                                    'Updated a confirmed facility booking.',
                                    Auth::user(),
                                    Auth::user(),
                                    ['booking_id' => $booking->id, 'facility' => $booking->facility_name, 'starts_at' => $booking->booking_date->toIso8601String()]
                                );

                                return $updated;
                            });
                        });
                });
        } catch (LockTimeoutException|QueryException) {
            return $this->conflictResponse('This slot is being reserved by another resident. Please choose another available time.');
        }

        if ($updated === 'facility_conflict') {
            return $this->conflictResponse('That time slot is already full for this facility.');
        }

        if ($updated === 'resident_conflict') {
            return $this->conflictResponse('You already have another booking during that time. Please choose a different slot.');
        }

        return redirect()
            ->route('bookings.index')
            ->with('success', 'Booking updated. Your reservation for ' . $booking->facility_name . ' on ' . $booking->booking_date->format('M d, Y h:i A') . ' has been saved.');
    }

    public function destroy(Booking $booking)
    {
        $user = Auth::user();

        $this->authorize('delete', $booking);

        DB::transaction(function () use ($booking) {
            DB::table('booking_slot_claims')->where('booking_id', $booking->id)->delete();
            $booking->update(['status' => 'cancelled']);
            UserActivityLog::record(
                'booking.cancelled',
                'Cancelled a facility booking and released its slot.',
                Auth::user(),
                Auth::user(),
                ['booking_id' => $booking->id, 'facility' => $booking->facility_name]
            );
        });

        return redirect()
            ->route('bookings.index')
            ->with('warning', 'Booking cancelled. The slot has been released and removed from your active bookings.');
    }

    public function getReservedSlots(Request $request)
    {
        $request->validate([
            'facility_name' => ['required', 'string', 'in:' . implode(',', array_keys($this->facilities))],
            'booking_date' => ['required', 'date'],
            'exclude_booking_id' => ['nullable', 'integer'],
        ]);

        $date = Carbon::parse($request->booking_date);
        $excludeBookingId = Booking::whereKey($request->integer('exclude_booking_id'))
            ->where('user_id', Auth::id())
            ->value('id');

        $facilityBookings = Booking::where('facility_name', $request->facility_name)
            ->where('status', 'approved')
            ->active()
            ->whereDate('booking_date', $date->toDateString())
            ->when($excludeBookingId, fn ($query, $bookingId) => $query->where('id', '!=', $bookingId))
            ->orderBy('booking_date')
            ->get();

        $slotCapacity = $this->slotCapacityMap($request->facility_name, $facilityBookings);
        $facilityReservedSlots = collect($slotCapacity)
            ->filter(fn ($slot) => $slot['available'] <= 0)
            ->keys()
            ->values();

        $residentReservedSlots = Booking::where('user_id', Auth::id())
            ->where('status', 'approved')
            ->active()
            ->whereDate('booking_date', $date->toDateString())
            ->when($excludeBookingId, fn ($query, $bookingId) => $query->where('id', '!=', $bookingId))
            ->orderBy('booking_date')
            ->get()
            ->map(fn ($booking) => $booking->booking_date->format('H:i'));

        $reservedSlots = collect($facilityReservedSlots
            ->concat($residentReservedSlots)
            ->all())
            ->unique()
            ->values();

        return response()->json([
            'reserved_slots' => $reservedSlots,
            'full_slots' => $facilityReservedSlots,
            'resident_reserved_slots' => $residentReservedSlots->values(),
            'available_slots' => $this->facilities[$request->facility_name]['slots'],
            'facility' => $this->facilities[$request->facility_name],
            'slot_capacity' => $slotCapacity,
            'recommendations' => $this->availabilityRecommendations($request->facility_name, $date, $facilityReservedSlots),
        ]);
    }

    public function calendar(Request $request)
    {
        if (Auth::user()->role !== 'manager') {
            abort(403);
        }

        $viewMode = $request->query('view', 'day');
        if (!in_array($viewMode, ['day', 'this_month', 'last_month'], true)) {
            $viewMode = 'day';
        }

        $selectedDate = $request->filled('date')
            ? Carbon::createFromFormat('Y-m-d', $request->query('date'))->startOfDay()
            : now()->startOfDay();

        $previousDate = $selectedDate->copy()->subDay()->toDateString();
        $nextDate = $selectedDate->copy()->addDay()->toDateString();
        $todayDate = now()->toDateString();

        $monthStart = $viewMode === 'last_month'
            ? now()->subMonthNoOverflow()->startOfMonth()
            : now()->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();
        $monthLabel = $monthStart->format('F Y');

        // Get all approved bookings for the selected date.
        $dayBookings = Booking::whereDate('booking_date', $selectedDate)
            ->where('status', Booking::STATUS_APPROVED)
            ->with('user')
            ->orderBy('booking_date')
            ->get();

        $bookings = $dayBookings->groupBy('facility_name');
        
        $timeSlots = self::STANDARD_BOOKING_SLOTS;
        
        $facilities = ['Study Room 1', 'Study Room 2', 'Conference Room', 'Gym'];
        $facilityCapacities = collect($facilities)
            ->mapWithKeys(fn ($facility) => [$facility => $this->facilities[$facility]['capacity']])
            ->all();
        
        // Build calendar grid
        $calendar = [];
        foreach ($facilities as $facility) {
            foreach ($timeSlots as $slot) {
                $slotBookings = $bookings->get($facility, collect())->filter(function($b) use ($slot) {
                    return $b->booking_date->format('H:00') == $slot;
                })->values();
                $calendar[$facility][$slot] = $slotBookings;
            }
        }
        
        // Get weekly stats
        $weekStart = $selectedDate->copy()->startOfWeek();
        $weekEnd = $selectedDate->copy()->endOfWeek();
        
        // Only count approved bookings in weekly stats — cancelled/rejected should not inflate the numbers (M-07).
        $weeklyBookings = Booking::where('status', Booking::STATUS_APPROVED)
            ->whereBetween('booking_date', [$weekStart, $weekEnd])
            ->get()
            ->groupBy(function ($b) {
                return $b->booking_date->format('Y-m-d');
            });

        $monthlyBookings = Booking::with('user')
            ->whereBetween('booking_date', [$monthStart, $monthEnd])
            ->orderBy('booking_date')
            ->get();

        $monthlyFacilitySummary = $monthlyBookings
            ->groupBy('facility_name')
            ->map(function ($facilityBookings, $facilityName) {
                return [
                    'facility_name' => $facilityName,
                    'count' => $facilityBookings->count(),
                    'approved' => $facilityBookings->where('status', 'approved')->count(),
                    'cancelled' => $facilityBookings->where('status', 'cancelled')->count(),
                ];
            })
            ->sortByDesc('count')
            ->values();

        return view('admin.bookings.calendar', compact(
            'calendar',
            'facilities',
            'timeSlots',
            'selectedDate',
            'weeklyBookings',
            'previousDate',
            'nextDate',
            'todayDate',
            'viewMode',
            'monthLabel',
            'monthlyBookings',
            'monthlyFacilitySummary',
            'facilityCapacities'
        ));
    }

    public function getBookingDetails(Booking $booking)
    {
        if (Auth::user()->role !== 'manager') {
            abort(403);
        }

        $slotBookings = Booking::with('user')
            ->where('facility_name', $booking->facility_name)
            ->where('status', Booking::STATUS_APPROVED)
            ->where('booking_date', $booking->booking_date)
            ->orderBy('created_at')
            ->get();

        // Pre-fetch booking counts for all users in one query to avoid N+1 (C-07).
        $userIds = $slotBookings->pluck('user_id')->filter()->unique()->all();
        $bookingCountsByUser = Booking::whereIn('user_id', $userIds)
            ->selectRaw('user_id, COUNT(*) as total')
            ->groupBy('user_id')
            ->pluck('total', 'user_id');

        $capacity = $this->facilities[$booking->facility_name]['capacity'] ?? $slotBookings->count();

        return response()->json([
            'id' => $booking->id,
            'facility_name' => $booking->facility_name,
            'user_name' => $booking->user->name ?? 'Resident',
            'date' => $booking->booking_date->format('F d, Y'),
            'time' => $booking->booking_date->format('h:i A') . ' - ' . $booking->end_time->format('h:i A'),
            'notes' => $booking->notes,
            'group_members' => $booking->group_members,
            'status' => $booking->status,
            'capacity' => $capacity,
            'reserved_count' => $slotBookings->count(),
            'available_count' => max(0, $capacity - $slotBookings->count()),
            'residents' => $slotBookings->map(fn ($slotBooking) => [
                'id' => $slotBooking->id,
                'name' => $slotBooking->user->name ?? 'Resident',
                'room_number' => $slotBooking->user->room_number ?? null,
                'resident_number' => $slotBooking->user->resident_number ?? null,
                'notes' => $slotBooking->notes,
                'group_members' => $slotBooking->group_members,
                'booking_date' => $slotBooking->booking_date->format('F d, Y'),
                'time_slot' => $slotBooking->booking_date->format('h:i A') . ' – ' . $slotBooking->end_time->format('h:i A'),
                'created_at' => $slotBooking->created_at?->format('M d, Y h:i A'),
                'total_bookings' => (int) ($bookingCountsByUser[$slotBooking->user_id] ?? 0),
                'cancel_url' => route('admin.bookings.cancel', $slotBooking),
            ])->values(),
        ]);
    }

    public function adminCancel(Request $request, Booking $booking)
    {
        if (Auth::user()->role !== 'manager') {
            abort(403);
        }

        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        if ($booking->status === Booking::STATUS_CANCELLED) {
            return response()->json([
                'message' => 'This booking was already cancelled.',
            ], 409);
        }

        $booking->load('user');

        DB::transaction(function () use ($booking, $validated) {
            DB::table('booking_slot_claims')->where('booking_id', $booking->id)->delete();

            $booking->update([
                'status' => Booking::STATUS_CANCELLED,
                'rejection_reason' => $validated['reason'],
                'rejected_at' => now(),
            ]);

            UserActivityLog::record(
                'booking.admin_cancelled',
                'Administration cancelled a resident facility booking.',
                $booking->user,
                Auth::user(),
                [
                    'booking_id' => $booking->id,
                    'facility' => $booking->facility_name,
                    'reason' => $validated['reason'],
                ]
            );
        });

        $booking->user->notify(new BookingStatusChangedNotification($booking->fresh(), 'cancelled'));

        return response()->json([
            'message' => 'Booking cancelled. The slot has been released.',
        ]);
    }

    protected function facilitySlotIsFull(
        string $facilityName,
        Carbon $startDateTime,
        Carbon $endDateTime,
        ?int $exceptBookingId = null
    ): bool
    {
        $reservedCount = Booking::where('facility_name', $facilityName)
            ->where('status', Booking::STATUS_APPROVED)
            ->where('booking_date', '<', $endDateTime)
            ->where('end_time', '>', $startDateTime)
            ->when($exceptBookingId, fn ($query) => $query->where('id', '!=', $exceptBookingId))
            ->count();

        return $reservedCount >= $this->facilities[$facilityName]['capacity'];
    }

    protected function slotLockKey(string $facilityName, Carbon $startDateTime): string
    {
        return 'booking-slot:' . sha1($facilityName . '|' . $startDateTime->format('Y-m-d H:i'));
    }

    protected function residentBookingLockKey(int $userId): string
    {
        return 'resident-bookings:' . $userId;
    }

    /**
     * Upsert a booking_slot_claims row for the given booking.
     *
     * This table serves two purposes (M-01):
     *
     * 1. DB-level safety net — the remaining unique constraint
     *    `booking_claims_user_slot_unique (user_id, booking_date)` provides
     *    a database-enforced guarantee that the same resident cannot hold two
     *    claims at the same exact datetime. This catches the race window that
     *    can exist between residentApprovedOverlapExists() and Booking::create().
     *    The facility+date unique constraint was intentionally dropped (migration
     *    2026_06_03_010000) to support multi-capacity slots.
     *
     * 2. Cancellation cleanup — cancelFutureBookings() deletes rows here first
     *    before bulk-cancelling bookings, preserving referential consistency.
     */
    protected function claimSlot(Booking $booking): void
    {
        $claim = [
            'user_id'       => $booking->user_id,
            'facility_name' => $booking->facility_name,
            'booking_date'  => $booking->booking_date,
            'updated_at'    => now(),
        ];

        if (DB::table('booking_slot_claims')->where('booking_id', $booking->id)->exists()) {
            DB::table('booking_slot_claims')->where('booking_id', $booking->id)->update($claim);
        } else {
            DB::table('booking_slot_claims')->insert([
                'booking_id'    => $booking->id,
                'user_id'       => $booking->user_id,
                'facility_name' => $booking->facility_name,
                'booking_date'  => $booking->booking_date,
                'updated_at'    => now(),
                'created_at'    => now(),
            ]);
        }
    }

    protected function residentApprovedOverlapExists(
        int $userId,
        Carbon $startDateTime,
        Carbon $endDateTime,
        ?int $exceptBookingId = null
    ): bool {
        return Booking::where('user_id', $userId)
            ->where('status', 'approved')
            ->where('booking_date', '<', $endDateTime)
            ->where('end_time', '>', $startDateTime)
            ->when($exceptBookingId, fn ($query) => $query->where('id', '!=', $exceptBookingId))
            ->exists();
    }

    protected function conflictResponse(string $message)
    {
        return back()
            ->withErrors(['booking_time' => $message])
            ->withInput()
            ->with('error', $message);
    }

    protected function slotCapacityMap(string $facilityName, $bookings): array
    {
        $capacity = $this->facilities[$facilityName]['capacity'];
        $counts = $bookings
            ->groupBy(fn ($booking) => $booking->booking_date->format('H:i'))
            ->map->count();

        return collect($this->facilities[$facilityName]['slots'])
            ->mapWithKeys(function ($slot) use ($counts, $capacity) {
                $reserved = (int) ($counts[$slot] ?? 0);

                return [$slot => [
                    'reserved' => $reserved,
                    'capacity' => $capacity,
                    'available' => max(0, $capacity - $reserved),
                ]];
            })
            ->all();
    }

    protected function availabilityRecommendations(string $facilityName, Carbon $date, $reservedSlots): array
    {
        $recommendations = [];

        foreach ($this->facilities as $otherFacility => $facility) {
            if ($otherFacility === $facilityName) {
                continue;
            }

            $otherBookings = Booking::where('facility_name', $otherFacility)
                ->where('status', 'approved')
                ->active()
                ->whereDate('booking_date', $date->toDateString())
                ->get();
            $otherCapacity = $this->slotCapacityMap($otherFacility, $otherBookings);
            $otherFullSlots = collect($otherCapacity)
                ->filter(fn ($slot) => $slot['available'] <= 0)
                ->keys()
                ->all();

            $available = collect($facility['slots'])->diff($otherFullSlots)->values()->all();
            if ($available !== []) {
                $recommendations[] = [
                    'facility_name' => $otherFacility,
                    'available_slots' => $available,
                ];
            }
        }

        return $recommendations;
    }
}
