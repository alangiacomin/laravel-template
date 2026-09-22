<?php

use App\Areas\Main\ExamplePage\Presentation\Http\Controllers\ExamplePageController;
use App\Areas\Main\FallbackController;
use Illuminate\Support\Facades\Route;

require __DIR__.'/web_auth.php';
require __DIR__.'/web_admin.php';

Route::get('/', [FallbackController::class, 'app'])->name('home');
Route::get('/example-page', [ExamplePageController::class, 'index'])->name('example.page');
Route::get('/{any}', [FallbackController::class, 'notFound'])->name('not.found');
