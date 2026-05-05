<?php

namespace Tests\Feature;

use App\Models\CommunityComment;
use App\Models\CommunityPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommunityCommentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_comment_owner_can_edit_comment(): void
    {
        $resident = User::factory()->create(['role' => 'resident']);
        $post = CommunityPost::create([
            'user_id' => $resident->id,
            'title' => 'Board game night',
            'content' => 'Anyone interested this Friday?',
            'type' => 'event',
            'status' => 'approved',
        ]);
        $comment = CommunityComment::create([
            'community_post_id' => $post->id,
            'user_id' => $resident->id,
            'content' => 'Original comment',
        ]);

        $response = $this->actingAs($resident)->patch(route('community.comments.update', $comment), [
            'content' => 'Updated comment',
        ]);

        $response->assertRedirect(route('community.show', $post));
        $this->assertDatabaseHas('community_comments', [
            'id' => $comment->id,
            'content' => 'Updated comment',
        ]);
    }

    public function test_comment_owner_can_delete_comment(): void
    {
        $resident = User::factory()->create(['role' => 'resident']);
        $post = CommunityPost::create([
            'user_id' => $resident->id,
            'title' => 'Study group',
            'content' => 'Looking for people to join.',
            'type' => 'discussion',
            'status' => 'approved',
        ]);
        $comment = CommunityComment::create([
            'community_post_id' => $post->id,
            'user_id' => $resident->id,
            'content' => 'Count me in.',
        ]);

        $response = $this->actingAs($resident)->delete(route('community.comments.destroy', $comment));

        $response->assertRedirect(route('community.show', $post));
        $this->assertDatabaseMissing('community_comments', [
            'id' => $comment->id,
        ]);
    }
}
