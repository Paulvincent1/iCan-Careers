<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WorkerDashboard extends Controller
{
    public function index(){
        return inertia('Worker/Dashboard');
    }
}
