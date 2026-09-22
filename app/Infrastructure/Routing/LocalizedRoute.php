<?php

namespace App\Infrastructure\Routing;

use Illuminate\Routing\Route as IlluminateRoute;

final class LocalizedRoute
{
    public static function name(string $name, ?string $locale = null): string
    {
        return ($locale ?? app()->getLocale()).'.'.$name;
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public static function url(string $name, array $parameters = [], ?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return route(self::name($name, $locale), $parameters);
    }

    public static function logicalName(IlluminateRoute $route): ?string
    {
        $name = $route->getName();

        if (!is_string($name)) {
            return null;
        }

        foreach (config('app.locales', []) as $locale) {
            $prefix = $locale.'.';

            if (str_starts_with($name, $prefix)) {
                return substr($name, strlen($prefix));
            }
        }

        return null;
    }

    public static function nameForPath(string $path, string $locale): ?string
    {
        foreach (config('app.locales', []) as $availableLocale) {
            foreach (config("routes.localized.$availableLocale", []) as $name => $uri) {
                if ($uri !== '' && !str_contains($uri, '{') && trim($uri, '/') === trim($path, '/')) {
                    return $name;
                }
            }
        }

        return null;
    }
}
