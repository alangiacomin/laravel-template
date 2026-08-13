<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Shared\Infrastructure\Enums\RoleEnum;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_guest_cannot_view_users_list(): void
    {
        $response = $this->get('/admin/users');

        $response->assertRedirect();
        $this->assertGuest();
    }

    public function test_authenticated_user_can_view_users_list(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/users');

        $response->assertOk();
    }

    public function test_banned_user_cannot_view_users_list(): void
    {
        $user = User::factory()->create([
            'banned_at' => now(),
        ]);

        $response = $this->actingAs($user)->get('/admin/users');

        $response->assertForbidden();
    }

    public function test_authenticated_user_can_view_a_single_user(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $response = $this->actingAs($user)->get("/admin/users/{$other->id}");

        $response->assertOk();
    }

    public function test_user_without_permission_cannot_update_a_user(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create(['name' => 'Old Name']);

        $response = $this->actingAs($user)->patch("/admin/users/{$other->id}/update", [
            'name' => 'New Name',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseHas('users', [
            'id' => $other->id,
            'name' => 'Old Name',
        ]);
    }

    public function test_super_admin_can_update_a_user(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::SUPER_ADMIN->value);

        $other = User::factory()->create(['name' => 'Old Name']);

        $response = $this->actingAs($admin)->patch("/admin/users/{$other->id}/update", [
            'name' => 'New Name',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('users', [
            'id' => $other->id,
            'name' => 'New Name',
        ]);
    }

    public function test_any_authenticated_user_can_blocca_a_user_due_to_missing_gate_enforcement(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $response = $this->actingAs($user)->patch("/admin/users/{$other->id}/blocca");

        $response->assertRedirect();
        $this->assertNotNull($other->refresh()->banned_at);
    }

    public function test_super_admin_can_blocca_a_user(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::SUPER_ADMIN->value);

        $other = User::factory()->create();

        $response = $this->actingAs($admin)->patch("/admin/users/{$other->id}/blocca");

        $response->assertRedirect();
        $this->assertNotNull($other->refresh()->banned_at);
    }

    public function test_super_admin_can_sblocca_a_user(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::SUPER_ADMIN->value);

        $other = User::factory()->create([
            'banned_at' => now(),
        ]);

        $response = $this->actingAs($admin)->patch("/admin/users/{$other->id}/sblocca");

        $response->assertRedirect();
        $this->assertNull($other->refresh()->banned_at);
    }
}
