<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\ConcernController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CommunityPostController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\Auth\ChangePasswordController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'password.changed'])
    ->name('dashboard');

Route::get('/live/heartbeat', [DashboardController::class, 'heartbeat'])
    ->middleware(['auth', 'password.changed'])
    ->name('live.heartbeat');

Route::middleware(['auth', 'password.changed', 'role:handyman'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/overview', [DashboardController::class, 'staffOverview'])->name('overview');
    Route::get('/work-queue', [DashboardController::class, 'staffQueue'])->name('queue');
    Route::get('/completed', [DashboardController::class, 'staffCompleted'])->name('completed');
});

Route::middleware('auth')->group(function () {
    Route::get('/change-password', [ChangePasswordController::class, 'show'])->name('password.change');
    Route::post('/change-password', [ChangePasswordController::class, 'update'])->name('password.change.update');
});

Route::middleware(['auth', 'password.changed'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

Route::middleware(['auth', 'password.changed'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/poll', [NotificationController::class, 'poll'])->name('notifications.poll');
    Route::get('/notifications/open/{type}/{id}', [NotificationController::class, 'open'])->name('notifications.open');
    Route::get('/notifications/database/{notification}', [NotificationController::class, 'openDatabase'])
        ->name('notifications.database.open');
    Route::get('/media/tickets/{ticket}/{type}', [MediaController::class, 'ticket'])
        ->whereIn('type', ['image', 'video'])
        ->name('media.tickets.show');
    Route::get('/media/community/{communityPost}/{type}', [MediaController::class, 'community'])
        ->whereIn('type', ['image', 'video'])
        ->name('media.community.show');
    Route::get('/media/users/{user}/profile-photo', [MediaController::class, 'profile'])
        ->name('media.users.profile-photo');
});

require __DIR__.'/auth.php';

// ==================== TICKET ROUTES ====================
Route::middleware(['auth', 'password.changed'])->group(function () {
    Route::get('/tickets/{ticket}/track', [TicketController::class, 'track'])->name('tickets.track');

    // Rate-limit ticket creation: 4 submissions per 5 minutes per user.
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store')->middleware('throttle:4,5');
    Route::resource('tickets', TicketController::class)->except('store');

    // Ticket status update (for handymen)
    Route::patch('/tickets/{ticket}/status', [TicketController::class, 'updateStatus'])->name('tickets.update-status');
    Route::patch('/tickets/{ticket}/reopen', [TicketController::class, 'reopen'])->name('tickets.reopen');
    Route::patch('/tickets/{ticket}/close', [TicketController::class, 'close'])->name('tickets.close');
    Route::patch('/tickets/{ticket}/request-cancellation', [TicketController::class, 'requestCancellation'])->name('tickets.request-cancellation');

    // Admin approval routes
    Route::post('/tickets/{ticket}/approve', [TicketController::class, 'approve'])->name('tickets.approve')->middleware(['auth', 'password.changed', 'role:manager']);
    Route::post('/tickets/{ticket}/reject', [TicketController::class, 'reject'])->name('tickets.reject')->middleware(['auth', 'password.changed', 'role:manager']);
    Route::post('/tickets/{ticket}/assign', [TicketController::class, 'assign'])->name('tickets.assign')->middleware(['auth', 'password.changed', 'role:manager']);
    Route::patch('/tickets/{ticket}/cancel', [TicketController::class, 'cancel'])->name('tickets.cancel')->middleware(['auth', 'password.changed', 'role:manager']);
});

Route::middleware(['auth', 'password.changed'])->group(function () {
    Route::get('/bookings/reserved-slots', [BookingController::class, 'getReservedSlots'])->name('bookings.reserved-slots');

    // Rate-limit booking creation: 6 per 5 minutes (covers rapid retries on slot conflicts).
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::resource('bookings', BookingController::class)->except('store');
});

Route::middleware(['auth', 'password.changed', 'role:manager'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard/metrics', [DashboardController::class, 'managerMetrics'])->name('dashboard.metrics');
    Route::get('/dashboard/stats', [DashboardController::class, 'pollStats'])->name('dashboard.stats');
    Route::get('/maintenance/latest', [DashboardController::class, 'pollMaintenance'])->name('maintenance.latest');
    Route::get('/community/latest', [DashboardController::class, 'pollCommunity'])->name('community.latest');
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');
    Route::get('/analytics/export/tickets', [AnalyticsController::class, 'exportTickets'])->name('analytics.export.tickets');
    Route::get('/analytics/export/bookings', [AnalyticsController::class, 'exportBookings'])->name('analytics.export.bookings');
    Route::get('/bookings/calendar', [BookingController::class, 'calendar'])->name('bookings.calendar');
    Route::get('/bookings/{booking}/details', [BookingController::class, 'getBookingDetails'])->name('bookings.details');
    Route::patch('/bookings/{booking}/cancel', [BookingController::class, 'adminCancel'])->name('bookings.cancel');

    Route::get('/users', [UserController::class, 'index'])->name('users');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::patch('/users/bulk-status', [UserController::class, 'bulkUpdate'])->name('users.bulk-status');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::patch('/users/{user}/status', [UserController::class, 'updateStatus'])->name('users.update-status');
    Route::patch('/users/{user}/password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    Route::patch('/users/{user}/move-out', [UserController::class, 'moveOut'])->name('users.move-out');
});

Route::middleware(['auth', 'password.changed', 'role:resident'])->group(function () {
    Route::post('/tickets/{ticket}/rate', [TicketController::class, 'rate'])->name('tickets.rate');

    Route::get('/concerns', [ConcernController::class, 'index'])->name('concerns.index');
    Route::get('/concerns/create', [ConcernController::class, 'create'])->name('concerns.create');
    // Rate-limit concern creation: 3 per 10 minutes.
    Route::post('/concerns', [ConcernController::class, 'store'])->name('concerns.store');

    Route::get('/concerns/{concern}', [ConcernController::class, 'show'])->name('concerns.show');
    Route::get('/concerns/{concern}/edit', [ConcernController::class, 'edit'])->name('concerns.edit');
    Route::patch('/concerns/{concern}', [ConcernController::class, 'update'])->name('concerns.update');
    Route::delete('/concerns/{concern}', [ConcernController::class, 'destroy'])->name('concerns.destroy');
    Route::patch('/concerns/{concern}/decision', [ConcernController::class, 'residentDecision'])->name('concerns.decision');
});

Route::middleware(['auth', 'password.changed'])->group(function () {
    Route::post('/concerns/{concern}/messages', [ConcernController::class, 'addMessage'])->name('concerns.messages.store');
    Route::post('/concerns/{concern}/evidence', [ConcernController::class, 'addEvidence'])->name('concerns.evidence.store');
    Route::get('/concerns/{concern}/evidence/{evidence}', [ConcernController::class, 'downloadEvidence'])->name('concerns.evidence.download');
});

Route::middleware(['auth', 'password.changed', 'role:manager'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/concerns', [ConcernController::class, 'adminIndex'])->name('concerns.index');
    Route::get('/concerns/{concern}', [ConcernController::class, 'adminShow'])->name('concerns.show');
    Route::patch('/concerns/{concern}', [ConcernController::class, 'adminUpdate'])->name('concerns.update');
    Route::patch('/concerns/{concern}/status', [ConcernController::class, 'adminTransition'])->name('concerns.transition');
    Route::post('/concerns/{concern}/assign', [ConcernController::class, 'assign'])->name('concerns.assign');
    Route::post('/concerns/{concern}/notes', [ConcernController::class, 'addInternalNote'])->name('concerns.notes.store');
});

// ==================== ANNOUNCEMENT ROUTES ====================
Route::middleware(['auth', 'password.changed'])->group(function () {
    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');

    // NOTE: this literal route must stay ABOVE the '/announcements/{announcement}' wildcard
    // route below, or Laravel matches "create" as the {announcement} param instead.
    Route::middleware(['role:manager'])->group(function () {
        Route::get('/announcements/create', [AnnouncementController::class, 'create'])->name('announcements.create');
    });

    Route::get('/announcements/{announcement}', [AnnouncementController::class, 'show'])->name('announcements.show');
});

Route::middleware(['auth', 'password.changed', 'role:manager'])->group(function () {
    Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
    Route::get('/announcements/{announcement}/edit', [AnnouncementController::class, 'edit'])->name('announcements.edit');
    Route::match(['put', 'patch'], '/announcements/{announcement}', [AnnouncementController::class, 'update'])->name('announcements.update');
    Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');
    Route::patch('/announcements/{announcement}/visibility', [AnnouncementController::class, 'toggle'])
        ->name('announcements.toggle');
});

// ==================== COMMUNITY HUB ROUTES ====================
Route::middleware(['auth', 'password.changed'])->group(function () {
    Route::get('/community', [CommunityPostController::class, 'index'])->name('community.index');
    Route::get('/community/create', [CommunityPostController::class, 'create'])->name('community.create');
    // Rate-limit community post creation: 5 per 10 minutes.
    Route::post('/community', [CommunityPostController::class, 'store'])->name('community.store');

    // Rate-limit comments: 15 per minute.
    Route::post('/community/{communityPost}/comment', [CommunityPostController::class, 'comment'])->name('community.comment')->middleware('throttle:15,1');

    Route::get('/community/{communityPost}/edit', [CommunityPostController::class, 'edit'])->name('community.edit');
    Route::patch('/community/{communityPost}', [CommunityPostController::class, 'update'])->name('community.update');
    Route::get('/community/{communityPost}', [CommunityPostController::class, 'show'])->name('community.show');

    Route::patch('/community/comments/{communityComment}', [CommunityPostController::class, 'updateComment'])->name('community.comments.update');
    Route::delete('/community/comments/{communityComment}', [CommunityPostController::class, 'destroyComment'])->name('community.comments.destroy');

    Route::post('/community/{communityPost}/like', [CommunityPostController::class, 'toggleLike'])->name('community.like');
    Route::delete('/community/{communityPost}', [CommunityPostController::class, 'destroy'])->name('community.destroy');
});

// Admin community management
Route::middleware(['auth', 'password.changed', 'role:manager'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/community', [CommunityPostController::class, 'manage'])->name('community');
});

Route::post('/community/{communityPost}/approve', [CommunityPostController::class, 'approve'])->name('community.approve')->middleware(['auth', 'password.changed', 'role:manager']);
Route::post('/community/{communityPost}/reject', [CommunityPostController::class, 'reject'])->name('community.reject')->middleware(['auth', 'password.changed', 'role:manager']);

