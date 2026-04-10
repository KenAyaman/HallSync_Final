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
            // Manager sees ALL tickets
            $tickets = MaintenanceTicket::orderBy('created_at', 'desc')->get();

            $handymen = User::where('role', 'handyman')->orderBy('name')->get();

            return view('admin.tickets.index', compact('tickets', 'handymen'));
        } elseif ($user->role === 'handyman') {
            return redirect()->route('staff.queue');
        } else {
            // Resident sees only their own tickets
            $tickets = MaintenanceTicket::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();

            return view('resident.tickets.index', compact('tickets'));
        }
    }

    public function create()
    {
        $user = Auth::user();

        // Handymen cannot create tickets
        if ($user->role === 'handyman') {
            abort(403, 'Handymen cannot create tickets.');
        }

        if ($user->role === 'manager') {
            return view('admin.tickets.create');
        }

        return view('resident.tickets.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:plumbing,electrical,furniture,hvac,other',
            'priority' => 'required|in:low,medium,critical',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'video' => 'nullable|file|mimes:mp4,mov,avi|max:10240',
        ]);

        try {
            $ticket = new MaintenanceTicket();
            $ticket->user_id = Auth::id();
            $ticket->ticket_id = 'TKT-' . strtoupper(uniqid());
            $ticket->title = $request->title;
            $ticket->description = $request->description;
            $ticket->category = $request->category;
            $ticket->priority = MaintenanceTicket::normalizePriorityValue($request->priority);
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

    public function show(MaintenanceTicket $ticket)
    {
        $user = Auth::user();

        if ($user->role === 'manager') {
            $handymen = User::where('role', 'handyman')->orderBy('name')->get();

            return view('admin.tickets.show', compact('ticket', 'handymen'));
        }

        if ($user->role === 'handyman') {
            if ($ticket->assigned_to !== $user->id && $ticket->user_id !== $user->id) {
                abort(403);
            }

            return view('handyman.tickets.show', compact('ticket'));
        }

        if ($ticket->user_id !== $user->id) {
            abort(403);
        }

        return view('resident.tickets.show', compact('ticket'));
    }

    public function edit(MaintenanceTicket $ticket)
    {
        $user = Auth::user();

        // Handymen cannot edit tickets
        if ($user->role === 'handyman') {
            abort(403, 'Handymen cannot edit tickets. Only update status.');
        }

        // Only ticket owner or manager can edit
        if ($user->role !== 'manager' && $ticket->user_id !== $user->id) {
            abort(403);
        }

        if ($user->role === 'manager') {
            return view('admin.tickets.edit', compact('ticket'));
        }

        return view('resident.tickets.edit', compact('ticket'));
    }

    public function update(Request $request, MaintenanceTicket $ticket)
    {
        $user = Auth::user();

        if ($user->role === 'handyman') {
            abort(403);
        }

        if ($user->role !== 'manager' && $ticket->user_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,critical',
            'category' => 'required|in:plumbing,electrical,furniture,hvac,other',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'video' => 'nullable|file|mimes:mp4,mov,avi|max:10240',
            'remove_image' => 'nullable|boolean',
            'remove_video' => 'nullable|boolean',
        ]);

        try {
            $ticket->description = $request->description;
            $ticket->priority = MaintenanceTicket::normalizePriorityValue($request->priority);
            $ticket->category = $request->category;

            if ($request->has('remove_image') && $request->remove_image) {
                if ($ticket->image_path) {
                    Storage::disk('public')->delete($ticket->image_path);
                    $ticket->image_path = null;
                }
            }

            if ($request->has('remove_video') && $request->remove_video) {
                if ($ticket->video_path) {
                    Storage::disk('public')->delete($ticket->video_path);
                    $ticket->video_path = null;
                }
            }

            if ($request->hasFile('image')) {
                if ($ticket->image_path) {
                    Storage::disk('public')->delete($ticket->image_path);
                }
                $path = $request->file('image')->store('tickets/images', 'public');
                $ticket->image_path = $path;
            }

            if ($request->hasFile('video')) {
                if ($ticket->video_path) {
                    Storage::disk('public')->delete($ticket->video_path);
                }
                $path = $request->file('video')->store('tickets/videos', 'public');
                $ticket->video_path = $path;
            }

            $ticket->save();

            return redirect()->route('tickets.index')
                ->with('success', 'Ticket updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Update failed. Please try again.')
                ->withInput();
        }
    }

    public function destroy(MaintenanceTicket $ticket)
    {
        $user = Auth::user();

        // Handymen cannot delete
        if ($user->role === 'handyman') {
            abort(403);
        }

        // Only ticket owner or manager can delete
        if ($user->role !== 'manager' && $ticket->user_id !== $user->id) {
            abort(403, 'You cannot delete this ticket.');
        }

        if ($user->role !== 'manager' && $ticket->status === 'in_progress') {
            return redirect()->route('tickets.index')
                ->with('error', 'Cannot delete a ticket that is already in progress.');
        }

        if ($ticket->image_path) {
            Storage::disk('public')->delete($ticket->image_path);
        }

        if ($ticket->video_path) {
            Storage::disk('public')->delete($ticket->video_path);
        }

        $ticket->delete();

        return redirect()->route('tickets.index')
            ->with('success', 'Ticket deleted successfully.');
    }

    public function updateStatus(Request $request, MaintenanceTicket $ticket)
    {
        $user = Auth::user();

        // Only handymen and managers can update status
        if ($user->role !== 'manager' && $user->role !== 'handyman') {
            abort(403);
        }

        // Handymen can only update assigned tickets
        if ($user->role === 'handyman' && $ticket->assigned_to !== $user->id) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:received,assigned,in_progress,completed'
        ]);

        $ticket->status = $request->status;
        $ticket->save();

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Ticket status updated successfully!');
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
        
        if (! in_array($ticket->status, ['pending_approval', 'approved', 'assigned'], true)) {
            return redirect()->back()
                ->with('error', 'Only tickets awaiting review or assignment can be assigned.');
        }
        
        $request->validate([
            'assigned_to' => 'required|exists:users,id'
        ]);
        
        $ticket->assigned_to = $request->assigned_to;
        $ticket->status = 'assigned';
        $ticket->save();
        
        return redirect()->route('tickets.index')
            ->with('success', 'Ticket assigned to staff successfully!');
    }
}
