<?php

namespace App\Http\Controllers;

use App\Models\JobPost;
use App\Notifications\WokerAppliesToJobPostNotification;
use Dacastro4\LaravelGmail\Facade\LaravelGmail;
use Dacastro4\LaravelGmail\Services\Message\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JobSearchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Build the query with filters
        $query = JobPost::with([
            'employer.employerProfile',
            'employer.employerProfile.businessInformation',
            'usersWhoSaved' => function ($query) use ($user) {
                $query->where('user_id', $user->id)->first();
            }
        ])->where('job_status', 'Open');

        // Apply filters
        $query->filter(request(['job_type','work_arrangement','experience','job_title']));

        // Get paginated results (10 items per page)
        $jobs = $query->latest()->paginate(10)->withQueryString();

        // dd($jobs); // You can remove this after testing

        return inertia('Worker/FindJobs', [
            'jobsProps' => $jobs->items(),
            'currentPage' => $jobs->currentPage(),
            'lastPage' => $jobs->lastPage(),
            'total' => $jobs->total(),
            'perPage' => $jobs->perPage(),
            'messageProp' => session()->get('messageProp')
        ]);
    }

    public function saveJob(JobPost $id){
        if($id->job_status === 'Pending'){
            abort(403, "You're not allowed to do this action");
        }

        $user = Auth::user();
        $job = $user->savedJobs()->where('job_post_id',$id->id)->first();


        if($job){
            $user->savedJobs()->detach($id);
            return redirect()->back()->with(['messageProp' => 'Successfuly saved!']);
        }

        $user->savedJobs()->attach($id);

        return redirect()->back()->with(['messageProp' => 'Successfuly saved!']);

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
    public function show(JobPost $id)
    {
        if($id->job_status === 'Closed'){
            return redirect()->back();
        }
        if($id->job_status === 'Locked'){
            return redirect()->back();
        }

        $user = Auth::user();
        $job = JobPost::with(['employer.employerProfile.businessInformation', 'usersWhoApplied' => function ($query) use($user) {
            $query->where('worker_id', $user->id)->first();
        },'usersWhoSaved' => function ($query) use($user) {
            $query->where('user_id',  $user->id)->first();
        }])->where('id', $id->id)->first();

        // dd($job);

        $interviewDetails = DB::table('application')->where('worker_id',$user->id)->where('job_post_id',$id->id)
        ->where('status','Interview Scheduled')->first();
        // dd($interviewDetails);

        return inertia('ShowJob',['jobPostProps' =>  $job, 'workerProfileProps' => $user->workerProfile,
        'interviewDetails' => $interviewDetails, 'messageProp' => session()->get('messageProp')]);
    }

    public function apply(JobPost $id){
        $user = Auth::user();

        if(!$id->usersWhoApplied()->where('worker_id',$user->id)->first() && $id->job_status != 'Closed'){
            $user->appliedJobs()->attach($id->id);

            $id->employer->notify(new WokerAppliesToJobPostNotification(applicant:$user,employer:$id->employer,jobPost:$id));
            // broadcast(new WokerAppliesToJobPostNotification(applicant:$user,employer:$id->employer,jobPost:$id));

            // ✅ Send Gmail email notification (added part)
            try {
                $token = LaravelGmail::makeToken(); // Generate Gmail token

                $mail = new Mail();
                $mail->using($token['access_token'])
                    ->to($id->employer->email, $id->employer->name)
                    ->from('icancareers2@gmail.com', 'iCan Careers')
                    ->subject('A Worker Has Applied to Your Job Post')
                    ->view('mail.worker-applied', [
                        'applicant' => $user,
                        'employer' => $id->employer,
                        'jobPost' => $id,
                    ])
                    ->send();

            } catch (\Exception $e) {
                return redirect()->back()->withErrors(['email' => 'Email sending failed: ' . $e->getMessage()]);
            }

            return redirect()->back()->with(['messageProp' => 'Successfuly applied!']);
        }
        // else{
        //     $user->appliedJobs()->detach($id->id);
        // }

        return redirect()->back();

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
