<?php

use App\Areas\Admin\Dashboard\Presentation\Http\Controllers\DashboardController;
use App\Areas\Admin\FallbackController;
use App\Areas\Admin\Roles\Presentation\Http\Controllers\RoleController;
use App\Areas\Admin\Users\Presentation\Http\Controllers\UserController;
use App\Shared\Infrastructure\Enums\GateEnum;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->middleware(['auth', 'verified', 'can:'.GateEnum::ADMIN_ACCESS->value])
    ->group(function (): void {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/ruoli', [RoleController::class, 'index'])->name('admin.roles');
        Route::get('/ruoli/{id}', [RoleController::class, 'show'])->whereNumber('id')->name('admin.role.show');
        Route::patch('/ruoli/{id}', [RoleController::class, 'update'])->whereNumber('id')->name('admin.role.update');
        Route::get('/utenti', [UserController::class, 'index'])->name('admin.users');
        Route::get('/utenti/{id}', [UserController::class, 'show'])->whereNumber('id')->name('admin.user.show');
        Route::patch('/utenti/{id}', [UserController::class, 'update'])->whereNumber('id')->name('admin.user.update');
        Route::patch('/utenti/{id}/blocca', [UserController::class, 'block'])->whereNumber('id')->name('admin.user.block');
        Route::patch('/utenti/{id}/sblocca', [UserController::class, 'unblock'])->whereNumber('id')->name('admin.user.unblock');
    });

Route::get('/admin/{any}', [FallbackController::class, 'notFound'])->where('any', '.*')->name('admin.not.found');
