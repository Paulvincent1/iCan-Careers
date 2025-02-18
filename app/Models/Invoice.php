<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_id',
        'external_id',
        'description',
        'amount',
        'items',
        'invoice_url',
        'status',
        'due_date',
        'paid_at',
        'employer_id',
    ];

    protected $casts = [
        'items' => 'array',
    ];

    public function worker(){
        return $this->belongsTo(User::class,'worker_id');
    }

    public function employer(){
        return $this->belongsTo(User::class,'employer_id');
    }
}
