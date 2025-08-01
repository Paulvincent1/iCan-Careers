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
        'ban'
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

    public function workerBasicInfo(){
        return $this->hasOne(WorkerBasicInfo::class,'worker_id');
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

    // public function businessInformation(){
    //     return $this->hasOne(BusinessInformation::class, 'user_id');
    // }

    public function employerJobPosts(){
        return $this->hasMany(JobPost::class,'employer_id');
    }

    public function savedJobs(){
        return $this->belongsToMany(JobPost::class, 'job_post_user','user_id','job_post_id');
    }

    public function appliedJobs(){
        return $this->belongsToMany(JobPost::class,'application','worker_id', 'job_post_id')
        ->withPivot('id','status', 'interview_schedule','interview_mode','coordinates')
        ->withTimestamps();
    }

    public function myJobs(){
        return $this->belongsToMany(JobPost::class, 'hired_workers','worker_id','job_post_id')
        ->withPivot('current','id')
        ->withTimestamps();
    }

    

    public function workerInvoices(){
        return $this->hasMany(Invoice::class,'worker_id');
    }

    public function employerInvoices(){
        return $this->hasMany(Invoice::class,'employer_id');
    }

    public function employerSubscriptionInvoices(){
        return $this->hasMany(EmployerSubscriptionInvoice::class,'employer_id');
    }

    // for tracking subscription
    public function employerSubscription(){
        return $this->hasOne(EmployerSubscription::class,'employer_id');
    }

    public function balance(){
        return $this->hasOne(Balance::class,'worker_id');
    }

    public function payouts(){
        return $this->hasMany(Payout::class, 'worker_id');
    }


    // for chat relationship
    public function sentMessages(){
        return $this->hasMany(Message::class,'sender_id');
    }

    public function receivedMessages(){
        return $this->hasMany(Message::class,'receiver_id');
    }

    public function subscriptionPaymentHistory(){
        return $this->hasMany(SubscriptionPaymentHistory::class, 'employer_id');
    }

    // for user report
    public function reportsMade(){
        return $this->hasMany(Report::class , 'complainant_user_id');
    }

    public function reportsAgainstMe() {
        return $this->hasMany(Report::class, 'reported_user_id');
    }

    // for job post report
    public function reportsMadeToJobPost(){
        return $this->hasMany(ReportJobPost::class , 'complainant_user_id');
    }

     public function receivedReviews(){
        return $this->hasMany(Review::class,'reviewee_id');
    }

    public function myReviews(){
        return $this->hasMany(Review::class,'reviewer_id');
    }


}
