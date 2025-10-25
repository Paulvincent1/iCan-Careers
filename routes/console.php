<?php

use App\Console\Commands\InvoiceUpdateStatus;
use App\Models\EmployerSubscription;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->daily();

Artisan::command('update:subscription-type-employer', function () {
    $employerExpiredSubscription = EmployerSubscription::whereDate('expiry_date', '<=', Carbon::now())
        ->whereIn('subscription_type', ['Pro', 'Premium'])->get();

          Log::info('hi');

    foreach ($employerExpiredSubscription as $expiredSubscription) {
        $expiredSubscription->update([
            'subscription_type' => 'Free'
        ]);

        Log::info($expiredSubscription->employer);

        foreach($expiredSubscription->employer->employerJobPosts()->where('job_status', 'Open')
            ->latest()->skip(3)->take(PHP_INT_MAX)->get() as $jobPost)
        {
            $jobPost->update([
                'job_status' => 'Locked'
            ]);
        }
    }

    EmployerSubscription::whereIn('subscription_type',['Pro','Premium'])->get()
    ->each(function ($empSubscription) {
        $empSubscription->employer->employerJobPosts()->where('job_status', 'Locked')
        ->update([
            'job_status'=> 'Open'
        ]);
    });

})->purpose('update employer subscription type if expired')->everyMinute();


Schedule::command(InvoiceUpdateStatus::class)->everyMinute();
