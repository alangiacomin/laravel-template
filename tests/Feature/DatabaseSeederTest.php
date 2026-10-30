<?php

namespace Tests\Feature;

use App\Models\User;
use App\Shared\Infrastructure\Enums\RoleEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_demo_users_in_testing_environment(): void
    {
        $this->seed();

        $admin = User::where('email', 'admin@example.com')->first();

        $this->assertNotNull($admin);
        $this->assertTrue($admin->hasRole(RoleEnum::SUPER_ADMIN));
        $this->assertNotNull($admin->email_verified_at);
        $this->assertNotNull($admin->remember_token);
    }

    public function test_banned_at_is_not_mass_assignable(): void
    {
        $user = User::factory()->create();

        $attributes = ['banned_at' => now()];

        $user->fill($attributes)->save();

        $this->assertNull($user->refresh()->banned_at);
    }

    public function test_seeder_does_not_create_demo_users_in_production_environment(): void
    {
        app()->detectEnvironment(fn () => 'production');

        $this->artisan('db:seed', ['--force' => true])->assertSuccessful();

        $this->assertSame(0, User::count());
        $this->assertGreaterThan(0, Role::count());
    }
}
