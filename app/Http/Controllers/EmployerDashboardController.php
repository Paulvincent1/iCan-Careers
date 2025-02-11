<?php

namespace App\Http\Controllers;

use App\Models\JobPost;
use App\Models\User;
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


        return inertia('Employer/Dashboard',['jobsProps' =>  $jobs, 'currentWorkerProps' => $hiredWorkers]);
    }

    public function showJobApplicants(Request $request, JobPost $jobid){

        Gate::authorize('view-applicants', [$jobid]);

        if($jobid->job_status === 'Pending'){
            abort(403, "You're not allowed to view this page");
        }

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

            $worker->myJobs()->attach($application->job_post_id);
        }

        return redirect()->back()->with('message','Successfully updated');
    }

}
