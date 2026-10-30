<?php

namespace App\Infrastructure\Middleware;

use App\Areas\Main\Auth\Application\Data\UserData;
use App\Areas\Main\Auth\Application\Inertia\AbilityResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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
        $locale = app()->getLocale();

        return [
            ...parent::share($request),
            'locale' => $locale,
            'translations' => fn () => $this->getTranslations($locale),
            'auth' => function () {
                $user = Auth::user();
                $userData = $user ? UserData::fromModel($user) : null;

                return [
                    'user' => $userData,
                    'capabilities' => app(AbilityResolver::class)->forUser($userData),
                ];
            },
            'flash' => [
                'success' => fn () => $request->hasSession() ? $request->session()->get('success') : null,
                'error' => fn () => $request->hasSession() ? $request->session()->get('error') : null,
            ],
        ];
    }

    /**
     * Carica tutte le traduzioni per la locale corrente, in cache finché i file non cambiano.
     *
     * @return array<string, array<string, mixed>>
     */
    private function getTranslations(string $locale): array
    {
        $files = glob(lang_path($locale).'/*.php') ?: [];

        $version = md5(implode('|', array_map(
            fn (string $file) => $file.':'.filemtime($file),
            $files,
        )));

        return Cache::remember("inertia.translations.$locale.$version", now()->addDay(), function () use ($files) {
            $translations = [];

            foreach ($files as $file) {
                $translations[basename($file, '.php')] = require $file;
            }

            return $translations;
        });
    }
}
