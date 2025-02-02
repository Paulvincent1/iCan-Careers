<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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

    public function scopeFilter(Builder $query, array $filters){
        if($filters['business_name'] ?? false){
            $query->where('business_name','like', '%' . request('business_name') . '%');
        }
    }

    // public function user(){
    //     return $this->belongsTo(User::class,'user_id');
    // }

    public function employerProfile(){
        return $this->hasMany(EmployerProfile::class, 'business_id');
    }

}
