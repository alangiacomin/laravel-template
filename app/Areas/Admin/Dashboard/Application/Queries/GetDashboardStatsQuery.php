<?php

namespace App\Areas\Admin\Dashboard\Application\Queries;

use App\Areas\Admin\Dashboard\Application\Data\DashboardStats;
use App\Models\User;
use Carbon\CarbonInterface;

class GetDashboardStatsQuery
{
    public function handle(CarbonInterface $now): DashboardStats
    {
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();
        $startOfLastMonth = $now->copy()->subMonthNoOverflow()->startOfMonth();
        $endOfLastMonth = $now->copy()->subMonthNoOverflow()->endOfMonth();

        $row = User::query()
            ->selectRaw(
                'COUNT(*) as total, '
                .'COALESCE(SUM(CASE WHEN email_verified_at IS NULL THEN 1 ELSE 0 END), 0) as total_unverified, '
                .'COALESCE(SUM(CASE WHEN created_at BETWEEN ? AND ? THEN 1 ELSE 0 END), 0) as month, '
                .'COALESCE(SUM(CASE WHEN created_at BETWEEN ? AND ? AND email_verified_at IS NULL THEN 1 ELSE 0 END), 0) as month_unverified, '
                .'COALESCE(SUM(CASE WHEN created_at BETWEEN ? AND ? THEN 1 ELSE 0 END), 0) as last_month, '
                .'COALESCE(SUM(CASE WHEN created_at BETWEEN ? AND ? AND email_verified_at IS NULL THEN 1 ELSE 0 END), 0) as last_month_unverified',
                [
                    $startOfMonth, $endOfMonth,
                    $startOfMonth, $endOfMonth,
                    $startOfLastMonth, $endOfLastMonth,
                    $startOfLastMonth, $endOfLastMonth,
                ]
            )
            ->toBase()
            ->first();

        return new DashboardStats(
            users_total_count: (int) $row->total,
            users_total_count_unverified: (int) $row->total_unverified,
            users_month_count: (int) $row->month,
            users_month_count_unverified: (int) $row->month_unverified,
            users_last_month_count: (int) $row->last_month,
            users_last_month_count_unverified: (int) $row->last_month_unverified,
        );
    }
}
