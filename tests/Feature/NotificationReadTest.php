<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\NotificationRead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationReadTest extends TestCase
{
    use RefreshDatabase;

    public function test_opening_notification_marks_it_as_read_and_hides_it_from_nav(): void
    {
        $resident = User::factory()->create(['role' => 'resident']);
        $announcement = Announcement::create([
            'user_id' => User::factory()->create(['role' => 'manager'])->id,
            'title' => 'Unique Announcement For Read Tracking',
            'content' => 'Please review this update.',
            'priority' => 'important',
            'is_active' => true,
        ]);

        $this->actingAs($resident)
            ->get(route('profile.edit'))
            ->assertSee('Unique Announcement For Read Tracking');

        $response = $this->actingAs($resident)
            ->get(route('notifications.open', ['type' => 'announcement', 'id' => $announcement->id]));

        $response->assertRedirect(route('announcements.show', $announcement));

        $this->assertDatabaseHas('notification_reads', [
            'user_id' => $resident->id,
            'notification_type' => 'announcement',
            'notification_id' => $announcement->id,
        ]);

        $this->actingAs($resident)
            ->get(route('profile.edit'))
            ->assertDontSee('Unique Announcement For Read Tracking');
    }
}
