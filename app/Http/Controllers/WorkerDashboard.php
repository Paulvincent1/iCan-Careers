<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessInvoicePdf;
use App\Models\Invoice;
use App\Models\JobPost;
use App\Models\User;
use App\Services\InvoiceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\LaravelPdf\Facades\Pdf;

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

        // $this->invoiceService
        // ->createInvoice(
        //     externalId:  'INV-' . uniqid(),
        //     description: 'test lang',
        //     items: [
        //         [
        //             'name' => 'video editing',
        //             'rate' => 5000,
        //             'hours' => 1,
        //         ],
        //         [
        //             'name' => 'video editing2',
        //             'rate' => 5000,
        //             'hours' => 1,
        //         ]
        //     ],
        // );
    }

    public function previewInvoice(Request $request){

        // dd($request);
        // dd(Carbon::parse('2025-02-20 23:59:59'));
        $fields = $request->validate([
            'dueDate' => 'required',
            'description' => 'required',
            'billTo' => 'required',
            'items' => 'required',
            'items.*.description' => 'required',
            'items.*.hours' => 'required',
            'items.*.rate' => 'required',
            'totalAmount' => 'required',
        ]);


       $items = json_decode($fields['items'], true);

       $employer = User::where('email', $fields['billTo'])->with('employerProfile')->first();
       
    //    dd($employer);

    //    foreach($items as $item){
    //     dd($item->id);
    //    }

        // $pdf = Pdf::view('pdf.invoice');
        // dd($pdf);
        // return $pdf->name('hi.pdf');
        return Pdf::view('pdf.invoice',[
            'invoiceId' => 'test',
            'dueDate' => $fields['dueDate'],
            'description' => $fields['description'],
            'employer' => $employer,
            'items' =>  $items,
            'totalAmount' => $fields['totalAmount'],
            'paymentUrl' => null
        ]);
    }

    public function sendInvoice(Request $request){

        $user = Auth::user();

        $fields = $request->validate([
            'dueDate' => 'required',
            'description' => 'required',
            'billTo' => 'required',
            'items' => 'required',
            'items.*.description' => 'required',
            'items.*.hours' => 'required',
            'items.*.rate' => 'required',
            'totalAmount' => 'required',
        ]);


        DB::beginTransaction();
        try{

            $items = json_decode($fields['items'], true);

            $employer = User::where('email', $fields['billTo'])->with('employerProfile')->first();

            $externalId = 'INV-' . uniqid();

    
            $secondsDuration = Carbon::now()->diffInSeconds(Carbon::parse($fields['dueDate'].'23:59:59'));

            $paymentUrl = $this->invoiceService
            ->createInvoice(
                externalId: $externalId,
                description: $fields['description'],
                items: $items,
                duration: $secondsDuration,
            );

            $user->workerInvoices()
            ->create(
                [
                    'external_id' => $externalId,
                    'description' =>  $fields['description'],
                    'amount' => $fields['totalAmount'],
                    'items' => $fields['items'],
                    'payment_url' => $paymentUrl,
                    'status' => 'pending',
                    'due_date' => $fields['dueDate'],
                    'paid_at' => null,
                    'employer_id' => $employer->id,
                ]
            );

            dispatch(new ProcessInvoicePdf(
             employer: $employer,
             externalId:  $externalId, 
             dueDate: $fields['dueDate'],
             description: $fields['description'],
             items: $items,
             totalAmount: $fields['totalAmount'],
             paymentUrl: $paymentUrl
            ));



            DB::commit();

            return redirect()->route('worker.dashboard');

        }catch(\Exception $e){

            DB::rollBack();
            dd('error');

        }
      



        // return Pdf::view('pdf.invoice',[
        //     'invoiceId' => 'test',
        //     'dueDate' => $fields['dueDate'],
        //     'description' => $fields['description'],
        //     'employer' => $employer,
        //     'items' =>  $items,
        //     'totalAmount' => $fields['totalAmount'],
        //     'paymentUrl' => null
        // ]);
    }
}
