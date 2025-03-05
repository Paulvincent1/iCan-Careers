<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkerBasicInfo extends Model
{
    protected $fillable = [
        'link',
        'address',
    ];

    public function worker()
    {
        return $this->belongsTo(User::class, 'worker_id');
    }
}
