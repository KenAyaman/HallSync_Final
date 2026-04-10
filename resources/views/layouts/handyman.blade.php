<x-app-layout>
<div class="space-y-8">

    {{-- HERO SECTION --}}
    <div class="relative overflow-hidden rounded-[36px] min-h-[280px] border border-[#3A342D]"
         style="background: linear-gradient(115deg, #1F2023 0%, #24262B 38%, #2C2C2F 62%, #3B3023 100%);">

        <div class="relative z-20 px-8 py-10 md:px-14 md:py-10">
            <div>
                <div class="mb-2"><span class="inline-block text-[11px] tracking-[0.30em] uppercase" style="color: #D2A04C; font-weight: 700;">Staff Portal</span></div>
                <h1 class="text-3xl md:text-4xl font-bold leading-[1.05]" style="font-family: 'Playfair Display', serif; color: #F8F3EA;">Maintenance<br><span style="color: #F3E5CF;">Work Orders</span></h1>
                <p class="text-sm leading-relaxed max-w-xl mt-3" style="color: rgba(255,255,255,0.82);">View and manage maintenance requests assigned to you.</p>
            </div>
        </div>
    </div>

    {{-- STATS CARDS --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-5 -mt-10 relative z-20">
        <div class="rounded-[24px] p-5 text-center" style="background: linear-gradient(135deg, #FFFFFF 0%, #F8F4EC 100%); border: 1px solid #3A342D;">
            <div class="text-4xl font-bold" style="color: #E74C3C;">{{ $urgentCount ?? 0 }}</div>
            <div class="text-sm mt-1" style="color: #8A6A3C;">Urgent Tasks</div>
        </div>
        <div class="rounded-[24px] p-5 text-center" style="background: linear-gradient(135deg, #FFFFFF 0%, #F8F4EC 100%); border: 1px solid #3A342D;">
            <div class="text-4xl font-bold" style="color: #C79745;">{{ $assignedCount ?? 0 }}</div>
            <div class="text-sm mt-1" style="color: #8A6A3C;">Assigned to You</div>
        </div>
        <div class="rounded-[24px] p-5 text-center" style="background: linear-gradient(135deg, #FFFFFF 0%, #F8F4EC 100%); border: 1px solid #3A342D;">
            <div class="text-4xl font-bold" style="color: #27AE60;">{{ $inProgressCount ?? 0 }}</div>
            <div class="text-sm mt-1" style="color: #8A6A3C;">In Progress</div>
        </div>
        <div class="rounded-[24px] p-5 text-center" style="background: linear-gradient(135deg, #FFFFFF 0%, #F8F4EC 100%); border: 1px solid #3A342D;">

            <div class="text-4xl font-bold" style="color: #2980B9;">{{ $completedCount ?? 0 }}</div>

            <div class="text-sm mt-1" style="color: #8A6A3C;">Completed (Week)</div>
        </div>
    </div>

    {{-- URGENT TASKS --}}
    @if(($urgentTickets ?? collect())->count() > 0)
    <div style="background: white; border-radius: 32px; padding: 24px 28px; border-left: 4px solid #E74C3C; border: 1px solid #3A342D;">
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px;"><span style="font-size: 22px;">🚨</span><h2 style="font-size: 18px; font-weight: 700; color: #E74C3C;">URGENT - Do First</h2></div>
        @foreach($urgentTickets as $ticket)
        <div style="background: #FEF5F5; border-radius: 16px; padding: 14px 18px; margin-bottom: 12px;">
            <div class="flex justify-between items-center">
                <div><strong>{{ $ticket->title }}</strong><div class="text-xs text-gray-500 mt-1">#{{ $ticket->ticket_id }}</div></div>
                <a href="{{ route('tickets.show', $ticket) }}" style="background: #C79745; color: white; padding: 6px 16px; border-radius: 20px; text-decoration: none; font-size: 12px;">View</a>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- MY ACTIVE TICKETS --}}
    <div style="background: white; border-radius: 32px; padding: 28px 32px; border: 1px solid #3A342D;">
        <h2 style="font-size: 20px; font-weight: 600; margin-bottom: 20px;">My Active Tickets</h2>
        @forelse($myTickets ?? [] as $ticket)
        <div style="background: #FAF7F2; border-radius: 16px; padding: 14px 18px; margin-bottom: 12px;">
            <div class="flex justify-between items-center">
                <div><strong>{{ $ticket->title }}</strong>
                    <div class="flex gap-2 mt-1">
                        @if($ticket->priority === 'urgent')<span style="font-size: 10px; background: #FEE2E2; color: #E74C3C; padding: 2px 8px; border-radius: 20px;">URGENT</span>@endif
                        <span style="font-size: 10px; background: #F5EDE8; padding: 2px 8px; border-radius: 20px;">{{ ucfirst($ticket->status) }}</span>
                    </div>
                </div>
                <a href="{{ route('tickets.show', $ticket) }}" style="color: #BE9360; text-decoration: none; font-weight: 500;">View →</a>
            </div>
        </div>
        @empty <p class="text-center text-gray-500 py-8">No tickets assigned to you yet.</p> @endforelse
    </div>

    {{-- FOOTER --}}
    <div class="mt-6"><div style="border-top: 1px solid #3A342D; padding: 36px 0 16px;"><p style="text-align: center; font-size: 12px; color: #8A7A66;">© {{ date('Y') }} HallSync — Staff Portal</p></div></div>

</div>
</x-app-layout>
