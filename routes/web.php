<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return inertia('Home');
})->name('home');

Route::get('/register',[AuthController::class, 'registerCreate'])->name('register.create');
Route::post('/register',[AuthController::class, 'register'])->name('register.post');

Route::get('/login',[AuthController::class, 'loginIndex'])->name('login.index');
Route::post('/login',[AuthController::class, 'login'])->name('login.post');
