<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailVerification extends Model
{
      protected $fillable = [
        'name',
        'email',
        'role',
        'verification_code'
    ];
}
