<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        if (!app()->environment('local', 'testing')) {
            return;
        }

        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $users = [
            ['name' => 'Admin', 'email' => 'admin@example.com'],
            ['name' => 'User', 'email' => 'user@example.com'],
            ['name' => 'Editor', 'email' => 'editor@example.com'],
        ];

        foreach ($users as $userData) {
            User::firstOrNew(['email' => $userData['email']])
                ->forceFill([
                    'name' => $userData['name'],
                    'email_verified_at' => now(),
                    'password' => Hash::make('password123'),
                    'remember_token' => Str::random(10),
                ])
                ->save();
        }
    }
}
