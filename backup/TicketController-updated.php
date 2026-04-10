<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceTicket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->role === 'manager') {
            // Admin sees ALL tickets with pending approval first
            $tickets = MaintenanceTicket::with(['user', 'assignedTo'])
                ->orderByRaw("FIELD(status, 'pending_approval', 'approved', 'assigned', 'in_progress', 'completed')")
                ->orderByRaw("FIELD(priority, 'urgent', 'high', 'medium', 'low')")
                ->orderBy('created_at', 'desc')
                ->get();
            
            $handymen = User::where('role', 'handyman')->get();
            
            return view('admin.tickets.index', compact('tickets', 'handymen'));
        }
        
        if ($user->role === 'handyman') {
            // Handyman sees only approved and assigned tickets
            $tickets = MaintenanceTicket::where('assigned_to', $user->id)
                ->whereIn('status', ['approved', 'assigned', 'in_progress'])
                ->orWhere(function($query) use ($user) {
                    $query->where('user_id', $user->id)
                          ->where('status', 'completed');
                })
                ->orderByRaw("FIELD(status, 'assigned', 'in_progress', 'approved')")
                ->orderByRaw("FIELD(priority, 'urgent', 'high', 'medium', 'low')")
                ->get();
            
            return view('tickets.index', compact('tickets'));
        }
        
        // Resident sees their own tickets
        $tickets = MaintenanceTicket::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('tickets.index', compact('tickets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'video' => 'nullable|file|mimes:mp4,mov,avi|max:10240',
        ]);

        try {
            $ticket = new MaintenanceTicket();
            $ticket->user_id = Auth::id();
            $ticket->ticket_id = 'TKT-' . strtoupper(uniqid());
            $ticket->title = $request->title;
            $ticket->description = $request->description;
            $ticket->priority = $request->priority;
            $ticket->status = 'pending_approval'; // NEW STATUS

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('tickets/images', 'public');
                $ticket->image_path = $path;
            }

            if ($request->hasFile('video')) {
                $path = $request->file('video')->store('tickets/videos', 'public');
                $ticket->video_path = $path;
            }

            $ticket->save();

            return redirect()->route('tickets.index')
                ->with('success', 'Ticket submitted for admin approval. Ticket ID: ' . $ticket->ticket_id);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Upload failed. Please try again.')
                ->withInput();
        }
    }

    // NEW: Admin approves ticket and optionally assigns handyman
    public function approve(Request $request, MaintenanceTicket $ticket)
    {
        if (Auth::user()->role !== 'manager') {
            abort(403);
        }
        
        $ticket->status = 'approved';
        
        if ($request->has('assigned_to') && $request->assigned_to) {
            $ticket->assigned_to = $request->assigned_to;
            $ticket->status = 'assigned';
        }
        
        $ticket->save();
        
        return redirect()->route('tickets.index')
            ->with('success', 'Ticket approved successfully!');
    }
    
    // NEW: Admin rejects ticket with reason
    public function reject(Request $request, MaintenanceTicket $ticket)
    {
        if (Auth::user()->role !== 'manager') {
            abort(403);
        }
        
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);
        
        $ticket->status = 'rejected';
        $ticket->rejection_reason = $request->rejection_reason;
        $ticket->save();
        
        return redirect()->route('tickets.index')
            ->with('success', 'Ticket rejected.');
    }

    // Admin assigns handyman to approved ticket
    public function assign(Request $request, MaintenanceTicket $ticket)
    {
        if (Auth::user()->role !== 'manager') {
            abort(403);
        }
        
        if ($ticket->status !== 'approved') {
            return redirect()->back()
                ->with('error', 'Only approved tickets can be assigned.');
        }
        
        $request->validate([
            'assigned_to' => 'required|exists:users,id'
        ]);
        
        $ticket->assigned_to = $request->assigned_to;
        $ticket->status = 'assigned';
        $ticket->save();
        
        return redirect()->route('tickets.index')
            ->with('success', 'Ticket assigned to handyman successfully!');
    }

    // ... rest of your existing methods (show, edit, update, destroy)
}
