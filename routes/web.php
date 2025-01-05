<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return inertia('Home');
})->name('home');

Route::get('/register',[AuthController::class, 'registerCreate'])->name('register.create');
Route::post('/register',[AuthController::class, 'register'])->name('register.post');

Route::get('/login',[AuthController::class, 'loginIndex'])->name('login');
Route::post('/login',[AuthController::class, 'login'])->name('login.post');

Route::get('/forgot-password',[AuthController::class, 'forgotPasswordIndex'])->name('password.request');
Route::post('/forgot-password',[AuthController::class, 'forgotPasswordSendVerification'])->name('password.email');

Route::get('/reset-password/{token}',[AuthController::class , 'resetPasswordIndex'])->name('password.reset');
Route::post('/reset-password', [AuthController::class , 'resetPassword'])->name('password.update');
