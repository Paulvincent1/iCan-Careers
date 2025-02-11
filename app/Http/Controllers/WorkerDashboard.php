<?php

namespace App\Http\Controllers;

use App\Models\JobPost;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkerDashboard extends Controller
{

    protected $invoiceService;

    // Inject the InvoiceService into the controller
    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

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

        $savedJobs = $user->savedJobs()->with(['employer.employerProfile.businessInformation'])->latest()->get();
        
        $appliedJobs = $user->appliedJobs()->with(['employer.employerProfile.businessInformation'])->latest()->get();
      
        return inertia('Worker/Dashboard', ['user' => $user, 'isPending' => $isPending, 'savedJobsProps' => $savedJobs ,'jobsAppliedProps' => $appliedJobs]);
    }

    public function createInvoice(){

        $user = Auth::user();
        $jobs = $user->myJobs()->with('employer')->wherePivot('current',true)->get();

        $employers = $jobs->pluck('employer')->unique();

        return inertia('Worker/CreateInvoice', ['employersProps' => $employers]);
    }

    public function storeInvoice(){

        $this->invoiceService
        ->createInvoice(
            amount: 200,
            description: 'test lang',
            items: [
                [
                    'name' => 'video editing',
                    'price' => 5000,
                    'quantity' => 1,
                ],
                [
                    'name' => 'video editing2',
                    'price' => 5000,
                    'quantity' => 1,
                ]
            ],
        );
    }
}
