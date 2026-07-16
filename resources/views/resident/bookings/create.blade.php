<x-app-layout>
    <div class="resident-booking-create-page">
        <div class="resident-booking-create-topbar">
            <a href="{{ route('bookings.index') }}" class="resident-back-link resident-create-back">← Back</a>
        </div>

        <section class="resident-booking-create-hero">
            <div class="resident-booking-create-copy">
                <p class="resident-booking-create-kicker">Resident Booking Hub</p>
                <h1 class="resident-booking-create-title">Book a Facility</h1>
                <p class="resident-booking-create-subtitle">
                    Reserve a shared Rexhall space with a cleaner booking flow that matches the rest of your resident dashboard experience.
                </p>

                <div class="resident-booking-create-stats">
                    <div class="resident-booking-create-stat">
                        <span>Facilities</span>
                        <strong>4 spaces</strong>
                    </div>
                    <div class="resident-booking-create-stat">
                        <span>Booking Type</span>
                        <strong>Single slot</strong>
                    </div>
                    <div class="resident-booking-create-stat">
                        <span>Availability</span>
                        <strong>Live check</strong>
                    </div>
                </div>
            </div>

        </section>

        <section class="resident-booking-create-panel">
                <div class="resident-booking-create-head">
                    <div>
                        <h2>Booking Details</h2>
                        <p>Select your facility, date, and one available slot.</p>
                    </div>

                    <span class="resident-booking-create-eyebrow">Time Slot Picker</span>
                </div>

                <div class="resident-booking-create-divider"></div>

                <form method="POST" action="{{ route('bookings.store') }}" class="resident-booking-create-form" data-prevent-double-submit data-submitting-text="Confirming Booking...">
                    @csrf

                    <div>
                        <label for="facility_intent" class="resident-booking-create-label">What do you need?</label>
                        <select id="facility_intent" class="resident-booking-create-input">
                            <option value="">Show all spaces</option>
                            <option value="silent">Silent zone</option>
                            <option value="monitor">Monitor / TV</option>
                            <option value="group">Group capacity (4+ people)</option>
                        </select>
                        <p class="resident-booking-create-help">Use an activity filter to narrow the facility list before choosing a room.</p>
                    </div>

                    <div>
                        <label for="facility_name" class="resident-booking-create-label">Facility Name</label>
                        <select name="facility_name" id="facility_name" required class="resident-booking-create-input" aria-describedby="facility_name_help facility_name_error" @error('facility_name') aria-invalid="true" @enderror>
                            <option value="">Select a facility</option>
                            <option value="Study Room 1" data-tags="silent monitor" @selected(old('facility_name') === 'Study Room 1')>Study Room 1</option>
                            <option value="Study Room 2" data-tags="silent" @selected(old('facility_name') === 'Study Room 2')>Study Room 2</option>
                            <option value="Conference Room" data-tags="group monitor" @selected(old('facility_name') === 'Conference Room')>Conference Room</option>
                            <option value="Gym" data-tags="group" @selected(old('facility_name') === 'Gym')>Gym</option>
                        </select>
                        <p id="facility_name_help" class="resident-booking-create-help">Select the shared facility you want to reserve.</p>
                        @error('facility_name')
                            <p id="facility_name_error" class="app-field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="booking_date" class="resident-booking-create-label">Booking Date</label>
                        <input type="date"
                               name="booking_date"
                               id="booking_date"
                               value="{{ old('booking_date') }}"
                               required
                               class="resident-booking-create-input"
                               aria-describedby="booking_date_help booking_date_error"
                               @error('booking_date') aria-invalid="true" @enderror>
                        <p id="booking_date_help" class="resident-booking-create-help">Past dates and elapsed times are not available.</p>
                        @error('booking_date')
                            <p id="booking_date_error" class="app-field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label id="booking_time_label" class="resident-booking-create-label">Select Time Slot</label>
                        <input type="hidden" name="booking_time" id="booking_time" value="{{ old('booking_time') }}" aria-describedby="booking_time_help booking_time_error">
                        <p id="slot-status" class="resident-booking-slot-status" role="status" aria-live="polite">Choose a facility and date to see available times.</p>
                        <div id="slot-grid" class="resident-booking-slot-grid" role="group" aria-labelledby="booking_time_label"></div>
                        <div id="availability-recommendations" class="resident-booking-recommendations" aria-live="polite"></div>
                        <p id="booking_time_help" class="resident-booking-create-help">Choose one available one-hour slot. Full slots and times you already reserved are locked automatically.</p>
                        @error('booking_time')
                            <p id="booking_time_error" class="app-field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="group_members" class="resident-booking-create-label">Roommates / Group Members <span>(Optional)</span></label>
                        <input type="text" name="group_members" id="group_members" value="{{ old('group_members') }}" class="resident-booking-create-input" placeholder="Example: Ana Cruz, Room 204">
                        <p class="resident-booking-create-help">Add names or room numbers so your group details stay with the reservation.</p>
                    </div>

                    <div class="resident-booking-create-form-actions">
                        <button type="submit" class="resident-booking-create-btn resident-booking-create-btn-primary" data-booking-submit disabled>Confirm Booking</button>
                        <a href="{{ route('bookings.index') }}" class="resident-booking-create-btn resident-booking-create-btn-secondary">Cancel</a>
                    </div>
                </form>
        </section>
    </div>

    <script>
        const bookingDateInput = document.getElementById('booking_date');
        const facilityInput = document.getElementById('facility_name');
        const facilityIntentInput = document.getElementById('facility_intent');
        const slotGrid = document.getElementById('slot-grid');
        const bookingTimeInput = document.getElementById('booking_time');
        const slotStatus = document.getElementById('slot-status');
        const recommendationPanel = document.getElementById('availability-recommendations');
        const submitButton = document.querySelector('[data-booking-submit]');

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

        function setSlotStatus(message, state = '') {
            slotStatus.textContent = message;
            slotStatus.dataset.state = state;
        }

        function syncSubmitButton() {
            submitButton.disabled = !bookingTimeInput.value;
        }

        function clearSlots(message = 'Choose a facility and date to see available times.', state = '') {
            slotGrid.innerHTML = '';
            recommendationPanel.innerHTML = '';
            setSlotStatus(message, state);
            syncSubmitButton();
        }

        async function loadSlots() {
            const facility = facilityInput.value;
            const date = bookingDateInput.value;

            if (!facility || !date) {
                clearSlots();
                return;
            }

            slotGrid.innerHTML = '';
            setSlotStatus('Checking live availability...', 'loading');

            try {
                const url = `{{ route('bookings.reserved-slots') }}?facility_name=${encodeURIComponent(facility)}&booking_date=${encodeURIComponent(date)}`;
                const response = await fetch(url, { headers: { 'Accept': 'application/json' } });
                if (!response.ok) {
                    throw new Error('Availability request failed');
                }
                const data = await response.json();
                renderSlots(data.available_slots || [], data.full_slots || [], data.resident_reserved_slots || [], data.slot_capacity || {});
                renderRecommendations(data.recommendations || []);
            } catch (error) {
                bookingTimeInput.value = '';
                clearSlots('Availability could not be loaded. Please try again.', 'error');
                window.appToast?.('error', 'Availability could not be loaded. Please check your connection and try again.');
                console.error('Failed to load slots:', error);
            }
        }

        function renderRecommendations(recommendations) {
            const useful = recommendations
                .filter(item => item.available_slots?.length)
                .slice(0, 2);

            recommendationPanel.innerHTML = useful.length
                ? `<strong>Smart alternatives</strong>${useful.map(item =>
                    `<span>${item.facility_name} has openings starting at ${formatTime(item.available_slots[0])}.</span>`
                ).join('')}`
                : '';
        }

        function renderSlots(timeSlots, fullSlots, residentReservedSlots, slotCapacity = {}) {
            slotGrid.innerHTML = '';
            let availableCount = 0;

            timeSlots.forEach(slot => {
                const capacity = slotCapacity[slot] || { reserved: 0, capacity: 0, available: 0 };
                const isFull = fullSlots.includes(slot);
                const hasResidentConflict = residentReservedSlots.includes(slot);
                const isPast = isPastSlot(slot);
                const isUnavailable = isFull || hasResidentConflict || isPast;
                const isSelected = !isUnavailable && bookingTimeInput.value === slot;

                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'resident-booking-slot';
                btn.dataset.slot = slot;
                btn.dataset.available = isUnavailable ? 'false' : 'true';
                btn.disabled = isUnavailable;
                btn.setAttribute('aria-pressed', isSelected ? 'true' : 'false');
                btn.innerHTML = `
                    <div class="resident-booking-slot-time">${formatTime(slot)}</div>
                    <div class="resident-booking-slot-state">${isFull ? 'Full' : (hasResidentConflict ? 'Reserved' : (isPast ? 'Elapsed' : (isSelected ? 'Selected' : 'Available')))}</div>
                    <div class="resident-booking-slot-capacity">Capacity: ${capacity.reserved}/${capacity.capacity}</div>
                `;

                if (isUnavailable) {
                    btn.classList.add('is-reserved');
                } else if (isSelected) {
                    availableCount += 1;
                    btn.classList.add('is-selected');
                    btn.addEventListener('click', function () {
                        selectSlot(btn, slot);
                    });
                } else {
                    availableCount += 1;
                    btn.classList.add('is-available');
                    btn.addEventListener('click', function () {
                        selectSlot(btn, slot);
                    });
                }

                slotGrid.appendChild(btn);
            });

            if (!availableCount) {
                bookingTimeInput.value = '';
                setSlotStatus('No available slots remain for this date. Please choose another date.', 'error');
            } else {
                setSlotStatus(`${availableCount} available ${availableCount === 1 ? 'slot' : 'slots'}. Select one to continue.`, 'ready');
            }

            syncSubmitButton();
        }

        function isPastSlot(slot) {
            if (bookingDateInput.value !== `${yyyy}-${mm}-${dd}`) {
                return false;
            }

            const [hour, minute] = slot.split(':').map(Number);
            const slotDate = new Date();
            slotDate.setHours(hour, minute, 0, 0);
            return slotDate < new Date();
        }

        function selectSlot(selectedButton, slot) {
            document.querySelectorAll('#slot-grid .resident-booking-slot').forEach(other => {
                if (other.dataset.available === 'true') {
                    other.classList.remove('is-selected');
                    other.classList.add('is-available');
                    other.setAttribute('aria-pressed', 'false');
                    other.querySelector('.resident-booking-slot-state').textContent = 'Available';
                }
            });

            selectedButton.classList.remove('is-available');
            selectedButton.classList.add('is-selected');
            selectedButton.setAttribute('aria-pressed', 'true');
            selectedButton.querySelector('.resident-booking-slot-state').textContent = 'Selected';
            bookingTimeInput.value = slot;
            setSlotStatus(`${formatTime(slot)} selected. You can confirm your booking.`, 'selected');
            syncSubmitButton();
        }

        function refreshSlots() {
            bookingTimeInput.value = '';
            syncSubmitButton();
            loadSlots();
        }

        facilityInput.addEventListener('change', refreshSlots);
        bookingDateInput.addEventListener('change', refreshSlots);
        facilityIntentInput.addEventListener('change', () => {
            const intent = facilityIntentInput.value;
            Array.from(facilityInput.options).forEach(option => {
                if (!option.value) {
                    return;
                }

                option.hidden = intent && !option.dataset.tags.split(' ').includes(intent);
            });

            if (facilityInput.selectedOptions[0]?.hidden) {
                facilityInput.value = '';
                refreshSlots();
            }
        });

        if (facilityInput.value && bookingDateInput.value) {
            loadSlots();
        } else {
            syncSubmitButton();
        }
    </script>

    <style>
.resident-booking-create-page {
    max-width: 1600px;
    margin: 0 auto;
    padding: 24px 16px 32px;
    display: flex;
    flex-direction: column;
    gap: 22px;
}
.resident-booking-create-hero, .resident-booking-create-panel {
    border: 1px solid rgba(214, 168, 91, 0.14);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.14);
}
.resident-booking-create-hero {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    gap: 20px;
    padding: 28px 30px;
    border-radius: 36px;
    background: linear-gradient(115deg, #1F2023 0%, #24262B 38%, #2C2C2F 62%, #3B3023 100%);
    box-shadow: 0 18px 50px rgba(0, 0, 0, 0.18);
}
.resident-booking-create-copy {
    max-width: 860px;
}
.resident-booking-create-kicker {
    margin: 0 0 10px;
    color: #D2A04C;
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.30em;
}
.resident-booking-create-title {
    margin: 0;
    color: #F8F3EA;
    font-family: 'Playfair Display', serif;
    font-size: clamp(2.4rem, 4.6vw, 3.8rem);
    line-height: 1.05;
}
.resident-booking-create-subtitle {
    margin: 12px 0 0;
    color: rgba(255, 255, 255, 0.82);
    font-size: 1.02rem;
    line-height: 1.7;
    max-width: 760px;
}
.resident-booking-create-stats {
    display: flex;
    flex-wrap: wrap;
    gap: 14px;
    margin-top: 22px;
}
.resident-booking-create-stat {
    min-width: 130px;
    padding: 12px 14px;
    border-radius: 16px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.07);
}
.resident-booking-create-stat span {
    display: block;
    color: #A89376;
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 0.14em;
    font-weight: 700;
}
.resident-booking-create-stat strong {
    display: block;
    margin-top: 6px;
    color: #F0E9DF;
    font-size: 1rem;
    font-weight: 700;
}
.resident-booking-create-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
}
.resident-booking-create-topbar {
    display: flex;
    justify-content: flex-start;
    align-items: center;
}
.resident-booking-create-panel {
    padding: 26px 28px;
    border-radius: 20px;
    background: rgba(42, 44, 48, 0.78);
    backdrop-filter: blur(10px);
}
.resident-booking-create-head {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 16px;
    margin-bottom: 16px;
}
.resident-booking-create-head h2 {
    margin: 0;
    color: #F0E9DF;
    font-size: 1.5rem;
    font-family: 'Playfair Display', serif;
}
.resident-booking-create-head p {
    margin: 4px 0 0;
    color: #8A7A66;
    font-size: 0.95rem;
}
.resident-booking-create-eyebrow {
    color: #D6A85B;
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.16em;
}
.resident-booking-create-divider {
    height: 1px;
    background: linear-gradient(to right, rgba(214, 168, 91, 0.3), rgba(214, 168, 91, 0.05), transparent);
    margin-bottom: 18px;
}
.resident-booking-create-form {
    display: flex;
    flex-direction: column;
    gap: 18px;
}
.resident-booking-create-label {
    display: block;
    font-weight: 700;
    margin-bottom: 10px;
    color: #D0C8B8;
    font-size: 14px;
    letter-spacing: 0.02em;
}
.resident-booking-create-input {
    width: 100%;
    padding: 14px 16px;
    border: 1px solid rgba(214, 168, 91, 0.14);
    border-radius: 16px;
    font-size: 15px;
    color: #F8F3EA;
    background: rgba(37, 39, 42, 0.90);
    outline: none;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.02);
}
.resident-booking-create-input:focus {
    border-color: rgba(214, 168, 91, 0.38);
    box-shadow: 0 0 0 4px rgba(214, 168, 91, 0.08);
}
.resident-booking-create-input[type="date"] {
    color-scheme: dark;
}
.resident-booking-create-input[type="date"]::-webkit-calendar-picker-indicator {
    cursor: pointer;
    opacity: 1;
    width: 18px;
    height: 18px;
    padding: 5px;
    border-radius: 10px;
    background-color: rgba(214, 168, 91, 0.18);
    filter: invert(91%) sepia(43%) saturate(807%) hue-rotate(343deg) brightness(112%) contrast(101%) drop-shadow(0 0 4px rgba(214, 168, 91, 0.55));
}
.resident-booking-create-textarea {
    resize: vertical;
    min-height: 140px;
    line-height: 1.7;
}
.resident-booking-create-help {
    font-size: 12px;
    color: #9A8D7B;
    margin-top: 8px;
}
.resident-booking-slot-status {
    margin: 0 0 12px;
    padding: 12px 14px;
    border-radius: 12px;
    color: #B8AB98;
    background: rgba(255, 255, 255, 0.03);
    border: 1px dashed rgba(214, 168, 91, 0.16);
    font-size: 0.84rem;
    line-height: 1.5;
}
.resident-booking-slot-status[data-state="loading"] {
    color: #F2D49A;
}
.resident-booking-slot-status[data-state="error"] {
    color: #F0B3A9;
    border-color: rgba(224, 112, 96, 0.24);
}
.resident-booking-recommendations {
    display: grid;
    gap: 6px;
    margin-top: 12px;
    color: #B8AB98;
    font-size: 0.82rem;
}
.resident-booking-recommendations strong {
    color: #D6A85B;
}
.resident-booking-slot-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
}
.resident-booking-slot {
    width: 100%;
    text-align: left;
    padding: 14px;
    border-radius: 18px;
    border: 1px solid rgba(214, 168, 91, 0.14);
    background: rgba(255, 255, 255, 0.03);
    color: #F0E9DF;
    cursor: pointer;
    transition: 0.2s ease;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.03);
}
.resident-booking-slot-time {
    font-size: 0.95rem;
    font-weight: 700;
}
.resident-booking-slot-state {
    margin-top: 6px;
    font-size: 0.82rem;
    color: #B8AB98;
}
.resident-booking-slot-capacity {
    min-height: 18px;
    margin-top: 4px;
    color: #8A7A66;
    font-size: 0.76rem;
}
.resident-booking-slot.is-available:hover {
    transform: translateY(-1px);
    border-color: rgba(111, 160, 111, 0.35);
    background: rgba(111, 160, 111, 0.08);
}
.resident-booking-slot.is-selected {
    border-color: rgba(214, 168, 91, 0.78);
    background: linear-gradient(135deg, rgba(214, 168, 91, 0.26), rgba(184, 132, 47, 0.16));
    color: #FFF2D3;
    box-shadow: 0 0 0 3px rgba(214, 168, 91, 0.12), 0 14px 28px rgba(0, 0, 0, 0.22);
}
.resident-booking-slot.is-selected .resident-booking-slot-state {
    color: #F2D49A;
    font-weight: 800;
}
.resident-booking-slot.is-reserved {
    cursor: not-allowed;
    opacity: 0.75;
    border-style: dashed;
    background: rgba(255, 255, 255, 0.025);
}
.resident-booking-create-form-actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    padding-top: 8px;
    border-top: 1px solid rgba(214, 168, 91, 0.10);
}
.resident-booking-create-btn {
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
.resident-booking-create-btn:hover {
    transform: translateY(-1px);
}
.resident-booking-create-btn-primary {
    background: linear-gradient(95deg, #b8842f, #d6a85b);
    color: #17120d;
    border: none;
    cursor: pointer;
}
.resident-booking-create-btn-primary:disabled {
    cursor: not-allowed;
    opacity: 0.52;
    transform: none;
}
.resident-booking-create-btn-secondary {
    background: rgba(255, 255, 255, 0.04);
    color: #D0C8B8;
    border: 1px solid rgba(214, 168, 91, 0.14);
}
@media (max-width:768px) {
    .resident-booking-create-hero {
        flex-direction: column;
        align-items: flex-start;
    }
    .resident-booking-slot-grid {
        grid-template-columns: 1fr;
    }
}
</style>
</x-app-layout>
