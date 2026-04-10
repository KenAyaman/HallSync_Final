<x-app-layout>
    @php
        $approvedCount = $bookings->where('status', 'approved')->count();
        $upcomingCount = $bookings->filter(fn ($booking) => $booking->booking_date->isFuture())->count();
        $pastCount = $bookings->filter(fn ($booking) => $booking->booking_date->isPast())->count();
    @endphp
    <div class="resident-page">
        <section class="resident-page-hero">
            <div class="resident-page-hero-copy">
                <p class="resident-page-kicker">Resident Booking Hub</p>
                <h1 class="resident-page-title">My Facility Bookings</h1>
                <p class="resident-page-subtitle">
                    Manage your reservation history, check upcoming schedules, and review your confirmed facility bookings.
                </p>

                <div class="resident-hero-stat-row">
                    <div class="resident-hero-stat">
                        <span>Confirmed</span>
                        <strong>{{ $approvedCount }}</strong>
                    </div>
                    <div class="resident-hero-stat">
                        <span>Past</span>
                        <strong>{{ $pastCount }}</strong>
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
            <div class="resident-flash resident-flash-error">{{ session('error') }}</div>
        @endif

        <section class="resident-page-panel">
            <div class="resident-page-panel-head">
                <div>
                    <h2>Submitted Bookings</h2>
                    <p>Your current and previous facility reservations.</p>
                </div>
                <span class="resident-page-eyebrow">Reservation History</span>
            </div>

            <div class="resident-page-divider"></div>

            <div class="resident-page-list">
                @forelse($bookings as $booking)
                    <article class="resident-card">
                        <div class="resident-card-top">
                            <div>
                                <div class="resident-card-heading">
                                    <h3>{{ $booking->facility_name }}</h3>
                                    <span class="resident-badge resident-badge-status-{{ $booking->status }}">
                                        {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                    </span>
                                </div>
                                <p class="resident-card-description">
                                    <strong>Booking Date:</strong> {{ $booking->booking_date->format('l, F d, Y') }}<br>
                                    <strong>Time Slot:</strong> {{ $booking->booking_date->format('h:i A') }} - {{ $booking->end_time->format('h:i A') }}
                                    @if($booking->notes)
                                        <br><strong>Notes:</strong> {{ Str::limit($booking->notes, 120) }}
                                    @endif
                                </p>
                            </div>

                            <div class="resident-card-links">
                                <a href="{{ route('bookings.edit', $booking) }}">Edit</a>
                                <a href="{{ route('bookings.show', $booking) }}">View Details</a>
                                <form method="POST" action="{{ route('bookings.destroy', $booking) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Are you sure you want to cancel this booking?')">Delete</button>
                                </form>
                            </div>
                        </div>

                        <div class="resident-card-meta-grid">
                            <div class="resident-meta-box">
                                <span>Booking ID</span>
                                <strong>{{ $booking->id }}</strong>
                            </div>
                            <div class="resident-meta-box">
                                <span>Submitted</span>
                                <strong>{{ $booking->created_at->format('M d, Y h:i A') }}</strong>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="resident-empty-state">
                        <h3>No bookings found</h3>
                        <p>Your facility reservations will appear here once you make a booking.</p>
                        <a href="{{ route('bookings.create') }}">Create a Booking</a>
                    </div>
                @endforelse
            </div>
        </section>
    </div>

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
            background: linear-gradient(180deg, rgba(53, 38, 35, 0.92) 0%, rgba(42, 31, 29, 0.92) 100%);
            color: #F0B3A9;
            border-color: rgba(224,112,96,0.22);
            backdrop-filter: blur(10px);
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

        .resident-page-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .resident-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            padding: 22px;
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

        .resident-card-description {
            margin: 12px 0 0;
            color: #B8AB98;
            line-height: 1.65;
            font-size: 0.9rem;
        }

        .resident-card-links {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: flex-end;
            gap: 14px;
        }

        .resident-card-links a,
        .resident-card-links button,
        .resident-empty-state a {
            color: #d7b07a;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 0;
        }

        .resident-badge {
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 0.68rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .resident-badge-status-pending {
            background: rgba(199, 151, 69, 0.18);
            color: #d7b07a;
        }

        .resident-badge-status-approved {
            background: rgba(120, 170, 120, 0.16);
            color: #98c48b;
        }

        .resident-badge-status-rejected,
        .resident-badge-status-cancelled {
            background: rgba(185, 106, 93, 0.16);
            color: #dc9a86;
        }

        .resident-card-meta-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin-top: 18px;
        }

        .resident-meta-box {
            padding: 12px 14px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255,255,255,0.05);
        }

        .resident-meta-box span {
            display: block;
            color: #8A7A66;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .resident-meta-box strong {
            display: block;
            margin-top: 6px;
            color: #f0e9df;
            font-size: 0.86rem;
            font-weight: 600;
        }

        .resident-empty-state {
            padding: 34px 20px;
            border-radius: 16px;
            text-align: center;
            border: 1px solid rgba(255,255,255,0.05);
            background: rgba(255, 255, 255, 0.03);
        }

        .resident-empty-state h3 {
            margin: 0;
            color: #f0e9df;
            font-size: 1.3rem;
            font-family: 'Playfair Display', serif;
        }

        .resident-empty-state p {
            margin: 10px 0 18px;
            color: #B8AB98;
            font-size: 0.92rem;
        }

        @media (max-width: 768px) {
            .resident-page {
                padding: 18px 0 28px;
            }

            .resident-page-hero,
            .resident-page-panel {
                padding: 22px;
            }

            .resident-page-hero,
            .resident-card-top {
                flex-direction: column;
                align-items: flex-start;
            }

            .resident-card-links {
                justify-content: flex-start;
            }

            .resident-card-meta-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</x-app-layout>
