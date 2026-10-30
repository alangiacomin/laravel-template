<?php

use App\Areas\Main\Auth\Presentation\Http\Controllers\AuthController;
use App\Areas\Main\Auth\Presentation\Http\Controllers\PasswordResetController;
use App\Areas\Main\Auth\Presentation\Http\Controllers\UserController;
use App\Infrastructure\Middleware\EnsureUserIsNotBanned;
use Illuminate\Support\Facades\Route;

Route::get('/accedi', [AuthController::class, 'loginView'])->name('login');
Route::get('/registrati', [AuthController::class, 'registerView'])->name('register');

Route::get('/profilo', [AuthController::class, 'userView'])->name('user.show');
Route::get('/verifica-email', [AuthController::class, 'verificationNoticeView'])->name('verification.notice');
Route::get('/verifica-email/conferma/{id}/{hash}', [AuthController::class, 'emailVerification'])->whereNumber('id')->name('verification.verify');

Route::get('/password-dimenticata', [PasswordResetController::class, 'forgotPasswordView'])->name('password.request');
Route::get('/reimposta-password/{token}', [PasswordResetController::class, 'resetPasswordView'])->name('password.reset');

Route::post('/accedi', [AuthController::class, 'login']);
Route::post('/registrati', [AuthController::class, 'register']);
Route::post('/verifica-email/reinvia', [AuthController::class, 'resendVerification'])->name('verification.send');
Route::post('/password-dimenticata', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
Route::post('/reimposta-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');
Route::post('/esci', [AuthController::class, 'logout'])->name('logout')->withoutMiddleware(EnsureUserIsNotBanned::class);

Route::patch('/profilo', [UserController::class, 'update'])->name('user.update');
