<?php

namespace App\Areas\Admin\Dashboard\Presentation\Http\Controllers;

use App\Areas\Admin\Dashboard\Application\Queries\GetDashboardStatsQuery;
use App\Infrastructure\Http\Controllers\Controller;
use Carbon\Carbon;
use Inertia\Response;
use Inertia\ResponseFactory;

class DashboardController extends Controller
{
    public function index(GetDashboardStatsQuery $query): Response|ResponseFactory
    {
        return inertia('Admin/Dashboard/Dashboard', [
            'dashboard' => $query->handle(Carbon::now()),
        ]);
    }
}
