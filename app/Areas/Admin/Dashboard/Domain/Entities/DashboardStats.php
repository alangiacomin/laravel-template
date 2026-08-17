<?php

namespace App\Areas\Admin\Dashboard\Domain\Entities;

use Spatie\LaravelData\Data;

class DashboardStats extends Data
{
    public function __construct(
        public int $users_total_count,
        public int $users_total_count_unverified,
        public int $users_month_count,
        public int $users_month_count_unverified,
        public int $users_last_month_count,
        public int $users_last_month_count_unverified,
    ) {}
}
