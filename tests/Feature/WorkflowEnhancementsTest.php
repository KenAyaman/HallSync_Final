<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\MaintenanceTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkflowEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    public function test_resident_only_sees_assigned_staff_after_assignment(): void
    {
        $resident = User::factory()->create(['role' => 'resident']);
        $handyman = User::factory()->create(['role' => 'handyman', 'name' => 'Marco Repair']);
        $ticket = $this->ticket($resident, ['status' => 'approved']);

        $this->actingAs($resident)
            ->get(route('tickets.show', $ticket))
            ->assertOk()
            ->assertDontSee('Assigned Staff');

        $ticket->update([
            'assigned_to' => $handyman->id,
            'status' => 'assigned',
        ]);

        $this->actingAs($resident)
            ->get(route('tickets.show', $ticket))
            ->assertOk()
            ->assertSee('Assigned Staff')
            ->assertSee('Marco Repair');

        $this->actingAs($resident)
            ->get(route('tickets.track', $ticket))
            ->assertOk()
            ->assertSee('Marco Repair');
    }

    public function test_handyman_completion_note_is_sanitized_saved_and_displayed(): void
    {
        $resident = User::factory()->create(['role' => 'resident']);
        $handyman = User::factory()->create(['role' => 'handyman']);
        $manager = User::factory()->create(['role' => 'manager']);
        $ticket = $this->ticket($resident, [
            'assigned_to' => $handyman->id,
            'status' => 'in_progress',
            'work_started_at' => now()->subMinutes(20),
            'task_started_at' => now()->subMinutes(20),
        ]);

        $this->actingAs($handyman)
            ->get(route('tickets.show', $ticket))
            ->assertOk()
            ->assertSee('Completion Note');

        $this->actingAs($handyman)
            ->patch(route('tickets.update-status', $ticket), [
                'status' => 'resolved',
                'completion_note' => '<b>Replaced</b> faucet washer and tested for leaks.',
            ])
            ->assertRedirect(route('tickets.show', $ticket));

        $this->assertDatabaseHas('maintenance_tickets', [
            'id' => $ticket->id,
            'status' => 'resolved',
            'completion_note' => 'Replaced faucet washer and tested for leaks.',
        ]);

        $this->actingAs($resident)
            ->get(route('tickets.show', $ticket))
            ->assertSee('What was done')
            ->assertSee('Replaced faucet washer and tested for leaks.');

        $this->actingAs($manager)
            ->get(route('tickets.show', $ticket))
            ->assertSee('Staff Completion Note');
    }

    public function test_resident_can_rate_a_resolved_ticket_only_once(): void
    {
        $resident = User::factory()->create(['role' => 'resident']);
        $otherResident = User::factory()->create(['role' => 'resident']);
        $ticket = $this->ticket($resident, ['status' => 'resolved']);

        $this->actingAs($resident)
            ->post(route('tickets.rate', $ticket), [
                'satisfaction_rating' => 4,
                'satisfaction_note' => 'Repair was completed well.',
            ])
            ->assertSessionHas('success');

        $ticket->refresh();
        $this->assertSame(4, $ticket->satisfaction_rating);
        $this->assertNotNull($ticket->satisfaction_rated_at);

        $this->actingAs($resident)
            ->post(route('tickets.rate', $ticket), [
                'satisfaction_rating' => 2,
            ])
            ->assertSessionHas('info');

        $this->assertSame(4, $ticket->fresh()->satisfaction_rating);

        $this->actingAs($otherResident)
            ->post(route('tickets.rate', $ticket), [
                'satisfaction_rating' => 5,
            ])
            ->assertForbidden();
    }

    public function test_manager_dashboard_shows_average_handyman_rating(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $resident = User::factory()->create(['role' => 'resident']);
        $handyman = User::factory()->create(['role' => 'handyman', 'name' => 'Rated Handyman']);

        $this->ticket($resident, [
            'assigned_to' => $handyman->id,
            'status' => 'closed',
            'satisfaction_rating' => 4,
            'satisfaction_rated_at' => now(),
        ]);

        $this->actingAs($manager)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Avg Rating')
            ->assertSee('4.0');
    }

    public function test_automatically_approved_booking_does_not_create_notification(): void
    {
        $resident = User::factory()->create(['role' => 'resident']);

        $this->actingAs($resident)
            ->post(route('bookings.store'), [
                'facility_name' => 'Study Room 1',
                'booking_date' => now()->addDay()->format('Y-m-d'),
                'booking_time' => '08:00',
            ])
            ->assertRedirect(route('bookings.index'));

        $this->assertDatabaseHas('bookings', [
            'user_id' => $resident->id,
            'facility_name' => 'Study Room 1',
            'status' => 'approved',
        ]);
        $this->assertCount(0, $resident->fresh()->notifications);
    }

    public function test_manager_cancellation_notifies_booking_owner(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $resident = User::factory()->create(['role' => 'resident']);
        $booking = Booking::create([
            'user_id' => $resident->id,
            'facility_name' => 'Gym',
            'booking_date' => now()->addDay()->startOfHour(),
            'end_time' => now()->addDay()->startOfHour()->addHour(),
            'status' => 'approved',
        ]);

        $this->actingAs($manager)
            ->patchJson(route('admin.bookings.cancel', $booking), [
                'reason' => 'Facility maintenance is required.',
            ])
            ->assertOk();

        $notification = $resident->notifications()->firstOrFail();
        $this->assertSame('cancelled', $notification->data['event']);
        $this->assertStringContainsString('cancelled', $notification->data['message']);
    }

    private function ticket(User $resident, array $attributes = []): MaintenanceTicket
    {
        return MaintenanceTicket::create(array_merge([
            'user_id' => $resident->id,
            'ticket_id' => 'TKT-' . uniqid(),
            'title' => 'Workflow enhancement test',
            'description' => 'A maintenance request used to verify the enhanced workflow.',
            'category' => 'plumbing',
            'location' => 'Room 204',
            'priority' => 'medium',
            'status' => 'pending_approval',
        ], $attributes));
    }
}
