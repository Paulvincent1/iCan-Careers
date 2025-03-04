<?php

namespace App\Http\Controllers;

use App\Models\EmployerProfile;
use App\Models\EmployerSubscription;
use App\Models\JobPost;
use App\Models\User;
use App\Models\Salary;
use App\Models\SubscriptionPaymentHistory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class AdminDashboardController extends Controller
{
    public function index()
{
    $salary = Salary::sum('total_earnings'); // Total earnings
    $job = JobPost::find(1);
    $application = DB::table('application')->get();
    $date = EmployerSubscription::find(1);
    
    // Count users except admins
    $userCount = User::whereDoesntHave('roles', function ($query) {
        $query->where('name', 'Admin');
    })->count();

    // Earnings grouped by month
    $monthlyEarnings = Salary::selectRaw('MONTH(created_at) as month, SUM(total_earnings) as total')
        ->groupBy('month')
        ->orderBy('month')
        ->get();

    // Convert month numbers to names (e.g., 1 -> Jan, 2 -> Feb)
    $months = $monthlyEarnings->map(function ($item) {
        return Carbon::create()->month($item->month)->format('M'); // "Jan", "Feb", etc.
    });

    $earnings = $monthlyEarnings->pluck('total');

    return Inertia::render('Admin/Dashboard', [
        'salaryProps' => [
            'total_earnings' => $salary,
            'months' => $months,
            'monthlyEarnings' => $earnings,
        ],
        'userCountProps' => $userCount,
        'applicationProps' => $application,
        'jobProps' => $job,
        'dateProps' => $date,
    ]);
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
