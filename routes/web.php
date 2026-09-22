<?php

use App\Areas\Main\ExamplePage\Presentation\Http\Controllers\ExamplePageController;
use App\Areas\Main\FallbackController;
use App\Areas\Main\Language\Presentation\Http\Controllers\LanguageController;
use App\Infrastructure\Routing\LocalizedRoute;
use Illuminate\Support\Facades\Route;

foreach (config('app.locales', []) as $locale) {
    Route::prefix($locale)->name($locale.'.')->group(function () use ($locale): void {
        $localizedRoutes = config("routes.localized.$locale");

        require __DIR__.'/web_auth.php';
        require __DIR__.'/web_admin.php';

        Route::get('/', [FallbackController::class, 'app'])->name('home');
        Route::get('/'.$localizedRoutes['example.page'], [ExamplePageController::class, 'index'])
            ->name('example.page');
        Route::post('/language', [LanguageController::class, 'update'])->name('language.update');
        Route::get('/{any}', [FallbackController::class, 'notFound'])->name('not.found');
    });
}

Route::get('/', function () {
    $locale = session('locale', config('app.fallback_locale'));

    return redirect()->route($locale.'.home');
})->name('legacy.home');

Route::match(['GET', 'POST', 'PATCH'], '/{legacyPath}', function (string $legacyPath) {
    $segments = explode('/', trim($legacyPath, '/'));
    $supportedLocales = config('app.locales', []);
    $firstSegment = $segments[0];

    if (
        $legacyPath === '.well-known'
        || str_starts_with($legacyPath, '.well-known/')
        || in_array($firstSegment, $supportedLocales, true)
    ) {
        abort(404);
    }

    $locale = session('locale', config('app.fallback_locale'));
    $logicalName = LocalizedRoute::nameForPath($legacyPath, $locale);

    if ($logicalName) {
        return redirect()->to(LocalizedRoute::url($logicalName, [], $locale), 308);
    }

    return redirect()->to('/'.$locale.'/'.$legacyPath, 308);
})->where('legacyPath', '.*')->name('legacy.redirect');
