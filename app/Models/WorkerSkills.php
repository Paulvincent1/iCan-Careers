<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkerSkills extends Model
{
    protected $fillable = [
        'skill_name',
        'experience',
        'rating'
    ];

    public function user() {
        return $this->belongsTo(User::class,'user_id');
    }
}
