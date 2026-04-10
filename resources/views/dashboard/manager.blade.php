<x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<div class="dash-root">

    <div class="dash-hero">
        <div class="hero-glow"></div>
        <div class="hero-grid-overlay"></div>
        <div class="hero-content">
            <div class="hero-eyebrow">
                <span class="eyebrow-dot"></span>
                Oversight Dashboard
            </div>
            <h1 class="hero-title">
                Good {{ now()->hour < 12 ? 'Morning' : (now()->hour < 18 ? 'Afternoon' : 'Evening') }},
                <span class="hero-name">{{ Auth::user()->name }}</span>
            </h1>
            <p class="hero-sub">
                Here's your facility overview for {{ now()->format('l, F j, Y') }}.
            </p>
        </div>
        <div class="hero-time-block">
            <div class="hero-time" id="live-clock"></div>
            <div class="hero-date-tag">Live</div>
        </div>
    </div>

    <div class="metrics-grid">
        <div class="metric-card">
            <div class="metric-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/>
                    <path d="M13 5v2"/><path d="M13 17v2"/><path d="M13 11v2"/>
                </svg>
            </div>
            <div class="metric-body">
                <div class="metric-value">{{ $openTickets ?? 0 }}</div>
                <div class="metric-label">Open Tickets</div>
            </div>
            <div class="metric-sub urgent-tag">
                <span class="urgent-dot"></span>{{ $urgentTickets ?? 0 }} urgent
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
            </div>
            <div class="metric-body">
                <div class="metric-value">{{ $pendingBookings ?? 0 }}</div>
                <div class="metric-label">Upcoming Bookings</div>
            </div>
            <div class="metric-sub">Already confirmed</div>
        </div>

        <div class="metric-card">
            <div class="metric-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
            <div class="metric-body">
                <div class="metric-value">{{ $totalResidents ?? 0 }}</div>
                <div class="metric-label">Total Residents</div>
            </div>
            <div class="metric-sub">{{ $activeResidents ?? 0 }} active</div>
        </div>

        <div class="metric-card">
            <div class="metric-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>
            <div class="metric-body">
                <div class="metric-value">{{ $resolvedThisWeek ?? 0 }}</div>
                <div class="metric-label">Resolved This Week</div>
            </div>
            <div class="metric-sub">
                <div class="rate-bar-wrap">
                    <div class="rate-bar-track">
                        <div class="rate-bar-fill" style="width: {{ $resolutionRate ?? 0 }}%"></div>
                    </div>
                    <span>{{ $resolutionRate ?? 0 }}%</span>
                </div>
            </div>
        </div>
    </div>

    <div class="analytics-section">
        <div class="section-header">
            <div>
                <h2 class="section-title">Data Analytics</h2>
                <p class="section-sub">Activity trends and category breakdown</p>
            </div>
        </div>

        <div class="analytics-grid-2x2">
            <div class="chart-card">
                <div class="chart-card-header">
                    <div>
                        <div class="chart-title">Ticket Volume</div>
                        <div class="chart-desc">Last 30 days</div>
                    </div>
                    <div class="chart-legend">
                        <span class="legend-dot" style="background:#D6A85B"></span> Tickets
                    </div>
                </div>
                <div class="chart-body">
                    <canvas id="ticketTrendChart" height="140"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <div class="chart-card-header">
                    <div class="chart-title">Ticket Status</div>
                    <div class="chart-desc">Current breakdown</div>
                </div>
                <div class="chart-body chart-center donut-chart-body">
                    <canvas id="statusDonutChart" height="140"></canvas>
                </div>
                <div class="donut-legend">
                    <div class="donut-leg-item">
                        <span class="legend-dot" style="background:#E07060"></span>Open
                    </div>
                    <div class="donut-leg-item">
                        <span class="legend-dot" style="background:#D6A85B"></span>In Progress
                    </div>
                    <div class="donut-leg-item">
                        <span class="legend-dot" style="background:#5A8A5A"></span>Resolved
                    </div>
                </div>
            </div>

            <div class="chart-card">
                <div class="chart-card-header">
                    <div class="chart-title">Tickets by Category</div>
                    <div class="chart-desc">Maintenance distribution</div>
                </div>
                <div class="chart-body">
                    <canvas id="categoryBarChart" height="140"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <div class="chart-card-header">
                    <div class="chart-title">Bookings by Space</div>
                    <div class="chart-desc">Facility utilization</div>
                </div>
                <div class="chart-body">
                    <canvas id="bookingSpaceChart" height="140"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="two-col-grid">
        <div class="panel">
            <div class="panel-header">
                <div>
                    <h2 class="panel-title">Recent Tickets</h2>
                    <p class="panel-sub">Latest maintenance requests</p>
                </div>
                <a href="{{ route('tickets.index') }}" class="panel-link">View all -></a>
            </div>
            <div class="item-list">
                @forelse($recentTickets ?? [] as $ticket)
                <div class="list-item">
                    <div class="list-item-left">
                        <div class="list-item-title-row">
                            <span class="list-item-title">{{ $ticket->title }}</span>
                            @if(isset($ticket->priority) && $ticket->priority === 'urgent')
                                <span class="tag tag-urgent">URGENT</span>
                            @endif
                        </div>
                        <div class="list-item-meta">
                            <span>#{{ $ticket->id ?? 'N/A' }}</span>
                            <span class="meta-sep">|</span>
                            <span>{{ $ticket->created_at->diffForHumans() ?? 'Recently' }}</span>
                        </div>
                    </div>
                    <div class="list-item-right">
                        <span class="status-pill status-{{ $ticket->status ?? 'open' }}">
                            {{ $ticket->status === 'in_progress' ? 'In Progress' : ucfirst($ticket->status ?? 'Open') }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="empty-state">No tickets yet</div>
                @endforelse
            </div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <div>
                    <h2 class="panel-title">Upcoming Bookings</h2>
                    <p class="panel-sub">Confirmed resident reservations</p>
                </div>
                <a href="{{ route('admin.bookings.calendar') }}" class="panel-link">Manage -></a>
            </div>
            <div class="item-list">
                @forelse($pendingBookingsList ?? [] as $booking)
                <div class="list-item">
                    <div class="list-item-left">
                        <div class="list-item-title">{{ $booking->facility_name ?? $booking->space_name ?? 'Facility' }}</div>
                        <div class="list-item-meta">
                            <span>{{ $booking->user->name ?? 'Resident' }}</span>
                            <span class="meta-sep">|</span>
                            <span>{{ \Carbon\Carbon::parse($booking->booking_date ?? now())->format('M d, Y') }}</span>
                        </div>
                    </div>
                    <div class="list-item-right">
                        <a href="{{ route('admin.bookings.calendar') }}" class="mini-action-link">View</a>
                    </div>
                </div>
                @empty
                <div class="empty-state">No upcoming bookings</div>
                @endforelse
            </div>
            <div class="quick-actions">
                <a href="{{ route('announcements.create') }}" class="qa-btn qa-primary">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m3 11 19-9-9 19-2-8-8-2z"/></svg>
                    Post Announcement
                </a>
                <a href="{{ route('tickets.index') }}?status=open" class="qa-btn qa-secondary">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/></svg>
                    Assign Tickets
                </a>
            </div>
        </div>
    </div>

    <div class="bottom-grid">
        <div class="panel">
            <div class="panel-header">
                <h2 class="panel-title">Community Announcements</h2>
                <p class="panel-sub">Keep residents informed</p>
            </div>
            <div class="item-list">
                @forelse(($announcements ?? collect())->take(3) as $announcement)
                    <div class="list-item announce-item">
                        <div class="list-item-title">{{ $announcement->title }}</div>
                        <p class="announce-body">{{ Str::limit($announcement->content ?? '', 80) }}</p>
                        <div class="list-item-time">{{ $announcement->created_at->diffForHumans() ?? 'Recently' }}</div>
                    </div>
                @empty
                    <div class="empty-state">No announcements yet</div>
                @endforelse
            </div>
        </div>

        <div class="panel summary-panel">
            <h2 class="panel-title" style="margin-bottom: 16px;">Activity Summary</h2>
            <div class="summary-row">
                <span>Tickets this month</span>
                <strong>{{ $ticketsThisMonth ?? 0 }}</strong>
            </div>
            <div class="summary-row">
                <span>Bookings this month</span>
                <strong>{{ $bookingsThisMonth ?? 0 }}</strong>
            </div>
            <div class="summary-row">
                <span>Community posts</span>
                <strong>{{ $communityPosts ?? 0 }}</strong>
            </div>
            <div class="summary-row">
                <span>Active residents</span>
                <strong>{{ $activeResidents ?? 0 }}</strong>
            </div>
            <div class="resolution-block">
                <div class="resolution-label">Resolution Rate</div>
                <div class="resolution-value">{{ $resolutionRate ?? 0 }}%</div>
                <div class="resolution-track">
                    <div class="resolution-fill" style="width: {{ $resolutionRate ?? 0 }}%"></div>
                </div>
                <div class="resolution-note">Based on tickets resolved this month</div>
            </div>
        </div>
    </div>

    <div class="dash-footer">
        <span>{{ date('Y') }} HallSync | Facility Oversight</span>
        <span>Last sync: {{ now()->format('g:i A') }}</span>
    </div>
</div>

<style>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600;700&display=swap');

:root {
    --gold: #D6A85B;
    --gold-dim: rgba(214,168,91,0.12);
    --border: rgba(214,168,91,0.14);
    --bg-page: transparent;
    --bg-panel: rgba(37,39,42,0.88);
    --bg-panel-soft: rgba(42,44,48,0.78);
    --green: #5A8A5A;
    --red: #E07060;
    --amber: #BE9360;
    --text-head: #F0E9DF;
    --text-body: #C4B8A8;
    --text-muted: #8A7A66;
    --radius-panel: 20px;
    --radius-card: 16px;
}

* { box-sizing: border-box; margin: 0; padding: 0; }

.dash-root {
    font-family: 'DM Sans', sans-serif;
    color: var(--text-body);
    background: var(--bg-page);
    min-height: 100vh;
    padding: 0;
    max-width: 1580px;
    width: 100%;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 28px;
    position: relative;
    z-index: 1;
    font-size: 16px;
    line-height: 1.55;
}

.dash-hero {
    position: relative;
    overflow: hidden;
    border-radius: var(--radius-panel);
    background: linear-gradient(120deg, #111009 0%, #1C1A12 50%, #201E14 100%);
    padding: 36px 44px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
    border: 1px solid rgba(214,168,91,0.18);
}

.hero-glow {
    position: absolute;
    top: -60px; right: -40px;
    width: 280px; height: 280px;
    background: radial-gradient(circle, rgba(214,168,91,0.15) 0%, transparent 70%);
    pointer-events: none;
}

.hero-grid-overlay {
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(rgba(214,168,91,0.04) 1px, transparent 1px),
        linear-gradient(90deg, rgba(214,168,91,0.04) 1px, transparent 1px);
    background-size: 48px 48px;
    pointer-events: none;
}

.hero-content { position: relative; z-index: 2; }

.hero-eyebrow {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.875rem;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    color: var(--gold);
    font-weight: 700;
    margin-bottom: 12px;
}

.eyebrow-dot {
    width: 6px; height: 6px;
    border-radius: 50%;
    background: var(--gold);
    animation: pulse-dot 2.4s ease-in-out infinite;
}

@keyframes pulse-dot {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(0.7); }
}

.hero-title {
    font-family: 'Playfair Display', serif;
    font-size: clamp(2.5rem, 4vw, 3.5rem);
    font-weight: 700;
    color: #F0E9DF;
    line-height: 1.12;
    margin-bottom: 12px;
}

.hero-name { color: var(--gold); }

.hero-sub {
    font-size: 1.125rem;
    color: rgba(255,255,255,0.62);
}

.hero-time-block {
    position: relative;
    z-index: 2;
    text-align: right;
    flex-shrink: 0;
}

.hero-time {
    font-size: clamp(2.5rem, 4.4vw, 3.5rem);
    font-weight: 700;
    color: var(--gold);
    font-variant-numeric: tabular-nums;
    font-family: 'DM Sans', sans-serif;
}

.hero-date-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.75rem;
    color: rgba(255,255,255,0.4);
    letter-spacing: 0.1em;
    text-transform: uppercase;
    margin-top: 4px;
}

.hero-date-tag::before {
    content: '';
    width: 6px; height: 6px;
    border-radius: 50%;
    background: #5A8A5A;
    animation: pulse-dot 1.8s ease-in-out infinite;
}

.metrics-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
}

.metric-card {
    background: var(--bg-panel);
    border-radius: var(--radius-card);
    padding: 18px 20px;
    border: 1px solid var(--border);
    display: flex;
    align-items: center;
    gap: 14px;
    transition: transform 0.2s, border-color 0.2s;
    backdrop-filter: blur(10px);
}

.metric-card:hover {
    transform: translateY(-2px);
    border-color: rgba(214,168,91,0.24);
}

.metric-icon {
    width: 44px; height: 44px;
    border-radius: 12px;
    background: var(--gold-dim);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--gold);
}

.metric-value {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--text-head);
    line-height: 1;
}

.metric-label {
    font-size: 0.95rem;
    color: var(--text-muted);
    margin-top: 4px;
}

.metric-sub { font-size: 0.9rem; color: var(--text-muted); margin-left: auto; }
.urgent-tag { color: var(--red); }
.urgent-dot { width: 6px; height: 6px; background: var(--red); border-radius: 50%; display: inline-block; margin-right: 4px; }

.rate-bar-wrap { display: flex; align-items: center; gap: 8px; }
.rate-bar-track { width: 60px; height: 4px; background: rgba(255,255,255,0.08); border-radius: 4px; }
.rate-bar-fill { height: 100%; background: var(--gold); border-radius: 4px; }

.analytics-section { display: flex; flex-direction: column; gap: 16px; }
.section-header { display: flex; justify-content: space-between; align-items: flex-end; }
.section-title { font-family: 'Playfair Display', serif; font-size: 1.75rem; font-weight: 600; color: var(--text-head); }
.section-sub { font-size: 0.95rem; color: var(--text-muted); margin-top: 3px; }

.analytics-grid-2x2 {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.chart-card {
    background: var(--bg-panel);
    border-radius: var(--radius-card);
    padding: 18px;
    border: 1px solid var(--border);
    backdrop-filter: blur(10px);
}

.chart-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 12px;
}

.chart-title { font-size: 1.05rem; font-weight: 600; color: var(--text-head); }
.chart-desc { font-size: 0.9rem; color: var(--text-muted); margin-top: 2px; }
.chart-legend { display: flex; align-items: center; gap: 6px; font-size: 0.9rem; color: var(--text-muted); }
.legend-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }

.chart-body { min-height: 180px; }
.chart-center { display: flex; justify-content: center; align-items: center; }

#statusDonutChart {
    width: 250px !important;
    height: 250px !important;
    max-width: 200px;
    max-height: 200px;
}

.donut-chart-body {
    min-height: 250px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.donut-legend {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-top: 12px;
    flex-wrap: wrap;
}

.donut-leg-item { display: flex; align-items: center; gap: 6px; font-size: 0.9rem; color: var(--text-muted); }

.two-col-grid, .bottom-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.panel {
    background: var(--bg-panel-soft);
    border-radius: var(--radius-panel);
    padding: 22px 24px;
    border: 1px solid var(--border);
    backdrop-filter: blur(10px);
}

.panel-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.panel-title {
    font-family: 'Playfair Display', serif;
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--text-head);
}

.panel-sub { font-size: 0.95rem; color: var(--text-muted); margin-top: 2px; }
.panel-link { color: var(--amber); font-size: 0.95rem; font-weight: 500; text-decoration: none; }
.panel-link:hover { color: var(--gold); }

.item-list { display: flex; flex-direction: column; }
.list-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid rgba(255,255,255,0.05);
}

.list-item:last-child { border-bottom: none; }
.list-item-left { flex: 1; min-width: 0; }
.list-item-title { font-weight: 600; font-size: 1rem; color: var(--text-head); }
.list-item-meta {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-top: 4px;
    font-size: 0.9rem;
    color: var(--text-muted);
}

.meta-sep { opacity: 0.5; }
.list-item-right { text-align: right; flex-shrink: 0; }
.list-item-time { font-size: 0.875rem; color: var(--text-muted); margin-top: 4px; }

.tag-urgent {
    background: rgba(224,112,96,0.15);
    color: var(--red);
    font-size: 0.75rem;
    padding: 2px 8px;
    border-radius: 20px;
    margin-left: 8px;
}

.status-pill {
    font-size: 0.875rem;
    padding: 6px 14px;
    border-radius: 20px;
    font-weight: 500;
}

.status-open { background: rgba(255,255,255,0.06); color: #D0C8B8; }
.status-in_progress { background: rgba(190,147,96,0.15); color: var(--amber); }
.status-completed { background: rgba(90,138,90,0.15); color: var(--green); }

.mini-action-link {
    color: var(--gold);
    font-size: 0.9rem;
    font-weight: 600;
    text-decoration: none;
}

.mini-action-link:hover { color: #f0c97a; }

.quick-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-top: 16px;
    padding-top: 14px;
    border-top: 1px solid rgba(255,255,255,0.05);
}

.qa-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 10px 12px;
    border-radius: 40px;
    font-size: 0.9rem;
    font-weight: 600;
    text-decoration: none;
}

.qa-primary { background: linear-gradient(135deg, #C79745, #D6A85B); color: white; }
.qa-primary:hover { transform: translateY(-1px); }

.qa-secondary {
    background: rgba(255,255,255,0.04);
    color: var(--text-head);
    border: 1px solid var(--border);
}

.announce-body { font-size: 0.95rem; color: var(--text-muted); margin-top: 4px; line-height: 1.6; }

.empty-state {
    text-align: center;
    padding: 32px;
    color: var(--text-muted);
    font-size: 0.95rem;
}

.summary-panel { display: flex; flex-direction: column; }

.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid rgba(255,255,255,0.05);
    font-size: 0.95rem;
}

.summary-row:first-of-type { border-top: 1px solid rgba(255,255,255,0.05); }

.resolution-block {
    margin-top: 20px;
    background: rgba(190,147,96,0.08);
    border-radius: 14px;
    padding: 16px;
    border: 1px solid rgba(190,147,96,0.12);
}

.resolution-label {
    font-size: 0.8rem;
    font-weight: 600;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--amber);
}

.resolution-value {
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--gold);
    line-height: 1;
}

.resolution-track {
    height: 6px;
    background: rgba(255,255,255,0.08);
    border-radius: 6px;
    overflow: hidden;
    margin-top: 10px;
}

.resolution-fill {
    height: 100%;
    background: linear-gradient(90deg, #C79745, #D6A85B);
    border-radius: 6px;
}

.resolution-note { font-size: 0.875rem; color: var(--text-muted); margin-top: 8px; }

.dash-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 0 8px;
    border-top: 1px solid rgba(255,255,255,0.06);
    font-size: 0.875rem;
    color: var(--text-muted);
}

body { overflow-x: hidden; }

@media (max-width: 1000px) {
    .analytics-grid-2x2 { grid-template-columns: 1fr; }
    .metrics-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 768px) {
    .dash-root { padding: 0; gap: 16px; }
    .dash-hero { flex-direction: column; align-items: flex-start; padding: 24px; }
    .hero-time-block { display: none; }
    .two-col-grid, .bottom-grid { grid-template-columns: 1fr; }
    .metric-sub { margin-left: 0; }
    .metric-card { align-items: flex-start; flex-direction: column; }
    .quick-actions { grid-template-columns: 1fr; }
}

@media (max-width: 480px) {
    .metrics-grid { grid-template-columns: 1fr; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function updateClock() {
        const el = document.getElementById('live-clock');
        if (!el) return;
        const now = new Date();
        let h = now.getHours(), m = now.getMinutes(), s = now.getSeconds();
        const ampm = h >= 12 ? 'PM' : 'AM';
        h = h % 12 || 12;
        el.textContent = `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')} ${ampm}`;
    }
    updateClock();
    setInterval(updateClock, 1000);

    Chart.defaults.font.family = "'DM Sans', sans-serif";
    Chart.defaults.color = '#9A8F84';

    const GOLD = '#D6A85B';
    const GOLD_FILL = 'rgba(214,168,91,0.12)';
    const RED = '#E07060';
    const GREEN = '#5A8A5A';

    @php
        $trendLabels = $ticketTrendLabels ?? collect(range(29, 0))->map(fn($i) => now()->subDays($i)->format('M d'))->toArray();
        $trendData = $ticketTrendData ?? array_fill(0, 30, 0);
        $catLabels = $categoryLabels ?? ['Plumbing', 'Electrical', 'Furniture', 'HVAC', 'Other'];
        $catData = $categoryData ?? [0, 0, 0, 0, 0];
        $spaceLabelsData = $spaceLabels ?? ['Function Hall', 'Gym', 'Study Room', 'Game Room', 'Laundry'];
        $spaceDataValues = $spaceData ?? [0, 0, 0, 0, 0];
    @endphp

    new Chart(document.getElementById('ticketTrendChart'), {
        type: 'line',
        data: {
            labels: @json($trendLabels),
            datasets: [{
                data: @json($trendData),
                borderColor: GOLD,
                backgroundColor: GOLD_FILL,
                borderWidth: 2,
                pointRadius: 2,
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: { legend: { display: false } },
            scales: {
                x: { ticks: { maxTicksLimit: 6, font: { size: 9 } }, grid: { color: 'rgba(255,255,255,0.04)' } },
                y: { beginAtZero: true, ticks: { precision: 0, font: { size: 9 } }, grid: { color: 'rgba(255,255,255,0.04)' } }
            }
        }
    });

    new Chart(document.getElementById('statusDonutChart'), {
        type: 'doughnut',
        data: {
            labels: ['Open', 'In Progress', 'Resolved'],
            datasets: [{
                data: [{{ $openTickets ?? 0 }}, {{ $inProgressTickets ?? 0 }}, {{ $resolvedThisWeek ?? 0 }}],
                backgroundColor: [RED, GOLD, GREEN],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '80%',
            plugins: { legend: { display: false } }
        }
    });

    new Chart(document.getElementById('categoryBarChart'), {
        type: 'bar',
        data: {
            labels: @json($catLabels),
            datasets: [{
                data: @json($catData),
                backgroundColor: GOLD,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0, font: { size: 9 } }, grid: { color: 'rgba(255,255,255,0.04)' } },
                x: { ticks: { font: { size: 9 } }, grid: { color: 'rgba(255,255,255,0.02)' } }
            }
        }
    });

    new Chart(document.getElementById('bookingSpaceChart'), {
        type: 'bar',
        data: {
            labels: @json($spaceLabelsData),
            datasets: [{
                data: @json($spaceDataValues),
                backgroundColor: GOLD_FILL,
                borderColor: GOLD,
                borderWidth: 1,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0, font: { size: 9 } }, grid: { color: 'rgba(255,255,255,0.04)' } },
                x: { ticks: { font: { size: 9 } }, grid: { color: 'rgba(255,255,255,0.02)' } }
            }
        }
    });
});
</script>

</x-app-layout>
