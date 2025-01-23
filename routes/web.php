<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployerProfileController;
use App\Http\Controllers\WorkerDashboard;
use App\Http\Controllers\WorkerProfileController;
use App\Http\Controllers\WorkerSkillsController;
use App\Http\Controllers\WorkerVerificationController;
use App\Http\Middleware\isEmployer;
use App\Http\Middleware\isWorker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

    
Route::get('/', function () {
    $userRole = '';
    if($auth = Auth::user()){
        foreach($auth->roles as $role){
            $userRole = $role->name;
        }

        if($userRole === 'Senior' || $userRole ==='PWD'){
            return redirect()->route('worker.dashboard');
        }
        // todo if employer
    }
    return inertia('Home');
})->name('home');

// functionality for the vue.

Route::get('/goback', function(){
    return redirect()->back();
});

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


Route::post('/logout',[AuthController::class, 'logout'])->middleware(['auth'])->name('logout');


Route::prefix('jobseekers')->middleware(['auth',isWorker::class])->group(function (){

    Route::get('/createprofile',[WorkerProfileController::class, 'createProfile'])->name('create.profile');
    Route::post('/createprofile',[WorkerProfileController::class, 'storeProfile'])->name('create.profile.post');

    Route::get('/addskills',[WorkerSkillsController::class, 'create'])->name('add.skills');
    Route::post('/addskills',[WorkerSkillsController::class, 'store'])->name('add.skills.post');

    Route::get('/',[WorkerDashboard::class, 'index'])->name('worker.dashboard');
    
    Route::get('/myprofile',[WorkerProfileController::class, 'myProfile'])->name('worker.profile');
    Route::put('/myprofile/updateprofile',[WorkerProfileController::class, 'updateProfile'])->name('update.profile.put');
    Route::put('/myprofile/updateskill/{skillid}',[WorkerProfileController::class, 'updateSkill'])->name('update.profile.put');
    Route::delete('/myprofile/deleteskill/{skillid}',[WorkerProfileController::class, 'deleteSkill'])->name('update.profile.delete');

    Route::prefix('verification')->group(function () {

        Route::get('/',[WorkerVerificationController::class, 'index'])->name('account.verify');
        
        Route::get('/id',[WorkerVerificationController::class, 'verificationId'])->name('account.verifiy.id');
        Route::post('/id', [WorkerVerificationController::class, 'store'])->name('account.verifiy.id.post');
    });
});


Route::prefix('employers')->middleware(['auth',isEmployer::class])->group(function (){

    Route::get('/createprofile',[EmployerProfileController::class, 'createProfile'])->name('create.profile.employer');
    Route::post('/createprofile',[EmployerProfileController::class, 'storeProfile'])->name('create.profile.employer.post');

    Route::get('/companyinformation',[EmployerProfileController::class, 'createCompanyInformation'])->name('create.company.employer');
});