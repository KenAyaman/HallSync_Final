<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\ConcernController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CommunityPostController;
use App\Http\Controllers\NotificationController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'role:handyman'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/overview', [DashboardController::class, 'staffOverview'])->name('overview');
    Route::get('/work-queue', [DashboardController::class, 'staffQueue'])->name('queue');
    Route::get('/completed', [DashboardController::class, 'staffCompleted'])->name('completed');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/notifications/open/{type}/{id}', [NotificationController::class, 'open'])->name('notifications.open');
});

require __DIR__.'/auth.php';

// ==================== TICKET ROUTES ====================
Route::middleware('auth')->group(function () {
    Route::resource('tickets', TicketController::class);
    
    // Ticket status update (for handymen)
    Route::patch('/tickets/{ticket}/status', [TicketController::class, 'updateStatus'])->name('tickets.update-status');
    
    // Admin approval routes
    Route::post('/tickets/{ticket}/approve', [TicketController::class, 'approve'])->name('tickets.approve')->middleware(['auth', 'role:manager']);
    Route::post('/tickets/{ticket}/reject', [TicketController::class, 'reject'])->name('tickets.reject')->middleware(['auth', 'role:manager']);
    Route::post('/tickets/{ticket}/assign', [TicketController::class, 'assign'])->name('tickets.assign')->middleware(['auth', 'role:manager']);
});


Route::middleware('auth')->group(function () {
    Route::get('/bookings/reserved-slots', [BookingController::class, 'getReservedSlots'])->name('bookings.reserved-slots');
    Route::resource('bookings', BookingController::class);
});

Route::middleware(['auth', 'role:manager'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/bookings/calendar', [BookingController::class, 'calendar'])->name('bookings.calendar');
    Route::get('/bookings/{booking}/details', [BookingController::class, 'getBookingDetails'])->name('bookings.details');
});

Route::middleware(['auth', 'role:resident'])->group(function () {
    Route::get('/concerns', [ConcernController::class, 'index'])->name('concerns.index');
    Route::get('/concerns/create', [ConcernController::class, 'create'])->name('concerns.create');
    Route::post('/concerns', [ConcernController::class, 'store'])->name('concerns.store');
    Route::get('/concerns/{concern}', [ConcernController::class, 'show'])->name('concerns.show');
});

Route::middleware(['auth', 'role:manager'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/concerns', [ConcernController::class, 'adminIndex'])->name('concerns.index');
    Route::get('/concerns/{concern}', [ConcernController::class, 'adminShow'])->name('concerns.show');
    Route::patch('/concerns/{concern}', [ConcernController::class, 'adminUpdate'])->name('concerns.update');
});

// ==================== ANNOUNCEMENT ROUTES ====================
Route::middleware('auth')->group(function () {
    Route::resource('announcements', AnnouncementController::class);
    Route::get('/announcements/{announcement}/toggle', [AnnouncementController::class, 'toggle'])
        ->name('announcements.toggle')
        ->middleware(['auth', 'role:manager']);
});

// ==================== COMMUNITY HUB ROUTES ====================

Route::get('/community', [CommunityPostController::class, 'index'])->name('community.index');
Route::get('/community/create', [CommunityPostController::class, 'create'])->name('community.create')->middleware('auth');
Route::post('/community', [CommunityPostController::class, 'store'])->name('community.store')->middleware('auth');
Route::get('/community/{communityPost}/edit', [CommunityPostController::class, 'edit'])->name('community.edit')->middleware('auth');
Route::patch('/community/{communityPost}', [CommunityPostController::class, 'update'])->name('community.update')->middleware('auth');
Route::get('/community/{communityPost}', [CommunityPostController::class, 'show'])->name('community.show');
Route::post('/community/{communityPost}/comment', [CommunityPostController::class, 'comment'])->name('community.comment')->middleware('auth');
Route::patch('/community/comments/{communityComment}', [CommunityPostController::class, 'updateComment'])->name('community.comments.update')->middleware('auth');
Route::delete('/community/comments/{communityComment}', [CommunityPostController::class, 'destroyComment'])->name('community.comments.destroy')->middleware('auth');
Route::post('/community/{communityPost}/like', [CommunityPostController::class, 'toggleLike'])->name('community.like')->middleware('auth');

// Admin management routes for community

Route::middleware(['auth', 'role:manager'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/community', [CommunityPostController::class, 'manage'])->name('community');
});

Route::post('/community/{communityPost}/approve', [CommunityPostController::class, 'approve'])->name('community.approve')->middleware(['auth', 'role:manager']);
Route::post('/community/{communityPost}/reject', [CommunityPostController::class, 'reject'])->name('community.reject')->middleware(['auth', 'role:manager']);
Route::delete('/community/{communityPost}', [CommunityPostController::class, 'destroy'])->name('community.destroy')->middleware('auth');
