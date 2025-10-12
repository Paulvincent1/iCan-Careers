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
        'id_image_public_id',
        'id_image_url',
        'selfie_image_public_id',
        'selfie_image_url',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
}
