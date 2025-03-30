<?php

namespace App\Http\Controllers;

use App\Models\EmployerProfile;
use App\Models\EmployerSubscription;
use App\Models\JobPost;
use App\Models\Report;
use App\Models\ReportJobPost;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Salary;
use App\Models\SubscriptionPaymentHistory;
use App\Models\WorkerVerification;
use App\Notifications\WorkerVerificationNotification;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redirect;

class AdminDashboardController extends Controller
{
    public function index()
{
    $salary = Salary::sum('total_earnings'); // Total earnings

    $date = EmployerSubscription::find(1);
 // Get today's date
    $today = \Carbon\Carbon::today();
     // Count total entities
    $totalWorkers = User::whereHas('roles', function ($query) {
        $query->where('name', 'Senior')->orWhere('name', 'PWD');
    })->count();
     // Count daily entities (records created today)
    $dailyWorkers = User::whereHas('roles', function ($query) {
        $query->where('name', 'Senior')->orWhere('name', 'PWD');
    })->whereDate('created_at', $today)->count();

    $totalEmployers = EmployerProfile::with('user')
    ->whereHas('user.roles', function ($query) {
            $query->where('name', '!=', 'Admin');
    })->count();
         $dailyEmployers = EmployerProfile::with('user')
     ->whereHas('user.roles', function ($query) {
            $query->where('name', '!=', 'Admin');
    })->whereDate('created_at', $today)->count();
    $totalJobs = JobPost::count();
    $dailyJobs = JobPost::whereDate('created_at', $today)->count();

     $dailyApplications = DB::table('application')
        ->whereDate('created_at', $today)
        ->count();
        $totalApplications = DB::table('application')->count();

     // Group reported users by date
    $reportedUsers = Report::selectRaw('DATE(created_at) as date, COUNT(*) as count')
        ->groupBy('date')
        ->orderBy('date')
        ->get();

    // Group reported job posts by date
    $reportedPosts = ReportJobPost::selectRaw('DATE(created_at) as date, COUNT(*) as count')
        ->groupBy('date')
        ->orderBy('date')
        ->get();


// Calculate total earnings
            // Adjust column if needed

            // Calculate daily earnings for the Pie Chart
            $dailyEarnings = SubscriptionPaymentHistory::selectRaw('DATE(created_at) as day, SUM(amount) as total')
                ->where('created_at', '>=', \Carbon\Carbon::now()->subDays(30))
                ->groupBy('day')
                ->orderBy('day')
                ->get();

            // Extract days and earnings values
            $days = $dailyEarnings->pluck('day')->map(fn($date) => \Carbon\Carbon::parse($date)->format('M d'));
            $earnings = $dailyEarnings->pluck('total');

    return Inertia::render('Admin/Dashboard', [
    'salaryProps' => [
        'total_earnings' => $salary,
        'days' => $days,
        'dailyEarnings' => $earnings,
    ],
    'applicationProps' => [
            'total' => $totalApplications,
            'daily' => $dailyApplications,
        ],
    'jobProps' => [
            'total' => $totalJobs,
            'daily' => $dailyJobs,
        ], // Send count instead of a single job
    'dateProps' => $date,
    'workerProps' => [
            'total' => $totalWorkers,
            'daily' => $dailyWorkers,
        ], // Send count instead of list
    'employerProps' => [
            'total' => $totalEmployers,
            'daily' => $dailyEmployers,
        ],
     'reportedUsersData' => $reportedUsers,
        'reportedPostsData' => $reportedPosts,
]);
}

    public function workers()
    {
        // Fetch all workers (Seniors or PWDs) with verification data
        $workers = User::with('workerVerification') // Eager load workerVerification
            ->whereHas('roles', function ($query) {
                $query->where('name', 'Senior')->orWhere('name', 'PWD');
            })
            ->get();

        // Map through workers to include worker_verification id
        $workers->map(function ($worker) {
            // Add the worker_verification ID to the worker model
            $worker->verification_id = $worker->workerVerification ? $worker->workerVerification->id : null;
            return $worker;
        });

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

        if(!$worker->verified){

            $worker->notify(new WorkerVerificationNotification(worker:$worker,verified:true));
            broadcast(new WorkerVerificationNotification(worker:$worker,verified:true));

        }
        $worker->verified = !$worker->verified; // Toggle between true/false
        $worker->save();

        return back()->with('success', 'Worker verification status updated.');
    }



    public function deleteVerification($id)
    {
        $worker = WorkerVerification::where('user_id', $id)->first();


        $userWorker = User::findOrFail($id);

        if (!$worker) {
            return redirect()->back()->with([
                'error' => 'Nothing to delete.'
            ]);
        }
        $worker->delete();  // Try deleting the record



        $userWorker->notify(new WorkerVerificationNotification(worker:$userWorker,verified:false));
        broadcast(new WorkerVerificationNotification(worker:$userWorker,verified:false));


        return redirect()->back()->with(['message' => 'Successfuly updated!']);
    }
    public function employers()
    {
        $employers = EmployerProfile::with(['user', 'businessInformation'])->whereHas('user.roles', function ($query) {
            $query->where('name', '!=', 'Admin');
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
        $reports = Report::with(['reported', 'reporter'])->latest()->get();

        return Inertia::render('Admin/ReportedUsers', [
            'reports' => $reports
        ]);
    }


    public function toggleBan($id)
    {
        $user = User::find($id); // Find the reported user directly

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->ban = !$user->ban; // Toggle ban status
        $user->save();

        return redirect()->back()->with('success', 'User ban status updated successfully.');
    }


    public function reportedPosts()
    {
        $reportPosts = ReportJobPost::with(['reporter', 'reportedJobPost'])->latest()->get();
        $user = Auth::user();

        return Inertia::render('Admin/ReportedPosts', [
            'reports' => $reportPosts,
            'userProps' => $user,
        ]);
    }



    public function banJob($id)
    {
        $report = ReportJobPost::with('reportedJobPost.employer')->findOrFail($id);

        if (!$report->reportedJobPost) {
            return Redirect::back()->with('error', "❌ No reported job post found for report ID: $id");
        }

        if (!$report->reportedJobPost->employer) {
            return Redirect::back()->with('error', "❌ No employer found for job post ID: {$report->reportedJobPost->id}");
        }

        // Toggle employer ban status
        $employer = $report->reportedJobPost->employer;
        $employer->ban = !$employer->ban;
        $employer->save();

        // Fetch updated reports
        $reports = ReportJobPost::with(['reportedJobPost.employer'])->get();

        // ✅ Redirect back with updated reports
        return Inertia::render('Admin/ReportedPosts', [
            'reports' => $reports
        ])->with('success', 'Ban status updated successfully.');
    }


    public function showReportedJob($id)
    {
        $job = JobPost::with('employer')->findOrFail($id);

        // Ensure location is properly formatted
        if (is_string($job->location)) {
            $job->location = json_decode($job->location, true); // Decode only if it's a string
        }

        return Inertia::render('Admin/ReportedPostsDetails', [
            'job' => $job
        ]);
    }
    // public function banJob(Request $request, $id)
    // {
    //     if ($request->method() !== 'PUT') {
    //         return response()->json(['error' => 'Method Not Allowed'], 405);
    //     }

    //     $report = ReportJobPost::with('reportedJobPost.employer')->findOrFail($id);

    //     if ($report->reportedJobPost && $report->reportedJobPost->employer) {
    //         $employer = $report->reportedJobPost->employer;
    //         $employer->update(['ban' => !$employer->ban]); // Toggle employer ban/unban
    //     }

    //     $report->update(['ban' => !$report->ban]);

    //     return response()->json([
    //         'success' => 'Employer ban status updated.',
    //         'report' => $report,
    //     ]);
    // }



    public function deleteJob($id)
    {
        $job = JobPost::findOrFail($id);
        $job->delete();

        return redirect()->route('admin.reported.posts')->with('success', 'Job post deleted successfully.');
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
            // Fetch payment history with employer details
            $subscriptionPaymentHistory = SubscriptionPaymentHistory::query()
                ->with('employer')
                ->latest()
                ->get();

            // Calculate total earnings
            $totalEarnings = SubscriptionPaymentHistory::sum('amount'); // Adjust column if needed

            // Calculate daily earnings for the Pie Chart
            $dailyEarnings = SubscriptionPaymentHistory::selectRaw('DATE(created_at) as day, SUM(amount) as total')
                ->where('created_at', '>=', \Carbon\Carbon::now()->subDays(30))
                ->groupBy('day')
                ->orderBy('day')
                ->get();

            // Extract days and earnings values
            $days = $dailyEarnings->pluck('day')->map(fn($date) => \Carbon\Carbon::parse($date)->format('M d'));
            $earnings = $dailyEarnings->pluck('total');

                // Count employers with at least one subscription
    $subscribedEmployersCount = SubscriptionPaymentHistory::query()
    ->with('employer')->count();

    // Count employers without any subscriptions
    $nonSubscribedEmployersCount = SubscriptionPaymentHistory::query()
    ->with('employer')->count();

            return Inertia::render('Admin/PaymentHistory', [
                'subscriptionPaymentHistoryProps' => $subscriptionPaymentHistory,
                'salaryProps' => [
                    'total_earnings' => $totalEarnings,
                    'days' => $days,
                    'dailyEarnings' => $earnings,
                ],
                 'subscribedUsersData' => [
            ['name' => 'Subscribed Employers', 'value' => $subscribedEmployersCount],
            ]
            ]);
        }


    public function subscribeUsers()
    {
        // Fetch employer subscriptions along with employer details
        $subscribedUsers = EmployerSubscription::with('employer:id,name,email')->whereHas('employer.roles', function ($query) {
            $query->where('name', '!=', 'Admin');
        })
            ->select('id', 'subscription_type', 'start_date', 'expiry_date', 'employer_id')
            ->get();

        return Inertia::render('Admin/SubscribeUser', [
            'subscribedUsers' => $subscribedUsers
        ]);
    }



    public function viewProfile(User $id){


        // worker
        if($id->roles()->first()->name != 'Employer'){

            if(!$id->workerProfile){
                return redirect()->back();
            }

            $workerSkills = $id->workerSkills;
            $workerProfile = $id->workerProfile;

            return inertia('Worker/Profile',['userProp' => $id,
            'workerSkillsProp' => $workerSkills,
            'workerProfileProp' => $workerProfile,
            'messageProp' => session('message'),
            'visitor' => true,
            'adminVisit' => true,
            ]);
        }

        //employer
        if($id->roles()->first()->name === 'Employer'){

            if(!$id->employerProfile){
                return redirect()->back();
            }

            $employerProfile = $id->employerProfile;
            $jobsPosted = JobPost::where('employer_id', $id->id)->get();
            $business = $id->employerProfile?->businessInformation;
            $subscription = EmployerSubscription::where('employer_id', $id->id)->first();

            return inertia('Employer/Profile', [
            "user" => $id,
            'employerProfileProp' => $employerProfile,
            'businessProps' => $business ?? null,
            'messageProp' => session('message'),
            'jobsPostedProps' => $jobsPosted, // Pass multiple jobs
            'subscriptionProps' => $subscription,
            'visitor' => true,
            'adminVisit' => true,
            ]);
        }


    }
}
