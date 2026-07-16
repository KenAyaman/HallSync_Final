<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Events\DashboardUpdated;
use App\Models\Booking;
use App\Models\MaintenanceTicket;
use App\Models\User;
use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductionHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_deactivated_user_cannot_log_in(): void
    {
        $user = User::factory()->create([
            'is_active' => false,
            'password' => 'password',
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_manager_cannot_submit_resident_booking(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);

        $response = $this->actingAs($manager)->post(route('bookings.store'), [
            'facility_name' => 'Study Room 1',
            'booking_date' => now()->addDay()->format('Y-m-d'),
            'booking_time' => '08:00',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_resident_cannot_open_hidden_announcement(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $resident = User::factory()->create(['role' => 'resident']);
        $announcement = Announcement::create([
            'user_id' => $manager->id,
            'title' => 'Draft notice',
            'content' => 'This should remain hidden.',
            'priority' => 'normal',
            'is_active' => false,
        ]);

        $this->actingAs($resident)
            ->get(route('announcements.show', $announcement))
            ->assertNotFound();
    }

    public function test_manager_cannot_assign_ticket_to_resident(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $resident = User::factory()->create(['role' => 'resident']);
        $ticket = MaintenanceTicket::create([
            'user_id' => $resident->id,
            'ticket_id' => 'TKT-1001',
            'title' => 'Leaking sink',
            'description' => 'The sink is leaking.',
            'priority' => 'medium',
            'status' => 'approved',
        ]);

        $this->actingAs($manager)
            ->post(route('tickets.assign', $ticket), ['assigned_to' => $resident->id])
            ->assertSessionHasErrors('assigned_to');
    }

    public function test_manager_can_open_user_management(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);

        $this->actingAs($manager)
            ->get(route('admin.users'))
            ->assertOk();
    }

    public function test_manager_can_reject_ticket_with_reason(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $resident = User::factory()->create(['role' => 'resident']);
        $ticket = MaintenanceTicket::create([
            'user_id' => $resident->id,
            'ticket_id' => 'TKT-REJECT-1',
            'title' => 'Duplicate maintenance request',
            'description' => 'This request was already reported.',
            'priority' => 'medium',
            'status' => 'pending_approval',
        ]);

        $this->actingAs($manager)
            ->post(route('tickets.reject', $ticket), [
                'rejection_reason' => 'This request duplicates an existing ticket.',
            ])
            ->assertRedirect(route('tickets.index'));

        $this->assertDatabaseHas('maintenance_tickets', [
            'id' => $ticket->id,
            'status' => 'rejected',
            'rejection_reason' => 'This request duplicates an existing ticket.',
        ]);

        $this->actingAs($manager)
            ->get(route('tickets.index'))
            ->assertSee('Rejected')
            ->assertSee('This request duplicates an existing ticket.');
    }

    public function test_database_updates_continue_when_realtime_broadcasting_is_offline(): void
    {
        Event::listen(DashboardUpdated::class, function () {
            throw new BroadcastException('WebSocket server is offline.');
        });

        $user = User::factory()->create();
        $user->update(['name' => 'Updated while offline']);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated while offline',
        ]);
    }

    public function test_recent_duplicate_maintenance_request_is_rejected(): void
    {
        $resident = User::factory()->create(['role' => 'resident']);
        $payload = [
            'title' => 'Leaking sink',
            'description' => 'Water is dripping continuously below the sink.',
            'category' => 'plumbing',
            'priority' => 'medium',
        ];

        $this->actingAs($resident)->post(route('tickets.store'), $payload);

        $this->actingAs($resident)
            ->from(route('tickets.create'))
            ->post(route('tickets.store'), $payload)
            ->assertRedirect(route('tickets.create'))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('maintenance_tickets', 1);
    }

    public function test_resident_can_reopen_recent_completed_ticket(): void
    {
        $resident = User::factory()->create(['role' => 'resident']);
        $handyman = User::factory()->create(['role' => 'handyman', 'is_active' => true]);
        $ticket = MaintenanceTicket::create([
            'user_id' => $resident->id,
            'assigned_to' => $handyman->id,
            'ticket_id' => 'TKT-REOPEN-1',
            'title' => 'Leaking sink',
            'description' => 'The repaired sink is leaking again.',
            'priority' => 'medium',
            'status' => 'completed',
        ]);

        $this->actingAs($resident)
            ->patch(route('tickets.reopen', $ticket))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('maintenance_tickets', [
            'id' => $ticket->id,
            'assigned_to' => $handyman->id,
            'status' => 'assigned',
        ]);
    }

    public function test_resident_cannot_reopen_ticket_after_reopening_period(): void
    {
        $resident = User::factory()->create(['role' => 'resident']);
        $ticket = MaintenanceTicket::create([
            'user_id' => $resident->id,
            'ticket_id' => 'TKT-REOPEN-OLD',
            'title' => 'Old repair',
            'description' => 'This repair was completed some time ago.',
            'priority' => 'medium',
            'status' => 'completed',
        ]);
        $ticket->forceFill(['updated_at' => now()->subDays(8)])->saveQuietly();

        $this->actingAs($resident)
            ->patch(route('tickets.reopen', $ticket))
            ->assertSessionHas('error');

        $this->assertSame('completed', $ticket->fresh()->status);
    }

    public function test_ticket_decisions_are_recorded_in_operational_audit_log(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $resident = User::factory()->create(['role' => 'resident']);
        $ticket = MaintenanceTicket::create([
            'user_id' => $resident->id,
            'ticket_id' => 'TKT-AUDIT-1',
            'title' => 'Audit this decision',
            'description' => 'This approval should be recorded.',
            'priority' => 'medium',
            'status' => 'pending_approval',
        ]);

        $this->actingAs($manager)->post(route('tickets.approve', $ticket));

        $this->assertDatabaseHas('user_activity_logs', [
            'actor_user_id' => $manager->id,
            'action' => 'ticket.approved',
        ]);
    }

    public function test_ticket_upload_rejects_unsupported_file_type(): void
    {
        $resident = User::factory()->create(['role' => 'resident']);

        $this->actingAs($resident)
            ->post(route('tickets.store'), [
                'title' => 'Unsafe attachment',
                'description' => 'This attachment type must not be accepted.',
                'category' => 'other',
                'priority' => 'medium',
                'video' => UploadedFile::fake()->create('payload.avi', 100, 'video/x-msvideo'),
            ])
            ->assertSessionHasErrors('video');

        $this->assertDatabaseCount('maintenance_tickets', 0);
    }

    public function test_ticket_media_requires_ticket_authorization(): void
    {
        Storage::fake('local');

        $owner = User::factory()->create(['role' => 'resident']);
        $otherResident = User::factory()->create(['role' => 'resident']);
        $ticket = MaintenanceTicket::create([
            'user_id' => $owner->id,
            'ticket_id' => 'TKT-MEDIA-1',
            'title' => 'Private media',
            'description' => 'Only the ticket owner should see this.',
            'priority' => 'medium',
            'status' => 'pending_approval',
            'image_path' => 'tickets/images/private.jpg',
        ]);
        Storage::disk('local')->put($ticket->image_path, 'private-image');

        $this->actingAs($otherResident)
            ->get(route('media.tickets.show', ['ticket' => $ticket, 'type' => 'image']))
            ->assertForbidden();

        $this->actingAs($owner)
            ->get(route('media.tickets.show', ['ticket' => $ticket, 'type' => 'image']))
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    public function test_analytics_csv_exports_escape_formula_cells(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $resident = User::factory()->create(['role' => 'resident', 'name' => '=Resident']);
        MaintenanceTicket::create([
            'user_id' => $resident->id,
            'ticket_id' => 'TKT-CSV-1',
            'title' => '=HYPERLINK("https://example.test","open")',
            'description' => 'Formula injection test.',
            'priority' => 'medium',
            'status' => 'pending_approval',
            'location' => '+Room 1',
        ]);
        Booking::create([
            'user_id' => $resident->id,
            'facility_name' => '@Gym',
            'booking_date' => now()->addDay(),
            'end_time' => now()->addDay()->addHour(),
            'status' => 'approved',
        ]);

        $ticketsExport = $this->actingAs($manager)
            ->get(route('admin.analytics.export.tickets'))
            ->assertOk();
        $ticketsCsv = $ticketsExport->streamedContent();

        $this->assertStringContainsString("'=HYPERLINK", $ticketsCsv);
        $this->assertStringContainsString("'+Room 1", $ticketsCsv);

        $bookingsExport = $this->actingAs($manager)
            ->get(route('admin.analytics.export.bookings'))
            ->assertOk();
        $bookingsCsv = $bookingsExport->streamedContent();

        $this->assertStringContainsString("'=Resident", $bookingsCsv);
        $this->assertStringContainsString("'@Gym", $bookingsCsv);
    }

    public function test_custom_announcement_expiry_must_be_after_scheduled_start(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);

        $this->actingAs($manager)
            ->post(route('announcements.store'), [
                'title' => 'Scheduled notice',
                'content' => 'This should not expire before it starts.',
                'priority' => 'normal',
                'starts_at' => now()->addDays(3)->format('Y-m-d H:i:s'),
                'expiration_option' => 'custom',
                'custom_expires_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
            ])
            ->assertSessionHasErrors('custom_expires_at');
    }
}
