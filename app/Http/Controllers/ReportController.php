<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\User;
use App\Notifications\AdminReportUserNotification;
use Dacastro4\LaravelGmail\Facade\LaravelGmail;
use Dacastro4\LaravelGmail\Services\Message\Mail;
use Illuminate\Http\Request;

class ReportController extends Controller
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
    public function store(Request $request, User $userId)
    {
        $request->validate([
            'reason' => 'required'
        ]);

        $user = $request->user();
        $user->reportsMade()->create([
            'reason' => $request->reason,
            'reported_user_id' => $userId->id,
        ]);

        $admin = User::whereHas('roles', function($query) {
            $query->where('name', 'Admin');
        })->first();

        $admin->notify(new AdminReportUserNotification(admin:$admin,user:$user));
        // broadcast(new AdminReportUserNotification(admin:$admin,user:$user));

        // Send Gmail notification
        try {
            $token = LaravelGmail::makeToken(); // Generate Gmail token

            $mail = new Mail();
            $mail->using($token['access_token'])
                ->to($admin->email, $admin->name)
                ->from('icancareers2@gmail.com', 'iCan Careers')
                ->subject('A User Has Been Reported')
                ->view('mail.report-user', [
                    'admin' => $admin,
                    'user' => $user,
                    'reported' => $userId,
                    'reason' => $request->reason,
                ])
                ->send();

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['email' => 'Email sending failed: ' . $e->getMessage()]);
        }


        return redirect()->back()->with('message','Thank you for reporting this user! We will review the behaviour of this user.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Report $report)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Report $report)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Report $report)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Report $report)
    {
        //
    }
}
