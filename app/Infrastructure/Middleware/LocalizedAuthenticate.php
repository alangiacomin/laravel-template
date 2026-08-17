<?php

namespace App\Infrastructure\Middleware;

use AlanGiacomin\LaravelCqrs\Infrastructure\Routing\LocalizedRouteGenerator;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\Request;

class LocalizedAuthenticate extends Authenticate
{
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        $routeGenerator = app(LocalizedRouteGenerator::class);

        return $routeGenerator->route('login');
    }
}
