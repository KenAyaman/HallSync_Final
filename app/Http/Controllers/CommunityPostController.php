<?php

namespace App\Http\Controllers;

use App\Models\CommunityPost;
use App\Models\CommunityComment;
use App\Models\CommunityPostLike;
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

            return view('admin.community.index', compact('pendingPosts', 'approvedPosts', 'rejectedPosts'));
        }

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

        if (!$user) {
            abort(403);
        }

        if ($user->role === 'manager') {
            return view('admin.community.create');
        }

        return view('resident.community.create');
    }

    // Store new post
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:lost_found,buy_sell,event,discussion,other',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'video' => 'nullable|file|mimes:mp4,mov,avi|max:10240',
        ]);

        $post = new CommunityPost();
        $post->user_id = Auth::id();
        $post->title = $request->title;
        $post->content = $request->content;
        $post->type = $request->type;
        $post->status = 'pending';

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('community/images', 'public');
            $post->image_path = $path;
        }

        if ($request->hasFile('video')) {
            $path = $request->file('video')->store('community/videos', 'public');
            $post->video_path = $path;
        }

        $post->save();

        return redirect()->route('community.index')
            ->with('success', 'Your post has been submitted for review. It will appear once approved.');
    }

    // Show single post
    public function show(CommunityPost $communityPost)
    {
        $user = Auth::user();

        $canViewOwnPost = $user && $communityPost->user_id === $user->id;

        if ($communityPost->status !== 'approved' && (!$user || ($user->role !== 'manager' && ! $canViewOwnPost))) {
            abort(403);
        }

        $post = $communityPost;
        $post->load(['user', 'comments.user'])
            ->loadCount(['comments', 'likes']);

        if ($user) {
            $post->liked_by_user = $post->likes()->where('user_id', $user->id)->exists();
        }

        if ($user && $user->role === 'manager') {
            return view('admin.community.show', compact('post'));
        }

        return view('resident.community.show', compact('post'));
    }

    // Admin moderation page
    public function manage()
    {
        if (Auth::user()->role !== 'manager') {
            abort(403);
        }

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
        if (Auth::user()->role !== 'manager') {
            abort(403);
        }

        $communityPost->status = 'approved';
        $communityPost->approved_at = now();
        $communityPost->approved_by = Auth::id();
        $communityPost->save();

        return redirect()->route('admin.community')
            ->with('success', 'Post approved and published!');
    }

    // Admin reject post
    public function reject(Request $request, CommunityPost $communityPost)
    {
        if (Auth::user()->role !== 'manager') {
            abort(403);
        }

        $request->validate([
            'rejection_reason' => 'nullable|string|max:500'
        ]);

        $communityPost->status = 'rejected';
        $communityPost->rejection_reason = $request->rejection_reason;
        $communityPost->save();

        return redirect()->route('admin.community')
            ->with('success', 'Post rejected.');
    }

    // Add comment
    public function comment(Request $request, CommunityPost $communityPost)
    {
        $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        $comment = new CommunityComment();
        $comment->community_post_id = $communityPost->id;
        $comment->user_id = Auth::id();
        $comment->content = $request->content;
        $comment->save();

        return redirect()->route('community.show', $communityPost)
            ->with('success', 'Comment added!');
    }

    public function toggleLike(Request $request, CommunityPost $communityPost)
    {
        $user = Auth::user();

        if (! $user) {
            abort(403);
        }

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
        if ($communityPost->user_id !== Auth::id()) {
            abort(403);
        }

        return view('resident.community.edit', compact('communityPost'));
    }

    // Update own pending post
    public function update(Request $request, CommunityPost $communityPost)
    {
        if ($communityPost->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:lost_found,buy_sell,event,discussion,other',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'video' => 'nullable|file|mimes:mp4,mov,avi|max:10240',
        ]);

        $communityPost->fill($request->only(['title', 'content', 'type']));

        if ($request->hasFile('image')) {
            if ($communityPost->image_path) {
                Storage::disk('public')->delete($communityPost->image_path);
            }

            if ($communityPost->video_path) {
                Storage::disk('public')->delete($communityPost->video_path);
                $communityPost->video_path = null;
            }

            $communityPost->image_path = $request->file('image')->store('community/images', 'public');
        }

        if ($request->hasFile('video')) {
            if ($communityPost->video_path) {
                Storage::disk('public')->delete($communityPost->video_path);
            }

            if ($communityPost->image_path) {
                Storage::disk('public')->delete($communityPost->image_path);
                $communityPost->image_path = null;
            }

            $communityPost->video_path = $request->file('video')->store('community/videos', 'public');
        }

        if (in_array($communityPost->status, ['approved', 'rejected'], true)) {
            $communityPost->status = 'pending';
            $communityPost->approved_at = null;
            $communityPost->approved_by = null;
            $communityPost->rejection_reason = null;
        }

        $communityPost->save();

        return redirect()->route('community.show', $communityPost)
            ->with('success', 'Post updated successfully. Edited posts are sent back for review before they become visible on the board again.');
    }

    // Delete post
    public function destroy(CommunityPost $communityPost)
    {
        $user = Auth::user();

        if ($communityPost->user_id !== Auth::id() && (!$user || $user->role !== 'manager')) {
            abort(403);
        }

        if ($communityPost->image_path) {
            Storage::disk('public')->delete($communityPost->image_path);
        }

        if ($communityPost->video_path) {
            Storage::disk('public')->delete($communityPost->video_path);
        }

        $communityPost->delete();

        return redirect()->route($user && $user->role === 'manager' ? 'admin.community' : 'community.index')
            ->with('success', 'Post deleted.');
    }
}
