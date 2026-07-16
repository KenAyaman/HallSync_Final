<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BookingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_resident_can_create_a_booking(): void
    {
        $resident = User::factory()->create(['role' => 'resident']);

        $response = $this->actingAs($resident)->post(route('bookings.store'), [
            'facility_name' => 'Study Room 1',
            'booking_date' => now()->addDay()->format('Y-m-d'),
            'booking_time' => '08:00',
            'notes' => 'Group study session',
        ]);

        $response->assertRedirect(route('bookings.index'));

        $this->assertDatabaseHas('bookings', [
            'user_id' => $resident->id,
            'facility_name' => 'Study Room 1',
            'status' => 'approved',
        ]);
    }

    public function test_same_facility_time_accepts_bookings_until_capacity_is_full(): void
    {
        $resident = User::factory()->create(['role' => 'resident']);
        $date = now()->addDay()->startOfDay()->setTime(8, 0);

        for ($i = 0; $i < 25; $i++) {
            Booking::create([
                'user_id' => User::factory()->create(['role' => 'resident'])->id,
                'facility_name' => 'Study Room 1',
                'booking_date' => $date,
                'end_time' => $date->copy()->addHour(),
                'status' => 'approved',
            ]);
        }

        $response = $this->from(route('bookings.create'))
            ->actingAs($resident)
            ->post(route('bookings.store'), [
                'facility_name' => 'Study Room 1',
                'booking_date' => $date->format('Y-m-d'),
                'booking_time' => '08:00',
            ]);

        $response->assertRedirect(route('bookings.create'));
        $response->assertSessionHasErrors('booking_time');
        $response->assertSessionHas('error', 'That time slot is already full for this facility.');
    }

    public function test_same_facility_time_can_be_shared_when_capacity_remains(): void
    {
        $resident = User::factory()->create(['role' => 'resident']);
        $date = now()->addDay()->startOfDay()->setTime(8, 0);

        Booking::create([
            'user_id' => User::factory()->create(['role' => 'resident'])->id,
            'facility_name' => 'Study Room 1',
            'booking_date' => $date,
            'end_time' => $date->copy()->addHour(),
            'status' => 'approved',
        ]);

        $this->actingAs($resident)
            ->post(route('bookings.store'), [
                'facility_name' => 'Study Room 1',
                'booking_date' => $date->format('Y-m-d'),
                'booking_time' => '08:00',
            ])
            ->assertRedirect(route('bookings.index'));

        $this->assertDatabaseCount('bookings', 2);
    }

    public function test_resident_cannot_book_two_facilities_during_the_same_time(): void
    {
        $resident = User::factory()->create(['role' => 'resident']);
        $date = now()->addDay()->startOfDay()->setTime(8, 0);

        Booking::create([
            'user_id' => $resident->id,
            'facility_name' => 'Study Room 1',
            'booking_date' => $date,
            'end_time' => $date->copy()->addHour(),
            'status' => 'approved',
        ]);

        $response = $this->from(route('bookings.create'))
            ->actingAs($resident)
            ->post(route('bookings.store'), [
                'facility_name' => 'Gym',
                'booking_date' => $date->format('Y-m-d'),
                'booking_time' => '08:00',
            ]);

        $response->assertRedirect(route('bookings.create'));
        $response->assertSessionHasErrors('booking_time');
        $response->assertSessionHas('error', 'You already have another booking during that time. Please choose a different slot.');
        $this->assertDatabaseCount('bookings', 1);
    }

    public function test_live_availability_marks_residents_other_facility_booking_as_reserved(): void
    {
        $resident = User::factory()->create(['role' => 'resident']);
        $date = now()->addDay()->startOfDay()->setTime(8, 0);

        Booking::create([
            'user_id' => $resident->id,
            'facility_name' => 'Study Room 1',
            'booking_date' => $date,
            'end_time' => $date->copy()->addHour(),
            'status' => 'approved',
        ]);

        $response = $this->actingAs($resident)->getJson(route('bookings.reserved-slots', [
            'facility_name' => 'Gym',
            'booking_date' => $date->format('Y-m-d'),
        ]));

        $response->assertOk();
        $response->assertJsonFragment(['reserved_slots' => ['08:00']]);
    }

    public function test_resident_cannot_edit_booking_into_another_booking_time(): void
    {
        $resident = User::factory()->create(['role' => 'resident']);
        $date = now()->addDay()->startOfDay()->setTime(8, 0);

        Booking::create([
            'user_id' => $resident->id,
            'facility_name' => 'Study Room 1',
            'booking_date' => $date,
            'end_time' => $date->copy()->addHour(),
            'status' => 'approved',
        ]);

        $booking = Booking::create([
            'user_id' => $resident->id,
            'facility_name' => 'Gym',
            'booking_date' => $date->copy()->addHour(),
            'end_time' => $date->copy()->addHours(2),
            'status' => 'approved',
        ]);

        $response = $this->from(route('bookings.edit', $booking))
            ->actingAs($resident)
            ->patch(route('bookings.update', $booking), [
                'facility_name' => 'Gym',
                'booking_date' => $date->format('Y-m-d'),
                'booking_time' => '08:00',
            ]);

        $response->assertRedirect(route('bookings.edit', $booking));
        $response->assertSessionHasErrors('booking_time');
        $response->assertSessionHas('error', 'You already have another booking during that time. Please choose a different slot.');
        $this->assertTrue($booking->fresh()->booking_date->equalTo($date->copy()->addHour()));
    }

    public function test_edit_availability_can_exclude_the_current_booking(): void
    {
        $resident = User::factory()->create(['role' => 'resident']);
        $date = now()->addDay()->startOfDay()->setTime(8, 0);

        $booking = Booking::create([
            'user_id' => $resident->id,
            'facility_name' => 'Study Room 1',
            'booking_date' => $date,
            'end_time' => $date->copy()->addHour(),
            'status' => 'approved',
        ]);

        $response = $this->actingAs($resident)->getJson(route('bookings.reserved-slots', [
            'facility_name' => 'Study Room 1',
            'booking_date' => $date->format('Y-m-d'),
            'exclude_booking_id' => $booking->id,
        ]));

        $response->assertOk();
        $response->assertJsonMissing(['reserved_slots' => ['08:00']]);
    }

    public function test_availability_counts_another_residents_booking_without_blocking_capacity(): void
    {
        $resident = User::factory()->create(['role' => 'resident']);
        $date = now()->addDay()->startOfDay()->setTime(8, 0);

        $booking = Booking::create([
            'user_id' => User::factory()->create(['role' => 'resident'])->id,
            'facility_name' => 'Study Room 1',
            'booking_date' => $date,
            'end_time' => $date->copy()->addHour(),
            'status' => 'approved',
        ]);

        $response = $this->actingAs($resident)->getJson(route('bookings.reserved-slots', [
            'facility_name' => 'Study Room 1',
            'booking_date' => $date->format('Y-m-d'),
            'exclude_booking_id' => $booking->id,
        ]));

        $response->assertOk();
        $response->assertJsonPath('reserved_slots', []);
        $response->assertJsonPath('slot_capacity.08:00.reserved', 1);
        $response->assertJsonPath('slot_capacity.08:00.capacity', 25);
        $response->assertJsonPath('slot_capacity.08:00.available', 24);
    }

    public function test_manager_booking_details_lists_every_resident_in_the_same_slot(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $firstResident = User::factory()->create(['role' => 'resident', 'name' => 'Ana Resident']);
        $secondResident = User::factory()->create(['role' => 'resident', 'name' => 'Ben Resident']);
        $date = now()->addDay()->startOfDay()->setTime(8, 0);

        $firstBooking = Booking::create([
            'user_id' => $firstResident->id,
            'facility_name' => 'Study Room 1',
            'booking_date' => $date,
            'end_time' => $date->copy()->addHour(),
            'status' => 'approved',
        ]);

        Booking::create([
            'user_id' => $secondResident->id,
            'facility_name' => 'Study Room 1',
            'booking_date' => $date,
            'end_time' => $date->copy()->addHour(),
            'status' => 'approved',
        ]);

        $response = $this->actingAs($manager)->getJson(route('admin.bookings.details', $firstBooking));

        $response->assertOk();
        $response->assertJsonPath('reserved_count', 2);
        $response->assertJsonPath('capacity', 25);
        $response->assertJsonPath('available_count', 23);
        $response->assertJsonPath('residents.0.name', 'Ana Resident');
        $response->assertJsonPath('residents.1.name', 'Ben Resident');
    }

    public function test_manager_can_cancel_a_resident_booking_with_a_reason(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $resident = User::factory()->create(['role' => 'resident']);
        $date = now()->addDay()->startOfDay()->setTime(15, 0);

        $booking = Booking::create([
            'user_id' => $resident->id,
            'facility_name' => 'Study Room 1',
            'booking_date' => $date,
            'end_time' => $date->copy()->addHour(),
            'status' => 'approved',
        ]);

        DB::table('booking_slot_claims')->insert([
            'booking_id' => $booking->id,
            'user_id' => $resident->id,
            'facility_name' => 'Study Room 1',
            'booking_date' => $date,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($manager)->patchJson(route('admin.bookings.cancel', $booking), [
            'reason' => 'Facility needs urgent maintenance.',
        ]);

        $response->assertOk();
        $response->assertJsonPath('message', 'Booking cancelled. The slot has been released.');
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'cancelled',
            'rejection_reason' => 'Facility needs urgent maintenance.',
        ]);
        $this->assertDatabaseMissing('booking_slot_claims', ['booking_id' => $booking->id]);
    }

    public function test_handyman_cannot_create_a_booking(): void
    {
        $handyman = User::factory()->create(['role' => 'handyman']);

        $response = $this->actingAs($handyman)->get(route('bookings.create'));

        $response->assertForbidden();
    }

    public function test_cancelling_a_booking_preserves_history_and_releases_the_slot(): void
    {
        $resident = User::factory()->create(['role' => 'resident']);
        $date = now()->addDay()->format('Y-m-d');

        $this->actingAs($resident)->post(route('bookings.store'), [
            'facility_name' => 'Study Room 1',
            'booking_date' => $date,
            'booking_time' => '08:00',
        ])->assertRedirect(route('bookings.index'));

        $booking = Booking::firstOrFail();

        $this->assertDatabaseHas('booking_slot_claims', ['booking_id' => $booking->id]);

        $this->actingAs($resident)
            ->delete(route('bookings.destroy', $booking))
            ->assertRedirect(route('bookings.index'));

        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'cancelled']);
        $this->assertDatabaseMissing('booking_slot_claims', ['booking_id' => $booking->id]);

        $this->actingAs($resident)->post(route('bookings.store'), [
            'facility_name' => 'Study Room 1',
            'booking_date' => $date,
            'booking_time' => '08:00',
        ])->assertRedirect(route('bookings.index'));

        $this->assertDatabaseCount('bookings', 2);
    }

}
