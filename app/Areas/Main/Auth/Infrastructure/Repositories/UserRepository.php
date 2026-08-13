<?php

namespace App\Areas\Main\Auth\Infrastructure\Repositories;

use App\Areas\Main\Auth\Domain\Entities\UserItem;
use App\Areas\Main\Auth\Domain\Repositories\IUserRepository;
use App\Areas\Main\Auth\Infrastructure\Mappers\UserItemMapper;
use App\Models\User;
use Illuminate\Support\Collection;

class UserRepository implements IUserRepository
{
    public function getRoles(int $id): Collection
    {
        return User::findOrFail($id)->getRoleNames();
    }

    public function all(): Collection
    {
        return User::all()->map(fn (User $u) => UserItemMapper::toDomain($u));
    }

    public function register(UserItem $user): UserItem
    {
        $model = User::create(
            [
                'name' => $user->name,
                'email' => $user->email,
                'password' => $user->password,
            ]
        );

        return UserItemMapper::toDomain($model);
    }

    public function allWithRoles(): Collection
    {
        return User::with('roles')->get()->map(fn (User $u) => [
            'user' => UserItemMapper::toDomain($u),
            'roles' => $u->getRoleNames()->toArray(),
        ]);
    }

    public function get(int $id): UserItem
    {
        return UserItemMapper::toDomain(User::findOrFail($id));
    }

    public function update(int $userId, array $data): UserItem
    {
        $user = User::findOrFail($userId);
        $user->update($data);

        return UserItemMapper::toDomain($user);
    }

    public function assignRoles(int $userId, array $roles): void
    {
        User::findOrFail($userId)->syncRoles($roles);
    }
}
