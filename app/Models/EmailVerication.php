<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailVerication extends Model
{
    protected $fillable = [
        'email',
        'verification_code'
    ];
}
