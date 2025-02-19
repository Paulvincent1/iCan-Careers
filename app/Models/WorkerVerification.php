<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkerVerification extends Model
{
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'id_image',
        'selfie_image',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
}
