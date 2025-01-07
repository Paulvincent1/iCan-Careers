<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkerProfile extends Model
{
    protected $fillable = [
        'job_title',
        'profile_description',
        'highest_educational_attainment',
        'job_type',
        'work_hour_per_day',
        'hour_pay',
        'month_pay',
        'gender',
    ];
}
