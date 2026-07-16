<x-app-layout>
    <div class="resident-booking-edit-page">
        <div class="resident-booking-edit-topbar">
            <a href="{{ route('bookings.index') }}" class="resident-back-link resident-create-back">← Back</a>
        </div>

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
                </div>
            </div>

        </section>

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

                <form method="POST" action="{{ route('bookings.update', $booking) }}" id="resident-booking-edit-form" class="resident-booking-edit-form" data-prevent-double-submit data-submitting-text="Saving Booking...">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="facility_name" class="resident-booking-edit-label">Facility Name</label>
                        <select name="facility_name" id="facility_name" required class="resident-booking-edit-input" aria-describedby="facility_name_help facility_name_error" @error('facility_name') aria-invalid="true" @enderror>
                            <option value="">Select a facility</option>
                            <option value="Study Room 1" {{ old('facility_name', $booking->facility_name) == 'Study Room 1' ? 'selected' : '' }}>Study Room 1</option>
                            <option value="Study Room 2" {{ old('facility_name', $booking->facility_name) == 'Study Room 2' ? 'selected' : '' }}>Study Room 2</option>
                            <option value="Conference Room" {{ old('facility_name', $booking->facility_name) == 'Conference Room' ? 'selected' : '' }}>Conference Room</option>
                            <option value="Gym" {{ old('facility_name', $booking->facility_name) == 'Gym' ? 'selected' : '' }}>Gym</option>
                        </select>
                        <p id="facility_name_help" class="resident-booking-edit-help">Select the facility you want to reserve.</p>
                        @error('facility_name')
                            <p id="facility_name_error" class="app-field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="booking_date" class="resident-booking-edit-label">Booking Date</label>
                        <input type="date" name="booking_date" id="booking_date" value="{{ old('booking_date', $selectedDate) }}" required class="resident-booking-edit-input" aria-describedby="booking_date_help booking_date_error" @error('booking_date') aria-invalid="true" @enderror>
                        <p id="booking_date_help" class="resident-booking-edit-help">Past dates and elapsed times are not available.</p>
                        @error('booking_date')
                            <p id="booking_date_error" class="app-field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label id="booking_time_label" class="resident-booking-edit-label">Select Time Slot</label>
                        <input type="hidden" name="booking_time" id="booking_time" value="{{ old('booking_time', $selectedTime) }}" aria-describedby="booking_time_help booking_time_error">
                        <p id="slot-status" class="resident-booking-slot-status" role="status" aria-live="polite">Checking live availability...</p>
                        <div id="slot-grid" class="resident-booking-slot-grid" role="group" aria-labelledby="booking_time_label"></div>
                        <p id="booking_time_help" class="resident-booking-edit-help">Choose one available one-hour slot. Full slots and times you already reserved are locked automatically.</p>
                        @error('booking_time')
                            <p id="booking_time_error" class="app-field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="group_members" class="resident-booking-edit-label">Roommates / Group Members <span>(Optional)</span></label>
                        <input type="text" name="group_members" id="group_members" value="{{ old('group_members', $booking->group_members) }}" class="resident-booking-edit-input" placeholder="Example: Ana Cruz, Room 204">
                        <p class="resident-booking-edit-help">Add names or room numbers so your group details stay with the reservation.</p>
                    </div>

                    <div class="resident-booking-edit-form-actions">
                        <button type="submit" class="resident-booking-edit-btn resident-booking-edit-btn-primary" data-booking-submit disabled>Save Changes</button>
                        <a href="{{ route('bookings.index') }}" class="resident-booking-edit-btn resident-booking-edit-btn-secondary">Cancel</a>
                    </div>
                </form>
            </section>

        </div>
    </div>

    <script>
        const bookingDateInput = document.getElementById('booking_date');
        const facilityInput = document.getElementById('facility_name');
        const slotGrid = document.getElementById('slot-grid');
        const bookingTimeInput = document.getElementById('booking_time');
        const slotStatus = document.getElementById('slot-status');
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
                const url = `{{ route('bookings.reserved-slots') }}?facility_name=${encodeURIComponent(facility)}&booking_date=${encodeURIComponent(date)}&exclude_booking_id={{ $booking->id }}`;
                const response = await fetch(url, { headers: { 'Accept': 'application/json' } });
                if (!response.ok) {
                    throw new Error('Availability request failed');
                }
                const data = await response.json();
                renderSlots(data.available_slots || [], data.full_slots || [], data.resident_reserved_slots || [], data.slot_capacity || {});
            } catch (error) {
                bookingTimeInput.value = '';
                clearSlots('Availability could not be loaded. Please try again.', 'error');
                window.appToast?.('error', 'Availability could not be loaded. Please check your connection and try again.');
                console.error('Failed to load slots:', error);
            }
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
            setSlotStatus(`${formatTime(slot)} selected. You can save your changes.`, 'selected');
            syncSubmitButton();
        }

        function refreshSlots() {
            bookingTimeInput.value = '';
            syncSubmitButton();
            loadSlots();
        }

        facilityInput.addEventListener('change', refreshSlots);
        bookingDateInput.addEventListener('change', refreshSlots);

        if (facilityInput.value && bookingDateInput.value) {
            loadSlots();
        } else {
            syncSubmitButton();
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
.resident-booking-edit-topbar {
    display: flex;
    align-items: center;
    justify-content: flex-start;
}
.resident-booking-edit-hero, .resident-booking-edit-panel {
    border: 1px solid rgba(214, 168, 91, 0.14);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.14);
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
.resident-booking-edit-copy {
    max-width: 860px;
}
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
    color: rgba(255, 255, 255, 0.82);
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
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.07);
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
    justify-content: flex-end;
    gap: 12px;
}
.resident-booking-edit-grid {
    display: grid;
    grid-template-columns: minmax(0, 1fr);
    gap: 24px;
    align-items: start;
}
.resident-booking-edit-panel {
    padding: 26px 28px;
    border-radius: 20px;
    background: rgba(42, 44, 48, 0.78);
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
    background: linear-gradient(to right, rgba(214, 168, 91, 0.3), rgba(214, 168, 91, 0.05), transparent);
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
    border: 1px solid rgba(214, 168, 91, 0.14);
    border-radius: 16px;
    font-size: 15px;
    color: #F8F3EA;
    background: rgba(37, 39, 42, 0.90);
    outline: none;
    transition: all 0.2s ease;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.02);
    font-family: inherit;
}
.resident-booking-edit-input:focus {
    border-color: rgba(214, 168, 91, 0.38);
    box-shadow: 0 0 0 4px rgba(214, 168, 91, 0.08);
}
.resident-booking-edit-input[type="date"] {
    color-scheme: dark;
}
.resident-booking-edit-input[type="date"]::-webkit-calendar-picker-indicator {
    cursor: pointer;
    opacity: 1;
    width: 18px;
    height: 18px;
    padding: 5px;
    border-radius: 10px;
    background-color: rgba(214, 168, 91, 0.18);
    filter: invert(91%) sepia(43%) saturate(807%) hue-rotate(343deg) brightness(112%) contrast(101%) drop-shadow(0 0 4px rgba(214, 168, 91, 0.55));
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
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.05);
    text-align: left;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.03);
}
.resident-booking-slot-time {
    font-weight: 700;
    font-size: 15px;
}
.resident-booking-slot-state {
    font-size: 12px;
    margin-top: 4px;
}
.resident-booking-slot-capacity {
    min-height: 18px;
    margin-top: 4px;
    color: #8A7A66;
    font-size: 0.76rem;
}
.resident-booking-slot.is-reserved {
    border-color: rgba(255, 255, 255, 0.05);
    background: rgba(255, 255, 255, 0.04);
    color: #8A7A66;
    cursor: not-allowed;
}
.resident-booking-slot.is-available {
    border-color: rgba(120, 170, 120, 0.25);
    background: rgba(120, 170, 120, 0.10);
    color: #98c48b;
    cursor: pointer;
}
.resident-booking-slot.is-selected {
    border: 1px solid rgba(214, 168, 91, 0.78);
    background: linear-gradient(135deg, rgba(214, 168, 91, 0.26), rgba(184, 132, 47, 0.16));
    color: #FFF2D3;
    cursor: pointer;
    box-shadow: 0 0 0 3px rgba(214, 168, 91, 0.12), 0 14px 28px rgba(0, 0, 0, 0.22);
}
.resident-booking-slot.is-selected .resident-booking-slot-state {
    color: #F2D49A;
    font-weight: 800;
}
.resident-booking-edit-form-actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    padding-top: 8px;
    border-top: 1px solid rgba(214, 168, 91, 0.10);
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
.resident-booking-edit-btn:hover {
    transform: translateY(-1px);
}
.resident-booking-edit-btn-primary {
    background: linear-gradient(95deg, #b8842f, #d6a85b);
    color: #17120d;
}
.resident-booking-edit-btn-primary:disabled {
    cursor: not-allowed;
    opacity: 0.52;
    transform: none;
}
.resident-booking-edit-btn-secondary {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(214, 168, 91, 0.22);
    color: #F0E9DF;
}
.resident-booking-edit-note-list, .resident-booking-edit-meta-list {
    display: grid;
    gap: 12px;
}
.resident-booking-edit-note-item, .resident-booking-edit-meta-item {
    padding: 14px 16px;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.05);
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
@media (max-width:1024px) {
    .resident-booking-edit-grid {
        grid-template-columns: 1fr;
    }
}
@media (max-width:768px) {
    .resident-booking-edit-page {
        padding: 18px 0 28px;
    }
    .resident-booking-edit-hero, .resident-booking-edit-panel {
        padding: 22px;
    }
    .resident-booking-edit-hero {
        flex-direction: column;
        align-items: flex-start;
    }
    .resident-booking-slot-grid {
        grid-template-columns: 1fr;
    }
}
@media (max-width:560px) {
    .resident-booking-edit-btn {
        width: 100%;
    }
    .resident-booking-edit-form-actions {
        flex-direction: column;
    }
}
</style>
</x-app-layout>
