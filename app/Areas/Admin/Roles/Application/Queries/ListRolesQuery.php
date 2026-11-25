<?php

namespace App\Areas\Admin\Roles\Application\Queries;

use App\Areas\Admin\Roles\Application\Data\Role;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role as SpatieRole;

class ListRolesQuery
{
    /**
     * @return Collection<int, Role>
     */
    public function handle(): Collection
    {
        return SpatieRole::with('permissions')
            ->get()
            ->map(fn (SpatieRole $role) => Role::fromModel($role));
    }
}
