<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class LearningController extends Controller
{
    public function learning()
    {
        return Inertia::render('Learning/LearningPage');
    }

    public function learningHealth()
    {
        return Inertia::render('Learning/HealthPage');
    }

    public function learningCreative()
    {
        return Inertia::render('Learning/CreativePage');
    }

    public function learningJobseeking()
    {
        
        return Inertia::render('Learning/JobseekingPage');
    }

}
