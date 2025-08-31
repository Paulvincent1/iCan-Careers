<?php

namespace App\Http\Controllers;

use App\Models\JobPost;
use App\Models\ReportJobPost;
use App\Models\User;
use App\Notifications\AdminReportJobPostNotification;
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
        broadcast(new AdminReportJobPostNotification(admin:$admin,user:$user));



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
