<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployerDashboardController;
use App\Http\Controllers\EmployerProfileController;
use App\Http\Controllers\EmployerSubscriptionController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\JobPostController;
use App\Http\Controllers\JobSearchController;
use App\Http\Controllers\LearningController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReportJobPostController;
use App\Http\Controllers\WorkerBasicInfoController;
use App\Http\Controllers\WorkerDashboard;
use App\Http\Controllers\WorkerProfileController;
use App\Http\Controllers\WorkerSkillsController;
use App\Http\Controllers\WorkerVerificationController;
use App\Http\Middleware\ForceGetRedirect;
use App\Http\Middleware\isAdmin;
use App\Http\Middleware\isBan;
use App\Http\Middleware\isEmployer;
use App\Http\Middleware\isWorker;
use App\Models\EmployerProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    $userRole = '';
    if ($auth = Auth::user()) {
        foreach ($auth->roles as $role) {
            $userRole = $role->name;
        }

        if ($userRole === 'Senior' || $userRole === 'PWD') {
            return redirect()->route('worker.dashboard');
        } elseif ($userRole === 'Employer') {
            return redirect()->route('employer.dashboard');
        }

        return redirect()->route('admin.dashboard');
    }
    return inertia('Home');
})->name('home');


Route::middleware(['guest'])->group(function () {


    Route::get('/register', [AuthController::class, 'registerCreate'])->name('register.create');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');

    Route::get('/login', [AuthController::class, 'loginIndex'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    Route::get('/forgot-password', [AuthController::class, 'forgotPasswordIndex'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'forgotPasswordSendVerification'])->name('password.email');

    Route::get('/reset-password/{token}', [AuthController::class, 'resetPasswordIndex'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});


Route::post('/logout', [AuthController::class, 'logout'])->middleware(['auth'])->name('logout');


Route::prefix('jobseekers')->middleware([ForceGetRedirect::class, isWorker::class, isBan::class])->group(function () {

    Route::prefix('verification')->group(function () {

        Route::get('/', [WorkerVerificationController::class, 'index'])->name('account.verify');

        Route::get('/id', [WorkerVerificationController::class, 'verificationId'])->name('account.verifiy.id');
        Route::post('/id', [WorkerVerificationController::class, 'store'])->name('account.verifiy.id.post');
    });

    Route::get('/createprofile', [WorkerProfileController::class, 'createProfile'])->name('create.profile');
    Route::post('/createprofile', [WorkerProfileController::class, 'storeProfile'])->name('create.profile.post');

    Route::get('/addskills', [WorkerSkillsController::class, 'create'])->name('add.skills');
    Route::post('/addskills', [WorkerSkillsController::class, 'store'])->name('add.skills.post');


    Route::get('/', [WorkerDashboard::class, 'index'])->name('worker.dashboard');

    Route::post('/payout', [WorkerDashboard::class, 'payout'])->name('worker.payout');


    Route::get('/create-invoice', [WorkerDashboard::class, 'createInvoice'])->name('worker.create.invoice');
    Route::get('/test-invoice', [WorkerDashboard::class, 'storeInvoice'])->name('worker.store.invoice');
    Route::post('/preview-invoice', [WorkerDashboard::class, 'previewInvoice'])->name('worker.preview.invoice');
    Route::post('/send-invoice', [WorkerDashboard::class, 'sendInvoice'])->name('worker.send.invoice');



    Route::get('/myprofile', [WorkerProfileController::class, 'myProfile'])->name('worker.profile');
    Route::put('/myprofile/updateprofile', [WorkerProfileController::class, 'updateProfile'])->name('update.profile.put');
    Route::put('/myprofile/updateskill/{skillid}', [WorkerProfileController::class, 'updateSkill'])->name('update.profile.put');
    Route::delete('/myprofile/deleteskill/{skillid}', [WorkerProfileController::class, 'deleteSkill'])->name('update.profile.delete');

    Route::put('/myprofile/updateBasicInfo', [WorkerBasicInfoController::class, 'update'])->name('update.basicInfo.put');


    // for job search
    Route::prefix('jobsearch')->group(function () {

        Route::get('/', [JobSearchController::class, 'index'])->name('jobsearch');
        Route::post('/save/{id}', [JobSearchController::class, 'saveJob'])->name('jobsearch.save.job');
        Route::get('/showjob/{id}', [JobSearchController::class, 'show'])->name('jobsearch.show');

        Route::post('/showjob/{id}', [JobSearchController::class, 'apply'])->name('jobsearch.apply');

        Route::get('/profile/{employerId}', [EmployerProfileController::class, 'showEmployerProfile'])->name('visit.employer.profile');
    });
});


Route::prefix('employers')->middleware([ForceGetRedirect::class, isEmployer::class, isBan::class])->group(function () {

    Route::get('/createprofile', [EmployerProfileController::class, 'createProfile'])->name('create.profile.employer');
    Route::post('/createprofile', [EmployerProfileController::class, 'storeProfile'])->name('create.profile.employer.post');
    Route::get('/myprofile', [EmployerProfileController::class, 'myProfile'])->name('employer.profile');
    Route::put('/myprofile/updateprofile', [EmployerProfileController::class, 'updateProfile'])->name('update.profile.put');


    Route::get('/', [EmployerDashboardController::class, 'index'])->name('employer.dashboard');
    Route::get('/createjob', [JobPostController::class, 'create'])->name('create.job');
    Route::post('/createjob', [JobPostController::class, 'store'])->name('create.job.post');


    Route::get('/jobpost/{jobid}', [JobPostController::class, 'show'])->name('employer.jobpost.show');
    Route::get('/jobpost/{jobid}/edit', [JobPostController::class, 'edit'])->name('employer.jobpost.edit');
    Route::put('/jobpost/{jobid}/update', [JobPostController::class, 'update'])->name('employer.jobpost.update');
    Route::put('/jobpost/{jobid}', [JobPostController::class, 'closeJob'])->name('employer.jobpost.close');


    Route::get('/applicants/{jobid}', [EmployerDashboardController::class, 'showJobApplicants'])->name('job.applicants');
    Route::put('/applicants/{pivotId}', [EmployerDashboardController::class, 'updateStatus'])->name('job.applicants.update.status');
    Route::put('/applicants/{pivotId}/add-interview-schedule', [EmployerDashboardController::class, 'addInterview'])->name('job.applicants.addinterview');

    // fire worker
    Route::put('/fire-worker/{workerId}', [JobPostController::class, 'fireWorker'])->name('fireworker');
});


Route::prefix('admin')->middleware([isAdmin::class])->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/workers', [AdminDashboardController::class, 'workers'])->name('admin.workers');
    Route::put('/workers/{id}/verify', [AdminDashboardController::class, 'toggleVerification'])->name('admin.workers.verify');
    Route::get('/workers/{id}/verification', [AdminDashboardController::class, 'workerVerificationDetails'])
        ->name('admin.workers.verification');
    Route::delete('/workers/{id}/delete', [AdminDashboardController::class, 'deleteVerification'])
        ->name('admin.workers.delete');
    Route::get('/employers', [AdminDashboardController::class, 'employers'])->name('admin.employers');
    Route::get('/reported-users', [AdminDashboardController::class, 'reportedUsers'])->name('admin.reported.users');
    Route::put('/reported-users/toggle-ban/{id}', [AdminDashboardController::class, 'toggleBan']);

    Route::get('/reported-posts', [AdminDashboardController::class, 'reportedPosts'])->name('admin.reported.posts');
    Route::get('/reported-job-posts/{id}', [AdminDashboardController::class, 'showReportedJob']);
    Route::put('/reported-job-posts/{id}/ban', [AdminDashboardController::class, 'banJob']);


    Route::delete('/job-posts/{id}/delete', [AdminDashboardController::class, 'deleteJob'])->name('admin.job.delete');


    Route::get('/job-approvals', [AdminDashboardController::class, 'jobApprovals'])->name('admin.job.approvals');
    Route::put('/job-approvals/{id}/update', [JobPostController::class, 'updateJobStatus'])
        ->name('admin.job.approvals.update');
    Route::get('/job-posts/{id}', [JobPostController::class, 'showJob'])->name('admin.show-job-post');
    Route::get('/payment-history', [AdminDashboardController::class, 'paymentHistory'])->name('admin.payment-history');
    Route::get('/table-subscription', [AdminDashboardController::class, 'subscribeUsers'])
        ->name('admin.table-subscription');


    // view profile user
    Route::get('/user/{id}',[AdminDashboardController::class, 'viewProfile'])->name('admin.view-profile-user');
});



//shared route for authenticated users
Route::middleware([ForceGetRedirect::class])->group(function () {

    //chat routes
    Route::prefix('/messages')->group(function () {


        Route::get('/', [MessageController::class, 'index'])->name('messages');

        Route::post('/send/{receiverId}', [MessageController::class, 'store'])->name('messages.send');
    });


    //notification
    Route::put('/mark-as-read-notifications', [NotificationController::class, 'markAllNotificationRead'])->name('user.mark-all-notification-as-read');


    //report user
    Route::post('/report/{userId}', [ReportController::class, 'store'])->name('report.user');

    //report job post
    Route::post('/report/job-post/{jobpostId}', [ReportJobPostController::class, 'store'])->name('report.job-post');



    Route::get('/view/applicant-profile/{applicantId}', [WorkerProfileController::class, 'show'])->name('worker.show.profile');


    Route::get('/{path}/{workerId?}', [WorkerProfileController::class, 'showResume'])->where('path', '^[^\/]+\/[^\/]+$')->where('workerId', '[0-9]+')->name('show.resume');
});

//for webhooks
Route::post('/webhook', [InvoiceController::class, 'webhook']);

Route::get('/pricing', [EmployerSubscriptionController::class, 'pricing'])->name('pricing');
Route::get('/learning', [LearningController::class, 'learning'])->name('learning');
