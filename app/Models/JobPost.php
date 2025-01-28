<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobPost extends Model
{
    protected $fillable = [
        'job_title',
        'job_type', // part-time or full-time, project based
        'work_arrangement', // wfh, onsite, hybrid
        'hour_per_day',
        'hourly_rate',
        'salary',
        'description',
        'preferred_educational_attainment', // can add No Preference
        'preferred_worker_types',
    ];

    protected $casts = [
        'preferred_worker_types' => 'array'
    ];

    public function employer(){
        return $this->belongsTo(User::class, 'employer_id');
    }

    public function jobStatus(){
        return $this->hasMany(JobStatus::class, 'job_id');
    }

}
