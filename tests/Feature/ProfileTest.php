<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create([
            'role' => 'resident',
            'resident_number' => 'RES-PROFILE-1',
            'room_number' => 'B-204',
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response
            ->assertOk()
            ->assertSee('Official Account Details')
            ->assertSee('RES-PROFILE-1')
            ->assertSee('B-204')
            ->assertDontSee('name="name"', false)
            ->assertDontSee('name="email"', false);
    }

    public function test_official_account_details_cannot_be_changed_from_the_profile(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response->assertSessionHasErrors(['name', 'email']);

        $user->refresh();

        $this->assertNotSame('Test User', $user->name);
        $this->assertNotSame('test@example.com', $user->email);
    }

    public function test_accounts_cannot_be_deleted_from_the_self_service_profile(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->delete('/profile', ['password' => 'password'])
            ->assertMethodNotAllowed();

        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }
}
