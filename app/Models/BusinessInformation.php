<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessInformation extends Model
{
    protected $fillable = [
        'business_name',
        'business_logo',
        'industry',
        'business_description',
        'business_location',
    ];

    protected $casts = [
        'business_location' => 'array'
    ];

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
}
