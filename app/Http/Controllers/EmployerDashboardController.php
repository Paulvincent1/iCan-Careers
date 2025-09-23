<?php

namespace App\Http\Controllers;

use App\Models\EmployerSubscription;
use App\Models\JobPost;
use App\Models\Message;
use App\Models\User;
use App\Notifications\HiringProcessNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Stevebauman\Location\Facades\Location;

class EmployerDashboardController extends Controller
{

    public function index(){

        $user = Auth::user();
        $jobs = JobPost::with(['usersWhoApplied', 'employedWorkers' => function ($query) {
            $query->wherePivot('current' , true);
        }])->where('employer_id',  $user->id)->latest()->get();

        $hiredWorkers = $jobs->pluck('employedWorkers')->flatten()->unique('id');
        // dd($hiredWorkers);
        $subscription = EmployerSubscription::where('employer_id', $user->id)->first();

        $invoices =  $user->employerInvoices()->with('worker')->get();
        // dd($invoices);

        // users that we interact
        $usersId = Message::where('sender_id',$user->id)->pluck('receiver_id')
        ->merge(Message::where('receiver_id',$user->id)->pluck('sender_id'))->unique()
        ->filter(function ($id) use ($user) {
            return $id != $user->id;
        });

        $users = User::whereIn('id',$usersId)->get();
        $chatHeads = [];

        foreach($users as $userchat){
            $sent = $userchat->sentMessages()->where('receiver_id',$user->id)->latest()->first();
            $received = $userchat->receivedMessages('sender_id',$user->id)->latest()->first();

            $latestMessage = null;
            if($sent && $received){
                if($sent->created_at > $received->created_at){

                    $latestMessage = $sent;
                }else{

                    $latestMessage = $received;
                }


            }elseif($sent){

                $latestMessage = $sent;

            }elseif($received){

                $latestMessage = $received;

            }


            $chatHeads[] = [
                'user' => $userchat,
                'latestMessage' => $latestMessage
            ];


        }

        usort($chatHeads, function ($a, $b){
           return $b['latestMessage']->created_at <=> $a['latestMessage']->created_at;
        });



        return inertia('Employer/Dashboard',['user' => [
            'name' => $user->name,

            'profile_photo_path' => $user->profile_img ?? null, // Ensure it's included
        ],'subscriptionProps' => $subscription,'jobsProps' =>  $jobs, 'currentWorkerProps' => $hiredWorkers, 'invoiceProps' =>  $invoices, 'successMessage' => session()->get('successMessage'), 'chatHeadsProps' => $chatHeads]);
    }

    public function showJobApplicants(Request $request, JobPost $jobid){

        Gate::authorize('view-applicants', [$jobid]);

        if($jobid->job_status === 'Pending'){
            abort(403, "You're not allowed to view this page");
        }
        // dd(now()->format('H:i'));
        // dd(Carbon::parse('2025-02-20' . ' 23:59:00'));

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
                'applicantsCount' => $applicants->count(),
                'statusCountProps' => $statusCounts ,
                'messageProp' => session()->get('message'),
                'jobProps' => $jobid,
            ]
        );
    }

    public function updateStatus(Request $request, int $pivotId){

        $request->validate(
            [
                'status' => 'required',
            ]
        );

        $update = DB::table('application')->where('id', $pivotId)->update(
            [
                'status' => $request->status,
            ]
        );

        $application = DB::table('application')->where('id', $pivotId)->first();
        $worker = User::find($application->worker_id);

        if($request->status === 'Accepted' && $update){

            // dd('hi');
            $worker->myJobs()->attach($application->job_post_id);
        }

        $jobPost = JobPost::where('id', $application->job_post_id)->first();
        // dd($jobPost);

        // for notif
        $worker->notify(new HiringProcessNotification(jobPost:$jobPost, applicant:$worker));
        broadcast(new HiringProcessNotification(jobPost:$jobPost, applicant:$worker));


        return redirect()->back()->with('message','Successfully updated.');
    }

        public function prevWorkers()
    {
        $user = Auth::user();

        $previousEmployerWithJobtitle = [];
        $user->employerJobPosts
            ->each(function ($job) use(&$previousEmployerWithJobtitle) {
               $job->employedWorkers()
                    ->orderBy('created_at', 'desc')
                    ->get()->each(
                    function($worker) use(&$previousEmployerWithJobtitle, $job) {
                        $previousEmployerWithJobtitle[] = [
                            'job' => [
                                'worker' => $worker,
                                'jobDetails' => $job
                            ]

                        ];
                    }
                );

            });

            // dd($previousEmployerWithJobtitle);


        return inertia('Employer/PreviousWorker', [
            'jobsProps' => $previousEmployerWithJobtitle ,
        ]);
    }


    public function addInterview(Request $request, int $pivotId) {
        // dd( Carbon::parse("$request->date $request->time"));
        $fields = $request->validate(
            [
                'date' => 'required',
                'time' => 'required',
                'interview_schedule' => 'nullable',
                'interview_mode' => 'nullable',
                'coordinates' => 'nullable|array',
            ]
        );
        $location = Location::get($request->ip());
        $timezone = $location ? $location->timezone : config('app.timezone', 'UTC');

        $interviewTime = Carbon::parse("$request->date $request->time", $timezone)->setTimezone('UTC');

        if ($interviewTime->lt(now()->addHours(12))) {
            return redirect()->back()->withErrors(['message' => 'Please select a time at least 12 hours from now.']);
        }

        DB::table('application')->where('id',$pivotId)
        ->update(
            [
                'status' => 'Interview Scheduled',
                'interview_schedule'=> $interviewTime,
                'interview_mode'=> $fields['interview_mode'],
                'coordinates'=>  $fields['coordinates'],
            ]
        );

        $application = DB::table('application')->where('id', $pivotId)->first();
        $worker = User::find($application->worker_id);

        $jobPost = JobPost::where('id', $application->job_post_id)->first();

         // for notif
         $worker->notify(new HiringProcessNotification(jobPost:$jobPost, applicant:$worker));
         broadcast(new HiringProcessNotification(jobPost:$jobPost, applicant:$worker));


//   dd( Carbon::parse("$request->date $request->time"));
        return redirect()->back()->with('message','Successfully updated');
    }

        public function preview($id)
        {
            $user = User::with([
                'roles',
                'workerProfile',
                'workerBasicInfo',
                'workerSkills',
                'employerProfile',
                'businessInformation',
                'businesses'
            ])
            ->withCount('receivedReviews')
            ->findOrFail($id);

            $averageStar = $user->receivedReviews()->avg('star');
            $role = $user->roles->pluck('name')->first(); // 👈 detect role

            $data = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'profile_img' => $user->profile_img,
                'created_at' => $user->created_at->diffForHumans(),
                'role' => $role,
                'average_star' => round($averageStar ?? 0, 2),
                'received_reviews_count' => $user->received_reviews_count,
            ];

            if ($role === 'Employer') {
                $data['company_name'] = $user->employerProfile->company_name ?? null;

                // collect industries from owned + linked businesses
                $industries = collect();
                if ($user->businessInformation) {
                    $industries->push($user->businessInformation->industry);
                }
                if ($user->businesses->isNotEmpty()) {
                    $industries = $industries->merge($user->businesses->pluck('industry'));
                }

                $addresses = collect();
                if ($user->businessInformation) {
                    $addresses->push($user->businessInformation->address ?? $user->businessInformation->business_location);
                }
                if ($user->businesses->isNotEmpty()) {
                    $addresses = $addresses->merge(
                        $user->businesses->map(fn($biz) => $biz->address ?? $biz->business_location)
                    );
                }

                $data['industries'] = $industries->filter()->unique()->values(); // array of industries
                $data['addresses'] = $addresses->filter()->unique()->values();   // array of addresses
            } else {
                $data['highest_educational_attainment'] = $user->workerProfile->highest_educational_attainment ?? null;
                $data['website_link'] = $user->workerBasicInfo->website_link ?? null;
                $data['address'] = $user->workerBasicInfo->address ?? null;
                $data['skills'] = $user->workerSkills->pluck('skill_name'); // optional
            }

            return response()->json($data);
        }

        public function previewJob($id)
        {
            $user = Auth::user();

            $job = JobPost::where('id', $id)
                ->withCount('usersWhoApplied')
                ->with([
                    'employer.businessInformation',  // one-to-one
                    'employer.businesses'            // many-to-many alias
                ])
                ->first();

            if (!$job) {
                return response()->json(['error' => 'Job not found or not yours'], 404);
            }

            $isOwner = $job->employer_id === $user->id;

            // Determine which business info to use (latest if exists)
            $businessInfo = $job->employer->businesses()
            ->orderByDesc('created_at')
            ->first();



            return response()->json([
                'id' => $job->id,
                'title' => $job->job_title,
                'job_type' => $job->job_type,
                'work_arrangement' => $job->work_arrangement,
                'experience' => $job->experience,
                'applications_count' => $job->users_who_applied_count,
                'hourly_rate' => $job->hourly_rate,
                'salary_per_month' => $job->salary,
                'location' => $job->location,
                'preferred_worker_types' => is_array($job->preferred_worker_types)
                    ? implode(', ', $job->preferred_worker_types)
                    : $job->preferred_worker_types,
                'job_status' => $job->job_status,
                'created_at' => $job->created_at->diffForHumans(),
                'is_owner' => $isOwner,
                'business' => [

                    'logo' => $businessInfo->business_logo ?? $job->employer->profile_img,
                ],
            ]);
        }



        public function previewBusiness($id)
        {
            $business = \App\Models\BusinessInformation::find($id);

            if (!$business) {
                return response()->json(['error' => 'Business not found'], 404);
            }

            return response()->json([
                'id' => $business->id,
                'business_logo' => $business->business_logo ?? '/assets/default_business_logo.png', // fallback image
                'business_name' => $business->business_name ?? 'N/A',
                'industry' => $business->industry ?? 'Not specified',
                'description' => $business->description ?? 'No description available',
                'created_at' => optional($business->created_at)->diffForHumans() ?? 'Unknown',
            ]);
        }




}
