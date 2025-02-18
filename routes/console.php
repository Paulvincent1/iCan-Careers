<?php

use App\Console\Commands\InvoiceUpdateStatus;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->everyMinute();


Schedule::command(InvoiceUpdateStatus::class)->everyMinute();