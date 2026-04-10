<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->role === 'manager') {
            // Admin view - see ALL announcements
            $announcements = Announcement::orderBy('created_at', 'desc')->get();
            $activeCount = Announcement::where('is_active', true)->count();
            $draftCount = Announcement::where('is_active', false)->count();
            
            return view('admin.announcements.index', compact('announcements', 'activeCount', 'draftCount'));
        }
        
        // Resident view - only see active announcements
        $announcements = Announcement::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('resident.announcements.index', compact('announcements'));
    }

    public function create()
    {
        // Only managers can create announcements
        if (Auth::user()->role !== 'manager') {
            abort(403);
        }
        
        return view('admin.announcements.create');
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'manager') {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'priority' => 'required|in:normal,important,urgent'
        ]);

        $announcement = new Announcement();
        $announcement->user_id = Auth::id();
        $announcement->title = $request->title;
        $announcement->content = $request->content;
        $announcement->priority = $request->priority;
        $announcement->is_active = true;
        $announcement->save();

        return redirect()->route('announcements.index')
            ->with('success', 'Announcement published!');
    }

    public function show(Announcement $announcement)
    {
        $user = Auth::user();
        
        if ($user->role === 'manager') {
            return view('admin.announcements.show', compact('announcement'));
        }
        
        return view('resident.announcements.show', compact('announcement'));
    }

    public function edit(Announcement $announcement)
    {
        if (Auth::user()->role !== 'manager') {
            abort(403);
        }
        
        return view('admin.announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        if (Auth::user()->role !== 'manager') {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'priority' => 'required|in:normal,important,urgent'
        ]);

        $announcement->update([
            'title' => $request->title,
            'content' => $request->content,
            'priority' => $request->priority,
        ]);

        return redirect()->route('announcements.index')
            ->with('success', 'Announcement updated successfully!');
    }

    public function toggle(Announcement $announcement)
    {
        if (Auth::user()->role !== 'manager') {
            abort(403);
        }
        
        $announcement->is_active = !$announcement->is_active;
        $announcement->save();
        
        $status = $announcement->is_active ? 'published' : 'hidden';
        return redirect()->route('announcements.index')
            ->with('success', "Announcement {$status} successfully!");
    }

    public function destroy(Announcement $announcement)
    {
        if (Auth::user()->role !== 'manager') {
            abort(403);
        }
        
        $announcement->delete();
        
        return redirect()->route('announcements.index')
            ->with('success', 'Announcement deleted successfully!');
    }
}
