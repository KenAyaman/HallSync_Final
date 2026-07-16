<?php

namespace App\Http\Controllers;

use App\Models\CommunityPost;
use App\Models\MaintenanceTicket;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class MediaController extends Controller
{
    public function ticket(MaintenanceTicket $ticket, string $type): Response
    {
        $this->authorize('view', $ticket);

        abort_unless(in_array($type, ['image', 'video'], true), 404);

        $path = $type === 'image' ? $ticket->image_path : $ticket->video_path;

        return $this->secureFileResponse($path);
    }

    public function community(CommunityPost $communityPost, string $type): Response
    {
        $user = Auth::user();

        abort_unless($user, 403);
        abort_unless(in_array($type, ['image', 'video'], true), 404);
        abort_unless(
            $user->isManager()
                || $communityPost->status === 'approved'
                || $communityPost->user_id === $user->id,
            403
        );

        $path = $type === 'image' ? $communityPost->image_path : $communityPost->video_path;

        return $this->secureFileResponse($path);
    }

    public function profile(User $user): Response
    {
        abort_unless(Auth::check(), 403);

        return $this->secureFileResponse($user->profile_photo_path);
    }

    private function secureFileResponse(?string $path): Response
    {
        abort_unless($path, 404);

        $disk = Storage::disk('local')->exists($path) ? 'local' : 'public';

        abort_unless(Storage::disk($disk)->exists($path), 404);

        return Storage::disk($disk)->response($path, null, [
            'Cache-Control' => 'private, max-age=300',
            'Content-Security-Policy' => "default-src 'none'; sandbox",
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
