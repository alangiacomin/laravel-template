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
        $response = $this->get('/admin/ruoli');

        $response->assertRedirect();
        $this->assertGuest();
    }

    public function test_authenticated_user_can_view_roles_list(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::ADMIN->value);

        $response = $this->actingAs($user)->get('/admin/ruoli');

        $response->assertOk();
    }

    public function test_user_without_role_view_permission_cannot_view_roles_list(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::USER->value);

        $this->actingAs($user)->get('/admin/ruoli')->assertForbidden();
    }

    public function test_banned_user_cannot_view_roles_list(): void
    {
        $user = User::factory()->create([
            'banned_at' => now(),
        ]);

        $response = $this->actingAs($user)->get('/admin/ruoli');

        $response->assertForbidden();
    }

    public function test_authenticated_user_can_view_a_single_role(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::ADMIN->value);
        $role = Role::where('name', RoleEnum::USER->value)->first();

        $response = $this->actingAs($user)->get("/admin/ruoli/{$role->id}");

        $response->assertOk();
    }

    public function test_user_without_permission_cannot_update_a_role(): void
    {
        $user = User::factory()->create();
        $role = Role::where('name', RoleEnum::EDITOR->value)->first();

        $response = $this->actingAs($user)->patch("/admin/ruoli/{$role->id}/aggiorna", [
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

        $response = $this->actingAs($admin)->patch("/admin/ruoli/{$role->id}/aggiorna", [
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

        $response = $this->actingAs($superAdmin)->patch("/admin/ruoli/{$role->id}/aggiorna", [
            'permissions' => [PermissionEnum::USER_READ->value => true],
        ]);

        $response->assertRedirect();
        $this->assertTrue($role->fresh()->hasPermissionTo(PermissionEnum::USER_READ->value));
    }

    public function test_update_rejects_unknown_permissions(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::ADMIN->value);
        $role = Role::where('name', RoleEnum::EDITOR->value)->first();

        $this->actingAs($admin)->patch("/admin/ruoli/{$role->id}/aggiorna", [
            'permissions' => ['permesso_inesistente' => true],
        ])->assertSessionHasErrors('permissions');
    }

    public function test_update_rejects_malformed_permissions_without_server_error(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::ADMIN->value);
        $role = Role::where('name', RoleEnum::EDITOR->value)->first();

        $this->actingAs($admin)->patch("/admin/ruoli/{$role->id}/aggiorna", [
            'permissions' => 'user_read',
        ])->assertSessionHasErrors('permissions');
    }

    public function test_non_super_admin_cannot_update_super_admin_role(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::ADMIN->value);
        $role = Role::where('name', RoleEnum::SUPER_ADMIN->value)->first();

        $this->actingAs($admin)->patch("/admin/ruoli/{$role->id}/aggiorna", [
            'permissions' => [PermissionEnum::USER_READ->value => true],
        ])->assertSessionHasErrors('permissions');

        $this->assertFalse($role->fresh()->hasPermissionTo(PermissionEnum::USER_READ->value));
    }

    public function test_non_super_admin_cannot_update_a_role_they_hold(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::ADMIN->value);
        $role = Role::where('name', RoleEnum::ADMIN->value)->first();

        $this->actingAs($admin)->patch("/admin/ruoli/{$role->id}/aggiorna", [
            'permissions' => [PermissionEnum::USER_DELETE->value => true],
        ])->assertSessionHasErrors('permissions');

        $this->assertFalse($role->fresh()->hasPermissionTo(PermissionEnum::USER_DELETE->value));
    }
}
