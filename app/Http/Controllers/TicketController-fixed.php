<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->role === 'manager') {
            // Admin sees ALL tickets
            $tickets = MaintenanceTicket::with('user', 'assignedTo')
                ->orderBy('created_at', 'desc')
                ->get();
            return view('admin.tickets.index', compact('tickets'));
            
        } elseif ($user->role === 'handyman') {
            // Handyman sees tickets assigned to them
            $tickets = MaintenanceTicket::where('assigned_to', $user->id)
                ->orWhere(function($query) use ($user) {
                    $query->where('user_id', $user->id)
                          ->whereNull('assigned_to');
                })
                ->orderBy('created_at', 'desc')
                ->get();
            return view('handyman.tickets.index', compact('tickets'));
            
        } else {
            // Resident sees ONLY their own tickets
            $tickets = MaintenanceTicket::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
            return view('resident.tickets.index', compact('tickets'));
        }
    }

    public function create()
    {
        // Only residents and managers can create tickets
        if (Auth::user()->role === 'handyman') {
            abort(403, 'Handymen cannot create tickets.');
        }
        
        // Use the same create view for both (it's shared)
        return view('tickets.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'category' => 'required|in:plumbing,electrical,furniture,hvac,other',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'video' => 'nullable|file|mimes:mp4,mov,avi|max:10240',
        ], [
            'title.required' => 'Please enter a title for your ticket.',
            'description.required' => 'Please describe the issue.',
            'priority.required' => 'Please select a priority level.',
            'category.required' => 'Please select a category for this issue.',
            'image.max' => 'Image must be less than 2MB.',
            'image.mimes' => 'Only JPEG, PNG, JPG, or GIF images are allowed.',
            'video.max' => 'Video must be less than 10MB.',
            'video.mimes' => 'Only MP4, MOV, or AVI videos are allowed.',
        ]);

        try {
            $ticket = new MaintenanceTicket();
            $ticket->user_id = Auth::id();
            $ticket->ticket_id = 'TKT-' . strtoupper(uniqid());
            $ticket->title = $request->title;
            $ticket->description = $request->description;
            $ticket->priority = $request->priority;
            $ticket->category = $request->category;
            $ticket->status = 'received';

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
                ->with('success', 'Ticket created successfully! Ticket ID: ' . $ticket->ticket_id);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Upload failed. Please try again.')
                ->withInput();
        }
    }

    public function show(MaintenanceTicket $ticket)
    {
        $user = Auth::user();
        
        // Admin can view any ticket
        if ($user->role === 'manager') {
            return view('admin.tickets.show', compact('ticket'));
        }
        
        // Handyman can view assigned tickets or their own
        if ($user->role === 'handyman') {
            if ($ticket->assigned_to !== $user->id && $ticket->user_id !== $user->id) {
                abort(403);
            }
            return view('handyman.tickets.show', compact('ticket'));
        }
        
        // Resident can only view their own tickets
        if ($ticket->user_id !== $user->id) {
            abort(403);
        }
        
        return view('resident.tickets.show', compact('ticket'));
    }

    public function edit(MaintenanceTicket $ticket)
    {
        $user = Auth::user();
        
        // Only ticket owner or manager can edit
        if ($user->role !== 'manager' && $ticket->user_id !== $user->id) {
            abort(403);
        }
        
        // Handymen cannot edit tickets
        if ($user->role === 'handyman') {
            abort(403, 'Handymen cannot edit tickets. Only update status.');
        }
        
        // Admin and resident use different edit views
        if ($user->role === 'manager') {
            return view('admin.tickets.edit', compact('ticket'));
        }
        
        return view('resident.tickets.edit', compact('ticket'));
    }

    public function update(Request $request, MaintenanceTicket $ticket)
    {
        $user = Auth::user();
        
        if ($user->role !== 'manager' && $ticket->user_id !== $user->id) {
            abort(403);
        }
        
        if ($user->role === 'handyman') {
            abort(403);
        }

        $request->validate([
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'category' => 'required|in:plumbing,electrical,furniture,hvac,other',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'video' => 'nullable|file|mimes:mp4,mov,avi|max:10240',
            'remove_image' => 'nullable|boolean',
            'remove_video' => 'nullable|boolean',
        ]);

        try {
            $ticket->description = $request->description;
            $ticket->priority = $request->priority;
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
        
        // Only ticket owner or manager can delete
        if ($user->role !== 'manager' && $ticket->user_id !== $user->id) {
            abort(403, 'You cannot delete this ticket.');
        }
        
        if ($user->role === 'handyman') {
            abort(403);
        }
        
        if (in_array($ticket->status, ['in_progress', 'completed'])) {
            return redirect()->route('tickets.index')
                ->with('error', 'Cannot delete a ticket that is already in progress or completed.');
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

    /**
     * Update ticket status (for handymen and managers)
     */
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
        
        // Redirect to appropriate view based on role
        if ($user->role === 'manager') {
            return redirect()->route('admin.tickets.show', $ticket)
                ->with('success', 'Ticket status updated successfully!');
        }
        
        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Ticket status updated successfully!');
    }
}