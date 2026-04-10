<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MaintenanceTicket;
use App\Models\Booking;
use App\Models\Announcement;
use App\Models\CommunityPost;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'manager') {
            // Get all analytics data for charts
            $ticketTrendData = $this->getTicketTrendData();
            $categoryStats = $this->getCategoryStats();
            $spaceStats = $this->getSpaceStats();
            
            return view('dashboard.manager', [
                // Existing metrics
                'openTickets' => MaintenanceTicket::where('status', '!=', 'completed')->count(),
                'urgentTickets' => MaintenanceTicket::where('priority', 'urgent')
                    ->where('status', '!=', 'completed')
                    ->count(),
                'pendingBookings' => Booking::where('status', 'approved')
                    ->where('booking_date', '>=', Carbon::now())
                    ->count(),
                'totalResidents' => User::where('role', 'resident')->count(),
                'resolvedThisWeek' => MaintenanceTicket::where('status', 'completed')
                    ->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()])
                    ->count(),
                'recentTickets' => MaintenanceTicket::latest()->take(5)->get(),
                'announcements' => Announcement::where('is_active', true)->latest()->take(4)->get(),
                'pendingBookingsList' => Booking::with('user')
                    ->where('status', 'approved')
                    ->where('booking_date', '>=', Carbon::now())
                    ->orderBy('booking_date')
                    ->take(5)
                    ->get(),
                'resolutionRate' => $this->calculateResolutionRate(),
                'ticketsThisMonth' => MaintenanceTicket::whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->count(),
                'bookingsThisMonth' => Booking::whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->count(),
                'communityPosts' => CommunityPost::count(),
'activeResidents' => User::where('role', 'resident')->count(),
                'handymen' => User::where('role', 'handyman')->get(),
                'inProgressTickets' => MaintenanceTicket::where('status', 'in_progress')->count(),
                
                // CHART DATA (NEW)
                'ticketTrendLabels' => $ticketTrendData['labels'],
                'ticketTrendData' => $ticketTrendData['data'],
                'categoryLabels' => $categoryStats['labels'],
                'categoryData' => $categoryStats['data'],
                'spaceLabels' => $spaceStats['labels'],
                'spaceData' => $spaceStats['data'],
            ]);
        }

        if ($user->role === 'handyman') {
            // Get tickets assigned to handyman
            $myTickets = MaintenanceTicket::where('assigned_to', $user->id)
                ->whereNotIn('status', ['completed', 'rejected'])
                ->orderByRaw("FIELD(priority, 'urgent', 'high', 'medium', 'low')")
                ->orderBy('created_at', 'desc')
                ->get();
            
            $assignedTickets = $myTickets->where('status', 'assigned')->count();
            $inProgressTickets = $myTickets->where('status', 'in_progress')->count();
            $urgentTickets = $myTickets->where('priority', 'urgent')->count();
            $completedTickets = MaintenanceTicket::where('assigned_to', $user->id)
                ->where('status', 'completed')
                ->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->get();
            
            $completedToday = $completedTickets->count();
            $urgentTicketsList = $myTickets->where('priority', 'urgent');
            
            return view('dashboard.handyman', compact(
                'myTickets', 'assignedTickets', 'inProgressTickets', 
                'completedToday', 'urgentTickets', 'urgentTicketsList', 'completedTickets'
            ));
        }

        // RESIDENT DASHBOARD
        return view('dashboard.resident', [
            'activeTickets' => MaintenanceTicket::where('user_id', $user->id)
                ->where('status', '!=', 'completed')
                ->count(),
            'inProgressTickets' => MaintenanceTicket::where('user_id', $user->id)
                ->where('status', 'in_progress')
                ->count(),
            'pendingBookings' => Booking::where('user_id', $user->id)
                ->where('status', 'approved')
                ->where('booking_date', '>=', Carbon::now())
                ->count(),
            'upcomingBookingsCount' => Booking::where('user_id', $user->id)
                ->where('booking_date', '>=', Carbon::now())
                ->where('status', 'approved')
                ->count(),
            'nextBookingDate' => Booking::where('user_id', $user->id)
                ->where('booking_date', '>=', Carbon::now())
                ->where('status', 'approved')
                ->orderBy('booking_date', 'asc')
                ->value('booking_date'),
            'myPostsCount' => CommunityPost::where('user_id', $user->id)->count(),
            'pendingPostsCount' => CommunityPost::where('user_id', $user->id)
                ->where('status', 'pending')
                ->count(),
            'announcements' => Announcement::where('is_active', true)
                ->latest()
                ->take(5)
                ->get(),
        ]);
    }

    private function calculateResolutionRate()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $totalTicketsThisMonth = MaintenanceTicket::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
        $resolvedThisMonth = MaintenanceTicket::where('status', 'completed')
            ->whereBetween('updated_at', [$startOfMonth, $endOfMonth])
            ->count();

        if ($totalTicketsThisMonth === 0) {
            return 100;
        }

        return round(($resolvedThisMonth / $totalTicketsThisMonth) * 100);
    }

    /**
     * Get ticket trend data for last 30 days
     */
    private function getTicketTrendData()
    {
        $labels = [];
        $data = [];
        
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('M d');
            $data[] = MaintenanceTicket::whereDate('created_at', $date)->count();
        }
        
        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * Get category statistics for chart
     */
    private function getCategoryStats()
    {
        // If category column exists, use it
        if (Schema::hasColumn('maintenance_tickets', 'category')) {
            $categories = MaintenanceTicket::select('category', DB::raw('count(*) as total'))
                ->whereNotNull('category')
                ->groupBy('category')
                ->get();
            
            if ($categories->isNotEmpty()) {
                $labels = [];
                $data = [];
                foreach ($categories as $cat) {
                    $labels[] = ucfirst($cat->category);
                    $data[] = $cat->total;
                }
                return ['labels' => $labels, 'data' => $data];
            }
        }
        
        // Fallback to default
        return [
            'labels' => ['Plumbing', 'Electrical', 'Furniture', 'HVAC', 'Other'],
            'data' => [0, 0, 0, 0, 0]
        ];
    }

    /**
     * Get space statistics for chart
     */
    private function getSpaceStats()
    {
        // If space_name column exists, use it
        if (Schema::hasColumn('bookings', 'space_name')) {
            $spaces = Booking::select('space_name', DB::raw('count(*) as total'))
                ->whereNotNull('space_name')
                ->groupBy('space_name')
                ->get();
            
            if ($spaces->isNotEmpty()) {
                $labels = [];
                $data = [];
                foreach ($spaces as $space) {
                    $labels[] = $space->space_name;
                    $data[] = $space->total;
                }
                return ['labels' => $labels, 'data' => $data];
            }
        }
        
        // Fallback to default
        return [
            'labels' => ['Function Hall', 'Gym', 'Study Room', 'Game Room', 'Laundry'],
            'data' => [0, 0, 0, 0, 0]
        ];
    }
}
