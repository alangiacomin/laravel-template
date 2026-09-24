<?php

namespace App\Areas\Main\Language\Presentation\Http\Controllers;

use AlanGiacomin\LaravelCqrs\App\Presentation\Http\Controllers\Controller;
use App\Infrastructure\Routing\LocalizedRoute;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LanguageController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $requestedLocale = $request->string('locale')->toString();
        $supportedLocales = config('app.locales', []);
        $fallbackLocale = config('app.fallback_locale', 'it');

        $locale = in_array($requestedLocale, $supportedLocales, true)
            ? $requestedLocale
            : $fallbackLocale;

        $request->session()->put('locale', $locale);

        $currentRoute = null;
        $referer = $request->headers->get('referer');

        if ($referer) {
            $path = parse_url($referer, PHP_URL_PATH);

            if (is_string($path)) {
                $probe = Request::create($path);

                try {
                    $currentRoute = app('router')->getRoutes()->match($probe);
                } catch (NotFoundHttpException) {
                }
            }
        }

        $currentRoute ??= $request->route();
        $logicalName = $currentRoute ? LocalizedRoute::logicalName($currentRoute) : null;

        if ($logicalName) {
            $parameters = $currentRoute->parameters();
            unset($parameters['locale']);

            $target = LocalizedRoute::url($logicalName, $parameters, $locale);
            $query = parse_url($referer ?: '', PHP_URL_QUERY);

            return redirect()->to($target.($query ? '?'.$query : ''));
        }

        return redirect()->to(LocalizedRoute::url('home', [], $locale));
    }
}
