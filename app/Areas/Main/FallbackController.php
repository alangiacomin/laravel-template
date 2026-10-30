<?php

namespace App\Areas\Main;

use App\Infrastructure\Http\Controllers\Controller;
use Inertia\Response;
use Inertia\ResponseFactory;

class FallbackController extends Controller
{
    public function app(): Response|ResponseFactory
    {
        return inertia('App/Home/Home');
    }

    public function notFound(): Response|ResponseFactory
    {
        abort(404);
    }
}
