<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WorkerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

use function Illuminate\Log\log;

class WorkerProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function createProfile()
    {
        $user = Auth::user();
        if($user->workerProfile){
     
            return redirect()->route('add.skills');
        }
        
        return inertia('WorkerAccountSetup/CreateProfile');
    }

    public function storeProfile(Request $request){
        // dd($request);
        $fields = $request->validate([
            'job_title' =>'required|max:255',
            'profile_description' =>'required',
            'highest_educational_attainment' =>'required',
            'job_type' =>'required',
            'work_hour_per_day' =>'required|numeric|min:1',
            'hour_pay' =>'required|numeric|min:1',
            'month_pay' =>'required|numeric|min:1',
            'birth_year' =>'required|numeric|min:1900',
            'gender' =>'required',
          
        ]);

        $request->user()->workerProfile()->create($fields);
      
        Inertia::clearHistory();

        return redirect()->route('add.skills');
    }


    public function myProfile(){
        $user = Auth::user();
        $workerSkills = $user->workerSkills;
        $workerProfile = $user->workerProfile;
        // dd($workerProfile);
        return inertia('Worker/Profile',['userProp' => $user, 'workerSkillsProp' => $workerSkills, 'workerProfileProp' => $workerProfile, 'messageProp' => session('message')]);
    }

    public function updateProfile(Request $request){
        $user = Auth::user();
        if($request->job_title){
            $user->workerProfile->update([
                'job_title' => $request->job_title
            ]);
        }

        // dd($request);

        return redirect()->back()->with(['message' => 'Successfuly updated!']);
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
    public function show(WorkerProfile $workerProfile)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WorkerProfile $workerProfile)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WorkerProfile $workerProfile)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WorkerProfile $workerProfile)
    {
        //
    }
}
