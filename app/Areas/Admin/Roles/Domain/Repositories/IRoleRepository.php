<?php

namespace App\Areas\Admin\Roles\Domain\Repositories;

use App\Areas\Admin\Roles\Domain\Entities\Role;
use AlanGiacomin\LaravelCqrs\App\Domain\Repositories\IRepository;
use Illuminate\Support\Collection;

interface IRoleRepository extends IRepository
{
    public function all(): Collection;

    public function get(int $id): ?Role;

    public function updatePermissions(int $id, array $permissions): void;
}
