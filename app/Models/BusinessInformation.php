<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BusinessInformation extends Model
{
    protected $fillable = [
        'business_name',
        'business_logo_public_id',
        'business_logo_url',
        'industry',
        'business_description',
        'business_location',
        'user_id',
    ];

    protected $casts = [
        'business_location' => 'array'
    ];

     // Add accessor for backward compatibility
    public function getBusinessLogoAttribute()
    {
        return $this->business_logo_url;
    }

    public function scopeFilter(Builder $query, array $filters){
        if($filters['business_name'] ?? false){
            $query->where('business_name','like', '%' . request('business_name') . '%');
        }
    }

    public function employer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function employers()
    {
        return $this->belongsToMany(User::class, 'business_user', 'business_information_id', 'user_id');
    }


    // public function user(){
    //     return $this->belongsTo(User::class,'user_id');
    // }

    public function employerProfile(){
        return $this->hasMany(EmployerProfile::class, 'business_id');
    }

}
