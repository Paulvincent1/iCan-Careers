<?php

namespace App\Http\Controllers;

use App\Models\JobPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class EmployerDashboardController extends Controller
{
    
    public function index(){

        $user = Auth::user();
        $jobs = JobPost::with(['usersWhoApplied'])->where('employer_id',  $user->id)->get();


        return inertia('Employer/Dashboard',['jobsProps' =>  $jobs]);
    }

    public function showJobApplicants(Request $request, JobPost $jobid){

        Gate::authorize('view-applicants', [$jobid]);

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
        // dd($pivotId);

        DB::table('application')->where('id', $pivotId)->update(['status' => $request->status]);

        return redirect()->back()->with('message','Successfully updated');
    }

}
