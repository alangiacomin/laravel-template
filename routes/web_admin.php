<?php

use App\Areas\Admin\Dashboard\Presentation\Http\Controllers\DashboardController;
use App\Areas\Admin\Fumetti\Presentation\Http\Controllers\FumettiController;
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
Route::get('/'.$localizedRoutes['admin.testate'], [FumettiController::class, 'testate'])->name('admin.testate');
Route::get('/'.$localizedRoutes['admin.testata.show'], [FumettiController::class, 'testata'])->name('admin.testata.show');
Route::post('/'.$localizedRoutes['admin.testate.create'], [FumettiController::class, 'creaTestata'])->name('admin.testate.create');
Route::patch('/'.$localizedRoutes['admin.testata.update'], [FumettiController::class, 'aggiornaTestata'])->name('admin.testata.update');
Route::delete('/'.$localizedRoutes['admin.testata.delete'], [FumettiController::class, 'eliminaTestata'])->name('admin.testata.delete');
Route::get('/'.$localizedRoutes['admin.serie'], [FumettiController::class, 'serie'])->name('admin.serie');
Route::get('/'.$localizedRoutes['admin.serie.show'], [FumettiController::class, 'serieDettaglio'])->name('admin.serie.show');
Route::post('/'.$localizedRoutes['admin.serie.create'], [FumettiController::class, 'creaSerie'])->name('admin.serie.create');
Route::patch('/'.$localizedRoutes['admin.serie.update'], [FumettiController::class, 'aggiornaSerie'])->name('admin.serie.update');
Route::delete('/'.$localizedRoutes['admin.serie.delete'], [FumettiController::class, 'eliminaSerie'])->name('admin.serie.delete');
Route::get('/'.$localizedRoutes['admin.albi'], [FumettiController::class, 'albi'])->name('admin.albi');
Route::get('/'.$localizedRoutes['admin.albo.show'], [FumettiController::class, 'albo'])->name('admin.albo.show');
Route::post('/'.$localizedRoutes['admin.albi.create'], [FumettiController::class, 'creaAlbo'])->name('admin.albi.create');
Route::patch('/'.$localizedRoutes['admin.albo.update'], [FumettiController::class, 'aggiornaAlbo'])->name('admin.albo.update');
Route::delete('/'.$localizedRoutes['admin.albo.delete'], [FumettiController::class, 'eliminaAlbo'])->name('admin.albo.delete');
Route::get('/'.$localizedRoutes['admin'].'/{any}', [FallbackController::class, 'notFound'])->name('admin.not.found');
