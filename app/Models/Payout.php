<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    protected $fillable = [
        'reference_id',
        'channel_code',
        'account_holder_name',
        'account_number',
        'amount',
    ];


    public function worker(){
        return $this->belongsTo(User::class, 'worker_id');
    }
}
