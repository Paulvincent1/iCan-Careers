<?php

namespace App\Http\Controllers;

use App\Models\EmployerSubscription;
use App\Models\EmployerSubscriptionInvoice;
use App\Models\Invoice;
use App\Services\InvoiceService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Salary;

use function Illuminate\Log\log;

class InvoiceController extends Controller
{

    public function __construct(public InvoiceService $invoiceService)
    {
        
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    public function webhook(Request $request){


        if($request->header('X-CALLBACK-TOKEN') === env('XENDIT_WEBHOOK_KEY')){

     

            if($employerSubscriptionInvoice = EmployerSubscriptionInvoice::where('external_id', $request['external_id'])->with('employer')->first()){

                $employer = $employerSubscriptionInvoice->employer;


                if($employerSubscriptionInvoice->subscription_type === 'Pro'){

                    if($employer->employerSubscription->expiry_date){
                        if(Carbon::parse($employer->employerSubscription->expiry_date)->isBefore(Carbon::now())){
 
                         $employer->employerSubscription->update([
                             'subscription_type'=> 'Pro',
                             'start_date' => Carbon::now(),
                             'expiry_date' => Carbon::now()->addMonth(),
                         ]);
     
                        }else{
                     
                            //if on going
                         $employer->employerSubscription->update([
                             'subscription_type'=> 'Pro',
                             'start_date' => Carbon::now(),
                             'expiry_date' => Carbon::parse($employer->employerSubscription->expiry_date)->addMonth(),
                         ]);

 
                        }
                    }else{

                        $employer->employerSubscription->update([
                            'subscription_type'=> 'Pro',
                            'start_date' => Carbon::now(),
                            'expiry_date' => Carbon::now()->addMonth(),
                        ]);
    
                    }


                    if($salary = Salary::find(1)){

                        $salary->increment('total_earnings',3797.4504);

                    }else{
                        Salary::create([
                            'total_earnings'=> 3797.4504,
                         ]);
    
                    }


                    $employer->subscriptionPaymentHistory()->create([
                        'amount' => 3797.4504,
                        'subscription_type' => 'Pro',
                    ]);
                    
                  
                    $employerSubscriptionInvoice->delete();

                    DB::beginTransaction();

                    try {
    
                        $externalIdProTier =  'INV-'.uniqid();
                    
                        // creating pro tier invoice
                        $proTierInvoice = $this->invoiceService
                        ->createInvoice(
                        externalId: $externalIdProTier,
                        description: 'Pro Tier Subscription (Monthly)',
                        items:  [
                            [
                            'description' => 'Pro Tier Subscription',
                            'rate' => 3999,
                            'hours' => 1,
                            ]
                          ],                  
                        duration: Carbon::now()->diffInSeconds(Carbon::now()->addMonth()->setTime(23,59,0)) 
                        );
    
                        $employer->employerSubscriptionInvoices()->create([
                            'external_id' =>  $externalIdProTier,
                            'invoice_id' =>  $proTierInvoice->getId(),
                            'description' => 'Pro Tier Subscription (Monthly)',
                            'invoice_url' =>  $proTierInvoice->getInvoiceUrl(),
                            'subscription_type' => 'Pro',
                            'duration' => now(),
                        ]);
                    
                        DB::commit();
    
                    }catch(Exception $e){
    
                        DB::rollBack();
                        dd($e->getMessage());
    
                    }
    
                }

                if($employerSubscriptionInvoice->subscription_type === 'Premium'){

                    if($employer->employerSubscription->expiry_date){
                       if(Carbon::parse($employer->employerSubscription->expiry_date)->isBefore(Carbon::now())){

                        $employer->employerSubscription->update([
                            'subscription_type'=> 'Premium',
                            'start_date' => Carbon::now(),
                            'expiry_date' => Carbon::now()->addYear(),
                        ]);
    
                       }else{
                            // if on going
                        $employer->employerSubscription->update([
                            'subscription_type'=> 'Premium',
                            'start_date' => Carbon::now(),
                            'expiry_date' => Carbon::parse($employer->employerSubscription->expiry_date)->addYear(),
                        ]);

                       }
                    }else{

                        
                        $employer->employerSubscription->update([
                            'subscription_type'=> 'Premium',
                            'start_date' => Carbon::now(),
                            'expiry_date' => Carbon::now()->addYear(),
                        ]);
                    }

                    
                    if($salary = Salary::find(1)){

                        $salary->increment('total_earnings',5411.7704);
                        
                    }else{
                        Salary::create([
                            'total_earnings'=> 5411.7704,
                        ]);
                    }

                    $employer->subscriptionPaymentHistory()->create([
                        'amount' => 5411.7704,
                        'subscription_type' => 'Premium',
                    ]);

                    $employerSubscriptionInvoice->delete();

                    DB::beginTransaction();

                    try {
    
                        $externalIdPremiumTier =  'INV-'.uniqid();
        
            
                            // creating premium tier invoice
                        $premiumTierInvoice = $this->invoiceService
                        ->createInvoice(
                        externalId: $externalIdPremiumTier,
                        description: 'Premium Tier Subscription (Anually)',
                        items:  [
                            [
                            'description' => 'Premium Tier Subscription',
                            'rate' => 5699,
                            'hours' => 1,
                            ]
                          ],
                        duration: Carbon::now()->diffInSeconds(Carbon::now()->addMonth()->setTime(23,59,0))           
                        );
                     
                        $employer->employerSubscriptionInvoices()->create([
                            'external_id' =>  $externalIdPremiumTier,
                            'invoice_id' =>  $premiumTierInvoice->getId(),
                            'description' => 'Premium Tier Subscription (Anually)',
                            'invoice_url' =>  $premiumTierInvoice->getInvoiceUrl(),
                            'subscription_type' => 'Premium',
                            'duration' => now(),
                        ]);
    
                        DB::commit();
    
                    }catch(Exception $e){
    
                        DB::rollBack();
                        dd($e->getMessage());
    
                    }



                }

                return;

            }


            $invoice = Invoice::where('external_id', $request['external_id'])->with(['worker.balance'])->first();

            $invoice->update([
                'status' => $request['status']
            ]);

            $invoice->worker->balance()->increment('unsettlement', $invoice->amount);

        }


    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        //
    }
}
