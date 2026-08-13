<?php

namespace App\Areas\Admin\Roles\Infrastructure\Repositories;

use App\Areas\Admin\Roles\Domain\Entities\Role;
use App\Areas\Admin\Roles\Domain\Repositories\IRoleRepository;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role as SpatieRole;

class RoleRepository implements IRoleRepository
{
    public function all(): Collection
    {
        return SpatieRole::all()->map(fn (SpatieRole $role) => Role::fromModel($role));
    }

    public function get(int $id): ?Role
    {
        $role = SpatieRole::find($id);

        return $role ? Role::fromModel($role) : null;
    }

    public function updatePermissions(int $id, array $permissions): void
    {
        $role = SpatieRole::findOrFail($id);
        $role->syncPermissions($permissions);
    }
}
