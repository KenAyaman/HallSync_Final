<x-app-layout>
    <div class="resident-booking-edit-page">
        <section class="resident-booking-edit-hero">
            <div class="resident-booking-edit-copy">
                <p class="resident-booking-edit-kicker">Modify Your Reservation</p>
                <h1 class="resident-booking-edit-title">Edit Booking</h1>
                <p class="resident-booking-edit-subtitle">
                    Update your facility, date, or time slot with a cleaner reservation editor that matches the rest of the resident portal.
                </p>

                <div class="resident-booking-edit-stats">
                    <div class="resident-booking-edit-stat">
                        <span>Facility</span>
                        <strong>{{ old('facility_name', $booking->facility_name) }}</strong>
                    </div>
                    <div class="resident-booking-edit-stat">
                        <span>Date</span>
                        <strong>{{ \Carbon\Carbon::parse(old('booking_date', $selectedDate))->format('M d, Y') }}</strong>
                    </div>
                    <div class="resident-booking-edit-stat">
                        <span>Status</span>
                        <strong>{{ ucfirst($booking->status) }}</strong>
                    </div>
                </div>
            </div>

            <div class="resident-booking-edit-actions">
                <a href="{{ route('bookings.show', $booking) }}" class="resident-booking-edit-btn resident-booking-edit-btn-secondary">Back to Booking</a>
            </div>
        </section>

        @if($errors->any())
            <div class="resident-booking-edit-error">
                <div class="resident-booking-edit-error-title">Please fix the following:</div>
                <ul class="resident-booking-edit-error-list">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="resident-booking-edit-grid">
            <section class="resident-booking-edit-panel">
                <div class="resident-booking-edit-head">
                    <div>
                        <h2>Edit Booking</h2>
                        <p>Adjust your reservation details while keeping the new slot aligned with live availability.</p>
                    </div>

                    <span class="resident-booking-edit-eyebrow">Update Details</span>
                </div>

                <div class="resident-booking-edit-divider"></div>

                <form method="POST" action="{{ route('bookings.update', $booking) }}" class="resident-booking-edit-form">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="facility_name" class="resident-booking-edit-label">Facility Name</label>
                        <select name="facility_name" id="facility_name" required class="resident-booking-edit-input">
                            <option value="">Select a facility</option>
                            <option value="Study Room 1" {{ old('facility_name', $booking->facility_name) == 'Study Room 1' ? 'selected' : '' }}>Study Room 1</option>
                            <option value="Study Room 2" {{ old('facility_name', $booking->facility_name) == 'Study Room 2' ? 'selected' : '' }}>Study Room 2</option>
                            <option value="Conference Room" {{ old('facility_name', $booking->facility_name) == 'Conference Room' ? 'selected' : '' }}>Conference Room</option>
                            <option value="Gym" {{ old('facility_name', $booking->facility_name) == 'Gym' ? 'selected' : '' }}>Gym</option>
                        </select>
                        <p class="resident-booking-edit-help">Select the facility you want to reserve.</p>
                    </div>

                    <div>
                        <label for="booking_date" class="resident-booking-edit-label">Booking Date</label>
                        <input type="date" name="booking_date" id="booking_date" value="{{ old('booking_date', $selectedDate) }}" required class="resident-booking-edit-input">
                        <p class="resident-booking-edit-help">Past dates are not allowed.</p>
                    </div>

                    <div>
                        <label class="resident-booking-edit-label">Select Time Slot</label>
                        <input type="hidden" name="booking_time" id="booking_time" value="{{ old('booking_time', $selectedTime) }}">
                        <div id="slot-grid" class="resident-booking-slot-grid"></div>
                        <p class="resident-booking-edit-help">Choose one available time slot. Reserved slots are locked and cannot be selected.</p>
                    </div>

                    <div class="resident-booking-edit-form-actions">
                        <button type="submit" class="resident-booking-edit-btn resident-booking-edit-btn-primary">Update Booking</button>
                        <a href="{{ route('bookings.show', $booking) }}" class="resident-booking-edit-btn resident-booking-edit-btn-secondary">Cancel</a>
                    </div>
                </form>
            </section>

            <aside class="resident-booking-edit-sidebar">
                <section class="resident-booking-edit-panel">
                    <div class="resident-booking-edit-head resident-booking-edit-head-simple">
                        <div>
                            <h2>Booking Rules</h2>
                            <p>Quick reminders before you confirm changes.</p>
                        </div>
                    </div>

                    <div class="resident-booking-edit-divider"></div>

                    <div class="resident-booking-edit-note-list">
                        <div class="resident-booking-edit-note-item">Only today and future dates are allowed.</div>
                        <div class="resident-booking-edit-note-item">Each booking is limited to one time slot or one hour block.</div>
                        <div class="resident-booking-edit-note-item">Confirmed bookings can still be adjusted if the new slot remains available.</div>
                    </div>
                </section>

                <section class="resident-booking-edit-panel">
                    <div class="resident-booking-edit-head resident-booking-edit-head-simple">
                        <div>
                            <h2>Time Slot Guide</h2>
                            <p>How the booking availability colors work.</p>
                        </div>
                    </div>

                    <div class="resident-booking-edit-divider"></div>

                    <div class="resident-booking-edit-meta-list">
                        <div class="resident-booking-edit-meta-item">
                            <span>Available</span>
                            <strong>Green cards can be selected.</strong>
                        </div>
                        <div class="resident-booking-edit-meta-item">
                            <span>Reserved</span>
                            <strong>Gray cards are already blocked.</strong>
                        </div>
                        <div class="resident-booking-edit-meta-item">
                            <span>Selected</span>
                            <strong>Gold highlights the slot you chose.</strong>
                        </div>
                    </div>
                </section>
            </aside>
        </div>
    </div>

    <script>
        const bookingDateInput = document.getElementById('booking_date');
        const facilityInput = document.getElementById('facility_name');
        const slotGrid = document.getElementById('slot-grid');
        const bookingTimeInput = document.getElementById('booking_time');

        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        bookingDateInput.min = `${yyyy}-${mm}-${dd}`;

        function formatTime(time24) {
            const [hour, minute] = time24.split(':');
            let h = parseInt(hour);
            const suffix = h >= 12 ? 'PM' : 'AM';
            h = h % 12 || 12;
            return `${h}:${minute} ${suffix}`;
        }

        function clearSlots() {
            slotGrid.innerHTML = '';
        }

        async function loadSlots() {
            const facility = facilityInput.value;
            const date = bookingDateInput.value;

            if (!facility || !date) {
                clearSlots();
                return;
            }

            try {
                const url = `{{ route('bookings.reserved-slots') }}?facility_name=${encodeURIComponent(facility)}&booking_date=${encodeURIComponent(date)}`;
                const response = await fetch(url);
                const data = await response.json();
                renderSlots(data.available_slots || [], data.reserved_slots || []);
            } catch (error) {
                clearSlots();
                console.error('Failed to load slots:', error);
            }
        }

        function renderSlots(timeSlots, reservedSlots) {
            slotGrid.innerHTML = '';

            timeSlots.forEach(slot => {
                const isReserved = reservedSlots.includes(slot);
                const isSelected = bookingTimeInput.value === slot;

                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'resident-booking-slot';
                btn.dataset.slot = slot;
                btn.dataset.available = isReserved ? 'false' : 'true';
                btn.innerHTML = `
                    <div class="resident-booking-slot-time">${formatTime(slot)}</div>
                    <div class="resident-booking-slot-state">${isReserved ? 'Reserved' : 'Available'}</div>
                `;

                if (isReserved) {
                    btn.classList.add('is-reserved');
                } else if (isSelected) {
                    btn.classList.add('is-selected');
                } else {
                    btn.classList.add('is-available');
                    btn.addEventListener('click', function () {
                        document.querySelectorAll('#slot-grid .resident-booking-slot').forEach(other => {
                            if (other.dataset.available === 'true') {
                                other.classList.remove('is-selected');
                                other.classList.add('is-available');
                            }
                        });

                        btn.classList.remove('is-available');
                        btn.classList.add('is-selected');
                        bookingTimeInput.value = slot;
                    });
                }

                slotGrid.appendChild(btn);
            });
        }

        facilityInput.addEventListener('change', loadSlots);
        bookingDateInput.addEventListener('change', loadSlots);

        if (facilityInput.value && bookingDateInput.value) {
            loadSlots();
        }
    </script>

    <style>
        .resident-booking-edit-page {
            max-width: 1600px;
            margin: 0 auto;
            padding: 24px 16px 32px;
            display: flex;
            flex-direction: column;
            gap: 22px;
        }

        .resident-booking-edit-hero,
        .resident-booking-edit-panel,
        .resident-booking-edit-error {
            border: 1px solid rgba(214,168,91,0.14);
            box-shadow: 0 12px 24px rgba(0,0,0,0.14);
        }

        .resident-booking-edit-hero {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 20px;
            padding: 28px 30px;
            border-radius: 36px;
            background: linear-gradient(115deg, #1F2023 0%, #24262B 38%, #2C2C2F 62%, #3B3023 100%);
            box-shadow: 0 18px 50px rgba(0, 0, 0, 0.18);
        }

        .resident-booking-edit-copy { max-width: 860px; }
        .resident-booking-edit-kicker {
            margin: 0 0 10px;
            color: #D2A04C;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.30em;
        }

        .resident-booking-edit-title {
            margin: 0;
            color: #F8F3EA;
            font-family: 'Playfair Display', serif;
            font-size: clamp(2.4rem, 4.6vw, 3.8rem);
            line-height: 1.05;
        }

        .resident-booking-edit-subtitle {
            margin: 12px 0 0;
            color: rgba(255,255,255,0.82);
            font-size: 1.02rem;
            line-height: 1.7;
            max-width: 760px;
        }

        .resident-booking-edit-stats {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            margin-top: 22px;
        }

        .resident-booking-edit-stat {
            min-width: 130px;
            padding: 12px 14px;
            border-radius: 16px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.07);
        }

        .resident-booking-edit-stat span {
            display: block;
            color: #A89376;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.14em;
            font-weight: 700;
        }

        .resident-booking-edit-stat strong {
            display: block;
            margin-top: 6px;
            color: #F0E9DF;
            font-size: 1rem;
            font-weight: 700;
        }

        .resident-booking-edit-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .resident-booking-edit-error {
            background: linear-gradient(180deg, rgba(53, 38, 35, 0.92) 0%, rgba(42, 31, 29, 0.92) 100%);
            border-radius: 20px;
            padding: 18px 22px;
            color: #F0B3A9;
            border-color: rgba(224,112,96,0.22);
        }

        .resident-booking-edit-error-title {
            font-weight: 700;
            margin-bottom: 8px;
            color: #FFB2A7;
        }

        .resident-booking-edit-error-list {
            margin: 0;
            padding-left: 18px;
            color: #E7C3BD;
            line-height: 1.7;
        }

        .resident-booking-edit-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.18fr) minmax(320px, 0.82fr);
            gap: 24px;
            align-items: start;
        }

        .resident-booking-edit-sidebar {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .resident-booking-edit-panel {
            padding: 26px 28px;
            border-radius: 20px;
            background: rgba(42,44,48,0.78);
            backdrop-filter: blur(10px);
        }

        .resident-booking-edit-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }

        .resident-booking-edit-head h2 {
            margin: 0;
            color: #F0E9DF;
            font-size: 1.5rem;
            font-family: 'Playfair Display', serif;
        }

        .resident-booking-edit-head p {
            margin: 4px 0 0;
            color: #8A7A66;
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .resident-booking-edit-eyebrow {
            color: #D6A85B;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.16em;
        }

        .resident-booking-edit-divider {
            height: 1px;
            background: linear-gradient(to right, rgba(214,168,91,0.3), rgba(214,168,91,0.05), transparent);
            margin-bottom: 20px;
        }

        .resident-booking-edit-form {
            display: flex;
            flex-direction: column;
            gap: 22px;
        }

        .resident-booking-edit-label {
            display: block;
            font-weight: 700;
            margin-bottom: 10px;
            color: #D0C8B8;
            font-size: 14px;
            letter-spacing: 0.02em;
        }

        .resident-booking-edit-input {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid rgba(214,168,91,0.14);
            border-radius: 16px;
            font-size: 15px;
            color: #F8F3EA;
            background: rgba(37,39,42,0.90);
            outline: none;
            transition: all 0.2s ease;
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.02);
            font-family: inherit;
        }

        .resident-booking-edit-input:focus {
            border-color: rgba(214,168,91,0.38);
            box-shadow: 0 0 0 4px rgba(214,168,91,0.08);
        }

        .resident-booking-edit-textarea {
            resize: vertical;
            min-height: 140px;
            line-height: 1.75;
        }

        .resident-booking-edit-help {
            margin: 8px 0 0;
            color: #8A7A66;
            font-size: 0.8rem;
            line-height: 1.7;
        }

        .resident-booking-slot-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .resident-booking-slot {
            width: 100%;
            padding: 14px 12px;
            border-radius: 16px;
            transition: all 0.2s ease;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.05);
        }

        .resident-booking-slot-time {
            font-weight: 700;
            font-size: 15px;
        }

        .resident-booking-slot-state {
            font-size: 12px;
            margin-top: 4px;
        }

        .resident-booking-slot.is-reserved {
            border-color: rgba(255,255,255,0.05);
            background: rgba(255,255,255,0.04);
            color: #8A7A66;
            cursor: not-allowed;
        }

        .resident-booking-slot.is-available {
            border-color: rgba(120,170,120,0.25);
            background: rgba(120,170,120,0.10);
            color: #98c48b;
            cursor: pointer;
        }

        .resident-booking-slot.is-selected {
            border: 2px solid rgba(214,168,91,0.48);
            background: rgba(214,168,91,0.16);
            color: #e8c58a;
            cursor: pointer;
        }

        .resident-booking-edit-form-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            padding-top: 8px;
            border-top: 1px solid rgba(214,168,91,0.10);
        }

        .resident-booking-edit-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 13px 22px;
            border-radius: 999px;
            text-decoration: none;
            font-size: 0.92rem;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .resident-booking-edit-btn:hover { transform: translateY(-1px); }
        .resident-booking-edit-btn-primary {
            background: linear-gradient(95deg, #b8842f, #d6a85b);
            color: #17120d;
            box-shadow: 0 10px 24px rgba(199, 150, 69, 0.28);
        }

        .resident-booking-edit-btn-secondary {
            background: rgba(255,255,255,0.04);
            color: #D0C8B8;
            border: 1px solid rgba(214,168,91,0.14);
        }

        .resident-booking-edit-note-list,
        .resident-booking-edit-meta-list {
            display: grid;
            gap: 12px;
        }

        .resident-booking-edit-note-item,
        .resident-booking-edit-meta-item {
            padding: 14px 16px;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 16px;
        }

        .resident-booking-edit-note-item {
            color: #B8AB98;
            font-size: 0.88rem;
            line-height: 1.75;
        }

        .resident-booking-edit-meta-item span {
            display: block;
            color: #8A7A66;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            margin-bottom: 8px;
        }

        .resident-booking-edit-meta-item strong {
            color: #F0E9DF;
            font-size: 0.92rem;
            line-height: 1.6;
            font-weight: 600;
        }

        @media (max-width: 1024px) {
            .resident-booking-edit-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            .resident-booking-edit-page { padding: 18px 0 28px; }
            .resident-booking-edit-hero,
            .resident-booking-edit-panel { padding: 22px; }
            .resident-booking-edit-hero { flex-direction: column; align-items: flex-start; }
            .resident-booking-slot-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 560px) {
            .resident-booking-edit-btn { width: 100%; }
            .resident-booking-edit-form-actions { flex-direction: column; }
        }
    </style>
</x-app-layout>
