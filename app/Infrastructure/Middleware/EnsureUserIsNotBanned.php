<?php

namespace App\Infrastructure\Middleware;

use App\Infrastructure\Session\SessionPreserver;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class EnsureUserIsNotBanned
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->isBanned()) {
            auth()->logout();
            app(SessionPreserver::class)->invalidate($request);

            throw new AccessDeniedHttpException(__('error.account_banned'));
        }

        return $next($request);
    }
}
