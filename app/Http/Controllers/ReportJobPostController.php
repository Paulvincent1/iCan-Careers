<?php

namespace App\Http\Controllers;

use App\Models\JobPost;
use App\Models\ReportJobPost;
use App\Models\User;
use App\Notifications\AdminReportJobPostNotification;
use Dacastro4\LaravelGmail\Facade\LaravelGmail;
use Dacastro4\LaravelGmail\Services\Message\Mail;
use Illuminate\Http\Request;

class ReportJobPostController extends Controller
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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, JobPost $jobpostId)
    {
        $request->validate([
            'reason' => 'required'
        ]);

        // dd('s');

        $user = $request->user();
        $user->reportsMadeToJobPost()->create([
            'reason' => $request->reason,
            'reported_job_post_id' => $jobpostId->id,
        ]);

        $admin = User::whereHas('roles', function($query) {
            $query->where('name', 'Admin');
        })->first();

        $admin->notify(new AdminReportJobPostNotification(admin:$admin,user:$user));
        // broadcast(new AdminReportJobPostNotification(admin:$admin,user:$user));

        // ✅ Send Gmail notification
        try {
            $token = LaravelGmail::makeToken(); // Generate Gmail token

            $mail = new Mail();
            $mail->using($token['access_token'])
                ->to($admin->email, $admin->name)
                ->from('icancareers2@gmail.com', 'iCan Careers')
                ->subject('A Job Post Has Been Reported')
                ->view('mail.report-job', [
                    'admin' => $admin,
                    'user' => $user,
                    'jobPost' => $jobpostId,
                    'reason' => $request->reason,
                ])
                ->send();

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['email' => 'Email sending failed: ' . $e->getMessage()]);
        }


        return redirect()->back()->with('messageProp','Thank you for reporting this user! We will review the behaviour of this user.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ReportJobPost $reportJobPost)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ReportJobPost $reportJobPost)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ReportJobPost $reportJobPost)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReportJobPost $reportJobPost)
    {
        //
    }
}
