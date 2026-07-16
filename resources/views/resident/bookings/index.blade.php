<x-app-layout>
    @php
        $activeCount = $bookings->where('status', 'approved')->count();
        $todayCount = $bookings->filter(fn ($booking) => $booking->status === 'approved' && $booking->booking_date->isToday())->count();
        $upcomingCount = $bookings->filter(fn ($booking) => $booking->status === 'approved' && $booking->booking_date->isFuture())->count();
        $bookingsByDate = $bookings->groupBy(fn ($booking) => $booking->booking_date->toDateString());
        $visibleBookingGroups = $bookingsByDate->take(3);
        $hiddenBookingGroups = $bookingsByDate->slice(3);
        $visibleHistoryBookings = $pastBookings->take(3);
        $hiddenHistoryBookings = $pastBookings->slice(3);
        $bookingStatusLabel = fn ($status) => match ($status) {
            'approved' => 'Reserved',
            'cancelled' => 'Cancelled',
            default => ucfirst(str_replace('_', ' ', $status)),
        };
    @endphp

    <div class="resident-page">
        <section class="resident-page-hero resident-hero-woven">
            <div class="resident-page-hero-copy">
                <p class="resident-page-kicker">Resident Booking Hub</p>
                <h1 class="resident-page-title">My Facility Bookings</h1>
                <p class="resident-page-subtitle">
                    Keep track of where you need to go next with a clean, date-first booking schedule.
                </p>

                <div class="resident-hero-stat-row">
                    <div class="resident-hero-stat">
                        <span>Upcoming Reservations</span>
                        <strong>{{ $activeCount }} of 3 Slots Used</strong>
                    </div>
                </div>
            </div>

            <div class="resident-page-actions">
                <a href="{{ route('bookings.create') }}" class="resident-page-btn resident-page-btn-primary">New Booking</a>
            </div>
        </section>

        <section class="resident-page-panel">
            <div class="resident-page-panel-head">
                <div>
                    <h2>My Bookings</h2>
                    <p>Your scheduled facility reservations, grouped by date.</p>
                </div>
            </div>

            <div class="resident-page-divider"></div>

            <div class="feature-skeleton-stack" data-feature-skeleton>
                @for($group = 0; $group < 2; $group++)
                    <section>
                        <span class="feature-skeleton-line feature-skeleton-day"></span>
                        <article class="resident-card resident-card-schedule feature-skeleton-card">
                            <div class="feature-skeleton-top">
                                <div class="feature-skeleton-title-row">
                                    <span class="feature-skeleton-line title"></span>
                                    <span class="feature-skeleton-pill"></span>
                                </div>
                                <div class="feature-skeleton-actions">
                                    <span class="feature-skeleton-button"></span>
                                    <span class="feature-skeleton-button"></span>
                                    <span class="feature-skeleton-button"></span>
                                </div>
                            </div>
                            <div class="feature-skeleton-meta">
                                <span class="feature-skeleton-box"></span>
                                <span class="feature-skeleton-box"></span>
                            </div>
                        </article>
                    </section>
                @endfor
            </div>

            <div class="resident-page-list" data-skeleton-content>
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
                                <article class="resident-card resident-card-schedule resident-booking-ledger-card"
                                         data-active-booking-id="{{ $booking->id }}">
                                    <div class="resident-card-top">
                                        <div class="resident-booking-ledger-main">
                                            <div class="resident-card-heading">
                                                <h3>{{ $booking->facility_name }}</h3>
                                                <span class="resident-badge resident-badge-status-{{ $booking->status }}">{{ $bookingStatusLabel($booking->status) }}</span>
                                            </div>
                                            <div class="resident-booking-inline-meta">
                                                <span>{{ $booking->booking_date->format('D, M d Y') }}</span>
                                                <span>{{ $booking->booking_date->format('h:i A') }} - {{ $booking->end_time->format('h:i A') }}</span>
                                                <span>Submitted {{ $booking->created_at->format('M d, Y h:i A') }}</span>
                                            </div>
                                        </div>

                                        <div class="resident-booking-action-pill" aria-label="Booking actions">
                                            <a class="resident-booking-action resident-booking-action-primary" href="{{ route('bookings.show', $booking) }}">Details</a>
                                            @if($booking->status === 'approved')
                                                <a class="resident-booking-action" href="{{ route('bookings.edit', $booking) }}">Edit</a>
                                                <form method="POST"
                                                      action="{{ route('bookings.destroy', $booking) }}"
                                                      data-confirm-message="Cancel this booking? This will remove it from your schedule."
                                                      data-prevent-double-submit
                                                      data-submitting-text="Cancelling Booking...">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="resident-booking-action resident-booking-action-danger" type="submit">Cancel</button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @empty
                    <x-resident-empty-state icon="booking" title="No bookings found" description="Your upcoming facility schedule will appear here once you make a booking." :action-href="route('bookings.create')" action-label="Create a Booking" />
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
                                        <article class="resident-card resident-card-schedule resident-booking-ledger-card"
                                                 data-active-booking-id="{{ $booking->id }}"
                                                 data-filter-card
                                                 data-search="{{ Str::lower($booking->facility_name . ' ' . $booking->booking_date->format('F d, Y h:i A') . ' ' . $booking->end_time->format('h:i A')) }}"
                                                 data-status="{{ $booking->status }}"
                                                 data-facility="{{ $booking->facility_name }}">
                                            <div class="resident-card-top">
                                                <div class="resident-booking-ledger-main">
                                                    <div class="resident-card-heading">
                                                        <h3>{{ $booking->facility_name }}</h3>
                                                        <span class="resident-badge resident-badge-status-{{ $booking->status }}">{{ $bookingStatusLabel($booking->status) }}</span>
                                                    </div>
                                                    <div class="resident-booking-inline-meta">
                                                        <span>{{ $booking->booking_date->format('D, M d Y') }}</span>
                                                        <span>{{ $booking->booking_date->format('h:i A') }} - {{ $booking->end_time->format('h:i A') }}</span>
                                                        <span>Submitted {{ $booking->created_at->format('M d, Y h:i A') }}</span>
                                                    </div>
                                                </div>

                                                <div class="resident-booking-action-pill" aria-label="Booking actions">
                                                    <a class="resident-booking-action resident-booking-action-primary" href="{{ route('bookings.show', $booking) }}">Details</a>
                                                    @if($booking->status === 'approved')
                                                        <a class="resident-booking-action" href="{{ route('bookings.edit', $booking) }}">Edit</a>
                                                        <form method="POST"
                                                              action="{{ route('bookings.destroy', $booking) }}"
                                                              data-confirm-message="Cancel this booking? This will remove it from your schedule."
                                                              data-prevent-double-submit
                                                              data-submitting-text="Cancelling Booking...">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="resident-booking-action resident-booking-action-danger" type="submit">Cancel</button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            </section>
                        @endforeach
                    </div>

                    <button type="button" class="resident-see-more-btn" data-target="resident-more-bookings" data-collapsed-label="See more" data-expanded-label="Show less" aria-expanded="false">See more</button>
                @endif

            </div>
        </section>

        {{-- BOOKING HISTORY (H-09) --}}
        @if($pastBookings->isNotEmpty())
        <section class="resident-page-panel">
            <div class="resident-page-panel-head">
                <div>
                    <h2>Booking History</h2>
                    <p>Your past and cancelled reservations from the last 30 records.</p>
                </div>
            </div>

            <div class="resident-page-divider"></div>

            <div class="resident-page-list">
                @foreach($visibleHistoryBookings as $booking)
                    <article class="resident-card resident-card-schedule resident-booking-ledger-card resident-booking-history-card"
                             data-history-booking-id="{{ $booking->id }}"
                             style="opacity: {{ $booking->status === 'cancelled' ? '0.72' : '1' }};">
                        <div class="resident-card-head">
                            <div class="resident-booking-ledger-main">
                                <strong class="resident-card-title">{{ $booking->facility_name }}</strong>
                                <div class="resident-booking-inline-meta">
                                    <span>{{ $booking->booking_date->format('D, M d Y') }}</span>
                                    <span>{{ $booking->booking_date->format('h:i A') }} - {{ $booking->end_time->format('h:i A') }}</span>
                                    <span>Booked {{ $booking->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                            <div class="resident-card-links">
                                <span class="resident-status-badge resident-status-badge-{{ $booking->status }}">
                                    {{ $booking->status === 'cancelled' ? 'Cancelled' : 'Completed' }}
                                </span>
                            </div>
                        </div>
                        <div class="resident-booking-schedule-grid">
                            <div class="resident-meta-box">
                                <span>Time Slot</span>
                                <strong>{{ $booking->booking_date->format('h:i A') }} – {{ $booking->end_time->format('h:i A') }}</strong>
                            </div>
                            <div class="resident-meta-box">
                                <span>Booked on</span>
                                <strong>{{ $booking->created_at->format('M d, Y') }}</strong>
                            </div>
                        </div>
                    </article>
                @endforeach

                @if($hiddenHistoryBookings->isNotEmpty())
                    <div id="resident-more-booking-history" class="resident-more-list" style="display: none;">
                        @foreach($hiddenHistoryBookings as $booking)
                            <article class="resident-card resident-card-schedule resident-booking-ledger-card resident-booking-history-card"
                                     data-history-booking-id="{{ $booking->id }}"
                                     style="opacity: {{ $booking->status === 'cancelled' ? '0.72' : '1' }};">
                                <div class="resident-card-head">
                                    <div class="resident-booking-ledger-main">
                                        <strong class="resident-card-title">{{ $booking->facility_name }}</strong>
                                        <div class="resident-booking-inline-meta">
                                            <span>{{ $booking->booking_date->format('D, M d Y') }}</span>
                                            <span>{{ $booking->booking_date->format('h:i A') }} - {{ $booking->end_time->format('h:i A') }}</span>
                                            <span>Booked {{ $booking->created_at->format('M d, Y') }}</span>
                                        </div>
                                    </div>
                                    <div class="resident-card-links">
                                        <span class="resident-status-badge resident-status-badge-{{ $booking->status }}">
                                            {{ $booking->status === 'cancelled' ? 'Cancelled' : 'Completed' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="resident-booking-schedule-grid">
                                    <div class="resident-meta-box">
                                        <span>Time Slot</span>
                                        <strong>{{ $booking->booking_date->format('h:i A') }} – {{ $booking->end_time->format('h:i A') }}</strong>
                                    </div>
                                    <div class="resident-meta-box">
                                        <span>Booked on</span>
                                        <strong>{{ $booking->created_at->format('M d, Y') }}</strong>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <button type="button" class="resident-see-more-btn" data-target="resident-more-booking-history" data-collapsed-label="See more history" data-expanded-label="Hide history" aria-expanded="false">See more history</button>
                @endif
            </div>
        </section>
        @endif
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
                    button.textContent = button.dataset.collapsedLabel || 'See more';
                    button.setAttribute('aria-expanded', 'false');
                } else {
                    target.style.display = 'flex';
                    button.textContent = button.dataset.expandedLabel || 'Show less';
                    button.setAttribute('aria-expanded', 'true');
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
.resident-page-hero, .resident-page-panel, .resident-flash {
    border: 1px solid rgba(214, 168, 91, 0.14);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.14);
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
    color: rgba(255, 255, 255, 0.82);
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
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.07);
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
    border-color: rgba(224, 112, 96, 0.22);
}
.resident-flash-success {
    background: linear-gradient(180deg, rgba(46, 58, 41, 0.92) 0%, rgba(34, 46, 31, 0.92) 100%);
    color: #D5E3BE;
    border-color: rgba(157, 195, 117, 0.18);
}
.resident-page-panel {
    padding: 26px 28px;
    border-radius: 20px;
    background: rgba(42, 44, 48, 0.78);
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
    background: linear-gradient(to right, rgba(214, 168, 91, 0.3), rgba(214, 168, 91, 0.05), transparent);
    margin-bottom: 18px;
}
.resident-page-list, .resident-day-list {
    display: flex;
    flex-direction: column;
}
.resident-page-list {
    gap: 22px;
}
.resident-day-list {
    gap: 14px;
}
.resident-day-list:has(.resident-booking-ledger-card), .resident-page-list:has(.resident-booking-ledger-card) {
    gap: 10px;
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
.resident-day-heading::before, .resident-day-heading::after {
    content: '';
    flex: 1;
    height: 1px;
    background: linear-gradient(to right, transparent, rgba(214, 168, 91, 0.28), transparent);
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
    border-color: rgba(214, 168, 91, 0.18);
}
.resident-card-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 18px;
}
.resident-booking-ledger-card {
    padding: 14px 16px;
}
.resident-booking-ledger-card .resident-card-top, .resident-booking-ledger-card .resident-card-head {
    align-items: center;
    gap: 16px;
}
.resident-booking-ledger-main {
    min-width: 0;
    flex: 1;
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
.resident-booking-inline-meta {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 7px 12px;
    margin-top: 7px;
    color: #B8AB98;
    font-size: 0.84rem;
    line-height: 1.45;
}
.resident-booking-inline-meta span {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    min-width: 0;
}
.resident-booking-inline-meta span + span::before {
    content: '';
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background: rgba(214, 168, 91, 0.55);
    flex: 0 0 auto;
}
.resident-booking-inline-meta span:nth-child(2) {
    color: #E9C17F;
    font-weight: 700;
}
.resident-booking-action-pill {
    display: inline-flex;
    align-items: stretch;
    overflow: hidden;
    flex: 0 0 auto;
    border: 1px solid rgba(214, 168, 91, 0.16);
    border-radius: 999px;
    background: rgba(214, 168, 91, 0.055);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.035);
}
.resident-booking-action-pill form {
    display: contents;
    margin: 0;
}
.resident-booking-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 32px;
    padding: 0 12px;
    border: 0;
    border-left: 1px solid rgba(214, 168, 91, 0.16);
    background: transparent;
    color: #D6A85B;
    cursor: pointer;
    font-family: inherit;
    font-size: 0.85rem;
    font-weight: 600;
    letter-spacing: 0.035em;
    line-height: 1;
    text-decoration: none;
    text-transform: uppercase;
    white-space: nowrap;
    transition: background 0.16s ease, color 0.16s ease;
}
.resident-booking-action:first-child {
    border-left: 0;
}
.resident-booking-action:hover {
    background: rgba(214, 168, 91, 0.10);
    color: #F0D39B;
}
.resident-booking-action-primary {
    background: linear-gradient(95deg, #B8842F, #D6A85B);
    color: #17120D;
    font-weight: 700;
}
.resident-booking-action-primary:hover {
    background: linear-gradient(95deg, #C6903A, #E0B566);
    color: #17120D;
}
.resident-booking-action-danger {
    background: rgba(92, 31, 35, 0.72);
    color: #F0B3A9;
}
.resident-booking-action-danger:hover {
    background: rgba(120, 42, 46, 0.86);
    color: #FFD6D0;
}
.resident-booking-history-card .resident-booking-schedule-grid {
    display: none;
}
.resident-card-links {
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-end;
    gap: 10px;
}
/* Fix:button shares the same visual style as links so Cancel looks consistent */
.resident-card-links a, .resident-card-links button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 10px 4px;
    color: #d7b07a;
    text-decoration: none;
    background: none;
    border: none;
    cursor: pointer;
    font-family: inherit;
    font-size: 0.82rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
}
.resident-card-links button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 10px 14px;
    border-radius: 999px;
    border: 1px solid rgba(224, 112, 96, 0.26);
    background: rgba(224, 112, 96, 0.08);
    color: #f0a297;
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
/*
         * Improvement:time-slot box gets a gold-tinted background so the most
         * critical booking fact (when is it?) reads at a glance before anything else.
         */
.resident-booking-schedule-grid .resident-meta-box:first-child {
    background: rgba(214, 168, 91, 0.08);
    border-color: rgba(214, 168, 91, 0.14);
}
.resident-booking-schedule-grid .resident-meta-box:first-child strong {
    color: #e9c17f;
    font-size: 1rem;
    font-weight: 700;
}
.resident-meta-box {
    padding: 14px 16px;
    border-radius: 14px;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.04);
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
    margin-top: 6px;
    color: #F0E9DF;
    font-size: 0.94rem;
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
.resident-badge-status-cancelled {
    background: rgba(185, 106, 93, 0.16);
    color: #dc9a86;
}
.resident-empty-state {
    padding: 26px;
    border-radius: 18px;
    background: rgba(255, 255, 255, 0.03);
    border: 1px dashed rgba(214, 168, 91, 0.18);
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
@media (max-width:768px) {
    .resident-page {
        padding: 18px 0 28px;
    }
    .resident-page-hero, .resident-page-panel {
        padding: 22px;
    }
    .resident-page-hero {
        flex-direction: column;
        align-items: flex-start;
    }
    .resident-card-top {
        flex-direction: column;
    }
    .resident-booking-ledger-card .resident-card-top, .resident-booking-ledger-card .resident-card-head {
        align-items: stretch;
    }
    .resident-booking-action-pill {
        width: 100%;
    }
    .resident-booking-action {
        flex: 1 1 0;
    }
    .resident-booking-schedule-grid {
        grid-template-columns: 1fr;
    }
}
@media (max-width:560px) {
    .resident-page-title {
        font-size: 2.15rem;
    }
    .resident-page-subtitle {
        font-size: 0.95rem;
    }
    .resident-page-panel-head h2, .resident-day-heading {
        font-size: 1.2rem;
    }
    .resident-card, .resident-page-panel, .resident-page-hero {
        border-radius: 22px;
    }
    .resident-card-links {
        width: 100%;
        justify-content: stretch;
    }
    .resident-card-links a, .resident-card-links form, .resident-card-links button {
        width: 100%;
    }
    .resident-card-links form {
        display: flex;
    }
    .resident-booking-inline-meta {
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
    }
    .resident-booking-inline-meta span + span::before {
        display: none;
    }
    .resident-booking-action {
        min-height: 32px;
        padding: 0 12px;
        font-size: 0.85rem;
    }
    .resident-day-heading {
        gap: 10px;
    }
}
</style>
</x-app-layout>
