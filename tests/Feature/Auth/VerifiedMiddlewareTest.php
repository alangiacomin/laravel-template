<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Shared\Infrastructure\Enums\RoleEnum;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VerifiedMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_unverified_user_is_redirected_from_profile(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)->get(route('user.show'))
            ->assertRedirect(route('verification.notice'));
    }

    public function test_unverified_user_cannot_update_profile(): void
    {
        $user = User::factory()->unverified()->create(['name' => 'Old Name']);

        $this->actingAs($user)->patch(route('user.update'), ['name' => 'New Name'])
            ->assertRedirect(route('verification.notice'));

        $this->assertSame('Old Name', $user->fresh()->name);
    }

    public function test_verified_user_can_view_profile(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('user.show'))->assertOk();
    }

    public function test_unverified_admin_is_redirected_from_admin_area(): void
    {
        $admin = User::factory()->unverified()->create();
        $admin->assignRole(RoleEnum::ADMIN);

        foreach (['admin.dashboard', 'admin.users', 'admin.roles'] as $routeName) {
            $this->actingAs($admin)->get(route($routeName))
                ->assertRedirect(route('verification.notice'));
        }
    }

    public function test_verified_admin_can_view_admin_area(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::ADMIN);

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
    }
}
