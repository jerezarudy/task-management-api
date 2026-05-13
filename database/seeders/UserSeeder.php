<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed team members for task assignment.
     */
    public function run(): void
    {
        $users = [
            ['name' => 'Mara Santos', 'email' => 'mara.santos@example.com'],
            ['name' => 'Leo Reyes', 'email' => 'leo.reyes@example.com'],
            ['name' => 'Nina Cruz', 'email' => 'nina.cruz@example.com'],
            ['name' => 'Paolo Garcia', 'email' => 'paolo.garcia@example.com'],
            ['name' => 'Test User', 'email' => 'test@example.com'],
        ];

        foreach ($users as $user) {
            User::query()->updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'email_verified_at' => now(),
                    'password' => Hash::make('password'),
                ],
            );
        }
    }
}
