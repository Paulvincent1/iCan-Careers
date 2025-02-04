<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WorkerProfile;
use App\Models\WorkerSkills;
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
            'resume' =>'nullable|file|extensions:pdf,docx,doc',
          
        ]);

        if($fields['resume']){
            $resumepath = Storage::disk('local')->put('resume', $request->resume);

        }

        $request->user()->workerProfile()->create([
            'job_title' => $fields['job_title'],
            'profile_description' => $fields['profile_description'],
            'highest_educational_attainment' => $fields['highest_educational_attainment'],
            'job_type' => $fields['job_type'],
            'work_hour_per_day' => $fields['work_hour_per_day'],
            'hour_pay' => $fields['hour_pay'],
            'month_pay' => $fields['month_pay'],
            'birth_year' => $fields['birth_year'],
            'gender' => $fields['gender'],
            'resume' => $resumepath ? $request->resume->getClientOriginalName() : null,
            'resume_path' => $resumepath  ?? null ,
        ]);
      
        Inertia::clearHistory();

        return redirect()->route('add.skills');
    }


    public function myProfile(){
        $user = Auth::user();
        if(!$user->workerProfile){
            return redirect()->route('create.profile');
        }
        $workerSkills = $user->workerSkills;
        $workerProfile = $user->workerProfile;
        
        return inertia('Worker/Profile',['userProp' => $user, 
         'workerSkillsProp' => $workerSkills,
         'workerProfileProp' => $workerProfile, 
         'messageProp' => session('message'),
        ]);
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

    public function updateSkill(Request $request, WorkerSkills $skillid){
       $user = Auth::user();
       
        if($request->skill_name){

            $request->validate(
                [
                    'skill_name' => 'max:255'
                    ]
                );


            $user->workerSkills()->find($skillid->id)->update([
                'skill_name' => $request->skill_name
            ]);
        }
    
       

        if($request->experience){
            $request->validate([
                'experience' => 'max:255'
            ]);
         

            $user->workerSkills()->find($skillid->id)->update(
                [
                    'experience' => $request->experience
                ]
            );
        }

        if($request->rating){
            $request->validate([
                'rating' => 'numeric|min:1|max:5'
            ]);

            $user->workerSkills()->find($skillid->id)->update(
                [
                    'rating' => $request->rating
                ]
            );
        }

        return redirect()->back()->with(['message' => 'Successfuly updated!']);
    }

    public function deleteSkill(WorkerSkills $skillid){

        $skillid->delete();


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

    public function showResume(string $path){
       $user = Auth::user();

    //    dd($user->workerProfile->resume_path === $path);

       if($user->workerProfile->resume_path === $path || $user->employerJobPosts()->where('employer_id',$user->id)->first()) { 
           return Storage::disk('local')->response($path);
        }

        abort(403,"You're not authorize to view this");
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
