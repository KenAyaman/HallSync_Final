<x-app-layout>
    @php
        $activeCount = $bookings->count();
        $todayCount = $bookings->filter(fn ($booking) => $booking->booking_date->isToday())->count();
        $upcomingCount = $bookings->filter(fn ($booking) => $booking->booking_date->isFuture())->count();
        $bookingsByDate = $bookings->groupBy(fn ($booking) => $booking->booking_date->toDateString());
        $visibleBookingGroups = $bookingsByDate->take(3);
        $hiddenBookingGroups = $bookingsByDate->slice(3);
    @endphp

    <div class="resident-page">
        <section class="resident-page-hero">
            <div class="resident-page-hero-copy">
                <p class="resident-page-kicker">Resident Booking Hub</p>
                <h1 class="resident-page-title">My Facility Bookings</h1>
                <p class="resident-page-subtitle">
                    Keep track of where you need to go next with a clean, date-first booking schedule.
                </p>

                <div class="resident-hero-stat-row">
                    <div class="resident-hero-stat">
                        <span>Active</span>
                        <strong>{{ $activeCount }}</strong>
                    </div>
                    <div class="resident-hero-stat">
                        <span>Today</span>
                        <strong>{{ $todayCount }}</strong>
                    </div>
                    <div class="resident-hero-stat">
                        <span>Upcoming</span>
                        <strong>{{ $upcomingCount }}</strong>
                    </div>
                </div>
            </div>

            <div class="resident-page-actions">
                <a href="{{ route('bookings.create') }}" class="resident-page-btn resident-page-btn-primary">New Booking</a>
            </div>
        </section>

        @if(session('error'))
            <div class="resident-flash resident-flash-error" data-auto-dismiss>{{ session('error') }}</div>
        @endif

        @if(session('success'))
            <div class="resident-flash resident-flash-success" data-auto-dismiss>{{ session('success') }}</div>
        @endif

        <section class="resident-page-panel">
            <div class="resident-page-panel-head">
                <div>
                    <h2>My Bookings</h2>
                    <p>Your reservations are grouped by day so the schedule is easier to spot.</p>
                </div>
                <span class="resident-page-eyebrow">Booking Schedule</span>
            </div>

            <div class="resident-page-divider"></div>

            <div class="resident-page-list">
                @forelse($visibleBookingGroups as $date => $dateBookings)
                    @php
                        $displayDate = \Illuminate\Support\Carbon::parse($date);
                    @endphp

                    <section class="resident-day-group">
                        <div class="resident-day-heading">
                            <span>{{ $displayDate->isToday() ? 'Today' : $displayDate->format('F d, Y') }}</span>
                        </div>

                        <div class="resident-day-list">
                            @foreach($dateBookings as $booking)
                                <article class="resident-card resident-card-schedule">
                                    <div class="resident-card-top">
                                        <div class="resident-card-heading">
                                            <h3>{{ $booking->facility_name }}</h3>
                                            <span class="resident-badge resident-badge-status-{{ $booking->status }}">
                                                Reserved
                                            </span>
                                        </div>

                                        <div class="resident-card-links">
                                            <a href="{{ route('bookings.edit', $booking) }}">Edit</a>
                                            <a href="{{ route('bookings.show', $booking) }}">View</a>
                                            <form method="POST" action="{{ route('bookings.destroy', $booking) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Are you sure you want to cancel this booking?')">Delete</button>
                                            </form>
                                        </div>
                                    </div>

                                    <div class="resident-booking-schedule-grid">
                                        <div class="resident-meta-box">
                                            <span>Time Slot</span>
                                            <strong>{{ $booking->booking_date->format('h:i A') }} - {{ $booking->end_time->format('h:i A') }}</strong>
                                        </div>
                                        <div class="resident-meta-box">
                                            <span>Submitted</span>
                                            <strong>{{ $booking->created_at->format('M d, Y h:i A') }}</strong>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @empty
                    <div class="resident-empty-state">
                        <h3>No bookings found</h3>
                        <p>Your upcoming facility schedule will appear here once you make a booking.</p>
                        <a href="{{ route('bookings.create') }}">Create a Booking</a>
                    </div>
                @endforelse

                @if($hiddenBookingGroups->isNotEmpty())
                    <div id="resident-more-bookings" class="resident-more-list" style="display: none;">
                        @foreach($hiddenBookingGroups as $date => $dateBookings)
                            @php
                                $displayDate = \Illuminate\Support\Carbon::parse($date);
                            @endphp

                            <section class="resident-day-group">
                                <div class="resident-day-heading">
                                    <span>{{ $displayDate->isToday() ? 'Today' : $displayDate->format('F d, Y') }}</span>
                                </div>

                                <div class="resident-day-list">
                                    @foreach($dateBookings as $booking)
                                        <article class="resident-card resident-card-schedule">
                                            <div class="resident-card-top">
                                                <div class="resident-card-heading">
                                                    <h3>{{ $booking->facility_name }}</h3>
                                                    <span class="resident-badge resident-badge-status-{{ $booking->status }}">
                                                        Reserved
                                                    </span>
                                                </div>

                                                <div class="resident-card-links">
                                                    <a href="{{ route('bookings.edit', $booking) }}">Edit</a>
                                                    <a href="{{ route('bookings.show', $booking) }}">View</a>
                                                    <form method="POST" action="{{ route('bookings.destroy', $booking) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" onclick="return confirm('Are you sure you want to cancel this booking?')">Delete</button>
                                                    </form>
                                                </div>
                                            </div>

                                            <div class="resident-booking-schedule-grid">
                                                <div class="resident-meta-box">
                                                    <span>Time Slot</span>
                                                    <strong>{{ $booking->booking_date->format('h:i A') }} - {{ $booking->end_time->format('h:i A') }}</strong>
                                                </div>
                                                <div class="resident-meta-box">
                                                    <span>Submitted</span>
                                                    <strong>{{ $booking->created_at->format('M d, Y h:i A') }}</strong>
                                                </div>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            </section>
                        @endforeach
                    </div>

                    <button type="button" class="resident-see-more-btn" data-target="resident-more-bookings">See more</button>
                @endif
            </div>
        </section>
    </div>

    <script>
        document.querySelectorAll('[data-auto-dismiss]').forEach((flash) => {
            setTimeout(() => {
                flash.style.transition = 'opacity 0.35s ease, transform 0.35s ease';
                flash.style.opacity = '0';
                flash.style.transform = 'translateY(-6px)';
                setTimeout(() => flash.remove(), 360);
            }, 3200);
        });

        document.querySelectorAll('.resident-see-more-btn').forEach((button) => {
            button.addEventListener('click', () => {
                const target = document.getElementById(button.dataset.target);
                if (!target) {
                    return;
                }

                const expanded = target.style.display !== 'none';
                if (expanded) {
                    target.style.display = 'none';
                    button.textContent = 'See more';
                } else {
                    target.style.display = 'flex';
                    button.textContent = 'Show Less';
                }
            });
        });
    </script>

    <style>
        .resident-page {
            max-width: 1600px;
            margin: 0 auto;
            padding: 24px 16px 32px;
            display: flex;
            flex-direction: column;
            gap: 22px;
        }

        .resident-page-hero,
        .resident-page-panel,
        .resident-flash {
            border: 1px solid rgba(214,168,91,0.14);
            box-shadow: 0 12px 24px rgba(0,0,0,0.14);
        }

        .resident-page-hero {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 20px;
            padding: 28px 30px;
            border-radius: 36px;
            background: linear-gradient(115deg, #1F2023 0%, #24262B 38%, #2C2C2F 62%, #3B3023 100%);
            box-shadow: 0 18px 50px rgba(0, 0, 0, 0.18);
        }

        .resident-page-hero-copy {
            max-width: 860px;
        }

        .resident-page-kicker {
            margin: 0 0 10px;
            color: #D2A04C;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.30em;
        }

        .resident-page-title {
            margin: 0;
            color: #F8F3EA;
            font-family: 'Playfair Display', serif;
            font-size: clamp(2.4rem, 4.6vw, 3.8rem);
            line-height: 1.05;
        }

        .resident-page-subtitle {
            margin: 12px 0 0;
            color: rgba(255,255,255,0.82);
            font-size: 1.02rem;
            line-height: 1.7;
            max-width: 760px;
        }

        .resident-hero-stat-row {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            margin-top: 22px;
        }

        .resident-hero-stat {
            min-width: 110px;
            padding: 12px 14px;
            border-radius: 16px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.07);
        }

        .resident-hero-stat span {
            display: block;
            color: #A89376;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.14em;
            font-weight: 700;
        }

        .resident-hero-stat strong {
            display: block;
            margin-top: 6px;
            color: #F0E9DF;
            font-size: 1.25rem;
            font-weight: 700;
        }

        .resident-page-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 22px;
            border-radius: 999px;
            text-decoration: none;
            font-size: 0.92rem;
            font-weight: 700;
            transition: transform 0.2s ease;
            background: linear-gradient(95deg, #b8842f, #d6a85b);
            color: #17120d;
        }

        .resident-page-btn:hover {
            transform: translateY(-1px);
        }

        .resident-flash {
            padding: 16px 20px;
            border-radius: 20px;
            font-size: 0.95rem;
            font-weight: 600;
            backdrop-filter: blur(10px);
        }

        .resident-flash-error {
            background: linear-gradient(180deg, rgba(53, 38, 35, 0.92) 0%, rgba(42, 31, 29, 0.92) 100%);
            color: #F0B3A9;
            border-color: rgba(224,112,96,0.22);
        }

        .resident-flash-success {
            background: linear-gradient(180deg, rgba(46, 58, 41, 0.92) 0%, rgba(34, 46, 31, 0.92) 100%);
            color: #D5E3BE;
            border-color: rgba(157,195,117,0.18);
        }

        .resident-page-panel {
            padding: 26px 28px;
            border-radius: 20px;
            background: rgba(42,44,48,0.78);
            backdrop-filter: blur(10px);
        }

        .resident-page-panel-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 16px;
        }

        .resident-page-panel-head h2 {
            margin: 0;
            color: #F0E9DF;
            font-size: 1.5rem;
            font-family: 'Playfair Display', serif;
        }

        .resident-page-panel-head p {
            margin: 4px 0 0;
            color: #8A7A66;
            font-size: 0.95rem;
        }

        .resident-page-eyebrow {
            color: #D6A85B;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.16em;
        }

        .resident-page-divider {
            height: 1px;
            background: linear-gradient(to right, rgba(214,168,91,0.3), rgba(214,168,91,0.05), transparent);
            margin-bottom: 18px;
        }

        .resident-page-list,
        .resident-day-list {
            display: flex;
            flex-direction: column;
        }

        .resident-page-list {
            gap: 22px;
        }

        .resident-day-list {
            gap: 14px;
        }

        .resident-day-group {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .resident-day-heading {
            display: flex;
            align-items: center;
            gap: 14px;
            color: #F0E9DF;
            font-family: 'Playfair Display', serif;
            font-size: 1.35rem;
        }

        .resident-day-heading::before,
        .resident-day-heading::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(to right, transparent, rgba(214,168,91,0.28), transparent);
        }

        .resident-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            padding: 20px 22px;
            transition: transform 0.18s ease, border-color 0.18s ease;
        }

        .resident-card:hover {
            transform: translateY(-2px);
            border-color: rgba(214,168,91,0.18);
        }

        .resident-card-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 18px;
        }

        .resident-card-heading {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
        }

        .resident-card-heading h3 {
            margin: 0;
            color: #f0e9df;
            font-size: 1.05rem;
            font-weight: 700;
        }

        .resident-card-links {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            gap: 10px;
        }

        .resident-card-links a,
        .resident-card-links button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 14px;
            border-radius: 999px;
            border: 1px solid rgba(214,168,91,0.18);
            background: rgba(255,255,255,0.03);
            color: #d7b07a;
            text-decoration: none;
            font-size: 0.82rem;
            font-weight: 700;
            cursor: pointer;
        }

        .resident-card-links form {
            margin: 0;
        }

        .resident-booking-schedule-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin-top: 16px;
        }

        .resident-meta-box {
            padding: 14px 16px;
            border-radius: 14px;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.04);
        }

        .resident-meta-box span {
            display: block;
            color: #8A7A66;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.12em;
        }

        .resident-meta-box strong {
            display: block;
            margin-top: 8px;
            color: #F0E9DF;
            font-size: 0.92rem;
            line-height: 1.65;
            font-weight: 600;
        }

        .resident-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 6px 11px;
            border-radius: 999px;
            font-size: 0.68rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .resident-badge-status-approved {
            background: rgba(120, 170, 120, 0.16);
            color: #98c48b;
        }

        .resident-badge-status-cancelled,
        .resident-badge-status-rejected {
            background: rgba(185, 106, 93, 0.16);
            color: #dc9a86;
        }

        .resident-empty-state {
            padding: 26px;
            border-radius: 18px;
            background: rgba(255,255,255,0.03);
            border: 1px dashed rgba(214,168,91,0.18);
            text-align: center;
        }

        .resident-empty-state h3 {
            margin: 0;
            color: #F0E9DF;
            font-size: 1.1rem;
        }

        .resident-empty-state p {
            margin: 10px 0 0;
            color: #B8AB98;
            line-height: 1.7;
        }

        .resident-empty-state a {
            display: inline-flex;
            margin-top: 16px;
            color: #D6A85B;
            text-decoration: none;
            font-weight: 700;
        }

        .resident-more-list {
            display: flex;
            flex-direction: column;
            gap: 22px;
            margin-top: 22px;
        }

        .resident-see-more-btn {
            margin-top: 16px;
            margin-left: auto;
            padding: 0;
            border: none;
            background: transparent;
            color: #D6A85B;
            font-weight: 700;
            cursor: pointer;
            display: block;
            text-align: right;
        }

        @media (max-width: 768px) {
            .resident-page {
                padding: 18px 0 28px;
            }

            .resident-page-hero,
            .resident-page-panel {
                padding: 22px;
            }

            .resident-page-hero {
                flex-direction: column;
                align-items: flex-start;
            }

            .resident-card-top {
                flex-direction: column;
            }

            .resident-booking-schedule-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 560px) {
            .resident-page-title {
                font-size: 2.15rem;
            }

            .resident-page-subtitle {
                font-size: 0.95rem;
            }

            .resident-page-panel-head h2,
            .resident-day-heading {
                font-size: 1.2rem;
            }

            .resident-card,
            .resident-page-panel,
            .resident-page-hero {
                border-radius: 22px;
            }

            .resident-card-links {
                width: 100%;
                justify-content: stretch;
            }

            .resident-card-links a,
            .resident-card-links form,
            .resident-card-links button {
                width: 100%;
            }

            .resident-card-links form {
                display: flex;
            }

            .resident-day-heading {
                gap: 10px;
            }
        }
    </style>
</x-app-layout>
