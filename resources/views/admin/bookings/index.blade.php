<x-app-layout>
<div class="space-y-8 admin-shell admin-booking-requests-page">

    {{-- PREMIUM ADMIN HEADER --}}
    <div class="relative overflow-hidden rounded-[36px] border border-[#3A342D]"
         style="background: linear-gradient(115deg, #1A1C1E 0%, #1F2023 38%, #24262B 62%, #2C2C2F 100%); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);">

        <div class="absolute top-[-90px] right-[10%] w-[320px] h-[320px] rounded-full blur-3xl opacity-20"
             style="background: rgba(199, 151, 69, 0.3);"></div>

        <div class="absolute bottom-[-120px] left-[18%] w-[260px] h-[260px] rounded-full blur-3xl opacity-10"
             style="background: rgba(255,255,255,0.08);"></div>

        <div class="relative z-10 px-8 py-10 md:px-14 md:py-12">
            <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                <div class="max-w-2xl">
                    <div class="mb-3 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center" style="background: linear-gradient(135deg, #D6A85B, #B8842F);">
                            <svg class="w-4 h-4" fill="none" stroke="white" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <span class="inline-block text-[11px] tracking-[0.30em] uppercase"
                              style="color: #D6A85B; font-weight: 700;">
                            Admin Reservation Hub
                        </span>
                    </div>

                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-[1.05] mb-4"
                        style="font-family: 'Playfair Display', serif; color: #F8F3EA;">
                        Facility<br>
                        <span style="color: #D6A85B;">Reservation Center</span>
                    </h1>

                    <p class="text-base md:text-lg leading-relaxed max-w-xl"
                       style="color: rgba(255,255,255,0.7);">
                        Review booking requests, approve legitimate reservations, 
                        manage facility conflicts, and optimize space utilization.
                    </p>
                </div>

                <div class="shrink-0 flex items-center gap-3 px-4 py-2 rounded-full"
                     style="background: rgba(214,168,91,0.1); border: 1px solid rgba(214,168,91,0.2);">
                    <span class="text-xs font-mono" style="color: #D6A85B;">👑 Administrator Access</span>
                </div>
            </div>
        </div>
    </div>

    {{-- STATS CARDS --}}
    @php
        $total = $bookings->count();
        $pending = $bookings->where('status', 'pending')->count();
        $approved = $bookings->where('status', 'approved')->count();
        $rejected = $bookings->where('status', 'rejected')->count();
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 admin-feature-stat-grid">
        <div class="group relative overflow-hidden rounded-[22px] transition-all duration-300"
             style="background: linear-gradient(180deg, #25272C 0%, #1F2023 100%); border: 1px solid rgba(214,168,91,0.14); box-shadow: 0 14px 36px rgba(0,0,0,0.22);">
            <div class="relative p-5">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center" style="background: rgba(214,168,91,0.12);">
                        <svg class="w-6 h-6" style="color: #D6A85B;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <span class="text-[34px] font-bold" style="color: #D6A85B; line-height: 1;">{{ $total }}</span>
                </div>
                <div class="text-[11px] font-semibold uppercase tracking-[0.16em]" style="color: #8A7A66;">Total Bookings</div>
                <div class="text-xs mt-1" style="color: #918473;">All recorded requests</div>
            </div>
        </div>

        <div class="group relative overflow-hidden rounded-[22px] transition-all duration-300"
             style="background: linear-gradient(180deg, #25272C 0%, #1F2023 100%); border: 1px solid rgba(224,112,96,0.12); box-shadow: 0 14px 36px rgba(0,0,0,0.22);">
            <div class="relative p-5">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center" style="background: rgba(224,112,96,0.12);">
                        <svg class="w-6 h-6" style="color: #E07060;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="text-[34px] font-bold" style="color: #F0B3A9; line-height: 1;">{{ $pending }}</span>
                </div>
                <div class="text-[11px] font-semibold uppercase tracking-[0.16em]" style="color: #8A7A66;">Pending Review</div>
                <div class="text-xs mt-1" style="color: #918473;">Awaiting decision</div>
            </div>
        </div>

        <div class="group relative overflow-hidden rounded-[22px] transition-all duration-300"
             style="background: linear-gradient(180deg, #25272C 0%, #1F2023 100%); border: 1px solid rgba(90,138,90,0.12); box-shadow: 0 14px 36px rgba(0,0,0,0.22);">
            <div class="relative p-5">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center" style="background: rgba(90,138,90,0.12);">
                        <svg class="w-6 h-6" style="color: #5A8A5A;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="text-[34px] font-bold" style="color: #A8CAA8; line-height: 1;">{{ $approved }}</span>
                </div>
                <div class="text-[11px] font-semibold uppercase tracking-[0.16em]" style="color: #8A7A66;">Approved</div>
                <div class="text-xs mt-1" style="color: #918473;">Confirmed reservations</div>
            </div>
        </div>

        <div class="group relative overflow-hidden rounded-[22px] transition-all duration-300"
             style="background: linear-gradient(180deg, #25272C 0%, #1F2023 100%); border: 1px solid rgba(190,147,96,0.12); box-shadow: 0 14px 36px rgba(0,0,0,0.22);">
            <div class="relative p-5">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center" style="background: rgba(190,147,96,0.12);">
                        <svg class="w-6 h-6" style="color: #BE9360;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m0 0A9.001 9.001 0 006.972 5.649l4.728-4.728 4.728 4.728A9.001 9.001 0 0018.364 5.636m0 0c-.317.301-.533.677-.533 1.118V15.5a2.25 2.25 0 01-2.25 2.25H9.25a2.25 2.25 0 01-2.25-2.25v-1.722c0-.441.217-.817.533-1.118z"></path>
                        </svg>
                    </div>
                    <span class="text-[34px] font-bold" style="color: #D7B48D; line-height: 1;">{{ $rejected }}</span>
                </div>
                <div class="text-[11px] font-semibold uppercase tracking-[0.16em]" style="color: #8A7A66;">Rejected</div>
                <div class="text-xs mt-1" style="color: #918473;">Declined requests</div>
            </div>
        </div>
    </div>

    {{-- TABS FOR DIFFERENT STATUSES --}}
    <div class="flex flex-wrap gap-2 border-b pb-3" style="border-color: rgba(214,168,91,0.2);">
        <button onclick="filterByStatus('all')" class="filter-tab active px-5 py-2 rounded-full text-sm font-medium transition-all duration-200" style="background: rgba(214,168,91,0.2); color: #D6A85B;">
            All ({{ $total }})
        </button>
        <button onclick="filterByStatus('pending')" class="filter-tab px-5 py-2 rounded-full text-sm font-medium transition-all duration-200" style="color: #B0A898;">
            Pending ({{ $pending }})
        </button>
        <button onclick="filterByStatus('approved')" class="filter-tab px-5 py-2 rounded-full text-sm font-medium transition-all duration-200" style="color: #B0A898;">
            Approved ({{ $approved }})
        </button>
        <button onclick="filterByStatus('rejected')" class="filter-tab px-5 py-2 rounded-full text-sm font-medium transition-all duration-200" style="color: #B0A898;">
            Rejected ({{ $rejected }})
        </button>
    </div>

    {{-- PENDING BOOKINGS SECTION --}}
    <div id="pending-section" class="booking-section">
        <div style="
            background: linear-gradient(180deg, #2A2C30 0%, #1F2023 100%);
            border-radius: 32px;
            padding: 28px 32px;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.4);
            border: 1px solid #3A342D;
        ">
            <div style="display:flex; justify-content:space-between; align-items:center; gap:16px; margin-bottom: 24px; flex-wrap: wrap;">
                <div>
                    <h2 style="
                        font-size: 24px;
                        font-weight: 600;
                        color: #F8F3EA;
                        font-family: 'Playfair Display', serif;
                        margin: 0;
                    ">
                        Pending Approvals
                    </h2>
                    <p style="font-size: 12px; color: #8A7A66; margin-top: 4px;">Review and process resident facility requests</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs px-3 py-1 rounded-full" style="background: rgba(224,112,96,0.15); color: #E07060;">
                        {{ $pending }} awaiting action
                    </span>
                </div>
            </div>

            <div style="height:1px; background: linear-gradient(to right, rgba(214,168,91,0.3), rgba(214,168,91,0.05), transparent); margin-bottom: 24px;"></div>

            @forelse($bookings->where('status', 'pending') as $booking)
                <div class="booking-item" data-status="{{ $booking->status }}"
                     style="
                        background: linear-gradient(135deg, #2C2C2F 0%, #25272A 100%);
                        border: 1px solid rgba(58,52,45,0.6);
                        border-radius: 20px;
                        padding: 24px 28px;
                        margin-bottom: 16px;
                        transition: all 0.3s ease;
                     "
                     onmouseover="this.style.transform='translateX(4px)'; this.style.borderColor='rgba(214,168,91,0.4)'; this.style.boxShadow='0 12px 32px rgba(0,0,0,0.4)';"
                     onmouseout="this.style.transform='translateX(0)'; this.style.borderColor='rgba(58,52,45,0.6)'; this.style.boxShadow='none';">

                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-4 mb-3">
                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl" style="background: rgba(214,168,91,0.12);">
                                    🏢
                                </div>
                                <div>
                                    <h3 style="font-size: 18px; font-weight: 700; color: #F8F3EA; margin: 0;">
                                        {{ $booking->facility_name }}
                                    </h3>
                                    <div class="flex flex-wrap items-center gap-3 mt-1">
                                        <span class="text-xs px-2 py-1 rounded-full" style="background: rgba(214,168,91,0.15); color: #D6A85B;">
                                            {{ $booking->booking_date->format('l, F d, Y') }}
                                        </span>
                                        <span class="text-xs px-2 py-1 rounded-full" style="background: rgba(90,138,90,0.15); color: #5A8A5A;">
                                            {{ $booking->booking_date->format('h:i A') }} - {{ $booking->end_time->format('h:i A') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-wrap items-center gap-4 text-sm mb-3" style="color: #B0A898;">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    {{ $booking->user->name ?? 'Resident' }}
                                </div>
                                <div class="w-px h-3" style="background: #5A4A3A;"></div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                                    </svg>
                                    #{{ $booking->id }}
                                </div>
                                <div class="w-px h-3" style="background: #5A4A3A;"></div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    {{ $booking->created_at->diffForHumans() }}
                                </div>
                            </div>

                            @if($booking->notes)
                                <div class="text-sm italic" style="color: #8A7A66;">
                                    "{{ Str::limit($booking->notes, 100) }}"
                                </div>
                            @endif
                        </div>

                        <div class="flex gap-3 flex-shrink-0">
                            <form action="{{ route('bookings.approve', $booking) }}" method="POST" class="inline-block">
                                @csrf
                                <button type="submit" class="px-6 py-3 rounded-xl font-bold flex items-center gap-2 transition-all duration-200"
                                    style="background: linear-gradient(135deg, #5A8A5A, #6DA76D); color: white; border: none; cursor: pointer;"
                                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 20px rgba(90,138,90,0.4)'"
                                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Approve
                                </button>
                            </form>
                            <form action="{{ route('bookings.reject', $booking) }}" method="POST" class="inline-block" onsubmit="return confirm('Reject this booking request?')">
                                @csrf
                                <button type="submit" class="px-6 py-3 rounded-xl font-bold flex items-center gap-2 transition-all duration-200"
                                    style="background: linear-gradient(135deg, #E07060, #D95B4F); color: white; border: none; cursor: pointer;"
                                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 20px rgba(224,112,96,0.4)'"
                                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Reject
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-16">
                    <div class="w-20 h-20 rounded-full mx-auto mb-5 flex items-center justify-center" style="background: rgba(214,168,91,0.1);">
                        <span class="text-4xl">✅</span>
                    </div>
                    <h3 style="font-size: 22px; font-weight: 600; color: #F8F3EA; margin-bottom: 8px;">No pending approvals</h3>
                    <p style="color: #8A7A66;">All booking requests have been processed.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- APPROVED BOOKINGS SECTION (Hidden by default) --}}
    <div id="approved-section" class="booking-section" style="display: none;">
        <div style="
            background: linear-gradient(180deg, #2A2C30 0%, #1F2023 100%);
            border-radius: 32px;
            padding: 28px 32px;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.4);
            border: 1px solid #3A342D;
        ">
            <div style="display:flex; justify-content:space-between; align-items:center; gap:16px; margin-bottom: 24px;">
                <h2 style="font-size: 24px; font-weight: 600; color: #F8F3EA; font-family: 'Playfair Display', serif; margin: 0;">
                    Approved Bookings
                </h2>
                <span class="text-xs px-3 py-1 rounded-full" style="background: rgba(90,138,90,0.15); color: #5A8A5A;">
                    {{ $approved }} confirmed
                </span>
            </div>

            <div style="height:1px; background: linear-gradient(to right, rgba(214,168,91,0.3), rgba(214,168,91,0.05), transparent); margin-bottom: 24px;"></div>

            @forelse($bookings->where('status', 'approved') as $booking)
                <div class="approved-item" style="
                    background: linear-gradient(135deg, #2C2C2F 0%, #25272A 100%);
                    border: 1px solid rgba(90,138,90,0.3);
                    border-radius: 20px;
                    padding: 20px 24px;
                    margin-bottom: 12px;
                    transition: all 0.3s ease;
                "
                onmouseover="this.style.transform='translateX(4px)'; this.style.borderColor='rgba(90,138,90,0.5)';">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <span class="text-lg">✅</span>
                                <h3 style="font-size: 16px; font-weight: 700; color: #F8F3EA;">{{ $booking->facility_name }}</h3>
                                <span class="text-xs px-2 py-1 rounded-full" style="background: rgba(90,138,90,0.2); color: #5A8A5A;">Approved</span>
                            </div>
                            <div class="text-sm" style="color: #B0A898;">
                                {{ $booking->booking_date->format('l, F d, Y') }} • {{ $booking->booking_date->format('h:i A') }} - {{ $booking->end_time->format('h:i A') }}
                            </div>
                            <div class="text-xs mt-1" style="color: #8A7A66;">by {{ $booking->user->name ?? 'Resident' }}</div>
                        </div>
                        <a href="{{ route('bookings.show', $booking) }}" class="text-sm" style="color: #D6A85B; text-decoration: none;">View Details →</a>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <p style="color: #8A7A66;">No approved bookings yet.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- REJECTED BOOKINGS SECTION (Hidden by default) --}}
    <div id="rejected-section" class="booking-section" style="display: none;">
        <div style="
            background: linear-gradient(180deg, #2A2C30 0%, #1F2023 100%);
            border-radius: 32px;
            padding: 28px 32px;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.4);
            border: 1px solid #3A342D;
        ">
            <div style="display:flex; justify-content:space-between; align-items:center; gap:16px; margin-bottom: 24px;">
                <h2 style="font-size: 24px; font-weight: 600; color: #F8F3EA; font-family: 'Playfair Display', serif; margin: 0;">
                    Rejected Bookings
                </h2>
                <span class="text-xs px-3 py-1 rounded-full" style="background: rgba(224,112,96,0.15); color: #E07060;">
                    {{ $rejected }} declined
                </span>
            </div>

            <div style="height:1px; background: linear-gradient(to right, rgba(214,168,91,0.3), rgba(214,168,91,0.05), transparent); margin-bottom: 24px;"></div>

            @forelse($bookings->where('status', 'rejected') as $booking)
                <div class="rejected-item" style="
                    background: linear-gradient(135deg, #2C2C2F 0%, #25272A 100%);
                    border: 1px solid rgba(224,112,96,0.3);
                    border-radius: 20px;
                    padding: 20px 24px;
                    margin-bottom: 12px;
                    transition: all 0.3s ease;
                ">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <span class="text-lg">❌</span>
                                <h3 style="font-size: 16px; font-weight: 700; color: #F8F3EA;">{{ $booking->facility_name }}</h3>
                                <span class="text-xs px-2 py-1 rounded-full" style="background: rgba(224,112,96,0.2); color: #E07060;">Rejected</span>
                            </div>
                            <div class="text-sm" style="color: #B0A898;">
                                {{ $booking->booking_date->format('l, F d, Y') }} • {{ $booking->booking_date->format('h:i A') }}
                            </div>
                            <div class="text-xs mt-1" style="color: #8A7A66;">by {{ $booking->user->name ?? 'Resident' }}</div>
                        </div>
                        <a href="{{ route('bookings.show', $booking) }}" class="text-sm" style="color: #D6A85B; text-decoration: none;">View Details →</a>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <p style="color: #8A7A66;">No rejected bookings.</p>
                </div>
            @endforelse
        </div>
    </div>

</div>

<script>
function filterByStatus(status) {
    // Update active tab styling
    document.querySelectorAll('.filter-tab').forEach(tab => {
        tab.style.background = 'transparent';
        tab.style.color = '#B0A898';
    });
    event.target.style.background = 'rgba(214,168,91,0.2)';
    event.target.style.color = '#D6A85B';
    
    // Show/hide sections
    document.getElementById('pending-section').style.display = status === 'all' || status === 'pending' ? 'block' : 'none';
    document.getElementById('approved-section').style.display = status === 'all' || status === 'approved' ? 'block' : 'none';
    document.getElementById('rejected-section').style.display = status === 'all' || status === 'rejected' ? 'block' : 'none';
}
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap');

.admin-shell {
    max-width: 1580px;
    width: 100%;
    margin: 0 auto;
}

.admin-shell > div:first-of-type {
    position: relative !important;
    overflow: hidden !important;
    border-radius: 20px !important;
    background: linear-gradient(120deg, #111009 0%, #1C1A12 50%, #201E14 100%) !important;
    border: 1px solid rgba(214,168,91,0.18) !important;
    box-shadow: none !important;
}

.admin-shell > div:first-of-type::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(rgba(214,168,91,0.04) 1px, transparent 1px),
        linear-gradient(90deg, rgba(214,168,91,0.04) 1px, transparent 1px);
    background-size: 48px 48px;
    pointer-events: none;
}

.admin-shell > div:first-of-type > div.absolute:first-child {
    top: -60px !important;
    right: -40px !important;
    width: 280px !important;
    height: 280px !important;
    background: radial-gradient(circle, rgba(214,168,91,0.15) 0%, transparent 70%) !important;
    opacity: 1 !important;
    filter: none !important;
}

.admin-shell > div:first-of-type > div.absolute:nth-child(2) {
    display: none !important;
}

.admin-shell > div:first-of-type > div.relative {
    padding: 36px 44px !important;
}

.admin-shell > div:first-of-type > div.relative > div {
    align-items: center !important;
}

.admin-shell > div:first-of-type .mb-3.flex.items-center.gap-3 {
    gap: 8px !important;
    margin-bottom: 12px !important;
}

.admin-shell > div:first-of-type .mb-3.flex.items-center.gap-3 div:first-child {
    width: 6px !important;
    height: 6px !important;
    min-width: 6px !important;
    min-height: 6px !important;
    border-radius: 999px !important;
    background: #d6a85b !important;
}

.admin-shell > div:first-of-type .mb-3.flex.items-center.gap-3 div:first-child svg {
    display: none !important;
}

.admin-shell > div:first-of-type .mb-3.flex.items-center.gap-3 span:last-child {
    font-size: 0.875rem !important;
    letter-spacing: 0.18em !important;
    text-transform: uppercase !important;
    color: #d6a85b !important;
    font-weight: 700 !important;
}

.admin-shell > div:first-of-type h1 {
    color: #f0e9df !important;
    font-family: 'Playfair Display', serif !important;
    font-size: clamp(2.5rem, 4vw, 3.5rem) !important;
    font-weight: 700 !important;
    line-height: 1.12 !important;
    margin-bottom: 12px !important;
}

.admin-shell > div:first-of-type p {
    color: rgba(255,255,255,0.62) !important;
    font-size: 1.125rem !important;
    max-width: 760px !important;
}

@media (max-width: 768px) {
    .admin-shell > div:first-of-type > div.relative {
        padding: 24px !important;
    }
}

::-webkit-scrollbar { width: 6px; }
::-webkit-scrollbar-track { background: #2A2C30; border-radius: 10px; }
::-webkit-scrollbar-thumb { background: linear-gradient(#D6A85B, #B8842F); border-radius: 10px; }
::-webkit-scrollbar-thumb:hover { background: linear-gradient(#C49A4A, #A37222); }
</style>
</x-app-layout>
