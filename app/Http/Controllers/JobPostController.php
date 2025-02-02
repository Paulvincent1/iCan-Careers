<?php

namespace App\Http\Controllers;

use App\Models\JobPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $user = Auth::user();
        $location = null;
        if($user->employerProfile->employer_type === 'business'){
            $location = $user->employerProfile->businessInformation->business_location;
        }
        return inertia('Employer/CreateJob',['locationProps' => $location]);
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



        if($fields['work_arrangement'] === 'On site' || $fields['work_arrangement'] === 'Hybrid'){
            $request->validate([
                'location' => 'required'
            ]);
        }

        $skills = [];
        foreach($fields['skills'] as $skill){
            $skills[] = $skill['name'];
        }


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
        ]);

        return redirect()->route('employer.dashboard');
    }

    /**
     * Display the specified resource.
     */
    public function show(JobPost $jobPost)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JobPost $jobPost)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JobPost $jobPost)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JobPost $jobPost)
    {
        //
    }
}
