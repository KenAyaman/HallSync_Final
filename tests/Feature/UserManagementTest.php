<?php

namespace Tests\Feature;

use App\Models\MaintenanceTicket;
use App\Models\Booking;
use App\Models\User;
use App\Models\UserActivityLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_managers_can_open_the_user_directory(): void
    {
        $resident = User::factory()->create(['role' => 'resident']);

        $this->actingAs($resident)
            ->get(route('admin.users'))
            ->assertForbidden();
    }

    public function test_manager_can_provision_a_user_with_a_temporary_password_and_an_audit_log(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);

        $response = $this->actingAs($manager)->post(route('admin.users.store'), [
            'name' => 'New Resident',
            'email' => 'resident@rexhall.com',
            'phone_number' => '09171234567',
            'role' => 'resident',
            'room_number' => 'A-101',
        ]);

        $user = User::where('email', 'resident@rexhall.com')->firstOrFail();

        $response
            ->assertRedirect(route('admin.users.show', $user))
            ->assertSessionHas('temporary_password');

        $temporaryPassword = $response->getSession()->get('temporary_password');

        $this->assertTrue($user->must_change_password);
        $this->assertTrue($user->is_active);
        $this->assertStringStartsWith('RexHall-', $temporaryPassword);
        $this->assertTrue(Hash::check($temporaryPassword, $user->password));
        $this->assertSame($temporaryPassword, $user->fresh()->temporary_password);
        $this->assertNotSame($temporaryPassword, $user->fresh()->getRawOriginal('temporary_password'));
        $this->assertSame('RES-'.str_pad((string) $user->id, 6, '0', STR_PAD_LEFT), $user->resident_number);
        $this->actingAs($manager)
            ->get(route('admin.users.show', $user))
            ->assertOk()
            ->assertSee($temporaryPassword);
        $this->assertDatabaseHas('user_activity_logs', [
            'subject_user_id' => $user->id,
            'actor_user_id' => $manager->id,
            'action' => 'account.created',
        ]);
    }

    public function test_duplicate_email_and_missing_resident_room_are_rejected(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        User::factory()->create(['email' => 'resident@rexhall.com']);

        $this->actingAs($manager)
            ->post(route('admin.users.store'), [
                'name' => 'Duplicate Resident',
                'email' => 'resident@rexhall.com',
                'role' => 'resident',
            ])
            ->assertSessionHasErrors(['email', 'room_number']);
    }

    public function test_user_accounts_require_the_official_rexhall_email_domain(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);

        $this->actingAs($manager)
            ->post(route('admin.users.store'), [
                'name' => 'Personal Email Resident',
                'email' => 'personal@gmail.com',
                'role' => 'resident',
                'room_number' => 'A-102',
            ])
            ->assertSessionHasErrors('email');
    }

    public function test_generated_password_requires_change_before_operational_access(): void
    {
        $user = User::factory()->create([
            'must_change_password' => true,
            'password' => Hash::make('Temporary-Password-123'),
            'temporary_password' => 'Temporary-Password-123',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('password.change'));

        $this->actingAs($user)
            ->post(route('password.change.update'), [
                'current_password' => 'Temporary-Password-123',
                'password' => 'Replacement-Password-456',
                'password_confirmation' => 'Replacement-Password-456',
            ])
            ->assertRedirect(route('dashboard'));

        $user->refresh();
        $this->assertFalse($user->must_change_password);
        $this->assertNull($user->temporary_password);
        $this->assertDatabaseHas('user_activity_logs', [
            'subject_user_id' => $user->id,
            'action' => 'password.changed',
        ]);
    }

    public function test_last_active_manager_cannot_be_deactivated_or_demoted(): void
    {
        $manager = User::factory()->create([
            'email' => 'manager@rexhall.com',
            'role' => 'manager',
            'is_active' => true,
        ]);

        $this->actingAs($manager)
            ->patch(route('admin.users.update-status', $manager), ['status' => 'inactive'])
            ->assertSessionHas('error');

        $this->actingAs($manager)
            ->patch(route('admin.users.update', $manager), [
                'name' => $manager->name,
                'email' => $manager->email,
                'phone_number' => null,
                'room_number' => 'A-001',
                'role' => 'resident',
            ])
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', [
            'id' => $manager->id,
            'role' => 'manager',
            'is_active' => true,
        ]);
    }

    public function test_account_with_operational_history_is_deactivated_instead_of_deleted(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $resident = User::factory()->create(['role' => 'resident']);
        MaintenanceTicket::create([
            'user_id' => $resident->id,
            'ticket_id' => 'TKT-USER-1',
            'title' => 'Broken lock',
            'description' => 'The room lock needs repair.',
            'priority' => 'medium',
            'status' => 'pending_approval',
        ]);

        $this->actingAs($manager)
            ->delete(route('admin.users.destroy', $resident))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $resident->id]);

        $this->actingAs($manager)
            ->patch(route('admin.users.update-status', $resident), ['status' => 'inactive'])
            ->assertSessionHas('success');

        $this->assertFalse($resident->fresh()->is_active);
    }

    public function test_manager_can_reset_password_with_a_temporary_password_and_an_audit_log(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $resident = User::factory()->create(['role' => 'resident']);
        $originalPassword = $resident->password;

        $response = $this->actingAs($manager)
            ->patch(route('admin.users.reset-password', $resident))
            ->assertSessionHas('success')
            ->assertSessionHas('temporary_password');

        $temporaryPassword = $response->getSession()->get('temporary_password');

        $resident->refresh();

        $this->assertNotSame($originalPassword, $resident->password);
        $this->assertTrue($resident->must_change_password);
        $this->assertStringStartsWith('RexHall-', $temporaryPassword);
        $this->assertTrue(Hash::check($temporaryPassword, $resident->password));
        $this->assertSame($temporaryPassword, $resident->temporary_password);
        $this->assertNotSame($temporaryPassword, $resident->getRawOriginal('temporary_password'));
        $this->assertDatabaseHas('user_activity_logs', [
            'subject_user_id' => $resident->id,
            'actor_user_id' => $manager->id,
            'action' => 'password.reset',
        ]);
    }

    public function test_manager_can_delete_an_unused_account_while_preserving_the_audit_entry(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $unusedAccount = User::factory()->create(['role' => 'resident']);

        $this->actingAs($manager)
            ->delete(route('admin.users.destroy', $unusedAccount))
            ->assertRedirect(route('admin.users'))
            ->assertSessionHas('success');

        $this->assertSoftDeleted('users', ['id' => $unusedAccount->id]);
        $this->assertDatabaseHas('user_activity_logs', [
            'actor_user_id' => $manager->id,
            'action' => 'account.deleted',
        ]);
    }

    public function test_manager_can_bulk_deactivate_accounts_and_filter_the_directory(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $residentA = User::factory()->create(['role' => 'resident', 'name' => 'Alpha Resident']);
        $residentB = User::factory()->create(['role' => 'resident', 'name' => 'Beta Resident']);

        $this->actingAs($manager)
            ->patch(route('admin.users.bulk-status'), [
                'bulk_action' => 'deactivate',
                'user_ids' => [$residentA->id, $residentB->id],
            ])
            ->assertSessionHas('success');

        $this->assertFalse($residentA->fresh()->is_active);
        $this->assertFalse($residentB->fresh()->is_active);

        $this->actingAs($manager)
            ->get(route('admin.users', ['status' => 'inactive', 'role' => 'resident', 'search' => 'Alpha']))
            ->assertOk()
            ->assertSee('Alpha Resident')
            ->assertDontSee('Beta Resident');
    }

    public function test_user_detail_page_displays_related_requests_and_activity(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $resident = User::factory()->create(['role' => 'resident', 'room_number' => 'B-202']);
        MaintenanceTicket::create([
            'user_id' => $resident->id,
            'ticket_id' => 'TKT-USER-2',
            'title' => 'Water pressure issue',
            'description' => 'The water pressure is low.',
            'priority' => 'medium',
            'status' => 'pending_approval',
        ]);
        UserActivityLog::record('account.updated', 'Updated account information.', $resident, $manager);

        $this->actingAs($manager)
            ->get(route('admin.users.show', $resident))
            ->assertOk()
            ->assertSee('Water pressure issue')
            ->assertSee('Updated account information.')
            ->assertSee('B-202');
    }

    public function test_deactivating_resident_cancels_future_bookings_and_releases_claims(): void
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
        DB::table('booking_slot_claims')->insert([
            'booking_id' => $booking->id,
            'user_id' => $resident->id,
            'facility_name' => $booking->facility_name,
            'booking_date' => $booking->booking_date,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($manager)
            ->patch(route('admin.users.update-status', $resident), ['status' => 'inactive'])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'cancelled']);
        $this->assertDatabaseMissing('booking_slot_claims', ['booking_id' => $booking->id]);
    }

    public function test_staff_with_active_assignments_cannot_be_deactivated(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $resident = User::factory()->create(['role' => 'resident']);
        $handyman = User::factory()->create(['role' => 'handyman']);
        MaintenanceTicket::create([
            'user_id' => $resident->id,
            'assigned_to' => $handyman->id,
            'ticket_id' => 'TKT-ACTIVE-STAFF',
            'title' => 'Repair in progress',
            'description' => 'This ticket must be reassigned first.',
            'priority' => 'medium',
            'status' => 'assigned',
        ]);

        $this->actingAs($manager)
            ->patch(route('admin.users.update-status', $handyman), ['status' => 'inactive'])
            ->assertSessionHas('error');

        $this->assertTrue($handyman->fresh()->is_active);
    }

    public function test_manual_resident_number_is_rejected_during_account_creation(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        User::factory()->create(['resident_number' => 'RES-EXISTING']);

        $this->actingAs($manager)
            ->post(route('admin.users.store'), [
                'name' => 'Duplicate Resident ID',
                'email' => 'unique@rexhall.com',
                'role' => 'resident',
                'room_number' => 'A-102',
                'resident_number' => 'RES-EXISTING',
            ])
            ->assertSessionHasErrors('resident_number');
    }
}
