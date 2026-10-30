<?php

namespace App\Areas\Main\Home\Presentation\Http\Controllers;

use App\Infrastructure\Http\Controllers\Controller;
use Inertia\Response;
use Inertia\ResponseFactory;

class HomeController extends Controller
{
    public function index(): Response|ResponseFactory
    {
        return inertia('App/Home/Home');
    }
}
