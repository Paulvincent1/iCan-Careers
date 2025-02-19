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
        'birth_year',
        'gender',
        'resume',
        'resume_path',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
}
