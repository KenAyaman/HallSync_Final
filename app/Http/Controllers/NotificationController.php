<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\CommunityPost;
use App\Models\Concern;
use App\Models\MaintenanceTicket;
use App\Models\NotificationRead;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class NotificationController extends Controller
{
    public function open(string $type, int $id): RedirectResponse
    {
        $user = Auth::user();

        abort_unless($user, 403);

        [$model, $redirectRoute, $routeParameter] = match ($type) {
            'announcement' => [Announcement::class, 'announcements.show', 'announcement'],
            'concern' => [Concern::class, 'concerns.show', 'concern'],
            'ticket' => [MaintenanceTicket::class, 'tickets.show', 'ticket'],
            'community_post' => [CommunityPost::class, 'community.show', 'communityPost'],
            default => abort(404),
        };

        $record = $model::query()->findOrFail($id);

        if (Schema::hasTable('notification_reads')) {
            NotificationRead::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'notification_type' => $type,
                    'notification_id' => $record->id,
                ],
                [
                    'read_at' => now(),
                ]
            );
        }

        return redirect()->route($redirectRoute, [$routeParameter => $record]);
    }
}
