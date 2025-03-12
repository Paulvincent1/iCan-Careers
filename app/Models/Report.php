<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'reason',
        'reported_user_id',
        'complainant_user_id'
    ];

    public function reporter(){
        return $this->belongsTo(User::class, 'complainant_user_id');
    }

    public function reported(){
        return $this->belongsTo(User::class,'reported_user_id');
    }

}
