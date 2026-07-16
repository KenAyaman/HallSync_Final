<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\Concern;
use App\Models\MaintenanceTicket;
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

    public function test_resident_receives_and_can_read_rejected_ticket_notification(): void
    {
        $resident = User::factory()->create(['role' => 'resident']);
        $otherResident = User::factory()->create(['role' => 'resident']);
        $ticket = MaintenanceTicket::create([
            'user_id' => $resident->id,
            'ticket_id' => 'TKT-NOTIFY-REJECT',
            'title' => 'Duplicate sink request',
            'description' => 'The sink issue was reported twice.',
            'priority' => 'medium',
            'status' => 'rejected',
            'rejection_reason' => 'This duplicates your earlier maintenance request.',
        ]);

        $this->actingAs($resident)
            ->get(route('profile.edit'))
            ->assertSee('Request Rejected')
            ->assertSee('This duplicates your earlier maintenance request.');

        $this->actingAs($otherResident)
            ->get(route('profile.edit'))
            ->assertDontSee('Request Rejected')
            ->assertDontSee('This duplicates your earlier maintenance request.');

        $this->actingAs($resident)
            ->get(route('notifications.open', ['type' => 'ticket', 'id' => $ticket->id]))
            ->assertRedirect(route('tickets.show', $ticket));

        $this->assertDatabaseHas('notification_reads', [
            'user_id' => $resident->id,
            'notification_type' => 'ticket',
            'notification_id' => $ticket->id,
        ]);

        $this->actingAs($resident)
            ->get(route('profile.edit'))
            ->assertDontSee('This duplicates your earlier maintenance request.');
    }

    public function test_resident_receives_approved_ticket_notification(): void
    {
        $resident = User::factory()->create(['role' => 'resident']);

        MaintenanceTicket::create([
            'user_id' => $resident->id,
            'ticket_id' => 'TKT-NOTIFY-APPROVE',
            'title' => 'Approved electrical request',
            'description' => 'The corridor light needs repair.',
            'priority' => 'medium',
            'status' => 'approved',
        ]);

        $this->actingAs($resident)
            ->get(route('profile.edit'))
            ->assertSee('Request Approved')
            ->assertSee('Approved electrical request');
    }

    public function test_resident_receives_and_can_read_concern_reply_notification(): void
    {
        $resident = User::factory()->create(['role' => 'resident']);
        $manager = User::factory()->create(['role' => 'manager']);
        $concern = Concern::create([
            'user_id' => $resident->id,
            'category' => 'noise',
            'subject' => 'Quiet hours complaint',
            'location' => 'Unit 3B',
            'details' => 'Noise has continued after quiet hours.',
            'status' => 'submitted',
        ]);

        $this->actingAs($manager)
            ->patch(route('admin.concerns.update', $concern), [
                'admin_reply' => 'Administration has reviewed your complaint.',
            ])
            ->assertRedirect(route('admin.concerns.show', $concern));

        $this->actingAs($resident)
            ->get(route('profile.edit'))
            ->assertSee('Concern Reply')
            ->assertSee('Quiet hours complaint');

        $this->actingAs($resident)
            ->get(route('notifications.open', ['type' => 'concern', 'id' => $concern->id]))
            ->assertRedirect(route('concerns.show', $concern));

        $this->assertDatabaseHas('notification_reads', [
            'user_id' => $resident->id,
            'notification_type' => 'concern',
            'notification_id' => $concern->id,
        ]);

        $this->actingAs($resident)
            ->get(route('profile.edit'))
            ->assertDontSee('Quiet hours complaint');
    }
}
