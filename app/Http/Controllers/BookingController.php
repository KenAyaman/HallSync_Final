<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BookingController extends Controller
{
    protected array $facilities = [
        'Study Room 1' => [
            'capacity' => 4,
            'hours' => '8:00 AM - 10:00 PM',
            'slots' => ['08:00', '09:00', '10:00', '11:00', '13:00', '14:00', '15:00', '16:00'],
        ],
        'Study Room 2' => [
            'capacity' => 4,
            'hours' => '8:00 AM - 10:00 PM',
            'slots' => ['08:00', '09:00', '10:00', '11:00', '13:00', '14:00', '15:00', '16:00'],
        ],
        'Conference Room' => [
            'capacity' => 12,
            'hours' => '9:00 AM - 9:00 PM',
            'slots' => ['09:00', '10:00', '11:00', '13:00', '14:00', '15:00', '16:00'],
        ],
        'Gym' => [
            'capacity' => 10,
            'hours' => '6:00 AM - 11:00 PM',
            'slots' => ['06:00', '07:00', '08:00', '09:00', '10:00', '16:00', '17:00', '18:00'],
        ],
        'Game Room' => [
            'capacity' => 8,
            'hours' => '10:00 AM - 11:00 PM',
            'slots' => ['10:00', '11:00', '13:00', '14:00', '15:00', '16:00', '17:00'],
        ],
        'Laundry Room' => [
            'capacity' => 6,
            'hours' => '6:00 AM - 12:00 AM',
            'slots' => ['06:00', '07:00', '08:00', '09:00', '10:00', '13:00', '14:00', '15:00'],
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

        $bookings = Booking::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('resident.bookings.index', compact('bookings'));
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
        if (Auth::user()->role === 'handyman') {
            abort(403);
        }

        $request->validate([
            'facility_name' => ['required', 'string', 'in:' . implode(',', array_keys($this->facilities))],
            'booking_date' => ['required', 'date', 'after_or_equal:today'],
            'booking_time' => ['required', 'date_format:H:i'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'booking_date.after_or_equal' => 'You cannot book a facility for a past date.',
            'booking_time.required' => 'Please select a time slot.',
        ]);

        $facility = $this->facilities[$request->facility_name];

        if (!in_array($request->booking_time, $facility['slots'], true)) {
            return back()
                ->withErrors(['booking_time' => 'That time slot is not valid for the selected facility.'])
                ->withInput();
        }

        $startDateTime = Carbon::parse($request->booking_date . ' ' . $request->booking_time);
        $endDateTime = (clone $startDateTime)->addHour();

        if ($startDateTime->lt(Carbon::now()->startOfMinute())) {
            return back()
                ->withErrors(['booking_date' => 'You cannot book a past date or time slot.'])
                ->withInput();
        }

        $activeBookings = Booking::where('user_id', Auth::id())
            ->where('status', 'approved')
            ->where('booking_date', '>', Carbon::now())
            ->count();

        if ($activeBookings >= 3) {
            return back()
                ->withErrors(['booking_date' => 'You already have 3 upcoming bookings. Please cancel one before reserving another slot.'])
                ->withInput();
        }

        $existingBooking = Booking::where('facility_name', $request->facility_name)
            ->where('status', 'approved')
            ->where('booking_date', $startDateTime)
            ->exists();

        if ($existingBooking) {
            return back()
                ->withErrors(['booking_time' => 'That time slot is already reserved for this facility.'])
                ->withInput();
        }

        $booking = Booking::create([
            'user_id' => Auth::id(),
            'facility_name' => $request->facility_name,
            'booking_date' => $startDateTime,
            'end_time' => $endDateTime,
            'notes' => $request->notes,
            'status' => 'approved',
        ]);

        return redirect()
            ->route('bookings.index')
            ->with('success', 'Booking confirmed successfully. Booking ID: #' . $booking->id);
    }

    public function show(Booking $booking)
    {
        $user = Auth::user();

        if ($user->role === 'manager') {
            return view('resident.bookings.show', compact('booking'));
        }

        if ($booking->user_id !== $user->id) {
            abort(403);
        }

        return view('resident.bookings.show', compact('booking'));
    }

    public function edit(Booking $booking)
    {
        $user = Auth::user();

        if ($user->role !== 'resident' || $booking->user_id !== $user->id) {
            abort(403);
        }

        $facilities = $this->facilities;
        $selectedTime = $booking->booking_date->format('H:i');
        $selectedDate = $booking->booking_date->format('Y-m-d');

        return view('resident.bookings.edit', compact('booking', 'facilities', 'selectedTime', 'selectedDate'));
    }

    public function update(Request $request, Booking $booking)
    {
        $user = Auth::user();

        if ($user->role !== 'resident' || $booking->user_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'facility_name' => ['required', 'string', 'in:' . implode(',', array_keys($this->facilities))],
            'booking_date' => ['required', 'date', 'after_or_equal:today'],
            'booking_time' => ['required', 'date_format:H:i'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $facility = $this->facilities[$request->facility_name];

        if (!in_array($request->booking_time, $facility['slots'], true)) {
            return back()
                ->withErrors(['booking_time' => 'That time slot is not valid for the selected facility.'])
                ->withInput();
        }

        $startDateTime = Carbon::parse($request->booking_date . ' ' . $request->booking_time);
        $endDateTime = (clone $startDateTime)->addHour();

        if ($startDateTime->lt(Carbon::now()->startOfMinute())) {
            return back()
                ->withErrors(['booking_date' => 'You cannot book a past date or time slot.'])
                ->withInput();
        }

        $existingBooking = Booking::where('facility_name', $request->facility_name)
            ->where('id', '!=', $booking->id)
            ->where('status', 'approved')
            ->where('booking_date', $startDateTime)
            ->exists();

        if ($existingBooking) {
            return back()
                ->withErrors(['booking_time' => 'That time slot is already reserved for this facility.'])
                ->withInput();
        }

        $booking->update([
            'facility_name' => $request->facility_name,
            'booking_date' => $startDateTime,
            'end_time' => $endDateTime,
            'notes' => $request->notes,
        ]);

        return redirect()
            ->route('bookings.index')
            ->with('success', 'Booking updated successfully!');
    }

    public function destroy(Booking $booking)
    {
        $user = Auth::user();

        if ($user->role !== 'resident' || $booking->user_id !== $user->id) {
            abort(403);
        }

        $booking->delete();

        return redirect()
            ->route('bookings.index')
            ->with('success', 'Booking cancelled successfully.');
    }

    public function getReservedSlots(Request $request)
    {
        $request->validate([
            'facility_name' => ['required', 'string', 'in:' . implode(',', array_keys($this->facilities))],
            'booking_date' => ['required', 'date'],
        ]);

        $date = Carbon::parse($request->booking_date);

        $reservedSlots = Booking::where('facility_name', $request->facility_name)
            ->where('status', 'approved')
            ->whereDate('booking_date', $date->toDateString())
            ->orderBy('booking_date')
            ->get()
            ->map(fn ($booking) => $booking->booking_date->format('H:i'))
            ->values();

        return response()->json([
            'reserved_slots' => $reservedSlots,
            'available_slots' => $this->facilities[$request->facility_name]['slots'],
            'facility' => $this->facilities[$request->facility_name],
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

        // Get all bookings for the selected date
        $bookings = Booking::whereDate('booking_date', $selectedDate)
            ->with('user')
            ->get()
            ->groupBy('facility_name');
        
        // Define time slots (8 AM to 10 PM)
        $timeSlots = [];
        for ($hour = 8; $hour <= 22; $hour++) {
            $timeSlots[] = sprintf('%02d:00', $hour);
        }
        
        $facilities = ['Study Room 1', 'Study Room 2', 'Conference Room', 'Gym', 'Game Room', 'Laundry Room'];
        
        // Build calendar grid
        $calendar = [];
        foreach ($facilities as $facility) {
            foreach ($timeSlots as $slot) {
                $booking = $bookings->get($facility)?->first(function($b) use ($slot) {
                    return $b->booking_date->format('H:00') == $slot;
                });
                $calendar[$facility][$slot] = $booking;
            }
        }
        
        // Get weekly stats
        $weekStart = $selectedDate->copy()->startOfWeek();
        $weekEnd = $selectedDate->copy()->endOfWeek();
        
        $weeklyBookings = Booking::whereBetween('booking_date', [$weekStart, $weekEnd])
            ->get()
            ->groupBy(function($b) {
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
                    'pending' => $facilityBookings->where('status', 'pending')->count(),
                    'rejected' => $facilityBookings->where('status', 'rejected')->count(),
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
            'monthlyFacilitySummary'
        ));
    }

    public function getBookingDetails(Booking $booking)
    {
        if (Auth::user()->role !== 'manager') {
            abort(403);
        }
        
        return response()->json([
            'id' => $booking->id,
            'facility_name' => $booking->facility_name,
            'user_name' => $booking->user->name ?? 'Resident',
            'date' => $booking->booking_date->format('F d, Y'),
            'time' => $booking->booking_date->format('h:i A') . ' - ' . $booking->end_time->format('h:i A'),
            'notes' => $booking->notes,
            'status' => $booking->status,
        ]);
    }
}
