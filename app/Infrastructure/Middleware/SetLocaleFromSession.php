<?php

namespace App\Infrastructure\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromSession
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale', config('app.locale'));
        $supportedLocales = config('app.locales', []);

        if (!in_array($locale, $supportedLocales, true)) {
            $locale = config('app.fallback_locale', 'it');
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
