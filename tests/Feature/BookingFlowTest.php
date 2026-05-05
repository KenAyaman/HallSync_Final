<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    public function test_booking_conflict_is_rejected(): void
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

        $response = $this->from(route('bookings.create'))
            ->actingAs($resident)
            ->post(route('bookings.store'), [
                'facility_name' => 'Study Room 1',
                'booking_date' => $date->format('Y-m-d'),
                'booking_time' => '08:00',
            ]);

        $response->assertRedirect(route('bookings.create'));
        $response->assertSessionHasErrors('booking_time');
    }

    public function test_handyman_cannot_create_a_booking(): void
    {
        $handyman = User::factory()->create(['role' => 'handyman']);

        $response = $this->actingAs($handyman)->get(route('bookings.create'));

        $response->assertForbidden();
    }
}
