<?php

namespace App\Areas\Main\ExamplePage\Presentation\Http\Controllers;

use App\Infrastructure\Http\Controllers\Controller;
use Inertia\Response;
use Inertia\ResponseFactory;

class ExamplePageController extends Controller
{
    public function index(): Response|ResponseFactory
    {
        return inertia('App/ExamplePage/ExamplePage');
    }
}
