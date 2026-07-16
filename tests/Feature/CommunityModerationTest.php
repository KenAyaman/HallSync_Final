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

    public function test_manager_can_open_read_only_community_post_details_for_moderation(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $resident = User::factory()->create(['role' => 'resident']);
        $post = CommunityPost::create([
            'user_id' => $resident->id,
            'title' => 'Resident activity notice',
            'content' => 'Please review the details for the weekend activity.',
            'type' => 'event',
            'status' => 'pending',
        ]);

        $this->actingAs($manager)
            ->get(route('community.show', $post))
            ->assertOk()
            ->assertSee('Community Moderation')
            ->assertSee('Resident activity notice')
            ->assertSee('Post Details')
            ->assertDontSee('Approve Post')
            ->assertDontSee('Reject Post')
            ->assertDontSee('Delete Post');
    }

    public function test_resident_feed_filters_match_supported_post_types_and_use_owner_friendly_review_copy(): void
    {
        $resident = User::factory()->create(['role' => 'resident']);
        CommunityPost::create([
            'user_id' => $resident->id,
            'title' => 'Study group notice',
            'content' => 'Looking for residents interested in a weekend study session.',
            'type' => 'discussion',
            'status' => 'pending',
        ]);

        $this->actingAs($resident)
            ->get(route('community.index'))
            ->assertOk()
            ->assertSee('Discussion')
            ->assertSee('Lost &amp; Found', false)
            ->assertSee('Buy &amp; Sell', false)
            ->assertSee('Track your submitted posts.')
            ->assertDontSee('Review and moderate resident submissions.');
    }
}
