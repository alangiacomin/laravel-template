<?php

namespace App\Areas\Main\Auth\Domain\Repositories;

use App\Areas\Main\Auth\Domain\Entities\UserItem;
use AlanGiacomin\LaravelCqrs\App\Domain\Repositories\IRepository;
use Illuminate\Support\Collection;

interface IUserRepository extends IRepository
{
    public function register(UserItem $user): UserItem;

    public function get(int $id): UserItem;

    public function getRoles(int $id): Collection;

    public function all(): Collection;

    public function update(int $userId, array $data): UserItem;

    public function assignRoles(int $userId, array $roles): void;
}
