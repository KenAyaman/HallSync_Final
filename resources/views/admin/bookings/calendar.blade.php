<x-app-layout>
@php
    $totalBookings = 0;
    foreach ($calendar as $facility) {
        foreach ($facility as $slotBookings) {
            $totalBookings += $slotBookings->count();
        }
    }

    $occupiedSlots = $totalBookings;
    $totalSlots = array_sum($facilityCapacities ?? []) * count($timeSlots);
    $occupancyRate = $totalSlots > 0 ? round(($occupiedSlots / $totalSlots) * 100) : 0;
    $monthBookingCount = $monthlyBookings->count();
    $monthApprovedCount = $monthlyBookings->where('status', 'approved')->count();
    $monthCancelledCount = $monthlyBookings->where('status', 'cancelled')->count();
    $bookingStatusLabel = fn ($status) => match ($status) {
        'approved' => 'Reserved',
        'pending' => 'Pending',
        'cancelled' => 'Cancelled',
        default => ucfirst(str_replace('_', ' ', $status)),
    };
@endphp

<div class="booking-dashboard">
    <div class="booking-shell">
        <section class="admin-overview-hero">
            <div>
                <p class="admin-overview-hero__kicker">HallSync Admin</p>
                <h1 class="admin-overview-hero__title">Booking <span>Calendar</span></h1>
                <span class="admin-overview-hero__subtitle">
                    @if ($viewMode === 'day')
                        A cleaner day-by-day view of Rexhall facility reservations for {{ $selectedDate->format('l, F j, Y') }}.
                    @else
                        Monthly reservation activity for {{ $monthLabel }}, grouped into a simpler management view.
                    @endif
                </span>
            </div>

            @if ($viewMode !== 'day')
                <div class="admin-overview-hero__actions booking-toolbar">
                    <div class="date-nav">
                        <div class="date-badge">{{ $monthLabel }}</div>
                    </div>
                </div>
            @endif
        </section>

        @if ($viewMode === 'day')
            <section class="stats-grid admin-compact-stats admin-compact-stats-4">
                <x-admin-compact-stat icon="calendar" :value="$totalBookings" label="Total Bookings" note="Scheduled today" />
                <x-admin-compact-stat icon="clock" :value="$occupancyRate . '%'" label="Occupancy" note="Used slots today" tone="blue" />
                <x-admin-compact-stat icon="check" :value="$totalSlots - $occupiedSlots" label="Available Slots" note="Open today" tone="green" />
                <x-admin-compact-stat icon="building" :value="count($facilities)" label="Facilities" note="Tracked spaces" />
            </section>
            <hr class="app-soft-divider">
            <section class="booking-panel booking-weekly-panel">
                <div class="panel-heading">
                    <div>
                        <h2>Weekly Overview</h2>
                        <p>Select a day or use the compact calendar to jump dates.</p>
                    </div>
                    <div class="date-nav date-nav-inline">
                        <form method="GET" action="{{ route('admin.bookings.calendar') }}" class="date-picker-form">
                            <input type="hidden" name="view" value="day">
                            <label for="bookingDateJump" class="date-picker-label" aria-label="Choose calendar date" data-date-picker-trigger>
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                                <span class="date-picker-display">{{ $selectedDate->format('m / d / Y') }}</span>
                                <input id="bookingDateJump" type="date" name="date" value="{{ $selectedDate->toDateString() }}" onchange="this.form.submit()">
                            </label>
                        </form>
                    </div>
                </div>

                <div class="week-strip">
                    @php
                        $weekStart = $selectedDate->copy()->startOfWeek();
                    @endphp
                    @for ($i = 0; $i < 7; $i++)
                        @php
                            $day = $weekStart->copy()->addDays($i);
                            $dayBookings = $weeklyBookings[$day->format('Y-m-d')] ?? collect();
                            $isToday = $day->isToday();
                            $isSelected = $day->isSameDay($selectedDate);
                        @endphp
                        <a href="{{ route('admin.bookings.calendar', ['view' => 'day', 'date' => $day->toDateString()]) }}"
                           class="week-day {{ $isSelected ? 'is-selected' : '' }} {{ $isToday ? 'is-today' : '' }}"
                           aria-current="{{ $isSelected ? 'date' : 'false' }}"
                           title="{{ $day->format('l, F j') }}">
                            <span class="week-day-name">{{ $day->format('D') }}</span>
                            <span class="week-day-number">{{ $day->format('d') }}</span>
                            <span class="week-day-count">{{ $dayBookings->count() }} bookings</span>
                        </a>
                    @endfor
                </div>
            </section>

            <section class="booking-panel">
                <div class="panel-heading">
                    <div>
                        <h2>Daily Grid</h2>
                        <p>Open any time block to review all reserved and available capacity cells.</p>
                    </div>
                </div>

                <div class="calendar-legend">
                    <span class="calendar-legend-item">
                        <span class="calendar-legend-swatch calendar-legend-swatch-booked"></span>
                        Reserved slot
                    </span>
                    <span class="calendar-legend-item">
                        <span class="calendar-legend-swatch calendar-legend-swatch-open"></span>
                        Available slot
                    </span>
                </div>

                <div class="calendar-table-wrap">
                    <table class="calendar-table">
                        <colgroup>
                            <col class="calendar-col-time">
                            @foreach ($facilities as $facility)
                                <col class="calendar-col-facility">
                            @endforeach
                        </colgroup>
                        <thead>
                            <tr>
                                <th>Time</th>
                                @foreach ($facilities as $facility)
                                    <th>{{ $facility }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($timeSlots as $slot)
                                @php
                                    $displayTime = date('g:i A', strtotime($slot));
                                @endphp
                                <tr>
                                    <td class="time-cell">{{ $displayTime }}</td>
                                    @foreach ($facilities as $facility)
                                        @php
                                            $slotBookings = $calendar[$facility][$slot] ?? collect();
                                            $slotBookingCount = $slotBookings->count();
                                            $slotCapacity = $facilityCapacities[$facility] ?? 0;
                                            $slotNames = $slotBookings
                                                ->map(fn ($booking) => $booking->user->name ?? 'Resident')
                                                ->filter()
                                                ->take(3)
                                                ->values();
                                            $slotRange = date('h:i A', strtotime($slot)) . ' - ' . date('h:i A', strtotime($slot . ' +1 hour'));
                                        @endphp
                                        <td>
                                            <button type="button"
                                                    class="booking-chip {{ $slotBookingCount > 0 ? 'booking-chip-reserved' : 'booking-chip-empty' }}"
                                                    onclick="@if($slotBookingCount > 0) showBookingDetails({{ $slotBookings->first()->id }}) @else showEmptySlotDetails(@js($facility), @js($selectedDate->format('F d, Y')), @js($slotRange), {{ $slotCapacity }}) @endif">
                                                @if($slotBookingCount > 0)
                                                    <span class="booking-chip-name">{{ $slotBookingCount }}/{{ $slotCapacity }} reserved</span>
                                                    <span class="booking-chip-residents">
                                                        {{ Str::limit($slotNames->join(', ') . ($slotBookingCount > 3 ? ' +' . ($slotBookingCount - 3) . ' more' : ''), 42) }}
                                                    </span>
                                                @else
                                                    <span class="booking-chip-name booking-chip-name-empty">Available</span>
                                                @endif
                                                <span class="booking-chip-time">{{ date('h:i A', strtotime($slot)) }}</span>
                                            </button>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @else
            <section class="stats-grid admin-compact-stats admin-compact-stats-4">
                <x-admin-compact-stat icon="calendar" :value="$monthBookingCount" label="Bookings" :note="$monthLabel" />
                <x-admin-compact-stat icon="check" :value="$monthApprovedCount" label="Reserved" note="Confirmed reservations" tone="green" />
                <x-admin-compact-stat icon="clock" :value="$monthCancelledCount" label="Cancelled" note="Resident cancelled" tone="blue" />
                <x-admin-compact-stat icon="building" :value="$monthlyFacilitySummary->count()" label="Facilities Used" note="Spaces with bookings" />
            </section>

            <div class="month-grid">
                <section class="booking-panel">
                    <div class="panel-heading">
                        <div>
                            <h2>Facility Summary</h2>
                            <p>High-level usage for {{ $monthLabel }}</p>
                        </div>
                    </div>

                    <div class="summary-list" data-progressive-list>
                        @forelse ($monthlyFacilitySummary as $summary)
                            <article class="summary-row" data-progressive-item>
                                <div>
                                    <h3>{{ $summary['facility_name'] }}</h3>
                                    <p>{{ $summary['approved'] }} reserved, {{ $summary['cancelled'] }} cancelled</p>
                                </div>
                                <strong>{{ $summary['count'] }}</strong>
                            </article>
                        @empty
                            <x-admin-empty-state compact icon="archive" title="No bookings found" description="Facility usage for {{ $monthLabel }} will appear here." />
                        @endforelse
                    </div>
                </section>

                <section class="booking-panel">
                    <div class="panel-heading">
                        <div>
                            <h2>Booking History</h2>
                            <p>Most recent bookings in {{ $monthLabel }}</p>
                        </div>
                    </div>

                    <div class="history-list" data-progressive-list>
                        @forelse ($monthlyBookings as $booking)
                            <article class="history-row" data-progressive-item>
                                <div>
                                    <h3>{{ $booking->facility_name }}</h3>
                                    <p>{{ $booking->user->name ?? 'Resident' }} • {{ $booking->booking_date->format('M d, Y h:i A') }}</p>
                                </div>
                                <span class="status-badge status-{{ $booking->status }}">{{ $bookingStatusLabel($booking->status) }}</span>
                            </article>
                        @empty
                            <x-admin-empty-state compact icon="archive" title="No booking records" description="Reservation history for this period will appear here." />
                        @endforelse
                    </div>
                </section>
            </div>
        @endif
    </div>
</div>

<div id="bookingModal" class="booking-modal" role="dialog" aria-modal="true" aria-hidden="true" aria-labelledby="booking-modal-title">
    <div class="booking-modal-card">
        <div class="booking-modal-header">
            <h3 id="booking-modal-title">Reservation Details</h3>
            <button type="button" onclick="closeBookingModal()" class="booking-modal-close" aria-label="Close booking details">&times;</button>
        </div>
        <div id="bookingDetails" class="booking-modal-body"></div>
        <button type="button" onclick="closeBookingModal()" class="booking-modal-button">Close</button>
    </div>
</div>

<style>
.booking-dashboard {
    --booking-bg: #1f2023;
    --booking-panel: linear-gradient(180deg, #25272c 0%, #1d1f23 100%);
    --booking-border: rgba(214, 168, 91, 0.16);
    --booking-border-strong: rgba(214, 168, 91, 0.34);
    --booking-gold: #d6a85b;
    --booking-gold-soft: rgba(214, 168, 91, 0.12);
    --booking-text: #f8f3ea;
    --booking-muted: #b3a792;
    --booking-muted-2: #8a7a66;
    --booking-green: #5a8a5a;
    --booking-red: #e07060;
    --booking-shadow: 0 24px 48px rgba(0, 0, 0, 0.28);
    color: var(--booking-text);
    max-width: 1580px;
    width: 100%;
    margin: 0 auto;
}
.booking-shell {
    display: flex;
    flex-direction: column;
    gap: 28px;
}
.booking-hero, .booking-panel, .stat-card {
    border: 1px solid var(--booking-border);
    box-shadow: var(--booking-shadow);
}
.booking-hero {
    display: flex;
    justify-content: space-between;
    gap: 24px;
    align-items: center;
    padding: 36px 44px;
    border-radius: 20px;
    background: linear-gradient(120deg, #111009 0%, #1C1A12 50%, #201E14 100%);
    position: relative;
    overflow: hidden;
}
.booking-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image: linear-gradient(rgba(214, 168, 91, 0.04) 1px, transparent 1px), linear-gradient(90deg, rgba(214, 168, 91, 0.04) 1px, transparent 1px);
    background-size: 48px 48px;
    pointer-events: none;
}
.booking-kicker {
    margin: 0 0 10px;
    font-size: 0.875rem;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    color: var(--booking-gold);
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.booking-kicker::before {
    content: '';
    width: 6px;
    height: 6px;
    border-radius: 999px;
    background: var(--booking-gold);
}
.booking-title {
    margin: 0;
    font-size: clamp(2.5rem, 4vw, 3.5rem);
    line-height: 1.12;
    font-family: 'Playfair Display', serif;
    font-weight: 700;
}
.booking-subtitle {
    margin: 12px 0 0;
    color: rgba(255, 255, 255, 0.62);
    font-size: 1.125rem;
    max-width: 760px;
}
.booking-toolbar {
    display: flex;
    flex-direction: column;
    gap: 14px;
    align-items: flex-end;
    justify-content: center;
    flex-shrink: 0;
}
.mode-switcher, .date-nav {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: flex-end;
}
.mode-pill, .nav-pill, .date-badge {
    padding: 10px 16px;
    border-radius: 999px;
    border: 1px solid var(--booking-border);
    background: rgba(255, 255, 255, 0.04);
    color: var(--booking-text);
    text-decoration: none;
    font-size: 0.92rem;
    transition: 0.2s ease;
}
.date-picker-form {
    margin: 0;
}
.date-picker-label {
    position: relative;
    display: inline-flex;
    align-items: center;
    gap: 9px;
    padding: 8px 18px;
    min-height: 38px;
    border-radius: 999px;
    border: 1px solid rgba(255, 247, 234, 0.38);
    background: rgba(255, 255, 255, 0.08);
    color: #fff7ea;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.10);
    cursor: pointer;
    overflow: hidden;
    user-select: none;
}
.date-picker-label svg,
.date-picker-display {
    position: relative;
    z-index: 1;
    pointer-events: none;
}
.date-picker-display {
    color: #fff7ea;
    font-size: 0.9rem;
    font-weight: 800;
    line-height: 1;
    white-space: nowrap;
}
.date-picker-label input {
    position: absolute;
    inset: 0;
    z-index: 2;
    width: 100%;
    height: 100%;
    padding: 0;
    border: 0;
    outline: none;
    background: transparent;
    color: transparent;
    color-scheme: light;
    cursor: pointer;
    opacity: 0;
}
.date-picker-label input::-webkit-calendar-picker-indicator {
    display: none;
    opacity: 0;
}
.mode-pill:hover, .nav-pill:hover {
    border-color: var(--booking-border-strong);
    color: var(--booking-gold);
}
.mode-pill.is-active, .nav-pill-highlight, .date-badge {
    background: var(--booking-gold-soft);
    border-color: var(--booking-border-strong);
    color: var(--booking-gold);
}
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 16px;
    margin-bottom: 24px; /* Add spacing between stats and weekly overview */
}
.stat-card {
    display: flex;
    flex-direction: column;
    gap: 8px;
    padding: 22px;
    border-radius: 22px;
    background: var(--booking-panel);
}
.stat-label {
    color: var(--booking-muted);
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.16em;
}
.stat-value {
    font-size: 2.2rem;
    color: var(--booking-gold);
    line-height: 1;
}
.stat-note {
    color: var(--booking-muted-2);
    font-size: 0.9rem;
}
.booking-panel {
    overflow: hidden;
    border: 1px solid rgba(107, 79, 58, 0.22);
    border-radius: 14px;
    background: #fffdf8;
    padding: 0;
    box-shadow: 0 14px 28px rgba(79, 58, 44, 0.12);
}
.calendar-legend {
    display: flex;
    flex-wrap: wrap;
    gap: 18px;
    margin: 22px 22px 16px;
    padding: 14px 16px;
    border-radius: 18px;
    background: #fff8ee;
    border: 1px solid #eadcc9;
}
.calendar-legend-item {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    color: #6f604f;
    font-size: 0.9rem;
    font-weight: 600;
}
.calendar-legend-swatch {
    width: 14px;
    height: 14px;
    border-radius: 999px;
    display: inline-block;
}
.calendar-legend-swatch-booked {
    background: linear-gradient(135deg, #d6a85b 0%, #b47721 100%);
    border: 1px solid rgba(214, 168, 91, 0.8);
}
.calendar-legend-swatch-open {
    background: #fffdf8;
    border: 1px solid #d9c9b5;
}
.panel-heading {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
    margin: 0;
    padding: 22px 28px 18px;
    background: #6B4F3A;
}
.date-nav-inline {
    justify-content: flex-end;
}
.panel-heading h2 {
    margin: 0;
    font-size: 1.5rem;
    font-family: 'Playfair Display', serif;
    color: #fff7ea;
}
.panel-heading p {
    margin: 6px 0 0;
    color: rgba(255, 247, 234, 0.78);
    font-size: 0.95rem;
}
.booking-panel .panel-heading,
body.role-manager .admin-content-shell .booking-dashboard .booking-panel .panel-heading {
    border-bottom: 0 !important;
}
.booking-weekly-panel {
    background: #fffdf8 !important;
}
body.role-manager .admin-content-shell .booking-dashboard .booking-weekly-panel {
    background: #fffdf8 !important;
}
.booking-weekly-panel .panel-heading {
    border-bottom: 0 !important;
}
body.role-manager .admin-content-shell .booking-dashboard .booking-weekly-panel .panel-heading {
    border-bottom: 0 !important;
}
.booking-weekly-panel .date-picker-label {
    background: rgba(255, 255, 255, 0.08) !important;
    color: #fff7ea !important;
}
.booking-weekly-panel .date-picker-label input {
    color: transparent !important;
    color-scheme: light !important;
}
body.role-manager .admin-content-shell .booking-dashboard .booking-weekly-panel .date-picker-label {
    background: rgba(255, 255, 255, 0.08) !important;
    border-color: rgba(255, 247, 234, 0.38) !important;
    color: #fff7ea !important;
}
body.role-manager .admin-content-shell .booking-dashboard .booking-weekly-panel .date-picker-label input {
    background: transparent !important;
    border: 0 !important;
    color: transparent !important;
    color-scheme: light !important;
    box-shadow: none !important;
}
body.role-manager .admin-content-shell .booking-dashboard .booking-weekly-panel .date-picker-label input::-webkit-calendar-picker-indicator {
    display: none !important;
    opacity: 0 !important;
}
.week-strip {
    display: grid;
    grid-template-columns: repeat(7, minmax(0, 1fr));
    gap: 10px;
    margin: 0;
    padding: 16px 24px 24px;
    border: 0;
    border-radius: 0;
    background: #fffdf8;
    box-shadow: none;
}
.booking-weekly-panel .week-strip {
    background: #fffdf8 !important;
    border: 0 !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    margin: 0 !important;
    padding: 14px 24px 20px !important;
}
body.role-manager .admin-content-shell .booking-dashboard .booking-weekly-panel .week-strip {
    background: #fffdf8 !important;
    border: 0 !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    margin: 0 !important;
    padding: 14px 24px 20px !important;
}
.week-day {
    display: flex;
    flex-direction: column;
    gap: 2px;
    padding: 8px 10px;
    border-radius: 14px;
    border: 1px solid rgba(255, 247, 234, 0.24);
    background: #6B4F3A;
    text-decoration: none;
    color: #fff7ea;
    text-align: center;
    transition: 0.2s ease;
    position: relative;
}
.booking-weekly-panel .week-day {
    background: #fdf6ea !important;
    border-color: rgba(180, 119, 33, 0.22) !important;
    color: #4c3a2c !important;
}
body.role-manager .admin-content-shell .booking-dashboard .booking-weekly-panel .week-day {
    background: #fdf6ea !important;
    border-color: rgba(180, 119, 33, 0.22) !important;
    color: #4c3a2c !important;
    gap: 2px !important;
    padding: 8px 10px !important;
    border-radius: 14px !important;
}
.week-day:hover, .week-day.is-selected {
    background: #7a5a42;
    border-color: rgba(255, 247, 234, 0.34);
    transform: translateY(-1px);
}
.booking-weekly-panel .week-day:hover,
.booking-weekly-panel .week-day.is-selected {
    background: #7a5a42 !important;
    color: #fff7ea !important;
}
.booking-weekly-panel .week-day:hover .week-day-name,
.booking-weekly-panel .week-day:hover .week-day-count,
.booking-weekly-panel .week-day:hover .week-day-number,
.booking-weekly-panel .week-day.is-selected .week-day-name,
.booking-weekly-panel .week-day.is-selected .week-day-count {
    color: rgba(255, 247, 234, 0.74) !important;
}
.booking-weekly-panel .week-day.is-selected .week-day-number {
    color: #fff7ea !important;
}
.booking-weekly-panel .week-day.is-selected {
    border-color: #f1c879 !important;
    box-shadow: inset 0 0 0 2px rgba(255, 247, 234, 0.42), 0 0 0 3px rgba(214, 168, 91, 0.24) !important;
}
body.role-manager .admin-content-shell .booking-dashboard .booking-weekly-panel .week-day.is-selected {
    border-color: #f1c879 !important;
    box-shadow: inset 0 0 0 2px rgba(255, 247, 234, 0.42), 0 0 0 3px rgba(214, 168, 91, 0.24) !important;
}
.booking-weekly-panel .week-day.is-selected::after {
    content: '';
    position: absolute;
    top: 8px;
    right: 10px;
    width: 7px;
    height: 7px;
    border-radius: 999px;
    background: #f1c879;
    box-shadow: 0 0 0 2px rgba(107, 79, 58, 0.45);
}
.week-day.is-today {
    background: linear-gradient(135deg, #d6a85b 0%, #b8842f 100%);
    border-color: rgba(255, 247, 234, 0.48);
    color: #24180f;
    box-shadow: 0 8px 18px rgba(184, 132, 47, 0.22);
}
.booking-weekly-panel .week-day.is-today {
    background: linear-gradient(135deg, #d6a85b 0%, #b8842f 100%) !important;
    color: #24180f !important;
}
body.role-manager .admin-content-shell .booking-dashboard .booking-weekly-panel .week-day.is-today {
    background: linear-gradient(135deg, #d6a85b 0%, #b8842f 100%) !important;
    border-color: rgba(255, 247, 234, 0.48) !important;
    color: #24180f !important;
}
.booking-weekly-panel .week-day.is-selected.is-today,
body.role-manager .admin-content-shell .booking-dashboard .booking-weekly-panel .week-day.is-selected.is-today {
    border-color: #fff7ea !important;
    box-shadow: inset 0 0 0 2px rgba(36, 24, 15, 0.28), 0 0 0 3px rgba(214, 168, 91, 0.30) !important;
}
.booking-weekly-panel .week-day.is-selected.is-today::after {
    background: #24180f;
    box-shadow: 0 0 0 2px rgba(255, 247, 234, 0.52);
}
.week-day-name, .week-day-count {
    color: rgba(255, 247, 234, 0.74);
    font-size: 0.82rem;
}
.week-day-number {
    font-size: 1.1rem;
    font-weight: 700;
    color: #fff7ea;
}
body.role-manager .admin-content-shell .booking-dashboard .booking-weekly-panel .week-day-name,
body.role-manager .admin-content-shell .booking-dashboard .booking-weekly-panel .week-day-count {
    color: rgba(76, 58, 44, 0.68) !important;
    font-size: 0.82rem !important;
    line-height: 1.25 !important;
}
body.role-manager .admin-content-shell .booking-dashboard .booking-weekly-panel .week-day-number {
    color: #4c3a2c !important;
    font-size: 1.1rem !important;
    line-height: 1.15 !important;
}
.week-day.is-today .week-day-name,
.week-day.is-today .week-day-count,
.week-day.is-today .week-day-number {
    color: #24180f;
}
body.role-manager .admin-content-shell .booking-dashboard .booking-weekly-panel .week-day.is-today .week-day-name,
body.role-manager .admin-content-shell .booking-dashboard .booking-weekly-panel .week-day.is-today .week-day-count,
body.role-manager .admin-content-shell .booking-dashboard .booking-weekly-panel .week-day.is-today .week-day-number {
    color: #24180f !important;
}
.calendar-table-wrap {
    overflow-x: auto;
    margin: 0 22px 22px;
}
.calendar-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    table-layout: fixed;
}
.calendar-col-time {
    width: 96px;
}
.calendar-col-facility {
    width: calc((100% - 96px) / 4);
}
.calendar-table th, .calendar-table td {
    padding: 12px;
    border-bottom: 1px solid #eadcc9;
    vertical-align: top;
}
.calendar-table td:not(:first-child), .calendar-table th:not(:first-child) {
    width: 25%;
}
.calendar-table th {
    text-align: left;
    font-size: 0.82rem;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: #7b6d5d;
    background: #fff8ee;
}
.calendar-table th:not(:first-child) {
    text-align: center;
    vertical-align: middle;
}
.calendar-table td:not(:first-child) {
    vertical-align: middle;
}
.time-cell {
    position: sticky;
    left: 0;
    z-index: 2;
    background: #fffdf8;
    white-space: nowrap;
    color: #7b6d5d;
    font-size: 0.84rem;
    width: 96px;
}
.booking-chip {
    width: 100%;
    text-align: center;
    padding: 12px;
    min-height: 72px;
    border-radius: 16px;
    border: 1px solid rgba(255, 255, 255, 0.08);
    background: #eeeeee;
    color: #8f8a84;
    box-shadow: none;
    cursor: pointer;
    transition: 0.2s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}
.booking-chip-reserved {
    border-color: rgba(255, 238, 198, 0.48) !important;
    background: linear-gradient(135deg, #f0bc62 0%, #d89426 56%, #9b5f12 100%) !important;
    color: #fffaf0 !important;
    box-shadow: inset 0 -16px 28px rgba(87, 48, 8, 0.18), 0 10px 22px rgba(126, 74, 17, 0.22) !important;
}
.booking-chip:hover {
    transform: translateY(-1px);
}
.booking-chip-reserved:hover {
    border-color: rgba(255, 248, 232, 0.74) !important;
    box-shadow: inset 0 -16px 28px rgba(87, 48, 8, 0.22), 0 15px 30px rgba(126, 74, 17, 0.32) !important;
}
.booking-chip-empty {
    border-style: dashed;
    background: #eeeeee !important;
    border-color: #dddddd !important;
    color: #8f8a84 !important;
    box-shadow: none !important;
}
.booking-chip-empty:hover {
    background: #e9e9e9 !important;
    border-color: #d1d1d1 !important;
}
.booking-chip-empty .booking-chip-name {
    color: #6f6a64 !important;
    font-weight: 600;
}
.booking-chip-name-empty {
    color: #9a9590;
    font-size: 0.78rem;
    font-weight: 500;
}
.booking-chip-name, .booking-chip-residents, .booking-chip-time {
    display: block;
}
.booking-chip-reserved .booking-chip-name {
    font-size: 0.92rem;
    font-weight: 800;
    color: #ffffff !important;
}
.booking-chip-reserved .booking-chip-residents {
    width: 100%;
    margin-top: 5px;
    color: rgba(255, 250, 240, 0.86) !important;
    font-size: 0.74rem;
    line-height: 1.25;
}
.booking-chip-empty .booking-chip-residents {
    color: var(--booking-muted-2);
}
.booking-chip-reserved .booking-chip-time {
    margin-top: 4px;
    font-size: 0.82rem;
    color: rgba(255, 250, 240, 0.92) !important;
}
.empty-slot {
    min-height: 72px;
    width: 100%;
    padding: 14px 10px;
    border-radius: 16px;
    background: rgba(255, 255, 255, 0.03);
    text-align: center;
    color: var(--booking-muted-2);
    font-size: 0.82rem;
    border: 1px dashed rgba(255, 255, 255, 0.08);
    display: flex;
    align-items: center;
    justify-content: center;
    line-height: 1.3;
}
.month-grid {
    display: grid;
    grid-template-columns: 1fr 1.1fr;
    gap: 18px;
}
.summary-list, .history-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.summary-row, .history-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding: 16px 18px;
    border-radius: 18px;
    background: #fff8ee;
    border: 1px solid #eadcc9;
}
.summary-row h3, .history-row h3 {
    margin: 0;
    font-size: 1rem;
    color: #342a23;
}
.summary-row p, .history-row p, .empty-copy {
    margin: 6px 0 0;
    color: #7b6d5d;
    font-size: 0.9rem;
}
.summary-row strong {
    color: var(--booking-gold);
    font-size: 1.5rem;
}
.status-badge {
    padding: 8px 12px;
    border-radius: 999px;
    font-size: 0.82rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
}
.status-approved {
    background: rgba(90, 138, 90, 0.16);
    color: var(--booking-green);
}
.status-pending {
    background: rgba(214, 168, 91, 0.16);
    color: var(--booking-gold);
}
.booking-modal {
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 18px;
    background: rgba(28, 24, 20, 0.42);
    backdrop-filter: blur(3px);
    z-index: 9999;
}
body.booking-modal-open {
    overflow: hidden;
}
.booking-modal-card {
    width: min(980px, calc(100vw - 32px));
    max-height: min(680px, calc(100vh - 40px));
    overflow-y: auto;
    padding: 22px;
    border-radius: 16px;
    background: #fffdf9;
    border: 1px solid #e5d8c8;
    box-shadow: 0 24px 60px rgba(39, 30, 21, 0.28);
    color: #342a23;
}
.booking-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 14px;
    margin-bottom: 16px;
}
.booking-modal-header h3 {
    margin: 0;
    color: #342a23;
    font-size: 1.3rem;
    font-family: 'Playfair Display', serif;
    font-weight: 700;
}
.booking-modal-close, .booking-modal-button {
    border: 1px solid #e5d8c8;
    background: #fbf3e4;
    color: #8b5b1d;
    cursor: pointer;
}
.booking-modal-close {
    width: 38px;
    height: 38px;
    flex: 0 0 auto;
    border-radius: 8px;
    font-size: 1.4rem;
    line-height: 1;
}
.booking-modal-body {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.booking-modal-item {
    padding: 13px 14px;
    border-radius: 10px;
    background: #f8f0e4;
    border: 1px solid #eadcc9;
}
.booking-modal-label {
    display: block;
    color: #8b7d70;
    font-size: 0.7rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.12em;
    margin-bottom: 6px;
}
.booking-modal-value {
    color: #342a23;
    font-size: 0.95rem;
    font-weight: 600;
    line-height: 1.5;
}
.booking-slot-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px 16px;
    margin-top: 4px;
}
.booking-slot-cell {
    min-height: 76px;
    padding: 13px 14px;
    border: 1px dashed #ead6b8;
    border-radius: 14px;
    background: #f7f4ee;
    color: #786b60;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    font-size: 0.86rem;
    line-height: 1.35;
}
.booking-slot-cell-reserved {
    align-items: flex-start;
    border-style: solid;
    border-color: rgba(255, 238, 198, 0.42);
    background: linear-gradient(135deg, #d6a85b 0%, #b47721 100%);
    color: #fffaf0;
    box-shadow: 0 8px 18px rgba(180, 119, 33, 0.18);
    text-align: left;
    justify-content: flex-start;
    flex-direction: column;
}
.booking-slot-cell-reserved strong {
    color: #ffffff;
    font-size: 0.88rem;
    line-height: 1.3;
}
.booking-slot-cell-reserved span {
    display: block;
    margin-top: 4px;
    color: rgba(255, 250, 240, 0.82);
    font-size: 0.7rem;
    line-height: 1.35;
}
.booking-slot-room {
    color: rgba(255, 250, 240, 0.92) !important;
    font-weight: 700;
}
.booking-slot-history {
    color: rgba(255, 250, 240, 0.76) !important;
    font-size: 0.65rem !important;
    margin-top: 3px !important;
}
.booking-slot-cancel-btn {
    margin-top: 10px;
    width: 100%;
    padding: 8px 10px;
    border: 1px solid rgba(255, 250, 240, 0.46);
    border-radius: 8px;
    background: rgba(224, 112, 96, 0.94);
    color: #fffaf0;
    cursor: pointer;
    font-size: 0.72rem;
    font-weight: 800;
    line-height: 1.2;
    transition: 0.18s ease;
}
.booking-slot-cancel-btn:hover {
    background: #c95749;
    transform: translateY(-1px);
}
.booking-cancel-reason {
    width: 100%;
    min-height: 104px;
    padding: 12px 13px;
    border: 1px solid rgba(214, 168, 91, 0.24);
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.06);
    color: #f4eadc;
    font: inherit;
    font-size: 0.92rem;
    line-height: 1.5;
    outline: none;
    resize: vertical;
}
.booking-cancel-reason:focus {
    border-color: rgba(214, 168, 91, 0.58);
    box-shadow: 0 0 0 3px rgba(214, 168, 91, 0.12);
}
.booking-cancel-reason::placeholder {
    color: rgba(244, 234, 220, 0.48);
}
.booking-cancel-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 4px;
}
.booking-cancel-secondary, .booking-cancel-danger {
    min-height: 52px;
    padding: 0 22px;
    border-radius: 999px;
    cursor: pointer;
    font: inherit;
    font-size: 0.95rem;
    font-weight: 800;
}
.booking-cancel-secondary {
    border: 1px solid rgba(214, 168, 91, 0.18);
    background: rgba(255, 255, 255, 0.04);
    color: #e8e0d3;
}
.booking-cancel-danger {
    border: 1px solid rgba(214, 168, 91, 0.16);
    background: linear-gradient(135deg, #c79745 0%, #d6a85b 100%);
    color: #1a1714;
}
.booking-cancel-danger:disabled {
    cursor: wait;
    opacity: 0.72;
}
.booking-modal-card.booking-cancel-confirm {
    width: min(480px, calc(100vw - 32px));
    padding: 24px;
    border-radius: 22px;
    border: 1px solid rgba(214, 168, 91, 0.18);
    background: rgba(30, 27, 23, 0.98);
    color: #f0e9df;
    box-shadow: 0 26px 70px rgba(0, 0, 0, 0.32);
}
.booking-cancel-confirm .booking-modal-header {
    display: grid;
    grid-template-columns: 42px 1fr;
    align-items: center;
    justify-content: start;
    gap: 16px;
    margin-bottom: 10px;
}
.booking-cancel-confirm .booking-modal-header::before {
    content: '!';
    width: 42px;
    height: 42px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 14px;
    background: rgba(214, 168, 91, 0.13);
    color: #d6a85b;
    font-weight: 900;
}
.booking-cancel-confirm .booking-modal-header h3 {
    color: #f0e9df;
    font-family: inherit;
    font-size: 1.12rem;
    font-weight: 800;
}
.booking-cancel-confirm .booking-modal-close, .booking-cancel-confirm .booking-modal-button {
    display: none;
}
.booking-cancel-confirm .booking-modal-body {
    display: grid;
    gap: 14px;
    padding-left: 58px;
}
.booking-cancel-copy {
    margin: 0;
    color: #c4b8a8;
    font-size: 1rem;
    line-height: 1.6;
}
.booking-cancel-confirm .booking-modal-item {
    padding: 0;
    border: 0;
    background: transparent;
}
.booking-cancel-confirm .booking-modal-label {
    color: #d6a85b;
    font-size: 0.72rem;
}
.booking-modal-button {
    margin-top: 14px;
    width: 100%;
    padding: 12px 14px;
    border-radius: 8px;
    font-size: 0.95rem;
    font-weight: 800;
}
.booking-modal-close:hover, .booking-modal-button:hover {
    border-color: #d7bd94;
    background: #f4e2c4;
    color: #754713;
}
@media (max-width:1100px) {
    .stats-grid, .month-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}
@media (max-width:820px) {
    .booking-hero {
        flex-direction: column;
        padding: 24px;
        align-items: flex-start;
    }
    .booking-toolbar {
        width: 100%;
        align-items: flex-start;
    }
    .mode-switcher, .date-nav {
        width: 100%;
        justify-content: flex-start;
    }
    .stats-grid {
        display: grid;
        grid-template-columns: 1fr;
    }
    .month-grid {
        grid-template-columns: 1fr;
    }
    .week-strip {
        grid-template-columns: repeat(7, minmax(120px, 1fr));
        overflow-x: auto;
        padding-bottom: 6px;
    }
    .calendar-table {
        min-width: 760px;
    }
    .panel-heading {
        align-items: flex-start;
    }
    .date-nav-inline {
        width: 100%;
        justify-content: flex-start;
    }
}
@media (max-width:640px) {
    .booking-hero {
        padding: 20px;
        border-radius: 22px;
    }
    .booking-panel {
        padding: 0;
        border-radius: 14px;
    }
    .booking-panel .panel-heading {
        padding: 20px;
    }
    .booking-panel .week-strip,
    .booking-panel .calendar-legend,
    .booking-panel .calendar-table-wrap,
    .booking-panel .summary-list,
    .booking-panel .history-list {
        margin-left: 14px;
        margin-right: 14px;
    }
    .booking-title {
        font-size: 2.1rem;
    }
    .booking-subtitle {
        font-size: 0.98rem;
        line-height: 1.6;
    }
    .mode-pill, .nav-pill, .date-badge {
        padding: 9px 14px;
        font-size: 0.84rem;
    }
    .summary-row, .history-row {
        flex-direction: column;
        align-items: flex-start;
    }
    .booking-modal-card {
        padding: 18px;
        border-radius: 14px;
    }
    .booking-slot-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}
@media (max-width:768px) {
    .booking-shell {
        gap: 18px;
    }
    .booking-panel {
        padding: 16px;
        border-radius: 16px;
    }
    .booking-panel .panel-heading {
        padding: 0;
        margin-bottom: 14px;
        gap: 10px;
    }
    .booking-panel .panel-heading h2 {
        font-size: 1.2rem;
        line-height: 1.15;
    }
    .booking-panel .panel-heading p {
        font-size: 0.9rem;
        line-height: 1.45;
    }
    .week-strip {
        display: grid;
        grid-template-columns: repeat(7, minmax(74px, 1fr));
        gap: 8px;
        overflow-x: auto;
        padding-bottom: 4px;
        scroll-snap-type: x proximity;
    }
    .week-day {
        min-height: 74px;
        padding: 10px 8px;
        border-radius: 14px;
        scroll-snap-align: start;
    }
    .week-day-count {
        font-size: 0.68rem;
        line-height: 1.2;
    }
    .calendar-legend {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        margin: 0 0 12px;
    }
    .calendar-legend-item {
        min-height: 40px;
        justify-content: center;
        border-radius: 12px;
        background: #fff8ee;
    }
    .calendar-table-wrap {
        overflow: visible;
        margin: 0;
    }
    .calendar-table,
    .calendar-table thead,
    .calendar-table tbody,
    .calendar-table tr,
    .calendar-table td {
        display: block;
        width: 100%;
    }
    .calendar-table {
        min-width: 0 !important;
        border-collapse: separate;
        border-spacing: 0;
    }
    .calendar-table colgroup,
    .calendar-table thead {
        display: none;
    }
    .calendar-table tbody {
        display: grid;
        gap: 14px;
    }
    .calendar-table tr {
        display: grid;
        grid-template-columns: 1fr;
        gap: 8px;
        padding: 14px;
        border: 1px solid #eadcc9;
        border-radius: 16px;
        background: #fffdf8;
        box-shadow: 0 10px 22px rgba(79, 58, 44, 0.08);
    }
    .calendar-table th,
    .calendar-table td {
        padding: 0 !important;
        border: 0;
    }
    .time-cell {
        position: static;
        width: 100%;
        padding: 0 0 4px !important;
        background: transparent;
        color: #6f4a1d;
        font-size: 0.95rem;
        font-weight: 900;
        letter-spacing: 0;
    }
    .calendar-table td:not(:first-child) {
        width: 100%;
    }
    .calendar-table td:not(:first-child)::before {
        display: block;
        margin: 2px 0 6px;
        color: #8b7d70;
        font-size: 0.68rem;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }
    .calendar-table td:nth-child(2)::before {
        content: "Study Room 1";
    }
    .calendar-table td:nth-child(3)::before {
        content: "Study Room 2";
    }
    .calendar-table td:nth-child(4)::before {
        content: "Conference Room";
    }
    .calendar-table td:nth-child(5)::before {
        content: "Gym";
    }
    .booking-chip {
        min-height: 58px;
        padding: 12px 14px;
        align-items: flex-start;
        text-align: left;
        border-radius: 14px;
    }
    .booking-chip-name,
    .booking-chip-residents,
    .booking-chip-time {
        width: 100%;
        text-align: left;
    }
    .booking-chip-time {
        margin-top: 3px;
        font-size: 0.76rem;
    }
    .booking-modal {
        align-items: flex-end;
        padding: 12px;
    }
    .booking-modal-card {
        width: 100%;
        max-height: 86dvh;
        padding: 18px;
        border-radius: 22px 22px 16px 16px;
    }
    .booking-modal-close {
        width: 44px;
        height: 44px;
        border-radius: 12px;
    }
}
@media (max-width:520px) {
    .booking-slot-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
let bookingModalPreviousFocus = null;

document.querySelectorAll('[data-date-picker-trigger]').forEach((trigger) => {
    const input = trigger.querySelector('input[type="date"]');

    if (!input) {
        return;
    }

    trigger.addEventListener('click', (event) => {
        event.preventDefault();
        input.focus();

        if (typeof input.showPicker === 'function') {
            input.showPicker();
        }
    });
});

function bookingDetailItem(label, value) {
    const item = document.createElement('div');
    const labelNode = document.createElement('span');
    const valueNode = document.createElement('div');

    item.className = 'booking-modal-item';
    labelNode.className = 'booking-modal-label';
    valueNode.className = 'booking-modal-value';
    labelNode.textContent = label;
    valueNode.textContent = value || 'Not provided';
    item.append(labelNode, valueNode);

    return item;
}

function bookingSlotGrid(label, capacity, residents = []) {
    const item = document.createElement('div');
    const labelNode = document.createElement('span');
    const gridNode = document.createElement('div');
    const total = Math.max(Number(capacity) || residents.length || 0, residents.length);

    item.className = 'booking-modal-item';
    labelNode.className = 'booking-modal-label';
    gridNode.className = 'booking-slot-grid';
    labelNode.textContent = label;

    for (let index = 0; index < total; index++) {
        const resident = residents[index];
        const cell = document.createElement('div');

        cell.className = resident ? 'booking-slot-cell booking-slot-cell-reserved' : 'booking-slot-cell';

        if (resident) {
            const name = document.createElement('strong');
            const roomLine = document.createElement('span');
            const metaLine = document.createElement('span');
            const historyLine = document.createElement('span');
            const cancelButton = document.createElement('button');
            name.textContent = resident.name || 'Resident';
            roomLine.className = 'booking-slot-room';
            const roomParts = [
                resident.room_number ? `Room ${resident.room_number}` : null,
                resident.resident_number ? `#${resident.resident_number}` : null,
            ].filter(Boolean);
            roomLine.textContent = roomParts.length ? roomParts.join(' · ') : '';
            metaLine.textContent = resident.time_slot || '';
            historyLine.className = 'booking-slot-history';
            historyLine.textContent = [
                resident.group_members ? `Group: ${resident.group_members}` : null,
                resident.notes ? `Note: ${resident.notes}` : null,
                resident.total_bookings != null ? `${resident.total_bookings} total booking${resident.total_bookings !== 1 ? 's' : ''}` : null,
            ].filter(Boolean).join(' · ') || 'Booked';
            cancelButton.type = 'button';
            cancelButton.className = 'booking-slot-cancel-btn';
            cancelButton.textContent = 'Cancel Booking';
            cancelButton.addEventListener('click', () => openBookingCancelDialog(resident));
            cell.append(name, ...(roomLine.textContent ? [roomLine] : []), metaLine, historyLine, cancelButton);
        } else {
            cell.textContent = 'Available';
        }

        gridNode.appendChild(cell);
    }

    item.append(labelNode, gridNode);
    return item;
}

function openBookingModalWithItems(items) {
    const modal = document.getElementById('bookingModal');
    const card = modal.querySelector('.booking-modal-card');
    const details = document.getElementById('bookingDetails');

    document.getElementById('booking-modal-title').textContent = 'Reservation Details';
    card.classList.remove('booking-cancel-confirm');
    bookingModalPreviousFocus = document.activeElement;
    if (modal.parentElement !== document.body) {
        document.body.appendChild(modal);
    }
    document.body.classList.add('booking-modal-open');
    document.body.style.overflow = 'hidden';
    details.replaceChildren(...items);
    modal.style.display = 'flex';
    modal.setAttribute('aria-hidden', 'false');
    modal.querySelector('.booking-modal-close').focus();
}

function showEmptySlotDetails(facilityName, dateLabel, timeLabel, capacity) {
    openBookingModalWithItems([
        bookingDetailItem('Facility', facilityName),
        bookingDetailItem('Slot Capacity', `0/${capacity} reserved, ${capacity} available`),
        bookingDetailItem('Date and Time', `${dateLabel} | ${timeLabel}`),
        bookingSlotGrid('Slot Availability', capacity, []),
    ]);
}

function showBookingDetails(bookingId) {
    const modal = document.getElementById('bookingModal');
    const card = modal.querySelector('.booking-modal-card');
    const details = document.getElementById('bookingDetails');

    document.getElementById('booking-modal-title').textContent = 'Reservation Details';
    card.classList.remove('booking-cancel-confirm');
    bookingModalPreviousFocus = document.activeElement;
    if (modal.parentElement !== document.body) {
        document.body.appendChild(modal);
    }
    document.body.classList.add('booking-modal-open');
    document.body.style.overflow = 'hidden';
    details.replaceChildren(bookingDetailItem('Loading', 'Loading booking details...'));
    details.setAttribute('aria-busy', 'true');
    modal.style.display = 'flex';
    modal.setAttribute('aria-hidden', 'false');
    modal.querySelector('.booking-modal-close').focus();

    fetch(`/admin/bookings/${bookingId}/details`, {
        headers: { Accept: 'application/json' },
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Unable to load booking details.');
            }

            return response.json();
        })
        .then(data => {
            const reservedCount = data.reserved_count ?? 1;
            const capacity = data.capacity ?? reservedCount;
            const items = [
                bookingDetailItem('Facility', data.facility_name),
                bookingDetailItem('Slot Capacity', `${reservedCount}/${capacity} reserved, ${data.available_count ?? Math.max(0, capacity - reservedCount)} available`),
                bookingDetailItem('Date and Time', `${data.date} | ${data.time}`),
                bookingSlotGrid('Slot Availability', capacity, data.residents || []),
            ];

            details.replaceChildren(...items);
        })
        .catch(() => {
            details.replaceChildren(bookingDetailItem('Unable to load', 'Please close this dialog and try again.'));
            window.appToast?.('error', 'Could not load booking details. Please try again.');
        })
        .finally(() => details.removeAttribute('aria-busy'));
}

function openBookingCancelDialog(resident) {
    const modal = document.getElementById('bookingModal');
    const card = modal.querySelector('.booking-modal-card');
    const title = document.getElementById('booking-modal-title');
    const details = document.getElementById('bookingDetails');
    const copy = document.createElement('p');
    const reasonItem = document.createElement('div');
    const label = document.createElement('label');
    const textarea = document.createElement('textarea');
    const actions = document.createElement('div');
    const backButton = document.createElement('button');
    const confirmButton = document.createElement('button');

    card.classList.add('booking-cancel-confirm');
    title.textContent = 'Confirm Action';
    copy.className = 'booking-cancel-copy';
    copy.textContent = 'Cancel this reservation? Write the reason before continuing.';
    reasonItem.className = 'booking-modal-item';
    label.className = 'booking-modal-label';
    label.setAttribute('for', 'bookingCancelReason');
    label.textContent = 'Cancellation reason';
    textarea.id = 'bookingCancelReason';
    textarea.className = 'booking-cancel-reason';
    textarea.rows = 4;
    textarea.maxLength = 500;
    textarea.placeholder = 'Write the reason for cancelling this resident booking.';

    actions.className = 'booking-cancel-actions';
    backButton.type = 'button';
    backButton.className = 'booking-cancel-secondary';
    backButton.textContent = 'Cancel';
    backButton.addEventListener('click', () => showBookingDetails(resident.id));
    confirmButton.type = 'button';
    confirmButton.className = 'booking-cancel-danger';
    confirmButton.textContent = 'Continue';
    confirmButton.addEventListener('click', () => submitBookingCancellation(resident, textarea, confirmButton));

    actions.append(backButton, confirmButton);
    reasonItem.append(label, textarea);
    details.replaceChildren(
        copy,
        reasonItem,
        actions
    );
    textarea.focus();
}

function submitBookingCancellation(resident, textarea, button) {
    const reason = textarea.value.trim();

    if (reason.length < 5) {
        window.appToast?.('warning', 'Please enter a cancellation reason with at least 5 characters.');
        textarea.focus();
        return;
    }

    button.disabled = true;
    button.textContent = 'Cancelling...';

    fetch(resident.cancel_url, {
        method: 'PATCH',
        credentials: 'same-origin',
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({ reason }),
    })
        .then(async response => {
            const payload = await response.json().catch(() => ({}));
            if (!response.ok) {
                throw new Error(payload.message || 'Unable to cancel this booking.');
            }
            return payload;
        })
        .then(payload => {
            closeBookingModal();
            window.appToast?.('warning', payload.message || 'Booking cancelled. The slot has been released.');
            window.setTimeout(() => window.location.reload(), 800);
        })
        .catch(error => {
            window.appToast?.('error', error.message || 'Unable to cancel this booking.');
            button.disabled = false;
            button.textContent = 'Cancel Booking';
        });
}

function closeBookingModal() {
    const modal = document.getElementById('bookingModal');
    document.getElementById('booking-modal-title').textContent = 'Reservation Details';
    modal.querySelector('.booking-modal-card')?.classList.remove('booking-cancel-confirm');
    modal.style.display = 'none';
    modal.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('booking-modal-open');
    document.body.style.overflow = '';
    bookingModalPreviousFocus?.focus();
}

document.getElementById('bookingModal').addEventListener('click', function(event) {
    if (event.target === this) {
        closeBookingModal();
    }
});

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeBookingModal();
    }
});
</script>
</x-app-layout>
