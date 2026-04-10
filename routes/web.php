<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CommunityPostController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
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
Route::get('/community/{communityPost}', [CommunityPostController::class, 'show'])->name('community.show');
Route::post('/community/{communityPost}/comment', [CommunityPostController::class, 'comment'])->name('community.comment')->middleware('auth');

// Admin management routes for community

Route::middleware(['auth', 'role:manager'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/community', [CommunityPostController::class, 'manage'])->name('community');
});

Route::post('/community/{communityPost}/approve', [CommunityPostController::class, 'approve'])->name('community.approve')->middleware(['auth', 'role:manager']);
Route::post('/community/{communityPost}/reject', [CommunityPostController::class, 'reject'])->name('community.reject')->middleware(['auth', 'role:manager']);
Route::delete('/community/{communityPost}', [CommunityPostController::class, 'destroy'])->name('community.destroy')->middleware('auth');
