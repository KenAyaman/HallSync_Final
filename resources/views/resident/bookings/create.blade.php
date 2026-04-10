<x-app-layout>
    <div class="space-y-8">

        {{-- PAGE HEADER --}}
        <div class="relative overflow-hidden rounded-[36px] border border-[#3A342D]"
             style="
                background:
                    linear-gradient(115deg,
                        #1F2023 0%,
                        #24262B 38%,
                        #2C2C2F 62%,
                        #3B3023 100%);
                box-shadow: 0 18px 50px rgba(0, 0, 0, 0.18);
             ">

            <div class="absolute top-[-90px] right-[10%] w-[320px] h-[320px] rounded-full blur-3xl opacity-20"
                 style="background: rgba(199, 151, 69, 0.35);"></div>

            <div class="absolute bottom-[-120px] left-[18%] w-[260px] h-[260px] rounded-full blur-3xl opacity-10"
                 style="background: rgba(255,255,255,0.18);"></div>

            <div class="relative z-10 px-8 py-10 md:px-14 md:py-12">
                <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                    <div class="max-w-2xl">
                        <div class="mb-3">
                            <span class="inline-block text-[11px] tracking-[0.30em] uppercase"
                                  style="color: #D2A04C; font-weight: 700;">
                                Reserve Shared Facilities
                            </span>
                        </div>

                        <h1 class="text-4xl md:text-5xl font-bold leading-[1.05] mb-4"
                            style="font-family: 'Playfair Display', serif; color: #F8F3EA;">
                            Book a<br>
                            <span style="color: #F3E5CF;">Facility</span>
                        </h1>

                        <p class="text-base md:text-lg leading-relaxed max-w-xl"
                           style="color: rgba(255,255,255,0.82);">
                            Pick a facility, choose a valid date, and reserve one available time slot.
                        </p>
                    </div>

                    <div class="shrink-0">
                        <a href="{{ route('bookings.index') }}"
                           style="
                                background: rgba(255,255,255,0.05);
                                border: 1px solid rgba(214,168,91,0.28);
                                color: #F2DEC0;
                                padding: 13px 26px;
                                border-radius: 999px;
                                font-weight: 600;
                                text-decoration: none;
                                backdrop-filter: blur(8px);
                                transition: all 0.3s ease;
                                display: inline-block;
                           "
                           onmouseover="this.style.transform='translateY(-2px)'; this.style.background='rgba(255,255,255,0.09)';"
                           onmouseout="this.style.transform='translateY(0)'; this.style.background='rgba(255,255,255,0.05)';">
                            ← Back to My Bookings
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- ERROR LIST --}}
        @if($errors->any())
            <div style="
                background: linear-gradient(180deg, #FFF6F4 0%, #FDF0ED 100%);
                border: 1px solid #C98B7F;
                border-radius: 24px;
                padding: 18px 22px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.04);
            ">
                <div style="font-weight: 700; color: #8F4538; margin-bottom: 10px;">
                    Please fix the following errors:
                </div>
                <ul style="margin-left: 18px; color: #8F4538; line-height: 1.8;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid lg:grid-cols-[1.2fr_0.8fr] gap-8">

            {{-- BOOKING FORM --}}
            <div style="
                background: linear-gradient(180deg, #FFFFFF 0%, #FDFBF8 100%);
                border-radius: 32px;
                padding: 28px 32px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.04);
                border: 1px solid #3A342D;
            ">
                <div style="display:flex; justify-content:space-between; align-items:center; gap:16px; margin-bottom: 20px; flex-wrap: wrap;">
                    <h2 style="
                        font-size: 24px;
                        font-weight: 600;
                        color: #2F2A27;
                        font-family: 'Playfair Display', serif;
                        margin: 0;
                    ">
                        Booking Request
                    </h2>

                    <span style="
                        font-size: 12px;
                        letter-spacing: 0.18em;
                        text-transform: uppercase;
                        color: #BE9360;
                        font-weight: 700;
                    ">
                        Time Slot Picker
                    </span>
                </div>

                <div style="height:1px; background: linear-gradient(to right, #E8D9C5, #F3ECE2, transparent); margin-bottom: 24px;"></div>

                <form method="POST" action="{{ route('bookings.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="facility_name" style="
                            display:block;
                            font-weight: 700;
                            margin-bottom: 10px;
                            color: #2F2A27;
                            font-size: 14px;
                        ">
                            Facility Name
                            <span style="color:#B96A5D;">*</span>
                        </label>

                        <select name="facility_name" id="facility_name" required
                                style="
                                    width:100%;
                                    padding: 14px 16px;
                                    border: 1px solid #D9CCBA;
                                    border-radius: 16px;
                                    font-size: 15px;
                                    color: #2F2A27;
                                    background: #FFFEFC;
                                    outline: none;
                                ">
                            <option value="">Select a facility</option>
                            <option value="Study Room 1" {{ old('facility_name') == 'Study Room 1' ? 'selected' : '' }}>Study Room 1</option>
                            <option value="Study Room 2" {{ old('facility_name') == 'Study Room 2' ? 'selected' : '' }}>Study Room 2</option>
                            <option value="Conference Room" {{ old('facility_name') == 'Conference Room' ? 'selected' : '' }}>Conference Room</option>
                            <option value="Gym" {{ old('facility_name') == 'Gym' ? 'selected' : '' }}>Gym</option>
                            <option value="Game Room" {{ old('facility_name') == 'Game Room' ? 'selected' : '' }}>Game Room</option>
                            <option value="Laundry Room" {{ old('facility_name') == 'Laundry Room' ? 'selected' : '' }}>Laundry Room</option>
                        </select>

                        <p style="font-size: 12px; color: #9A8D7B; margin-top: 8px;">
                            Select the facility you want to reserve.
                        </p>
                    </div>

                    <div>
                        <label for="booking_date" style="
                            display:block;
                            font-weight: 700;
                            margin-bottom: 10px;
                            color: #2F2A27;
                            font-size: 14px;
                        ">
                            Booking Date
                            <span style="color:#B96A5D;">*</span>
                        </label>

                        <input type="date"
                               name="booking_date"
                               id="booking_date"
                               value="{{ old('booking_date') }}"
                               required
                               style="
                                    width:100%;
                                    padding: 14px 16px;
                                    border: 1px solid #D9CCBA;
                                    border-radius: 16px;
                                    font-size: 15px;
                                    color: #2F2A27;
                                    background: #FFFEFC;
                                    outline: none;
                               ">

                        <p style="font-size: 12px; color: #9A8D7B; margin-top: 8px;">
                            Past dates are not allowed.
                        </p>
                    </div>

                    <div>
                        <label style="
                            display:block;
                            font-weight: 700;
                            margin-bottom: 12px;
                            color: #2F2A27;
                            font-size: 14px;
                        ">
                            Select Time Slot
                            <span style="color:#B96A5D;">*</span>
                        </label>

                        <input type="hidden" name="booking_time" id="booking_time" value="{{ old('booking_time') }}">

                        <div id="slot-grid" class="grid grid-cols-2 md:grid-cols-3 gap-3"></div>

                        <p style="font-size: 12px; color: #9A8D7B; margin-top: 10px;">
                            Choose one available time slot.
                        </p>
                    </div>

                    <div>
                        <label for="notes" style="
                            display:block;
                            font-weight: 700;
                            margin-bottom: 10px;
                            color: #2F2A27;
                            font-size: 14px;
                        ">
                            Notes <span style="font-weight:500; color:#9A8D7B;">(Optional)</span>
                        </label>

                        <textarea name="notes" id="notes" rows="4"
                                  placeholder="Any special requests or additional information?"
                                  style="
                                        width:100%;
                                        padding: 14px 16px;
                                        border: 1px solid #D9CCBA;
                                        border-radius: 16px;
                                        font-size: 15px;
                                        color: #2F2A27;
                                        background: #FFFEFC;
                                        outline: none;
                                        resize: vertical;
                                  ">{{ old('notes') }}</textarea>
                    </div>

                    <div style="
                        padding-top: 8px;
                        border-top: 1px solid #EFE6DA;
                    ">
                        <button type="submit"
                                style="
                                    background: linear-gradient(90deg, #B8842F 0%, #D6A85B 100%);
                                    color: #FFFFFF;
                                    padding: 14px 28px;
                                    border-radius: 999px;
                                    font-weight: 700;
                                    border: none;
                                    cursor: pointer;
                                    box-shadow: 0 10px 24px rgba(199, 150, 69, 0.28);
                                    transition: all 0.3s ease;
                                    font-size: 14px;
                                "
                                onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 16px 30px rgba(199,150,69,0.34)';"
                                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 24px rgba(199,150,69,0.28)';">
                            Submit Booking Request
                        </button>
                    </div>
                </form>
            </div>

            {{-- BOOKING GUIDE --}}
            <div class="space-y-6">
                <div style="
                    background: linear-gradient(180deg, #FFFFFF 0%, #FDFBF8 100%);
                    border-radius: 32px;
                    padding: 28px 28px;
                    box-shadow: 0 10px 30px rgba(0,0,0,0.04);
                    border: 1px solid #3A342D;
                ">
                    <h2 style="
                        font-size: 24px;
                        font-weight: 600;
                        color: #2F2A27;
                        font-family: 'Playfair Display', serif;
                        margin-bottom: 16px;
                    ">
                        Booking Rules
                    </h2>

                    <div style="height:1px; background: linear-gradient(to right, #E8D9C5, #F3ECE2, transparent); margin-bottom: 18px;"></div>

                    <div style="display:grid; gap:14px;">
                        <div style="display:flex; gap:12px; align-items:flex-start;">
                            <span style="font-size:18px;">📅</span>
                            <div style="color:#6E665C; font-size:14px; line-height:1.6;">
                                Only <strong style="color:#8A6A3C;">today and future dates</strong> are allowed.
                            </div>
                        </div>

                        <div style="display:flex; gap:12px; align-items:flex-start;">
                            <span style="font-size:18px;">⏰</span>
                            <div style="color:#6E665C; font-size:14px; line-height:1.6;">
                                Each request is limited to <strong style="color:#8A6A3C;">one time slot</strong>.
                            </div>
                        </div>

                        <div style="display:flex; gap:12px; align-items:flex-start;">
                            <span style="font-size:18px;">🔒</span>
                            <div style="color:#6E665C; font-size:14px; line-height:1.6;">
                                Reserved slots cannot be selected again.
                            </div>
                        </div>
                    </div>
                </div>

                <div style="
                    background: linear-gradient(180deg, #FFFFFF 0%, #FDFBF8 100%);
                    border-radius: 32px;
                    padding: 28px 28px;
                    box-shadow: 0 10px 30px rgba(0,0,0,0.04);
                    border: 1px solid #3A342D;
                ">
                    <h2 style="
                        font-size: 24px;
                        font-weight: 600;
                        color: #2F2A27;
                        font-family: 'Playfair Display', serif;
                        margin-bottom: 16px;
                    ">
                        Time Slot Guide
                    </h2>

                    <div style="height:1px; background: linear-gradient(to right, #E8D9C5, #F3ECE2, transparent); margin-bottom: 18px;"></div>

                    <div style="color:#6E665C; font-size:14px; line-height:1.8;">
                        <div>• Green means available.</div>
                        <div>• Gray means reserved.</div>
                        <div>• Gold means your selected slot.</div>
                        <div>• Pick a date first to load the time slots.</div>
                    </div>
                </div>
            </div>
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
        bookingTimeInput.value = '';
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
        bookingTimeInput.value = '';

        timeSlots.forEach(slot => {
            const isReserved = reservedSlots.includes(slot);

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.dataset.slot = slot;
            btn.dataset.available = isReserved ? 'false' : 'true';
            btn.innerHTML = `
                <div style="font-weight:700; font-size:15px;">${formatTime(slot)}</div>
                <div style="font-size:12px; margin-top:4px;">${isReserved ? 'Reserved' : 'Available'}</div>
            `;

            btn.style.width = '100%';
            btn.style.padding = '14px 12px';
            btn.style.borderRadius = '18px';
            btn.style.transition = 'all 0.2s ease';

            if (isReserved) {
                btn.style.border = '1px solid #D8D1C7';
                btn.style.background = '#F3F0EB';
                btn.style.color = '#9A8D7B';
                btn.style.cursor = 'not-allowed';
            } else {
                btn.style.border = '1px solid #BFD9C3';
                btn.style.background = '#EEF8F0';
                btn.style.color = '#3E7A4A';
                btn.style.cursor = 'pointer';

                btn.addEventListener('click', function () {
                    document.querySelectorAll('#slot-grid button').forEach(other => {
                        if (other.dataset.available === 'true') {
                            other.style.background = '#EEF8F0';
                            other.style.border = '1px solid #BFD9C3';
                            other.style.color = '#3E7A4A';
                        }
                    });

                    btn.style.background = '#F7E7C9';
                    btn.style.border = '1px solid #D6A85B';
                    btn.style.color = '#8A6A3C';
                    bookingTimeInput.value = slot;
                });
            }

            slotGrid.appendChild(btn);
        });

        if (bookingTimeInput.value) {
            const selected = document.querySelector(`#slot-grid button[data-slot="${bookingTimeInput.value}"][data-available="true"]`);
            if (selected) selected.click();
        }
    }

    facilityInput.addEventListener('change', loadSlots);
    bookingDateInput.addEventListener('change', loadSlots);

    if (facilityInput.value && bookingDateInput.value) {
        loadSlots();
    }
</script>
</x-app-layout>