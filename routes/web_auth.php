<?php

use App\Areas\Main\Auth\Presentation\Http\Controllers\AuthController;
use App\Areas\Main\Auth\Presentation\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/** @var array<string, string> $localizedRoutes */
$localizedRoutes = $localizedRoutes ?? [];

Route::get('/'.$localizedRoutes['login'], [AuthController::class, 'loginView'])->name('login');
Route::get('/'.$localizedRoutes['register'], [AuthController::class, 'registerView'])->name('register');
Route::get('/'.$localizedRoutes['logout'], [AuthController::class, 'logout'])->name('logout');

Route::get('/'.$localizedRoutes['user.show'], [AuthController::class, 'userView'])->name('user.show');
Route::get('/'.$localizedRoutes['verification.notice'], [AuthController::class, 'verificationNoticeView'])->name('verification.notice');
Route::get('/'.$localizedRoutes['verification.verify'], [AuthController::class, 'emailVerification'])->name('verification.verify');

Route::post('/'.$localizedRoutes['login'], [AuthController::class, 'login']);
Route::post('/'.$localizedRoutes['register'], [AuthController::class, 'register']);

Route::patch('/'.$localizedRoutes['user.update'], [UserController::class, 'update'])->name('user.update');
