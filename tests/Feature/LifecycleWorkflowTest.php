<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\Booking;
use App\Models\CommunityPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LifecycleWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_residents_only_see_current_announcements(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $resident = User::factory()->create(['role' => 'resident']);

        Announcement::create([
            'user_id' => $manager->id,
            'title' => 'Expired notice',
            'content' => 'This should stay in admin history.',
            'priority' => 'normal',
            'is_active' => true,
            'starts_at' => now()->subDays(2),
            'expires_at' => now()->subDay(),
        ]);

        Announcement::create([
            'user_id' => $manager->id,
            'title' => 'Current notice',
            'content' => 'This is visible to residents.',
            'priority' => 'important',
            'is_active' => true,
            'starts_at' => now()->subHour(),
            'expires_at' => now()->addDay(),
        ]);

        $this->actingAs($resident)
            ->get(route('announcements.index'))
            ->assertOk()
            ->assertSee('Current notice')
            ->assertDontSee('Expired notice');
    }

    public function test_expired_booking_disappears_from_resident_schedule_without_being_deleted(): void
    {
        $resident = User::factory()->create(['role' => 'resident']);

        $booking = Booking::create([
            'user_id' => $resident->id,
            'facility_name' => 'Study Room 1',
            'booking_date' => now()->subHours(2),
            'end_time' => now()->subHour(),
            'status' => 'approved',
        ]);

        $this->actingAs($resident)
            ->get(route('bookings.index'))
            ->assertOk()
            ->assertDontSee('data-active-booking-id="'.$booking->id.'"', false)
            ->assertSee('data-history-booking-id="'.$booking->id.'"', false)
            ->assertSee('Study Room 1');

        $this->assertDatabaseHas('bookings', ['id' => $booking->id]);
    }

    public function test_pending_community_post_cannot_be_edited_mid_review(): void
    {
        $resident = User::factory()->create(['role' => 'resident']);

        $post = CommunityPost::create([
            'user_id' => $resident->id,
            'title' => 'Awaiting review',
            'content' => 'Please review this post.',
            'type' => 'discussion',
            'status' => 'pending',
        ]);

        $this->actingAs($resident)
            ->get(route('community.edit', $post))
            ->assertRedirect(route('community.index'))
            ->assertSessionHas('error');
    }
}
