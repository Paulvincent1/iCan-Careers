<?php

use App\Console\Commands\InvoiceUpdateStatus;
use App\Models\EmployerSubscription;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->daily();

Artisan::command('update:subscription-type-employer', function () {
    $employerExpiredSubscription = EmployerSubscription::whereDate('expiry_date', '<=', Carbon::now())
        ->whereIn('subscription_type', ['Pro', 'Premium'])->get();

    foreach ($employerExpiredSubscription as $expiredSubscription) {
        $expiredSubscription->update([
            'subscription_type' => 'Free'
        ]);

        foreach($expiredSubscription->employer->employerJobPosts()->latest()->skip(3)->get() as $jobPost){
            $jobPost->update([
                'job_status' => 'Locked'
            ]);
        }
    }
})->purpose('update employer subscription type if expired')->daily();


Schedule::command(InvoiceUpdateStatus::class)->everyMinute();
