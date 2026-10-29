<?php

use App\Areas\Main\Auth\Presentation\Http\Controllers\AuthController;
use App\Areas\Main\Auth\Presentation\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/accedi', [AuthController::class, 'loginView'])->name('login');
Route::get('/registrati', [AuthController::class, 'registerView'])->name('register');
Route::get('/esci', [AuthController::class, 'logout'])->name('logout');

Route::get('/profilo', [AuthController::class, 'userView'])->name('user.show');
Route::get('/verifica-email', [AuthController::class, 'verificationNoticeView'])->name('verification.notice');
Route::get('/verifica-email/conferma/{id}/{hash}', [AuthController::class, 'emailVerification'])->name('verification.verify');

Route::post('/accedi', [AuthController::class, 'login']);
Route::post('/registrati', [AuthController::class, 'register']);

Route::patch('/profilo/{id}', [UserController::class, 'update'])->name('user.update');
