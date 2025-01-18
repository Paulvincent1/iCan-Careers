<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkerDashboard extends Controller
{
    public function index(){
        $user = Auth::user();
        $isPending = '';
        if(!$user->verified){
            if($user->workerVerification){
                $isPending = 'Pending verification';
            }else{
                $isPending = null;
            }
        }
        return inertia('Worker/Dashboard', ['user' => $user, 'isPending' => $isPending]);
    }
}
