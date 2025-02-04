<?php

namespace App\Http\Controllers;

use App\Models\JobPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobSearchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $jobType = $request->input('job_type') ?? [];
        // $workArrangement = $request->input('work_arrangement') ?? [];
        // $experience = $request->input('experience') ?? [];

        $user = Auth::user();
        $jobs = JobPost::with(['employer.employerProfile','employer.employerProfile.businessInformation','usersWhoSaved' => function ($query) use ($user) {
            $query->where('user_id',  $user->id)->first();
        }])->filter(request(['job_type','work_arrangement','experience','job_title']))->whereDoesntHave('usersWhoApplied', function ($query){
            $query->whereIn('status', ['Accepted']);
        })->latest()->get();
      

        // dd($jobs);
        // $jobs = JobPost::whereIn('job_type',$jobType)->orWhereIn('work_arrangement', $workArrangement)
        // ->orWhereIn('experience', $experience)->get();

        // JobPost::whereIn('job_title',$jobType)->orWhere(function ($query) use ($workArrangement) {
        //     $query->whereIn('work_arrangement', $workArrangement);
        // })->orWhere(function ($query) use ($experience) {
        //     $query->whereIn('experience', $experience);
        // })->get();

        // dd($jobs);

        // dd(session()->get('messageProp'));
      
       
        return inertia('Worker/FindJobs',['jobsProps' => $jobs, 'messageProp' => session()->get('messageProp')]);
    }

    public function saveJob(JobPost $id){

        $user = Auth::user();
        $job = $user->savedJobs()->where('job_post_id',$id->id)->first();
       
      
        if($job){
            $user->savedJobs()->detach($id);
            return redirect()->back()->with(['messageProp' => 'Successfuly saved!']);
        }

        $user->savedJobs()->attach($id);

        return redirect()->back()->with(['messageProp' => 'Successfuly saved!']);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(JobPost $id)
    {
        $user = Auth::user();
        $job = JobPost::with(['employer.employerProfile.businessInformation', 'usersWhoApplied' => function ($query) use($user) {
            $query->where('worker_id', $user->id)->first();
        },'usersWhoSaved' => function ($query) use($user) {
            $query->where('user_id',  $user->id)->first();
        }])->where('id', $id->id)->first();

        // dd($job);
 
        return inertia('Worker/ShowJob',['jobPostProps' =>  $job, 'workerProfileProps' => $user->workerProfile, 'messageProp' => session()->get('messageProp')]);
    }

    public function apply(JobPost $id){
        $user = Auth::user();

        if(!$id->usersWhoApplied()->where('worker_id',$user->id)->first()){
            $user->appliedJobs()->attach($id->id);
            return redirect()->back()->with(['messageProp' => 'Successfuly applied!']);
        }
        // else{
        //     $user->appliedJobs()->detach($id->id);
        // }
        
        return redirect()->back();

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
