<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_img',
        'verified',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles(){
        return $this->belongsToMany(Role::class,'role_user');
    }

    public function workerProfile(){
        return $this->hasOne(WorkerProfile::class, 'user_id');
    }

    public function workerSkills() {
        return $this->hasMany(WorkerSkills::class, 'user_id');
    }

    public function workerVerification(){
        return $this->hasOne(WorkerVerification::class, 'user_id');
    }

    public function employerProfile(){
        return $this->hasOne(EmployerProfile::class, 'user_id');
    }

    public function businessInformation(){
        return $this->hasOne(BusinessInformation::class, 'user_id');
    }

    public function employerJobPosts(){
        return $this->hasMany(JobPost::class,'employer_id');
    }

    public function workerJobStatuses(){
        return $this->hasMany(JobStatus::class, 'worker_id');
    }
}
