<?php

namespace App\Areas\Admin\Dashboard\Domain\Repositories;

use AlanGiacomin\LaravelCqrs\App\Domain\Repositories\IRepository;
use App\Areas\Admin\Dashboard\Domain\Entities\DashboardStats;

interface IDashboardRepository extends IRepository
{
    public function getStats(): DashboardStats;
}
