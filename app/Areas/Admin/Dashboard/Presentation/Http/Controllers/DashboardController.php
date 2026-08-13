<?php

namespace App\Areas\Admin\Dashboard\Presentation\Http\Controllers;

use App\Areas\Admin\Dashboard\Domain\Repositories\IDashboardRepository;
use AlanGiacomin\LaravelCqrs\App\Presentation\Http\Controllers\Controller;
use Illuminate\Routing\Attributes\Controllers\Middleware;

class DashboardController extends Controller
{
    #[Middleware('auth')]
    #[Middleware('not_banned')]
    public function index(IDashboardRepository $dashboardRepository)
    {
        return inertia('Admin/Dashboard/Dashboard', [
            'dashboard' => $dashboardRepository->getStats(),
        ]);
    }
}
