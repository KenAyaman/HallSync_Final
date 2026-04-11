<x-app-layout>
@php
    $totalBookings = 0;
    foreach ($calendar as $facility) {
        foreach ($facility as $slot) {
            if ($slot) {
                $totalBookings++;
            }
        }
    }

    $occupiedSlots = $totalBookings;
    $totalSlots = count($facilities) * count($timeSlots);
    $occupancyRate = $totalSlots > 0 ? round(($occupiedSlots / $totalSlots) * 100) : 0;
    $monthBookingCount = $monthlyBookings->count();
    $monthApprovedCount = $monthlyBookings->where('status', 'approved')->count();
    $monthPendingCount = $monthlyBookings->where('status', 'pending')->count();
@endphp

<div class="booking-dashboard">
    <div class="booking-shell">
        <section class="booking-hero">
            <div>
                <p class="booking-kicker">Manager Booking Center</p>
                <h1 class="booking-title">Booking Calendar</h1>
                <p class="booking-subtitle">
                    @if ($viewMode === 'day')
                        A cleaner day-by-day view of Rexhall facility reservations for {{ $selectedDate->format('l, F j, Y') }}.
                    @else
                        Monthly reservation activity for {{ $monthLabel }}, grouped into a simpler management view.
                    @endif
                </p>
            </div>

            <div class="booking-toolbar">
                <div class="mode-switcher">
                    <a href="{{ route('admin.bookings.calendar', ['view' => 'day', 'date' => $selectedDate->toDateString()]) }}"
                       class="mode-pill {{ $viewMode === 'day' ? 'is-active' : '' }}">
                        Day
                    </a>
                    <a href="{{ route('admin.bookings.calendar', ['view' => 'this_month']) }}"
                       class="mode-pill {{ $viewMode === 'this_month' ? 'is-active' : '' }}">
                        This Month
                    </a>
                    <a href="{{ route('admin.bookings.calendar', ['view' => 'last_month']) }}"
                       class="mode-pill {{ $viewMode === 'last_month' ? 'is-active' : '' }}">
                        Last Month
                    </a>
                </div>

                @if ($viewMode !== 'day')
                    <div class="date-nav">
                        <div class="date-badge">{{ $monthLabel }}</div>
                    </div>
                @endif
            </div>
        </section>

        @if ($viewMode === 'day')
            <section class="stats-grid">
                <article class="stat-card">
                    <span class="stat-label">Total Bookings</span>
                    <strong class="stat-value">{{ $totalBookings }}</strong>
                    <span class="stat-note">Scheduled today</span>
                </article>
                <article class="stat-card">
                    <span class="stat-label">Occupancy</span>
                    <strong class="stat-value">{{ $occupancyRate }}%</strong>
                    <span class="stat-note">Used slots today</span>
                </article>
                <article class="stat-card">
                    <span class="stat-label">Available Slots</span>
                    <strong class="stat-value">{{ $totalSlots - $occupiedSlots }}</strong>
                    <span class="stat-note">Open today</span>
                </article>
                <article class="stat-card">
                    <span class="stat-label">Facilities</span>
                    <strong class="stat-value">{{ count($facilities) }}</strong>
                    <span class="stat-note">Tracked spaces</span>
                </article>
            </section>

            <section class="booking-panel">
                <div class="panel-heading">
                    <div>
                        <h2>Weekly Overview</h2>
                        <p>Select a day to jump the calendar</p>
                    </div>
                    <div class="date-nav date-nav-inline">
                        <a href="{{ route('admin.bookings.calendar', ['view' => 'day', 'date' => $previousDate]) }}" class="nav-pill">Previous</a>
                        <div class="date-badge">{{ $selectedDate->format('M d, Y') }}</div>
                        <a href="{{ route('admin.bookings.calendar', ['view' => 'day', 'date' => $nextDate]) }}" class="nav-pill">Next</a>
                        <a href="{{ route('admin.bookings.calendar', ['view' => 'day', 'date' => $todayDate]) }}" class="nav-pill nav-pill-highlight">Today</a>
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
                           class="week-day {{ $isSelected ? 'is-selected' : '' }} {{ $isToday ? 'is-today' : '' }}">
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
                        <p>Open any occupied slot to review the reservation details.</p>
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
                                            $booking = $calendar[$facility][$slot] ?? null;
                                        @endphp
                                        <td>
                                            @if ($booking)
                                                <button type="button"
                                                        class="booking-chip"
                                                        onclick="showBookingDetails({{ $booking->id }})">
                                                    <span class="booking-chip-name">{{ Str::limit($booking->user->name ?? 'Resident', 15) }}</span>
                                                    <span class="booking-chip-time">{{ $booking->booking_date->format('h:i A') }}</span>
                                                </button>
                                            @else
                                                <div class="empty-slot">Available</div>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @else
            <section class="stats-grid">
                <article class="stat-card">
                    <span class="stat-label">Bookings</span>
                    <strong class="stat-value">{{ $monthBookingCount }}</strong>
                    <span class="stat-note">{{ $monthLabel }}</span>
                </article>
                <article class="stat-card">
                    <span class="stat-label">Approved</span>
                    <strong class="stat-value">{{ $monthApprovedCount }}</strong>
                    <span class="stat-note">Confirmed reservations</span>
                </article>
                <article class="stat-card">
                    <span class="stat-label">Pending</span>
                    <strong class="stat-value">{{ $monthPendingCount }}</strong>
                    <span class="stat-note">Awaiting decision</span>
                </article>
                <article class="stat-card">
                    <span class="stat-label">Facilities Used</span>
                    <strong class="stat-value">{{ $monthlyFacilitySummary->count() }}</strong>
                    <span class="stat-note">Spaces with bookings</span>
                </article>
            </section>

            <div class="month-grid">
                <section class="booking-panel">
                    <div class="panel-heading">
                        <div>
                            <h2>Facility Summary</h2>
                            <p>High-level usage for {{ $monthLabel }}</p>
                        </div>
                    </div>

                    <div class="summary-list">
                        @forelse ($monthlyFacilitySummary as $summary)
                            <article class="summary-row">
                                <div>
                                    <h3>{{ $summary['facility_name'] }}</h3>
                                    <p>{{ $summary['approved'] }} approved, {{ $summary['pending'] }} pending, {{ $summary['rejected'] }} rejected</p>
                                </div>
                                <strong>{{ $summary['count'] }}</strong>
                            </article>
                        @empty
                            <p class="empty-copy">No bookings found for {{ $monthLabel }}.</p>
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

                    <div class="history-list">
                        @forelse ($monthlyBookings as $booking)
                            <article class="history-row">
                                <div>
                                    <h3>{{ $booking->facility_name }}</h3>
                                    <p>{{ $booking->user->name ?? 'Resident' }} • {{ $booking->booking_date->format('M d, Y h:i A') }}</p>
                                </div>
                                <span class="status-badge status-{{ $booking->status }}">{{ ucfirst($booking->status) }}</span>
                            </article>
                        @empty
                            <p class="empty-copy">No booking records for this period.</p>
                        @endforelse
                    </div>
                </section>
            </div>
        @endif
    </div>
</div>

<div id="bookingModal" class="booking-modal">
    <div class="booking-modal-card">
        <div class="booking-modal-header">
            <h3>Booking Details</h3>
            <button type="button" onclick="closeBookingModal()" class="booking-modal-close">&times;</button>
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

    .booking-hero,
    .booking-panel,
    .stat-card {
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
        background-image:
            linear-gradient(rgba(214,168,91,0.04) 1px, transparent 1px),
            linear-gradient(90deg, rgba(214,168,91,0.04) 1px, transparent 1px);
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
        color: rgba(255,255,255,0.62);
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

    .mode-switcher,
    .date-nav {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: flex-end;
    }

    .mode-pill,
    .nav-pill,
    .date-badge {
        padding: 10px 16px;
        border-radius: 999px;
        border: 1px solid var(--booking-border);
        background: rgba(255, 255, 255, 0.04);
        color: var(--booking-text);
        text-decoration: none;
        font-size: 0.92rem;
        transition: 0.2s ease;
    }

    .mode-pill:hover,
    .nav-pill:hover {
        border-color: var(--booking-border-strong);
        color: var(--booking-gold);
    }

    .mode-pill.is-active,
    .nav-pill-highlight,
    .date-badge {
        background: var(--booking-gold-soft);
        border-color: var(--booking-border-strong);
        color: var(--booking-gold);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
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
        border-radius: 28px;
        background: var(--booking-panel);
        padding: 24px 26px;
    }

    .calendar-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 18px;
        margin-bottom: 16px;
        padding: 14px 16px;
        border-radius: 18px;
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.05);
    }

    .calendar-legend-item {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        color: var(--booking-muted);
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
        background: rgba(214, 168, 91, 0.5);
        border: 1px solid rgba(214, 168, 91, 0.6);
    }

    .calendar-legend-swatch-open {
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.14);
    }

    .panel-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 18px;
    }

    .date-nav-inline {
        justify-content: flex-end;
    }

    .panel-heading h2 {
        margin: 0;
        font-size: 1.5rem;
        font-family: 'Playfair Display', serif;
    }

    .panel-heading p {
        margin: 6px 0 0;
        color: var(--booking-muted);
        font-size: 0.95rem;
    }

    .week-strip {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        gap: 10px;
    }

    .week-day {
        display: flex;
        flex-direction: column;
        gap: 4px;
        padding: 14px 10px;
        border-radius: 18px;
        border: 1px solid rgba(255, 255, 255, 0.05);
        background: rgba(255, 255, 255, 0.02);
        text-decoration: none;
        color: var(--booking-text);
        text-align: center;
        transition: 0.2s ease;
    }

    .week-day:hover,
    .week-day.is-selected {
        background: var(--booking-gold-soft);
        border-color: var(--booking-border-strong);
    }

    .week-day.is-today .week-day-number,
    .week-day.is-selected .week-day-number,
    .week-day.is-selected .week-day-name {
        color: var(--booking-gold);
    }

    .week-day-name,
    .week-day-count {
        color: var(--booking-muted);
        font-size: 0.82rem;
    }

    .week-day-number {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--booking-text);
    }

    .calendar-table-wrap {
        overflow-x: auto;
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

    .calendar-table th,
    .calendar-table td {
        padding: 12px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        vertical-align: top;
    }

    .calendar-table td:not(:first-child),
    .calendar-table th:not(:first-child) {
        width: 25%;
    }

    .calendar-table th {
        text-align: left;
        font-size: 0.82rem;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: var(--booking-muted);
        background: rgba(255,255,255,0.02);
    }

    .time-cell {
        white-space: nowrap;
        color: var(--booking-muted);
        font-size: 0.84rem;
        width: 96px;
    }

    .booking-chip {
        width: 100%;
        text-align: left;
        padding: 12px;
        min-height: 72px;
        border-radius: 16px;
        border: 1px solid rgba(214, 168, 91, 0.22);
        background: rgba(214, 168, 91, 0.1);
        color: var(--booking-text);
        cursor: pointer;
        transition: 0.2s ease;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .booking-chip:hover {
        border-color: var(--booking-border-strong);
        transform: translateY(-1px);
    }

    .booking-chip-name,
    .booking-chip-time {
        display: block;
    }

    .booking-chip-name {
        font-size: 0.92rem;
        font-weight: 600;
    }

    .booking-chip-time {
        margin-top: 4px;
        font-size: 0.82rem;
        color: var(--booking-muted);
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
        border: 1px dashed rgba(255,255,255,0.08);
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

    .summary-list,
    .history-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .summary-row,
    .history-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding: 16px 18px;
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.04);
    }

    .summary-row h3,
    .history-row h3 {
        margin: 0;
        font-size: 1rem;
    }

    .summary-row p,
    .history-row p,
    .empty-copy {
        margin: 6px 0 0;
        color: var(--booking-muted);
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

    .status-rejected {
        background: rgba(224, 112, 96, 0.16);
        color: var(--booking-red);
    }

    .booking-modal {
        position: fixed;
        inset: 0;
        display: none;
        align-items: center;
        justify-content: center;
        background: rgba(0, 0, 0, 0.72);
        backdrop-filter: blur(8px);
        z-index: 60;
    }

    .booking-modal-card {
        width: min(420px, 92vw);
        padding: 24px;
        border-radius: 24px;
        background: linear-gradient(180deg, #2a2c31 0%, #1f2023 100%);
        border: 1px solid var(--booking-border);
        box-shadow: var(--booking-shadow);
    }

    .booking-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }

    .booking-modal-header h3 {
        margin: 0;
        font-size: 1.35rem;
        font-family: 'Playfair Display', serif;
    }

    .booking-modal-close,
    .booking-modal-button {
        border: 1px solid var(--booking-border);
        background: rgba(255, 255, 255, 0.04);
        color: var(--booking-text);
        cursor: pointer;
    }

    .booking-modal-close {
        width: 38px;
        height: 38px;
        border-radius: 999px;
        font-size: 1.4rem;
    }

    .booking-modal-body {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .booking-modal-item {
        padding: 12px 14px;
        border-radius: 16px;
        background: rgba(214, 168, 91, 0.07);
    }

    .booking-modal-label {
        display: block;
        color: var(--booking-muted);
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        margin-bottom: 4px;
    }

    .booking-modal-value {
        color: var(--booking-text);
        font-size: 0.95rem;
    }

    .booking-modal-button {
        margin-top: 16px;
        width: 100%;
        padding: 12px 14px;
        border-radius: 14px;
        font-size: 0.95rem;
    }

    @media (max-width: 1100px) {
        .stats-grid,
        .month-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 820px) {
        .booking-hero {
            flex-direction: column;
            padding: 24px;
            align-items: flex-start;
        }

        .booking-toolbar {
            width: 100%;
            align-items: flex-start;
        }

        .mode-switcher,
        .date-nav {
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

    @media (max-width: 640px) {
        .booking-hero,
        .booking-panel {
            padding: 20px;
            border-radius: 22px;
        }

        .booking-title {
            font-size: 2.1rem;
        }

        .booking-subtitle {
            font-size: 0.98rem;
            line-height: 1.6;
        }

        .mode-pill,
        .nav-pill,
        .date-badge {
            padding: 9px 14px;
            font-size: 0.84rem;
        }

        .summary-row,
        .history-row {
            flex-direction: column;
            align-items: flex-start;
        }

        .booking-modal-card {
            padding: 18px;
            border-radius: 20px;
        }
    }
</style>

<script>
function showBookingDetails(bookingId) {
    fetch(`/admin/bookings/${bookingId}/details`)
        .then(response => response.json())
        .then(data => {
            const modal = document.getElementById('bookingModal');
            const details = document.getElementById('bookingDetails');

            details.innerHTML = `
                <div class="booking-modal-item">
                    <span class="booking-modal-label">Facility</span>
                    <div class="booking-modal-value">${data.facility_name}</div>
                </div>
                <div class="booking-modal-item">
                    <span class="booking-modal-label">Booked By</span>
                    <div class="booking-modal-value">${data.user_name}</div>
                </div>
                <div class="booking-modal-item">
                    <span class="booking-modal-label">Date and Time</span>
                    <div class="booking-modal-value">${data.date} • ${data.time}</div>
                </div>
                ${data.notes ? `
                    <div class="booking-modal-item">
                        <span class="booking-modal-label">Notes</span>
                        <div class="booking-modal-value">${data.notes}</div>
                    </div>
                ` : ''}
                <div class="booking-modal-item">
                    <span class="booking-modal-label">Status</span>
                    <div class="booking-modal-value">${data.status.toUpperCase()}</div>
                </div>
            `;

            modal.style.display = 'flex';
        })
        .catch(() => {
            alert('Could not load booking details');
        });
}

function closeBookingModal() {
    document.getElementById('bookingModal').style.display = 'none';
}

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeBookingModal();
    }
});
</script>
</x-app-layout>
