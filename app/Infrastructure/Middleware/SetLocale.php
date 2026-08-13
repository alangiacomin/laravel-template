<?php

namespace App\Infrastructure\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $defaultLocale = config('app.locale');
        $locales = config('app.locales', [$defaultLocale]);

        $routeLocale = $request->route('locale');
        $sessionLocale = session('locale');

        $currentLocale = $sessionLocale ?? $defaultLocale;
        $this->setLocale($currentLocale);

        if ($request->method() === 'GET') {
            if ($routeLocale && !in_array($routeLocale, $locales)) {
                abort(404);
            }

            $this->setLocale($routeLocale);

            if ($request->route()) {
                $request->route()->forgetParameter('locale');
            }
        }

        return $next($request);
    }

    private function setLocale($locale): void
    {
        app()->setLocale($locale);
        Context::add('locale', $locale);
        session(['locale' => $locale]);
    }
}
