<?php

namespace App\Http\Controllers;

use App\Models\CommunityPost;
use App\Models\CommunityComment;
use App\Models\CommunityPostLike;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CommunityPostController extends Controller
{
    // Display community posts
    public function index()
    {
        $user = Auth::user();
        $ownReviewPosts = collect();

        // Manager/admin sees moderation dashboard
        if ($user && $user->role === 'manager') {
            return redirect()->route('admin.community');
        }

        abort_unless($user?->isResident(), 403);

        // Public / resident community feed
        $posts = CommunityPost::query()
            ->with(['user'])
            ->withCount(['comments', 'likes'])
            ->when($user, function ($query) use ($user) {
                $query->withExists([
                    'likes as liked_by_user' => fn ($likeQuery) => $likeQuery->where('user_id', $user->id),
                ]);
            })
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        if ($user) {
            $ownReviewPosts = CommunityPost::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'rejected'])
                ->with(['user'])
                ->withCount(['comments', 'likes'])
                ->orderBy('updated_at', 'desc')
                ->take(4)
                ->get();
        }

        return view('resident.community.index', compact('posts', 'ownReviewPosts'));
    }

    // Show form to create new post
    public function create()
    {
        $user = Auth::user();

        abort_unless($user?->isResident(), 403);

        return view('resident.community.create');
    }

    // Store new post
    public function store(Request $request)
    {
        abort_unless(Auth::user()?->isResident(), 403);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:500',
            'type' => 'required|in:lost_found,buy_sell,event,discussion,other',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|mimetypes:image/jpeg,image/png,image/webp|max:2048',
            'video' => 'nullable|file|mimes:mp4,mov|mimetypes:video/mp4,video/quicktime|max:10240',
        ]);

        $post = new CommunityPost();
        $post->user_id = Auth::id();
        $post->title = $request->title;
        $post->content = $request->content;
        $post->type = $request->type;
        $post->status = 'pending';

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('community/images', 'local');
            $post->image_path = $path;
        }

        if ($request->hasFile('video')) {
            $path = $request->file('video')->store('community/videos', 'local');
            $post->video_path = $path;
        }

        $post->save();
        UserActivityLog::record('community_post.created', 'Submitted a community post for moderation.', Auth::user(), Auth::user(), ['community_post_id' => $post->id]);

        return redirect()->route('community.index')
            ->with('success', 'Your post has been submitted for review. It will appear once approved.');
    }

    // Show single post
    public function show(CommunityPost $communityPost)
    {
        $user = Auth::user();

        abort_unless($user?->isResident() || $user?->isManager(), 403);

        $canViewOwnPost = $user && $communityPost->user_id === $user->id;

        if ($communityPost->status !== 'approved' && (!$user || ($user->role !== 'manager' && ! $canViewOwnPost))) {
            abort(403);
        }

        $post = $communityPost;
        $post->load(['user'])->loadCount(['comments', 'likes']);

        // Paginate comments to avoid loading hundreds on a single page.
        $comments = $communityPost->comments()
            ->with('user')
            ->latest()
            ->paginate(15);

        if ($user) {
            $post->liked_by_user = $post->likes()->where('user_id', $user->id)->exists();
        }

        if ($user && $user->role === 'manager') {
            return view('admin.community.show', compact('post', 'comments'));
        }

        return view('resident.community.show', compact('post', 'comments'));
    }

    // Admin moderation page
    public function manage()
    {
        $this->authorize('moderate', CommunityPost::class);

        $pendingPosts = CommunityPost::where('status', 'pending')
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->paginate(20);

        $approvedPosts = CommunityPost::where('status', 'approved')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        $rejectedPosts = CommunityPost::where('status', 'rejected')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        return view('admin.community.manage', compact('pendingPosts', 'approvedPosts', 'rejectedPosts'));
    }

    // Admin approve post
    public function approve(CommunityPost $communityPost)
    {
        $this->authorize('moderate', CommunityPost::class);

        $communityPost->status = 'approved';
        $communityPost->approved_at = now();
        $communityPost->approved_by = Auth::id();
        $communityPost->save();
        UserActivityLog::record('community_post.approved', 'Approved a community post.', $communityPost->user, Auth::user(), ['community_post_id' => $communityPost->id]);

        return redirect()->route('admin.community')
            ->with('success', 'Post approved and published!');
    }

    // Admin reject post
    public function reject(Request $request, CommunityPost $communityPost)
    {
        $this->authorize('moderate', CommunityPost::class);

        $request->validate([
            'rejection_reason' => 'nullable|string|max:500'
        ]);

        $communityPost->status = 'rejected';
        $communityPost->rejection_reason = $request->rejection_reason;
        $communityPost->save();
        UserActivityLog::record('community_post.rejected', 'Rejected a community post.', $communityPost->user, Auth::user(), ['community_post_id' => $communityPost->id, 'reason' => $communityPost->rejection_reason]);

        return redirect()->route('admin.community')
            ->with('error', 'Post rejected.');
    }

    // Add comment — residents can comment on their own or approved posts; managers can comment on any (H-10).
    public function comment(Request $request, CommunityPost $communityPost)
    {
        $user = Auth::user();
        abort_unless($user?->isResident() || $user?->isManager(), 403);

        $canComment = $communityPost->status === 'approved'
            || $communityPost->user_id === $user->id
            || $user->isManager();
        abort_unless($canComment, 403);

        $request->validate([
            'content' => 'required|string|max:300'
        ]);

        $comment = new CommunityComment();
        $comment->community_post_id = $communityPost->id;
        $comment->user_id = Auth::id();
        $comment->content = $request->content;
        $comment->save();

        return redirect()->route('community.show', $communityPost)
            ->with('success', 'Comment added!');
    }

    public function updateComment(Request $request, CommunityComment $communityComment)
    {
        $user = Auth::user();

        if (! $user || (! $user->isResident() && ! $user->isManager()) || ($communityComment->user_id !== $user->id && $user->role !== 'manager')) {
            abort(403);
        }

        $request->validate([
            'content' => 'required|string|max:300',
        ]);

        $communityComment->update([
            'content' => $request->content,
        ]);

        // Pass the post ID directly — route resolves it; avoids loading the full model (M-08).
        return redirect()
            ->route('community.show', ['communityPost' => $communityComment->community_post_id])
            ->with('success', 'Comment updated successfully.');
    }

    public function destroyComment(CommunityComment $communityComment)
    {
        $user = Auth::user();

        if (! $user || (! $user->isResident() && ! $user->isManager()) || ($communityComment->user_id !== $user->id && $user->role !== 'manager')) {
            abort(403);
        }

        $postId = $communityComment->community_post_id;
        $communityComment->delete();

        return redirect()
            ->route('community.show', $postId)
            ->with('success', 'Comment deleted successfully.');
    }

    public function toggleLike(Request $request, CommunityPost $communityPost)
    {
        $user = Auth::user();

        abort_unless($user?->isResident(), 403);

        if ($communityPost->status !== 'approved' && $communityPost->user_id !== $user->id && $user->role !== 'manager') {
            abort(403);
        }

        $existingLike = CommunityPostLike::where('community_post_id', $communityPost->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingLike) {
            $existingLike->delete();
            $liked = false;
            $message = 'Post unliked.';
        } else {
            CommunityPostLike::create([
                'community_post_id' => $communityPost->id,
                'user_id' => $user->id,
            ]);
            $liked = true;
            $message = 'Post liked.';
        }

        $likesCount = $communityPost->likes()->count();

        if ($request->expectsJson()) {
            return response()->json([
                'liked' => $liked,
                'likes_count' => $likesCount,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }

    // Edit own pending post
    public function edit(CommunityPost $communityPost)
    {
        $this->authorize('update', $communityPost);

        if ($communityPost->status === 'pending') {
            return redirect()
                ->route('community.index')
                ->with('error', 'This post is already awaiting review. You can revise it if administration returns it with feedback.');
        }

        return view('resident.community.edit', compact('communityPost'));
    }

    // Update own pending post
    public function update(Request $request, CommunityPost $communityPost)
    {
        $this->authorize('update', $communityPost);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:lost_found,buy_sell,event,discussion,other',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|mimetypes:image/jpeg,image/png,image/webp|max:2048',
            'video' => 'nullable|file|mimes:mp4,mov|mimetypes:video/mp4,video/quicktime|max:10240',
        ]);

        $communityPost->fill($request->only(['title', 'content', 'type']));

        if ($request->hasFile('image')) {
            if ($communityPost->image_path) {
                $this->deleteStoredMedia($communityPost->image_path);
            }

            if ($communityPost->video_path) {
                $this->deleteStoredMedia($communityPost->video_path);
                $communityPost->video_path = null;
            }

            $communityPost->image_path = $request->file('image')->store('community/images', 'local');
        }

        if ($request->hasFile('video')) {
            if ($communityPost->video_path) {
                $this->deleteStoredMedia($communityPost->video_path);
            }

            if ($communityPost->image_path) {
                $this->deleteStoredMedia($communityPost->image_path);
                $communityPost->image_path = null;
            }

            $communityPost->video_path = $request->file('video')->store('community/videos', 'local');
        }

        if (in_array($communityPost->status, ['approved', 'rejected'], true)) {
            $communityPost->status = 'pending';
            $communityPost->approved_at = null;
            $communityPost->approved_by = null;
            $communityPost->rejection_reason = null;
        }

        $communityPost->save();
        UserActivityLog::record('community_post.updated', 'Updated a community post and returned it to moderation.', Auth::user(), Auth::user(), ['community_post_id' => $communityPost->id]);

        return redirect()->route('community.show', $communityPost)
            ->with('success', 'Post updated successfully. Edited posts are sent back for review before they become visible on the board again.');
    }

    // Delete post
    public function destroy(CommunityPost $communityPost)
    {
        $user = Auth::user();

        $this->authorize('delete', $communityPost);

        if ($communityPost->image_path) {
            $this->deleteStoredMedia($communityPost->image_path);
        }

        if ($communityPost->video_path) {
            $this->deleteStoredMedia($communityPost->video_path);
        }

        $communityPost->delete();
        // If admin deletes: subject = post owner; if resident self-deletes: subject = actor = user.
        $subject = $user->isManager() ? $communityPost->user : $user;
        UserActivityLog::record('community_post.deleted', 'Deleted a community post.', $subject, $user, ['community_post_id' => $communityPost->id]);

        $redirectRoute = $user && $user->role === 'manager' ? 'admin.community' : 'community.index';
        $flashType = $user && $user->role === 'manager' ? 'success' : 'warning';

        return redirect()->route($redirectRoute)
            ->with($flashType, 'Post deleted.');
    }

    private function deleteStoredMedia(string $path): void
    {
        Storage::disk('local')->delete($path);
        Storage::disk('public')->delete($path);
    }
}
