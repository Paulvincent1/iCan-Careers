<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployerSubscriptionInvoice extends Model
{
    
    protected $fillable = [
        'external_id',
        'invoice_id',
        'description',
        'invoice_url',
        'subscription_type',
        'duration',
    ];


    public function employer(){
        return $this->belongsTo(User::class,'employer_id');
    }
}
