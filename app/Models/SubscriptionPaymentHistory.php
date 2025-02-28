<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPaymentHistory extends Model
{
    protected $fillable = [
        'subscription_type',
        'amount'  
    ];

    public function employer(){
        return $this->belongsTo(User::class, 'employer_id');
    }
    
}
