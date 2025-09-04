<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Review extends Model
{
    protected $fillable = [
        'comment',
        'star',
        'reviewee_id',
        'reviewer_id'
    ];

    public function reviewee(){
        return $this->belongsTo(User::class,'reviewee_id');
    }

    public function reviewer(){
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    // public function job()
    // {
    //     return $this->belongsTo(JobPost::class, 'job_post_id');
    // }
}
