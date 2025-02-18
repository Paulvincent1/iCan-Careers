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

        $invoiceTransactions = $user->workerInvoices()->whereIn('status',['PAID','SETTLED'])->with('employer')->get();
        // dd($invoiceTransactions);

        return inertia('Worker/Dashboard', 
        [
            'user' => $user,
            'isPending' => $isPending,
            'savedJobsProps' => $savedJobs,
            'jobsAppliedProps' => $appliedJobs,
            'balanceProps' => $user->balance,
            'invoiceTransactionsProps' => $invoiceTransactions

        ]);
    }

    public function createInvoice(){

        $user = Auth::user();
        $jobs = $user->myJobs()->with('employer')->wherePivot('current',true)->get();

        $employers = $jobs->pluck('employer')->unique();

        return inertia('Worker/CreateInvoice', ['employersProps' => $employers]);
    }

    public function storeInvoice(){

        // Pdf::view('pdf.invoice',[
        //     'invoiceId' => '$this->externalId',
        //     'dueDate' => '2025-02-20',
        //     'description' => '$this->description',
        //     'employer' => User::find(1),
        //     'items' =>  [['description' => 'tesdasdas t', 'rate' => 100 ,'hours' => 2,'amount'=> 1000]],
        //     'totalAmount' => 20000,
        //     'paymentUrl' => '$this->paymentUrl'
        // ])->disk('public')->save('/invoices/h.pdf');
    }

    public function previewInvoice(Request $request){

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
    //    dd($items);

       $employer = User::where('email', $fields['billTo'])->with('employerProfile')->first();
       
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

            $items = $fields['items'];

            $employer = User::where('email', $fields['billTo'])->with('employerProfile')->first();

            $externalId = 'INV-' . strtoupper(uniqid());

    
            $secondsDuration = Carbon::now()->diffInSeconds(Carbon::parse($fields['dueDate'] . ' 23:59:00'));
            // dd(Carbon::now());

            $resultInvoice = $this->invoiceService
            ->createInvoice(
                externalId: $externalId,
                description: $fields['description'],
                items: $items,
                duration: $secondsDuration,
            );

            $user->workerInvoices()
            ->create(
                [
                    'invoice_id' => $resultInvoice->getId(),
                    'external_id' => $externalId,
                    'description' =>  $fields['description'],
                    'amount' => $fields['totalAmount'],
                    'items' => $fields['items'],
                    'invoice_url' => $resultInvoice->getInvoiceUrl(),
                    'status' => 'pending',
                    'due_date' => Carbon::parse($fields['dueDate'] .' 23:59:00'),
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
             invoiceUrl: $resultInvoice->getInvoiceUrl()
            ));



            DB::commit();

            return redirect()->route('worker.dashboard');

        }catch(\Exception $e){

            DB::rollBack();
            dd($e->getMessage());

        }
      
    }
}
