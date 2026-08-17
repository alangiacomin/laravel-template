<?php

namespace Database\Seeders;

use App\Models\User;
use App\Shared\Infrastructure\Enums\RoleEnum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(
            [
                RolesAndPermissionsSeeder::class,
                UsersSeeder::class,
            ]
        );

        User::where('email', 'admin@example.com')->first()->assignRole(RoleEnum::SUPER_ADMIN);
        User::where('email', 'editor@example.com')->first()->assignRole(RoleEnum::EDITOR, RoleEnum::USER);
        User::where('email', 'user@example.com')->first()->assignRole(RoleEnum::USER);
        // User::where('email', 'user@example.com')->first()->assignRole(RoleEnum::SUPER_ADMIN);
    }
}
