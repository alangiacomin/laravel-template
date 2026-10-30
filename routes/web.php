<?php

use App\Areas\Main\ExamplePage\Presentation\Http\Controllers\ExamplePageController;
use App\Areas\Main\FallbackController;
use App\Areas\Main\Home\Presentation\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

require __DIR__.'/web_auth.php';
require __DIR__.'/web_admin.php';

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/example.page', [ExamplePageController::class, 'index'])
    ->name('example.page');
Route::fallback([FallbackController::class, 'notFound'])->name('not.found');
