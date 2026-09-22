<?php

namespace App\Infrastructure\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromSession
{
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = config('app.locales', []);
        $routeLocale = $request->segment(1);
        $locale = is_string($routeLocale) && in_array($routeLocale, $supportedLocales, true)
            ? $routeLocale
            : $request->session()->get('locale', config('app.locale'));

        if (!in_array($locale, $supportedLocales, true)) {
            $locale = config('app.fallback_locale', 'it');
        }

        app()->setLocale($locale);
        $request->session()->put('locale', $locale);
        URL::defaults(['locale' => $locale]);

        return $next($request);
    }
}
