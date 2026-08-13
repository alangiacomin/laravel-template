<?php

use App\Areas\Admin\Dashboard\Presentation\Http\Controllers\DashboardController;
use App\Areas\Admin\FallbackController;
use App\Areas\Admin\Roles\Presentation\Http\Controllers\RoleController;
use App\Areas\Admin\Users\Presentation\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

localeRoutes(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/roles', [RoleController::class, 'index'])->name('roles');
        Route::get('/roles/{id}', [RoleController::class, 'show'])->name('role.show');

        Route::get('/users', [UserController::class, 'index'])->name('users');
        Route::get('/users/{id}', [UserController::class, 'show'])->name('user.show');

        Route::get('/{any}', [FallbackController::class, 'notFound'])->name('not.found');
    });
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::patch('/roles/{id}/update', [RoleController::class, 'update'])->name('role.update');

    Route::patch('/users/{id}/update', [UserController::class, 'update'])->name('user.update');
    Route::patch('/users/{id}/blocca', [UserController::class, 'blocca'])->name('user.blocca');
    Route::patch('/users/{id}/sblocca', [UserController::class, 'sblocca'])->name('user.sblocca');
});
