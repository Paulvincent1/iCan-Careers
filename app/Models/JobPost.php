<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;

class JobPost extends Model
{
    protected $fillable = [
        'job_title',
        'job_type', // part-time or full-time, project based
        'work_arrangement', // wfh, onsite, hybrid
        'location',
        'experience', // fresher, 0-2 years, etc
        'hour_per_day',
        'hourly_rate',
        'salary',
        'description',
        'preferred_educational_attainment', // can add No Preference
        'skills',
        'preferred_worker_types',
        'job_status',
    ];

    protected $casts = [
        'preferred_worker_types' => 'array',
        'skills' => 'array',
        'location' => 'array'
    ];



    public function scopeFilter(EloquentBuilder $query, array $filters){
        
        if($filters['job_type'] ?? false) {
            $query->whereIn('job_type', request('job_type'));
        }

        if($filters['work_arrangement'] ?? false){
            $query->whereIn('work_arrangement',request('work_arrangement'));
        }

        if($filters['experience'] ?? false){
            $query->whereIn('experience', request('experience'));
        }

        if($filters['job_title'] ?? false){
            $query->where('job_title','like', '%' . request('job_title') . '%');
        }
    }




    public function employer(){
        return $this->belongsTo(User::class, 'employer_id');
    }


    public function usersWhoSaved(){
        return $this->belongsToMany(User::class, 'job_post_user','job_post_id','user_id');
    }

    public function usersWhoApplied(){
        return $this->belongsToMany(User::class, 'application','job_post_id', 'worker_id')
        ->withPivot(['id','status'])
        ->withTimestamps();
    }
}
