<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkerBasicInfo extends Model
{
    protected $fillable = [
        'link',
        'address',
    ];
}
