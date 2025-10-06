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
        if ($user->workerProfile) {

            return redirect()->route('add.skills');
        }

        return inertia('WorkerAccountSetup/CreateProfile');
    }

    public function storeProfile(Request $request)
    {
        $fields = $request->validate([
            'job_title' => 'required|max:255',
            'profile_description' => 'required',
            'highest_educational_attainment' => 'required',
            'job_type' => 'required',
            'work_hour_per_day' => 'required|numeric|min:1',
            'hour_pay' => 'required|numeric|min:1',
            'month_pay' => 'required|numeric|min:1',
            'birth_year' => 'required|numeric|min:1900|max:' . now()->year,
            'gender' => 'required',
            'resume' => 'nullable|file|extensions:pdf,docx,doc',
        ]);

        $user = $request->user();

        // ✅ Enforce Senior Citizen rule
        if ($user->roles()->where('name', 'Senior')->exists()) {
            $age = now()->year - $fields['birth_year'];
            if ($age < 60) {
                return back()->withErrors([
                    'birth_year' => 'Senior citizens must be at least 60 years old.',
                    'message' => 'Senior Citizens must be at least 60 years old.'
                ])->withInput();
            }
        }

        // Handle Resume
        $resumepath = null;
        if ($request->hasFile('resume')) {
            $resumepath = Storage::disk('local')->put('resume', $request->resume);
        }

        // Save Profile
        $user->workerProfile()->create([
            'job_title' => $fields['job_title'],
            'profile_description' => $fields['profile_description'],
            'highest_educational_attainment' => $fields['highest_educational_attainment'],
            'job_type' => $fields['job_type'],
            'work_hour_per_day' => $fields['work_hour_per_day'],
            'hour_pay' => $fields['hour_pay'],
            'month_pay' => $fields['month_pay'],
            'birth_year' => $fields['birth_year'],
            'gender' => $fields['gender'],
            'resume' => $request->resume?->getClientOriginalName() ?? null,
            'resume_path' => $resumepath,
        ]);

        Inertia::clearHistory();

        return redirect()->route('add.skills');
    }


    public function myProfile()
    {
        $user = Auth::user();
        if (!$user->workerProfile) {
            return redirect()->route('create.profile');
        }
        $appliedJobs = $user->appliedJobs()->with(['employer.employerProfile.businessInformation'])->latest()->get();
        $workerSkills = $user->workerSkills;
        $workerProfile = $user->workerProfile;
        $workerBasicInfo = $user->workerBasicInfo;

        $recentReview = $user->receivedReviews()->with('reviewer')->orderBy('created_at', 'desc')->first();

        $sumStars = 0;
        $receivedReviews = $user->receivedReviews()->get();
        $reviewCount = $receivedReviews->count();

        $receivedReviews->pluck('star')
            ->each(function ($e) use (&$sumStars) {
                $sumStars += $e;
            });

        if ($sumStars) {
            $averageStar = $sumStars / $reviewCount;

            $roundedAverageStar = round($averageStar, 2);
        }



        return inertia('Worker/Profile', [
            'userProp' => $user,
            'workerSkillsProp' => $workerSkills,
            'workerProfileProp' => $workerProfile,
            'appliedJobsProps' => $appliedJobs,
            'workerBasicInfoProp' => $workerBasicInfo,
            'messageProp' => session()->get('message'),
            'averageStar' => $roundedAverageStar ?? 0.00,
            'recentReview' => $recentReview
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        // ✅ Check Senior role if updating birth_year
        if ($request->has('birth_year')) {
            $request->validate([
                'birth_year' => 'required|numeric|min:1900|max:' . now()->year,
            ]);

            if ($user->roles()->where('name', 'Senior')->exists()) {
                $age = now()->year - $request->birth_year;
                if ($age < 60) {
                    return back()->withErrors([
                        'birth_year' => 'Senior Citizens must be at least 60 years old.',
                    ])->withInput();
                }
            }

            $user->workerProfile->update([
                'birth_year' => $request->birth_year
            ]);
        }

        // Update gender if present
        if ($request->has('gender')) {
            $request->validate([
                'gender' => 'required|string'
            ]);
            $user->workerProfile->update([
                'gender' => $request->gender
            ]);
        }

        // Update educational attainment if present
        if ($request->has('highest_educational_attainment')) {
            $request->validate([
                'highest_educational_attainment' => 'required|string'
            ]);
            $user->workerProfile->update([
                'highest_educational_attainment' => $request->highest_educational_attainment
            ]);
        }

        if ($request->job_title) {
            $request->validate([
                'job_title' => 'min:1'
            ]);
            $user->workerProfile->update([
                'job_title' => $request->job_title
            ]);
        }

        if ($request->job_type && $request->work_hour_per_day && $request->hour_pay && $request->month_pay) {
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

        if ($request->profile_description) {
            $request->validate([
                'profile_description' => 'required'
            ]);
            $user->workerProfile->update([
                'profile_description' => $request->profile_description
            ]);
        }

        // ✅ Update address and website link in worker_basic_infos table
        if ($request->has('address') || $request->has('website_link')) {
            // Get or create worker basic info
            $workerBasicInfo = $user->workerBasicInfo ?? new WorkerBasicInfo(['worker_id' => $user->id]);

            if ($request->has('address')) {
                $workerBasicInfo->address = $request->address ?: null;
            }

            if ($request->has('website_link')) {
                $workerBasicInfo->website_link = $request->website_link ?: null;
            }

            $workerBasicInfo->save();
        }

        if ($request->hasFile('profile_img')) {
            $request->validate([
                'profile_img' => 'required|image'
            ]);

            if ($user->profile_img) {
                $relativePath = str_replace('/storage/', '', $user->profile_img);
                if (Storage::disk('public')->exists($relativePath)) {
                    Storage::disk('public')->delete($relativePath);
                }
            }

            $path = Storage::disk('public')->put('images', $request->profile_img);

            $user->update([
                'profile_img' => '/storage/' . $path
            ]);
        }

        if ($request->hasFile('resume')) {
            $request->validate([
                'resume' => 'nullable|file|extensions:pdf,docx,doc',
            ]);

            $existingResumePath = $user->workerProfile->resume_path;

            if ($existingResumePath) {
                if (Storage::disk('local')->exists($existingResumePath)) {
                    Storage::disk('local')->delete($existingResumePath);
                }
            }

            $resumePath = Storage::disk('local')->put('resume', $request->resume);

            $user->workerProfile->update([
                'resume' => $request->resume?->getClientOriginalName() ?? null,
                'resume_path' => $resumePath,
            ]);
        }

        return redirect()->back()->with(['message' => 'Successfully updated!']);
    }
    public function updateSkill(Request $request, WorkerSkills $skillid)
    {
        $user = Auth::user();

        if ($request->skill_name) {

            $request->validate(
                [
                    'skill_name' => 'max:255'
                ]
            );


            $user->workerSkills()->find($skillid->id)->update([
                'skill_name' => $request->skill_name
            ]);
        }



        if ($request->experience) {
            $request->validate([
                'experience' => 'max:255'
            ]);


            $user->workerSkills()->find($skillid->id)->update(
                [
                    'experience' => $request->experience
                ]
            );
        }

        if ($request->rating) {
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

    public function deleteSkill(WorkerSkills $skillid)
    {

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
    public function show(User $id)
    {
        $user = Auth::user();
        $isEmployed = null;

        if ($user->roles()->first()->name === 'Employer') {
            $isEmployed = $id->myJobs()->where('current', true)->whereHas('employer', function ($query) use ($user) {
                $query->where('id', $user->id);
            })->get();
        }

        $workerSkills = $id->workerSkills;
        $workerProfile = $id->workerProfile;

        // ✅ FIX: Load workerBasicInfo for visitors
        $workerBasicInfo = $id->workerBasicInfo;

        if ($id->id === $user->id) {
            $visitor = false;
        } else {
            $visitor = true;
        }

        $recentReview = $id->receivedReviews()->with('reviewer')->orderBy('created_at', 'desc')->first();

        $sumStars = 0;
        $receivedReviews = $id->receivedReviews()->get();
        $reviewCount = $receivedReviews->count();

        $receivedReviews->pluck('star')
            ->each(function ($e) use (&$sumStars) {
                $sumStars += $e;
            });

        if ($sumStars) {
            $averageStar = $sumStars / $reviewCount;
            $roundedAverageStar = round($averageStar, 2);
        }

        $appliedJobs = $id->appliedJobs()->with([
            'employer.employerProfile.businessInformation'
        ])->get();

        return inertia('Worker/Profile', [
            'userProp' => $id,
            'workerSkillsProp' => $workerSkills,
            'appliedJobsProps' => $appliedJobs,
            'workerProfileProp' => $workerProfile,
            'workerBasicInfoProp' => $workerBasicInfo, // ✅ Now this will be available for visitors
            'messageProp' => session('message'),
            'visitor' => $visitor,
            'currentlyEmployedByMeProp' => $isEmployed,
            'recentReview' => $recentReview,
            'averageStar' => $roundedAverageStar ?? 0.00
        ]);
    }

    public function showResume(string $path, User $workerId)
    {
        $user = Auth::user();

        //    dd($user->employerJobPosts()->whereHas('usersWhoApplied', function ($query) use ($workerId){
        //     $query->where('worker_id',4);
        //    })->first());

        //    dd($user->workerProfile->resume_path === $path);
        // dd($workerId);

        if ($user->workerProfile?->resume_path === $path || $user->employerJobPosts()->whereHas('usersWhoApplied', function ($query) use ($workerId) {
            $query->where('worker_id', $workerId->id);
        })->first() || $user->roles()->first()->name === 'Admin') {
            return Storage::disk('local')->response($path);
        }

        abort(403, "You're not authorize to view this");
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
