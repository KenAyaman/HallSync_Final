$ErrorActionPreference = 'Stop'

$root = (Resolve-Path '.').Path
$output = Join-Path $root 'HallSync_Laravel_Panel_Review_Guide.docx'
$work = Join-Path $root ('.docx_build_' + [guid]::NewGuid().ToString('N'))

function XmlEscape([string] $text) {
    return [System.Security.SecurityElement]::Escape($text)
}

function ParagraphXml {
    param(
        [string] $Text,
        [string] $Style = 'Normal',
        [bool] $Bullet = $false,
        [bool] $PageBreak = $false
    )

    if ($PageBreak) {
        return '<w:p><w:r><w:br w:type="page"/></w:r></w:p>'
    }

    $styleXml = ''
    if ($Style -ne 'Normal' -or $Bullet) {
        $styleXml = '<w:pPr>'
        if ($Style -ne 'Normal') {
            $styleXml += '<w:pStyle w:val="' + $Style + '"/>'
        }
        if ($Bullet) {
            $styleXml += '<w:numPr><w:ilvl w:val="0"/><w:numId w:val="1"/></w:numPr>'
        }
        $styleXml += '</w:pPr>'
    }

    $runPr = ''
    if ($Style -eq 'Code') {
        $runPr = '<w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas"/><w:sz w:val="18"/></w:rPr>'
    }

    return '<w:p>' + $styleXml + '<w:r>' + $runPr + '<w:t xml:space="preserve">' + (XmlEscape $Text) + '</w:t></w:r></w:p>'
}

$content = @'
# HallSync Laravel Panel Review Guide
Beginner-friendly capstone defense notes, panel walkthrough scripts, code explanations, graph/analytics breakdown, and Laravel study guide.

Prepared from the actual HallSync codebase in this workspace.

How to use this file: read Part 1 first if Laravel still feels confusing. Read Parts 2 to 6 before demo day. Use the demo scripts and panel Q&A near the end for oral practice.

## Table of Contents
- Part 1 - Big picture: what HallSync is and what stack it uses.
- Part 2 - Laravel basics explained from zero.
- Part 3 - File map: what the folders in this system mean.
- Part 4 - Panels: resident, admin/manager, and handyman workspaces.
- Part 5 - Graphs and analytics: what was used, how data becomes charts.
- Part 6 - Real-time integration: Echo, Reverb/websockets, heartbeat fallback.
- Part 7 - Database/models: how tables, records, and relationships work.
- Part 8 - Security and workflow rules: login, roles, middleware, validation.
- Part 9 - Testing and defense readiness.
- Part 10 - Demo script and panel Q&A cheat sheet.

[PAGE]
# Part 1 - What HallSync Is
HallSync is a role-based residence operations system. It gives residents, administrators/managers, and maintenance staff different panels because each role has different responsibilities.

## Strong Defense Sentence
HallSync separates resident requests, administrative decision-making, and maintenance execution into role-specific Laravel panels. The system uses database-backed workflows so every ticket, booking, concern, announcement, and community post can move through controlled statuses instead of being handled manually.

## Core Modules
- Authentication and account management: users log in, roles decide access, and temporary passwords force password change.
- Resident dashboard: shows a resident their maintenance tickets, bookings, notifications, announcements, and community updates.
- Admin/manager dashboard: command center with operational metrics, analytics, forecasts, recommended actions, audit logs, and management shortcuts.
- Handyman/staff dashboard: work queue, critical dispatch, assigned jobs, and completed task history.
- Maintenance tickets: residents submit issues; admins approve/reject/assign; staff update progress; residents can track status.
- Bookings: residents reserve facilities; admins view booking calendar and facility usage analytics.
- Announcements: admins publish notices visible to residents.
- Community hub: residents post; admins moderate; comments and likes support interaction.
- Concerns: private resident concern reports with evidence, messages, status history, and admin handling.
- Notifications and live updates: users see relevant updates, and dashboard pages refresh when important records change.

## Tech Stack Used
- Backend framework: Laravel 12, written in PHP 8.2.
- Frontend rendering: Blade templates, which are Laravel HTML files with PHP-style variables and directives.
- Styling: Tailwind CSS through Vite, plus many page-specific CSS blocks inside Blade views.
- JavaScript bundler: Vite, configured through vite.config.js and package.json.
- Frontend helpers: Alpine.js for small interactive behavior, axios for AJAX requests.
- Charts/graphs: Chart.js 4.4 loaded from CDN in the analytics/dashboard Blade pages.
- Real-time updates: Laravel Echo, Pusher JS client, Laravel Reverb, and a heartbeat polling fallback.
- Database access: Eloquent ORM models such as User, MaintenanceTicket, Booking, Concern, Announcement, and CommunityPost.
- Testing: PHPUnit feature tests under tests/Feature.
- Authentication starter style: Laravel Breeze-like structure with Blade views and auth controllers.

## Important Package Evidence
```
composer.json
"laravel/framework": "^12.0",
"laravel/reverb": "^1.10",
"beyondcode/laravel-websockets": "^1.16",
"pusher/pusher-php-server": "^7.0"

package.json
"alpinejs": "^3.4.2",
"axios": "^1.11.0",
"laravel-echo": "^1.14.0",
"pusher-js": "^8.0.0",
"laravel-vite-plugin": "^2.0.0",
"tailwindcss": "^3.1.0",
"vite": "^7.0.7"
```

## What To Review For Panels
- What is the purpose of each panel?
- What role can access it?
- What route opens it?
- What controller prepares its data?
- What Blade file displays it?
- What models/tables provide the data?
- What actions can the user perform from the panel?
- What security prevents another role from using it?
- What analytics or status information does it show?
- What part would you demo to prove it works?

[PAGE]
# Part 2 - Laravel From Zero
If you are a beginner, think of Laravel as an organized way to build a website where each request follows a path: route, controller, model/database, view, response.

## The Laravel Request Flow
- 1. A user visits a URL, for example /dashboard.
- 2. Laravel checks routes/web.php to find what code should handle that URL.
- 3. Middleware checks if the user is logged in and has the correct role.
- 4. A controller method runs. Controllers are like traffic managers for a page.
- 5. The controller asks models for database records.
- 6. The controller sends data to a Blade view.
- 7. The Blade view turns the data into HTML/CSS/JavaScript shown in the browser.

## Example From This System
```
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'password.changed'])
    ->name('dashboard');
```
Beginner explanation: this route says that when the user opens /dashboard, Laravel should run the index method of DashboardController. Before that happens, auth checks that the user is logged in and password.changed checks that temporary-password users changed their password.

## What MVC Means Here
- Model: represents a database table. Example: MaintenanceTicket is the PHP class for maintenance_tickets.
- View: the page template. Example: resources/views/dashboard/manager.blade.php.
- Controller: receives a request, fetches data, and chooses what view to show. Example: DashboardController.

## Blade Basics
- {{ $variable }} prints a value safely into HTML.
- @if, @foreach, and @forelse are Blade logic statements.
- route('tickets.index') generates a URL by route name.
- <x-app-layout> wraps the page inside the shared app layout.
- @vite loads compiled CSS and JS from resources/css/app.css and resources/js/app.js.

```
@foreach($recentTickets->take(3) as $ticket)
    <a href="{{ route('tickets.show', $ticket) }}">
        <h3>{{ $ticket->title }}</h3>
        <p>{{ Str::limit($ticket->description ?? '', 64) }}</p>
    </a>
@empty
    <div>No recent requests</div>
@endforelse
```
This Blade code loops through recent tickets. If there are no tickets, @empty displays an empty message. This is how the resident dashboard avoids showing blank panels.

## Eloquent Basics
```
MaintenanceTicket::where('user_id', $user->id)
    ->whereNotIn('status', ['closed', 'cancelled', 'rejected'])
    ->count();
```
This means: count the tickets that belong to the logged-in user and are still active. Eloquent lets you write database queries in PHP instead of writing raw SQL all the time.

## Common Beginner Confusions
- A migration is not the database itself. It is an instruction file that creates or changes database tables.
- A model is not a single row. It is a class that can represent one row or build queries for many rows.
- A controller does not display HTML by itself. It usually returns a Blade view.
- A route is not the page design. It only points Laravel to the controller/action.
- Blade is not plain HTML. It is HTML plus Laravel syntax.
- Vite does not replace Laravel. It only bundles frontend assets.
- Middleware is like a guard before the controller runs.

[PAGE]
# Part 3 - File Map For This Project
The system follows Laravel conventions. Routes define entry points, controllers implement application logic, models represent database records, migrations define the database schema, Blade views render the panels, and JavaScript/CSS improve the user interface.

## Important Folders
- routes/web.php - URL map for normal web pages.
- routes/auth.php - login, logout, forgot password, reset password, and password confirmation routes.
- app/Http/Controllers - page logic and workflow logic.
- app/Models - database table classes and relationships.
- app/Http/Middleware - request guards such as role checking and password-change enforcement.
- app/Policies - authorization rules for actions on records.
- app/Events - broadcast events, including DashboardUpdated.
- app/Notifications - notification classes used for status changes.
- database/migrations - versioned database table changes.
- database/seeders - sample/admin/resident/staff data creation.
- resources/views - Blade UI templates.
- resources/js - frontend JavaScript loaded by Vite.
- resources/css - global stylesheet.
- config - system settings for database, broadcasting, cache, session, Reverb, etc.
- tests/Feature - browser-like tests that verify workflows.
- public - publicly served images/assets such as White1.jpg, chair.png, and index.php.

## How To Explain The Folder Structure To A Panel
"The system follows Laravel's standard structure. Routes define the URL entry points, controllers prepare the data, models represent database tables, migrations define the database structure, and Blade views render the panels. JavaScript and CSS are bundled through Vite."

## Files You Should Review Hardest
- routes/web.php because it shows system access and module entry points.
- app/Http/Controllers/DashboardController.php because it powers the panels and analytics.
- app/Http/Controllers/AnalyticsController.php because it powers full analytics and CSV exports.
- resources/views/dashboard/manager.blade.php because it contains tabs, Chart.js setup, metrics, and live refresh.
- resources/views/dashboard/resident.blade.php because it shows beginner-friendly Blade loops and resident scoping.
- resources/views/dashboard/handyman.blade.php because it shows staff queue design.
- resources/js/real_time.js because it explains live updates.
- app/Models/MaintenanceTicket.php because it shows lifecycle statuses, relationships, and helper methods.
- app/Http/Middleware/RoleMiddleware.php because it explains role protection.

[PAGE]
# Part 4 - Role-Based Panels
The strongest panel defense point is that HallSync does not show the same dashboard to everyone. The dashboard changes based on the authenticated user role.

## Role Routing Code
```
public function index(Request $request)
{
    $user = Auth::user();

    if ($user->role === 'manager') {
        return view('dashboard.manager', [...]);
    }

    if ($user->role === 'handyman') {
        return redirect()->route('staff.overview');
    }

    return view('dashboard.resident', [...]);
}
```
Explanation: one /dashboard route decides which panel to show. Managers see the admin command center, handymen are redirected to staff overview, and residents see their personal resident dashboard.

## Panel 1 - Resident Dashboard
File: resources/views/dashboard/resident.blade.php. Controller data source: DashboardController@index resident branch.

- Purpose: give residents one place to manage their own activity.
- Shows active repairs, in-progress repairs, upcoming bookings, pending community posts, recent tickets, notifications, announcements, and community board previews.
- Only resident-owned data is counted for tickets and bookings.
- Cards are clickable so residents can jump directly to tickets, bookings, and community pages.
- Uses Blade loops, route() helpers, status chips, empty states, and responsive CSS.

```
return view('dashboard.resident', [
    'activeTickets' => MaintenanceTicket::where('user_id', $user->id)
        ->whereNotIn('status', ['closed', 'cancelled', 'rejected'])
        ->count(),
    'recentTickets' => MaintenanceTicket::where('user_id', $user->id)
        ->latest()
        ->take(5)
        ->get(),
]);
```
How to explain it: The resident panel is scoped to the logged-in user. That means residents cannot see other residents' tickets because the query filters by user_id.

## Panel 2 - Admin/Manager Dashboard
File: resources/views/dashboard/manager.blade.php. Controller data source: DashboardController@index manager branch.

- Purpose: command center for operations.
- Shows open tickets, urgent tickets, bookings, residents, active residents, recent activity logs, announcements, staff list, operational intelligence, analytics, forecasts, and recommended actions.
- Uses tabs: Command Center, Analytics & Trends, Predictive Ops, Activity Logs.
- Uses Chart.js for line, bar, and doughnut charts.
- Includes live metric refresh via a JSON endpoint and real-time updates.
- Exports analytics CSV from AnalyticsController.

```
'openTickets' => $managerMetrics['openTickets'],
'urgentTickets' => $managerMetrics['urgentTickets'],
'pendingBookings' => $managerMetrics['pendingBookings'],
'totalResidents' => $managerMetrics['totalResidents'],
'deepAnalytics' => $this->getDeepAnalyticsData($analyticsFilters),
'ticketTrendLabels' => $ticketTrendData['labels'],
'ticketTrendData' => $ticketTrendData['data'],
'predictiveAnalytics' => $predictiveAnalytics,
```
How to explain it: The manager dashboard is not just decoration. It summarizes operational pressure and turns data into decisions: what needs assignment, what is aging, what category is rising, and what facility may need preparation.

## Panel 3 - Handyman/Staff Dashboard
File: resources/views/dashboard/handyman.blade.php. Controller data source: DashboardController@staffOverview and getStaffWorkspaceData().

- Purpose: help maintenance staff focus on assigned work.
- Shows assigned tickets, in-progress tickets, completed work, and critical dispatch.
- Separates urgent tickets from normal queue work.
- Staff routes are under /staff and protected by role:handyman middleware.
- Staff do not see manager analytics because their work is execution-focused.

```
Route::middleware(['auth', 'password.changed', 'role:handyman'])
    ->prefix('staff')
    ->name('staff.')
    ->group(function () {
        Route::get('/overview', [DashboardController::class, 'staffOverview'])->name('overview');
        Route::get('/work-queue', [DashboardController::class, 'staffQueue'])->name('queue');
        Route::get('/completed', [DashboardController::class, 'staffCompleted'])->name('completed');
    });
```
How to explain it: The handyman panel is a focused work queue. It avoids admin data and prioritizes the exact records assigned to that staff account.

[PAGE]
# Part 5 - Graphs And Analytics
The graphs use Chart.js. Chart.js is a JavaScript charting library. In this system, PHP/Laravel prepares arrays of labels and numbers, Blade injects them into JavaScript with @json, and Chart.js draws them inside canvas elements.

## Where The Graphs Are
- resources/views/dashboard/manager.blade.php - dashboard charts inside the manager panel.
- resources/views/analytics/index.blade.php - full analytics page with charts and export links.
- app/Http/Controllers/DashboardController.php - prepares dashboard chart data.
- app/Http/Controllers/AnalyticsController.php - prepares full analytics data and CSV exports.

## Chart.js Evidence
```
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

new Chart(document.getElementById('ticketTrendChart'), {
    type: 'line',
    data: {
        labels: @json($trendLabels),
        datasets: [{
            data: @json($trendData),
            borderColor: amber,
            backgroundColor: amberFill,
            tension: .32,
            fill: true
        }]
    },
    options: chartOptions
});
```
Beginner explanation: canvas is the drawing area, new Chart(...) creates the graph, type chooses the graph style, labels are the x-axis text, datasets are the numbers, and options control the look/behavior.

## How Laravel Creates Ticket Trend Data
```
$counts = $this->filteredTicketQuery($filters)
    ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
    ->whereBetween('created_at', [$start, $end])
    ->groupByRaw('DATE(created_at)')
    ->pluck('total', 'day');

for ($i = $days - 1; $i >= 0; $i--) {
    $date = $end->copy()->subDays($i);
    $labels[] = $date->format('M d');
    $data[] = (int) ($counts[$date->toDateString()] ?? 0);
}
```
Explanation: the query groups maintenance tickets by date. The loop fills missing days with 0 so the chart always has a complete timeline. This is better than showing only days that have tickets.

## Graph Types In HallSync
- Line chart: ticket volume over time and forecast trends.
- Bar chart: category demand, facility booking usage, and day-of-week pattern.
- Doughnut chart: open work distribution by status.
- Funnel-style custom UI: submitted to approved to assigned to in-progress to resolved.
- Inline bar indicators: age buckets, staff performance, and diagnostic rows.

## Analytics Categories Explained For Defense
- Descriptive analytics: what happened? Example: tickets created, bookings made, categories reported.
- Diagnostic analytics: why might it be happening? Example: one category is increasing or tickets wait too long before work starts.
- Predictive analytics: what might happen next? Example: next 7-day maintenance demand forecast.
- Prescriptive analytics: what should admins do? Example: assign critical tickets, review aging backlog, respond to concerns, prepare busy facilities.

## Predictive Analytics In Simple Words
```
$previousAverage = $dailyTicketCounts->take(14)->avg('count');
$recentAverage = $dailyTicketCounts->skip(14)->avg('count');
$dailyTrend = ($recentAverage - $previousAverage) / 14;
$forecastCounts = collect(range(1, 7))->map(
    fn ($day) => max(0, (int) round($recentAverage + ($dailyTrend * $day)))
);
```
Explanation: the code compares older recent data with newer recent data. If the newer average is higher, the forecast trends upward. It is not machine learning; it is a simple moving-trend forecast, which is easier to explain and defend for a capstone.

## CSV Export Demo
```
Route::get('/analytics/export/tickets', [AnalyticsController::class, 'exportTickets'])
    ->name('analytics.export.tickets');

return response()->stream(function () use ($tickets) {
    $h = fopen('php://output', 'w');
    fputcsv($h, ['ID', 'Title', 'Category', 'Priority', 'Status']);
    foreach ($tickets as $t) {
        fputcsv($h, [$t->ticket_id ?? $t->id, $t->title, $t->category, $t->priority, $t->status]);
    }
}, 200, ['Content-Type' => 'text/csv']);
```
Explanation: this does not create a saved file on the server. It streams CSV rows directly to the browser as a download.

[PAGE]
# Part 6 - Real-Time Integration
HallSync uses two layers for freshness: real-time broadcasting and heartbeat polling fallback. This makes the demo easier to defend because the system still updates even if websockets are unavailable.

## Packages Used
- Laravel Reverb: Laravel-native websocket server.
- Laravel Echo: JavaScript client that listens to broadcast channels.
- Pusher JS: protocol/client used by Echo.
- DashboardUpdated event: Laravel event broadcast when records change.
- Heartbeat endpoint: /live/heartbeat returns latest updated_at visible to the user.

## Broadcast Event
```
class DashboardUpdated implements ShouldBroadcastNow
{
    public function broadcastOn(): Channel
    {
        return new Channel('dashboard-updates');
    }

    public function broadcastAs(): string
    {
        return 'DashboardUpdated';
    }
}
```
Explanation: ShouldBroadcastNow means the event is broadcast immediately. The browser listens to the dashboard-updates channel.

## Automatic Broadcasting From Models
```
trait BroadcastsDashboardUpdates
{
    protected static function bootBroadcastsDashboardUpdates(): void
    {
        static::created(fn () => static::broadcastDashboardUpdate());
        static::updated(fn () => static::broadcastDashboardUpdate());
        static::deleted(fn () => static::broadcastDashboardUpdate());
    }
}
```
Explanation: models using this trait broadcast when records are created, updated, or deleted. MaintenanceTicket, Booking, and User use this pattern, so dashboards know when operational data changes.

## Frontend Listener
```
echo.channel('dashboard-updates').listen('.DashboardUpdated', () => {
    if (typeof window.refreshManagerDashboardMetrics === 'function') {
        window.refreshManagerDashboardMetrics();
        return;
    }

    if (pageRecentlyLoaded() || hasSubmittingForm() || hasActiveToast()) {
        return;
    }

    window.location.reload();
});
```
Explanation: on manager dashboard, it refreshes metrics without full reload. On other live pages, it reloads only if safe. It avoids wiping flash messages right after a form submission.

## Heartbeat Fallback
```
Route::get('/live/heartbeat', [DashboardController::class, 'heartbeat'])
    ->middleware(['auth', 'password.changed'])
    ->name('live.heartbeat');
```
The heartbeat endpoint returns the newest updated_at timestamp that the current user is allowed to know about. This matters for privacy: residents should not infer that another resident created a private ticket.

## Defense Sentence
We integrated live updates using Laravel Echo and Reverb. When operational records change, the backend broadcasts a DashboardUpdated event. The frontend listens for that event and either refreshes dashboard metrics or reloads safe pages. We also added a heartbeat fallback so the system still stays fresh if websocket connection fails.

[PAGE]
# Part 7 - Database, Models, And Relationships
Laravel models are the bridge between PHP code and database tables. Instead of manually writing SQL for everything, the project uses Eloquent models.

## Important Models
- User - all accounts: residents, managers, and handymen.
- MaintenanceTicket - maintenance requests and repair lifecycle.
- Booking - facility reservations.
- Announcement - admin-created notices.
- CommunityPost and CommunityComment - community board content.
- Concern - private concern reports.
- ConcernMessage, ConcernEvidence, ConcernStatusHistory - concern workflow details.
- UserActivityLog - operational audit trail.
- NotificationRead - tracks read/unread notification state.

## Model Fillable Fields
```
protected $fillable = [
    'user_id',
    'assigned_to',
    'ticket_id',
    'title',
    'description',
    'category',
    'location',
    'priority',
    'status',
];
```
Explanation: fillable means these fields are allowed for mass assignment. It is a Laravel safety feature so users cannot submit unexpected fields such as is_admin.

## Relationships
```
public function user()
{
    return $this->belongsTo(User::class, 'user_id');
}

public function assignedTo()
{
    return $this->belongsTo(User::class, 'assigned_to');
}
```
Explanation: a maintenance ticket belongs to the resident who submitted it and can also belong to a staff user assigned to it. This is why code can use $ticket->user->name or $ticket->assignedTo->name.

```
public function maintenanceTickets()
{
    return $this->hasMany(MaintenanceTicket::class);
}

public function assignedTickets()
{
    return $this->hasMany(MaintenanceTicket::class, 'assigned_to');
}
```
Explanation: a user can submit many maintenance tickets, and a staff user can be assigned many tickets.

## Status Lifecycle For Tickets
- pending_approval - resident submitted the ticket and admin must review.
- approved - admin accepted the request.
- assigned - admin assigned staff.
- in_progress - handyman started work.
- resolved - staff finished and resident/admin can review closure.
- closed - completed record is final.
- rejected - admin rejected the request.
- cancelled - request was cancelled.

## Migration Meaning
Migrations are the version history of your database. Example: one migration creates maintenance_tickets, later migrations add category, assigned_to, media fields, lifecycle fields, priority changes, soft deletes, and location.

Defense sentence: We used Laravel migrations so the database structure is repeatable. A developer can run php artisan migrate and Laravel applies the table definitions in order.

[PAGE]
# Part 8 - Security, Roles, Validation, And Workflow Rules

## Role Middleware
```
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user()->role !== $role) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
```
Explanation: middleware runs before protected controller logic. If a resident tries to open a manager route, the system aborts with 403 Unauthorized.

## Route-Level Protection Examples
- Admin analytics routes use role:manager.
- Staff routes use role:handyman.
- Resident concerns routes use role:resident.
- Most routes also require auth and password.changed.
- Ticket creation, booking creation, concern creation, and community posting are rate-limited.

```
Route::post('/tickets', [TicketController::class, 'store'])
    ->name('tickets.store')
    ->middleware('throttle:4,5');

Route::post('/concerns', [ConcernController::class, 'store'])
    ->name('concerns.store')
    ->middleware('throttle:3,10');
```
Explanation: throttle:4,5 means a user can submit only 4 tickets per 5 minutes. This reduces spam and accidental repeated submissions.

## Soft Deletes
Some models use SoftDeletes. That means deleted records are not immediately removed from the database; Laravel marks them with deleted_at. This helps preserve operational history for capstone defense and audit purposes.

## Validation And Double Submit Protection
- Laravel controllers validate incoming form data before saving.
- Forms use CSRF protection through the meta csrf-token and Laravel form tokens.
- JavaScript disables submit buttons after submission to reduce duplicate records.
- MaintenanceTicket::isRecentDuplicate checks for similar recent submissions.
- Status labels and normalized priority methods keep display values consistent.

## Account Safety
- User model prevents deleting the last active manager.
- Users with operational history should be deactivated instead of deleted.
- Temporary password is hidden/encrypted.
- must_change_password forces users to update temporary credentials.

[PAGE]
# Part 9 - Testing And Readiness
Tests live in tests/Feature and tests/Unit. Feature tests act like a user visiting pages and submitting forms. They help prove that important workflows still work after changes.

## Important Test Areas
- AuthenticationTest - login/logout/auth behavior.
- BookingFlowTest - booking workflows.
- ConcernFlowTest and ConcernManagementSystemTest - concern reporting and admin handling.
- CommunityModerationTest - community approval/rejection.
- DashboardAnalyticsTest - dashboard analytics content.
- LifecycleWorkflowTest - record lifecycle behavior.
- NotificationReadTest - notification read/open flow.
- ProductionHardeningTest - security and resilience checks.
- UserManagementTest - admin user workflows.

## Known Testing Note
The project includes MySQL-only dashboard analytics tests because some analytics queries use database-specific date functions. If tests are run under SQLite, those tests are skipped or may need compatibility adjustments. This is a reasonable technical discussion point: production uses MySQL-style behavior, while SQLite test environments may differ.

## How To Talk About Tests
We wrote feature tests for important workflows such as authentication, booking, concerns, community moderation, notifications, dashboard analytics, and user management. These tests verify that routes respond correctly, users see expected content, and role-based workflows behave as intended.

[PAGE]
# Part 10 - Demo Script
Use this section when practicing your capstone demo. Do not memorize every word. Understand the flow and adapt it naturally.

## Demo Opening
Good day. This is HallSync, a Laravel-based residence operations system. The system separates workflows by role: residents submit requests and bookings, administrators manage operations and analytics, and maintenance staff handle assigned work. The goal is to replace scattered manual coordination with a controlled, trackable, database-backed process.

## Demo 1 - Login And Role-Based Dashboard
- Log in as a manager and open /dashboard.
- Explain that /dashboard runs DashboardController@index.
- Point out that the controller checks Auth::user()->role.
- Show the manager command center.
- Mention that residents and handymen see different panels from the same dashboard entry point.

## Demo 2 - Resident Maintenance Ticket Flow
- Log in as resident.
- Click Report Maintenance Issue.
- Submit a ticket with category, title, description, priority, and location.
- Show it in Recent Requests or ticket list.
- Log in as manager and approve/assign it.
- Log in as handyman and show it in work queue.
- Update status to in progress/resolved.
- Return to resident tracking page and show lifecycle progress.

### Talking Point For Demo 2
This demonstrates the complete workflow: resident submission, admin decision, staff assignment, staff update, and resident tracking. The same database record moves through statuses instead of creating disconnected records.

## Demo 3 - Graphs And Analytics
- Open the manager dashboard Analytics & Trends tab.
- Show ticket volume chart.
- Explain Chart.js draws the graph, but Laravel prepares the data.
- Show category/facility/status charts.
- Open full analytics page.
- Use date filters if available.
- Click export tickets/bookings.

### Talking Point For Demo 3
The analytics are generated from actual records. Laravel groups tickets by date, category, status, and facility, then passes the labels and counts to Chart.js. This turns operational history into decisions such as staffing, facility preparation, and backlog review.

## Demo 4 - Real-Time Update Integration
- Keep the manager dashboard open.
- In another role/session, create or update a ticket/booking/concern.
- Explain that the model broadcasts DashboardUpdated on create/update/delete.
- Mention Laravel Echo/Reverb listens in resources/js/real_time.js.
- Show that manager metrics can refresh without manual reload.
- If websockets are unavailable, explain the heartbeat fallback.

## Demo 5 - Admin User Management And Security
- Open admin users.
- Show search/filter/status actions.
- Explain roles: manager, resident, handyman.
- Explain route middleware and User model safety rules.
- Mention last active manager cannot be deleted.

[PAGE]
# Panel Questions And Easy Answers

## Question: What stack did you use?
Answer: We used Laravel 12 with PHP 8.2 for the backend, Blade for server-rendered views, Tailwind/Vite for frontend assets, Alpine.js and axios for frontend behavior, Chart.js for graphs, and Laravel Echo/Reverb for real-time dashboard updates.

## Question: What did you use for graphs?
Answer: We used Chart.js. The backend prepares the analytics arrays using Eloquent queries, Blade injects them into JavaScript using @json, and Chart.js renders the charts inside canvas elements.

## Question: Why use role-based panels?
Answer: Each role has a different responsibility. Residents need self-service submission and tracking. Managers need decision-making, analytics, moderation, and assignment tools. Handymen need a focused work queue. Role-based panels reduce confusion and protect unauthorized data.

## Question: How do you prevent unauthorized access?
Answer: Routes use auth middleware and role middleware. If a user is not logged in, they are redirected to login. If they have the wrong role, Laravel aborts with a 403 error. Controllers and policies also add workflow-level checks.

## Question: How does the manager dashboard get its numbers?
Answer: DashboardController queries models such as MaintenanceTicket, Booking, User, Concern, and Announcement. It counts open tickets, urgent tickets, active residents, bookings, concerns, and recent activity, then passes those values to dashboard.manager.blade.php.

## Question: Is the forecast AI?
Answer: No. It is a simple moving-trend forecast. The system compares recent ticket averages with previous ticket averages and projects the next seven days. We chose this because it is transparent and easy to explain.

## Question: How are real-time updates integrated?
Answer: Models trigger a DashboardUpdated event when records change. Laravel broadcasts it through Reverb/websockets. The frontend listens with Laravel Echo and updates metrics or reloads safe pages. A heartbeat endpoint acts as fallback.

## Question: Why use Laravel instead of plain PHP?
Answer: Laravel gives a structured way to build authentication, routing, database access, validation, middleware, views, and tests. It keeps the capstone maintainable compared with manually mixing SQL, PHP, and HTML in one file.

## Question: How do models help?
Answer: Models represent tables and relationships. For example, a ticket belongs to a user and may belong to an assigned staff member. This makes it easy to display related data such as resident name or assigned handyman.

[PAGE]
# Beginner Study Drills

## Drill 1: Explain one route
Pick one line in routes/web.php. Say the URL, controller, method, middleware, and route name. Example: /dashboard -> DashboardController@index -> requires auth and password.changed -> route name dashboard.

## Drill 2: Trace one value
Trace openTickets: DashboardController calculates it, passes it to dashboard.manager.blade.php, Blade prints it inside a metric card, JavaScript can refresh it through admin.dashboard.metrics.

## Drill 3: Explain one model relationship
A MaintenanceTicket belongsTo User through user_id. That is how the ticket knows who submitted it. User hasMany MaintenanceTicket, meaning one resident can submit many tickets.

## Drill 4: Explain one chart
Ticket Volume chart: Controller groups tickets by created_at date, creates labels like Jun 01 and values like 3, then Chart.js renders those numbers as a line chart.

## Drill 5: Explain one security rule
role:manager protects admin analytics. If the authenticated user role is not manager, RoleMiddleware returns 403 Unauthorized.

# Final Review Checklist
- Can I explain Laravel request flow?
- Can I name the tech stack?
- Can I explain what Chart.js does?
- Can I trace dashboard data from controller to Blade?
- Can I explain role middleware?
- Can I explain resident/admin/handyman panel differences?
- Can I explain ticket lifecycle statuses?
- Can I explain real-time updates and fallback polling?
- Can I demo one complete ticket workflow?
- Can I answer why this system is useful compared with manual coordination?

# Short Closing Script
HallSync was built as a Laravel system because the project needs structured routing, authentication, database models, role-based access, and maintainable views. The panels are separated by user responsibility: residents submit and track, managers analyze and decide, and staff execute assigned work. The analytics use Chart.js with backend-prepared data, and live updates use Laravel Echo/Reverb with a heartbeat fallback. Overall, the system turns residence operations into trackable workflows with clear accountability.
'@

New-Item -ItemType Directory -Path $work, (Join-Path $work '_rels'), (Join-Path $work 'word'), (Join-Path $work 'word\_rels') | Out-Null

$paragraphs = New-Object System.Collections.Generic.List[string]
$inCode = $false

foreach ($rawLine in ($content -split "`r?`n")) {
    $line = $rawLine.TrimEnd()
    if ($line -eq '[PAGE]') {
        $paragraphs.Add((ParagraphXml -Text '' -PageBreak $true))
        continue
    }
    if ($line -eq '```') {
        $inCode = -not $inCode
        continue
    }
    if ($inCode) {
        $paragraphs.Add((ParagraphXml -Text $line -Style 'Code'))
        continue
    }
    if ($line.Trim().Length -eq 0) {
        $paragraphs.Add((ParagraphXml -Text ''))
        continue
    }
    if ($line.StartsWith('### ')) {
        $paragraphs.Add((ParagraphXml -Text $line.Substring(4) -Style 'Heading3'))
    } elseif ($line.StartsWith('## ')) {
        $paragraphs.Add((ParagraphXml -Text $line.Substring(3) -Style 'Heading2'))
    } elseif ($line.StartsWith('# ')) {
        $paragraphs.Add((ParagraphXml -Text $line.Substring(2) -Style 'Heading1'))
    } elseif ($line.StartsWith('- ')) {
        $paragraphs.Add((ParagraphXml -Text $line.Substring(2) -Bullet $true))
    } else {
        $paragraphs.Add((ParagraphXml -Text $line))
    }
}

$documentXml = @'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:document xmlns:wpc="http://schemas.microsoft.com/office/word/2010/wordprocessingCanvas" xmlns:mc="http://schemas.openxmlformats.org/markup-compatibility/2006" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" xmlns:m="http://schemas.openxmlformats.org/officeDocument/2006/math" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:wp14="http://schemas.microsoft.com/office/word/2010/wordprocessingDrawing" xmlns:wp="http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing" xmlns:w10="urn:schemas-microsoft-com:office:word" xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main" xmlns:w14="http://schemas.microsoft.com/office/word/2010/wordml" xmlns:wpg="http://schemas.microsoft.com/office/word/2010/wordprocessingGroup" xmlns:wpi="http://schemas.microsoft.com/office/word/2010/wordprocessingInk" xmlns:wne="http://schemas.microsoft.com/office/word/2006/wordml" xmlns:wps="http://schemas.microsoft.com/office/word/2010/wordprocessingShape" mc:Ignorable="w14 wp14">
<w:body>
'@ + ($paragraphs -join "`n") + @'
<w:sectPr><w:pgSz w:w="12240" w:h="15840"/><w:pgMar w:top="720" w:right="720" w:bottom="720" w:left="720" w:header="720" w:footer="720" w:gutter="0"/></w:sectPr>
</w:body></w:document>
'@

$stylesXml = @'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:styles xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
<w:style w:type="paragraph" w:default="1" w:styleId="Normal"><w:name w:val="Normal"/><w:pPr><w:spacing w:after="120" w:line="276" w:lineRule="auto"/></w:pPr><w:rPr><w:rFonts w:ascii="Aptos" w:hAnsi="Aptos"/><w:sz w:val="22"/></w:rPr></w:style>
<w:style w:type="paragraph" w:styleId="Heading1"><w:name w:val="heading 1"/><w:basedOn w:val="Normal"/><w:next w:val="Normal"/><w:qFormat/><w:pPr><w:spacing w:before="240" w:after="160"/></w:pPr><w:rPr><w:b/><w:color w:val="1F4E79"/><w:sz w:val="34"/></w:rPr></w:style>
<w:style w:type="paragraph" w:styleId="Heading2"><w:name w:val="heading 2"/><w:basedOn w:val="Normal"/><w:next w:val="Normal"/><w:qFormat/><w:pPr><w:spacing w:before="200" w:after="120"/></w:pPr><w:rPr><w:b/><w:color w:val="2F5496"/><w:sz w:val="28"/></w:rPr></w:style>
<w:style w:type="paragraph" w:styleId="Heading3"><w:name w:val="heading 3"/><w:basedOn w:val="Normal"/><w:next w:val="Normal"/><w:qFormat/><w:pPr><w:spacing w:before="160" w:after="100"/></w:pPr><w:rPr><w:b/><w:color w:val="5B6770"/><w:sz w:val="24"/></w:rPr></w:style>
<w:style w:type="paragraph" w:styleId="Code"><w:name w:val="Code"/><w:basedOn w:val="Normal"/><w:pPr><w:spacing w:before="20" w:after="20"/><w:shd w:val="clear" w:fill="F3F4F6"/></w:pPr><w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas"/><w:sz w:val="18"/></w:rPr></w:style>
</w:styles>
'@

$numberingXml = @'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:numbering xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
<w:abstractNum w:abstractNumId="0"><w:multiLevelType w:val="hybridMultilevel"/><w:lvl w:ilvl="0"><w:start w:val="1"/><w:numFmt w:val="bullet"/><w:lvlText w:val="•"/><w:lvlJc w:val="left"/><w:pPr><w:ind w:left="720" w:hanging="360"/></w:pPr><w:rPr><w:rFonts w:ascii="Symbol" w:hAnsi="Symbol" w:hint="default"/></w:rPr></w:lvl></w:abstractNum>
<w:num w:numId="1"><w:abstractNumId w:val="0"/></w:num>
</w:numbering>
'@

$contentTypes = @'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
<Default Extension="xml" ContentType="application/xml"/>
<Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>
<Override PartName="/word/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.styles+xml"/>
<Override PartName="/word/numbering.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.numbering+xml"/>
</Types>
'@

$rels = @'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>
</Relationships>
'@

$docRels = @'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/numbering" Target="numbering.xml"/>
</Relationships>
'@

$utf8 = New-Object System.Text.UTF8Encoding($false)
[System.IO.File]::WriteAllText((Join-Path $work '[Content_Types].xml'), $contentTypes, $utf8)
[System.IO.File]::WriteAllText((Join-Path $work '_rels\.rels'), $rels, $utf8)
[System.IO.File]::WriteAllText((Join-Path $work 'word\document.xml'), $documentXml, $utf8)
[System.IO.File]::WriteAllText((Join-Path $work 'word\styles.xml'), $stylesXml, $utf8)
[System.IO.File]::WriteAllText((Join-Path $work 'word\numbering.xml'), $numberingXml, $utf8)
[System.IO.File]::WriteAllText((Join-Path $work 'word\_rels\document.xml.rels'), $docRels, $utf8)

if (Test-Path -LiteralPath $output) {
    Remove-Item -LiteralPath $output -Force
}

$zipPath = Join-Path $root ('HallSync_Laravel_Panel_Review_Guide_' + [guid]::NewGuid().ToString('N') + '.zip')
Compress-Archive -Path (Join-Path $work '*') -DestinationPath $zipPath -Force
Move-Item -LiteralPath $zipPath -Destination $output -Force

Write-Output "Created: $output"
