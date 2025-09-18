<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Create manager
        $manager = User::firstOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'Manager One',
                'password' => Hash::make('password'),
            ]
        );
        $manager->assignRole('manager');

        // Create users
        $user = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'User One',
                'password' => Hash::make('password'),
            ]
        );
        $user->assignRole('user');

        // Create additional users for testing
        $user2 = User::firstOrCreate(
            ['email' => 'user2@example.com'],
            [
                'name' => 'User Two',
                'password' => Hash::make('password'),
            ]
        );
        $user2->assignRole('user');
    }
}