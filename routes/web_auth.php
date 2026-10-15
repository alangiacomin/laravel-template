<?php

use App\Areas\Main\Auth\Presentation\Http\Controllers\AuthController;
use App\Areas\Main\Auth\Presentation\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'loginView'])->name('login');
Route::get('/register', [AuthController::class, 'registerView'])->name('register');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/user.show', [AuthController::class, 'userView'])->name('user.show');
Route::get('/verification.notice', [AuthController::class, 'verificationNoticeView'])->name('verification.notice');
Route::get('/verification.verify', [AuthController::class, 'emailVerification'])->name('verification.verify');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::patch('/user.update/{id}', [UserController::class, 'update'])->name('user.update');
