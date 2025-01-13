<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\WorkerProfileController;
use App\Http\Controllers\WorkerSkillsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return inertia('Home');
})->name('home');

Route::middleware(['guest'])->group(function (){

    Route::get('/register',[AuthController::class, 'registerCreate'])->name('register.create');
    Route::post('/register',[AuthController::class, 'register'])->name('register.post');

    Route::get('/login',[AuthController::class, 'loginIndex'])->name('login');
    Route::post('/login',[AuthController::class, 'login'])->name('login.post');

    Route::get('/forgot-password',[AuthController::class, 'forgotPasswordIndex'])->name('password.request');
    Route::post('/forgot-password',[AuthController::class, 'forgotPasswordSendVerification'])->name('password.email');

    Route::get('/reset-password/{token}',[AuthController::class , 'resetPasswordIndex'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class , 'resetPassword'])->name('password.update');
});


Route::prefix('jobseekers')->middleware(['auth'])->group(function (){
    Route::post('/logout',[AuthController::class, 'logout'])->name('logout');

    Route::get('/create-profile',[WorkerProfileController::class, 'createProfile'])->name('create.profile');
    Route::post('/create-profile',[WorkerProfileController::class, 'storeProfile'])->name('create.profile.post');

    Route::get('/add-skills',[WorkerSkillsController::class, 'create'])->name('add.skills');
    Route::post('/add-skills',[WorkerSkillsController::class, 'store'])->name('add.skills.post');
});
