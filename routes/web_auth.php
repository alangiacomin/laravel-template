<?php

use App\Areas\Main\Auth\Presentation\Http\Controllers\AuthController;
use App\Areas\Main\Auth\Presentation\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

localeRoutes(function () {
    Route::get('/login', [AuthController::class, 'loginView'])->name('login');
    Route::get('/register', [AuthController::class, 'registerView'])->name('register');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/user', [AuthController::class, 'userView'])->name('user.show');
    Route::get('/email/verify', [AuthController::class, 'verificationNoticeView'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'emailVerification'])->name('verification.verify');
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::patch('/user/{id}/update', [UserController::class, 'update'])->name('user.update');
