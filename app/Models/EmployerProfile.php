<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployerProfile extends Model
{
    protected $fillable = [
        'full_name',
        'phone_number',
        'birth_year',      
        'gender',      
        'employer_type',
    ];

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
}
