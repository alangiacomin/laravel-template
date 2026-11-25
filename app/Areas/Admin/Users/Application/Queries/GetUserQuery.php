<?php

namespace App\Areas\Admin\Users\Application\Queries;

use App\Areas\Admin\Users\Application\Data\AdminUserData;
use App\Models\User;

class GetUserQuery
{
    public function handle(int $id): AdminUserData
    {
        return AdminUserData::fromModel(User::with('roles')->findOrFail($id));
    }
}
