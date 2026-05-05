<?php

namespace App\Providers;

use App\Models\Announcement;
use App\Models\CommunityPost;
use App\Models\Concern;
use App\Models\MaintenanceTicket;
use App\Models\NotificationRead;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.navigation', function ($view) {
            $notifications = collect();

            if (! Auth::check()) {
                $view->with([
                    'navNotifications' => $notifications,
                    'navNotificationCount' => 0,
                ]);

                return;
            }

            $user = Auth::user();
            $readsByType = collect();

            if (Schema::hasTable('notification_reads')) {
                $readsByType = NotificationRead::query()
                    ->where('user_id', $user->id)
                    ->get()
                    ->groupBy('notification_type')
                    ->map(fn ($reads) => $reads->pluck('notification_id')->all());
            }

            if ($user->role === 'resident') {
                $readAnnouncementIds = $readsByType->get('announcement', []);
                $readConcernIds = $readsByType->get('concern', []);

                $announcementNotificationQuery = Announcement::query()
                    ->where('is_active', true)
                    ->when($readAnnouncementIds, fn ($query) => $query->whereNotIn('id', $readAnnouncementIds));

                $concernNotificationQuery = Concern::query()
                    ->where('user_id', $user->id)
                    ->whereNotNull('admin_reply')
                    ->when($readConcernIds, fn ($query) => $query->whereNotIn('id', $readConcernIds));

                $navNotificationCount = (clone $announcementNotificationQuery)->count()
                    + (clone $concernNotificationQuery)->count();

                $announcementNotifications = $announcementNotificationQuery
                    ->latest()
                    ->take(3)
                    ->get()
                    ->map(fn ($announcement) => [
                        'title' => 'Announcement',
                        'message' => $announcement->title,
                        'time' => $announcement->created_at?->diffForHumans(),
                        'sort_key' => $announcement->created_at?->timestamp ?? 0,
                        'url' => route('notifications.open', ['type' => 'announcement', 'id' => $announcement->id]),
                    ]);

                $concernNotifications = $concernNotificationQuery
                    ->latest('replied_at')
                    ->take(2)
                    ->get()
                    ->map(fn ($concern) => [
                        'title' => 'Concern Reply',
                        'message' => $concern->subject,
                        'time' => optional($concern->replied_at)->diffForHumans(),
                        'sort_key' => optional($concern->replied_at)->timestamp ?? 0,
                        'url' => route('notifications.open', ['type' => 'concern', 'id' => $concern->id]),
                    ]);

                $notifications = $announcementNotifications
                    ->concat($concernNotifications)
                    ->sortByDesc(fn ($item) => $item['sort_key'] ?? 0)
                    ->take(5)
                    ->map(fn ($item) => collect($item)->except('sort_key')->all())
                    ->values();
            } elseif ($user->role === 'manager') {
                $readTicketIds = $readsByType->get('ticket', []);
                $readCommunityPostIds = $readsByType->get('community_post', []);

                $ticketNotificationQuery = MaintenanceTicket::query()
                    ->where('status', 'pending_approval')
                    ->when($readTicketIds, fn ($query) => $query->whereNotIn('id', $readTicketIds));

                $communityNotificationQuery = CommunityPost::query()
                    ->where('status', 'pending')
                    ->when($readCommunityPostIds, fn ($query) => $query->whereNotIn('id', $readCommunityPostIds));

                $navNotificationCount = (clone $ticketNotificationQuery)->count()
                    + (clone $communityNotificationQuery)->count();

                $ticketNotifications = $ticketNotificationQuery
                    ->latest()
                    ->take(3)
                    ->get()
                    ->map(fn ($ticket) => [
                        'title' => 'Ticket Review',
                        'message' => $ticket->title,
                        'time' => $ticket->created_at?->diffForHumans(),
                        'sort_key' => $ticket->created_at?->timestamp ?? 0,
                        'url' => route('notifications.open', ['type' => 'ticket', 'id' => $ticket->id]),
                    ]);

                $communityNotifications = $communityNotificationQuery
                    ->latest()
                    ->take(2)
                    ->get()
                    ->map(fn ($post) => [
                        'title' => 'Community Review',
                        'message' => $post->title,
                        'time' => $post->created_at?->diffForHumans(),
                        'sort_key' => $post->created_at?->timestamp ?? 0,
                        'url' => route('notifications.open', ['type' => 'community_post', 'id' => $post->id]),
                    ]);

                $notifications = $ticketNotifications
                    ->concat($communityNotifications)
                    ->sortByDesc(fn ($item) => $item['sort_key'] ?? 0)
                    ->map(fn ($item) => collect($item)->except('sort_key')->all())
                    ->values();
            } else {
                $readTicketIds = $readsByType->get('ticket', []);

                $ticketNotificationQuery = MaintenanceTicket::query()
                    ->where('assigned_to', $user->id)
                    ->whereIn('status', ['assigned', 'in_progress'])
                    ->when($readTicketIds, fn ($query) => $query->whereNotIn('id', $readTicketIds));

                $navNotificationCount = (clone $ticketNotificationQuery)->count();

                $notifications = $ticketNotificationQuery
                    ->latest('updated_at')
                    ->take(5)
                    ->get()
                    ->map(fn ($ticket) => [
                        'title' => 'Assigned Task',
                        'message' => $ticket->title,
                        'time' => $ticket->updated_at?->diffForHumans(),
                        'sort_key' => $ticket->updated_at?->timestamp ?? 0,
                        'url' => route('notifications.open', ['type' => 'ticket', 'id' => $ticket->id]),
                    ])
                    ->sortByDesc(fn ($item) => $item['sort_key'] ?? 0)
                    ->map(fn ($item) => collect($item)->except('sort_key')->all())
                    ->values();
            }

            $view->with([
                'navNotifications' => $notifications,
                'navNotificationCount' => $navNotificationCount ?? $notifications->count(),
            ]);
        });
    }
}
