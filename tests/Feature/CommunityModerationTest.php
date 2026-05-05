<?php

namespace Tests\Feature;

use App\Models\CommunityPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommunityModerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_resident_posts_start_in_pending_status(): void
    {
        $resident = User::factory()->create(['role' => 'resident']);

        $response = $this->actingAs($resident)->post(route('community.store'), [
            'title' => 'Lost water bottle',
            'content' => 'Blue bottle left near the study room.',
            'type' => 'lost_found',
        ]);

        $response->assertRedirect(route('community.index'));

        $this->assertDatabaseHas('community_posts', [
            'user_id' => $resident->id,
            'title' => 'Lost water bottle',
            'status' => 'pending',
        ]);
    }

    public function test_manager_can_approve_pending_post(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $resident = User::factory()->create(['role' => 'resident']);
        $post = CommunityPost::create([
            'user_id' => $resident->id,
            'title' => 'Weekend cleanup drive',
            'content' => 'Looking for volunteers for Saturday morning.',
            'type' => 'event',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($manager)->post(route('community.approve', $post));

        $response->assertRedirect(route('admin.community'));

        $this->assertDatabaseHas('community_posts', [
            'id' => $post->id,
            'status' => 'approved',
            'approved_by' => $manager->id,
        ]);
    }
}
