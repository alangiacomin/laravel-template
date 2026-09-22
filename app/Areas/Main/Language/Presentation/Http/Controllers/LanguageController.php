<?php

namespace App\Areas\Main\Language\Presentation\Http\Controllers;

use AlanGiacomin\LaravelCqrs\App\Presentation\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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

        return back();
    }
}
