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
            $sent = $userchat->sentMessages()->latest()->first();
            $received = $userchat->receivedMessages()->latest()->first();

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

        if(Carbon::parse("$request->date $request->time")->isPast()){
            return redirect()->back()->withErrors(['message'=>'Please select a time valid time.']);
        }

        DB::table('application')->where('id',$pivotId)
        ->update(
            [
                'status' => 'Interview Scheduled',
                'interview_schedule'=> Carbon::parse("$request->date $request->time"),
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

}
