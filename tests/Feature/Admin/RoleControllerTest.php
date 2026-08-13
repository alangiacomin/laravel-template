<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Shared\Infrastructure\Enums\PermissionEnum;
use App\Shared\Infrastructure\Enums\RoleEnum;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_guest_cannot_view_roles_list(): void
    {
        $response = $this->get('/admin/roles');

        $response->assertRedirect();
        $this->assertGuest();
    }

    public function test_authenticated_user_can_view_roles_list(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/roles');

        $response->assertOk();
    }

    public function test_banned_user_cannot_view_roles_list(): void
    {
        $user = User::factory()->create([
            'banned_at' => now(),
        ]);

        $response = $this->actingAs($user)->get('/admin/roles');

        $response->assertForbidden();
    }

    public function test_authenticated_user_can_view_a_single_role(): void
    {
        $user = User::factory()->create();
        $role = Role::where('name', RoleEnum::USER->value)->first();

        $response = $this->actingAs($user)->get("/admin/roles/{$role->id}");

        $response->assertOk();
    }

    public function test_user_without_permission_cannot_update_a_role(): void
    {
        $user = User::factory()->create();
        $role = Role::where('name', RoleEnum::EDITOR->value)->first();

        $response = $this->actingAs($user)->patch("/admin/roles/{$role->id}/update", [
            'permissions' => [PermissionEnum::TODOS_DELETE->value => true],
        ]);

        $response->assertForbidden();
        // The role permissions must remain untouched (a successful update would have
        // removed the other permissions via syncPermissions).
        $this->assertTrue($role->fresh()->hasPermissionTo(PermissionEnum::TODOS_CREATE->value));
    }

    public function test_admin_can_update_a_role(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::ADMIN->value);

        $role = Role::where('name', RoleEnum::EDITOR->value)->first();

        $response = $this->actingAs($admin)->patch("/admin/roles/{$role->id}/update", [
            'permissions' => [
                PermissionEnum::TODOS_CREATE->value => true,
                PermissionEnum::TODOS_READ->value => true,
            ],
        ]);

        $response->assertRedirect();

        $role->refresh();
        $this->assertTrue($role->hasPermissionTo(PermissionEnum::TODOS_CREATE->value));
        $this->assertTrue($role->hasPermissionTo(PermissionEnum::TODOS_READ->value));
        $this->assertFalse($role->hasPermissionTo(PermissionEnum::TODOS_UPDATE->value));
    }

    public function test_super_admin_can_update_a_role(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(RoleEnum::SUPER_ADMIN->value);

        $role = Role::where('name', RoleEnum::USER->value)->first();

        $response = $this->actingAs($superAdmin)->patch("/admin/roles/{$role->id}/update", [
            'permissions' => [PermissionEnum::USER_READ->value => true],
        ]);

        $response->assertRedirect();
        $this->assertTrue($role->fresh()->hasPermissionTo(PermissionEnum::USER_READ->value));
    }
}
