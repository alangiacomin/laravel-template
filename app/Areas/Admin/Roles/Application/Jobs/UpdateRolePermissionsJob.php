<?php

namespace App\Areas\Admin\Roles\Application\Jobs;

use App\Models\User;
use App\Shared\Infrastructure\Enums\RoleEnum;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class UpdateRolePermissionsJob
{
    use Dispatchable;

    /**
     * @param  array<string, mixed>  $permissions
     */
    public function __construct(
        public int $id,
        public int $actorId,
        public array $permissions,
    ) {}

    /**
     * Chi non è super-admin non può modificare il ruolo super-admin né i ruoli
     * che possiede, e può cambiare solo permessi che possiede a sua volta.
     */
    public function handle(): void
    {
        $role = Role::findOrFail($this->id);
        $actor = User::findOrFail($this->actorId);

        $requested = array_keys(array_filter(
            $this->permissions,
            fn ($granted) => filter_var($granted, FILTER_VALIDATE_BOOLEAN),
        ));

        if (!$actor->hasRole(RoleEnum::SUPER_ADMIN->value)) {
            $current = $role->permissions->pluck('name')->all();
            $changed = array_merge(
                array_diff($current, $requested),
                array_diff($requested, $current),
            );
            $ownPermissions = $actor->getAllPermissions()->pluck('name')->all();

            if (
                $role->name === RoleEnum::SUPER_ADMIN->value
                || $actor->hasRole($role->name)
                || array_diff($changed, $ownPermissions) !== []
            ) {
                throw ValidationException::withMessages([
                    'permissions' => __('validation.in', ['attribute' => 'permissions']),
                ]);
            }
        }

        $role->syncPermissions($requested);
    }
}
