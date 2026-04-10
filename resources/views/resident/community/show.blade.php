<x-app-layout>
<div style="max-width: 800px; margin: 0 auto;">

    <div style="margin-bottom: 24px;">
        <a href="{{ route('community.index') }}" style="color: #7B746B; text-decoration: none;">← Back to Community</a>
    </div>

    {{-- Post --}}
    <div style="background: white; border-radius: 24px; padding: 28px; margin-bottom: 24px; border: 1px solid #F0F0F0;">
        
        {{-- User Info --}}
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
            <div style="width: 48px; height: 48px; background: #F5F0E8; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; color: #C79745;">
                {{ strtoupper(substr($post->user->name, 0, 1)) }}
            </div>
            <div>
                <div style="font-weight: 600; color: #2F2A27;">{{ $post->user->name }}</div>
                <div style="font-size: 12px; color: #B39A78;">{{ $post->created_at->format('M d, Y') }}</div>
            </div>
            <div style="margin-left: auto;">
                <span style="background: #FEF8F0; color: #C79745; padding: 4px 12px; border-radius: 20px; font-size: 11px;">
                    {{ str_replace('_', ' ', ucfirst($post->type)) }}
                </span>
            </div>
        </div>

        {{-- Title & Content --}}
        <h1 style="font-size: 26px; font-weight: 600; color: #2F2A27; margin-bottom: 16px;">
            {{ $post->title }}
        </h1>
        <p style="color: #5C5348; line-height: 1.7; font-size: 16px; margin-bottom: 20px;">
            {{ $post->content }}
        </p>

        {{-- Image --}}
        @if($post->image_path)
            <img src="{{ Storage::url($post->image_path) }}" 
                 style="max-width: 100%; border-radius: 16px; margin-bottom: 20px;">
        @endif

        {{-- Video --}}
        @if($post->video_path)
            <video controls style="max-width: 100%; border-radius: 16px; margin-bottom: 20px;">
                <source src="{{ Storage::url($post->video_path) }}">
            </video>
        @endif

        {{-- Rejection Reason (if rejected) --}}
        @if($post->status === 'rejected' && $post->rejection_reason)
            <div style="background: #FEF5E8; padding: 12px; border-radius: 12px; margin-top: 16px;">
                <p style="color: #C79745; font-size: 12px; margin: 0;">❌ Rejected: {{ $post->rejection_reason }}</p>
            </div>
        @endif
    </div>

    {{-- Comments Section --}}
    <div style="background: white; border-radius: 24px; padding: 28px; border: 1px solid #F0F0F0;">
        <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 20px;">Comments ({{ $post->comments->count() }})</h3>

        {{-- Comment Form --}}
        @auth
        <form method="POST" action="{{ route('community.comment', $post) }}" style="margin-bottom: 28px;">
            @csrf
            <textarea name="content" rows="3" 
                      style="width: 100%; padding: 12px; border: 1px solid #E5E0D8; border-radius: 16px; margin-bottom: 12px;"
                      placeholder="Write a comment..."></textarea>
            <button type="submit" 
                    style="background: #C79745; color: white; padding: 10px 20px; border-radius: 30px; border: none; cursor: pointer;">
                Post Comment
            </button>
        </form>
        @endauth

        {{-- Comments List --}}
        @forelse($post->comments as $comment)
            <div style="padding: 16px 0; border-bottom: 1px solid #F5F0E8;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                    <div style="width: 32px; height: 32px; background: #F5F0E8; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600;">
                        {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                    </div>
                    <div>
                        <span style="font-weight: 600; color: #2F2A27;">{{ $comment->user->name }}</span>
                        <span style="font-size: 11px; color: #B39A78; margin-left: 8px;">{{ $comment->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                <p style="color: #5C5348; margin-left: 42px;">{{ $comment->content }}</p>
            </div>
        @empty
            <p style="text-align: center; color: #B39A78; padding: 32px;">No comments yet. Be the first to comment!</p>
        @endforelse
    </div>

</div>
</x-app-layout>