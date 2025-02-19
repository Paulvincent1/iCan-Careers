<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{
    protected $fillable = [
        'balance',
        'unsettlement',
    ];


    public function worker(){
        return $this->belongsTo(User::class,'worker_id');
    }
}
