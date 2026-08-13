<?php

namespace App\Infrastructure\Middleware;

use App\Areas\Main\Auth\Application\Data\UserData;
use App\Areas\Main\Auth\Application\Inertia\AbilityResolver;
use App\Areas\Main\Auth\Infrastructure\Mappers\UserItemMapper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = Auth::user();
        $userData = $user ? UserData::from(UserItemMapper::toDomain($user)) : null;

        $locale = app()->getLocale();
        $defaultLocale = config('app.fallback_locale');

        return [
            ...parent::share($request),
            //
            'locales' => config('app.locales'),
            'locale' => $locale,
            'defaultLocale' => $defaultLocale,
            'translations' => $this->getTranslations($locale),
            'auth' => [
                'user' => $userData,
                'capabilities' => app(AbilityResolver::class)->forUser($userData),
            ],
            'flash' => [
                'success' => fn () => $request->hasSession() ? $request->session()->get('success') : null,
                'error' => fn () => $request->hasSession() ? $request->session()->get('error') : null,
            ],
        ];
    }

    /**
     * Carica tutte le traduzioni per la locale corrente
     */
    private function getTranslations(string $locale): array
    {
        $langPath = lang_path($locale);

        if (!is_dir($langPath)) {
            return [];
        }

        $translations = [];

        foreach (glob("$langPath/*.php") as $file) {
            $key = basename($file, '.php');
            $translations[$key] = require $file;
        }

        return $translations;
    }
}
