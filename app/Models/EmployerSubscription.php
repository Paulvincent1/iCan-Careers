<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployerSubscription extends Model
{
    protected $fillable = [
        'subscription_type',
        'start_date',
        'expiry_date',
    ];

    public function employer(){
        return $this->belongsTo(User::class,'employer_id');
    }
}
