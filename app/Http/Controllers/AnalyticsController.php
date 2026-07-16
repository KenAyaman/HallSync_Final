<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MaintenanceTicket;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        [$from, $to, $period] = $this->parseDateRange($request);

        $periodDays = max(1, (int) $from->diffInDays($to) + 1);
        $prevTo     = $from->copy()->subSecond();
        $prevFrom   = $prevTo->copy()->subDays($periodDays - 1)->startOfDay();

        // ── Current period KPIs ──────────────────────────────────────────
        $totalCreated    = MaintenanceTicket::whereBetween('created_at', [$from, $to])->count();
        $resolvedTickets = MaintenanceTicket::whereIn('status', ['resolved', 'closed'])
            ->whereBetween('updated_at', [$from, $to])
            ->get(['created_at', 'updated_at', 'task_duration_minutes']);
        $totalResolved   = $resolvedTickets->count();
        $resolutionRate  = $totalCreated > 0 ? round(($totalResolved / $totalCreated) * 100) : 0;
        $avgResolutionHours = $resolvedTickets->isEmpty() ? null : round(
            $resolvedTickets->avg(fn ($t) => ($t->task_duration_minutes ?? $t->created_at->diffInMinutes($t->updated_at)) / 60), 1
        );
        $totalBookings = Booking::whereBetween('created_at', [$from, $to])->count();

        // ── Previous period KPIs (comparison) ───────────────────────────
        $prevCreated         = MaintenanceTicket::whereBetween('created_at', [$prevFrom, $prevTo])->count();
        $prevResolvedTickets = MaintenanceTicket::whereIn('status', ['resolved', 'closed'])
            ->whereBetween('updated_at', [$prevFrom, $prevTo])
            ->get(['created_at', 'updated_at', 'task_duration_minutes']);
        $prevResolved        = $prevResolvedTickets->count();
        $prevResolutionRate  = $prevCreated > 0 ? round(($prevResolved / $prevCreated) * 100) : 0;
        $prevAvgHours        = $prevResolvedTickets->isEmpty() ? null : round(
            $prevResolvedTickets->avg(fn ($t) => ($t->task_duration_minutes ?? $t->created_at->diffInMinutes($t->updated_at)) / 60), 1
        );
        $prevBookings        = Booking::whereBetween('created_at', [$prevFrom, $prevTo])->count();

        // ── Ticket trend (daily counts) ──────────────────────────────────
        $trendCounts = MaintenanceTicket::selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->whereBetween('created_at', [$from, $to])
            ->groupByRaw('DATE(created_at)')
            ->pluck('total', 'day');
        $trendLabels = [];
        $trendData   = [];
        for ($i = 0; $i < $periodDays; $i++) {
            $date          = $from->copy()->addDays($i);
            $trendLabels[] = $periodDays <= 31 ? $date->format('M d') : $date->format('M \'y');
            $trendData[]   = (int) ($trendCounts[$date->toDateString()] ?? 0);
        }

        // ── Ticket funnel ────────────────────────────────────────────────
        $funnelStages = [
            'Submitted'   => MaintenanceTicket::whereBetween('created_at', [$from, $to])->count(),
            'Approved'    => MaintenanceTicket::whereIn('status', ['approved', 'assigned', 'in_progress', 'resolved', 'closed'])->whereBetween('created_at', [$from, $to])->count(),
            'Assigned'    => MaintenanceTicket::whereIn('status', ['assigned', 'in_progress', 'resolved', 'closed'])->whereBetween('created_at', [$from, $to])->count(),
            'In Progress' => MaintenanceTicket::whereIn('status', ['in_progress', 'resolved', 'closed'])->whereBetween('created_at', [$from, $to])->count(),
            'Resolved'    => MaintenanceTicket::whereIn('status', ['resolved', 'closed'])->whereBetween('created_at', [$from, $to])->count(),
        ];

        // ── Day of week pattern ──────────────────────────────────────────
        $dowRaw    = MaintenanceTicket::query()
            ->whereBetween('created_at', [$from, $to])
            ->get(['created_at'])
            ->groupBy(fn ($ticket) => $ticket->created_at->dayOfWeek)
            ->map->count();
        $dowLabels = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        $dowData   = array_map(fn ($d) => (int) ($dowRaw[$d] ?? 0), range(0, 6));

        // ── Open ticket age buckets (current snapshot, not date-filtered) ─
        $openNow    = MaintenanceTicket::whereIn('status', MaintenanceTicket::OPEN_STATUSES)->get(['created_at']);
        $ageBuckets = [
            '0–24h'  => $openNow->filter(fn ($t) => $t->created_at->diffInHours(now()) <= 24)->count(),
            '25–48h' => $openNow->filter(fn ($t) => $t->created_at->diffInHours(now()) > 24  && $t->created_at->diffInHours(now()) <= 48)->count(),
            '49–72h' => $openNow->filter(fn ($t) => $t->created_at->diffInHours(now()) > 48  && $t->created_at->diffInHours(now()) <= 72)->count(),
            '73h+'   => $openNow->filter(fn ($t) => $t->created_at->diffInHours(now()) > 72)->count(),
        ];

        // ── Category & facility ──────────────────────────────────────────
        $categories     = MaintenanceTicket::select('category', DB::raw('count(*) as total'))
            ->whereNotNull('category')
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();
        $categoryLabels = $categories->pluck('category')->map(fn ($c) => ucfirst($c))->all();
        $categoryData   = $categories->pluck('total')->all();

        $spaces      = Booking::select('facility_name', DB::raw('count(*) as total'))
            ->whereNotNull('facility_name')
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('facility_name')
            ->orderByDesc('total')
            ->get();
        $spaceLabels = $spaces->pluck('facility_name')->all();
        $spaceData   = $spaces->pluck('total')->all();

        // ── Status breakdown ─────────────────────────────────────────────
        $statusCounts = MaintenanceTicket::select('status', DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('status')
            ->pluck('total', 'status');

        // ── Pipeline bottleneck (avg hours per stage) ────────────────────
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

        // ── Resident hotspots ────────────────────────────────────────────
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
            return [
                'name'         => $user->name ?? 'Unknown',
                'total'        => $row->total,
                'resolved'     => $resolved,
                'top_category' => $topCat ? ucfirst($topCat) : '—',
            ];
        })->values();

        // ── Handyman performance ─────────────────────────────────────────
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
            return ['name' => $h->name, 'assigned' => $assigned, 'resolved' => $resolved, 'avg_hours' => $avgHours];
        })->sortByDesc('resolved')->values();

        return view('analytics.index', compact(
            'from', 'to', 'period', 'prevFrom', 'prevTo', 'periodDays',
            'totalCreated', 'totalResolved', 'resolutionRate', 'avgResolutionHours', 'totalBookings',
            'prevCreated', 'prevResolved', 'prevResolutionRate', 'prevAvgHours', 'prevBookings',
            'trendLabels', 'trendData',
            'funnelStages',
            'dowLabels', 'dowData',
            'ageBuckets',
            'categoryLabels', 'categoryData',
            'spaceLabels', 'spaceData',
            'statusCounts',
            'avgPreWorkHours', 'avgWorkHours', 'avgTotalCycleHours',
            'residentHotspots', 'handymanStats'
        ));
    }

    public function exportTickets(Request $request)
    {
        [$from, $to] = $this->parseDateRange($request);
        $tickets     = MaintenanceTicket::with(['user', 'assignedTo'])
            ->whereBetween('created_at', [$from, $to])
            ->orderBy('created_at', 'desc')
            ->get();
        $filename = 'tickets_' . $from->format('Y-m-d') . '_to_' . $to->format('Y-m-d') . '.csv';

        return response()->stream(function () use ($tickets) {
            $h = fopen('php://output', 'w');
            fputcsv($h, ['ID', 'Title', 'Category', 'Priority', 'Status', 'Resident', 'Assigned To', 'Location', 'Created At', 'Resolved At', 'Duration (hrs)']);
            foreach ($tickets as $t) {
                $mins = $t->task_duration_minutes ?? (in_array($t->status, ['resolved', 'closed']) ? $t->created_at->diffInMinutes($t->updated_at) : null);
                fputcsv($h, [
                    $this->csvSafe($t->ticket_id ?? $t->id),
                    $this->csvSafe($t->title),
                    $this->csvSafe($t->category ?? ''),
                    $this->csvSafe($t->priority ?? ''),
                    $this->csvSafe($t->status),
                    $this->csvSafe($t->user->name ?? ''),
                    $this->csvSafe($t->assignedTo->name ?? ''),
                    $this->csvSafe($t->location ?? ''),
                    $t->created_at->format('Y-m-d H:i'),
                    in_array($t->status, ['resolved', 'closed']) ? $t->updated_at->format('Y-m-d H:i') : '',
                    $mins !== null ? round($mins / 60, 1) : '',
                ]);
            }
            fclose($h);
        }, 200, ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename=\"{$filename}\""]);
    }

    public function exportBookings(Request $request)
    {
        [$from, $to] = $this->parseDateRange($request);
        $bookings    = Booking::with('user')
            ->whereBetween('created_at', [$from, $to])
            ->orderBy('booking_date', 'desc')
            ->get();
        $filename = 'bookings_' . $from->format('Y-m-d') . '_to_' . $to->format('Y-m-d') . '.csv';

        return response()->stream(function () use ($bookings) {
            $h = fopen('php://output', 'w');
            fputcsv($h, ['ID', 'Resident', 'Facility', 'Booking Date', 'End Time', 'Status', 'Created At']);
            foreach ($bookings as $b) {
                fputcsv($h, [
                    $b->id,
                    $this->csvSafe($b->user->name ?? ''),
                    $this->csvSafe($b->facility_name ?? ''),
                    $b->booking_date ? Carbon::parse($b->booking_date)->format('Y-m-d') : '',
                    $b->end_time ?? '',
                    $this->csvSafe($b->status),
                    $b->created_at->format('Y-m-d H:i'),
                ]);
            }
            fclose($h);
        }, 200, ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename=\"{$filename}\""]);
    }

    private function parseDateRange(Request $request): array
    {
        $requestedPeriod = (int) $request->input('period', 30);
        $allowedPeriods = [7, 14, 30, 60, 90];
        $period = in_array($requestedPeriod, $allowedPeriods, true) ? $requestedPeriod : 30;

        $from   = $request->filled('from')
            ? Carbon::parse($request->get('from'))->startOfDay()
            : Carbon::now()->subDays($period - 1)->startOfDay();
        $to     = $request->filled('to')
            ? Carbon::parse($request->get('to'))->endOfDay()
            : Carbon::now()->endOfDay();
        return [$from, $to, $period];
    }

    private function csvSafe(mixed $value): string
    {
        $value = (string) $value;

        return preg_match('/^[=+\-@\t\r]/', $value) ? "'{$value}" : $value;
    }
}
