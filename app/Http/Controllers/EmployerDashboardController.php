<?php

namespace App\Http\Controllers;

use App\Models\JobPost;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class EmployerDashboardController extends Controller
{
    
    public function index(){

        $user = Auth::user();
        $jobs = JobPost::with(['usersWhoApplied', 'employedWorkers' => function ($query) {
            $query->wherePivot('current' , true);
        }])->where('employer_id',  $user->id)->latest()->get();

        $hiredWorkers = $jobs->pluck('employedWorkers')->flatten()->unique();
        
        $invoices =  $user->employerInvoices()->with('worker')->get();
        // dd($invoices);

        return inertia('Employer/Dashboard',['jobsProps' =>  $jobs, 'currentWorkerProps' => $hiredWorkers, 'invoiceProps' =>  $invoices, 'successMessage' => session()->get('successMessage')]);
    }

    public function showJobApplicants(Request $request, JobPost $jobid){

        Gate::authorize('view-applicants', [$jobid]);

        if($jobid->job_status === 'Pending'){
            abort(403, "You're not allowed to view this page");
        }
        // dd(now()->format('H:i'));
        // dd(Carbon::parse('2025-02-20' . ' 23:59:00'));

        $applicants = $jobid->usersWhoApplied()->with(['workerProfile'])->where('name' ,'like', '%' . $request->get('q') . '%')->get();

     
        // $applicantsCount = $jobid->usersWhoApplied()
        // ->selectRaw('status, count(*) as count')
        // ->where('application.job_post_id', $jobid->id)
        // ->groupBy('status')
        // ->get();
    
    
        $statusCounts = $jobid->usersWhoApplied()
        ->selectRaw('status, count(*) as count')
        ->wherePivot('job_post_id', $jobid->id)
        ->groupBy('status')
        ->pluck('count', 'status');
        
        // dd($statusCounts);
        
        return inertia('Employer/Applicants', 
            [
                'applicantsProps' => $applicants, 
                'statusCountProps' => $statusCounts ,
                'messageProp' => session()->get('message'),
                'jobProps' => $jobid,
            ]
        );
    }

    public function updateStatus(Request $request, int $pivotId){

        $update = DB::table('application')->where('id', $pivotId)->update(['status' => $request->status]);
        if($request->status === 'Accepted' && $update){
            $application = DB::table('application')->where('id', $pivotId)->first();
            $worker = User::find($application->worker_id);

            // dd('hi');
            $worker->myJobs()->attach($application->job_post_id);
        }

        return redirect()->back()->with('message','Successfully updated.');
    }

    public function addInterview(Request $request, int $pivotId) {
        // dd( Carbon::parse("$request->date $request->time"));

        if(Carbon::parse("$request->date $request->time")->isPast()){
            return redirect()->back()->withErrors(['message'=>'Please select a time valid time.']);
        }
       
        DB::table('application')->where('id',$pivotId)->update(['status' => 'Interview Scheduled','interview_schedule'=> Carbon::parse("$request->date $request->time")]);
//   dd( Carbon::parse("$request->date $request->time"));
        return redirect()->back()->with('message','Successfully updated');
    }

}
