<?php

namespace App\Providers;

use App\Models\Announcement;
use App\Models\CommunityPost;
use App\Models\Concern;
use App\Models\MaintenanceTicket;
use App\Models\NotificationRead;
use App\Notifications\BookingStatusChangedNotification;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
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
        // Force all generated URLs (route(), url(), asset(), form actions, etc.)
        // to use https in every environment except local development. This is a
        // safety net on top of trustProxies() in bootstrap/app.php: even if the
        // proxy header detection has an edge case, every link and form action
        // Laravel renders will still be https, so the browser never flags the
        // page or its forms as insecure.
        if (! $this->app->environment('local')) {
            URL::forceScheme('https');
        }

        View::composer(['layouts.navigation', 'layouts.admin-nav', 'layouts.handyman-nav', 'dashboard.resident'], function ($view) {
            $notifications = collect();

            if (! Auth::check()) {
                $view->with([
                    'navNotifications' => $notifications,
                    'navNotificationCount' => 0,
                ]);

                return;
            }

            $user = Auth::user();
            $reads = collect();

            if (Schema::hasTable('notification_reads')) {
                $reads = NotificationRead::query()
                    ->where('user_id', $user->id)
                    ->get();
            }

            $isRead = fn (string $type, int $id, string $status) => $reads->contains(
                fn ($read) => $read->notification_type === $type
                    && (int) $read->notification_id === $id
                    && (string) $read->notification_status === $status
            );
            $unread = fn ($items) => $items->filter(fn ($item) => empty($item['is_read']))->values();

            if ($user->role === 'resident') {
                $announcementNotifications = Announcement::visibleToResidents()
                    ->latest()
                    ->get()
                    ->map(fn ($announcement) => [
                        'title' => 'Announcement',
                        'message' => $announcement->title,
                        'time' => $announcement->created_at?->diffForHumans(),
                        'sort_key' => $announcement->created_at?->timestamp ?? 0,
                        'status' => 'published',
                        'url' => route('notifications.open', ['type' => 'announcement', 'id' => $announcement->id, 'status' => 'published']),
                        'is_read' => $isRead('announcement', $announcement->id, 'published'),
                    ]);

                $concernNotifications = Concern::query()
                    ->where('user_id', $user->id)
                    ->whereNotNull('admin_reply')
                    ->latest('replied_at')
                    ->get()
                    ->map(fn ($concern) => [
                        'title' => 'Concern Reply',
                        'message' => $concern->subject,
                        'time' => optional($concern->replied_at)->diffForHumans(),
                        'sort_key' => optional($concern->replied_at)->timestamp ?? 0,
                        'status' => $concern->status,
                        'url' => route('notifications.open', ['type' => 'concern', 'id' => $concern->id, 'status' => $concern->status]),
                        'is_read' => $isRead('concern', $concern->id, $concern->status),
                    ]);

                $ticketNotifications = MaintenanceTicket::query()
                    ->where('user_id', $user->id)
                    ->whereIn('status', ['approved', 'assigned', 'resolved', 'closed', 'cancelled', 'rejected'])
                    ->latest('updated_at')
                    ->get()
                    ->map(function ($ticket) use ($isRead) {
                        $title = match ($ticket->status) {
                            'rejected'   => 'Request Rejected',
                            'cancelled'  => 'Request Cancelled',
                            'resolved'   => 'Request Resolved',
                            'closed'     => 'Request Closed',
                            'assigned'   => 'Assigned to Staff',
                            default      => 'Request Approved',
                        };
                        $message = ($ticket->status === 'rejected' && $ticket->rejection_reason)
                            ? $ticket->title . ': ' . $ticket->rejection_reason
                            : $ticket->title;

                        return [
                            'title'    => $title,
                            'message'  => $message,
                            'time'     => $ticket->updated_at?->diffForHumans(),
                            'sort_key' => $ticket->updated_at?->timestamp ?? 0,
                            'status'   => $ticket->status,
                            'url'      => route('notifications.open', ['type' => 'ticket', 'id' => $ticket->id, 'status' => $ticket->status]),
                            'is_read'  => $isRead('ticket', $ticket->id, $ticket->status),
                        ];
                    });

                $bookingNotifications = Schema::hasTable('notifications')
                    ? $user->unreadNotifications()
                        ->where('type', BookingStatusChangedNotification::class)
                        ->where('data->event', 'cancelled')
                        ->latest()
                        ->get()
                        ->map(fn ($notification) => [
                        'title' => $notification->data['title'] ?? 'Booking Update',
                        'message' => $notification->data['message'] ?? 'Your booking status has been updated.',
                        'time' => $notification->created_at?->diffForHumans(),
                        'sort_key' => $notification->created_at?->timestamp ?? 0,
                        'status' => $notification->data['status'] ?? 'updated',
                        'url' => route('notifications.database.open', $notification),
                        'is_read' => false,
                    ])
                    : collect();

                $unreadNotifications = $unread($announcementNotifications)
                    ->concat($unread($concernNotifications))
                    ->concat($unread($ticketNotifications))
                    ->concat($bookingNotifications);
                $navNotificationCount = $unreadNotifications->count();

                $notifications = $unreadNotifications
                    ->sortByDesc(fn ($item) => $item['sort_key'] ?? 0)
                    ->take(3)
                    ->map(fn ($item) => collect($item)->except('sort_key')->all())
                    ->values();
            } elseif ($user->role === 'manager') {
                $ticketNotifications = MaintenanceTicket::query()
                    ->where('status', 'pending_approval')
                    ->latest()
                    ->get()
                    ->map(fn ($ticket) => [
                        'title' => 'Ticket Review',
                        'message' => $ticket->title,
                        'time' => $ticket->created_at?->diffForHumans(),
                        'sort_key' => $ticket->created_at?->timestamp ?? 0,
                        'status' => $ticket->status,
                        'url' => route('notifications.open', ['type' => 'ticket', 'id' => $ticket->id, 'status' => $ticket->status]),
                        'is_read' => $isRead('ticket', $ticket->id, $ticket->status),
                    ]);

                $communityNotifications = CommunityPost::query()
                    ->where('status', 'pending')
                    ->latest()
                    ->get()
                    ->map(fn ($post) => [
                        'title' => 'Community Review',
                        'message' => $post->title,
                        'time' => $post->created_at?->diffForHumans(),
                        'sort_key' => $post->created_at?->timestamp ?? 0,
                        'status' => $post->status,
                        'url' => route('notifications.open', ['type' => 'community_post', 'id' => $post->id, 'status' => $post->status]),
                        'is_read' => $isRead('community_post', $post->id, $post->status),
                    ]);

                $concernNotifications = Concern::query()
                    ->whereIn('status', ['submitted', 'reopened'])
                    ->latest()
                    ->get()
                    ->map(fn ($concern) => [
                        'title' => 'Concern Review',
                        'message' => $concern->subject,
                        'time' => $concern->created_at?->diffForHumans(),
                        'sort_key' => $concern->created_at?->timestamp ?? 0,
                        'status' => $concern->status,
                        'url' => route('notifications.open', ['type' => 'concern', 'id' => $concern->id, 'status' => $concern->status]),
                        'is_read' => $isRead('concern', $concern->id, $concern->status),
                    ]);

                $unreadNotifications = $unread($ticketNotifications)
                    ->concat($unread($communityNotifications))
                    ->concat($unread($concernNotifications));
                $navNotificationCount = $unreadNotifications->count();

                $notifications = $unreadNotifications
                    ->sortByDesc(fn ($item) => $item['sort_key'] ?? 0)
                    ->take(3)
                    ->map(fn ($item) => collect($item)->except('sort_key')->all())
                    ->values();
            } else {
                $ticketNotifications = MaintenanceTicket::query()
                    ->where('assigned_to', $user->id)
                    ->whereIn('status', ['assigned', 'in_progress'])
                    ->latest('updated_at')
                    ->get()
                    ->map(fn ($ticket) => [
                        'title' => 'Assigned Task',
                        'message' => $ticket->title,
                        'time' => $ticket->updated_at?->diffForHumans(),
                        'sort_key' => $ticket->updated_at?->timestamp ?? 0,
                        'status' => $ticket->status,
                        'url' => route('notifications.open', ['type' => 'ticket', 'id' => $ticket->id, 'status' => $ticket->status]),
                        'is_read' => $isRead('ticket', $ticket->id, $ticket->status),
                    ]);
                $unreadNotifications = $unread($ticketNotifications);
                $navNotificationCount = $unreadNotifications->count();

                $notifications = $unreadNotifications
                    ->sortByDesc(fn ($item) => $item['sort_key'] ?? 0)
                    ->take(3)
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