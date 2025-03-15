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
        if ($worker->verified) {
            $worker->notify(new WorkerVerificationNotification(worker: $worker, verified: true));
            broadcast(new WorkerVerificationNotification(worker: $worker, verified: true));
        }
        $worker->verified = !$worker->verified; // Toggle between true/false
        $worker->save();

        return back()->with('success', 'Worker verification status updated.');
    }



    public function deleteVerification($id)
    {
        $worker = WorkerVerification::where('user_id', $id)->first();

        $userWorker = User::where('id', $id);

        if (!$worker) {
            return redirect()->back()->with([
                'error' => 'Nothing to delete.'
            ]);
        }
        $worker->delete();  // Try deleting the record

        $worker->notify(new WorkerVerificationNotification(worker: $userWorker, verified: false));
        broadcast(new WorkerVerificationNotification(worker: $userWorker, verified: false));

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
        $subscriptionPaymentHistory = SubscriptionPaymentHistory::query()->with('employer')->latest()->get();

        // dd($subscriptionPaymentHistory);
        return Inertia::render('Admin/PaymentHistory', ['subscriptionPaymentHistoryProps' => $subscriptionPaymentHistory]);
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
}
