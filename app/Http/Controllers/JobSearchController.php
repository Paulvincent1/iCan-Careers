<?php

namespace App\Http\Controllers;

use App\Models\JobPost;
use Illuminate\Http\Request;

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

        $jobs = JobPost::with(['employer.businessInformation','employer.employerProfile'])->filter(request(['job_type','work_arrangement','experience','job_title']))->latest()->get();
      

        // dd($jobs);
        // $jobs = JobPost::whereIn('job_type',$jobType)->orWhereIn('work_arrangement', $workArrangement)
        // ->orWhereIn('experience', $experience)->get();

        // JobPost::whereIn('job_title',$jobType)->orWhere(function ($query) use ($workArrangement) {
        //     $query->whereIn('work_arrangement', $workArrangement);
        // })->orWhere(function ($query) use ($experience) {
        //     $query->whereIn('experience', $experience);
        // })->get();

        // dd($jobs);
       
        return inertia('Worker/FindJobs',['jobsProps' => $jobs]);
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
    public function show(string $id)
    {
        //
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
