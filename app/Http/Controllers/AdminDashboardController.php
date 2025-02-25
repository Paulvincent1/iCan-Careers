<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminDashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Dashboard');
    }

    public function workers()
    {
        // Fetch all workers (Seniors or PWDs) with verification status
        $workers = User::whereHas('roles', function ($query) {
            $query->where('name', 'Senior')->orWhere('name', 'PWD');
        })->get();

        return Inertia::render('Admin/Workers', [
            'workers' => $workers
        ]);
    }

    public function workerVerificationDetails($id)
{
    // Fetch the worker with verification details
    $worker = User::with('workerVerification')->find($id);

    if (!$worker) {
        abort(404, 'Worker not found');
    }
    return Inertia::render('Admin/WorkerVerificationDetails', [
        'worker' => $worker,
        'verification' => $worker->workerVerification,
    ]);
}
    public function verifyWorker($id)
    {
        $worker = User::findOrFail($id);
        $worker->verified = true;
        $worker->save();

        return back()->with('success', 'Worker verified successfully.');
    }

    

    public function toggleVerification($id)
    {
        $worker = User::findOrFail($id);
        $worker->verified = !$worker->verified; // Toggle between true/false
        $worker->save();

         return back()->with('success', 'Worker verification status updated.');
    }

    public function employers()
    {
        return Inertia::render('Admin/Employers');
    }

    public function reportedUsers()
    {
        return Inertia::render('Admin/ReportedUsers');
    }

    public function jobApprovals()
{
    $jobs = \App\Models\JobPost::with('employer:id,name') // Fetch employer name
        ->select('id', 'job_title', 'job_status', 'employer_id')
        ->get();

    return Inertia::render('Admin/JobApprovals', [
        'jobs' => $jobs
    ]);
}




    public function paymentHistory()
    {
        return Inertia::render('Admin/PaymentHistory');
    }

    public function subscribeUsers()
    
    {
        return Inertia::render('Admin/SubscribeUser');
    }


}
