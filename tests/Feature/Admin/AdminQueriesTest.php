<?php

namespace Tests\Feature\Admin;

use App\Areas\Admin\Roles\Application\Queries\GetRoleQuery;
use App\Areas\Admin\Roles\Application\Queries\ListRolesQuery;
use App\Areas\Admin\Users\Application\Queries\GetUserQuery;
use App\Areas\Admin\Users\Application\Queries\ListUsersQuery;
use App\Models\User;
use App\Shared\Infrastructure\Enums\RoleEnum;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminQueriesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_list_users_is_ordered_by_id_and_paginated(): void
    {
        User::factory()->count(5)->create();

        $page = app(ListUsersQuery::class)->handle(2);

        $this->assertSame(5, $page->total());
        $this->assertCount(2, $page->items());
        $this->assertSame([1, 2], collect($page->items())->pluck('id')->all());
    }

    public function test_get_user_includes_roles(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::ADMIN->value);

        $data = app(GetUserQuery::class)->handle($user->id);

        $this->assertSame($user->id, $data->id);
        $this->assertSame([RoleEnum::ADMIN->value], $data->roles);
    }

    public function test_get_user_fails_for_missing_id(): void
    {
        $this->expectException(ModelNotFoundException::class);

        app(GetUserQuery::class)->handle(999999);
    }

    public function test_list_roles_includes_permissions(): void
    {
        $roles = app(ListRolesQuery::class)->handle();

        $this->assertCount(Role::count(), $roles);
        $admin = $roles->firstWhere('name', RoleEnum::ADMIN->value);
        $this->assertNotEmpty($admin->permissions);
    }

    public function test_get_role_fails_for_missing_id(): void
    {
        $this->expectException(ModelNotFoundException::class);

        app(GetRoleQuery::class)->handle(999999);
    }
}
