<?php

namespace App\Areas\Admin\Dashboard\Presentation\Http\Controllers;

use App\Areas\Admin\Dashboard\Application\Queries\GetDashboardStatsQuery;
use App\Infrastructure\Http\Controllers\Controller;
use App\Shared\Infrastructure\Enums\GateEnum;
use Carbon\Carbon;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Inertia\Response;
use Inertia\ResponseFactory;

class DashboardController extends Controller
{
    #[Middleware('auth')]
    #[Middleware('verified')]
    #[Middleware('can:'.GateEnum::ADMIN_ACCESS->value)]
    public function index(GetDashboardStatsQuery $query): Response|ResponseFactory
    {
        return inertia('Admin/Dashboard/Dashboard', [
            'dashboard' => $query->handle(Carbon::now()),
        ]);
    }
}
