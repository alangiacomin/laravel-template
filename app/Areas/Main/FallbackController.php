<?php

namespace App\Areas\Main;

use AlanGiacomin\LaravelCqrs\App\Presentation\Http\Controllers\Controller;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Inertia\Response;
use Inertia\ResponseFactory;

class FallbackController extends Controller
{
    #[Middleware('not_banned')]
    public function app(): Response|ResponseFactory
    {
        return inertia('App/Home/Home');
    }

    public function notFound(): Response|ResponseFactory
    {
        abort(404);
    }
}
