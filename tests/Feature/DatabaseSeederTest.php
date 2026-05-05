<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_creates_demo_accounts(): void
    {
        $this->seed();

        $this->assertDatabaseHas('users', [
            'email' => 'admin@hallsync.com',
            'role' => 'manager',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'handyman@hallsync.com',
            'role' => 'handyman',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'role' => 'resident',
        ]);
    }
}
