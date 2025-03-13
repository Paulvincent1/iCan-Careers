<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportJobPost extends Model
{
    protected $fillable = [
        'reason',
        'reported_job_post_id'
    ];

    public function reporter(){
        return $this->belongsTo(User::class, 'complainant_user_id');
    }

    public function reportedJobPost(){
        return $this->belongsTo(JobPost::class,'reported_job_post_id');
    }
}
