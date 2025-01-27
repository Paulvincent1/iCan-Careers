<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobStatus extends Model
{
    protected $fillable = [
        'job_status',
        'resume',
        'job_id',  
    ];

    public function job(){
        return $this->belongsTo(JobPost::class, 'job_id');
    }

    public function worker(){
        return $this->belongsTo(User::class, 'worker_id');
    }
}
