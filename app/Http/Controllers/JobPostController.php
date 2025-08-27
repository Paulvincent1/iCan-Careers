<?php

namespace App\Http\Controllers;

use App\Models\JobPost;
use App\Models\User;
use App\Notifications\AdminFreeJobPostNotification;
use App\Notifications\FireWorkerNotification;
use App\Notifications\JobOpenedByAdminNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class JobPostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if(!Gate::allows('employer-profile-check')) {
            return redirect()->route('create.profile.employer');
        }
        $user = Auth::user();
        // dd($user->employerJobPosts()->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->count());
        $location = null;
        if ($user->employerProfile->employer_type === 'business') {
            $location = $user->employerProfile->businessInformation->business_location;
        }
        return inertia('Employer/CreateJob', ['locationProps' => $location]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        // dd($request);

        $user = Auth::user();
        $fields = $request->validate([
            'job_title' => 'required|max:255',
            'job_type' => 'required|max:255',
            'work_arrangement' => 'required|max:255',
            'location' => 'nullable',
            'experience' => 'required',
            'hour_per_day' => 'required|max:255',
            'hourly_rate' => 'required|max:255',
            'salary' => 'required|max:255',
            'description' => 'required|max:255',
            'preferred_educational_attainment' => 'required|max:255',
            'skills' => 'required',
            'preferred_worker_types' => 'required',
        ]);



        if ($fields['work_arrangement'] === 'On site' || $fields['work_arrangement'] === 'Hybrid') {
            $request->validate([
                'location' => 'required'
            ]);
        }

        $skills = [];
        foreach ($fields['skills'] as $skill) {
            $skills[] = $skill['name'];
        }
        // dd($user->employerSubscription->subscription_type);

        $jobImage = null;
        if ($request->hasFile('job_image')) {
            $jobImage = Storage::disk('public')->put('images', $request->job_image);
        }



        if ($user->employerSubscription->subscription_type === 'Free') {

            if (
                $user->employerJobPosts()->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
                ->count() < 3
            ) {
                $user->employerJobPosts()->create([
                    'job_title' =>  $fields['job_title'],
                    'job_type' =>  $fields['job_type'],
                    'work_arrangement' =>  $fields['work_arrangement'],
                    'location' =>  $fields['location'],
                    'experience' =>  $fields['experience'],
                    'hour_per_day' =>  $fields['hour_per_day'],
                    'hourly_rate' =>  $fields['hourly_rate'],
                    'salary' =>  $fields['salary'],
                    'description' =>  $fields['description'],
                    'preferred_educational_attainment' =>  $fields['preferred_educational_attainment'],
                    'skills' =>  $skills,
                    'preferred_worker_types' =>  $fields['preferred_worker_types'],
                    'job_status' =>  $user->employerSubscription->subscription_type === 'Free' ? 'Pending' : 'Open',
                    // 'job_image' => $
                ]);

                $admin = User::whereHas('roles', function($query) {
                    $query->where('name', 'Admin');
                })->first();

                $admin->notify(new AdminFreeJobPostNotification(admin:$admin,employer:$user));
                broadcast(new AdminFreeJobPostNotification(admin:$admin,employer:$user));


            }else{

                return redirect()->back()->withErrors(['message' => 'You can post up to 3 jobs per month(Free tier)']);

            }

        } else {

            if ($user->employerJobPosts()->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
                ->count() < 5
            ) {

                $user->employerJobPosts()->create([
                    'job_title' =>  $fields['job_title'],
                    'job_type' =>  $fields['job_type'],
                    'work_arrangement' =>  $fields['work_arrangement'],
                    'location' =>  $fields['location'],
                    'experience' =>  $fields['experience'],
                    'hour_per_day' =>  $fields['hour_per_day'],
                    'hourly_rate' =>  $fields['hourly_rate'],
                    'salary' =>  $fields['salary'],
                    'description' =>  $fields['description'],
                    'preferred_educational_attainment' =>  $fields['preferred_educational_attainment'],
                    'skills' =>  $skills,
                    'preferred_worker_types' =>  $fields['preferred_worker_types'],
                    'job_status' =>  $user->employerSubscription->subscription_type === 'Free' ? 'Pending' : 'Open',
                    // 'job_image' => $
                ]);
            }else {

                return redirect()->back()->withErrors(['message' => 'You can post up to 5 jobs per month(Pro and Premium tier)']);
            }
        }

        return redirect()->route('employer.dashboard');
    }

    /**
     * Display the specified resource.
     */
    public function show(JobPost $jobid)
    {

        $user = Auth::user();

        $jobid->load('employer.employerProfile.businessInformation');

        if ($jobid->employer_id != $user->id) {
            abort(403, 'Your not authorize to see this');
        }
        // dd($job);

        return inertia('ShowJob', ['jobPostProps' =>  $jobid, 'messageProp' => session()->get('messageProp')]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JobPost $jobid)
    {
        // dd($jobid);
        if($jobid->job_status === 'Closed'){
            return redirect()->back();
        }
        return inertia('Employer/CreateJob', ['jobPostProp' => $jobid, 'isEdit' => true]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JobPost $jobid)
    {

        // dd($request);

        $user = Auth::user();
        $fields = $request->validate([
            'job_title' => 'required|max:255',
            'job_type' => 'required|max:255',
            'work_arrangement' => 'required|max:255',
            'location' => 'nullable',
            'experience' => 'required',
            'hour_per_day' => 'required|max:255',
            'hourly_rate' => 'required|max:255',
            'salary' => 'required|max:255',
            'description' => 'required|max:255',
            'preferred_educational_attainment' => 'required|max:255',
            'skills' => 'required',
            'preferred_worker_types' => 'required',
        ]);



        if ($fields['work_arrangement'] === 'On site' || $fields['work_arrangement'] === 'Hybrid') {
            $request->validate([
                'location' => 'required'
            ]);
        }

        $skills = [];
        foreach ($fields['skills'] as $skill) {
            $skills[] = $skill['name'];
        }
        // dd($user->employerSubscription->subscription_type);


        $user->employerJobPosts()->where('id', $jobid->id)->update([
            'job_title' =>  $fields['job_title'],
            'job_type' =>  $fields['job_type'],
            'work_arrangement' =>  $fields['work_arrangement'],
            'location' =>  $fields['location'],
            'experience' =>  $fields['experience'],
            'hour_per_day' =>  $fields['hour_per_day'],
            'hourly_rate' =>  $fields['hourly_rate'],
            'salary' =>  $fields['salary'],
            'description' =>  $fields['description'],
            'preferred_educational_attainment' =>  $fields['preferred_educational_attainment'],
            'skills' =>  $skills,
            'preferred_worker_types' =>  $fields['preferred_worker_types'],
            'job_status' =>  $user->employerSubscription->subscription_type === 'Free' ? 'Pending' : 'Open'
        ]);

        return redirect()->route('employer.dashboard')->with('successMessage', 'Sucessfully updated!');
    }

    public function updateJobStatus(Request $request, $id)
    {
        $jobPost = JobPost::findOrFail($id);
        $jobPost->update(['job_status' => $request->status]);

        $employer = User::whereHas('roles', function ($query) {
            $query->where('name', 'Employer');
        })->whereHas('employerJobPosts',function ($query) use($jobPost){
            $query->where('id',  $jobPost->id);
        })->first();

        $employer->notify(new JobOpenedByAdminNotification(employer:$employer,jobPost:$jobPost));
        broadcast(new JobOpenedByAdminNotification(employer:$employer,jobPost:$jobPost));

        return redirect()->route('admin.job.approvals')->with('success', 'Job status updated successfully.');
    }

    public function showJob($id)
    {
        $job = JobPost::with('employer')->findOrFail($id);

        // Ensure location is properly formatted
        if (is_string($job->location)) {
            $job->location = json_decode($job->location, true); // Decode only if it's a string
        }

        return Inertia::render('Admin/JobPostDetails', [
            'job' => $job
        ]);
    }



    public function closeJob(JobPost $jobid)
    {
        $user = Auth::user();

        if ($jobid->employer_id != $user->id) {

            abort(403, 'Your not authorize to see this.');
        }
        $jobid->update([
            'job_status' => 'Closed'
        ]);

        $jobid->usersWhoApplied()->wherePivotNotIn('status', ['Accepted', 'Rejected'])
            ->update([
                'status' => 'Rejected'
            ]);

        Inertia::clearHistory();
        return redirect()->route('employer.dashboard');
    }

    public function fireWorker(User $workerId, JobPost $jobPostId){

        // dd($workerId);
        $employer = Auth::user();

        $currentJob = $workerId->myJobs()->where('current', true)->where('job_post_id', $jobPostId->id)->whereHas('employer', function ($query) use($employer) {
            $query->where('id', $employer->id);
        })->first();

        if($currentJob){
            // expects the related model(myJobs) id not the pivot table id.
            $workerId->myJobs()->updateExistingPivot($currentJob->id, ['current' => false]);

            $workerId->notify(new FireWorkerNotification(employer:$employer,worker:$workerId));
            broadcast(new FireWorkerNotification(employer:$employer,worker:$workerId));
        }

        return redirect()->back()->with('message','Successfully ended the contract.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JobPost $jobPost)
    {
        //
    }
}
