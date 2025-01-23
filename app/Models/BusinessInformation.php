<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessInformation extends Model
{
    protected $fillable = [
        'business_name',
        'business_logo',
        'industry',
        'company_description',
        'company_location',
    ];
}
