<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index(){
        return inertia('Admin/Dashboard');
    }

    public function workers(){
        return inertia('Admin/Workers');
    }

    public function employers(){
        return inertia('Admin/Employers');
    }

    public function reportedUsers(){
        return inertia('Admin/ReportedUsers');
    }
    
    public function jobApprovals(){
        return inertia('Admin/JobApprovals');
    }
}
