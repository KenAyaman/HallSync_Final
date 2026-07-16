<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\MaintenanceTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CapstoneDefenseReadinessTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrator_issued_account_does_not_require_a_separate_email_verification_step(): void
    {
        $resident = User::factory()->unverified()->create(['role' => 'resident']);

        foreach (['tickets.index', 'bookings.index', 'concerns.index', 'community.index'] as $route) {
            $this->actingAs($resident)
                ->get(route($route))
                ->assertOk();
        }
    }

    public function test_guest_cannot_read_community_feed(): void
    {
        $this->get(route('community.index'))->assertRedirect(route('login'));
    }

    public function test_resident_cannot_modify_or_delete_ticket_after_approval(): void
    {
        $resident = User::factory()->create(['role' => 'resident']);
        $ticket = $this->ticket(['user_id' => $resident->id, 'status' => 'approved']);

        $this->actingAs($resident)
            ->patch(route('tickets.update', $ticket), [
                'description' => 'Changed after approval.',
                'priority' => 'medium',
                'category' => 'other',
            ])
            ->assertForbidden();

        $this->actingAs($resident)
            ->delete(route('tickets.destroy', $ticket))
            ->assertForbidden();
    }

    public function test_resident_ticket_tracker_hides_the_internal_approved_stage(): void
    {
        $resident = User::factory()->create(['role' => 'resident']);
        $ticket = $this->ticket(['user_id' => $resident->id, 'status' => 'approved']);

        $this->assertSame([
            'Submitted for Review',
            'Assigned to Staff',
            'Work in Progress',
            'Resolved',
        ], collect($ticket->tracker_steps)->pluck('label')->all());

        $this->actingAs($resident)
            ->get(route('tickets.track', $ticket))
            ->assertOk()
            ->assertSee('Status Guide')
            ->assertDontSee('Administration accepted the request and it is ready for staff assignment.')
            ->assertDontSee('Ticket Information');

        $this->actingAs($resident)
            ->get(route('tickets.show', $ticket))
            ->assertOk()
            ->assertSee('Ticket Information')
            ->assertSee('Track')
            ->assertDontSee('Status Guide');
    }

    public function test_ticket_moves_from_assignment_to_resolution_and_resident_closure(): void
    {
        $resident = User::factory()->create(['role' => 'resident']);
        $handyman = User::factory()->create(['role' => 'handyman']);
        $ticket = $this->ticket([
            'user_id' => $resident->id,
            'assigned_to' => $handyman->id,
            'status' => 'assigned',
        ]);

        $this->actingAs($handyman)
            ->patch(route('tickets.update-status', $ticket), ['status' => 'in_progress'])
            ->assertRedirect(route('tickets.show', $ticket));

        $this->actingAs($handyman)
            ->patch(route('tickets.update-status', $ticket), ['status' => 'resolved'])
            ->assertRedirect(route('tickets.show', $ticket));

        $this->actingAs($resident)
            ->patch(route('tickets.close', $ticket))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('maintenance_tickets', [
            'id' => $ticket->id,
            'status' => 'closed',
        ]);

        $ticket->refresh();
        $this->assertNotNull($ticket->work_started_at);
        $this->assertNotNull($ticket->task_started_at);
        $this->assertNotNull($ticket->task_completed_at);
        $this->assertNotNull($ticket->task_duration_minutes);
    }

    public function test_post_approval_ticket_uses_audited_cancellation_request(): void
    {
        $resident = User::factory()->create(['role' => 'resident']);
        $manager = User::factory()->create(['role' => 'manager']);
        $ticket = $this->ticket(['user_id' => $resident->id, 'status' => 'approved']);

        $this->actingAs($resident)
            ->patch(route('tickets.request-cancellation', $ticket), [
                'cancellation_reason' => 'The issue was resolved before staff arrival.',
            ])
            ->assertSessionHas('success');

        $this->actingAs($manager)
            ->patch(route('tickets.cancel', $ticket))
            ->assertRedirect(route('tickets.index'));

        $this->assertDatabaseHas('maintenance_tickets', [
            'id' => $ticket->id,
            'status' => 'cancelled',
        ]);
        $this->assertDatabaseHas('user_activity_logs', ['action' => 'ticket.cancellation_requested']);
        $this->assertDatabaseHas('user_activity_logs', ['action' => 'ticket.cancelled']);
    }

    public function test_move_out_archives_resident_and_releases_future_booking(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $resident = User::factory()->create(['role' => 'resident']);
        $startsAt = now()->addDay()->startOfHour();
        $booking = Booking::create([
            'user_id' => $resident->id,
            'facility_name' => 'Study Room 1',
            'booking_date' => $startsAt,
            'end_time' => $startsAt->copy()->addHour(),
            'status' => 'approved',
        ]);
        DB::table('booking_slot_claims')->insert([
            'booking_id' => $booking->id,
            'user_id' => $resident->id,
            'facility_name' => $booking->facility_name,
            'booking_date' => $startsAt,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($manager)
            ->patch(route('admin.users.move-out', $resident))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $resident->id,
            'is_active' => false,
            'residency_status' => 'moved_out',
        ]);
        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'cancelled']);
        $this->assertDatabaseMissing('booking_slot_claims', ['booking_id' => $booking->id]);
    }

    private function ticket(array $attributes = []): MaintenanceTicket
    {
        return MaintenanceTicket::create(array_merge([
            'user_id' => User::factory()->create(['role' => 'resident'])->id,
            'ticket_id' => 'TKT-' . uniqid(),
            'title' => 'Test maintenance issue',
            'description' => 'A maintenance issue requiring workflow validation.',
            'priority' => 'medium',
            'status' => 'pending_approval',
        ], $attributes));
    }
}
