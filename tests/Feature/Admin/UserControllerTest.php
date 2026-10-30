<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Shared\Infrastructure\Enums\PermissionEnum;
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
        $response = $this->get(route('admin.users'));

        $response->assertRedirect();
        $this->assertGuest();
    }

    public function test_authenticated_user_can_view_users_list(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::SUPER_ADMIN->value);

        $response = $this->actingAs($user)->get(route('admin.users'));

        $response->assertOk();
    }

    public function test_user_without_user_view_permission_cannot_view_users_list(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::USER->value);

        $this->actingAs($user)->get(route('admin.users'))->assertForbidden();
    }

    public function test_banned_user_cannot_view_users_list(): void
    {
        $user = User::factory()->create([
            'banned_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('admin.users'));

        $response->assertForbidden();
    }

    public function test_authenticated_user_can_view_a_single_user(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::SUPER_ADMIN->value);
        $other = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.user.show', ['id' => $other->id]));

        $response->assertOk();
    }

    public function test_user_without_permission_cannot_update_a_user(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create(['name' => 'Old Name']);

        $response = $this->actingAs($user)->patch(route('admin.user.update', ['id' => $other->id]), [
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

        $response = $this->actingAs($admin)->patch(route('admin.user.update', ['id' => $other->id]), [
            'name' => 'New Name',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('users', [
            'id' => $other->id,
            'name' => 'New Name',
        ]);
    }

    public function test_user_without_permission_cannot_blocca_a_user(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $response = $this->actingAs($user)->patch(route('admin.user.blocca', ['id' => $other->id]));

        $response->assertForbidden();
        $this->assertNull($other->refresh()->banned_at);
    }

    public function test_super_admin_can_blocca_a_user(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::SUPER_ADMIN->value);

        $other = User::factory()->create();

        $response = $this->actingAs($admin)->patch(route('admin.user.blocca', ['id' => $other->id]));

        $response->assertRedirect();
        $this->assertNotNull($other->refresh()->banned_at);
    }

    public function test_admin_can_blocca_a_normal_user(): void
    {
        $admin = $this->userManagerAdmin();
        $other = User::factory()->create();

        $this->actingAs($admin)->patch(route('admin.user.blocca', ['id' => $other->id]))->assertRedirect();

        $this->assertNotNull($other->refresh()->banned_at);
    }

    public function test_admin_can_blocca_another_admin(): void
    {
        $admin = $this->userManagerAdmin();
        $other = User::factory()->create();
        $other->assignRole(RoleEnum::ADMIN->value);

        $this->actingAs($admin)->patch(route('admin.user.blocca', ['id' => $other->id]))->assertRedirect();

        $this->assertNotNull($other->refresh()->banned_at);
    }

    public function test_admin_cannot_blocca_themselves(): void
    {
        $admin = $this->userManagerAdmin();

        $this->actingAs($admin)->patch(route('admin.user.blocca', ['id' => $admin->id]))->assertForbidden();

        $this->assertNull($admin->refresh()->banned_at);
    }

    public function test_super_admin_cannot_blocca_themselves(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(RoleEnum::SUPER_ADMIN->value);

        $this->actingAs($superAdmin)->patch(route('admin.user.blocca', ['id' => $superAdmin->id]))->assertForbidden();

        $this->assertNull($superAdmin->refresh()->banned_at);
    }

    public function test_admin_cannot_blocca_a_super_admin(): void
    {
        $admin = $this->userManagerAdmin();
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(RoleEnum::SUPER_ADMIN->value);

        $this->actingAs($admin)->patch(route('admin.user.blocca', ['id' => $superAdmin->id]))->assertForbidden();

        $this->assertNull($superAdmin->refresh()->banned_at);
    }

    public function test_super_admin_can_sblocca_a_user(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::SUPER_ADMIN->value);

        $other = User::factory()->create([
            'banned_at' => now(),
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.user.sblocca', ['id' => $other->id]));

        $response->assertRedirect();
        $this->assertNull($other->refresh()->banned_at);
    }

    public function test_admin_can_view_users_list(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::ADMIN->value);

        $this->actingAs($admin)->get(route('admin.users'))->assertOk();
    }

    public function test_update_rejects_unknown_roles(): void
    {
        $other = User::factory()->create();

        $this->actingAs($this->userEditor())->patch(route('admin.user.update', ['id' => $other->id]), [
            'name' => 'Nome',
            'roles' => ['inesistente' => true],
        ])->assertSessionHasErrors('roles');

        $this->assertFalse($other->fresh()->hasRole('inesistente'));
    }

    public function test_update_rejects_malformed_roles_without_server_error(): void
    {
        $other = User::factory()->create();

        $this->actingAs($this->userEditor())->patch(route('admin.user.update', ['id' => $other->id]), [
            'name' => 'Nome',
            'roles' => 'admin',
        ])->assertSessionHasErrors('roles');
    }

    public function test_non_super_admin_cannot_assign_super_admin_role(): void
    {
        $other = User::factory()->create();

        $this->actingAs($this->userEditor())->patch(route('admin.user.update', ['id' => $other->id]), [
            'name' => 'Nome',
            'roles' => [RoleEnum::SUPER_ADMIN->value => true],
        ])->assertSessionHasErrors('roles');

        $this->assertFalse($other->fresh()->hasRole(RoleEnum::SUPER_ADMIN->value));
    }

    public function test_non_super_admin_cannot_assign_admin_role(): void
    {
        $other = User::factory()->create();

        $this->actingAs($this->userEditor())->patch(route('admin.user.update', ['id' => $other->id]), [
            'name' => 'Nome',
            'roles' => [RoleEnum::ADMIN->value => true],
        ])->assertSessionHasErrors('roles');

        $this->assertFalse($other->fresh()->hasRole(RoleEnum::ADMIN->value));
    }

    public function test_non_super_admin_cannot_remove_admin_role_from_another_user(): void
    {
        $other = User::factory()->create();
        $other->assignRole(RoleEnum::ADMIN->value);

        $this->actingAs($this->userEditor())->patch(route('admin.user.update', ['id' => $other->id]), [
            'name' => 'Nome',
            'roles' => [RoleEnum::ADMIN->value => false],
        ])->assertSessionHasErrors('roles');

        $this->assertTrue($other->fresh()->hasRole(RoleEnum::ADMIN->value));
    }

    public function test_non_super_admin_cannot_change_own_roles(): void
    {
        $actor = $this->userEditor();

        $this->actingAs($actor)->patch(route('admin.user.update', ['id' => $actor->id]), [
            'name' => 'Nome',
            'roles' => [RoleEnum::EDITOR->value => true],
        ])->assertSessionHasErrors('roles');

        $this->assertFalse($actor->fresh()->hasRole(RoleEnum::EDITOR->value));
    }

    public function test_non_super_admin_can_assign_non_privileged_role_to_another_user(): void
    {
        $other = User::factory()->create();

        $this->actingAs($this->userEditor())->patch(route('admin.user.update', ['id' => $other->id]), [
            'name' => 'Nome',
            'roles' => [RoleEnum::EDITOR->value => true],
        ])->assertSessionHasNoErrors();

        $this->assertTrue($other->fresh()->hasRole(RoleEnum::EDITOR->value));
    }

    public function test_super_admin_can_assign_admin_role(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(RoleEnum::SUPER_ADMIN->value);
        $other = User::factory()->create();

        $this->actingAs($superAdmin)->patch(route('admin.user.update', ['id' => $other->id]), [
            'name' => 'Nome',
            'roles' => [RoleEnum::ADMIN->value => true],
        ])->assertSessionHasNoErrors();

        $this->assertTrue($other->fresh()->hasRole(RoleEnum::ADMIN->value));
    }

    /**
     * Admin con i permessi richiesti dal gate user_manage.
     */
    private function userManagerAdmin(): User
    {
        $admin = User::factory()->create();
        $admin->assignRole(RoleEnum::ADMIN->value);
        $admin->givePermissionTo(
            PermissionEnum::USER_CREATE->value,
            PermissionEnum::USER_READ->value,
            PermissionEnum::USER_UPDATE->value,
            PermissionEnum::USER_DELETE->value,
        );

        return $admin;
    }

    /**
     * Utente con user_update ma senza il ruolo super-admin.
     */
    private function userEditor(): User
    {
        $actor = User::factory()->create();
        $actor->givePermissionTo(PermissionEnum::USER_READ->value, PermissionEnum::USER_UPDATE->value);

        return $actor;
    }
}
