<?php

namespace App\Areas\Main\ExamplePage\Presentation\Http\Controllers;

use AlanGiacomin\LaravelCqrs\App\Presentation\Http\Controllers\Controller;
use Inertia\Response;
use Inertia\ResponseFactory;

class ExamplePageController extends Controller
{
    public function index(): Response|ResponseFactory
    {
        return inertia('App/ExamplePage/ExamplePage');
    }
}
