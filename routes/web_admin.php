<?php

use App\Areas\Admin\Dashboard\Presentation\Http\Controllers\DashboardController;
use App\Areas\Admin\FallbackController;
use App\Areas\Admin\Roles\Presentation\Http\Controllers\RoleController;
use App\Areas\Admin\Users\Presentation\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/** @var array<string, string> $localizedRoutes */
$localizedRoutes = $localizedRoutes ?? [];

Route::get('/'.$localizedRoutes['admin.dashboard'], [DashboardController::class, 'index'])->name('admin.dashboard');
Route::get('/'.$localizedRoutes['admin.roles'], [RoleController::class, 'index'])->name('admin.roles');
Route::get('/'.$localizedRoutes['admin.role.show'], [RoleController::class, 'show'])->name('admin.role.show');
Route::patch('/'.$localizedRoutes['admin.role.update'], [RoleController::class, 'update'])->name('admin.role.update');
Route::get('/'.$localizedRoutes['admin.users'], [UserController::class, 'index'])->name('admin.users');
Route::get('/'.$localizedRoutes['admin.user.show'], [UserController::class, 'show'])->name('admin.user.show');
Route::patch('/'.$localizedRoutes['admin.user.update'], [UserController::class, 'update'])->name('admin.user.update');
Route::patch('/'.$localizedRoutes['admin.user.blocca'], [UserController::class, 'blocca'])->name('admin.user.blocca');
Route::patch('/'.$localizedRoutes['admin.user.sblocca'], [UserController::class, 'sblocca'])->name('admin.user.sblocca');
Route::get('/'.$localizedRoutes['admin'].'/{any}', [FallbackController::class, 'notFound'])->name('admin.not.found');
