<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessInvoicePdf;
use App\Models\Invoice;
use App\Models\JobPost;
use App\Models\Message;
use App\Models\User;
use App\Notifications\WorkerSendAnInvoiceToEmployerNotification;
use App\Services\InvoiceService;
use App\Services\PayoutService;
use Carbon\Carbon;
use Dacastro4\LaravelGmail\Facade\LaravelGmail;
use Dacastro4\LaravelGmail\Services\Message\Mail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPdf\Facades\Pdf;
use Stevebauman\Location\Facades\Location;

class WorkerDashboard extends Controller
{

    protected $invoiceService;
    protected $payoutService;

    // Inject the InvoiceService into the controller
    public function __construct(InvoiceService $invoiceService, PayoutService $payoutService)
    {
        $this->invoiceService = $invoiceService;
        $this->payoutService = $payoutService;
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

        $invoiceTransactions = $user->workerInvoices()->whereIn('status',['PAID','SETTLED'])->with('employer')->latest()->get();

        $invoices = $user->workerInvoices()->latest()->get();
        // dd($invoiceTransactions);

        // users that we interact
        $usersId = Message::where('sender_id',$user->id)->pluck('receiver_id')
        ->merge(Message::where('receiver_id',$user->id)->pluck('sender_id'))->unique()
        ->filter(function ($id) use ($user) {
            return $id != $user->id;
        });

        $users = User::whereIn('id',$usersId)->get();
        $chatHeads = [];

        foreach($users as $userchat){
            $sent = $userchat->sentMessages()->where('receiver_id',$user->id)->latest()->first();
            $received = $userchat->receivedMessages('sender_id',$user->id)->latest()->first();


            $latestMessage = null;
            if($sent && $received){
                if($sent->created_at > $received->created_at){

                    $latestMessage = $sent;
                }else{

                    $latestMessage = $received;
                }


            }elseif($sent){

                $latestMessage = $sent;

            }elseif($received){

                $latestMessage = $received;

            }


            $chatHeads[] = [
                'user' => $userchat,
                'latestMessage' => $latestMessage
            ];


        }

        usort($chatHeads, function ($a, $b){
           return $b['latestMessage']->created_at <=> $a['latestMessage']->created_at;
        });


        return inertia('Worker/Dashboard',
        [
            'user' => [
                'name' => $user->name,
                'profile_photo_path' => $user->profile_img_url ?? null, // Ensure it's included
            ],
            'isPending' => $isPending,
            'savedJobsProps' => $savedJobs,
            'jobsAppliedProps' => $appliedJobs,
            'balanceProps' => $user->balance,
            'invoiceTransactionsProps' => $invoiceTransactions,
            'invoicesProps' => $invoices->load('employer'),
            'payoutProps' => $user->payouts()->latest()->get(),
            'chatHeadsProps' => $chatHeads

        ]);
    }

    public function createInvoice(){

        if(!Gate::allows('worker-profile-check')){
            return redirect()->route('create.profile');
        }

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

       $xenditTransactionFee = (($fields['totalAmount'] / 0.955) * 0.045);
       $vatTransactionFee = $xenditTransactionFee * 0.12;

       $totalAmount = $fields['totalAmount'] + $xenditTransactionFee + $vatTransactionFee;

    //    dd($totalAmount);

        return Pdf::view('pdf.invoice',[
            'invoiceId' => 'test',
            'dueDate' => $fields['dueDate'],
            'description' => $fields['description'],
            'employer' => $employer,
            'items' =>  $items,
            'xenditTransactionFee' => $xenditTransactionFee, // 4.5%
            'vatTransactionFee' => $vatTransactionFee, // 12%
            'totalAmount' =>  $totalAmount,
            'invoiceUrl' => null,
        ]);
    }

    public function prevEmployers()
    {
        $user = Auth::user();

        // Get all previous jobs with employer and their business info
        $jobs = $user->myJobs()
            ->with(['employer.employerProfile.businessInformation'])
            ->latest()
            ->get();

        return inertia('Worker/PreviousEmployer', [
            'jobsProps' => $jobs
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
            'items.*.hours' => 'required|numeric|min:1',
            'items.*.rate' => 'required|numeric|min:1',
            'totalAmount' => 'required|numeric|max:50000|min:1',
        ]);


        if ($fields['totalAmount'] < 500) {
            return redirect()->back()
            ->withErrors(['totalAmount' => 'The total amount must be at least 500.']);
        }


        DB::beginTransaction();
        try{

            $items = $fields['items'];

            $employer = User::where('email', $fields['billTo'])->with('employerProfile')->first();

            $externalId = 'INV-' . strtoupper(uniqid());


            $location = Location::get($request->ip());

            $timezone = $location ? $location->timezone : config('app.timezone', 'UTC');
            $dueDate = Carbon::parse($fields['dueDate'] . ' 23:59:00',  $timezone)
                ->setTimezone('UTC');
                // dd($dueDate);
            $secondsDuration = Carbon::now('UTC')->diffInSeconds($dueDate);
            //  dd($secondsDuration);

            $xenditTransactionFee = (($fields['totalAmount'] / 0.955) * 0.045);
            $vatTransactionFee = $xenditTransactionFee * 0.12;

            $totalAmount = $fields['totalAmount'] + $xenditTransactionFee + $vatTransactionFee;

            $resultInvoice = $this->invoiceService
            ->createInvoice(
                externalId: $externalId,
                description: $fields['description'],
                items: $items,
                duration: $secondsDuration,
                totalAmountWithTaxes: $totalAmount
            );

            // put it in the ProcessInvoicePdf.php
            $user->workerInvoices()
            ->create(
                [
                    'invoice_id' => $resultInvoice->getId(),
                    'external_id' => $externalId,
                    'description' =>  $fields['description'],
                    'amount' => $totalAmount,
                    'items' => $fields['items'],
                    'invoice_url' => $resultInvoice->getInvoiceUrl(),
                    'status' => 'pending',
                    'due_date' => $dueDate,
                    'paid_at' => null,
                    'employer_id' => $employer->id,
                ]
            );

            $employer->notify(new WorkerSendAnInvoiceToEmployerNotification(worker:$user,employer:$employer));

            // ✅ Send Gmail email to employer
            try {
                $token = LaravelGmail::makeToken();

                $mail = new Mail();
                $mail->using($token['access_token'])
                    ->to($employer->email, $employer->name)
                    ->from('icancareers2@gmail.com', 'iCan Careers')
                    ->subject('New Invoice from ' . $user->name)
                    ->view('mail.worker-send-invoice', [
                        'employer' => $employer,
                        'worker' => $user,
                        'invoiceUrl' => $resultInvoice->getInvoiceUrl(),
                        'dueDate' => $fields['dueDate'],
                        'description' => $fields['description'],
                        'items' => $items,
                        'totalAmount' => $fields['totalAmount'],
                    ])
                    ->send();

            } catch (\Exception $e) {
                return redirect()->back()->withErrors(['email' => 'Email sending failed: ' . $e->getMessage()]);
            }


            dispatch(new ProcessInvoicePdf(
             employer: $employer,
             externalId:  $externalId,
             dueDate: $fields['dueDate'],
             description: $fields['description'],
             items: $items,
             xenditTransactionFee: $xenditTransactionFee,
             vatTransactionFee: $vatTransactionFee,
             totalAmount: $fields['totalAmount'],
             invoiceUrl: $resultInvoice->getInvoiceUrl()
            ));

            DB::commit();

            return redirect()->route('worker.dashboard');

        }catch(\Exception $e){

            DB::rollBack();
            // dd($e->getMessage());

            // TODO: error handling same date.

            return redirect()->back()->withErrors(['duedate' => 'The due date and time must be in the future (UTC).']);

        }

    }

    public function payout(Request $request){
        // dd($request);
        $fields = $request->validate([
            'amount' => 'required|numeric|min:100',
            'channelCode' => 'required',
            'accountName' => 'required',
            'accountNumber' => 'required|numeric',
        ]);

        $user = Auth::user();

        if ($user->balance->balance < $fields['amount']){
            return redirect()->back()->withErrors(['error'=> 'Not Enough balance to make this request.']);
        }

        $payoutUniqueId = 'DISB-' . uniqid();

        DB::beginTransaction();

        try{

            $this->payoutService->createPayoutRequest(
                payoutUniqueId:  $payoutUniqueId,
                channelCode:$fields['channelCode'],
                accountHolderName:$fields['accountName'],
                accountNumber:$fields['accountNumber'] ,
                amount: $fields['amount'],
            );



            $user->payouts()->create(
                [
                    'reference_id' => $payoutUniqueId,
                    'channel_code' => $fields['channelCode'],
                    'account_holder_name' => $fields['accountName'],
                    'account_number' => $fields['accountNumber'],
                    'amount' => $fields['amount'],
                    ]
            );

            $user->balance()->decrement('balance', $fields['amount']);

            DB::commit();

            return redirect()->back();


        }catch(Exception $e){

            DB::rollBack();

            dd($e->getMessage());


        }

    }
}
