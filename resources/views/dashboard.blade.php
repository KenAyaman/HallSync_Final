<x-app-layout>
<div class="space-y-8">

    {{-- HERO SECTION (Same for all roles) --}}
    <div class="relative overflow-hidden rounded-[36px] min-h-[440px] border border-[#3A342D]"
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

        <div class="absolute bottom-[-120px] left-[22%] w-[260px] h-[260px] rounded-full blur-3xl opacity-10"
             style="background: rgba(255,255,255,0.18);"></div>

        <div class="absolute inset-0 overflow-hidden rounded-[36px]">
            <img src="{{ asset('1.png') }}"
                 alt="Hero image"
                 class="absolute inset-0 w-full h-full object-cover object-right">

            <div class="absolute inset-0"
                 style="background: linear-gradient(90deg, rgba(31,32,35,1.00) 0%, rgba(31,32,35,0.98) 10%, rgba(33,34,38,0.93) 22%, rgba(36,38,43,0.82) 34%, rgba(38,40,44,0.65) 45%, rgba(42,43,46,0.44) 55%, rgba(46,44,40,0.24) 65%, rgba(52,46,34,0.10) 75%, rgba(59,48,35,0.03) 85%, rgba(59,48,35,0.00) 100%);">
            </div>

            <div class="absolute inset-0"
                 style="background: linear-gradient(90deg, rgba(25,26,28,0.55) 0%, rgba(25,26,28,0.25) 25%, rgba(25,26,28,0.05) 45%, rgba(25,26,28,0.00) 60%);">
            </div>

            <div class="absolute inset-0"
                 style="background: linear-gradient(180deg, rgba(0,0,0,0.08) 0%, rgba(0,0,0,0.20) 100%);"></div>

            <div class="absolute inset-x-0 top-0 h-[18%]"
                 style="background: linear-gradient(180deg, rgba(0,0,0,0.14) 0%, rgba(0,0,0,0.00) 100%);"></div>
        </div>

        <div class="relative z-20 px-8 py-12 md:px-14 md:py-16">
            <div class="max-w-2xl">
                <div class="mb-4">
                    <span class="inline-block text-[11px] tracking-[0.30em] uppercase"
                          style="color: #D2A04C; font-weight: 700;">
                        Refined Student Living
                    </span>
                </div>

                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-[1.02] mb-6"
                    style="font-family: 'Playfair Display', serif; color: #F8F3EA;">
                    Luxury Living,<br>
                    <span style="color: #F3E5CF;">Perfectly Designed</span>
                </h1>

                <p class="text-base md:text-lg leading-relaxed mb-8 max-w-xl"
                   style="color: rgba(255,255,255,0.82);">
                    HallSync is more than just a dormitory management system — it is a
                    carefully designed living experience built around comfort,
                    service, and seamless resident care.
                </p>

                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('tickets.create') }}"
                       style="background: linear-gradient(90deg, #B8842F 0%, #D6A85B 100%); color: #FFFFFF; padding: 13px 30px; border-radius: 999px; font-weight: 700; text-decoration: none; box-shadow: 0 10px 24px rgba(199, 150, 69, 0.28); transition: all 0.3s ease;"
                       onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 16px 30px rgba(199,150,69,0.34)';"
                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 24px rgba(199,150,69,0.28)';">
                        Get Started
                    </a>

                    <a href="{{ route('bookings.create') }}"
                       style="background: rgba(255,255,255,0.05); border: 1px solid rgba(214,168,91,0.28); color: #F2DEC0; padding: 13px 30px; border-radius: 999px; font-weight: 600; text-decoration: none; backdrop-filter: blur(8px); transition: all 0.3s ease;"
                       onmouseover="this.style.transform='translateY(-2px)'; this.style.background='rgba(255,255,255,0.09)';"
                       onmouseout="this.style.transform='translateY(0)'; this.style.background='rgba(255,255,255,0.05)';">
                        Book a Space
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ROLE-BASED CONTENT --}}
    @if(Auth::user()->role === 'manager')
        {{-- MANAGER DASHBOARD STATS --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-5 -mt-10 relative z-20">
            <div class="rounded-[24px] p-6 text-center" style="background: linear-gradient(135deg, #FFFFFF 0%, #F8F4EC 100%); border: 1px solid #3A342D;">
                <div class="text-3xl md:text-4xl font-bold" style="color: #C79745;">{{ $openTickets ?? 0 }}</div>
                <div class="text-sm mt-2" style="color: #8A6A3C;">Open Tickets</div>
                <div class="text-xs mt-1" style="color: #B39A78;">{{ $urgentTickets ?? 0 }} urgent</div>
            </div>
            <div class="rounded-[24px] p-6 text-center" style="background: linear-gradient(135deg, #FFFFFF 0%, #F8F4EC 100%); border: 1px solid #3A342D;">
                <div class="text-3xl md:text-4xl font-bold" style="color: #C79745;">{{ $pendingBookings ?? 0 }}</div>
                <div class="text-sm mt-2" style="color: #8A6A3C;">Pending Bookings</div>
                <div class="text-xs mt-1" style="color: #B39A78;">Awaiting approval</div>
            </div>
            <div class="rounded-[24px] p-6 text-center" style="background: linear-gradient(135deg, #FFFFFF 0%, #F8F4EC 100%); border: 1px solid #3A342D;">
                <div class="text-3xl md:text-4xl font-bold" style="color: #C79745;">{{ $totalResidents ?? 0 }}</div>
                <div class="text-sm mt-2" style="color: #8A6A3C;">Total Residents</div>
                <div class="text-xs mt-1" style="color: #B39A78;">Registered</div>
            </div>
            <div class="rounded-[24px] p-6 text-center" style="background: linear-gradient(135deg, #FFFFFF 0%, #F8F4EC 100%); border: 1px solid #3A342D;">
                <div class="text-3xl md:text-4xl font-bold" style="color: #C79745;">{{ $resolvedThisWeek ?? 0 }}</div>
                <div class="text-sm mt-2" style="color: #8A6A3C;">Resolved This Week</div>
            </div>
        </div>

    @elseif(Auth::user()->role === 'handyman')
        {{-- HANDYMAN DASHBOARD STATS --}}
        <div class="grid grid-cols-2 md:grid-cols-3 gap-5 -mt-10 relative z-20">
            <div class="rounded-[24px] p-6 text-center" style="background: linear-gradient(135deg, #FFFFFF 0%, #F8F4EC 100%); border: 1px solid #3A342D;">
                <div class="text-3xl md:text-4xl font-bold" style="color: #C79745;">{{ $assignedTickets ?? 0 }}</div>
                <div class="text-sm mt-2" style="color: #8A6A3C;">Assigned to You</div>
            </div>
            <div class="rounded-[24px] p-6 text-center" style="background: linear-gradient(135deg, #FFFFFF 0%, #F8F4EC 100%); border: 1px solid #3A342D;">
                <div class="text-3xl md:text-4xl font-bold" style="color: #C79745;">{{ $inProgressTickets ?? 0 }}</div>
                <div class="text-sm mt-2" style="color: #8A6A3C;">In Progress</div>
            </div>
            <div class="rounded-[24px] p-6 text-center" style="background: linear-gradient(135deg, #FFFFFF 0%, #F8F4EC 100%); border: 1px solid #3A342D;">
                <div class="text-3xl md:text-4xl font-bold" style="color: #C79745;">{{ $completedToday ?? 0 }}</div>
                <div class="text-sm mt-2" style="color: #8A6A3C;">Completed Today</div>
            </div>
        </div>

    @else
        {{-- RESIDENT DASHBOARD STATS --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-5 -mt-10 relative z-20">
            <div class="rounded-[24px] p-6 text-center" style="background: linear-gradient(135deg, #FFFFFF 0%, #F8F4EC 100%); border: 1px solid #3A342D;">
                <div class="text-3xl md:text-4xl font-bold" style="color: #C79745;">{{ $activeTickets ?? 0 }}</div>
                <div class="text-sm mt-2" style="color: #8A6A3C;">Active Tickets</div>
                <div class="text-xs mt-1" style="color: #B39A78;">{{ $inProgressTickets ?? 0 }} in progress</div>
            </div>
            <div class="rounded-[24px] p-6 text-center" style="background: linear-gradient(135deg, #FFFFFF 0%, #F8F4EC 100%); border: 1px solid #3A342D;">
                <div class="text-3xl md:text-4xl font-bold" style="color: #C79745;">{{ $pendingBookings ?? 0 }}</div>
                <div class="text-sm mt-2" style="color: #8A6A3C;">Pending Bookings</div>
                <div class="text-xs mt-1" style="color: #B39A78;">Awaiting approval</div>
            </div>
            <div class="rounded-[24px] p-6 text-center" style="background: linear-gradient(135deg, #FFFFFF 0%, #F8F4EC 100%); border: 1px solid #3A342D;">
                <div class="text-3xl md:text-4xl font-bold" style="color: #C79745;">{{ $upcomingBookingsCount ?? 0 }}</div>
                <div class="text-sm mt-2" style="color: #8A6A3C;">Upcoming Bookings</div>
                <div class="text-xs mt-1" style="color: #B39A78;">
                    @if($nextBookingDate ?? false)
                        Next: {{ \Carbon\Carbon::parse($nextBookingDate)->format('M d') }}
                    @else
                        No upcoming
                    @endif
                </div>
            </div>
            <div class="rounded-[24px] p-6 text-center" style="background: linear-gradient(135deg, #FFFFFF 0%, #F8F4EC 100%); border: 1px solid #3A342D;">
                <div class="text-3xl md:text-4xl font-bold" style="color: #C79745;">{{ $myPostsCount ?? 0 }}</div>
                <div class="text-sm mt-2" style="color: #8A6A3C;">My Community Posts</div>
                <div class="text-xs mt-1" style="color: #B39A78;">{{ $pendingPostsCount ?? 0 }} pending</div>
            </div>
        </div>
    @endif

    {{-- TRANSITION DIVIDER --}}
    <div style="display: flex; align-items: center; justify-content: center; gap: 24px; padding: 10px 0 2px;">
        <div style="flex: 1; height: 1px; background: linear-gradient(to left, #C79745, #EAE3D8, transparent); max-width: 200px;"></div>
        <span style="font-size: 11px; font-weight: 700; letter-spacing: 0.24em; color: #F3E5CF; text-transform: uppercase;">
            Latest Updates
        </span>
        <div style="flex: 1; height: 1px; background: linear-gradient(to right, #C79745, #EAE3D8, transparent); max-width: 200px;"></div>
    </div>

    {{-- ANNOUNCEMENTS SECTION (Same for all roles) --}}
    <div style="background: linear-gradient(180deg, #FFFFFF 0%, #FDFBF8 100%); border-radius: 32px; padding: 28px 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.04); border: 1px solid #3A342D;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="font-size: 24px; font-weight: 600; color: #2F2A27; font-family: 'Playfair Display', serif;">
                Community Board
            </h2>
            <a href="{{ route('announcements.index') }}"
               style="color: #BE9360; text-decoration: none; font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.05em;">
                View all →
            </a>
        </div>

        <div class="space-y-2">
            @forelse(($announcements ?? collect())->take(3) as $announcement)
                <div style="display: flex; gap: 16px; align-items: center; padding: 16px 0; border-bottom: 1px solid #F8F4EE;">
                    <div style="width: 42px; height: 42px; background: #FBF8F3; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;">
                        @if($announcement->priority === 'urgent') 🚨
                        @elseif($announcement->priority === 'important') ✨
                        @else 📢
                        @endif
                    </div>
                    <div style="flex: 1;">
                        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                            <span style="font-weight: 600; font-size: 14px; color: #2F2A27;">{{ $announcement->title }}</span>
                            @if($announcement->priority === 'important')
                                <span style="font-size: 9px; background: #FEF8F0; color: #BE9360; padding: 3px 8px; border-radius: 999px; font-weight: 700;">NEW</span>
                            @endif
                        </div>
                        <p style="font-size: 13px; color: #7B746B; line-height: 1.5; margin-top: 4px;">{{ Str::limit($announcement->content, 120) }}</p>
                    </div>
                    <div style="text-align: right; flex-shrink: 0;">
                        <p style="font-size: 11px; color: #B39A78; font-weight: 500;">{{ $announcement->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            @empty
                <div style="text-align: center; padding: 32px 0;">
                    <p style="color: #B39A78; font-size: 14px;">No new announcements today.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- FOOTER --}}
    <div class="mt-6">
        <div style="border-top: 1px solid #3A342D; padding: 36px 0 16px;">
            <div class="grid md:grid-cols-3 gap-10 items-start">
                <div>
                    <h2 style="font-size: 24px; font-weight: 600; color: #EDE6D6; font-family: 'Playfair Display', serif; margin-bottom: 10px;">
                        Hall<span style="color:#D4AF37;">Sync</span>
                    </h2>
                    <p style="font-size: 14px; line-height: 1.7; color: #A99A86;">
                        A refined student hall experience designed for seamless bookings,
                        resident care, and a connected community.
                    </p>
                </div>
                <div>
                    <h2 style="font-size: 24px; font-weight: 600; color: #EDE6D6; font-family: 'Playfair Display', serif; margin-bottom: 14px;">
                        Quick Links
                    </h2>
                    <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px 24px; font-size: 14px;">
                        <a href="{{ route('dashboard') }}" style="color:#BFAE92; text-decoration:none;" onmouseover="this.style.color='#D4AF37'" onmouseout="this.style.color='#BFAE92'">Home</a>
                        <a href="{{ route('bookings.index') }}" style="color:#BFAE92; text-decoration:none;" onmouseover="this.style.color='#D4AF37'" onmouseout="this.style.color='#BFAE92'">Spaces</a>
                        <a href="{{ route('tickets.index') }}" style="color:#BFAE92; text-decoration:none;" onmouseover="this.style.color='#D4AF37'" onmouseout="this.style.color='#BFAE92'">Tickets</a>
                        <a href="{{ route('announcements.index') }}" style="color:#BFAE92; text-decoration:none;" onmouseover="this.style.color='#D4AF37'" onmouseout="this.style.color='#BFAE92'">Community</a>
                        @auth
                        <a href="{{ route('profile.edit') }}" style="color:#BFAE92; text-decoration:none;" onmouseover="this.style.color='#D4AF37'" onmouseout="this.style.color='#BFAE92'">Profile</a>
                        @endauth
                    </div>
                </div>
                <div class="md:text-right">
                    <h2 style="font-size: 24px; font-weight: 600; color: #EDE6D6; font-family: 'Playfair Display', serif; margin-bottom: 14px;">
                        Experience
                    </h2>
                    <div style="font-size: 14px; line-height: 1.8; color: #A99A86;">
                        <div>Modern student living</div>
                        <div>Comfort & convenience</div>
                        <div>Smart dorm management</div>
                    </div>
                </div>
            </div>
            <div style="margin-top: 30px; padding-top: 16px; border-top: 1px solid rgba(58, 52, 45, 0.6);">
                <div class="flex flex-col md:flex-row items-center justify-between gap-3">
                    <p style="font-size: 12px; color: #8A7A66; margin: 0;">© {{ date('Y') }} HallSync. All rights reserved.</p>
                    <div class="flex items-center gap-4">
                        <span style="font-size: 12px; color: #8A7A66;">Refined Living</span>
                        <span style="width: 5px; height: 5px; border-radius: 999px; background: #D4AF37; display: inline-block;"></span>
                        <span style="font-size: 12px; color: #8A7A66;">Premium Experience</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
</x-app-layout>