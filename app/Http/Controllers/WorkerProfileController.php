<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WorkerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
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
        // dd($user);
        return inertia('Worker/Profile',['userProp' => $user, 'workerSkillsProp' => $workerSkills, 'workerProfileProp' => $workerProfile, 'messageProp' => session('message')]);
    }

    public function updateProfile(Request $request){
        $user = Auth::user();
        if($request->job_title){
            $request->validate([
                'job_title' => 'min:1'
            ]);
            $user->workerProfile->update([
                'job_title' => $request->job_title
            ]);
        }
 
       
        if($request->job_type && $request->work_hour_per_day && $request->hour_pay && $request->month_pay){
            $request->validate([
                'job_type' => 'required',
                'work_hour_per_day' => 'required|numeric|min:1',
                'hour_pay' => 'required|numeric|min:1',
                'month_pay' => 'required|numeric|min:1',
            ]);

            $user->workerProfile->update([
                'job_type' => $request->job_type,
                'work_hour_per_day' => $request->work_hour_per_day,
                'hour_pay' => $request->hour_pay,
                'month_pay' => $request->month_pay,
            ]);
        }
        
        if($request->profile_description){
            
          $request->validate([
            'profile_description' => 'required'
          ]);
          $user->workerProfile->update([
            'profile_description'=>$request->profile_description
          ]);
        }
        

        
        if($request->hasFile('profile_img')){

            $request->validate([
                'profile_img' => 'required|image'
            ]);
            

            if($user->profile_img){
                $relativePath = str_replace('/storage/','',$user->profile_img);
                if(Storage::disk('public')->exists($relativePath)){
                    Storage::disk('public')->delete($relativePath);
                }
            }

            $path = Storage::disk('public')->put('images', $request->profile_img);

            $user->update([
                'profile_img' => '/storage/'.$path
            ]);
        }


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
