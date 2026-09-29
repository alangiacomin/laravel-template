<?php

namespace App\Areas\Admin\Dashboard\Presentation\Http\Controllers;

use AlanGiacomin\LaravelCqrs\App\Presentation\Http\Controllers\Controller;
use App\Areas\Admin\Dashboard\Domain\Entities\DashboardStats;
use App\Areas\Main\Fumetti\Infrastructure\Persistence\Eloquent\Models\Albo;
use App\Areas\Main\Fumetti\Infrastructure\Persistence\Eloquent\Models\Pubblicazioni;
use App\Areas\Main\Fumetti\Infrastructure\Persistence\Eloquent\Models\Serie;
use App\Areas\Main\Fumetti\Infrastructure\Persistence\Eloquent\Models\Testata;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Routing\Attributes\Controllers\Middleware;

class DashboardController extends Controller
{
    #[Middleware('auth')]
    #[Middleware('not_banned')]
    public function index()
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();
        $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfLastMonth = $now->copy()->subMonth()->endOfMonth();

        return inertia('Admin/Dashboard/Dashboard', [
            'dashboard' => new DashboardStats(
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
                comic_titles_count: Testata::count(),
                comic_series_count: Serie::count(),
                comic_issues_count: Albo::count(),
                comic_issues_without_series_count: Albo::doesntHave('pubblicazioni')->count(),
                comic_publications_count: Pubblicazioni::count(),
            ),
        ]);
    }
}
