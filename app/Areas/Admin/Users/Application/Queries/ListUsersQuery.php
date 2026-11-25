<?php

namespace App\Areas\Admin\Users\Application\Queries;

use App\Areas\Admin\Users\Application\Data\AdminUserData;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class ListUsersQuery
{
    /**
     * @return LengthAwarePaginator<int, AdminUserData>
     */
    public function handle(int $perPage = 15): LengthAwarePaginator
    {
        return User::with('roles')
            ->orderBy('id')
            ->paginate($perPage)
            ->through(fn (User $user) => AdminUserData::fromModel($user));
    }
}
