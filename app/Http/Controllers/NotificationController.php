<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Booking;
use App\Models\CommunityPost;
use App\Models\Concern;
use App\Models\MaintenanceTicket;
use App\Models\NotificationRead;
use App\Notifications\BookingStatusChangedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        abort_unless($user, 403);

        $notifications = $this->notificationItems($user, false, 60);
        $unreadCount = $notifications->where('is_read', false)->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    public function poll()
    {
        $user = Auth::user();
        abort_unless($user, 403);

        $notifications = $this->notificationItems($user, false, 10);
        $unreadCount = $notifications->where('is_read', false)->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    public function open(string $type, int $id): RedirectResponse
    {
        $user = Auth::user();

        abort_unless($user, 403);

        [$model, $redirectRoute, $routeParameter] = match ($type) {
            'announcement' => [Announcement::class, 'announcements.show', 'announcement'],
            'concern' => [Concern::class, $user->isManager() ? 'admin.concerns.show' : 'concerns.show', 'concern'],
            'ticket' => [MaintenanceTicket::class, 'tickets.show', 'ticket'],
            'community_post' => [CommunityPost::class, 'community.show', 'communityPost'],
            default => abort(404),
        };

        $record = $model::query()->findOrFail($id);

        // Ownership check: verify the authenticated user is allowed to see this record.
        // Announcements are public to all authenticated users; everything else is scoped.
        if ($type !== 'announcement') {
            $permitted = match ($type) {
                'concern' => $user->isManager() || $record->user_id === $user->id,
                'ticket'  => $user->isManager()
                    || $record->user_id === $user->id
                    || ($user->isHandyman() && $record->assigned_to === $user->id),
                'community_post' => $user->isManager()
                    || $record->user_id === $user->id
                    || $record->status === 'approved',
                default   => false,
            };
            abort_unless($permitted, 403);
        }

        if (Schema::hasTable('notification_reads')) {
            $status = (string) request()->query('status', $this->notificationStatus($type, $record));

            NotificationRead::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'notification_type' => $type,
                    'notification_id' => $record->id,
                    'notification_status' => $status,
                ],
                [
                    'read_at' => now(),
                ]
            );
        }

        return redirect()->route($redirectRoute, [$routeParameter => $record]);
    }

    public function openDatabase(string $notification): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user, 403);

        /** @var DatabaseNotification $record */
        $record = $user->notifications()->findOrFail($notification);
        $booking = Booking::query()
            ->where('user_id', $user->id)
            ->findOrFail($record->data['booking_id'] ?? null);

        $record->markAsRead();

        return redirect()->route('bookings.show', $booking);
    }

    private function notificationItems($user, bool $unreadOnly = false, int $limit = 60)
    {
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
        $filterUnread = fn ($items) => $unreadOnly
            ? $items->filter(fn ($item) => empty($item['is_read']))->values()
            : $items;

        if ($user->isManager()) {
            $ticketNotifications = MaintenanceTicket::query()->where('status', 'pending_approval')
                ->latest()->take($limit)->get()->map(fn ($ticket) => [
                'type' => 'ticket',
                'title' => 'Ticket Review',
                'message' => $ticket->title,
                'time' => $ticket->created_at?->diffForHumans(),
                'sort_key' => $ticket->created_at?->timestamp ?? 0,
                'status' => $ticket->status,
                'url' => route('notifications.open', ['type' => 'ticket', 'id' => $ticket->id, 'status' => $ticket->status]),
                'is_read' => $isRead('ticket', $ticket->id, $ticket->status),
            ]);

            $communityNotifications = CommunityPost::query()->where('status', 'pending')
                ->latest()->take($limit)->get()->map(fn ($post) => [
                'type' => 'community_post',
                'title' => 'Community Review',
                'message' => $post->title,
                'time' => $post->created_at?->diffForHumans(),
                'sort_key' => $post->created_at?->timestamp ?? 0,
                'status' => $post->status,
                'url' => route('notifications.open', ['type' => 'community_post', 'id' => $post->id, 'status' => $post->status]),
                'is_read' => $isRead('community_post', $post->id, $post->status),
            ]);

            $concernNotifications = Concern::query()->whereIn('status', ['submitted', 'reopened'])
                ->latest()->take($limit)->get()->map(fn ($concern) => [
                'type' => 'concern',
                'title' => 'Concern Review',
                'message' => $concern->subject,
                'time' => $concern->created_at?->diffForHumans(),
                'sort_key' => $concern->created_at?->timestamp ?? 0,
                'status' => $concern->status,
                'url' => route('notifications.open', ['type' => 'concern', 'id' => $concern->id, 'status' => $concern->status]),
                'is_read' => $isRead('concern', $concern->id, $concern->status),
            ]);

            return $filterUnread($ticketNotifications)
                ->concat($filterUnread($communityNotifications))
                ->concat($filterUnread($concernNotifications))
                ->sortByDesc(fn ($item) => $item['sort_key'] ?? 0)
                ->take($limit)
                ->map(fn ($item) => collect($item)->except('sort_key')->all())
                ->values();
        }

        if ($user->isHandyman()) {
            return $filterUnread(MaintenanceTicket::query()
                    ->where('assigned_to', $user->id)
                    ->whereIn('status', ['assigned', 'in_progress'])
                ->latest('updated_at')->take($limit)->get()->map(fn ($ticket) => [
                'type' => 'ticket',
                'title' => $ticket->status === 'in_progress' ? 'Work In Progress' : 'Assigned Work',
                'message' => $ticket->title,
                'time' => $ticket->updated_at?->diffForHumans(),
                'sort_key' => $ticket->updated_at?->timestamp ?? 0,
                'status' => $ticket->status,
                'url' => route('notifications.open', ['type' => 'ticket', 'id' => $ticket->id, 'status' => $ticket->status]),
                'is_read' => $isRead('ticket', $ticket->id, $ticket->status),
            ]))->sortByDesc(fn ($item) => $item['sort_key'] ?? 0)
                ->map(fn ($item) => collect($item)->except('sort_key')->all())
                ->values();
        }

        $announcementNotifications = Announcement::visibleToResidents()
            ->latest()->take($limit)->get()->map(fn ($announcement) => [
            'type' => 'announcement',
            'title' => 'Announcement',
            'message' => $announcement->title,
            'time' => $announcement->created_at?->diffForHumans(),
            'sort_key' => $announcement->created_at?->timestamp ?? 0,
            'status' => 'published',
            'url' => route('notifications.open', ['type' => 'announcement', 'id' => $announcement->id, 'status' => 'published']),
            'is_read' => $isRead('announcement', $announcement->id, 'published'),
        ]);

        $concernNotifications = Concern::query()->where('user_id', $user->id)->whereNotNull('admin_reply')
            ->latest('replied_at')->take($limit)->get()->map(fn ($concern) => [
            'type' => 'concern',
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
            ->latest('updated_at')->take($limit)->get()->map(function ($ticket) use ($isRead) {
            $title = match ($ticket->status) {
                'rejected'   => 'Request Rejected',
                'cancelled'  => 'Request Cancelled',
                'resolved'   => 'Request Resolved',
                'closed'     => 'Request Closed',
                'assigned'   => 'Assigned to Staff',
                default      => 'Request Approved',
            };

            return [
                'type' => 'ticket',
                'title' => $title,
                'message' => ($ticket->status === 'rejected' && $ticket->rejection_reason)
                    ? $ticket->title . ': ' . $ticket->rejection_reason
                    : $ticket->title,
                'time' => $ticket->updated_at?->diffForHumans(),
                'sort_key' => $ticket->updated_at?->timestamp ?? 0,
                'status' => $ticket->status,
                'url' => route('notifications.open', ['type' => 'ticket', 'id' => $ticket->id, 'status' => $ticket->status]),
                'is_read' => $isRead('ticket', $ticket->id, $ticket->status),
            ];
        });

        $bookingNotifications = Schema::hasTable('notifications')
            ? $user->notifications()
                ->where('type', BookingStatusChangedNotification::class)
                ->where('data->event', 'cancelled')
                ->latest()
                ->take($limit)
                ->get()
                ->map(fn ($notification) => [
                'type' => 'booking',
                'title' => $notification->data['title'] ?? 'Booking Update',
                'message' => $notification->data['message'] ?? 'Your booking status has been updated.',
                'time' => $notification->created_at?->diffForHumans(),
                'sort_key' => $notification->created_at?->timestamp ?? 0,
                'status' => $notification->data['status'] ?? 'updated',
                'url' => route('notifications.database.open', $notification),
                'is_read' => $notification->read_at !== null,
            ])
            : collect();

        return $filterUnread($announcementNotifications)
            ->concat($filterUnread($concernNotifications))
            ->concat($filterUnread($ticketNotifications))
            ->concat($filterUnread($bookingNotifications))
            ->sortByDesc(fn ($item) => $item['sort_key'] ?? 0)
            ->take($limit)
            ->map(fn ($item) => collect($item)->except('sort_key')->all())
            ->values();
    }

    private function notificationStatus(string $type, $record): string
    {
        return match ($type) {
            'announcement' => 'published',
            'concern', 'ticket', 'community_post' => (string) $record->status,
            default => 'default',
        };
    }
}
