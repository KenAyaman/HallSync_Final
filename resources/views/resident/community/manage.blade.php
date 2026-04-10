{{-- dashboard/manager.blade.php --}}
<div class="admin-dashboard">
    {{-- Header --}}
    <div class="dashboard-header">
        <div>
            <h1 class="dashboard-title">Oversight Dashboard</h1>
            <p class="dashboard-subtitle">Data-driven insights & analytics</p>
        </div>
        <div class="header-actions">
            <span class="date-badge">{{ now()->format('l, F j, Y') }}</span>
            <button class="refresh-btn" onclick="location.reload()">
                <i class="fas fa-chart-line"></i> Refresh Data
            </button>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="stats-grid-compact">
        <div class="stat-card-compact">
            <div class="stat-icon">🎫</div>
            <div class="stat-info">
                <span class="stat-value">{{ $openTickets ?? 0 }}</span>
                <span class="stat-label">Open Tickets</span>
                @if(($urgentTickets ?? 0) > 0)
                    <span class="stat-badge urgent">{{ $urgentTickets }} urgent</span>
                @endif
            </div>
        </div>

        <div class="stat-card-compact">
            <div class="stat-icon">📅</div>
            <div class="stat-info">
                <span class="stat-value">{{ $pendingBookings ?? 0 }}</span>
                <span class="stat-label">Pending Bookings</span>
                <span class="stat-badge">Awaiting approval</span>
            </div>
        </div>

        <div class="stat-card-compact">
            <div class="stat-icon">👥</div>
            <div class="stat-info">
                <span class="stat-value">{{ $totalResidents ?? 0 }}</span>
                <span class="stat-label">Total Residents</span>
                <span class="stat-badge">Active community</span>
            </div>
        </div>

        <div class="stat-card-compact">
            <div class="stat-icon">✅</div>
            <div class="stat-info">
                <span class="stat-value">{{ $resolvedThisWeek ?? 0 }}</span>
                <span class="stat-label">Resolved This Week</span>
                <span class="stat-badge">{{ $resolutionRate ?? 0 }}% rate</span>
            </div>
        </div>
    </div>

    {{-- CHARTS ROW 1: Line Chart + Pie Chart --}}
    <div class="charts-row">
        {{-- Ticket Trends Line Chart --}}
        <div class="chart-card">
            <div class="chart-header">
                <h3><i class="fas fa-chart-line"></i> Ticket Trends (Last 7 Days)</h3>
                <span class="chart-subtitle">Daily maintenance requests</span>
            </div>
            <div class="chart-body">
                <canvas id="ticketTrendsChart" height="200"></canvas>
            </div>
        </div>

        {{-- Status Distribution Doughnut Chart --}}
        <div class="chart-card">
            <div class="chart-header">
                <h3><i class="fas fa-chart-pie"></i> Ticket Status Distribution</h3>
                <span class="chart-subtitle">Current ticket breakdown</span>
            </div>
            <div class="chart-body">
                <canvas id="statusChart" height="200"></canvas>
            </div>
        </div>
    </div>

    {{-- CHARTS ROW 2: Bar Charts --}}
    <div class="charts-row">
        {{-- Maintenance Types Bar Chart --}}
        <div class="chart-card">
            <div class="chart-header">
                <h3><i class="fas fa-chart-bar"></i> Most Common Issues</h3>
                <span class="chart-subtitle">By maintenance category</span>
            </div>
            <div class="chart-body">
                <canvas id="maintenanceChart" height="200"></canvas>
            </div>
        </div>

        {{-- Popular Spaces Bar Chart --}}
        <div class="chart-card">
            <div class="chart-header">
                <h3><i class="fas fa-chart-bar"></i> Most Booked Spaces</h3>
                <span class="chart-subtitle">Facility popularity</span>
            </div>
            <div class="chart-body">
                <canvas id="bookingChart" height="200"></canvas>
            </div>
        </div>
    </div>

    {{-- Bottom Row: Recent Tickets + Monthly Summary + Quick Actions --}}
    <div class="admin-grid-3">
        {{-- Recent Tickets --}}
        <div class="admin-card">
            <div class="card-header">
                <h3>Recent Tickets</h3>
                <a href="{{ route('tickets.index') }}" class="card-link">View all →</a>
            </div>
            <div class="card-body">
                @forelse($recentTickets ?? [] as $ticket)
                <div class="list-item">
                    <div class="list-item-main">
                        <span class="list-item-title">{{ $ticket->title }}</span>
                        <span class="list-item-meta">#{{ $ticket->id }} • {{ $ticket->created_at->diffForHumans() }}</span>
                    </div>
                    <span class="status-badge status-{{ $ticket->status }}">
                        {{ $ticket->status === 'in_progress' ? 'In Progress' : ucfirst($ticket->status ?? 'Open') }}
                    </span>
                </div>
                @empty
                <div class="empty-state">No tickets yet</div>
                @endforelse
            </div>
        </div>

        {{-- Monthly Summary --}}
        <div class="admin-card">
            <div class="card-header">
                <h3>Monthly Summary</h3>
            </div>
            <div class="card-body">
                <div class="stat-row">
                    <span>Tickets this month</span>
                    <strong>{{ $ticketsThisMonth ?? 0 }}</strong>
                </div>
                <div class="stat-row">
                    <span>Bookings this month</span>
                    <strong>{{ $bookingsThisMonth ?? 0 }}</strong>
                </div>
                <div class="stat-row">
                    <span>Community posts</span>
                    <strong>{{ $communityPosts ?? 0 }}</strong>
                </div>
                <div class="stat-row">
                    <span>Active residents</span>
                    <strong>{{ $activeResidents ?? 0 }}</strong>
                </div>
                <div class="progress-section">
                    <div class="progress-label">Resolution Rate</div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $resolutionRate ?? 0 }}%"></div>
                    </div>
                    <div class="progress-value">{{ $resolutionRate ?? 0 }}%</div>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="admin-card">
            <div class="card-header">
                <h3>Quick Actions</h3>
            </div>
            <div class="card-body">
                <div class="quick-actions">
                    <a href="{{ route('announcements.create') }}" class="action-btn">
                        <i class="fas fa-bullhorn"></i> Post Announcement
                    </a>
                    <a href="{{ route('tickets.index') }}?status=open" class="action-btn">
                        <i class="fas fa-tools"></i> Assign Tickets
                    </a>
                    <a href="{{ route('bookings.index') }}?status=pending" class="action-btn">
                        <i class="fas fa-calendar-check"></i> Approve Bookings
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Ticket Trends Line Chart
        const ticketTrendsCtx = document.getElementById('ticketTrendsChart').getContext('2d');
        new Chart(ticketTrendsCtx, {
            type: 'line',
            data: {
                labels: @json($ticketTrends['labels'] ?? ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']),
                datasets: [{
                    label: 'Tickets Created',
                    data: @json($ticketTrends['data'] ?? [0, 0, 0, 0, 0, 0, 0]),
                    borderColor: '#C79745',
                    backgroundColor: 'rgba(199, 151, 69, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#D6A85B',
                    pointBorderColor: '#fff',
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'top' },
                    tooltip: { mode: 'index', intersect: false }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#F0E8DC' } },
                    x: { grid: { display: false } }
                }
            }
        });

        // 2. Status Distribution Doughnut Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: @json($statusDistribution['labels'] ?? ['Open', 'In Progress', 'Completed', 'Cancelled']),
                datasets: [{
                    data: @json($statusDistribution['data'] ?? [0, 0, 0, 0]),
                    backgroundColor: ['#C79745', '#D6A85B', '#5A8A5A', '#B39A78'],
                    borderWidth: 0,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // 3. Maintenance Types Bar Chart
        const maintenanceCtx = document.getElementById('maintenanceChart').getContext('2d');
        new Chart(maintenanceCtx, {
            type: 'bar',
            data: {
                labels: @json($maintenanceTypes['labels'] ?? ['Plumbing', 'Electrical', 'Furniture', 'HVAC', 'Other']),
                datasets: [{
                    label: 'Number of Requests',
                    data: @json($maintenanceTypes['data'] ?? [0, 0, 0, 0, 0]),
                    backgroundColor: 'rgba(199, 151, 69, 0.7)',
                    borderRadius: 8,
                    barPercentage: 0.65
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#F0E8DC' } },
                    x: { grid: { display: false } }
                }
            }
        });

        // 4. Booking Trends Bar Chart
        const bookingCtx = document.getElementById('bookingChart').getContext('2d');
        new Chart(bookingCtx, {
            type: 'bar',
            data: {
                labels: @json($bookingTrends['labels'] ?? ['Study Room', 'Event Hall', 'Gym', 'Game Room', 'Laundry']),
                datasets: [{
                    label: 'Total Bookings',
                    data: @json($bookingTrends['data'] ?? [0, 0, 0, 0, 0]),
                    backgroundColor: 'rgba(214, 168, 91, 0.7)',
                    borderRadius: 8,
                    barPercentage: 0.65
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#F0E8DC' } },
                    x: { grid: { display: false } }
                }
            }
        });
    });
</script>

<style>
    .admin-dashboard {
        max-width: 1400px;
        margin: 0 auto;
    }

    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 28px;
        flex-wrap: wrap;
        gap: 16px;
    }

    .dashboard-title {
        font-size: 28px;
        font-weight: 700;
        color: #2F2A27;
        margin: 0 0 4px 0;
        font-family: 'Playfair Display', serif;
    }

    .dashboard-subtitle {
        font-size: 14px;
        color: #7B746B;
        margin: 0;
    }

    .date-badge {
        background: #F8F4EC;
        padding: 8px 16px;
        border-radius: 40px;
        font-size: 13px;
        color: #8A6A3C;
    }

    .refresh-btn {
        background: #C79745;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 30px;
        font-size: 12px;
        cursor: pointer;
        margin-left: 12px;
    }

    .refresh-btn:hover {
        background: #D6A85B;
    }

    /* Stats Grid */
    .stats-grid-compact {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 28px;
    }

    .stat-card-compact {
        background: white;
        border-radius: 20px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        border: 1px solid #3A342D;
        transition: all 0.2s;
    }

    .stat-card-compact:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    }

    .stat-icon {
        font-size: 32px;
    }

    .stat-info {
        display: flex;
        flex-direction: column;
    }

    .stat-value {
        font-size: 32px;
        font-weight: 800;
        color: #C79745;
        line-height: 1;
    }

    .stat-label {
        font-size: 13px;
        color: #8A6A3C;
        margin-top: 4px;
    }

    .stat-badge {
        font-size: 10px;
        color: #B39A78;
        margin-top: 2px;
    }

    .stat-badge.urgent {
        color: #E07060;
    }

    /* Charts */
    .charts-row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
        margin-bottom: 28px;
    }

    .chart-card {
        background: white;
        border-radius: 24px;
        border: 1px solid #3A342D;
        padding: 20px;
    }

    .chart-header {
        margin-bottom: 20px;
        border-bottom: 1px solid #F0EBE0;
        padding-bottom: 12px;
    }

    .chart-header h3 {
        font-size: 16px;
        font-weight: 600;
        color: #2F2A27;
        margin: 0 0 4px 0;
    }

    .chart-subtitle {
        font-size: 11px;
        color: #B39A78;
    }

    .chart-body {
        position: relative;
        min-height: 220px;
    }

    /* Admin Grids */
    .admin-grid-3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
    }

    .admin-card {
        background: white;
        border-radius: 24px;
        border: 1px solid #3A342D;
        overflow: hidden;
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 18px 24px;
        border-bottom: 1px solid #F0EBE0;
    }

    .card-header h3 {
        font-size: 18px;
        font-weight: 600;
        color: #2F2A27;
        margin: 0;
    }

    .card-link {
        color: #BE9360;
        text-decoration: none;
        font-size: 12px;
        font-weight: 500;
    }

    .card-body {
        padding: 0 24px;
    }

    /* List Items */
    .list-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 0;
        border-bottom: 1px solid #F5F0E8;
    }

    .list-item-title {
        font-weight: 600;
        color: #2F2A27;
        font-size: 14px;
    }

    .list-item-meta {
        font-size: 11px;
        color: #B39A78;
    }

    .status-badge {
        font-size: 11px;
        padding: 4px 12px;
        border-radius: 20px;
        background: #F5F5F5;
        color: #8A7A66;
    }

    .status-in_progress {
        background: #FEF8F0;
        color: #BE9360;
    }

    .status-completed {
        background: #F0F8F0;
        color: #5A8A5A;
    }

    /* Stat Rows */
    .stat-row {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #F5F0E8;
    }

    .progress-section {
        margin-top: 16px;
        padding-top: 12px;
    }

    .progress-label {
        font-size: 12px;
        color: #7B746B;
        margin-bottom: 8px;
    }

    .progress-bar {
        height: 6px;
        background: #F0E8DC;
        border-radius: 10px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #C79745, #D6A85B);
        border-radius: 10px;
    }

    .progress-value {
        font-size: 12px;
        font-weight: 600;
        color: #C79745;
        margin-top: 6px;
        text-align: right;
    }

    /* Quick Actions */
    .quick-actions {
        display: flex;
        flex-direction: column;
        gap: 12px;
        padding: 8px 0;
    }

    .action-btn {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        background: #F8F4EC;
        border-radius: 12px;
        text-decoration: none;
        color: #2F2A27;
        font-size: 14px;
        transition: all 0.2s;
    }

    .action-btn:hover {
        background: #F0E8DC;
        transform: translateX(4px);
    }

    .empty-state {
        text-align: center;
        padding: 40px;
        color: #B39A78;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .stats-grid-compact {
            grid-template-columns: repeat(2, 1fr);
        }
        .charts-row {
            grid-template-columns: 1fr;
        }
        .admin-grid-3 {
            grid-template-columns: 1fr;
        }
    }
</style>