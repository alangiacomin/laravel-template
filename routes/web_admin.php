<?php

use App\Areas\Admin\Dashboard\Presentation\Http\Controllers\DashboardController;
use App\Areas\Admin\FallbackController;
use App\Areas\Admin\Roles\Presentation\Http\Controllers\RoleController;
use App\Areas\Admin\Users\Presentation\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/admin.dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
Route::get('/admin.roles', [RoleController::class, 'index'])->name('admin.roles');
Route::get('/admin.role.show', [RoleController::class, 'show'])->name('admin.role.show');
Route::patch('/admin.role.update', [RoleController::class, 'update'])->name('admin.role.update');
Route::get('/admin.users', [UserController::class, 'index'])->name('admin.users');
Route::get('/admin.user.show', [UserController::class, 'show'])->name('admin.user.show');
Route::patch('/admin.user.update', [UserController::class, 'update'])->name('admin.user.update');
Route::patch('/admin.user.blocca', [UserController::class, 'blocca'])->name('admin.user.blocca');
Route::patch('/admin.user.sblocca', [UserController::class, 'sblocca'])->name('admin.user.sblocca');
Route::get('/admin'.'/{any}', [FallbackController::class, 'notFound'])->name('admin.not.found');
