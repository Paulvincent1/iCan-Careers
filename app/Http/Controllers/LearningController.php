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
}
