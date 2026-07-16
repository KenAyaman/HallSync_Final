<x-app-layout>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

@php
    $predictive = $predictiveAnalytics ?? [];
    $forecastDirection = $predictive['trendDirection'] ?? 'steady';
    $forecastPercent = abs($predictive['trendPercent'] ?? 0);
    $statusLabel = fn ($status) => str($status ?? 'open')->replace('_', ' ')->title();
    $statusTone = function ($status) {
        return match ($status) {
            'completed', 'resolved', 'approved' => 'success',
            'rejected', 'cancelled', 'critical' => 'danger',
            'pending', 'pending_approval', 'received' => 'warning',
            'assigned', 'in_progress' => 'info',
            default => 'neutral',
        };
    };
    $activityActionLabel = function ($action) {
        return match ($action) {
            'account_logged_in' => 'Account signed in',
            'booking.created' => 'Booking created',
            'ticket.created' => 'Ticket submitted',
            'ticket.assigned' => 'Ticket assigned',
            'ticket.approved' => 'Ticket approved',
            'ticket.rejected' => 'Ticket rejected',
            'announcement.created' => 'Announcement posted',
            'community.approved' => 'Community post approved',
            'community.rejected' => 'Community post rejected',
            default => str($action ?? 'system event')->replace(['_', '.'], ' ')->title(),
        };
    };
    $healthTone = ($predictive['healthScore'] ?? 0) >= 85
        ? 'success'
        : (($predictive['healthScore'] ?? 0) >= 65 ? 'warning' : 'danger');
@endphp

<div class="hs-dashboard" data-dashboard-workspace>
    <header class="hs-topbar">
        <div>
            <p class="hs-eyebrow">HallSync Admin</p>
            <h1>Good {{ now()->hour < 12 ? 'Morning' : (now()->hour < 18 ? 'Afternoon' : 'Evening') }}, <span>{{ Auth::user()->name }}</span></h1>
            <p class="hs-date">{{ now()->format('l, F j, Y') }}</p>
        </div>
        <div class="hs-live-clock" aria-label="Live dashboard clock">
            <strong id="live-clock"></strong>
            <span id="dashboard-live-status" data-dashboard-live-status><i></i><b>Live</b></span>
        </div>
    </header>

    <nav class="hs-tabs" aria-label="Dashboard sections" role="tablist">
        <button class="hs-tab is-active" type="button" role="tab" aria-selected="true" aria-controls="command-center" data-dashboard-tab="command-center">
            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="8"/><circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3"/></svg>
            Command Center
        </button>
        <button class="hs-tab" type="button" role="tab" aria-selected="false" aria-controls="analytics-trends" data-dashboard-tab="analytics-trends">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 19V5M4 19h16"/><path d="m7 15 4-5 3 3 5-7"/></svg>
            Analytics &amp; Trends
        </button>
        <button class="hs-tab" type="button" role="tab" aria-selected="false" aria-controls="predictive-ops" data-dashboard-tab="predictive-ops">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3v3M12 18v3M4.2 6.2l2.1 2.1M17.7 15.7l2.1 2.1M3 12h3M18 12h3M4.2 17.8l2.1-2.1M17.7 8.3l2.1-2.1"/><circle cx="12" cy="12" r="4"/></svg>
            Forecast &amp; Staff
        </button>
        <button class="hs-tab" type="button" role="tab" aria-selected="false" aria-controls="activity-logs" data-dashboard-tab="activity-logs">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 4h12v16H6z"/><path d="M9 8h6M9 12h6M9 16h4"/></svg>
            Activity Logs
        </button>
    </nav>

    <section class="hs-view is-active" id="command-center" role="tabpanel" data-dashboard-panel="command-center">
        <div class="hs-section-heading">
            <div>
                <p class="hs-eyebrow">Operations Intelligence</p>
                <h2>Today&apos;s priorities <span class="da-live-radar-dot" aria-hidden="true"></span></h2>
            </div>
        </div>

        <section class="da-intelligence-hub">
            <div class="da-intelligence-hub-status">
                <h3>Priority Command Center</h3>
                <span class="hs-health hs-tone-{{ $healthTone }}">Health score {{ $predictive['healthScore'] ?? 0 }}/100 | {{ $predictive['healthLabel'] ?? 'Needs attention' }}</span>
            </div>

            <div class="hs-command-grid">
                <section class="hs-card hs-alert-panel">
                    <div class="hs-card-heading">
                        <div>
                            <p class="hs-eyebrow">Signals</p>
                            <h3>Operational Alerts</h3>
                        </div>
                        <span>{{ collect($predictive['executiveBrief'] ?? [])->count() }} signals</span>
                    </div>
                    <div class="hs-alert-list" data-progressive-list data-progressive-limit="4">
                        @foreach($predictive['executiveBrief'] ?? [] as $brief)
                            <article class="hs-alert hs-alert-{{ $brief['tone'] }}" data-progressive-item>
                                <div class="hs-alert-symbol">{{ $brief['tone'] === 'critical' ? '!' : ($brief['tone'] === 'warning' ? '!' : 'OK') }}</div>
                                <div>
                                    <h4>{{ $brief['title'] }}</h4>
                                    <p>{{ $brief['detail'] }}</p>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>

                <section class="hs-card hs-actions-panel">
                    <div class="hs-card-heading">
                        <div>
                            <p class="hs-eyebrow">Prescriptive Analytics</p>
                            <h3>Recommended Actions</h3>
                        </div>
                        <span>{{ collect($predictive['recommendations'] ?? [])->count() }} actions</span>
                    </div>
                    <div class="hs-action-list" data-progressive-list data-progressive-limit="4">
                        @foreach($predictive['recommendations'] ?? [] as $recommendation)
                            <article class="hs-action hs-action-{{ $recommendation['level'] }}" data-progressive-item>
                                <div>
                                    <span>{{ $recommendation['label'] }}</span>
                                    <h4>{{ $recommendation['title'] }}</h4>
                                    <p>{{ $recommendation['detail'] }}</p>
                                </div>
                                <a href="{{ $recommendation['url'] }}"><span>{{ $recommendation['action'] }}</span><b>-></b></a>
                            </article>
                        @endforeach
                    </div>
                </section>
            </div>
        </section>

        <div class="hs-diagnostics">
            <article class="hs-diagnostic-card hs-diagnostic-card-amber">
                <span class="hs-diagnostic-label">Unassigned Workload</span>
                <strong class="hs-diagnostic-value" data-dashboard-metric="unassignedOpen">{{ $predictive['unassignedOpen'] ?? 0 }}</strong>
                <p><b>Routing</b><span>Awaiting staff assignment</span></p>
            </article>
            <article class="hs-diagnostic-card hs-diagnostic-card-green">
                <span class="hs-diagnostic-label">Resident Concerns</span>
                <strong class="hs-diagnostic-value" data-dashboard-metric="openConcerns">{{ $predictive['openConcerns'] ?? 0 }}</strong>
                <p><b>Response</b><span>Awaiting admin follow-up</span></p>
            </article>
            <article class="hs-diagnostic-card hs-diagnostic-card-blue">
                <span class="hs-diagnostic-label">Available Staff</span>
                <strong class="hs-diagnostic-value" data-dashboard-metric="availableHandymen">{{ $predictive['availableHandymen'] ?? 0 }}/{{ $predictive['activeHandymen'] ?? 0 }}</strong>
                <p><b>Coverage</b><span>Ready for open work</span></p>
            </article>
            <article class="hs-diagnostic-card hs-diagnostic-card-amber">
                <span class="hs-diagnostic-label">Category Hotspot</span>
                <strong class="hs-diagnostic-value">{{ $predictive['topCategoryShare'] ?? 0 }}%</strong>
                <p><b>{{ str($predictive['topCategory'] ?? 'No category')->title() }}</b><span>Most common issue</span></p>
            </article>
        </div>
    </section>

    <section class="hs-view" id="analytics-trends" role="tabpanel" data-dashboard-panel="analytics-trends" hidden>
        @php $da = $deepAnalytics ?? []; @endphp
        <div class="hs-section-heading">
            <div>
                <p class="hs-eyebrow">Operational Analytics</p>
                <h2>Operational Analytics</h2>
            </div>
            <div class="hs-dashboard-controls" aria-label="Analytics dashboard controls">
                <a class="hs-export-btn" href="{{ route('admin.analytics.export.tickets') }}">Export Tickets</a>
                <a class="hs-export-btn" href="{{ route('admin.analytics.export.bookings') }}">Export Bookings</a>
            </div>
        </div>

        @php
            $summarySpaceLabels = $spaceLabels ?? [];
            $summarySpaceData = $spaceData ?? [];
            $summaryTopSpaceIndex = null;
            $summaryTopSpaceCount = 0;
            foreach ($summarySpaceData as $idx => $count) {
                if ($summaryTopSpaceIndex === null || $count > $summaryTopSpaceCount) {
                    $summaryTopSpaceIndex = $idx;
                    $summaryTopSpaceCount = $count;
                }
            }
            $summaryTopSpace = $summaryTopSpaceIndex !== null ? ($summarySpaceLabels[$summaryTopSpaceIndex] ?? null) : null;
            $summaryQueueWait = $da['avgPreWorkHours'] ?? null;
            $summaryWorkTime = $da['avgWorkHours'] ?? null;
        @endphp
        <section class="da-executive-summary-box" aria-labelledby="da-executive-summary-title">
            <div class="da-executive-summary-head">
                <div>
                    <h3 id="da-executive-summary-title">Operational Summary</h3>
                </div>
                <strong class="hs-health hs-tone-{{ $healthTone }}">{{ $predictive['healthLabel'] ?? 'Needs attention' }}</strong>
            </div>
            <ul class="da-executive-summary-list">
                <li>
                    @if($summaryTopSpace && $summaryTopSpaceCount > 0)
                        <strong>{{ $summaryTopSpace }}</strong> is the busiest facility with {{ $summaryTopSpaceCount }} confirmed booking{{ $summaryTopSpaceCount === 1 ? '' : 's' }}. Check room readiness, cleaning, and access before peak use.
                    @else
                        Facility bookings are currently light, so no room needs extra preparation yet.
                    @endif
                </li>
                <li>
                    @if(($predictive['topCategoryShare'] ?? 0) >= 35)
                        <strong>{{ str($predictive['topCategory'] ?? 'Maintenance')->title() }}</strong> is taking up {{ $predictive['topCategoryShare'] ?? 0 }}% of recent tickets. Inspect that issue type first because it is the clearest complaint hotspot.
                    @elseif(($predictive['topCategoryShare'] ?? 0) > 0)
                        The most common complaint type is <strong>{{ str($predictive['topCategory'] ?? 'maintenance')->title() }}</strong>, but it is not dominating the queue yet.
                    @else
                        No complaint category is standing out yet.
                    @endif
                </li>
                <li>
                    @if($summaryQueueWait !== null && $summaryQueueWait > 24)
                        Tickets wait about <strong>{{ $summaryQueueWait }} hours</strong> before work starts. Assign new requests sooner to reduce resident waiting time.
                    @elseif($summaryQueueWait !== null && $summaryWorkTime !== null && $summaryQueueWait > $summaryWorkTime)
                        Queue time is longer than repair time, so the main delay is assignment rather than handyman work.
                    @elseif($summaryQueueWait !== null)
                        Queue wait is about <strong>{{ $summaryQueueWait }} hours</strong>, which is manageable if staffing stays steady.
                    @else
                        There is not enough timing data yet to judge queue delay.
                    @endif
                </li>
            </ul>
        </section>

        <section class="hs-analytics-chapter" aria-labelledby="analytics-summary-kpis">
            <div class="hs-chapter-heading">
                <span>Chapter 1</span>
                <h3 id="analytics-summary-kpis">Descriptive Analytics</h3>
            </div>

        {{-- Period comparison KPIs --}}
        @php
        $pct = fn($c,$p) => $p > 0 ? round((($c-$p)/$p)*100) : ($c > 0 ? 100 : 0);
        $daKpis = [
            ['label'=>'Tickets Created',  'curr'=>$da['totalCreated']??0,   'prev'=>$da['prevCreated']??0,   'fmt'=>'num','accent'=>'amber','higher_is'=>'neutral'],
            ['label'=>'Tickets Resolved', 'curr'=>$da['totalResolved']??0,  'prev'=>$da['prevResolved']??0,  'fmt'=>'num','accent'=>'green','higher_is'=>'good'],
            ['label'=>'Resolution Rate',  'curr'=>$da['resolutionRate']??0, 'prev'=>$da['prevResolutionRate']??0,'fmt'=>'pct','accent'=>'amber','higher_is'=>'good'],
            ['label'=>'Bookings',         'curr'=>$da['totalBookings']??0,  'prev'=>$da['prevBookings']??0,  'fmt'=>'num','accent'=>'blue','higher_is'=>'neutral'],
        ];
        @endphp
        <div class="da-kpi-grid">
            @foreach($daKpis as $k)
            @php
                $c=$k['curr']; $p=$k['prev'];
                $ch=($c!==null&&$p!==null)?$pct($c,$p):null;
                $up=$ch!==null&&$ch>0; $dn=$ch!==null&&$ch<0;
                $isGood=$k['higher_is']==='good'; $isBad=$k['higher_is']==='bad';
                $tc=$ch===null||$k['higher_is']==='neutral'?'da-trend-neutral':(($up&&$isGood)||($dn&&$isBad)?'da-trend-up-good':(($up&&$isBad)||($dn&&$isGood)?'da-trend-up-bad':'da-trend-neutral'));
                $disp=$k['fmt']==='pct'?($c!==null?$c.'%':'-'):($k['fmt']==='hrs'?($c!==null?$c.'h':'-'):($c??'-'));
            @endphp
            <article class="da-kpi-card da-kpi-{{ $k['accent'] }}">
                <span class="da-kpi-label">{{ $k['label'] }}</span>
                <strong class="da-kpi-value">{{ $disp }}</strong>
                <div class="da-kpi-foot">
                    @if($ch!==null)
                        <span class="da-trend {{ $tc }}">{{ $up?'+':'-' }}{{ abs($ch) }}%</span>
                        <span class="da-kpi-vs">vs prev 30d</span>
                    @else
                        <span class="da-kpi-vs">last 30 days</span>
                    @endif
                </div>
            </article>
            @endforeach
        </div>

        </section>
        <hr class="hs-analytics-divider">

        {{-- Existing charts --}}
        <div class="hs-analytics-grid">
            <article class="hs-card hs-chart-wide">
                <div class="hs-card-heading">
                    <div><h3>Ticket Volume</h3><p>Daily requests created over the last 30 days.</p></div>
                    <div class="hs-chart-heading-actions">
                        <a class="da-drill-link" href="{{ route('tickets.index') }}?source=analytics&chart=ticket-volume">Review records &rarr;</a>
                    </div>
                </div>
                <div class="hs-chart-body"><canvas id="ticketTrendChart" role="img" aria-label="Bar chart showing daily ticket volume over the last 30 days"></canvas></div>
                @php
                    $tvData       = collect($ticketTrendData ?? array_fill(0, 30, 0))->values()->all();
                    $tvLabels     = collect($ticketTrendLabels ?? [])->values()->all();
                    $tvTotal      = array_sum($tvData);

                    $tvMax        = $tvTotal > 0 ? max($tvData) : 0;
                    $tvMaxIdx     = $tvMax > 0 ? array_search($tvMax, $tvData) : null;
                    $tvMaxLabel   = ($tvMaxIdx !== null && isset($tvLabels[$tvMaxIdx]))
                                    ? $tvLabels[$tvMaxIdx] : null;

                    $tvNonZero    = array_filter($tvData, fn($v) => $v > 0);
                    $tvMin        = count($tvNonZero) > 1 ? min($tvNonZero) : null;
                    $tvMinIdx     = ($tvMin !== null)
                                    ? array_search($tvMin, $tvData) : null;
                    $tvMinLabel   = ($tvMinIdx !== null && isset($tvLabels[$tvMinIdx]))
                                    ? $tvLabels[$tvMinIdx] : null;

                    $tvPrev       = $da['prevCreated'] ?? 0;
                    $tvCurr       = $da['totalCreated'] ?? 0;
                    $tvDiff       = $tvCurr - $tvPrev;
                    $tvResolved   = $da['totalResolved'] ?? 0;
                    $tvActiveDays = count($tvNonZero);
                @endphp
                <ul class="hs-chart-stats">
                    <li><span>Total tickets</span><strong>{{ $tvTotal }}</strong></li>
                    <li><span>Resolved</span><strong>{{ $tvResolved }}</strong></li>
                    @if($tvMaxLabel)
                    <li><span>Busiest day</span><strong>{{ $tvMaxLabel }} ({{ $tvMax }})</strong></li>
                    @endif
                    <li><span>Active days</span><strong>{{ $tvActiveDays }}</strong></li>
                    @if($tvMinLabel && $tvMinLabel !== $tvMaxLabel)
                    <li><span>Quietest day</span><strong>{{ $tvMinLabel }} ({{ $tvMin }})</strong></li>
                    @endif
                </ul>
            </article>
            <article class="hs-card">
                <div class="hs-card-heading">
                    <div><h3>Tickets by Category</h3><p>Issue types creating the most demand.</p></div>
                    <a class="da-drill-link" href="{{ route('tickets.index') }}?source=analytics&chart=category">Review records &rarr;</a>
                </div>
                <div class="hs-chart-body"><canvas id="categoryBarChart" role="img" aria-label="Bar chart showing ticket volume by maintenance category"></canvas></div>
                @php
                    $catData       = collect($categoryData ?? [])->values()->all();
                    $catLabelList  = collect($categoryLabels ?? [])->values()->all();
                    $catTotal      = array_sum($catData);

                    $catMax        = $catTotal > 0 ? max($catData) : 0;
                    $catMaxIdx     = $catMax > 0
                                     ? array_search($catMax, $catData) : null;
                    $catTopName    = ($catMaxIdx !== null
                                     && isset($catLabelList[$catMaxIdx]))
                                     ? $catLabelList[$catMaxIdx] : 'N/A';
                    $catTopShare   = $catTotal > 0 && $catMax > 0
                                     ? round(($catMax / $catTotal) * 100) : 0;

                    $catNonZero    = array_filter($catData, fn($v) => $v > 0);
                    $catMin        = count($catNonZero) > 1 ? min($catNonZero) : null;
                    $catMinIdx     = ($catMin !== null)
                                     ? array_search($catMin, $catData) : null;
                    $catLowName    = ($catMinIdx !== null
                                     && isset($catLabelList[$catMinIdx])
                                     && $catMinIdx !== $catMaxIdx)
                                     ? $catLabelList[$catMinIdx] : null;

                    $catTracked    = count($catData);
                    $catWithData   = count($catNonZero);
                    $catZero       = $catTracked - $catWithData;
                @endphp
                <ul class="hs-chart-stats">
                    <li><span>Highest category</span><strong>{{ $catTopName }}</strong></li>
                    <li><span>Top category tickets</span><strong>{{ $catMax }}</strong></li>
                    <li><span>Of all category tickets</span><strong>{{ $catTopShare }}%</strong></li>
                    <li><span>Categories tracked</span><strong>{{ $catTracked }}</strong></li>
                    @if($catLowName && $catMin !== null)
                    <li><span>Lowest category</span><strong>{{ $catLowName }} ({{ $catMin }})</strong></li>
                    @endif
                    @if($catZero > 0)
                    <li>
                        <span>No tickets yet</span>
                        <strong>{{ $catZero }} {{ $catZero === 1 ? 'category' : 'categories' }}</strong>
                    </li>
                    @endif
                </ul>
            </article>
            <article class="hs-card">
                <div class="hs-card-heading">
                    <div><h3>Bookings by Space</h3><p>Confirmed reservation activity by facility.</p></div>
                    <a class="da-drill-link" href="{{ route('admin.bookings.calendar') }}?source=analytics&chart=booking-space">Open calendar &rarr;</a>
                </div>
                <div class="hs-chart-body"><canvas id="bookingSpaceChart" role="img" aria-label="Bar chart showing confirmed bookings per facility space"></canvas></div>
                @php
                    $bsLabels      = collect($spaceLabels ?? [])->values()->all();
                    $bsData        = collect($spaceData ?? [])->values()->all();
                    $bsTotal       = array_sum($bsData);

                    $bsMax         = $bsTotal > 0 ? max($bsData) : 0;
                    $bsMaxIdx      = $bsMax > 0
                                     ? array_search($bsMax, $bsData) : null;
                    $bsTopName     = ($bsMaxIdx !== null
                                     && isset($bsLabels[$bsMaxIdx]))
                                     ? $bsLabels[$bsMaxIdx] : 'N/A';
                    $bsTopShare    = $bsTotal > 0 && $bsMax > 0
                                     ? round(($bsMax / $bsTotal) * 100) : 0;

                    $bsNonZero     = array_filter($bsData, fn($v) => $v > 0);
                    $bsMin         = count($bsNonZero) > 1 ? min($bsNonZero) : null;
                    $bsMinIdx      = ($bsMin !== null)
                                     ? array_search($bsMin, $bsData) : null;
                    $bsLowName     = ($bsMinIdx !== null
                                     && isset($bsLabels[$bsMinIdx])
                                     && $bsMinIdx !== $bsMaxIdx)
                                     ? $bsLabels[$bsMinIdx] : null;

                    $bsSpaceCount  = count($bsData);
                    $bsWithData    = count($bsNonZero);
                    $bsZero        = $bsSpaceCount - $bsWithData;

                    $bsPrev        = $da['prevBookings'] ?? 0;
                    $bsCurr        = $da['totalBookings'] ?? 0;
                    $bsDiff        = $bsCurr - $bsPrev;
                @endphp
                <ul class="hs-chart-stats">
                    <li><span>Total bookings</span><strong>{{ $bsTotal }}</strong></li>
                    <li><span>Top space bookings</span><strong>{{ $bsMax }}</strong></li>
                    <li><span>Busiest space</span><strong>{{ $bsTopName }}</strong></li>
                    <li><span>Of all bookings</span><strong>{{ $bsTopShare }}%</strong></li>
                    @if($bsLowName && $bsMin !== null)
                    <li><span>Lightest space</span><strong>{{ $bsLowName }} ({{ $bsMin }})</strong></li>
                    @endif
                    @if($bsZero > 0)
                    <li>
                        <span>No bookings yet</span>
                        <strong>{{ $bsZero }} {{ $bsZero === 1 ? 'space' : 'spaces' }}</strong>
                    </li>
                    @endif
                </ul>
            </article>
        </div>
        <hr class="hs-analytics-divider">

</section>

    <section class="hs-view" id="predictive-ops" role="tabpanel" data-dashboard-panel="predictive-ops" hidden>
        <div class="hs-section-heading">
            <div>
                <p class="hs-eyebrow">Predictive Operations</p>
                <h2>Plan the next seven days</h2>
            </div>
        </div>

        <section class="hs-analytics-chapter" aria-labelledby="predictive-forecast-chapter">
            <div class="hs-chapter-heading">
                <span>Chapter 1</span>
                <h3 id="predictive-forecast-chapter">Predictive Analytics</h3>
            </div>
        <div class="hs-predictive-grid">
            <article class="hs-card hs-forecast-card hs-brown-card">
                <div class="hs-card-heading">
                    <div><h3>Ticket Demand Forecast</h3><p>Recent history with a seven-day projection.</p></div>
                    <div class="hs-chart-heading-actions">
                        <a class="da-drill-link" href="{{ route('tickets.index') }}">Review assignments &rarr;</a>
                    </div>
                </div>
                <div class="hs-chart-body hs-chart-tall"><canvas id="ticketForecastChart" role="img" aria-label="Line chart forecasting expected ticket volume for the coming week"></canvas></div>
                @php
                    $forecastCount = $predictive['forecastTicketCount'] ?? 0;
                    $forecastLabelsList = collect($predictive['forecastLabels'] ?? [])->values()->all();
                    $forecastDataList = collect($predictive['forecastTicketData'] ?? [])->values()->all();
                    $forecastNonZero = array_filter($forecastDataList, fn($v) => $v !== null && $v > 0);
                    $forecastPeak = count($forecastNonZero) > 0 ? max($forecastNonZero) : 0;
                    $forecastPeakIdx = $forecastPeak > 0 ? array_search($forecastPeak, $forecastDataList) : null;
                    $forecastPeakLabel = ($forecastPeakIdx !== null && isset($forecastLabelsList[$forecastPeakIdx]))
                        ? $forecastLabelsList[$forecastPeakIdx] : null;
                    $forecastActiveDays = count($forecastNonZero);
                    $agingBacklog = $predictive['agingBacklog'] ?? 0;
                    $availableStaff = $predictive['availableHandymen'] ?? 0;
                    $activeStaff = $predictive['activeHandymen'] ?? 0;
                @endphp
                <ul class="hs-chart-stats">
                    <li><span>Next 7 days <button class="hs-help-term" type="button" aria-label="About Next 7 days: Estimated new tickets expected during the next seven calendar days." data-tooltip="Estimated new tickets expected during the next seven calendar days."><svg viewBox="0 0 12 12" aria-hidden="true"><path d="M4.2 4.25a1.85 1.85 0 1 1 2.35 1.78c-.72.24-1.05.63-1.05 1.22v.15"/><path d="M5.5 9.35h.01"/></svg></button></span><strong>{{ $forecastCount }}</strong></li>
                    <li><span>Aging backlog <button class="hs-help-term" type="button" aria-label="About Aging backlog: Open tickets that have been waiting for a while." data-tooltip="Open tickets that have been waiting for a while."><svg viewBox="0 0 12 12" aria-hidden="true"><path d="M4.2 4.25a1.85 1.85 0 1 1 2.35 1.78c-.72.24-1.05.63-1.05 1.22v.15"/><path d="M5.5 9.35h.01"/></svg></button></span><strong>{{ $agingBacklog }}</strong></li>
                    @if($forecastPeakLabel)
                    <li><span>Peak forecast <button class="hs-help-term" type="button" aria-label="About Peak forecast: The forecasted day with the highest expected ticket count." data-tooltip="The forecasted day with the highest expected ticket count."><svg viewBox="0 0 12 12" aria-hidden="true"><path d="M4.2 4.25a1.85 1.85 0 1 1 2.35 1.78c-.72.24-1.05.63-1.05 1.22v.15"/><path d="M5.5 9.35h.01"/></svg></button></span><strong>{{ $forecastPeakLabel }} ({{ $forecastPeak }})</strong></li>
                    @endif
                    <li><span>Active staff <button class="hs-help-term" type="button" aria-label="About Active staff: Available staff compared with all active staff accounts." data-tooltip="Available staff compared with all active staff accounts."><svg viewBox="0 0 12 12" aria-hidden="true"><path d="M4.2 4.25a1.85 1.85 0 1 1 2.35 1.78c-.72.24-1.05.63-1.05 1.22v.15"/><path d="M5.5 9.35h.01"/></svg></button></span><strong>{{ $availableStaff }}/{{ $activeStaff }}</strong></li>
                    <li><span>Trend <button class="hs-help-term" type="button" aria-label="About Trend: Shows whether the number of tickets is going up or down." data-tooltip="Shows whether the number of tickets is going up or down."><svg viewBox="0 0 12 12" aria-hidden="true"><path d="M4.2 4.25a1.85 1.85 0 1 1 2.35 1.78c-.72.24-1.05.63-1.05 1.22v.15"/><path d="M5.5 9.35h.01"/></svg></button></span><strong>{{ ucfirst($forecastDirection) }} {{ $forecastPercent }}%</strong></li>
                    <li><span>Forecast days <button class="hs-help-term" type="button" aria-label="About Forecast days: Upcoming days expected to have new tickets." data-tooltip="Upcoming days expected to have new tickets."><svg viewBox="0 0 12 12" aria-hidden="true"><path d="M4.2 4.25a1.85 1.85 0 1 1 2.35 1.78c-.72.24-1.05.63-1.05 1.22v.15"/><path d="M5.5 9.35h.01"/></svg></button></span><strong>{{ $forecastActiveDays }}</strong></li>
                </ul>
            </article>
        </div>
        </section>
        <hr class="hs-analytics-divider">

        {{-- Handyman Performance --}}
        <article class="hs-card da-card da-diagnostic-card hs-brown-card">
            <div class="hs-card-heading">
                <div><h3>Handyman Performance</h3><p>Staff productivity, completion rate, and average resolution time — last 30 days.</p></div>
            </div>
            <div class="da-table-wrap da-table-scroll">
                <table class="da-table">
                    <thead><tr><th>Staff</th><th>Assigned</th><th>Resolved</th><th>Avg Rating</th><th>Completion Rate</th><th>Avg Resolution</th></tr></thead>
                    <tbody>
                        @forelse($da['handymanStats'] ?? [] as $h)
                        @php $hRate=$h['assigned']>0?round(($h['resolved']/$h['assigned'])*100):0; @endphp
                        <tr>
                            <td><div class="da-person"><div class="da-avatar">{{ strtoupper(substr($h['name'],0,1)) }}</div><span>{{ $h['name'] }}</span></div></td>
                            <td class="da-td-muted">{{ $h['assigned'] }}</td>
                            <td class="da-td-green">{{ $h['resolved'] }}</td>
                            <td class="da-td-muted">{{ $h['avg_rating'] ?? '—' }}</td>
                            <td>
                                <div class="da-bar-inline">
                                    <div class="da-bar-track"><div class="da-bar-fill" style="width:{{ $hRate }}%; background:{{ $hRate>=70?'var(--hs-green)':($hRate>=40?'var(--hs-amber)':'var(--hs-red)') }};"></div></div>
                                    <span style="color:{{ $hRate>=70?'var(--hs-green)':($hRate>=40?'var(--hs-amber)':'var(--hs-red)') }};">{{ $hRate }}%</span>
                                </div>
                            </td>
                            <td class="da-td-muted">{{ $h['avg_hours']!==null?$h['avg_hours'].'h':'—' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">
                                <div class="da-empty-micro-card">✨ System nominal. No diagnostic anomalies or hotspots recorded for this period.</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="da-diagnostic-footer">
                <a href="{{ route('admin.analytics') }}">View Full Diagnostic Log &rarr;</a>
            </div>
        </article>
    </section>

    <section class="hs-view" id="activity-logs" role="tabpanel" data-dashboard-panel="activity-logs" hidden>
        <div class="hs-section-heading">
            <div>
                <p class="hs-eyebrow">Activity Logs</p>
                <h2>Ticket and audit activity</h2>
            </div>
            <a class="hs-button" href="{{ route('tickets.index') }}">View all tickets</a>
        </div>
        <section class="hs-card hs-log-card hs-brown-card">
            <div class="hs-card-heading">
                <div><h3>Ticket activity queue</h3><p>Recent maintenance requests with priority and current handling state.</p></div>
            </div>
            <div class="hs-log-list" data-progressive-list data-progressive-limit="3">
                @forelse($recentTickets ?? [] as $ticket)
                    <article class="hs-log-row" data-progressive-item>
                        <div class="hs-log-main">
                            <div>
                                <h3>{{ $ticket->title }}</h3>
                                <p><span>Ticket {{ $ticket->ticket_id ?? '#' . ($ticket->id ?? 'N/A') }}</span><i></i><span>{{ $ticket->created_at?->diffForHumans() ?? 'Recently' }}</span></p>
                            </div>
                        </div>
                        <div class="hs-log-badges">
                            @if(in_array($ticket->priority, ['critical', 'urgent', 'high'], true))
                                <span class="hs-badge hs-badge-danger">{{ str($ticket->priority)->title() }} priority</span>
                            @endif
                            <span class="hs-badge hs-badge-{{ $statusTone($ticket->status) }}">{{ $statusLabel($ticket->status) }}</span>
                        </div>
                    </article>
                @empty
                    <div class="hs-empty">No maintenance tickets have been submitted yet.</div>
                @endforelse
            </div>
        </section>
        <section class="hs-card hs-log-card hs-brown-card">
            <div class="hs-card-heading">
                <div><h3>Operational audit trail</h3><p>Human-readable account, booking, ticket, announcement, concern, and moderation events.</p></div>
            </div>
            <div class="hs-log-list" data-progressive-list data-progressive-limit="5">
                @forelse($recentActivityLogs ?? [] as $log)
                    <article class="hs-log-row" data-progressive-item>
                        <div class="hs-log-main">
                            <div>
                                <h3>{{ $log->description }}</h3>
                                <p>{{ $log->actor?->name ?? 'System' }} <i></i> {{ $log->created_at?->diffForHumans() ?? 'Recently' }}</p>
                            </div>
                        </div>
                        <span class="hs-badge hs-badge-info">{{ $activityActionLabel($log->action) }}</span>
                    </article>
                @empty
                    <div class="hs-empty">No operational audit events have been recorded yet.</div>
                @endforelse
            </div>
        </section>
    </section>


</div>

<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap');
.hs-dashboard {
    --hs-bg: #F4EFE7;
    width: 100%;
    --hs-card: #FFFDF8;
    --hs-card-shadow: 0 10px 24px rgba(79, 58, 44, .08);
    --hs-border: #E3D8CA;
    --hs-border-soft: rgba(227, 216, 202, .88);
    --hs-text: #2F271F;
    --hs-muted: #5D5043;
    --hs-subtle: #806F5C;
    --hs-mocha: #4F3A2C;
    --hs-mocha-soft: #6A5140;
    --hs-amber: #B47721;
    --hs-amber-soft: #F3E3CC;
    --hs-red: #dc2626;
    --hs-red-soft: #fef2f2;
    --hs-green: #15803d;
    --hs-green-soft: #f0fdf4;
    --hs-blue: #2563eb;
    --hs-blue-soft: #eff6ff;
    max-width: 100%;
    min-height: 100vh;
    margin: 0;
    padding: 18px 20px 24px;
    border-radius: 16px;
    background: var(--hs-bg);
    color: var(--hs-text);
    font-family: 'DM Sans', sans-serif;
}
.hs-dashboard * {
    box-sizing: border-box;
}
.hs-dashboard h1, .hs-dashboard h2, .hs-dashboard h3, .hs-dashboard h4, .hs-dashboard p {
    margin: 0;
}
.hs-dashboard button, .hs-dashboard a {
    font-family: inherit;
}
.hs-dashboard svg {
    fill: none;
    stroke: currentColor;
    stroke-linecap: round;
    stroke-linejoin: round;
    stroke-width: 1.8;
}
.hs-topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 18px;
    padding: 8px 2px 18px;
    border-bottom: 1px solid var(--hs-border);
}
.hs-eyebrow {
    color: var(--hs-subtle) !important;
    font-size: .82rem;
    font-weight: 800;
    letter-spacing: .14em;
    line-height: 1.35;
    text-transform: uppercase;
}
.hs-topbar h1 {
    margin-top: 3px;
    color: var(--hs-text);
    font-family: 'Playfair Display', serif;
    font-size: clamp(1.85rem, 3vw, 2.35rem);
    letter-spacing: 0;
    line-height: 1.15;
}
.hs-topbar h1 span {
    color: var(--hs-amber);
}
.hs-date {
    margin-top: 5px !important;
    color: var(--hs-subtle) !important;
    font-size: .82rem;
    font-weight: 400;
    line-height: 1.45;
}
.hs-live-clock {
    display: flex;
    align-items: center;
    gap: 12px;
    color: var(--hs-text);
}
.hs-live-clock strong {
    font-size: 1rem;
    font-variant-numeric: tabular-nums;
    letter-spacing: .06em;
}
.hs-live-clock span {
    display: flex;
    align-items: center;
    gap: 6px;
    color: var(--hs-green);
    font-size: .7rem;
    font-weight: 700;
    letter-spacing: .12em;
    text-transform: uppercase;
}
.hs-live-clock b {
    font: inherit;
}
.hs-live-clock i {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: var(--hs-green);
    box-shadow: 0 0 0 4px rgba(79, 128, 92, .12);
}
.hs-live-clock span.is-updating {
    color: var(--hs-amber);
}
.hs-live-clock span.is-updating i {
    background: var(--hs-amber);
    box-shadow: 0 0 0 4px rgba(180, 119, 33, .14);
}
.hs-tabs {
    display: flex;
    gap: 3px;
    overflow-x: auto;
    border-bottom: 1px solid var(--hs-border);
}
.hs-tab {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    min-width: max-content;
    padding: 12px 15px;
    border: 0;
    border-bottom: 2px solid transparent;
    background: transparent;
    color: var(--hs-muted);
    cursor: pointer;
    font-size: .82rem;
    font-weight: 600;
    transition: color .18s, border-color .18s, background .18s;
}
.hs-tab svg {
    width: 16px;
    height: 16px;
}
.hs-tab:hover {
    color: var(--hs-text);
    background: rgba(15, 23, 42, .04);
}
.hs-tab.is-active {
    border-bottom-color: var(--hs-amber);
    color: var(--hs-amber);
}
.hs-view {
    padding-top: 22px;
}
.hs-view[hidden] {
    display: none !important;
}
.hs-view.is-active .hs-section-heading, .hs-view.is-active .hs-metric-card, .hs-view.is-active .hs-card, .hs-view.is-active .hs-diagnostics article, .hs-view.is-active .hs-forecast-stat, .hs-view.is-active .hs-log-summary article, .hs-view.is-active .da-kpi-card, .hs-view.is-active .da-insight-card, .hs-view.is-active .da-executive-summary-box {
    animation: hs-panel-rise .38s ease both;
}
.hs-view.is-active .hs-section-heading {
    animation-delay: .02s;
}
.hs-view.is-active .hs-metric-card:nth-child(1), .hs-view.is-active .da-kpi-card:nth-child(1), .hs-view.is-active .da-insight-card:nth-child(1) {
    animation-delay: .04s;
}
.hs-view.is-active .hs-metric-card:nth-child(2), .hs-view.is-active .da-kpi-card:nth-child(2), .hs-view.is-active .da-insight-card:nth-child(2) {
    animation-delay: .08s;
}
.hs-view.is-active .hs-metric-card:nth-child(3), .hs-view.is-active .da-kpi-card:nth-child(3), .hs-view.is-active .da-insight-card:nth-child(3) {
    animation-delay: .12s;
}
.hs-view.is-active .da-kpi-card:nth-child(4) {
    animation-delay: .16s;
}
.hs-view.is-active .da-kpi-card:nth-child(5) {
    animation-delay: .2s;
}
.hs-view.is-active .da-kpi-card:nth-child(6) {
    animation-delay: .24s;
}
.hs-view.is-active .hs-analytics-grid > .hs-card:nth-child(1), .hs-view.is-active .da-two-col > .hs-card:nth-child(1) {
    animation-delay: .1s;
}
.hs-view.is-active .hs-analytics-grid > .hs-card:nth-child(2), .hs-view.is-active .da-two-col > .hs-card:nth-child(2) {
    animation-delay: .15s;
}
.hs-view.is-active .hs-analytics-grid > .hs-card:nth-child(3) {
    animation-delay: .2s;
}
.hs-view.is-active .hs-analytics-grid > .hs-card:nth-child(4) {
    animation-delay: .25s;
}
@keyframes hs-panel-rise {
    from {
        opacity: 0;
        transform: translateY(12px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
@media (prefers-reduced-motion:reduce) {
    .hs-view.is-active .hs-section-heading, .hs-view.is-active .hs-metric-card, .hs-view.is-active .hs-card, .hs-view.is-active .hs-diagnostics article, .hs-view.is-active .hs-forecast-stat, .hs-view.is-active .hs-log-summary article, .hs-view.is-active .da-kpi-card, .hs-view.is-active .da-insight-card, .hs-view.is-active .da-executive-summary-box {
        animation: none;
    }
}
.hs-metrics {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 12px;
}
.hs-metric-card {
    --hs-metric-accent: var(--hs-amber);
    position: relative;
    isolation: isolate;
    overflow: hidden;
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 16px 18px;
    border: 1px solid var(--hs-border-soft);
    border-left: 3px solid var(--hs-metric-accent);
    border-radius: 12px;
    background: var(--hs-card);
    box-shadow: var(--hs-card-shadow);
    transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease;
}
.hs-metric-card::after {
    content: none;
}
.hs-metric-card:hover {
    transform: translateY(-2px);
    border-color: var(--hs-border);
    border-left-color: var(--hs-metric-accent);
    box-shadow: 0 4px 12px rgba(15, 23, 42, .08);
}
.hs-metric-card:nth-child(2) {
    --hs-metric-accent: var(--hs-green);
}
.hs-metric-card:nth-child(3) {
    --hs-metric-accent: var(--hs-blue);
}
.hs-metric-icon {
    display: grid;
    width: 42px;
    height: 42px;
    flex: 0 0 auto;
    place-items: center;
    border-radius: 10px;
}
.hs-metric-icon svg {
    width: 20px;
    height: 20px;
}
.hs-icon-amber {
    background: var(--hs-amber-soft);
    color: var(--hs-amber);
}
.hs-icon-green {
    background: var(--hs-green-soft);
    color: var(--hs-green);
}
.hs-icon-blue {
    background: var(--hs-blue-soft);
    color: var(--hs-blue);
}
.hs-metric-card p, .hs-metric-card span {
    display: block;
    color: var(--hs-subtle) !important;
    font-size: .76rem;
    font-weight: 600;
}
.hs-metric-card strong {
    color: var(--hs-text);
    font-size: 1.8rem;
    font-weight: 800;
    line-height: 1;
}
.hs-text-danger {
    color: var(--hs-red) !important;
}
.hs-section-heading {
    display: flex;
    align-items: end;
    justify-content: space-between;
    gap: 1.5rem;
    margin: 2px 0 12px;
}
.hs-section-heading h2 {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    margin-top: 4px;
    color: var(--hs-text);
    font-family: 'Playfair Display', serif;
    font-size: 1.85rem;
    line-height: 1.15;
}
.hs-section-heading .hs-eyebrow {
    color: #B47721 !important;
}
.hs-section-heading > div > p:last-child:not(.hs-eyebrow) {
    margin-top: 6px;
    color: var(--hs-muted) !important;
    font-size: .95rem;
    line-height: 1.5;
}
.hs-health {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 45px;
    padding: 0 20px;
    border: 1px solid rgba(155, 45, 45, .12);
    border-radius: 999px;
    background: #fff7ef;
    color: #9f2f2f;
    font-size: .74rem;
    font-weight: 800;
    letter-spacing: .075em;
    line-height: 1;
    text-decoration: none;
    text-transform: uppercase;
    box-shadow: 0 1px 0 rgba(255, 255, 255, .75) inset, 0 4px 10px rgba(79, 58, 44, .08);
    transition: transform .18s ease, box-shadow .18s ease, background-color .18s ease;
    white-space: nowrap;
}
/* Plain-text status labels (not buttons) — same treatment as the
   "3 pending approval" line, used instead of pill/badge styling for
   read-only status text like confidence levels and activity tags. */
.hs-plain-label {
    display: inline;
    font-size: .78rem;
    font-weight: 700;
    letter-spacing: .02em;
    white-space: nowrap;
}
.hs-plain-label-success { color: var(--hs-green); }
.hs-plain-label-warning { color: var(--hs-amber); }
.hs-plain-label-danger { color: #8F342E; }
.hs-plain-label-neutral { color: var(--hs-muted); }
.hs-health {
    margin-left: auto;
    flex: 0 0 auto;
    background: #FFF7EA;
    border-color: rgba(180, 119, 33, .28);
    box-shadow: 0 8px 18px rgba(79, 58, 44, .08);
}
.hs-tone-success {
    border-color: rgba(80, 128, 92, .24);
    background: #F7F2E8;
    color: var(--hs-green);
}
.hs-tone-warning {
    border-color: rgba(180, 119, 33, .28);
    background: #FFF7EA;
    color: var(--hs-amber);
}
.hs-tone-danger {
    border-color: rgba(143, 52, 46, .24);
    background: #FFF7EA;
    color: #8F342E;
}
.da-intelligence-hub {
    margin: 0;
    padding: 26px 28px 28px;
    border-radius: 14px;
    background: #6B4F3A;
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, .72),
        0 14px 32px rgba(79, 58, 44, .10);
}
.da-intelligence-hub-status {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 18px;
    margin-bottom: 18px;
}
.da-intelligence-hub-status h3 {
    margin: 0;
    color: #FFF7EA;
    font-family: 'Playfair Display', serif;
    font-size: clamp(1.35rem, 2vw, 1.8rem);
    line-height: 1.1;
}
.da-intelligence-hub-status .hs-health {
    margin-left: auto;
}
.da-live-radar-dot {
    position: relative;
    display: inline-block;
    width: 6px;
    height: 6px;
    flex: 0 0 auto;
    border-radius: 50%;
    background: var(--hs-green);
    box-shadow: 0 0 0 5px rgba(79, 128, 92, .12);
    animation: pulse 1.85s ease-in-out infinite;
}
@keyframes pulse {
    0%, 100% {
        opacity: .42;
        transform: scale(.92);
        box-shadow: 0 0 0 4px rgba(79, 128, 92, .10);
    }
    50% {
        opacity: 1;
        transform: scale(1);
        box-shadow: 0 0 0 8px rgba(79, 128, 92, .03);
    }
}
.hs-command-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: var(--spacing, 16px);
    align-items: stretch;
}
.hs-command-grid > .hs-card {
    display: flex;
    min-width: 0;
    height: 100%;
    min-height: 0;
    flex-direction: column;
    align-self: stretch;
}
.hs-card {
    border: 1px solid var(--hs-border-soft);
    border-radius: 12px;
    background: var(--hs-card);
    box-shadow: var(--hs-card-shadow);
}
.hs-card-heading {
    display: flex;
    align-items: start;
    justify-content: space-between;
    gap: 16px;
    padding: 18px 20px 14px;
    border-bottom: 1px solid var(--hs-border-soft);
}
.hs-card-heading h3 {
    color: var(--hs-text);
    font-size: 1rem;
}
.hs-card-heading p {
    margin-top: 3px;
    color: var(--hs-muted) !important;
    font-size: .86rem;
    line-height: 1.35;
}
.hs-card-heading > span {
    color: var(--hs-subtle);
    font-size: .8rem;
    font-weight: 700;
    white-space: nowrap;
}
.hs-chart-heading-actions {
    display: flex;
    flex: 0 0 auto;
    align-items: center;
    justify-content: flex-end;
    gap: 12px;
}
.hs-alert-list, .hs-action-list {
    display: flex;
    flex: 1 1 auto;
    flex-direction: column;
}
.hs-alert-list .app-progressive-action, .hs-action-list .app-progressive-action {
    display: flex;
    justify-content: flex-end;
    padding: 12px 15px 14px;
    border-top: 1px solid var(--hs-border-soft);
}
.hs-alert-list .app-progressive-action button, .hs-action-list .app-progressive-action button {
    padding: 6px 11px;
    border: 1px solid #e5cfad;
    border-radius: 999px;
    background: #fffaf5;
    color: var(--hs-amber);
    cursor: pointer;
    font-size: .7rem;
    font-weight: 800;
    letter-spacing: .04em;
    text-transform: uppercase;
    transition: background .18s ease, border-color .18s ease, color .18s ease, transform .18s ease;
}
.hs-alert-list .app-progressive-action button:hover, .hs-action-list .app-progressive-action button:hover {
    border-color: var(--hs-amber);
    background: var(--hs-amber-soft);
    transform: translateY(-1px);
}
.hs-alert {
    display: flex;
    gap: 10px;
    padding: 12px 15px;
    border-bottom: 1px solid var(--hs-border-soft);
}
.hs-alert:last-child, .hs-action:last-child {
    border-bottom: 0;
}
.hs-alert-symbol {
    display: grid;
    width: 21px;
    height: 21px;
    flex: 0 0 auto;
    place-items: center;
    border-radius: 50%;
    background: var(--hs-green-soft);
    color: var(--hs-green);
    font-size: .72rem;
    font-weight: 700;
}
.hs-alert-warning .hs-alert-symbol {
    background: var(--hs-amber-soft);
    color: var(--hs-amber);
}
.hs-alert-critical .hs-alert-symbol {
    background: var(--hs-red-soft);
    color: var(--hs-red);
}
.hs-alert h4, .hs-action h4 {
    color: var(--hs-text);
    font-size: .92rem;
}
.hs-alert p, .hs-action p {
    margin-top: 4px;
    color: var(--hs-muted) !important;
    font-size: .84rem;
    line-height: 1.45;
}
.hs-action {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 128px;
    align-items: center;
    gap: 16px;
    padding: 10px 15px;
    border-bottom: 1px solid var(--hs-border-soft);
}
.hs-action > div {
    min-width: 0;
}
.hs-action > div > span {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: var(--hs-amber);
    font-size: .61rem;
    font-weight: 700;
    letter-spacing: .1em;
    text-transform: uppercase;
}
.hs-action > div > span::before {
    content: "";
    display: inline-block;
    width: 7px;
    height: 7px;
    flex: 0 0 auto;
    border-radius: 50%;
    background: currentColor;
}
.hs-action-critical > div > span {
    color: var(--hs-red);
}
.hs-action a {
    display: inline-grid;
    grid-template-columns: minmax(0, 1fr) 14px;
    align-items: center;
    justify-self: end;
    column-gap: 6px;
    max-width: 100%;
    padding: 7px 11px;
    border: 1px solid var(--accent-gold, #c5a059);
    border-radius: 20px;
    color: var(--accent-gold, var(--hs-amber));
    font-size: .75rem;
    font-weight: 800;
    text-align: right;
    text-decoration: none;
    white-space: nowrap;
    transition: background .18s ease, border-color .18s ease, color .18s ease, transform .18s ease, box-shadow .18s ease;
}
.hs-action a:hover {
    border-color: var(--hs-amber);
    background: var(--hs-amber-soft);
    color: var(--hs-amber);
    transform: translateY(-1px);
    box-shadow: 0 5px 12px rgba(180, 119, 33, .10);
}
.hs-action a span {
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
}
.hs-action a b {
    display: inline-block;
    width: 14px;
    text-align: right;
}
.hs-diagnostics {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 9px;
    align-items: stretch;
    margin-top: 12px;
}
.hs-diagnostic-card {
    --hs-diagnostic-accent: var(--hs-amber);
    --hs-diagnostic-border: rgba(180, 119, 33, .24);
    --hs-diagnostic-glow: rgba(180, 119, 33, .12);
    --hs-diagnostic-start: #FFF8ED;
    --hs-diagnostic-end: #FFFDF8;
    position: relative;
    isolation: isolate;
    overflow: hidden;
    display: flex;
    min-width: 0;
    min-height: 100px;
    max-height: 120px;
    flex-direction: column;
    gap: 8px;
    justify-content: space-between;
    padding: 16px 20px;
    border: 1px solid var(--hs-diagnostic-border);
    border-radius: 12px;
    background: linear-gradient(135deg, var(--hs-diagnostic-start) 0%, var(--hs-diagnostic-end) 72%);
    box-shadow: 0 3px 10px rgba(84, 61, 37, .035);
    transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease;
}
.hs-diagnostic-card::after {
    content: "";
    position: absolute;
    top: -28px;
    right: -18px;
    z-index: -1;
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: radial-gradient(circle, var(--hs-diagnostic-glow), transparent 70%);
    pointer-events: none;
}
.hs-diagnostic-card:hover {
    transform: translateY(-2px);
    border-color: var(--hs-diagnostic-accent);
    box-shadow: 0 7px 16px var(--hs-diagnostic-glow);
}
.hs-diagnostic-card-green {
    --hs-diagnostic-accent: var(--hs-green);
    --hs-diagnostic-border: rgba(79, 128, 92, .22);
    --hs-diagnostic-glow: rgba(79, 128, 92, .12);
    --hs-diagnostic-start: #F1F8F2;
}
.hs-diagnostic-card-blue {
    --hs-diagnostic-accent: var(--hs-blue);
    --hs-diagnostic-border: rgba(82, 120, 140, .22);
    --hs-diagnostic-glow: rgba(82, 120, 140, .12);
    --hs-diagnostic-start: #F1F7F9;
}
.hs-diagnostic-label, .hs-forecast-stat span {
    display: block;
    min-height: 1.4em;
    color: #756451;
    font-size: .64rem;
    font-weight: 800;
    letter-spacing: .13em;
    line-height: 1.35;
    text-transform: uppercase;
}
.hs-diagnostic-value, .hs-forecast-stat strong {
    display: block;
    color: var(--hs-diagnostic-accent);
    font-family: 'Playfair Display', serif;
    font-size: 2.25rem;
    font-weight: 600;
    line-height: .95;
}
.hs-diagnostic-card p, .hs-forecast-stat p {
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
    color: #7B6A59 !important;
    font-size: .72rem;
    line-height: 1.35;
}
.hs-diagnostic-card p b {
    color: var(--hs-diagnostic-accent);
    font-weight: 800;
}
.hs-diagnostic-card p span {
    min-height: 0;
    color: inherit;
    font-size: inherit;
    font-weight: 500;
    letter-spacing: 0;
    line-height: inherit;
    text-transform: none;
}
.hs-analytics-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 24px;
    align-items: stretch;
}
.hs-analytics-grid .hs-chart-wide {
    grid-column: auto;
}
.hs-chart-wide {
    grid-column: 1 / -1;
}
.hs-analytics-grid > .hs-card, .da-two-col > .hs-card {
    display: flex;
    min-width: 0;
    min-height: 0;
    flex-direction: column;
}
.hs-chart-body {
    height: 225px;
    padding: 16px 20px 8px;
}
.hs-analytics-grid .hs-chart-body {
    flex: 1 1 auto;
    min-height: 225px;
}
.hs-analytics-grid .hs-insight {
    margin-top: auto;
}
.hs-chart-tall {
    height: 330px;
}
.hs-insight {
    padding: 8px 20px 18px;
    color: var(--hs-muted) !important;
    font-size: .74rem;
    line-height: 1.55;
}
.hs-admin-insight {
    margin: 14px 28px 20px;
    padding: 14px 18px;
    border: 1px solid #eadfcf;
    border-radius: 9px;
    background: #fffaf2;
    color: #5f5146 !important;
    font-size: .75rem;
    line-height: 1.55;
}
.hs-insight.hs-admin-insight {
    padding: 14px 18px;
}
.hs-admin-insight strong {
    color: var(--hs-text);
    font-weight: 800;
}
.hs-insight-action {
    display: inline-flex;
    margin-left: 6px;
    color: var(--hs-amber);
    font-weight: 800;
    text-decoration: none;
    white-space: nowrap;
}
.hs-insight-action:hover {
    text-decoration: underline;
    text-underline-offset: 3px;
}
.hs-admin-insight-warning {
    border-color: #efc9c6;
    background: var(--hs-red-soft);
    color: var(--hs-red) !important;
}
.hs-admin-insight-success {
    border-color: #cee1d2;
    background: var(--hs-green-soft);
    color: var(--hs-green) !important;
}
.hs-chart-key {
    display: flex;
    align-items: center;
    gap: 6px;
    color: var(--hs-muted) !important;
}
.hs-chart-key i {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--hs-amber);
}
#analytics-trends .hs-card {
    overflow: hidden;
}
#analytics-trends .hs-card-heading,
.hs-brown-card > .hs-card-heading {
    padding: 15px 22px 12px;
    border-bottom-color: rgba(255, 247, 234, .14);
    border-radius: 12px 12px 0 0;
    background: #6B4F3A;
}
#analytics-trends .hs-card-heading p,
.hs-brown-card > .hs-card-heading p {
    margin-top: 3px;
}
#analytics-trends .hs-card-heading h3,
.hs-brown-card > .hs-card-heading h3 {
    color: #FFF7EA;
}
#analytics-trends .hs-card-heading p,
.hs-brown-card > .hs-card-heading p,
#analytics-trends .hs-chart-key,
.hs-brown-card .hs-chart-key {
    color: rgba(255, 247, 234, .78) !important;
}
#analytics-trends .hs-card-heading > span {
    color: #F0D39A;
}
.hs-chart-stats {
    list-style: none;
    padding: 10px 18px 12px;
    margin: 0;
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 8px 18px;
    border-top: 1px solid var(--hs-border, #e8e0d5);
    font-family: 'DM Sans', sans-serif;
}
#analytics-trends .hs-chart-stats {
    border-top-color: rgba(255, 247, 234, .14);
    background: #6B4F3A;
}
.hs-brown-card > .hs-chart-stats {
    border-top-color: rgba(255, 247, 234, .14);
    border-radius: 0 0 12px 12px;
    background: #6B4F3A;
}
#analytics-trends .hs-chart-stats li,
.hs-brown-card > .hs-chart-stats li {
    border-color: rgba(255, 255, 255, .10);
    background: rgba(255, 255, 255, .05);
}
#analytics-trends .hs-chart-stats li > span,
.hs-brown-card > .hs-chart-stats li > span {
    color: rgba(255, 247, 234, .68);
    font-weight: 400;
}
#analytics-trends .hs-chart-stats li strong,
.hs-brown-card > .hs-chart-stats li strong {
    color: #FFFFFF;
}
.hs-chart-stats li {
    display: flex;
    min-height: 0;
    min-width: 0;
    flex-direction: column-reverse;
    align-items: flex-start;
    justify-content: center;
    gap: 3px;
    padding: 7px 10px;
    border: 1px solid rgba(234, 223, 210, .42);
    border-radius: 6px;
    background: rgba(255, 253, 248, .42);
    font-size: .82rem;
}
.hs-chart-stats li > span {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    color: var(--hs-muted, #786b60);
    font-size: .7rem;
    font-weight: 400;
    line-height: 1.25;
    white-space: normal;
}
.hs-chart-stats li > span::after {
    content: none;
}
.hs-chart-stats li strong {
    color: var(--hs-text, #2c2419);
    font-size: .94rem;
    font-weight: 450;
    line-height: 1.05;
    text-align: left;
    min-width: 0;
    overflow-wrap: anywhere;
}
.hs-stat-up {
    color: #bd5349 !important;
}
.hs-stat-down {
    color: #4f805c !important;
}
.hs-dashboard .hs-chart-stats .hs-help-term {
    position: relative;
    display: inline-grid !important;
    flex: 0 0 18px !important;
    place-items: center;
    width: 18px !important;
    min-width: 18px !important;
    max-width: 18px !important;
    height: 18px !important;
    min-height: 18px !important;
    max-height: 18px !important;
    aspect-ratio: 1 / 1;
    margin: 0 0 0 6px !important;
    padding: 0 !important;
    appearance: none;
    border: 1px solid #cbd5e1 !important;
    border-radius: 50% !important;
    background: #f8fafc !important;
    color: #475569 !important;
    box-shadow: 0 1px 2px rgba(15, 23, 42, .06);
    box-sizing: border-box;
    font-size: 0;
    line-height: 1;
    cursor: help;
    vertical-align: middle;
    transition: color .16s ease, background-color .16s ease, border-color .16s ease, box-shadow .16s ease;
}
.hs-dashboard .hs-chart-stats .hs-help-term svg {
    display: block;
    width: 10px !important;
    height: 10px !important;
    fill: none;
    stroke: currentColor;
    stroke-width: 1.7;
}
.hs-dashboard .hs-chart-stats .hs-help-term:hover {
    border-color: #94a3b8 !important;
    background: #fff !important;
    color: #2563eb !important;
    box-shadow: 0 3px 8px rgba(15, 23, 42, .14);
}
.hs-dashboard .hs-chart-stats .hs-help-term:focus {
    outline: none;
}
.hs-dashboard .hs-chart-stats .hs-help-term:focus-visible {
    border-color: #2563eb !important;
    background: #eff6ff !important;
    color: #1d4ed8 !important;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, .18);
}
.hs-help-term::before {
    content: attr(data-tooltip);
    position: absolute;
    left: 50%;
    bottom: calc(100% + 9px);
    z-index: 30;
    width: max-content;
    max-width: 230px;
    padding: 8px 10px;
    border: 1px solid #e0cfbb;
    border-radius: 8px;
    background: #342a23;
    color: #fffaf2;
    box-shadow: 0 12px 28px rgba(52, 42, 35, .18);
    font-size: .76rem;
    font-weight: 700;
    letter-spacing: 0;
    line-height: 1.35;
    text-transform: none;
    white-space: normal;
    opacity: 0;
    pointer-events: none;
    transform: translate(-50%, 4px);
    transition: opacity .16s ease, transform .16s ease;
}
.hs-help-term:hover::before, .hs-help-term:focus-visible::before {
    opacity: 1;
    transform: translate(-50%, 0);
}
.hs-donut-layout {
    display: grid;
    grid-template-columns: 190px 1fr;
    align-items: center;
    gap: 18px;
    padding: 18px 20px 20px;
}
.hs-donut-wrap {
    position: relative;
    width: 170px;
    height: 170px;
}
.hs-donut-wrap > div {
    position: absolute;
    inset: 0;
    display: grid;
    place-content: center;
    text-align: center;
    pointer-events: none;
}
.hs-donut-wrap strong {
    color: var(--hs-text);
    font-size: 1.65rem;
    line-height: 1;
}
.hs-donut-wrap span {
    color: var(--hs-subtle);
    font-size: .65rem;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
}
.hs-donut-legend {
    display: grid;
    gap: 10px;
}
.hs-donut-legend span {
    display: flex;
    align-items: center;
    gap: 7px;
    color: var(--hs-muted);
    font-size: .75rem;
}
.hs-donut-legend b {
    margin-left: auto;
    color: var(--hs-text);
}
.hs-donut-legend i {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}
.hs-dot-red {
    background: var(--hs-red);
}
.hs-dot-amber {
    background: var(--hs-amber);
}
.hs-dot-green {
    background: var(--hs-green);
}
.hs-predictive-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 12px;
}
.hs-forecast-total {
    text-align: right;
}
.hs-forecast-total strong {
    display: block;
    color: var(--hs-amber);
    font-size: 1.7rem;
    line-height: 1;
}
.hs-forecast-total span {
    color: var(--hs-muted);
    font-size: .68rem;
}
.hs-predictive-side {
    display: grid;
    gap: 9px;
}
.hs-forecast-stat {
    padding: 14px 15px;
    border: 1px solid var(--hs-border-soft);
    border-radius: 10px;
    background: var(--hs-card);
    box-shadow: var(--hs-card-shadow);
}
.hs-button {
    /* --- Layout & Structure (From Main Button) --- */
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 45px;
    padding: 0 20px;
    border: none; /* Removed the harsh red/brown border to favor the gradient */
    border-radius: 777px;
    white-space: nowrap;

    /* --- Typography (From Main Button) --- */
    font-size: .74rem;
    font-weight: 800;
    letter-spacing: .075em;
    line-height: 1;
    text-transform: uppercase;
    text-decoration: none;

    /* --- Color & Depth (From "Better" Button) --- */
    background: linear-gradient(90deg, #B8842F 0%, #D6A85B 100%);
    color: #FFFFFF;
    box-shadow: 0 12px 28px rgba(199, 150, 69, 0.3);

    /* --- Smooth Transitions (From "Better" Button) --- */
    transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.3s ease;
}

/* --- The Combined Premium Hover State --- */
.hs-button:hover,
.hs-button:focus-visible {
    outline: none;
    /* Clean CSS alternative to the JS onmouseover */
    transform: translateY(-3px); 
    box-shadow: 0 20px 40px rgba(199, 150, 69, 0.4);
    
    /* Optional: If you want a slight color shifting glow on hover, 
       we can subtly shift the gradient look by brightening it slightly */
    filter: brightness(1.05); 
}

/* --- Clean Reset for Active (Click) State --- */
.hs-button:active {
    transform: translateY(-1px);
    box-shadow: 0 8px 16px rgba(199, 150, 69, 0.3);
}

.hs-button {
    /* --- Layout & Structure (From Main Button) --- */
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 45px;
    padding: 0 20px;
    border: none; /* Removed the harsh red/brown border to favor the gradient */
    border-radius: 777px;
    white-space: nowrap;

    /* --- Typography (From Main Button) --- */
    font-size: .74rem;
    font-weight: 800;
    letter-spacing: .075em;
    line-height: 1;
    text-transform: uppercase;
    text-decoration: none;

    /* --- Color & Depth (From "Better" Button) --- */
    background: linear-gradient(90deg, #B8842F 0%, #D6A85B 100%);
    color: #FFFFFF;
    box-shadow: 0 12px 28px rgba(199, 150, 69, 0.3);

    /* --- Smooth Transitions (From "Better" Button) --- */
    transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.3s ease;
}

/* --- The Combined Premium Hover State --- */
.hs-button:hover,
.hs-button:focus-visible {
    outline: none;
    /* Clean CSS alternative to the JS onmouseover */
    transform: translateY(-3px); 
    box-shadow: 0 20px 40px rgba(199, 150, 69, 0.4);
    
    /* Optional: If you want a slight color shifting glow on hover, 
       we can subtly shift the gradient look by brightening it slightly */
    filter: brightness(1.05); 
}

/* --- Clean Reset for Active (Click) State --- */
.hs-button:active {
    transform: translateY(-1px);
    box-shadow: 0 8px 16px rgba(199, 150, 69, 0.3);
}
.hs-dashboard-controls {
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-end;
    gap: 8px;
    flex-shrink: 0;
}
.hs-export-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 38px;
    padding: 8px 16px;
    border: none;
    border-radius: 999px;
    background: linear-gradient(90deg, #B8842F 0%, #D6A85B 100%);
    color: #FFFFFF;
    font-size: .74rem;
    font-weight: 800;
    letter-spacing: .075em;
    text-transform: uppercase;
    text-decoration: none;
    box-shadow: 0 10px 24px rgba(199, 150, 69, 0.3);
    transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
}
.hs-export-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 14px 32px rgba(199, 150, 69, 0.4);
    filter: brightness(1.05);
}
.hs-export-btn:active {
    transform: translateY(-1px);
    box-shadow: 0 8px 16px rgba(199, 150, 69, 0.3);
}
.hs-log-card {
    overflow: hidden;
}
.hs-log-card + .hs-log-card {
    margin-top: 16px;
}
.hs-log-list {
    display: flex;
    flex-direction: column;
}
.hs-log-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    padding: 14px 18px;
    border-bottom: 1px solid var(--hs-border-soft);
}
.hs-log-row:last-child {
    border-bottom: 0;
}
.hs-log-main {
    display: flex;
    align-items: center;
    gap: 9px;
    flex: 1 1 auto;
    min-width: 0;
}
.hs-log-main h3 {
    overflow: hidden;
    color: var(--hs-text);
    font-size: .88rem;
    font-weight: 700;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.hs-log-main p {
    display: flex;
    align-items: center;
    gap: 7px;
    margin-top: 4px;
    color: var(--hs-muted) !important;
    font-size: .72rem;
    line-height: 1.4;
}
.hs-log-main p i {
    width: 3px;
    height: 3px;
    border-radius: 50%;
    background: #b9ada1;
}
.hs-log-badges {
    display: flex;
    flex: 0 0 auto;
    align-items: center;
    justify-content: flex-end;
    gap: 8px;
    min-width: 0;
}
.hs-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 auto;
    min-height: 24px;
    padding: 5px 9px;
    border-radius: 999px;
    font-size: .68rem;
    font-weight: 800;
    line-height: 1;
    white-space: nowrap;
}
.hs-badge-success {
    border: 1px solid #b9dfc2;
    background: #ecf8ef;
    color: #17692f;
}
.hs-badge-warning {
    border: 1px solid #e6c58d;
    background: #fff3dd;
    color: #8b5a12;
}
.hs-badge-danger {
    border: 1px solid #efb7b2;
    background: #fff0ef;
    color: #b42318;
}
.hs-badge-info {
    border: 1px solid #c7d8f6;
    background: #f0f5ff;
    color: #1f58c7;
}
.hs-badge-neutral {
    border: 1px solid #ded5ca;
    background: #f6f2ec;
    color: var(--hs-muted);
}
.hs-empty {
    padding: 32px;
    color: var(--hs-muted);
    text-align: center;
    font-size: .8rem;
}
.hs-log-card .app-progressive-action {
    padding: 12px 16px 14px;
    border-top: 1px solid var(--hs-border-soft);
}
.hs-log-card.hs-brown-card .app-progressive-action {
    border-top-color: rgba(255, 247, 234, .14);
    background: #6B4F3A;
}
.hs-log-card.hs-brown-card .app-progressive-action button {
    border-color: rgba(255, 247, 234, .22);
    background: rgba(255, 255, 255, .06);
    color: #fff7ea;
}
.hs-log-summary {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 9px;
    margin-top: 16px;
}
.hs-log-summary article {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding: 12px 14px;
    border: 1px solid var(--hs-border-soft);
    border-radius: 10px;
    background: var(--hs-card);
    box-shadow: var(--hs-card-shadow);
}
.hs-log-summary span {
    color: var(--hs-muted);
    font-size: .72rem;
}
.hs-log-summary strong {
    color: var(--hs-text);
    font-size: 1rem;
}
@media (max-width:1100px) {
    .hs-command-grid, .hs-predictive-grid {
        grid-template-columns: 1fr;
    }
    .hs-diagnostics {
        grid-template-columns: repeat(2, 1fr);
    }
    .hs-predictive-side {
        grid-template-columns: repeat(4, 1fr);
    }
}
@media (max-width:760px) {
    .hs-dashboard {
        padding: 14px 12px 18px;
        border-radius: 12px;
    }
    .hs-topbar {
        align-items: start;
    }
    .hs-live-clock {
        display: none;
    }
    .hs-card-heading {
        padding: 15px 16px 12px;
    }
    .hs-card-heading,
    .hs-chart-heading-actions {
        align-items: flex-start;
    }
    .hs-chart-heading-actions {
        flex-direction: column;
        gap: 6px;
    }
    .hs-metrics, .hs-analytics-grid {
        grid-template-columns: 1fr;
    }
    .hs-analytics-grid, .da-two-col {
        gap: 16px;
    }
    .hs-chart-body {
        min-height: 210px;
        padding: 14px 16px 8px;
    }
    .hs-insight {
        padding: 7px 16px 15px;
    }
    .hs-chart-wide, .hs-analytics-grid .hs-chart-wide {
        grid-column: auto;
    }
    .hs-diagnostics, .hs-predictive-side, .hs-log-summary {
        grid-template-columns: repeat(2, 1fr);
    }
    .hs-section-heading {
        align-items: start;
        flex-direction: column;
    }
    .hs-chart-stats {
        grid-template-columns: 1fr;
    }
    .hs-dashboard .da-table-wrap,
    .hs-dashboard .da-table-scroll {
        overflow: visible !important;
    }
    .hs-dashboard .da-table,
    .hs-dashboard .da-table thead,
    .hs-dashboard .da-table tbody,
    .hs-dashboard .da-table tr,
    .hs-dashboard .da-table td {
        display: block;
        width: 100%;
    }
    .hs-dashboard .da-table {
        min-width: 0 !important;
        border-collapse: separate;
        border-spacing: 0;
    }
    .hs-dashboard .da-table thead {
        position: absolute;
        width: 1px;
        height: 1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
    }
    .hs-dashboard .da-table tbody {
        display: grid;
        gap: 12px;
    }
    .hs-dashboard .da-table tr {
        display: grid;
        gap: 8px;
        padding: 14px;
        border: 1px solid rgba(109, 80, 54, 0.16);
        border-radius: 16px;
        background: #fffdf9;
        box-shadow: 0 10px 22px rgba(79, 58, 44, 0.08);
    }
    .hs-dashboard .da-table td {
        padding: 0 !important;
        border: 0 !important;
        text-align: left !important;
    }
    .hs-dashboard .da-table td:not(:first-child)::before {
        display: block;
        margin-bottom: 3px;
        color: #8b7d70;
        font-size: 0.66rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }
    .hs-dashboard .da-table td:nth-child(2)::before {
        content: "Assigned";
    }
    .hs-dashboard .da-table td:nth-child(3)::before {
        content: "Resolved";
    }
    .hs-dashboard .da-table td:nth-child(4)::before {
        content: "Avg rating";
    }
    .hs-dashboard .da-table td:nth-child(5)::before {
        content: "Completion";
    }
    .hs-dashboard .da-table td:nth-child(6)::before {
        content: "Avg resolution";
    }
    .hs-dashboard .da-person,
    .hs-dashboard .da-bar-inline {
        justify-content: flex-start;
    }
}
@media (max-width:460px) {
    .hs-diagnostics, .hs-predictive-side, .hs-log-summary {
        grid-template-columns: 1fr;
    }
    .hs-donut-layout {
        grid-template-columns: 1fr;
        justify-items: center;
    }
    .hs-action {
        grid-template-columns: 1fr;
        align-items: start;
        gap: 6px;
    }
    .hs-action a {
        justify-self: start;
        text-align: left;
    }
}
/* ── Deep Analytics ───────────────────────────────────────── */
.da-executive-summary-box {
    position: relative;
    isolation: isolate;
    overflow: hidden;
    margin: 0 0 2.5rem;
    padding: 26px 28px;
    border: 1px solid var(--hs-border-soft);
    border-radius: 12px;
    background: #6B4F3A;
    box-shadow: 0 14px 34px rgba(84, 61, 37, .12);
}
.da-executive-summary-box::before {
    content: none;
}
.da-executive-summary-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 18px;
    margin-bottom: 18px;
}
.da-executive-summary-head span {
    display: block;
    margin-bottom: 6px;
    color: #D6A85B;
    font-size: .64rem;
    font-weight: 800;
    letter-spacing: .14em;
    text-transform: uppercase;
}
.da-executive-summary-head h3 {
    color: #FFF7EA;
    font-family: 'Playfair Display', serif;
    font-size: clamp(1.35rem, 2.3vw, 1.8rem);
    line-height: 1.12;
}
.da-executive-summary-head > strong:not(.hs-health) {
    flex: 0 0 auto;
    padding: 8px 11px;
    border: 1px solid #e5cfad;
    border-radius: 999px;
    background: var(--hs-amber-soft);
    color: var(--hs-amber);
    font-size: .72rem;
    font-weight: 800;
    white-space: nowrap;
}
.da-executive-summary-list {
    position: relative;
    overflow: hidden;
    display: grid;
    margin: 0;
    padding: 1rem;
    border-left: 1px solid rgba(180, 119, 33, .28);
    border-radius: 12px;
    background-color: rgba(255, 253, 248, .94);
    list-style: none;
}
.da-executive-summary-list::before, .da-executive-summary-list::after {
    content: "";
    position: absolute;
    left: 12px;
    right: 1.5rem;
    height: 1px;
    pointer-events: none;
    background: linear-gradient(90deg, rgba(120, 107, 96, .28), rgba(120, 107, 96, .14) 62%, transparent);
}
.da-executive-summary-list::before {
    top: 0;
}
.da-executive-summary-list::after {
    bottom: 0;
}
.da-executive-summary-list li {
    position: relative;
    min-height: 58px;
    padding: 1.25rem .5rem 1.25rem 2.75rem;
    border: 0;
    border-bottom: 1px solid rgba(0, 0, 0, 0.065);
    border-radius: 0;
    background: transparent;
    color: var(--hs-muted);
    font-size: .84rem;
    line-height: 1.55;
}
.da-executive-summary-list li:last-child {
    border-bottom: 0;
}
.da-executive-summary-list li::before {
    content: "";
    position: absolute;
    top: 1.32rem;
    left: .5rem;
    width: 20px;
    height: 20px;
    border: 1px solid rgba(180, 119, 33, .12);
    border-radius: 50%;
    background: radial-gradient(circle at center, rgba(180, 119, 33, .64) 0 3px, transparent 4px), rgba(180, 119, 33, .08);
    box-shadow: none;
}
.da-executive-summary-list strong {
    color: var(--hs-text);
    font-weight: 800;
}
.da-executive-summary-list .da-summary-action-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
}
.da-summary-action-row > span {
    min-width: 0;
}
.hs-summary-action-link {
    flex: 0 0 auto;
    color: var(--accent-color, var(--hs-amber));
    font-size: .875rem;
    font-weight: 800;
    letter-spacing: .02em;
    text-decoration: underline;
    text-underline-offset: 3px;
    white-space: nowrap;
}
.hs-summary-action-link:hover {
    text-decoration: underline;
    color: var(--hs-amber);
}
.hs-analytics-chapter {
    margin-bottom: 0;
}
.hs-chapter-heading {
    display: flex;
    align-items: center;
    gap: 11px;
    margin: 0 0 1rem;
}
.hs-chapter-heading span {
    padding: 4px 8px;
    border: 1px solid #eadccd;
    border-radius: 999px;
    background: #fffaf5;
    color: var(--hs-subtle);
    font-size: .6rem;
    font-weight: 800;
    letter-spacing: .09em;
    text-transform: uppercase;
    white-space: nowrap;
}
.hs-chapter-heading h3 {
    color: var(--hs-text);
    font-size: .92rem;
    line-height: 1.25;
}

#analytics-trends .da-kpi-grid {
    margin-bottom: 0;
}
#analytics-trends .da-insight-grid {
    margin-bottom: 0;
}
#analytics-trends .hs-analytics-grid {
    margin-bottom: 0;
}
#analytics-trends > .da-two-col {
    margin-top: 0;
}
#predictive-ops .hs-analytics-divider {
    margin: 2rem 0;
}
.da-kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 12px 18px;
}
@media (max-width:1100px) {
    .da-kpi-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}
@media (max-width:500px) {
    .da-kpi-grid {
        grid-template-columns: 1fr;
    }
}
@media (max-width:760px) {
    .da-executive-summary-box {
        margin-bottom: 1.7rem;
        padding: 20px 18px;
    }
    .da-executive-summary-head {
        flex-direction: column;
        gap: 12px;
    }
    .da-executive-summary-list li {
        padding-right: 12px;
    }
    .hs-chapter-heading {
        align-items: flex-start;
        flex-direction: column;
        gap: 8px;
    }
    #analytics-trends .da-kpi-grid, #analytics-trends .da-insight-grid, #analytics-trends .hs-analytics-grid {
        margin-bottom: 0;
    }
    .hs-analytics-divider {
        margin: 1.6rem 0;
    }
    #analytics-trends .da-kpi-grid, #analytics-trends .da-insight-grid, #analytics-trends .hs-analytics-grid, #analytics-trends .da-two-col {
        gap: 16px;
    }
}
.hs-button-muted {
    background: #fffaf5;
    color: var(--hs-muted);
    border-color: var(--hs-border);
}
.da-insight-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 18px;
}
.da-insight-card {
    --hs-metric-accent: var(--hs-amber);
    position: relative;
    isolation: isolate;
    overflow: hidden;
    min-width: 0;
    min-height: 150px;
    padding: 15px 18px;
    border: 1px solid var(--hs-border-soft);
    border-left: 3px solid var(--hs-metric-accent);
    border-radius: 12px;
    background: var(--hs-card);
    box-shadow: var(--hs-card-shadow);
    transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease;
}
.da-insight-card::after {
    content: none;
}
.da-insight-card:hover {
    transform: translateY(-2px);
    border-color: var(--hs-border);
    border-left-color: var(--hs-metric-accent);
    box-shadow: 0 4px 12px rgba(15, 23, 42, .08);
}
.da-insight-card:nth-child(2) {
    --hs-metric-accent: var(--hs-green);
}
.da-insight-card:nth-child(3) {
    --hs-metric-accent: var(--hs-blue);
}
.da-insight-card span {
    display: block;
    margin-bottom: 6px;
    font-size: .64rem;
    font-weight: 800;
    letter-spacing: .1em;
    text-transform: uppercase;
    color: var(--hs-subtle);
}
.da-insight-card strong {
    display: block;
    color: var(--hs-text);
    font-family: 'Playfair Display', serif;
    font-size: 1.28rem;
    line-height: 1.1;
}
.da-insight-card p {
    margin: 8px 0 10px;
    color: var(--hs-muted) !important;
    font-size: .76rem;
    line-height: 1.55;
}
.da-insight-card small {
    color: var(--hs-amber);
    font-size: .68rem;
    font-weight: 800;
    letter-spacing: .07em;
    text-transform: uppercase;
}
.da-drill-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 auto;
    padding: 6px 0;
    border: 0;
    background: transparent;
    color: var(--hs-amber);
    font-family: 'DM Sans', sans-serif;
    font-size: .72rem;
    font-weight: 800;
    letter-spacing: .02em;
    line-height: 1;
    text-decoration: none;
    white-space: nowrap;
    transition: color .16s ease, transform .16s ease;
}
#analytics-trends .da-drill-link,
.hs-brown-card .da-drill-link {
    color: #fff7ea;
    text-shadow: 0 1px 0 rgba(62, 44, 30, .28);
}
.da-drill-link:hover,
.da-drill-link:focus-visible {
    color: #6B4F3A;
    text-decoration: underline;
    text-underline-offset: 4px;
    transform: translateX(2px);
}
#analytics-trends .da-drill-link:hover,
#analytics-trends .da-drill-link:focus-visible,
.hs-brown-card .da-drill-link:hover,
.hs-brown-card .da-drill-link:focus-visible {
    color: #ffffff;
}
.da-card > .hs-insight.hs-admin-insight {
    margin-top: 16px;
}
.da-funnel .hs-insight.hs-admin-insight {
    margin: 14px 0 0;
}
@media (max-width:980px) {
    .da-insight-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}
@media (max-width:620px) {
    .da-insight-grid {
        grid-template-columns: 1fr;
    }
}
.da-kpi-card {
    --hs-metric-accent: var(--hs-amber);
    --hs-metric-border: rgba(180, 119, 33, .22);
    --hs-metric-glow: rgba(180, 119, 33, .12);
    --hs-metric-start: #fff8ed;
    --hs-metric-end: #ffffff;
    position: relative;
    isolation: isolate;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    gap: 9px;
    min-height: 108px;
    justify-content: space-between;
    padding: 18px 22px;
    border: 1px solid var(--hs-metric-border);
    border-radius: 12px;
    background: linear-gradient(135deg, var(--hs-metric-start) 0%, var(--hs-metric-end) 72%);
    box-shadow: 0 3px 10px rgba(84, 61, 37, .035);
    transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease;
}
.da-kpi-card::after {
    content: "";
    position: absolute;
    top: -28px;
    right: -18px;
    z-index: -1;
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: radial-gradient(circle, var(--hs-metric-glow), transparent 70%);
    pointer-events: none;
}
.da-kpi-card:hover {
    transform: translateY(-2px);
    border-color: var(--hs-metric-accent);
    box-shadow: 0 7px 16px var(--hs-metric-glow);
}
.da-kpi-green {
    --hs-metric-accent: var(--hs-green);
    --hs-metric-border: rgba(79, 128, 92, .22);
    --hs-metric-glow: rgba(79, 128, 92, .12);
    --hs-metric-start: #f1f8f2;
}
.da-kpi-blue {
    --hs-metric-accent: var(--hs-blue);
    --hs-metric-border: rgba(82, 120, 140, .22);
    --hs-metric-glow: rgba(82, 120, 140, .12);
    --hs-metric-start: #f1f7f9;
}
.da-kpi-label {
    font-size: .64rem;
    font-weight: 800;
    letter-spacing: .13em;
    line-height: 1.35;
    text-transform: uppercase;
    color: var(--hs-subtle);
}
.da-kpi-value {
    font-family: 'Playfair Display', serif;
    font-size: clamp(1.65rem, 2.7vw, 2.25rem);
    font-weight: 700;
    line-height: .95;
    color: var(--hs-text);
}
.da-kpi-amber .da-kpi-value {
    color: var(--hs-amber);
}
.da-kpi-green .da-kpi-value {
    color: var(--hs-green);
}
.da-kpi-blue .da-kpi-value {
    color: var(--hs-blue);
}
.da-kpi-foot {
    display: flex;
    align-items: center;
    gap: 7px;
    margin-top: 0;
    color: var(--hs-subtle);
}
.da-kpi-vs {
    font-size: .62rem;
    color: var(--hs-subtle);
}
.da-trend {
    font-size: .68rem;
    font-weight: 800;
}
.da-trend-up-good {
    color: var(--hs-green);
}
.da-trend-up-bad {
    color: var(--hs-red);
}
.da-trend-neutral {
    color: var(--hs-subtle);
}
.da-two-col {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
}
@media (max-width:760px) {
    .da-two-col {
        grid-template-columns: 1fr;
    }
}
.da-card {
    overflow: hidden;
}
/* Funnel */
.da-funnel {
    padding: 18px 20px 16px;
    display: flex;
    flex-direction: column;
    gap: 11px;
}
.da-funnel-row {
    display: flex;
    align-items: center;
    gap: 10px;
}
.da-funnel-label {
    width: 80px;
    flex-shrink: 0;
    font-size: .72rem;
    font-weight: 600;
    color: var(--hs-muted);
    text-align: right;
}
.da-funnel-bar-wrap {
    flex: 1;
}
.da-funnel-bar {
    height: 30px;
    border-right: 3px solid;
    border-radius: 6px;
    display: flex;
    align-items: center;
    padding: 0 10px;
    min-width: 32px;
}
.da-funnel-bar span {
    font-size: .75rem;
    font-weight: 700;
}
.da-funnel-conv {
    width: 36px;
    flex-shrink: 0;
    font-size: .7rem;
    font-weight: 700;
    text-align: center;
}
.da-conv-invisible {
    visibility: hidden;
}
.da-conv-good {
    color: var(--hs-green);
}
.da-conv-warn {
    color: var(--hs-amber);
}
.da-conv-bad {
    color: var(--hs-red);
}
/* Age buckets */
.da-age-list {
    padding: 14px 16px 16px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.da-age-row {
    display: flex;
    flex-direction: column;
    gap: 5px;
}
.da-age-header {
    display: flex;
    align-items: center;
    gap: 8px;
}
.da-age-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
}
.da-age-label {
    font-size: .74rem;
    color: var(--hs-muted);
    flex: 1;
}
.da-age-val {
    font-size: .84rem;
    font-weight: 700;
}
.da-age-track {
    height: 6px;
    border-radius: 999px;
    background: #f0ebe4;
    overflow: hidden;
}
.da-age-fill {
    height: 100%;
    border-radius: 999px;
    transition: width .4s ease;
}
.da-alert-inline {
    margin-top: 4px;
    padding: 8px 12px;
    border-radius: 8px;
    background: var(--hs-red-soft);
    border: 1px solid #efc9c6;
    font-size: .73rem;
    color: var(--hs-red);
}
.da-alert-inline strong {
    font-weight: 700;
}
/* Pipeline */
.da-pipeline {
    display: flex;
    align-items: center;
    gap: 0;
    padding: 20px 16px 12px;
    flex-wrap: wrap;
}
.da-pipeline-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
}
.da-pipeline-step span {
    font-size: .7rem;
    font-weight: 600;
    color: var(--hs-muted);
    white-space: nowrap;
}
.da-pipeline-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}
.da-pipeline-icon svg {
    width: 18px;
    height: 18px;
}
.da-icon-amber {
    background: var(--hs-amber-soft);
    color: var(--hs-amber);
}
.da-icon-blue {
    background: var(--hs-blue-soft);
    color: var(--hs-blue);
}
.da-icon-green {
    background: var(--hs-green-soft);
    color: var(--hs-green);
}
.da-pipeline-arrow {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 2px;
    padding: 0 8px;
}
.da-pipeline-arrow strong {
    font-size: .95rem;
    font-weight: 700;
}
.da-pipeline-arrow span {
    font-size: .62rem;
    color: var(--hs-subtle);
    white-space: nowrap;
}
.da-pipeline-summary {
    display: grid;
    grid-template-columns: repeat(3, 1fr) auto;
    gap: 8px;
    align-items: center;
    padding: 12px 16px 16px;
    border-top: 1px solid #f0ebe4;
}
.da-pipeline-summary > div {
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.da-pipeline-summary span {
    font-size: .62rem;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: var(--hs-subtle);
}
.da-pipeline-summary strong {
    font-size: 1.4rem;
    font-family: 'Playfair Display', serif;
    color: var(--hs-text);
}
.da-pipeline-verdict {
    padding: 6px 10px;
    border-radius: 8px;
    font-size: .7rem;
    font-weight: 700;
    white-space: nowrap;
}
.da-verdict-warn {
    background: var(--hs-red-soft);
    color: var(--hs-red);
    border: 1px solid #efc9c6;
}
.da-verdict-ok {
    background: var(--hs-green-soft);
    color: var(--hs-green);
    border: 1px solid #cee1d2;
}
/* Tables */
.da-diagnostic-card {
    display: flex;
    flex-direction: column;
    overflow: hidden;
}
.da-table-wrap {
    overflow-x: auto;
}
.da-table-scroll {
    max-height: 350px;
    overflow: auto;
    overscroll-behavior: contain;
    scrollbar-width: thin;
    scrollbar-color: rgba(180, 119, 33, .32) transparent;
}
.da-table-scroll::-webkit-scrollbar {
    width: 7px;
    height: 7px;
}
.da-table-scroll::-webkit-scrollbar-track {
    background: transparent;
}
.da-table-scroll::-webkit-scrollbar-thumb {
    border-radius: 999px;
    background: rgba(180, 119, 33, .28);
}
.da-table-scroll::-webkit-scrollbar-thumb:hover {
    background: rgba(180, 119, 33, .42);
}
.da-table {
    width: 100%;
    border-collapse: collapse;
    font-size: .78rem;
    background: var(--hs-card);
}
.da-table thead tr {
    background: #fff7ed;
}
.da-table th {
    position: sticky;
    top: 0;
    z-index: 2;
    padding: 9px 14px;
    text-align: left;
    font-size: .62rem;
    font-weight: 800;
    letter-spacing: .10em;
    text-transform: uppercase;
    color: var(--hs-subtle);
    border-bottom: 1px solid var(--hs-border);
    background: #fff7ed;
    box-shadow: 0 1px 0 var(--hs-border);
}
.da-table td {
    padding: 11px 14px;
    border-bottom: 1px solid rgba(234, 223, 210, .74);
    color: var(--hs-text);
    vertical-align: middle;
}
.da-table tbody tr:last-child td {
    border-bottom: 0;
}
.da-table tbody tr:hover td {
    background: #fffaf5;
}
.da-empty-micro-card {
    display: grid;
    min-height: 116px;
    place-items: center;
    margin: 10px;
    padding: 22px;
    border: 1px dashed #e6d8c7;
    border-radius: 12px;
    background: #fffaf2;
    color: var(--hs-muted);
    font-size: .82rem;
    font-weight: 700;
    line-height: 1.5;
    text-align: center;
}
.da-td-muted {
    color: var(--hs-muted) !important;
}
.da-td-accent {
    color: var(--hs-amber) !important;
    font-weight: 700;
}
.da-td-green {
    color: var(--hs-green) !important;
    font-weight: 700;
}
.da-person {
    display: flex;
    align-items: center;
    gap: 8px;
}
.da-avatar {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: var(--hs-amber-soft);
    color: var(--hs-amber);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .68rem;
    font-weight: 700;
    flex-shrink: 0;
    border: 1px solid #e5cfad;
}
.da-cat-badge {
    padding: 3px 8px;
    border-radius: 999px;
    background: var(--hs-amber-soft);
    color: var(--hs-amber);
    font-size: .68rem;
    font-weight: 600;
    border: 1px solid #e5cfad;
}
.da-bar-inline {
    display: flex;
    align-items: center;
    gap: 6px;
}
.da-bar-track {
    width: 70px;
    height: 5px;
    border-radius: 999px;
    background: #f3eadf;
    overflow: hidden;
}
.da-bar-fill {
    height: 100%;
    border-radius: 999px;
}
.da-bar-inline > span {
    font-size: .72rem;
    font-weight: 700;
    min-width: 30px;
}
.da-diagnostic-footer {
    display: flex;
    justify-content: flex-end;
    padding: 10px 16px 14px;
    border-top: 1px solid var(--hs-border-soft);
    background: #ffffff;
}
.hs-brown-card .da-diagnostic-footer {
    border-top-color: rgba(255, 247, 234, .14);
    border-radius: 0 0 12px 12px;
    background: #6B4F3A;
}
.da-diagnostic-footer a {
    color: var(--hs-amber);
    font-size: .72rem;
    font-weight: 800;
    letter-spacing: .02em;
    text-decoration: none;
}
.hs-brown-card .da-diagnostic-footer a {
    color: #fff7ea;
    text-shadow: 0 1px 0 rgba(62, 44, 30, .28);
}
.da-diagnostic-footer a:hover {
    text-decoration: underline;
    text-underline-offset: 3px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const clock = document.getElementById('live-clock');
    const updateClock = () => {
        if (!clock) return;
        clock.textContent = new Date().toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
    };
    updateClock();
    setInterval(updateClock, 1000);

    const metricsUrl = @json(route('admin.dashboard.metrics'));
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
    const liveStatus = document.querySelector('[data-dashboard-live-status]');
    const metricNodes = {
        openTickets: document.querySelector('[data-dashboard-metric="openTickets"]'),
        urgentTickets: document.querySelector('[data-dashboard-metric="urgentTickets"]'),
        upcomingBookings: document.querySelector('[data-dashboard-metric="upcomingBookings"]'),
        totalResidents: document.querySelector('[data-dashboard-metric="totalResidents"]'),
        activeResidents: document.querySelector('[data-dashboard-metric="activeResidents"]'),
        openConcerns: document.querySelector('[data-dashboard-metric="openConcerns"]'),
        unassignedOpen: document.querySelector('[data-dashboard-metric="unassignedOpen"]'),
        availableHandymen: document.querySelector('[data-dashboard-metric="availableHandymen"]'),
    };
    let consecutiveMetricFailures = 0;

    const setLiveStatus = (isHealthy) => {
        if (!liveStatus) return;

        liveStatus.classList.toggle('is-updating', !isHealthy);
        const label = liveStatus.querySelector('b');
        if (label) {
            label.textContent = isHealthy ? 'Live' : 'Updating...';
        }
    };

    const setMetricText = (key, value) => {
        const node = metricNodes[key];
        if (!node || value === undefined || value === null) return;

        if (key === 'urgentTickets') {
            node.textContent = `${value} urgent`;
            node.classList.toggle('hs-text-danger', Number(value) > 0);
            return;
        }

        if (key === 'activeResidents') {
            node.textContent = `${value} active accounts`;
            return;
        }

        if (key === 'availableHandymen') {
            node.textContent = `${value}/${window.dashboardActiveHandymen ?? value}`;
            return;
        }

        node.textContent = value;
    };

    window.refreshManagerDashboardMetrics = async () => {
        try {
            const response = await fetch(metricsUrl, {
                method: 'GET',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
                },
            });

            if (!response.ok) {
                throw new Error('Metrics request failed');
            }

            const metrics = await response.json();
            window.dashboardActiveHandymen = metrics.activeHandymen ?? window.dashboardActiveHandymen;
            Object.keys(metricNodes).forEach((key) => setMetricText(key, metrics[key]));
            consecutiveMetricFailures = 0;
            setLiveStatus(true);
        } catch (error) {
            consecutiveMetricFailures += 1;
            if (consecutiveMetricFailures >= 2) {
                setLiveStatus(false);
            }
        }
    };

    window.dashboardActiveHandymen = @json($predictive['activeHandymen'] ?? 0);
    window.refreshManagerDashboardMetrics();
    setInterval(window.refreshManagerDashboardMetrics, 30000);

    const tabs = document.querySelectorAll('[data-dashboard-tab]');
    const panels = document.querySelectorAll('[data-dashboard-panel]');
    const chartInstances = {};
    const chartText = '#5D5043';
    const chartGrid = 'rgba(227, 216, 202, 0.48)';
    const amber = '#B47721';
    const ticketLine = '#C65A11';
    const ticketLineFill = 'rgba(198, 90, 17, .12)';
    const categoryPalette = ['#B45309', '#8A5A2F', '#5F7F6E', '#7A6A3A', '#9A5A4D', '#6B4F3A'];
    const bookingPalette = ['#52786E', '#B47721', '#8A5A2F', '#6B7A3A', '#9A5A4D', '#6B4F3A'];
    const red = '#dc2626';
    const green = '#15803d';

    @php
        $trendLabels = $ticketTrendLabels ?? collect(range(29, 0))->map(fn($i) => now()->subDays($i)->format('M d'))->toArray();
        $trendData = $ticketTrendData ?? array_fill(0, 30, 0);
        $catLabels = $categoryLabels ?? ['Plumbing', 'Electrical', 'Furniture', 'HVAC', 'Other'];
        $catData = $categoryData ?? [0, 0, 0, 0, 0];
        $spaceLabelsData = $spaceLabels ?? ['Study Room 1', 'Study Room 2', 'Conference Room', 'Gym'];
        $spaceDataValues = $spaceData ?? [0, 0, 0, 0];
        $forecastLabels = $predictiveAnalytics['forecastLabels'] ?? [];
        $actualTicketData = $predictiveAnalytics['actualTicketData'] ?? [];
        $forecastTicketData = $predictiveAnalytics['forecastTicketData'] ?? [];
    @endphp
    const axis = {
        ticks: { color: chartText, font: { size: 10 }, precision: 0 },
        grid: { color: chartGrid },
        border: { display: false }
    };
    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { x: axis, y: { ...axis, beginAtZero: true } }
    };
    const ticketIndexUrl = @json(route('tickets.index'));
    const bookingCalendarUrl = @json(route('admin.bookings.calendar'));
    const analyticsUrl = (baseUrl, params = {}) => {
        const url = new URL(baseUrl, window.location.origin);
        url.searchParams.set('source', 'analytics');
        Object.entries(params).forEach(([key, value]) => {
            if (value !== null && value !== undefined && value !== '') {
                url.searchParams.set(key, value);
            }
        });
        return url.toString();
    };

    const buildAnalyticsCharts = () => {
        if (chartInstances.analytics) return;
        chartInstances.analytics = true;
        new Chart(document.getElementById('ticketTrendChart'), {
            type: 'line',
            data: { labels: @json($trendLabels), datasets: [{ data: @json($trendData), borderColor: ticketLine, backgroundColor: ticketLineFill, borderWidth: 2, pointRadius: 2, tension: .32, fill: true }] },
            options: {
                ...chartOptions,
                onClick: (event, elements, chart) => {
                    if (!elements.length) return;
                    window.location.href = analyticsUrl(ticketIndexUrl, {
                        chart: 'ticket-volume',
                        date: chart.data.labels[elements[0].index],
                    });
                },
            }
        });
        new Chart(document.getElementById('categoryBarChart'), {
            type: 'bar',
            data: { labels: @json($catLabels), datasets: [{ data: @json($catData), backgroundColor: @json($catLabels).map((_, index) => categoryPalette[index % categoryPalette.length]), borderRadius: 4, barPercentage: .52, categoryPercentage: .7 }] },
            options: {
                ...chartOptions,
                onClick: (event, elements, chart) => {
                    if (!elements.length) return;
                    window.location.href = analyticsUrl(ticketIndexUrl, {
                        chart: 'category',
                        category: chart.data.labels[elements[0].index],
                    });
                },
            }
        });
        new Chart(document.getElementById('bookingSpaceChart'), {
            type: 'bar',
            data: { labels: @json($spaceLabelsData), datasets: [{ data: @json($spaceDataValues), backgroundColor: @json($spaceLabelsData).map((_, index) => bookingPalette[index % bookingPalette.length]), borderRadius: 4, barPercentage: .52, categoryPercentage: .7 }] },
            options: {
                ...chartOptions,
                onClick: (event, elements, chart) => {
                    if (!elements.length) return;
                    window.location.href = analyticsUrl(bookingCalendarUrl, {
                        chart: 'booking-space',
                        facility: chart.data.labels[elements[0].index],
                    });
                },
            }
        });
    };

    const buildForecastChart = () => {
        if (chartInstances.forecast) return;
        chartInstances.forecast = true;
        new Chart(document.getElementById('ticketForecastChart'), {
            type: 'line',
            data: {
                labels: @json($forecastLabels),
                datasets: [
                    { label: 'Actual tickets', data: @json($actualTicketData), borderColor: ticketLine, backgroundColor: ticketLineFill, borderWidth: 2, pointRadius: 2, tension: .32, fill: true },
                    { label: 'Forecast', data: @json($forecastTicketData), borderColor: red, borderDash: [6, 5], borderWidth: 2, pointRadius: 2, tension: .32 }
                ]
            },
            options: {
                ...chartOptions,
                plugins: { legend: { display: true, labels: { color: chartText, boxWidth: 10, boxHeight: 10, font: { size: 10 } } } }
            }
        });
    };

    const activate = (target) => {
        tabs.forEach((tab) => {
            const active = tab.dataset.dashboardTab === target;
            tab.classList.toggle('is-active', active);
            tab.setAttribute('aria-selected', active ? 'true' : 'false');
        });
        panels.forEach((panel) => {
            const active = panel.dataset.dashboardPanel === target;
            panel.hidden = !active;
            panel.classList.toggle('is-active', active);
        });
        if (target === 'analytics-trends') buildAnalyticsCharts();
        if (target === 'predictive-ops') buildForecastChart();
    };

    tabs.forEach((tab) => tab.addEventListener('click', () => {
        const target = tab.dataset.dashboardTab;
        activate(target);
        try { history.replaceState(null, '', '#' + target); } catch {}
    }));

    // Restore active tab from URL hash on load / back-navigation.
    const initialHash = location.hash ? location.hash.slice(1) : null;
    const validTabs   = Array.from(tabs).map((t) => t.dataset.dashboardTab);
    if (initialHash && validTabs.includes(initialHash)) {
        activate(initialHash);
    }
});
</script>
</x-app-layout>
