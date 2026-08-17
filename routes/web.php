<?php

use App\Areas\Main\ExamplePage\Presentation\Http\Controllers\ExamplePageController;
use App\Areas\Main\FallbackController;
use Illuminate\Support\Facades\Route;

if (!function_exists('localeRoutes')) {
    function localeRoutes($localizedRoutes): void
    {
        $localizedRoutes();

        Route::prefix('{locale}')
            ->name('localized.')
            ->group($localizedRoutes);
    }
}

require __DIR__.'/web_auth.php';
require __DIR__.'/web_admin.php';

localeRoutes(function () {
    Route::get('/', [FallbackController::class, 'app'])->name('home');
    Route::get('/example-page', [ExamplePageController::class, 'index'])->name('example.page');
});

localeRoutes(function () {
    Route::get('/{any}', [FallbackController::class, 'notFound'])->name('not.found');
});
