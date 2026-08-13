<?php

use AlanGiacomin\LaravelCqrs\App\Infrastructure\Middleware\ApplyGateAttributes;
use App\Infrastructure\Middleware\EnsureUserIsNotBanned;
use App\Infrastructure\Middleware\HandleInertiaRequests;
use App\Infrastructure\Middleware\LocalizedAuthenticate;
use App\Infrastructure\Middleware\LocalizedEnsureEmailIsVerified;
use App\Infrastructure\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

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
                SetLocale::class,
                HandleInertiaRequests::class,
                ApplyGateAttributes::class,
            ],
            prepend: [
            ]);
        // $middleware->preventRequestForgery(except: [
        //     'todos',
        // ]);

        $middleware->alias([
            'auth' => LocalizedAuthenticate::class,
            'verified' => LocalizedEnsureEmailIsVerified::class,
            'not_banned' => EnsureUserIsNotBanned::class,
        ]);
    })->withEvents(discover: [
        __DIR__.'/../app/Main/*/Domain/Listeners',
    ])
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(function (Throwable $e, $request) {
            // dd($e->getMessage());

            // Lascia che Inertia gestisca ValidationException correttamente
            if ($e instanceof ValidationException) {
                return null;
            }

            $status = method_exists($e, 'getStatusCode')
                ? $e->getStatusCode()
                : $e->getCode();

            if ($request->inertia()) {
                if (!$request->isMethod('GET')) {
                    return back()->with('error', $e->getMessage());
                }
                // dd('son qua');
            }
            // dd($status);

            if (in_array($status, [403, 404, 500, 503])) {
                $shared = app(HandleInertiaRequests::class)->share($request);
                $props = array_merge($shared, [
                    'status' => $status,
                    'reason' => $e->getMessage(),
                ]);
                $isAdmin = $request->routeIs('admin.*') || $request->routeIs('localized.admin.*') || str_starts_with($request->path(), 'admin');

                // dd("isAdmin: $isAdmin", $request->route()->getName(), $props);
                // dd($request->user(), Auth::user(), $request->hasSession(), $props);

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
