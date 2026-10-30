<?php

use App\Infrastructure\Middleware\EnsureUserIsNotBanned;
use App\Infrastructure\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(
            append: [
                EnsureUserIsNotBanned::class,
                HandleInertiaRequests::class,
            ],
        );

        $middleware->redirectGuestsTo(fn () => route('login'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(function (HttpException $e, $request) {
            $status = $e->getStatusCode();

            if ($request->inertia() && !$request->isMethod('GET')) {
                return back()->with('error', $e->getMessage());
            }

            if (in_array($status, [403, 404, 500, 503])) {
                $reason = $status >= 500 && !config('app.debug') ? '' : $e->getMessage();
                $shared = app(HandleInertiaRequests::class)->share($request);
                $props = array_merge($shared, [
                    'status' => $status,
                    'reason' => $reason,
                ]);
                $isAdmin = $request->routeIs('admin.*') || str_starts_with($request->path(), 'admin');

                return Inertia::render(
                    $isAdmin
                        ? 'Admin/ErrorPage/ErrorPage'
                        : 'App/ErrorPage/ErrorPage',
                    $props)
                    ->toResponse($request)->setStatusCode($status);
            }

            return null;
        });
    })
    ->create();
