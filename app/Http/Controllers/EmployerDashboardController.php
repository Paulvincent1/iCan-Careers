<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmployerDashboardController extends Controller
{
    
    public function index(){
        return inertia('Employer/Dashboard');
    }
}
