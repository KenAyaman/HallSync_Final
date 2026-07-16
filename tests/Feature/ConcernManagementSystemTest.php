<?php

namespace Tests\Feature;

use App\Models\Concern;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ConcernManagementSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_resident_submission_generates_a_private_audited_case_with_automatic_priority(): void
    {
        Storage::fake('local');
        $resident = User::factory()->create(['role' => 'resident']);

        $this->actingAs($resident)->post(route('concerns.store'), [
            'category' => 'safety',
            'subject' => 'Unsafe hallway incident',
            'location' => 'Third floor hallway',
            'incident_at' => now()->subHour()->format('Y-m-d H:i:s'),
            'details' => 'A blocked emergency exit created an unsafe hallway condition.',
            'is_anonymous' => '1',
            'evidence' => [UploadedFile::fake()->create('hallway.jpg', 100, 'image/jpeg')],
        ])->assertRedirect();

        $concern = Concern::firstOrFail();
        $this->assertStringStartsWith('CON-', $concern->concern_id);
        $this->assertSame('urgent', $concern->priority);
        $this->assertTrue($concern->is_anonymous);
        $this->assertDatabaseHas('concern_status_histories', ['concern_id' => $concern->id, 'to_status' => 'submitted']);
        $this->assertDatabaseHas('concern_audit_logs', ['concern_id' => $concern->id, 'action' => 'concern.submitted']);
        $this->assertDatabaseCount('concern_evidence', 1);
    }

    public function test_resident_cannot_view_another_residents_concern(): void
    {
        $owner = User::factory()->create(['role' => 'resident']);
        $other = User::factory()->create(['role' => 'resident']);
        $concern = $this->concern(['user_id' => $owner->id]);

        $this->actingAs($other)->get(route('concerns.show', $concern))->assertForbidden();
    }

    public function test_invalid_admin_transition_is_rejected(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $concern = $this->concern(['status' => 'submitted']);

        $this->actingAs($manager)
            ->patch(route('admin.concerns.transition', $concern), ['status' => 'closed'])
            ->assertSessionHasErrors('status');

        $this->assertSame('submitted', $concern->fresh()->status);
    }

    public function test_resident_cannot_edit_concern_after_review_starts(): void
    {
        $resident = User::factory()->create(['role' => 'resident']);
        $concern = $this->concern(['user_id' => $resident->id, 'status' => 'under_review']);

        $this->actingAs($resident)->get(route('concerns.edit', $concern))->assertForbidden();
    }

    public function test_resident_can_reopen_a_resolved_concern_with_a_reason(): void
    {
        $resident = User::factory()->create(['role' => 'resident']);
        $concern = $this->concern(['user_id' => $resident->id, 'status' => 'resolved']);

        $this->actingAs($resident)
            ->patch(route('concerns.decision', $concern), [
                'decision' => 'reopen',
                'reason' => 'The same disturbance continued again after the reported resolution.',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('concerns', ['id' => $concern->id, 'status' => 'reopened', 'reopen_count' => 1]);
        $this->assertDatabaseHas('concern_status_histories', ['concern_id' => $concern->id, 'to_status' => 'reopened']);
    }

    private function concern(array $attributes = []): Concern
    {
        return Concern::create(array_merge([
            'concern_id' => 'CON-' . uniqid(),
            'user_id' => User::factory()->create(['role' => 'resident'])->id,
            'category' => 'noise',
            'subject' => 'Late night disturbance',
            'location' => 'Second floor hallway',
            'incident_at' => now()->subHour(),
            'details' => 'Repeated noise was recorded during quiet hours and requires review.',
            'status' => 'submitted',
            'priority' => 'medium',
            'submitted_at' => now(),
        ], $attributes));
    }
}
