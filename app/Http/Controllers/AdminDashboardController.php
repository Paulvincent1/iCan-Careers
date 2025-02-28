<?php

namespace App\Http\Controllers;

use App\Models\EmployerProfile;
use App\Models\EmployerSubscription;
use App\Models\User;
use App\Models\Salary;
use App\Models\SubscriptionPaymentHistory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $salary = Salary::find(1);
        // dd($salary);    

        $userCount = User::whereDoesntHave('roles', function ($query) {
            $query->where('name','Admin');
        })->count();

        // dd($userCount);
        return Inertia::render('Admin/Dashboard', ['salaryProps' => $salary, 'userCountProps' => $userCount]);
    }

    public function workers()
    {
        // Fetch all workers (Seniors or PWDs) with verification status
        $workers = User::whereHas('roles', function ($query) {
            $query->where('name', 'Senior')->orWhere('name', 'PWD');
        })->get();

        return Inertia::render('Admin/Workers', [
            'workers' => $workers
        ]);
    }

    public function workerVerificationDetails($id)
{
    // Fetch the worker with verification details
    $worker = User::with('workerVerification')->find($id);

    if (!$worker) {
        abort(404, 'Worker not found');
    }
    return Inertia::render('Admin/WorkerVerificationDetails', [
        'worker' => $worker,
        'verification' => $worker->workerVerification,
    ]);
}
    public function verifyWorker($id)
    {
        $worker = User::findOrFail($id);
        $worker->verified = true;
        $worker->save();

        return back()->with('success', 'Worker verified successfully.');
    }

    

    public function toggleVerification($id)
    {
        $worker = User::findOrFail($id);
        $worker->verified = !$worker->verified; // Toggle between true/false
        $worker->save();

         return back()->with('success', 'Worker verification status updated.');
    }

   public function employers()
{
    $employers = EmployerProfile::with(['user', 'businessInformation'])->whereHas('user.roles', function($query){
        $query->where('name','!=','Admin');
    })
        ->select('id', 'user_id', 'full_name', 'employer_type', 'business_id')
        ->get()
        ->map(function ($employer) {
            return [
                'id' => $employer->id,
                'user' => [
                    'username' => $employer->user->name ?? 'N/A',
                    'email' => $employer->user->email ?? 'N/A',
                ],
                'employer_type' => $employer->employer_type,
                'business_information' => [
                    'business_name' => $employer->businessInformation->business_name ?? 'N/A',
                ],
            ];
        });

    return Inertia::render('Admin/Employers', [
        'employers' => $employers
    ]);
}


    public function reportedUsers()
    {
        return Inertia::render('Admin/ReportedUsers');
    }

    public function jobApprovals()
{
    $jobs = \App\Models\JobPost::with('employer:id,email') // Fetch employer name
        ->select('id', 'job_title', 'job_status', 'employer_id')
        ->get();

    return Inertia::render('Admin/JobApprovals', [
        'jobs' => $jobs
    ]);
}




    public function paymentHistory()
    {
        $subscriptionPaymentHistory = SubscriptionPaymentHistory::query()->with('employer')->latest()->get();

        // dd($subscriptionPaymentHistory);    
        return Inertia::render('Admin/PaymentHistory',['subscriptionPaymentHistoryProps'=> $subscriptionPaymentHistory]);
    }

    public function subscribeUsers()
{
    // Fetch employer subscriptions along with employer details
    $subscribedUsers = EmployerSubscription::with('employer:id,name,email')->whereHas('employer.roles', function ($query) {
      $query->where('name', '!=','Admin');
    })
        ->select('id', 'subscription_type', 'start_date', 'expiry_date', 'employer_id')
        ->get();

    return Inertia::render('Admin/SubscribeUser', [
        'subscribedUsers' => $subscribedUsers
    ]);
}

}
