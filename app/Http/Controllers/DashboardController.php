<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MaintenanceTicket;
use App\Models\Booking;
use App\Models\Announcement;
use App\Models\CommunityPost;
use App\Models\CommunityComment;
use App\Models\Concern;
use App\Models\User;
use App\Models\UserActivityLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'manager') {
            $analyticsFilters = $this->getAnalyticsFilters($request);
            $analyticsFilterOptions = $this->getAnalyticsFilterOptions();

            // Get all analytics data for charts
            $ticketTrendData = $this->getTicketTrendData($analyticsFilters);
            $categoryStats = $this->getCategoryStats($analyticsFilters);
            $spaceStats = $this->getSpaceStats($analyticsFilters);
            $predictiveAnalytics = $this->getPredictiveAnalytics();
            $managerMetrics = $this->getManagerDashboardMetrics($predictiveAnalytics);
            
            return view('dashboard.manager', [
                // Existing metrics
                'openTickets' => $managerMetrics['openTickets'],
                'urgentTickets' => $managerMetrics['urgentTickets'],
                'upcomingBookings' => $managerMetrics['upcomingBookings'],
                'totalResidents' => $managerMetrics['totalResidents'],
                'resolvedThisWeek' => MaintenanceTicket::whereIn('status', ['resolved', 'closed'])
                    ->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()])
                    ->count(),
                'recentTickets' => MaintenanceTicket::latest()->take(5)->get(),
                'recentActivityLogs' => UserActivityLog::with('actor')->latest()->take(10)->get(),
                'announcements' => Announcement::visibleToResidents()->latest()->take(4)->get(),
                'upcomingBookingsList' => Booking::with('user')
                    ->where('status', 'approved')
                    ->active()
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
                'activeResidents' => $managerMetrics['activeResidents'],
                'handymen' => User::where('role', 'handyman')->get(),
                'inProgressTickets' => MaintenanceTicket::where('status', 'in_progress')->count(),
                'deepAnalytics' => $this->getDeepAnalyticsData($analyticsFilters),
                'analyticsFilters' => $analyticsFilters,
                'analyticsFilterOptions' => $analyticsFilterOptions,

                // CHART DATA (NEW)
                'ticketTrendLabels' => $ticketTrendData['labels'],
                'ticketTrendData' => $ticketTrendData['data'],
                'categoryLabels' => $categoryStats['labels'],
                'categoryData' => $categoryStats['data'],
                'spaceLabels' => $spaceStats['labels'],
                'spaceData' => $spaceStats['data'],
                'predictiveAnalytics' => $predictiveAnalytics,
            ]);
        }

        if ($user->role === 'handyman') {
            return redirect()->route('staff.overview');
        }

        // RESIDENT DASHBOARD
        return view('dashboard.resident', [
            'activeTickets' => MaintenanceTicket::where('user_id', $user->id)
                ->whereNotIn('status', ['closed', 'cancelled', 'rejected'])
                ->count(),
            'inProgressTickets' => MaintenanceTicket::where('user_id', $user->id)
                ->where('status', 'in_progress')
                ->count(),
            'upcomingBookings' => ($upcomingCount = Booking::where('user_id', $user->id)
                ->where('status', 'approved')
                ->active()
                ->count()),
            // Reuse the same computed value to avoid a duplicate query (M-05).
            'upcomingBookingsCount' => $upcomingCount,
            'nextBookingDate' => Booking::where('user_id', $user->id)
                ->active()
                ->where('status', 'approved')
                ->orderBy('booking_date', 'asc')
                ->value('booking_date'),
            'myPostsCount' => CommunityPost::where('user_id', $user->id)->count(),
            'pendingPostsCount' => CommunityPost::where('user_id', $user->id)
                ->where('status', 'pending')
                ->count(),
            'recentTickets' => MaintenanceTicket::where('user_id', $user->id)
                ->latest()
                ->take(5)
                ->get(),
            'announcements' => Announcement::visibleToResidents()
                ->latest()
                ->take(5)
                ->get(),
            'communityPosts' => CommunityPost::where('status', 'approved')
                ->with('user')
                ->latest()
                ->take(4)
                ->get(),
        ]);
    }

    public function managerMetrics()
    {
        return response()->json($this->getManagerDashboardMetrics());
    }

    public function pollStats()
    {
        $user = Auth::user();
        abort_unless($user && $user->isManager(), 403);

        return response()->json($this->getManagerDashboardMetrics());
    }

    public function pollMaintenance()
    {
        $user = Auth::user();
        abort_unless($user && $user->isManager(), 403);

        $recentTickets = MaintenanceTicket::latest()->take(5)->get(['id', 'title', 'status', 'priority', 'created_at', 'updated_at']);

        return response()->json([
            'tickets' => $recentTickets,
            'updated_at' => $recentTickets->max('updated_at')?->toIso8601String(),
        ]);
    }

    public function pollCommunity()
    {
        $user = Auth::user();
        abort_unless($user && $user->isManager(), 403);

        $pendingPosts = CommunityPost::where('status', 'pending')->latest()->take(5)->get(['id', 'title', 'user_id', 'created_at']);
        $recentComments = CommunityComment::with('post')->latest()->take(5)->get(['id', 'content', 'community_post_id', 'user_id', 'created_at']);

        return response()->json([
            'pending_posts' => $pendingPosts,
            'recent_comments' => $recentComments,
            'updated_at' => max(
                $pendingPosts->max('created_at')?->toIso8601String(),
                $recentComments->max('created_at')?->toIso8601String()
            ),
        ]);
    }

    private function calculateResolutionRate()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $totalTicketsThisMonth = MaintenanceTicket::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
        $resolvedThisMonth = MaintenanceTicket::whereIn('status', ['resolved', 'closed'])
            ->whereBetween('updated_at', [$startOfMonth, $endOfMonth])
            ->count();

        if ($totalTicketsThisMonth === 0) {
            return 100;
        }

        return round(($resolvedThisMonth / $totalTicketsThisMonth) * 100);
    }

    private function getManagerDashboardMetrics(?array $predictiveAnalytics = null): array
    {
        $predictiveAnalytics ??= $this->getPredictiveAnalytics();

        return [
            'openTickets' => (int) ($predictiveAnalytics['openTicketCount'] ?? 0),
            'urgentTickets' => MaintenanceTicket::where('priority', 'critical')
                ->whereNotIn('status', ['closed', 'cancelled', 'rejected'])
                ->count(),
            'upcomingBookings' => Booking::where('status', 'approved')
                ->active()
                ->count(),
            'totalResidents' => User::where('role', 'resident')->count(),
            'activeResidents' => User::where('role', 'resident')->where('is_active', true)->count(),
            'openConcerns' => (int) ($predictiveAnalytics['openConcerns'] ?? 0),
            'unassignedOpen' => (int) ($predictiveAnalytics['unassignedOpen'] ?? 0),
            'availableHandymen' => (int) ($predictiveAnalytics['availableHandymen'] ?? 0),
            'activeHandymen' => (int) ($predictiveAnalytics['activeHandymen'] ?? 0),
        ];
    }

    /**
     * Returns the latest updated_at timestamp scoped to what the authenticated user
     * is allowed to see — prevents residents/handymen from inferring system-wide activity (M-11).
     */
    public function heartbeat()
    {
        $user = Auth::user();

        if ($user->isManager()) {
            $timestamps = [
                Booking::latest('updated_at')->value('updated_at'),
                MaintenanceTicket::latest('updated_at')->value('updated_at'),
                Announcement::latest('updated_at')->value('updated_at'),
                CommunityPost::latest('updated_at')->value('updated_at'),
                CommunityComment::latest('updated_at')->value('updated_at'),
                Concern::latest('updated_at')->value('updated_at'),
                User::latest('updated_at')->value('updated_at'),
            ];
        } elseif ($user->isHandyman()) {
            $timestamps = [
                MaintenanceTicket::where('assigned_to', $user->id)->latest('updated_at')->value('updated_at'),
            ];
        } else {
            // Resident: own tickets, own bookings, public announcements, approved community posts, own concerns.
            $timestamps = [
                MaintenanceTicket::where('user_id', $user->id)->latest('updated_at')->value('updated_at'),
                Booking::where('user_id', $user->id)->latest('updated_at')->value('updated_at'),
                Announcement::visibleToResidents()->latest('updated_at')->value('updated_at'),
                CommunityPost::where('status', 'approved')->latest('updated_at')->value('updated_at'),
                Concern::where('user_id', $user->id)->latest('updated_at')->value('updated_at'),
            ];
        }

        $lastUpdated = collect($timestamps)->filter()->max();

        return response()->json([
            'updated_at' => $lastUpdated
                ? Carbon::parse($lastUpdated)->toIso8601String()
                : Carbon::now()->toIso8601String(),
        ]);
    }

    private function getAnalyticsFilters(Request $request): array
    {
        $period = $request->input('analytics_period', '30');
        $allowedPeriods = ['7', '30', 'month'];

        return [
            'period' => in_array($period, $allowedPeriods, true) ? $period : '30',
            'category' => trim((string) $request->input('category', '')),
            'facility' => trim((string) $request->input('facility', '')),
            'status' => trim((string) $request->input('status', '')),
            'staff' => $request->filled('staff') ? (int) $request->input('staff') : null,
        ];
    }

    private function getAnalyticsFilterOptions(): array
    {
        return [
            'categories' => MaintenanceTicket::whereNotNull('category')
                ->distinct()
                ->orderBy('category')
                ->pluck('category')
                ->filter()
                ->values(),
            'facilities' => Booking::whereNotNull('facility_name')
                ->distinct()
                ->orderBy('facility_name')
                ->pluck('facility_name')
                ->filter()
                ->values(),
            'statuses' => MaintenanceTicket::whereNotNull('status')
                ->distinct()
                ->orderBy('status')
                ->pluck('status')
                ->filter()
                ->values(),
            'staff' => User::where('role', 'handyman')
                ->orderBy('name')
                ->get(['id', 'name']),
        ];
    }

    private function analyticsWindow(array $filters): array
    {
        $to = Carbon::now()->endOfDay();
        $from = match ($filters['period'] ?? '30') {
            '7' => Carbon::now()->subDays(6)->startOfDay(),
            'month' => Carbon::now()->startOfMonth(),
            default => Carbon::now()->subDays(29)->startOfDay(),
        };
        $days = max(1, (int) $from->diffInDays($to) + 1);

        return [$from, $to, $days];
    }

    private function filteredTicketQuery(array $filters)
    {
        $query = MaintenanceTicket::query();

        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['staff'])) {
            $query->where('assigned_to', $filters['staff']);
        }

        return $query;
    }

    private function filteredBookingQuery(array $filters)
    {
        $query = Booking::query();

        if (!empty($filters['facility'])) {
            $query->where('facility_name', $filters['facility']);
        }

        return $query;
    }

    /**
     * Get ticket trend data for last 30 days.
     * Uses one GROUP BY query instead of 30 individual COUNT queries (C-06).
     */
    private function getTicketTrendData(array $filters = []): array
    {
        [$start, $end, $days] = $this->analyticsWindow($filters);

        $counts = $this->filteredTicketQuery($filters)
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->whereBetween('created_at', [$start, $end])
            ->groupByRaw('DATE(created_at)')
            ->pluck('total', 'day');

        $labels = [];
        $data   = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date     = $end->copy()->subDays($i);
            $labels[] = $date->format('M d');
            $data[]   = (int) ($counts[$date->toDateString()] ?? 0);
        }

        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * Get category statistics for chart
     */
    private function getCategoryStats(array $filters = [])
    {
        [$from, $to] = $this->analyticsWindow($filters);

        // If category column exists, use it
        if (Schema::hasColumn('maintenance_tickets', 'category')) {
            $categories = $this->filteredTicketQuery($filters)
                ->select('category', DB::raw('count(*) as total'))
                ->whereBetween('created_at', [$from, $to])
                ->whereNotNull('category')
                ->groupBy('category')
                ->orderByDesc('total')
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
     * Get facility booking stats for the space-utilisation chart.
     * facility_name has been the canonical column since migration 2026_04_07_024957;
     * the legacy space_name fallback is removed (L-06).
     */
    private function getSpaceStats(array $filters = []): array
    {
        [$from, $to] = $this->analyticsWindow($filters);

        $spaces = $this->filteredBookingQuery($filters)
            ->select('facility_name', DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$from, $to])
            ->whereNotNull('facility_name')
            ->groupBy('facility_name')
            ->orderByDesc('total')
            ->get();

        if ($spaces->isNotEmpty()) {
            return [
                'labels' => $spaces->pluck('facility_name')->all(),
                'data'   => $spaces->pluck('total')->all(),
            ];
        }

        return [
            'labels' => ['Study Room 1', 'Study Room 2', 'Conference Room', 'Gym'],
            'data'   => [0, 0, 0, 0],
        ];
    }

    private function getPredictiveAnalytics(): array
    {
        $today = Carbon::today();
        $openStatuses = ['pending_approval', 'approved', 'assigned', 'in_progress', 'resolved'];

        // One GROUP BY query instead of 28 individual COUNT queries (C-06).
        $historicStart = $today->copy()->subDays(27)->startOfDay();
        $dayCounts = MaintenanceTicket::selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->where('created_at', '>=', $historicStart)
            ->groupByRaw('DATE(created_at)')
            ->pluck('total', 'day');

        $dailyTicketCounts = collect(range(27, 0))->map(function ($daysAgo) use ($today, $dayCounts) {
            $date = $today->copy()->subDays($daysAgo);

            return [
                'date'  => $date,
                'count' => (int) ($dayCounts[$date->toDateString()] ?? 0),
            ];
        });

        $previousAverage = $dailyTicketCounts->take(14)->avg('count');
        $recentAverage = $dailyTicketCounts->skip(14)->avg('count');
        $dailyTrend = ($recentAverage - $previousAverage) / 14;
        $forecastCounts = collect(range(1, 7))->map(
            fn ($day) => max(0, (int) round($recentAverage + ($dailyTrend * $day)))
        );
        $lastSevenTicketCount = $dailyTicketCounts->take(-7)->sum('count');
        $forecastTicketCount = $forecastCounts->sum();
        $trendPercent = $previousAverage > 0
            ? (int) round((($recentAverage - $previousAverage) / $previousAverage) * 100)
            : ($recentAverage > 0 ? 100 : 0);

        $openTickets = MaintenanceTicket::whereIn('status', $openStatuses)->get();
        $openTicketCount = $openTickets->count();
        $unassignedOpen = $openTickets->whereNull('assigned_to')->count();
        $inProgressCount = $openTickets->where('status', 'in_progress')->count();
        $assignedActiveCount = $openTickets->filter(fn ($ticket) => $ticket->assigned_to
            && $ticket->status !== 'in_progress')->count();
        $criticalUnassigned = MaintenanceTicket::whereIn('priority', ['critical', 'urgent', 'high'])
            ->whereIn('status', $openStatuses)
            ->whereNull('assigned_to')
            ->count();
        $agingBacklog = MaintenanceTicket::whereIn('status', $openStatuses)
            ->where('created_at', '<=', now()->subHours(48))
            ->count();

        $completedTickets = MaintenanceTicket::whereIn('status', ['resolved', 'closed'])
            ->where('updated_at', '>=', now()->subDays(30))
            ->get(['created_at', 'updated_at', 'task_started_at', 'task_completed_at', 'task_duration_minutes']);
        $averageResolutionHours = $completedTickets->isEmpty()
            ? null
            : round($completedTickets->avg(
                fn ($ticket) => ($ticket->task_duration_minutes ?? $ticket->created_at->diffInMinutes($ticket->updated_at)) / 60
            ), 1);
        $previousCompletedCount = MaintenanceTicket::whereIn('status', ['resolved', 'closed'])
            ->whereBetween('updated_at', [now()->subDays(60), now()->subDays(30)])
            ->count();
        $completionTrendPercent = $previousCompletedCount > 0
            ? (int) round((($completedTickets->count() - $previousCompletedCount) / $previousCompletedCount) * 100)
            : ($completedTickets->isNotEmpty() ? 100 : 0);

        $recentTickets = MaintenanceTicket::where('created_at', '>=', now()->subDays(30))->get();
        $topCategoryGroup = $recentTickets->whereNotNull('category')->groupBy('category')->map->count()->sortDesc();
        $topCategory = $topCategoryGroup->keys()->first();
        $topCategoryCount = $topCategory ? $topCategoryGroup->get($topCategory) : 0;
        $topCategoryShare = $recentTickets->isEmpty()
            ? 0
            : (int) round(($topCategoryCount / $recentTickets->count()) * 100);

        $upcomingBookings = Booking::where('status', 'approved')
            ->whereBetween('booking_date', [$today, $today->copy()->addDays(30)])
            ->get(['facility_name']);
        $busiestFacility = $upcomingBookings
            ->groupBy('facility_name')
            ->map->count()
            ->sortDesc()
            ->keys()
            ->first();
        $busiestFacilityBookings = $busiestFacility
            ? $upcomingBookings->where('facility_name', $busiestFacility)->count()
            : 0;
        // Exclude terminal, legacy, and draft statuses from the "needs attention" count (M-03).
        $openConcerns = Concern::whereNotIn('status', ['draft', 'responded', 'closed', 'rejected'])->count();
        $activeHandymen = User::where('role', 'handyman')->where('is_active', true)->count();
        $busyHandymen = $openTickets->whereIn('status', ['assigned', 'in_progress'])
            ->pluck('assigned_to')
            ->filter()
            ->unique()
            ->count();
        $availableHandymen = max(0, $activeHandymen - $busyHandymen);

        $healthScore = max(0, 100
            - min(45, $criticalUnassigned * 15)
            - min(25, $agingBacklog * 5)
            - min(15, $unassignedOpen * 3)
            - min(15, $openConcerns * 3));
        $healthLabel = $healthScore >= 85 ? 'Running well' : ($healthScore >= 65 ? 'Needs attention' : 'Action required');

        $executiveBrief = collect([
            [
                'tone' => $healthScore >= 85 ? 'stable' : ($healthScore >= 65 ? 'warning' : 'critical'),
                'title' => $healthScore >= 85 ? 'Operations are running well' : 'Some requests need your attention',
                'detail' => "{$unassignedOpen} ticket(s) still need staff, {$agingBacklog} ticket(s) have been open for more than 48 hours, and {$openConcerns} private concern(s) still need a response.",
            ],
            [
                'tone' => $trendPercent > 0 ? 'warning' : 'stable',
                'title' => $trendPercent > 0 ? 'More maintenance requests are coming in' : 'Maintenance requests are stable',
                'detail' => "Residents submitted {$lastSevenTicketCount} ticket(s) in the last 7 days. The next 7 days are estimated at {$forecastTicketCount}.",
            ],
            [
                'tone' => $topCategoryShare >= 40 ? 'warning' : 'stable',
                'title' => $topCategory ? ucfirst($topCategory) . ' is the most common issue' : 'No repeated issue detected yet',
                'detail' => $topCategory
                    ? "{$topCategoryCount} of {$recentTickets->count()} request(s) from the last 30 days are {$topCategory} related. Consider checking for a recurring cause."
                    : 'There are not enough recent maintenance requests to identify a recurring issue.',
            ],
        ]);

        $recommendations = collect();

        if ($criticalUnassigned > 0) {
            $recommendations->push([
                'level' => 'critical',
                'label' => 'Immediate',
                'title' => 'Assign critical maintenance requests',
                'detail' => "{$criticalUnassigned} high-priority ticket(s) still need an assigned handyman.",
                'url' => route('tickets.index'),
                'action' => 'Review tickets',
            ]);
        }

        if ($agingBacklog > 0) {
            $recommendations->push([
                'level' => 'warning',
                'label' => 'Backlog',
                'title' => 'Review aging open tickets',
                'detail' => "{$agingBacklog} ticket(s) have remained open for more than 48 hours.",
                'url' => route('tickets.index'),
                'action' => 'Open backlog',
            ]);
        }

        if ($openConcerns > 0) {
            $recommendations->push([
                'level' => 'warning',
                'label' => 'Resident reports',
                'title' => 'Respond to unresolved private concerns',
                'detail' => "{$openConcerns} private concern(s) still need an administrative response.",
                'url' => route('admin.concerns.index'),
                'action' => 'Review concerns',
            ]);
        }

        if ($forecastTicketCount > $lastSevenTicketCount) {
            $increase = $forecastTicketCount - $lastSevenTicketCount;
            $recommendations->push([
                'level' => 'planning',
                'label' => 'Forecast',
                'title' => 'Prepare for higher maintenance demand',
                'detail' => "The next seven days are projected to bring {$increase} more ticket(s) than the previous seven days.",
                'url' => route('tickets.index'),
                'action' => 'Plan workload',
            ]);
        }

        if ($busiestFacility) {
            $recommendations->push([
                'level' => 'planning',
                'label' => 'Utilization',
                'title' => "Check {$busiestFacility} readiness",
                'detail' => "{$busiestFacilityBookings} confirmed booking(s) make this the busiest facility for the next 30 days.",
                'url' => route('admin.bookings.calendar'),
                'action' => 'View calendar',
            ]);
        }

        if ($recommendations->isEmpty()) {
            $recommendations->push([
                'level' => 'stable',
                'label' => 'Stable',
                'title' => 'Maintain the current operations plan',
                'detail' => 'No urgent workload or facility pressure signals were detected today.',
                'url' => route('tickets.index'),
                'action' => 'Monitor tickets',
            ]);
        }

        $historyCount = $dailyTicketCounts->sum('count');
        $confidence = $historyCount >= 30 ? 'High' : ($historyCount >= 10 ? 'Moderate' : 'Early estimate');
        $confidenceDetail = match ($confidence) {
            'High' => "Based on {$historyCount} tickets across the last 28 days. The projection is trend-based and reliable enough for staffing preparation.",
            'Moderate' => "Based on {$historyCount} tickets across the last 28 days. Use this as an early planning signal, not a final workload commitment.",
            default => "Only {$historyCount} tickets are available in the recent history window. Treat this as directional until more data is collected.",
        };

        return [
            'forecastLabels' => $dailyTicketCounts
                ->take(-14)
                ->pluck('date')
                ->concat(collect(range(1, 7))->map(fn ($day) => $today->copy()->addDays($day)))
                ->map(fn ($date) => $date->format('M d'))
                ->values(),
            'actualTicketData' => $dailyTicketCounts->take(-14)->pluck('count')
                ->concat(array_fill(0, 7, null))
                ->values(),
            'forecastTicketData' => collect(array_fill(0, 13, null))
                ->push($dailyTicketCounts->last()['count'])
                ->concat($forecastCounts)
                ->values(),
            'forecastTicketCount' => $forecastTicketCount,
            'lastSevenTicketCount' => $lastSevenTicketCount,
            'trendPercent' => $trendPercent,
            'trendDirection' => $trendPercent > 0 ? 'up' : ($trendPercent < 0 ? 'down' : 'steady'),
            'healthScore' => $healthScore,
            'healthLabel' => $healthLabel,
            'executiveBrief' => $executiveBrief,
            'openTicketCount' => $openTicketCount,
            'unassignedOpen' => $unassignedOpen,
            'assignedActiveCount' => $assignedActiveCount,
            'inProgressCount' => $inProgressCount,
            'criticalUnassigned' => $criticalUnassigned,
            'agingBacklog' => $agingBacklog,
            'averageResolutionHours' => $averageResolutionHours,
            'completionTrendPercent' => $completionTrendPercent,
            'topCategory' => $topCategory ? ucfirst($topCategory) : null,
            'topCategoryCount' => $topCategoryCount,
            'topCategoryShare' => $topCategoryShare,
            'busiestFacility' => $busiestFacility,
            'busiestFacilityBookings' => $busiestFacilityBookings,
            'openConcerns' => $openConcerns,
            'activeHandymen' => $activeHandymen,
            'availableHandymen' => $availableHandymen,
            'statusBreakdownLabels' => ['Unassigned', 'Assigned', 'In Progress'],
            'statusBreakdownData' => [
                $unassignedOpen,
                $assignedActiveCount,
                $inProgressCount,
            ],
            'confidence' => $confidence,
            'confidenceDetail' => $confidenceDetail,
            'forecastModel' => '28-day moving trend model',
            'recommendations' => $recommendations->take(4),
        ];
    }

    private function getDeepAnalyticsData(array $filters = []): array
    {
        [$from, $to, $days] = $this->analyticsWindow($filters);
        $prevTo = $from->copy()->subSecond();
        $prevFrom = $prevTo->copy()->subDays($days - 1)->startOfDay();

        $resolvedTickets = $this->filteredTicketQuery($filters)
            ->whereIn('status', ['resolved', 'closed'])
            ->whereBetween('updated_at', [$from, $to])
            ->get(['created_at', 'updated_at', 'task_duration_minutes']);
        $totalCreated    = $this->filteredTicketQuery($filters)->whereBetween('created_at', [$from, $to])->count();
        $totalResolved   = $resolvedTickets->count();
        $resolutionRate  = $totalCreated > 0 ? round(($totalResolved / $totalCreated) * 100) : 0;
        $avgResolutionHours = $resolvedTickets->isEmpty() ? null : round(
            $resolvedTickets->avg(fn ($t) => ($t->task_duration_minutes ?? $t->created_at->diffInMinutes($t->updated_at)) / 60), 1
        );
        $totalBookings = $this->filteredBookingQuery($filters)->whereBetween('created_at', [$from, $to])->count();

        $prevCreated         = $this->filteredTicketQuery($filters)->whereBetween('created_at', [$prevFrom, $prevTo])->count();
        $prevResolvedTickets = $this->filteredTicketQuery($filters)
            ->whereIn('status', ['resolved', 'closed'])
            ->whereBetween('updated_at', [$prevFrom, $prevTo])
            ->get(['created_at', 'updated_at', 'task_duration_minutes']);
        $prevResolved       = $prevResolvedTickets->count();
        $prevResolutionRate = $prevCreated > 0 ? round(($prevResolved / $prevCreated) * 100) : 0;
        $prevAvgHours       = $prevResolvedTickets->isEmpty() ? null : round(
            $prevResolvedTickets->avg(fn ($t) => ($t->task_duration_minutes ?? $t->created_at->diffInMinutes($t->updated_at)) / 60), 1
        );
        $prevBookings = $this->filteredBookingQuery($filters)->whereBetween('created_at', [$prevFrom, $prevTo])->count();

        $currentCategoryCounts = $this->filteredTicketQuery($filters)
            ->whereBetween('created_at', [$from, $to])
            ->whereNotNull('category')
            ->select('category', DB::raw('count(*) as total'))
            ->groupBy('category')
            ->pluck('total', 'category');

        $previousCategoryCounts = $this->filteredTicketQuery($filters)
            ->whereBetween('created_at', [$prevFrom, $prevTo])
            ->whereNotNull('category')
            ->select('category', DB::raw('count(*) as total'))
            ->groupBy('category')
            ->pluck('total', 'category');

        $categoryShift = $currentCategoryCounts
            ->map(function ($current, $category) use ($previousCategoryCounts) {
                $previous = (int) ($previousCategoryCounts[$category] ?? 0);
                $change = $previous > 0 ? (int) round((($current - $previous) / $previous) * 100) : ($current > 0 ? 100 : 0);

                return [
                    'category' => ucfirst((string) $category),
                    'current' => (int) $current,
                    'previous' => $previous,
                    'change' => $change,
                ];
            })
            ->sortByDesc(fn ($row) => abs($row['change']))
            ->first();

        $funnelStages = [
            'Submitted'   => $this->filteredTicketQuery($filters)->whereBetween('created_at', [$from, $to])->count(),
            'Approved'    => $this->filteredTicketQuery($filters)->whereIn('status', ['approved', 'assigned', 'in_progress', 'resolved', 'closed'])->whereBetween('created_at', [$from, $to])->count(),
            'Assigned'    => $this->filteredTicketQuery($filters)->whereIn('status', ['assigned', 'in_progress', 'resolved', 'closed'])->whereBetween('created_at', [$from, $to])->count(),
            'In Progress' => $this->filteredTicketQuery($filters)->whereIn('status', ['in_progress', 'resolved', 'closed'])->whereBetween('created_at', [$from, $to])->count(),
            'Resolved'    => $this->filteredTicketQuery($filters)->whereIn('status', ['resolved', 'closed'])->whereBetween('created_at', [$from, $to])->count(),
        ];

        $dowRaw = $this->filteredTicketQuery($filters)
            ->whereBetween('created_at', [$from, $to])
            ->get(['created_at'])
            ->groupBy(fn ($ticket) => $ticket->created_at->dayOfWeek)
            ->map->count();
        $dowData = array_map(fn ($day) => (int) ($dowRaw[$day] ?? 0), range(0, 6));
        $topDowIndex = collect($dowData)->keys()->sortByDesc(fn ($index) => $dowData[$index])->first();
        $topDowLabels = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $topSubmissionDay = $topDowIndex !== null
            ? [
                'day' => $topDowLabels[$topDowIndex],
                'count' => (int) ($dowData[$topDowIndex] ?? 0),
            ]
            : null;

        $openNow    = MaintenanceTicket::whereIn('status', MaintenanceTicket::OPEN_STATUSES)->get(['created_at']);
        $ageBuckets = [
            '0–24h'  => $openNow->filter(fn ($t) => $t->created_at->diffInHours(now()) <= 24)->count(),
            '25–48h' => $openNow->filter(fn ($t) => $t->created_at->diffInHours(now()) > 24  && $t->created_at->diffInHours(now()) <= 48)->count(),
            '49–72h' => $openNow->filter(fn ($t) => $t->created_at->diffInHours(now()) > 48  && $t->created_at->diffInHours(now()) <= 72)->count(),
            '73h+'   => $openNow->filter(fn ($t) => $t->created_at->diffInHours(now()) > 72)->count(),
        ];

        $pipelineTickets = MaintenanceTicket::whereIn('status', ['resolved', 'closed'])
            ->whereBetween('updated_at', [$from, $to])
            ->whereNotNull('task_started_at')
            ->get(['created_at', 'task_started_at', 'updated_at', 'task_duration_minutes']);
        $avgPreWorkHours = $pipelineTickets->isEmpty() ? null : round(
            $pipelineTickets->avg(fn ($t) => $t->created_at->diffInHours($t->task_started_at)), 1
        );
        $avgWorkHours    = $pipelineTickets->isEmpty() ? null : round(
            $pipelineTickets->avg(fn ($t) => ($t->task_duration_minutes ?? $t->task_started_at->diffInMinutes($t->updated_at)) / 60), 1
        );
        $avgTotalCycleHours = ($avgPreWorkHours !== null && $avgWorkHours !== null)
            ? round($avgPreWorkHours + $avgWorkHours, 1)
            : null;

        $hotspotRows      = MaintenanceTicket::select('user_id', DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->take(8)
            ->get();
        $residentHotspots = $hotspotRows->map(function ($row) use ($from, $to) {
            $user     = User::find($row->user_id);
            $resolved = MaintenanceTicket::where('user_id', $row->user_id)
                ->whereIn('status', ['resolved', 'closed'])
                ->whereBetween('created_at', [$from, $to])
                ->count();
            $topCat   = MaintenanceTicket::where('user_id', $row->user_id)
                ->whereBetween('created_at', [$from, $to])
                ->whereNotNull('category')
                ->select('category', DB::raw('count(*) as c'))
                ->groupBy('category')
                ->orderByDesc('c')
                ->value('category');
            return ['name' => $user->name ?? 'Unknown', 'total' => $row->total, 'resolved' => $resolved, 'top_category' => $topCat ? ucfirst($topCat) : '—'];
        })->values();

        $handymen      = User::where('role', 'handyman')->get();
        $handymanStats = $handymen->map(function ($h) use ($from, $to) {
            $assigned        = MaintenanceTicket::where('assigned_to', $h->id)->whereBetween('created_at', [$from, $to])->count();
            $resolvedTickets = MaintenanceTicket::where('assigned_to', $h->id)
                ->whereIn('status', ['resolved', 'closed'])
                ->whereBetween('updated_at', [$from, $to])
                ->get(['created_at', 'updated_at', 'task_duration_minutes']);
            $resolved  = $resolvedTickets->count();
            $avgHours  = $resolvedTickets->isEmpty() ? null : round(
                $resolvedTickets->avg(fn ($t) => ($t->task_duration_minutes ?? $t->created_at->diffInMinutes($t->updated_at)) / 60), 1
            );
            $avgRating = MaintenanceTicket::where('assigned_to', $h->id)
                ->whereNotNull('satisfaction_rating')
                ->whereBetween('created_at', [$from, $to])
                ->avg('satisfaction_rating');

            return [
                'name' => $h->name,
                'assigned' => $assigned,
                'resolved' => $resolved,
                'avg_hours' => $avgHours,
                'avg_rating' => $avgRating ? number_format($avgRating, 1) . ' ★' : null,
            ];
        })->sortByDesc('resolved')->values();

        return compact(
            'totalCreated', 'totalResolved', 'resolutionRate', 'avgResolutionHours', 'totalBookings',
            'prevCreated', 'prevResolved', 'prevResolutionRate', 'prevAvgHours', 'prevBookings',
            'funnelStages', 'dowData', 'ageBuckets',
            'avgPreWorkHours', 'avgWorkHours', 'avgTotalCycleHours',
            'residentHotspots', 'handymanStats', 'categoryShift', 'topSubmissionDay'
        );
    }

    public function staffOverview()
    {
        $user = Auth::user();
        abort_unless($user->role === 'handyman', 403);

        return view('dashboard.handyman', $this->getStaffWorkspaceData($user));
    }

    public function staffQueue()
    {
        $user = Auth::user();
        abort_unless($user->role === 'handyman', 403);

        return view('handyman.queue', $this->getStaffWorkspaceData($user));
    }

    public function staffCompleted()
    {
        $user = Auth::user();
        abort_unless($user->role === 'handyman', 403);

        return view('handyman.completed', $this->getStaffWorkspaceData($user));
    }

    private function getStaffWorkspaceData(User $user): array
    {
        $myTickets = MaintenanceTicket::where('assigned_to', $user->id)
            ->whereNotIn('status', ['resolved', 'closed', 'cancelled', 'completed', 'rejected'])
            ->orderByRaw("CASE priority WHEN 'critical' THEN 1 WHEN 'medium' THEN 2 ELSE 3 END")
            ->orderBy('created_at', 'desc')
            ->get();

        $completedTickets = MaintenanceTicket::where('assigned_to', $user->id)
            ->whereIn('status', ['resolved', 'closed'])
            ->latest('updated_at')
            ->get();

        return [
            'myTickets' => $myTickets,
            'assignedTickets' => $myTickets->where('status', 'assigned')->count(),
            'inProgressTickets' => $myTickets->where('status', 'in_progress')->count(),
            'completedToday' => $completedTickets->filter(fn ($ticket) => $ticket->updated_at?->isToday())->count(),
            'averageTaskMinutes' => (int) round($completedTickets->whereNotNull('task_duration_minutes')->avg('task_duration_minutes') ?? 0),
            'urgentTickets' => $myTickets->filter(fn ($ticket) => $ticket->normalized_priority === 'critical')->count(),
            'urgentTicketsList' => $myTickets->filter(fn ($ticket) => $ticket->normalized_priority === 'critical'),
            'completedTickets' => $completedTickets,
        ];
    }
}
