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

                    $employer->employerSubscription->update([
                        'subscription_type'=> 'Pro',
                        'start_date' => Carbon::now(),
                        'expiry_date' => Carbon::now()->addMonth(),
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
                        // dd($proTierInvoice->getInvoiceUrl());
    
                        $employer->employerSubscriptionInvoices()->create([
                            'external_id' =>  $externalIdProTier,
                            'description' => 'Pro Tier Subscription (Monthly)',
                            'payment_url' =>  $proTierInvoice->getInvoiceUrl(),
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

                    $employer->employerSubscription->update([
                        'subscription_type'=> 'Premium',
                        'start_date' => Carbon::now(),
                        'expiry_date' => Carbon::now()->addYear(),
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
                            'description' => 'Premium Tier Subscription (Anually)',
                            'payment_url' =>  $premiumTierInvoice->getInvoiceUrl(),
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
