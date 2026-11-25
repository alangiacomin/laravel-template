<?php

namespace App\Areas\Admin\Roles\Application\Queries;

use App\Areas\Admin\Roles\Application\Data\Role;
use Spatie\Permission\Models\Role as SpatieRole;

class GetRoleQuery
{
    public function handle(int $id): Role
    {
        return Role::fromModel(SpatieRole::with('permissions')->findOrFail($id));
    }
}
