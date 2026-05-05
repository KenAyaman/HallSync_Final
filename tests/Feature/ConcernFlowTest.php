<?php

namespace Tests\Feature;

use App\Models\Concern;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConcernFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_resident_can_submit_a_concern(): void
    {
        $resident = User::factory()->create(['role' => 'resident']);

        $response = $this->actingAs($resident)->post(route('concerns.store'), [
            'category' => 'noise',
            'involved_person' => 'Roommate A',
            'location' => 'Unit 3B',
            'details' => 'Loud noise has continued well past quiet hours for several nights.',
        ]);

        $concern = Concern::first();

        $response->assertRedirect(route('concerns.show', $concern));

        $this->assertDatabaseHas('concerns', [
            'user_id' => $resident->id,
            'category' => 'noise',
            'status' => 'submitted',
        ]);
    }

    public function test_manager_can_reply_to_a_concern(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $resident = User::factory()->create(['role' => 'resident']);
        $concern = Concern::create([
            'user_id' => $resident->id,
            'category' => 'safety',
            'subject' => 'Safety Concern',
            'details' => 'The rear entrance lock has been broken since yesterday evening.',
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($manager)->patch(route('admin.concerns.update', $concern), [
            'admin_reply' => 'We have escalated this to building staff and will update you shortly.',
        ]);

        $response->assertRedirect(route('admin.concerns.show', $concern));

        $this->assertDatabaseHas('concerns', [
            'id' => $concern->id,
            'handled_by' => $manager->id,
            'status' => 'responded',
        ]);
    }
}
