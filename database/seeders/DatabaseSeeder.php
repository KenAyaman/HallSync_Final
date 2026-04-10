<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@hallsync.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => 'manager',
            ]
        );

        User::updateOrCreate(
            ['email' => 'handyman@hallsync.com'],
            [
                'name' => 'Handyman',
                'password' => Hash::make('password'),
                'role' => 'handyman',
            ]
        );

        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Resident',
                'password' => Hash::make('password'),
                'role' => 'resident',
            ]
        );
    }
}