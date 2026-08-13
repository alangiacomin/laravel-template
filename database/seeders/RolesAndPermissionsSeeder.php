<?php

namespace Database\Seeders;

use App\Shared\Infrastructure\Enums\PermissionEnum;
use App\Shared\Infrastructure\Enums\RoleEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        foreach (PermissionEnum::cases() as $permission) {
            Permission::findOrCreate($permission->value);
        }

        // update the cache to know about the newly created permissions (required if using WithoutModelEvents in seeders)
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Lista dei ruoli e permessi associati
        $roles = [
            RoleEnum::SUPER_ADMIN->value => [],
            RoleEnum::ADMIN->value => [
                PermissionEnum::ROLE_CREATE,
                PermissionEnum::ROLE_READ,
                PermissionEnum::ROLE_UPDATE,
                PermissionEnum::ROLE_DELETE,
            ],
            RoleEnum::EDITOR->value => [
                PermissionEnum::TODOS_CREATE,
                PermissionEnum::TODOS_READ,
                PermissionEnum::TODOS_UPDATE,
                PermissionEnum::TODOS_DELETE,
            ],
            RoleEnum::USER->value => [
                PermissionEnum::TODOS_READ,
                PermissionEnum::USER_READ,
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::findOrCreate($roleName);

            // Sincronizza i permessi (aggiunge quelli nuovi, rimuove quelli mancanti)
            $role->syncPermissions($rolePermissions);
        }
    }
}
