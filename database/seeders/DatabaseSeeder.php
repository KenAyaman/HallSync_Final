<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ResidentRosterEntry;
use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@hallsync.com'],
            [
                'name' => 'Admin',
                'password' => 'password',
                'role' => 'manager',
            ]
        );

        ResidentRosterEntry::updateOrCreate(
            ['resident_number' => 'RES-DEMO-001'],
            [
                'name' => 'Demo Incoming Resident',
                'email' => 'incoming.resident@example.com',
                'room_number' => 'A-101',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'handyman@hallsync.com'],
            [
                'name' => 'Handyman',
                'password' => 'password',
                'role' => 'handyman',
            ]
        );

        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Resident',
                'password' => 'password',
                'role' => 'resident',
            ]
        );
    }
}
