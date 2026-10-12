<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Shared\Infrastructure\Enums\RoleEnum;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_guest_cannot_view_admin_dashboard(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect();
        $this->assertGuest();
    }

    public function test_authenticated_user_can_view_admin_dashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::EDITOR->value);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertOk();
    }

    public function test_user_without_admin_access_cannot_view_admin_dashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::USER->value);

        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    public function test_banned_user_cannot_view_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'banned_at' => now(),
        ]);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertForbidden();
    }
}
