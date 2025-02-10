<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'external_id',
        'description',
        'amount',
        'items',
        'status',
        'due_date',
        'paid_at',
    ];

    public function worker(){
        return $this->belongsTo(User::class,'worker_id');
    }

    public function employer(){
        return $this->belongsTo(User::class,'employer_id');
    }
}
