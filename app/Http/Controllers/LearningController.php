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
    public function learningTechnology()
    {
        
        return Inertia::render('Learning/TechnITPage');
    }
    public function learningBusiness()
    {
        
        return Inertia::render('Learning/BusinessMarketingPage');
    }
    public function learningEducation()
    {
        
        return Inertia::render('Learning/EducationAndTrainingPage');
    }
    public function learningFinance()
    {
        
        return Inertia::render('Learning/FinanceAndAccountingPage');
    }
    public function learningPersonal()
    {
        
        return Inertia::render('Learning/PersonalDevelopmentPage');
    }

}
