<?php

namespace App\Http\Controllers;

use App\Models\WorkerSkills;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkerSkillsController extends Controller
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
        if(!$user->workerProfile){
     
            return redirect()->route('create.profile');
        }

        if($user->workerSkills){
            return redirect()->route('worker.dashboard');
        }

        return inertia('WorkerAccountSetup/AddSkills');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    { 

        // dd($request);
       
        $skills = $request->validate([
            'skills'=> 'required|array',
            'skills.*.name' => 'required',
            'skills.*.experience' => 'nullable',
            'skills.*.star'=> 'required|numeric|min_digits:1|max_digits:5',
        ]);
        $user = Auth::user();
        foreach($skills['skills'] as $skill){
        //    dd($skill['experience']);
            $user->workerSkills()->create([
                'skill_name' => $skill['name'],
                'experience' => $skill['experience'],
                'rating' => $skill['star'],
            ]);
        }

        

        return redirect()->route('worker.dashboard');
       
    }

    /**
     * Display the specified resource.
     */
    public function show(WorkerSkills $workerSkills)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WorkerSkills $workerSkills)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WorkerSkills $workerSkills)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WorkerSkills $workerSkills)
    {
        //
    }
}
