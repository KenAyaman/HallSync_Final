<x-app-layout>
@php
$pct = fn($curr, $prev) => $prev > 0 ? round((($curr - $prev) / $prev) * 100) : ($curr > 0 ? 100 : 0);
@endphp

<div class="dash-root admin-ticket-page" style="display:flex; flex-direction:column; gap:24px;">

{{-- ═══════════════════════════════════════════════════════
     HEADER + DATE FILTER
═══════════════════════════════════════════════════════ --}}
<div class="relative overflow-hidden rounded-[36px] border border-[#3A342D]"
     style="background: linear-gradient(115deg, #1A1C1E 0%, #1F2023 38%, #24262B 62%, #2C2C2F 100%);
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);">
    <div class="absolute top-[-90px] right-[10%] w-[320px] h-[320px] rounded-full blur-3xl opacity-20"
         style="background: rgba(199,151,69,0.3);"></div>
    <div class="relative z-10 px-8 py-10 md:px-14 md:py-11">
        <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <div class="mb-3 flex items-center gap-3">
                    <span class="inline-block w-7 h-7 rounded-full" style="background: linear-gradient(135deg, #D6A85B, #B8842F);"></span>
                    <span class="text-[11px] tracking-[0.28em] uppercase font-bold" style="color: #D6A85B;">Admin · Data Analytics</span>
                </div>
                <h1 class="text-4xl md:text-5xl font-bold leading-tight mb-2"
                    style="font-family: 'Playfair Display', serif; color: #F8F3EA;">
                    Analytics <span style="color: #D6A85B;">Centre</span>
                </h1>
                <p class="text-sm" style="color: rgba(255,255,255,0.50);">
                    {{ $from->format('M d, Y') }} — {{ $to->format('M d, Y') }}
                    &nbsp;·&nbsp; {{ $periodDays }} day{{ $periodDays != 1 ? 's' : '' }}
                    &nbsp;·&nbsp; Compared to {{ $prevFrom->format('M d') }} – {{ $prevTo->format('M d') }}
                </p>
            </div>

            <form method="GET" action="{{ route('admin.analytics') }}"
                  class="flex flex-wrap items-end gap-2 rounded-2xl p-4"
                  style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.09);">
                <div class="flex flex-col gap-1">
                    <label class="text-[10px] uppercase tracking-widest font-bold" style="color: #D6A85B;">From</label>
                    <input type="date" name="from" value="{{ $from->format('Y-m-d') }}"
                           class="rounded-xl px-3 py-2 text-sm font-medium"
                           style="background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.14); color: #F5F0E9; outline:none;">
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-[10px] uppercase tracking-widest font-bold" style="color: #D6A85B;">To</label>
                    <input type="date" name="to" value="{{ $to->format('Y-m-d') }}"
                           class="rounded-xl px-3 py-2 text-sm font-medium"
                           style="background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.14); color: #F5F0E9; outline:none;">
                </div>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all"
                        style="background: #D6A85B; color: #1A1714;">Apply</button>
                <div class="flex gap-1.5">
                    @foreach([['7','7d'],['30','30d'],['90','90d'],['365','1yr']] as [$d,$label])
                        <a href="{{ route('admin.analytics', ['period' => $d]) }}"
                           class="px-3 py-2 rounded-xl text-xs font-semibold transition-all"
                           style="background: {{ $period==$d && !request('from') ? 'rgba(214,168,91,0.18)' : 'rgba(255,255,255,0.05)' }};
                                  color: {{ $period==$d && !request('from') ? '#D6A85B' : 'rgba(255,255,255,0.45)' }};
                                  border: 1px solid {{ $period==$d && !request('from') ? 'rgba(214,168,91,0.28)' : 'rgba(255,255,255,0.08)' }};">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     KPI CARDS WITH PERIOD-OVER-PERIOD COMPARISON
═══════════════════════════════════════════════════════ --}}
@php
$kpis = [
    ['label'=>'Tickets Created',  'curr'=>$totalCreated,    'prev'=>$prevCreated,    'fmt'=>'num',  'color'=>'#D6A85B', 'note'=>'submitted in period',    'higher_is'=>'neutral'],
    ['label'=>'Tickets Resolved', 'curr'=>$totalResolved,   'prev'=>$prevResolved,   'fmt'=>'num',  'color'=>'#5A9E6A', 'note'=>'closed in period',        'higher_is'=>'good'],
    ['label'=>'Resolution Rate',  'curr'=>$resolutionRate,  'prev'=>$prevResolutionRate,'fmt'=>'pct','color'=>$resolutionRate>=70?'#5A9E6A':'#E07060','note'=>'resolved ÷ created','higher_is'=>'good'],
    ['label'=>'Avg Resolution',   'curr'=>$avgResolutionHours,'prev'=>$prevAvgHours, 'fmt'=>'hrs',  'color'=>'#C79745', 'note'=>'hours to close',          'higher_is'=>'bad'],
    ['label'=>'Bookings',         'curr'=>$totalBookings,   'prev'=>$prevBookings,   'fmt'=>'num',  'color'=>'#7B9EC9', 'note'=>'facility reservations',   'higher_is'=>'neutral'],
];
@endphp
<div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
    @foreach($kpis as $kpi)
    @php
        $c = $kpi['curr'] ?? null;
        $p = $kpi['prev'] ?? null;
        $change = ($c !== null && $p !== null) ? $pct($c, $p) : null;
        $up = $change !== null && $change > 0;
        $down = $change !== null && $change < 0;
        $goodUp = $kpi['higher_is'] === 'good';
        $badUp  = $kpi['higher_is'] === 'bad';
        $trendColor = $change === null || $kpi['higher_is'] === 'neutral'
            ? 'rgba(255,255,255,0.35)'
            : (($up && $goodUp) || ($down && $badUp) ? '#5A9E6A' : (($up && $badUp) || ($down && $goodUp) ? '#E07060' : 'rgba(255,255,255,0.35)'));
        $display = $kpi['fmt'] === 'pct' ? ($c !== null ? $c.'%' : '—')
                 : ($kpi['fmt'] === 'hrs' ? ($c !== null ? $c.'h' : '—')
                 : ($c ?? '—'));
    @endphp
    <div class="rounded-2xl p-4 flex flex-col gap-2"
         style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07);">
        <div class="text-[10px] font-bold uppercase tracking-widest" style="color: rgba(255,255,255,0.38);">{{ $kpi['label'] }}</div>
        <div class="text-[2rem] font-bold leading-none" style="color: {{ $kpi['color'] }}; font-family:'Playfair Display',serif;">{{ $display }}</div>
        <div class="flex items-center gap-1.5 mt-auto">
            @if($change !== null)
                <span class="text-xs font-bold flex items-center gap-0.5" style="color: {{ $trendColor }};">
                    @if($up)↑@elseif($down)↓@endif {{ abs($change) }}%
                </span>
                <span class="text-[10px]" style="color: rgba(255,255,255,0.25);">vs prev period</span>
            @else
                <span class="text-[10px]" style="color: rgba(255,255,255,0.25);">{{ $kpi['note'] }}</span>
            @endif
        </div>
    </div>
    @endforeach
</div>

{{-- ═══════════════════════════════════════════════════════
     TICKET TREND (full width)
═══════════════════════════════════════════════════════ --}}
<div class="rounded-2xl p-6" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07);">
    <div class="flex items-start justify-between mb-5">
        <div>
            <div class="text-base font-semibold mb-0.5" style="color: #F5F0E9;">Ticket Volume Trend</div>
            <div class="text-xs" style="color: rgba(255,255,255,0.38);">Daily ticket submissions across the selected period</div>
        </div>
        <a href="{{ route('admin.analytics.export.tickets', ['from'=>$from->format('Y-m-d'),'to'=>$to->format('Y-m-d')]) }}"
           class="flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-semibold shrink-0"
           style="background: rgba(214,168,91,0.10); color: #D6A85B; border: 1px solid rgba(214,168,91,0.20);">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Export CSV
        </a>
    </div>
    <div style="height: 200px;"><canvas id="trendChart"></canvas></div>
</div>

{{-- ═══════════════════════════════════════════════════════
     FUNNEL + DAY OF WEEK
═══════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

    {{-- Ticket Funnel --}}
    <div class="rounded-2xl p-6" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07);">
        <div class="text-base font-semibold mb-1" style="color: #F5F0E9;">Ticket Funnel</div>
        <div class="text-xs mb-5" style="color: rgba(255,255,255,0.38);">Where tickets drop off — from submission to resolution</div>
        @php
            $funnelColors = ['#C79745','#7B9EC9','#9B7EC8','#5A8A8A','#5A9E6A'];
            $top = max(1, $funnelStages['Submitted'] ?? 1);
            $stageKeys = array_keys($funnelStages);
        @endphp
        <div class="flex flex-col gap-2">
            @foreach($funnelStages as $stage => $count)
            @php
                $idx = array_search($stage, $stageKeys);
                $pctWidth = max(8, round(($count / $top) * 100));
                $convPct = $idx > 0 ? ($funnelStages[$stageKeys[$idx-1]] > 0 ? round(($count / $funnelStages[$stageKeys[$idx-1]]) * 100) : 0) : 100;
                $color = $funnelColors[$idx];
            @endphp
            <div class="flex items-center gap-3">
                <div class="w-24 shrink-0 text-right">
                    <span class="text-xs font-medium" style="color: rgba(255,255,255,0.55);">{{ $stage }}</span>
                </div>
                <div class="flex-1 flex items-center gap-2">
                    <div class="relative h-8 rounded-lg overflow-hidden flex-1" style="background: rgba(255,255,255,0.05);">
                        <div class="absolute inset-y-0 left-0 rounded-lg flex items-center px-3 transition-all"
                             style="width: {{ $pctWidth }}%; background: {{ $color }}22; border-right: 2px solid {{ $color }};">
                            <span class="text-xs font-bold" style="color: {{ $color }}; white-space:nowrap;">{{ $count }}</span>
                        </div>
                    </div>
                    @if($idx > 0)
                        <div class="w-12 shrink-0 text-center">
                            <span class="text-[11px] font-bold" style="color: {{ $convPct >= 70 ? '#5A9E6A' : ($convPct >= 40 ? '#C79745' : '#E07060') }};">
                                {{ $convPct }}%
                            </span>
                        </div>
                    @else
                        <div class="w-12 shrink-0"></div>
                    @endif
                </div>
            </div>
            @if(!$loop->last)
            <div class="flex items-center gap-3">
                <div class="w-24 shrink-0"></div>
                <div class="flex-1 flex justify-start pl-2">
                    <div class="w-px h-3" style="background: rgba(255,255,255,0.12); margin-left: 4px;"></div>
                </div>
            </div>
            @endif
            @endforeach
        </div>
        <p class="text-[10px] mt-4" style="color: rgba(255,255,255,0.25);">% shows conversion from previous stage</p>
    </div>

    {{-- Day of Week --}}
    <div class="rounded-2xl p-6" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07);">
        <div class="text-base font-semibold mb-1" style="color: #F5F0E9;">Submission by Day</div>
        <div class="text-xs mb-5" style="color: rgba(255,255,255,0.38);">Which days of the week generate the most tickets</div>
        <div style="height: 200px;"><canvas id="dowChart"></canvas></div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     AGE BUCKETS + STATUS BREAKDOWN
═══════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

    {{-- Open Ticket Age Buckets --}}
    <div class="rounded-2xl p-6" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07);">
        <div class="text-base font-semibold mb-1" style="color: #F5F0E9;">Open Ticket Age</div>
        <div class="text-xs mb-5" style="color: rgba(255,255,255,0.38);">Current snapshot — how long unresolved tickets have been open</div>
        @php
            $ageColors = ['#5A9E6A','#C79745','#D4894A','#E07060'];
            $ageMax = max(1, max(array_values($ageBuckets)));
            $ageKeys = array_keys($ageBuckets);
        @endphp
        <div class="flex flex-col gap-4">
            @foreach($ageBuckets as $label => $count)
            @php $ai = array_search($label, $ageKeys); @endphp
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full" style="background: {{ $ageColors[$ai] }};"></span>
                        <span class="text-xs font-medium" style="color: rgba(255,255,255,0.65);">{{ $label }}</span>
                    </div>
                    <span class="text-sm font-bold" style="color: {{ $ageColors[$ai] }};">{{ $count }}</span>
                </div>
                <div class="h-2 rounded-full overflow-hidden" style="background: rgba(255,255,255,0.06);">
                    <div class="h-full rounded-full transition-all"
                         style="width: {{ round(($count / $ageMax) * 100) }}%; background: {{ $ageColors[$ai] }};"></div>
                </div>
            </div>
            @endforeach
        </div>
        @php $critical = ($ageBuckets['49–72h'] ?? 0) + ($ageBuckets['73h+'] ?? 0); @endphp
        @if($critical > 0)
        <div class="mt-5 rounded-xl px-4 py-3 flex items-center gap-3"
             style="background: rgba(224,112,96,0.10); border: 1px solid rgba(224,112,96,0.20);">
            <span style="color: #E07060;">⚠</span>
            <span class="text-xs" style="color: rgba(255,255,255,0.65);">
                <strong style="color: #E07060;">{{ $critical }} ticket{{ $critical!=1?'s':'' }}</strong> have been open for more than 48 hours
            </span>
        </div>
        @endif
    </div>

    {{-- Status Breakdown --}}
    <div class="rounded-2xl p-6" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07);">
        <div class="text-base font-semibold mb-1" style="color: #F5F0E9;">Status Breakdown</div>
        <div class="text-xs mb-5" style="color: rgba(255,255,255,0.38);">Distribution of tickets created in this period by current status</div>
        @php
        $statusColorMap = ['pending_approval'=>'#C79745','approved'=>'#7B9EC9','assigned'=>'#9B7EC8','in_progress'=>'#5A8A8A','resolved'=>'#5A9E6A','closed'=>'#888','rejected'=>'#E07060','cancelled'=>'#b06060'];
        @endphp
        <div style="height: 160px; margin-bottom: 16px;"><canvas id="statusChart"></canvas></div>
        <div class="grid grid-cols-2 gap-x-4 gap-y-2">
            @foreach($statusCounts as $status => $count)
            <div class="flex items-center justify-between text-xs">
                <div class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full shrink-0" style="background: {{ $statusColorMap[$status] ?? '#888' }};"></span>
                    <span style="color: rgba(255,255,255,0.55);">{{ ucfirst(str_replace('_',' ',$status)) }}</span>
                </div>
                <span class="font-semibold ml-2" style="color: #F5F0E9;">{{ $count }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     CATEGORY + FACILITY
═══════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <div class="rounded-2xl p-6" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07);">
        <div class="text-base font-semibold mb-1" style="color: #F5F0E9;">Tickets by Category</div>
        <div class="text-xs mb-5" style="color: rgba(255,255,255,0.38);">Which maintenance types are most common</div>
        <div style="height: 210px;"><canvas id="categoryChart"></canvas></div>
    </div>
    <div class="rounded-2xl p-6" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07);">
        <div class="flex items-start justify-between mb-1">
            <div class="text-base font-semibold" style="color: #F5F0E9;">Facility Utilization</div>
            <a href="{{ route('admin.analytics.export.bookings', ['from'=>$from->format('Y-m-d'),'to'=>$to->format('Y-m-d')]) }}"
               class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold shrink-0"
               style="background: rgba(214,168,91,0.10); color: #D6A85B; border: 1px solid rgba(214,168,91,0.20);">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Export CSV
            </a>
        </div>
        <div class="text-xs mb-5" style="color: rgba(255,255,255,0.38);">Bookings per facility in selected range</div>
        <div style="height: 210px;"><canvas id="facilityChart"></canvas></div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     PIPELINE BOTTLENECK
═══════════════════════════════════════════════════════ --}}
@if($avgTotalCycleHours !== null)
<div class="rounded-2xl p-6" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07);">
    <div class="text-base font-semibold mb-1" style="color: #F5F0E9;">Resolution Pipeline</div>
    <div class="text-xs mb-6" style="color: rgba(255,255,255,0.38);">Average time spent in each stage for resolved tickets — where is time being lost?</div>
    <div class="flex flex-col md:flex-row items-center gap-0 md:gap-0">

        {{-- Stage: Submitted → Work Start --}}
        <div class="flex flex-col items-center gap-2 flex-1">
            <div class="w-12 h-12 rounded-full flex items-center justify-center"
                 style="background: rgba(199,151,69,0.15); border: 2px solid rgba(199,151,69,0.40);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#C79745" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/></svg>
            </div>
            <span class="text-xs font-semibold" style="color: rgba(255,255,255,0.65);">Submitted</span>
        </div>

        <div class="flex flex-col items-center gap-1 flex-1 md:flex-none md:w-32 my-2 md:my-0">
            <div class="text-base font-bold" style="color: {{ $avgPreWorkHours > 24 ? '#E07060' : ($avgPreWorkHours > 8 ? '#C79745' : '#5A9E6A') }};">
                {{ $avgPreWorkHours }}h
            </div>
            <div class="w-full h-px md:w-full hidden md:block" style="background: rgba(255,255,255,0.15);"></div>
            <div class="text-[10px] text-center" style="color: rgba(255,255,255,0.35);">admin + queue wait</div>
        </div>

        {{-- Stage: Work in Progress --}}
        <div class="flex flex-col items-center gap-2 flex-1">
            <div class="w-12 h-12 rounded-full flex items-center justify-center"
                 style="background: rgba(90,138,90,0.15); border: 2px solid rgba(90,138,90,0.40);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5A9E6A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <span class="text-xs font-semibold" style="color: rgba(255,255,255,0.65);">Work Started</span>
        </div>

        <div class="flex flex-col items-center gap-1 flex-1 md:flex-none md:w-32 my-2 md:my-0">
            <div class="text-base font-bold" style="color: {{ $avgWorkHours > 12 ? '#E07060' : ($avgWorkHours > 4 ? '#C79745' : '#5A9E6A') }};">
                {{ $avgWorkHours }}h
            </div>
            <div class="w-full h-px hidden md:block" style="background: rgba(255,255,255,0.15);"></div>
            <div class="text-[10px] text-center" style="color: rgba(255,255,255,0.35);">active repair time</div>
        </div>

        {{-- Stage: Resolved --}}
        <div class="flex flex-col items-center gap-2 flex-1">
            <div class="w-12 h-12 rounded-full flex items-center justify-center"
                 style="background: rgba(90,158,106,0.15); border: 2px solid rgba(90,158,106,0.40);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5A9E6A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <span class="text-xs font-semibold" style="color: rgba(255,255,255,0.65);">Resolved</span>
        </div>
    </div>

    <div class="mt-6 pt-5 border-t flex flex-wrap gap-6" style="border-color: rgba(255,255,255,0.07);">
        <div>
            <div class="text-[10px] uppercase tracking-widest font-bold mb-1" style="color: rgba(255,255,255,0.30);">Total Cycle Time</div>
            <div class="text-2xl font-bold" style="color: #D6A85B; font-family:'Playfair Display',serif;">{{ $avgTotalCycleHours }}h</div>
        </div>
        <div>
            <div class="text-[10px] uppercase tracking-widest font-bold mb-1" style="color: rgba(255,255,255,0.30);">Pre-Work (queue)</div>
            <div class="text-2xl font-bold" style="color: {{ $avgPreWorkHours > 24 ? '#E07060' : '#C79745' }};">{{ $avgPreWorkHours }}h</div>
        </div>
        <div>
            <div class="text-[10px] uppercase tracking-widest font-bold mb-1" style="color: rgba(255,255,255,0.30);">Active Work</div>
            <div class="text-2xl font-bold" style="color: #5A9E6A;">{{ $avgWorkHours }}h</div>
        </div>
        <div class="ml-auto self-center">
            @if($avgPreWorkHours > $avgWorkHours)
            <div class="rounded-xl px-4 py-2.5" style="background: rgba(224,112,96,0.10); border: 1px solid rgba(224,112,96,0.20);">
                <div class="text-xs font-semibold" style="color: #E07060;">Bottleneck: Queue Wait</div>
                <div class="text-[11px] mt-0.5" style="color: rgba(255,255,255,0.45);">Tickets wait longer to start than they take to fix</div>
            </div>
            @else
            <div class="rounded-xl px-4 py-2.5" style="background: rgba(90,158,106,0.10); border: 1px solid rgba(90,158,106,0.20);">
                <div class="text-xs font-semibold" style="color: #5A9E6A;">Pipeline looks healthy</div>
                <div class="text-[11px] mt-0.5" style="color: rgba(255,255,255,0.45);">Work starts faster than it takes to complete</div>
            </div>
            @endif
        </div>
    </div>
</div>
@endif

{{-- ═══════════════════════════════════════════════════════
     RESIDENT HOTSPOTS
═══════════════════════════════════════════════════════ --}}
@if($residentHotspots->isNotEmpty())
<div class="rounded-2xl overflow-hidden" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07);">
    <div class="px-6 py-5 border-b flex items-center justify-between" style="border-color: rgba(255,255,255,0.07);">
        <div>
            <div class="text-base font-semibold" style="color: #F5F0E9;">Resident Hotspots</div>
            <div class="text-xs mt-0.5" style="color: rgba(255,255,255,0.38);">Top submitters — residents with recurring or high-volume requests</div>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr style="background: rgba(255,255,255,0.02);">
                    <th class="text-left px-6 py-3 text-[10px] font-bold uppercase tracking-widest" style="color: rgba(255,255,255,0.30);">#</th>
                    <th class="text-left px-6 py-3 text-[10px] font-bold uppercase tracking-widest" style="color: rgba(255,255,255,0.30);">Resident</th>
                    <th class="text-center px-6 py-3 text-[10px] font-bold uppercase tracking-widest" style="color: rgba(255,255,255,0.30);">Submitted</th>
                    <th class="text-center px-6 py-3 text-[10px] font-bold uppercase tracking-widest" style="color: rgba(255,255,255,0.30);">Resolved</th>
                    <th class="text-center px-6 py-3 text-[10px] font-bold uppercase tracking-widest" style="color: rgba(255,255,255,0.30);">Resolution %</th>
                    <th class="text-center px-6 py-3 text-[10px] font-bold uppercase tracking-widest" style="color: rgba(255,255,255,0.30);">Top Category</th>
                </tr>
            </thead>
            <tbody>
                @foreach($residentHotspots as $i => $r)
                @php $rate = $r['total'] > 0 ? round(($r['resolved'] / $r['total']) * 100) : 0; @endphp
                <tr style="border-top: 1px solid rgba(255,255,255,0.05);">
                    <td class="px-6 py-4 text-xs font-bold" style="color: rgba(255,255,255,0.25);">{{ $i+1 }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold shrink-0"
                                 style="background: rgba(199,151,69,0.12); color: #D6A85B; border: 1px solid rgba(199,151,69,0.20);">
                                {{ strtoupper(substr($r['name'],0,1)) }}
                            </div>
                            <span class="font-medium" style="color: #F5F0E9;">{{ $r['name'] }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center font-bold" style="color: #D6A85B;">{{ $r['total'] }}</td>
                    <td class="px-6 py-4 text-center font-semibold" style="color: #5A9E6A;">{{ $r['resolved'] }}</td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <div class="w-16 h-1.5 rounded-full overflow-hidden" style="background: rgba(255,255,255,0.07);">
                                <div class="h-full rounded-full" style="width:{{ $rate }}%; background: {{ $rate>=70?'#5A9E6A':($rate>=40?'#C79745':'#E07060') }};"></div>
                            </div>
                            <span class="text-xs font-bold w-8" style="color: {{ $rate>=70?'#5A9E6A':($rate>=40?'#C79745':'#E07060') }};">{{ $rate }}%</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold"
                              style="background: rgba(199,151,69,0.10); color: #C79745; border: 1px solid rgba(199,151,69,0.18);">
                            {{ $r['top_category'] }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- ═══════════════════════════════════════════════════════
     HANDYMAN PERFORMANCE
═══════════════════════════════════════════════════════ --}}
<div class="rounded-2xl overflow-hidden" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07);">
    <div class="px-6 py-5 border-b" style="border-color: rgba(255,255,255,0.07);">
        <div class="text-base font-semibold" style="color: #F5F0E9;">Handyman Performance</div>
        <div class="text-xs mt-0.5" style="color: rgba(255,255,255,0.38);">Staff productivity, completion rate, and average resolution time in the selected period</div>
    </div>
    @if($handymanStats->isEmpty())
        <div class="px-6 py-10 text-center text-sm" style="color: rgba(255,255,255,0.30);">No handymen found.</div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr style="background: rgba(255,255,255,0.02);">
                    <th class="text-left px-6 py-3 text-[10px] font-bold uppercase tracking-widest" style="color: rgba(255,255,255,0.30);">Staff</th>
                    <th class="text-center px-6 py-3 text-[10px] font-bold uppercase tracking-widest" style="color: rgba(255,255,255,0.30);">Assigned</th>
                    <th class="text-center px-6 py-3 text-[10px] font-bold uppercase tracking-widest" style="color: rgba(255,255,255,0.30);">Resolved</th>
                    <th class="text-center px-6 py-3 text-[10px] font-bold uppercase tracking-widest" style="color: rgba(255,255,255,0.30);">Completion Rate</th>
                    <th class="text-center px-6 py-3 text-[10px] font-bold uppercase tracking-widest" style="color: rgba(255,255,255,0.30);">Avg Resolution</th>
                </tr>
            </thead>
            <tbody>
                @foreach($handymanStats as $h)
                @php $rate = $h['assigned'] > 0 ? round(($h['resolved'] / $h['assigned']) * 100) : 0; @endphp
                <tr style="border-top: 1px solid rgba(255,255,255,0.05);">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold shrink-0"
                                 style="background: rgba(214,168,91,0.12); color: #D6A85B; border: 1px solid rgba(214,168,91,0.20);">
                                {{ strtoupper(substr($h['name'],0,1)) }}
                            </div>
                            <span class="font-medium" style="color: #F5F0E9;">{{ $h['name'] }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center font-semibold" style="color: rgba(255,255,255,0.65);">{{ $h['assigned'] }}</td>
                    <td class="px-6 py-4 text-center font-bold" style="color: #5A9E6A;">{{ $h['resolved'] }}</td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <div class="w-20 h-1.5 rounded-full overflow-hidden" style="background: rgba(255,255,255,0.07);">
                                <div class="h-full rounded-full" style="width:{{ $rate }}%; background: {{ $rate>=70?'#5A9E6A':($rate>=40?'#C79745':'#E07060') }};"></div>
                            </div>
                            <span class="text-xs font-bold w-8 text-right" style="color: {{ $rate>=70?'#5A9E6A':($rate>=40?'#C79745':'#E07060') }};">{{ $rate }}%</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center text-sm" style="color: rgba(255,255,255,0.55);">
                        {{ $h['avg_hours'] !== null ? $h['avg_hours'].'h' : '—' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

</div>{{-- end dash-root --}}

<style>
@media (max-width: 768px) {
    .admin-main-content [class*="rounded-2xl"] {
        border-radius: 16px !important;
    }
    .admin-main-content .grid {
        gap: 12px !important;
    }
    .admin-main-content form[action="{{ route('admin.analytics') }}"] {
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: 10px !important;
        width: 100% !important;
    }
    .admin-main-content form[action="{{ route('admin.analytics') }}"] label,
    .admin-main-content form[action="{{ route('admin.analytics') }}"] button {
        width: 100% !important;
    }
    .admin-main-content canvas {
        min-height: 260px;
    }
    .admin-main-content .overflow-x-auto {
        overflow: visible !important;
    }
    .admin-main-content .overflow-x-auto table,
    .admin-main-content .overflow-x-auto thead,
    .admin-main-content .overflow-x-auto tbody,
    .admin-main-content .overflow-x-auto tr,
    .admin-main-content .overflow-x-auto td {
        display: block;
        width: 100%;
    }
    .admin-main-content .overflow-x-auto table {
        min-width: 0 !important;
    }
    .admin-main-content .overflow-x-auto thead {
        position: absolute;
        width: 1px;
        height: 1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
    }
    .admin-main-content .overflow-x-auto tbody {
        display: grid;
        gap: 12px;
        padding: 12px;
    }
    .admin-main-content .overflow-x-auto tr {
        display: grid;
        gap: 8px;
        padding: 14px;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.035);
    }
    .admin-main-content .overflow-x-auto td {
        padding: 0 !important;
        border: 0 !important;
        text-align: left !important;
    }
    .admin-main-content .overflow-x-auto td:first-child {
        color: rgba(255, 255, 255, 0.42) !important;
        font-size: 0.74rem !important;
        font-weight: 800 !important;
    }
    .admin-main-content .overflow-x-auto td:not(:first-child)::before {
        display: block;
        margin-bottom: 3px;
        color: rgba(255, 255, 255, 0.38);
        font-size: 0.66rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }
    .admin-main-content .overflow-x-auto td:nth-child(2)::before {
        content: "Name";
    }
    .admin-main-content .overflow-x-auto td:nth-child(3)::before {
        content: "Submitted / Assigned";
    }
    .admin-main-content .overflow-x-auto td:nth-child(4)::before {
        content: "Resolved";
    }
    .admin-main-content .overflow-x-auto td:nth-child(5)::before {
        content: "Performance";
    }
    .admin-main-content .overflow-x-auto td:nth-child(6)::before {
        content: "Category";
    }
    .admin-main-content .overflow-x-auto .flex {
        justify-content: flex-start !important;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
Chart.defaults.color = 'rgba(255,255,255,0.45)';
Chart.defaults.font  = { family: "'Inter',sans-serif", size: 11 };

const grid  = 'rgba(255,255,255,0.06)';
const ticks = 'rgba(255,255,255,0.30)';
const gold  = '#C79745';

// Trend
new Chart(document.getElementById('trendChart'), {
    type: 'line',
    data: {
        labels: @json($trendLabels),
        datasets: [{
            data: @json($trendData),
            borderColor: gold,
            backgroundColor: 'rgba(199,151,69,0.08)',
            borderWidth: 2,
            pointRadius: @json(count($trendLabels)) > 45 ? 0 : 3,
            pointBackgroundColor: gold,
            tension: 0.38,
            fill: true,
        }],
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ' ' + ctx.parsed.y + ' tickets' } } },
        scales: {
            x: { grid: { color: grid }, ticks: { color: ticks, maxTicksLimit: 12 } },
            y: { grid: { color: grid }, ticks: { color: ticks, stepSize: 1 }, beginAtZero: true },
        },
    },
});

// Day of week
const maxDow = Math.max(...@json($dowData), 1);
new Chart(document.getElementById('dowChart'), {
    type: 'bar',
    data: {
        labels: @json($dowLabels),
        datasets: [{
            data: @json($dowData),
            backgroundColor: @json($dowData).map(v => `rgba(199,151,69,${0.25 + 0.65*(v/maxDow)})`),
            borderRadius: 8,
            borderSkipped: false,
        }],
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false }, ticks: { color: ticks } },
            y: { grid: { color: grid }, ticks: { color: ticks, stepSize: 1 }, beginAtZero: true },
        },
    },
});

// Status donut
@php
$sLabels = $statusCounts->keys()->map(fn($s)=>ucfirst(str_replace('_',' ',$s)))->values()->toJson();
$sData   = $statusCounts->values()->toJson();
$sColors = $statusCounts->keys()->map(fn($s)=>['pending_approval'=>'#C79745','approved'=>'#7B9EC9','assigned'=>'#9B7EC8','in_progress'=>'#5A8A8A','resolved'=>'#5A9E6A','closed'=>'#666','rejected'=>'#E07060','cancelled'=>'#b06060'][$s]??'#888')->values()->toJson();
@endphp
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: { labels: {!! $sLabels !!}, datasets: [{ data: {!! $sData !!}, backgroundColor: {!! $sColors !!}, borderWidth: 0, hoverOffset: 6 }] },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, cutout: '65%' },
});

// Category
new Chart(document.getElementById('categoryChart'), {
    type: 'bar',
    data: {
        labels: @json($categoryLabels),
        datasets: [{ data: @json($categoryData), backgroundColor: 'rgba(199,151,69,0.50)', borderColor: gold, borderWidth: 1, borderRadius: 6 }],
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false }, ticks: { color: ticks } },
            y: { grid: { color: grid }, ticks: { color: ticks, stepSize: 1 }, beginAtZero: true },
        },
    },
});

// Facility
new Chart(document.getElementById('facilityChart'), {
    type: 'bar',
    data: {
        labels: @json($spaceLabels),
        datasets: [{ data: @json($spaceData), backgroundColor: 'rgba(123,158,201,0.50)', borderColor: '#7B9EC9', borderWidth: 1, borderRadius: 6 }],
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false }, ticks: { color: ticks } },
            y: { grid: { color: grid }, ticks: { color: ticks, stepSize: 1 }, beginAtZero: true },
        },
    },
});
</script>
</x-app-layout>

