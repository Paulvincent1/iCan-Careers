<?php

namespace App\Http\Controllers;

use App\Models\JobPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployerDashboardController extends Controller
{
    
    public function index(){

        $user = Auth::user();
        $openJobs = JobPost::with(['usersWhoApplied'])->where('employer_id',  $user->id)->whereDoesntHave('usersWhoApplied',function ($query){
            $query->whereIn('status',['Accepted']);
        })->get();


        return inertia('Employer/Dashboard',['openJobsProps' =>  $openJobs]);
    }
}
