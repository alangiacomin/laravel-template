<?php

namespace App\Infrastructure\Middleware;

use AlanGiacomin\LaravelCqrs\Infrastructure\Routing\LocalizedRouteGenerator;
use Closure;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use Illuminate\Support\Facades\Redirect;

class LocalizedEnsureEmailIsVerified extends EnsureEmailIsVerified
{
    public function handle($request, Closure $next, $redirectToRoute = null)
    {
        if (!$request->user() || !$request->user()->hasVerifiedEmail()
        ) {
            if ($request->expectsJson()) {
                abort(403, __('error.email_not_verified'));
            }

            $routeGenerator = app(LocalizedRouteGenerator::class);

            return Redirect::guest($routeGenerator->route('verification.notice'));
        }

        return $next($request);
    }
}
