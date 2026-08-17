<?php

namespace App\Areas\Admin;

use AlanGiacomin\LaravelCqrs\App\Presentation\Http\Controllers\Controller;
use Inertia\Response;
use Inertia\ResponseFactory;

class FallbackController extends Controller
{
    public function notFound(): Response|ResponseFactory
    {
        abort(404);
    }
}
