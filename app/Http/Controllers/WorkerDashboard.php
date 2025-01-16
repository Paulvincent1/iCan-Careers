<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkerDashboard extends Controller
{
    public function index(){
        $user = Auth::user();
        return inertia('Worker/Dashboard', ['user' => $user]);
    }
}
