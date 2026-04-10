<x-app-layout>
    <div class="resident-booking-page">
        <section class="resident-booking-hero">
            <div class="resident-booking-hero-copy">
                <p class="resident-booking-kicker">Reservation Overview</p>
                <h1 class="resident-booking-title">Booking Details</h1>
                <p class="resident-booking-subtitle">
                    Review your selected facility and exact schedule in one simple, easy-to-scan summary.
                </p>

                <div class="resident-booking-hero-stats">
                    <div class="resident-booking-hero-stat">
                        <span>Facility</span>
                        <strong>{{ $booking->facility_name }}</strong>
                    </div>
                    <div class="resident-booking-hero-stat">
                        <span>Date</span>
                        <strong>{{ $booking->booking_date->format('M d, Y') }}</strong>
                    </div>
                    <div class="resident-booking-hero-stat">
                        <span>Time Slot</span>
                        <strong>{{ $booking->booking_date->format('h:i A') }} - {{ $booking->end_time->format('h:i A') }}</strong>
                    </div>
                </div>
            </div>

            <div class="resident-booking-hero-actions">
                <a href="{{ route('bookings.index') }}" class="resident-booking-btn resident-booking-btn-secondary">Back to My Bookings</a>
                <a href="{{ route('bookings.edit', $booking) }}" class="resident-booking-btn resident-booking-btn-primary">Edit Booking</a>
            </div>
        </section>

        <section class="resident-booking-panel">
            <div class="resident-booking-panel-head">
                <div>
                    <h2>Reservation Information</h2>
                    <p>Your facility schedule in one main view without extra side panels.</p>
                </div>

                <span class="resident-booking-badge resident-booking-badge-status-{{ $booking->status }}">
                    Reserved
                </span>
            </div>

            <div class="resident-booking-divider"></div>

            <div class="resident-booking-detail-list">
                <div class="resident-booking-detail-box">
                    <span>Facility</span>
                    <strong>{{ $booking->facility_name }}</strong>
                </div>

                <div class="resident-booking-meta-grid">
                    <div class="resident-booking-detail-box">
                        <span>Booking Date</span>
                        <strong>{{ $booking->booking_date->format('F d, Y') }}</strong>
                    </div>

                    <div class="resident-booking-detail-box">
                        <span>Reserved Slot</span>
                        <strong>{{ $booking->booking_date->format('h:i A') }} - {{ $booking->end_time->format('h:i A') }}</strong>
                    </div>
                </div>

                <div class="resident-booking-detail-box">
                    <span>Booked On</span>
                    <strong>{{ $booking->created_at->format('F d, Y h:i A') }}</strong>
                </div>
            </div>
        </section>
    </div>

    <style>
        .resident-booking-page {
            max-width: 1600px;
            margin: 0 auto;
            padding: 24px 16px 32px;
            display: flex;
            flex-direction: column;
            gap: 22px;
        }

        .resident-booking-hero,
        .resident-booking-panel {
            border: 1px solid rgba(214,168,91,0.14);
            box-shadow: 0 12px 24px rgba(0,0,0,0.14);
        }

        .resident-booking-hero {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 20px;
            padding: 28px 30px;
            border-radius: 36px;
            background: linear-gradient(115deg, #1F2023 0%, #24262B 38%, #2C2C2F 62%, #3B3023 100%);
            box-shadow: 0 18px 50px rgba(0, 0, 0, 0.18);
        }

        .resident-booking-hero-copy {
            max-width: 860px;
        }

        .resident-booking-kicker {
            margin: 0 0 10px;
            color: #D2A04C;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.30em;
        }

        .resident-booking-title {
            margin: 0;
            color: #F8F3EA;
            font-family: 'Playfair Display', serif;
            font-size: clamp(2.4rem, 4.6vw, 3.8rem);
            line-height: 1.05;
        }

        .resident-booking-subtitle {
            margin: 12px 0 0;
            color: rgba(255,255,255,0.82);
            font-size: 1.02rem;
            line-height: 1.7;
            max-width: 760px;
        }

        .resident-booking-hero-stats {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            margin-top: 22px;
        }

        .resident-booking-hero-stat {
            min-width: 130px;
            padding: 12px 14px;
            border-radius: 16px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.07);
        }

        .resident-booking-hero-stat span,
        .resident-booking-detail-box span {
            display: block;
            color: #8A7A66;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.12em;
        }

        .resident-booking-hero-stat span {
            color: #A89376;
        }

        .resident-booking-hero-stat strong {
            display: block;
            margin-top: 6px;
            color: #F0E9DF;
            font-size: 1rem;
            font-weight: 700;
        }

        .resident-booking-hero-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            gap: 12px;
        }

        .resident-booking-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 22px;
            border-radius: 999px;
            text-decoration: none;
            font-size: 0.92rem;
            font-weight: 700;
            transition: transform 0.2s ease;
        }

        .resident-booking-btn:hover {
            transform: translateY(-1px);
        }

        .resident-booking-btn-primary {
            background: linear-gradient(95deg, #b8842f, #d6a85b);
            color: #17120d;
        }

        .resident-booking-btn-secondary {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(214,168,91,0.22);
            color: #F0E9DF;
        }

        .resident-booking-panel {
            padding: 26px 28px;
            border-radius: 20px;
            background: rgba(42,44,48,0.78);
            backdrop-filter: blur(10px);
        }

        .resident-booking-panel-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }

        .resident-booking-panel-head h2 {
            margin: 0;
            color: #F0E9DF;
            font-size: 1.5rem;
            font-family: 'Playfair Display', serif;
        }

        .resident-booking-panel-head p {
            margin: 4px 0 0;
            color: #8A7A66;
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .resident-booking-divider {
            height: 1px;
            background: linear-gradient(to right, rgba(214,168,91,0.3), rgba(214,168,91,0.05), transparent);
            margin-bottom: 18px;
        }

        .resident-booking-badge {
            padding: 6px 11px;
            border-radius: 999px;
            font-size: 0.68rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .resident-booking-badge-status-approved {
            background: rgba(120, 170, 120, 0.16);
            color: #98c48b;
        }

        .resident-booking-badge-status-cancelled,
        .resident-booking-badge-status-rejected {
            background: rgba(185, 106, 93, 0.16);
            color: #dc9a86;
        }

        .resident-booking-detail-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .resident-booking-detail-box {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 16px;
            padding: 16px 18px;
        }

        .resident-booking-detail-box strong {
            display: block;
            margin-top: 8px;
            color: #F0E9DF;
            font-size: 0.95rem;
            line-height: 1.7;
            font-weight: 600;
        }

        .resident-booking-meta-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        @media (max-width: 768px) {
            .resident-booking-page {
                padding: 18px 0 28px;
            }

            .resident-booking-hero,
            .resident-booking-panel {
                padding: 22px;
            }

            .resident-booking-hero {
                flex-direction: column;
                align-items: flex-start;
            }

            .resident-booking-meta-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</x-app-layout>
