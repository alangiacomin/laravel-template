<?php

namespace App\Areas\Admin\Dashboard\Infrastructure\Repositories;

use App\Areas\Admin\Dashboard\Domain\Entities\DashboardStats;
use App\Areas\Admin\Dashboard\Domain\Repositories\IDashboardRepository;
use App\Models\User;
use Carbon\Carbon;

class DashboardRepository implements IDashboardRepository
{
    public function getStats(): DashboardStats
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();
        $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfLastMonth = $now->copy()->subMonth()->endOfMonth();

        return new DashboardStats(
            users_total_count: User::count(),
            users_total_count_unverified: User::whereNull('email_verified_at')->count(),
            users_month_count: User::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count(),
            users_month_count_unverified: User::whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->whereNull('email_verified_at')
                ->count(),
            users_last_month_count: User::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count(),
            users_last_month_count_unverified: User::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
                ->whereNull('email_verified_at')
                ->count(),
        );
    }
}
