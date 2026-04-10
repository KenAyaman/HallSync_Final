@if($ticket->status === 'pending_approval')
    <div class="flex gap-2">
        <form method="POST" action="{{ route('tickets.approve', $ticket) }}" class="inline" onsubmit="return confirm('Approve this ticket?')">
            @csrf @method('POST')
            <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold flex items-center gap-1.5 bg-green-500/20 text-green-400 border border-green-500/30 hover:bg-green-500/30 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Approve
            </button>
        </form>
        <form method="POST" action="{{ route('tickets.reject', $ticket) }}" class="inline" onsubmit="return confirm('Reject this ticket?')">
            @csrf @method('POST')
            <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold flex items-center gap-1.5 bg-red-500/20 text-red-400 border border-red-500/30 hover:bg-red-500/30 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Reject
            </button>
        </form>
    </div>
@endif
