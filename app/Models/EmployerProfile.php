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
        'business_id',
    ];

    // public function user(){
    //     return $this->belongsTo(User::class,'user_id');
    // }

    public function businessInformation(){
        return $this->belongsTo(BusinessInformation::class, 'business_id');
    }
}
