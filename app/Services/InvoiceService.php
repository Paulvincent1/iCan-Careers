<?php

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Xendit\Configuration;
use Xendit\Invoice\CreateInvoiceRequest;
use Xendit\Invoice\InvoiceApi;
use Xendit\Invoice\InvoiceItem;

class InvoiceService {

    private $apiInstance;

    public function __construct()
    {

        // dd(env("XENDIT_SECRET_KEY"));
        Configuration::setXenditKey(env("XENDIT_SECRET_KEY"));
        $this->apiInstance = new InvoiceApi();
    }

    public function createInvoice(string $externalId, string $description, array $items, int $duration){

        $invoicesItems = [];
        $totalAmount = 0;
        // dd($items);

        foreach($items as $item){
       
            $invoicesItems[] = new InvoiceItem(
            [
                'name' => $item['description'], 
                'price' => $item['rate'], 
                'quantity' => $item['hours'],
            ]);

            $totalAmount+= $item['rate'] * $item['hours'];
        }
        // dd($totalAmount);

        $create_invoice_request = new CreateInvoiceRequest([
            'external_id' => $externalId,
            'description' => $description,
            'amount' => $totalAmount,
            'invoice_duration' => $duration,
            'success_redirect_url' => 'http://127.0.0.1:8000/employers',
            'failure_redirect_url' => 'http://127.0.0.1:8000/employers',
            'currency' => 'PHP',
            'reminder_time' => 1,
            'items' => $invoicesItems,
        ]);
        $for_user_id = "679097a12e753bd42605ae99";
         
          
        try {
            $result = $this->apiInstance->createInvoice($create_invoice_request, $for_user_id);
            return $result;
            // print_r($result);
        } catch (\Xendit\XenditSdkException $e) {
            // dd($e->getMessage());
            // echo 'Exception when calling InvoiceApi->createInvoice: ', $e->getMessage(), PHP_EOL;
            // echo 'Full Error: ', json_encode($e->getFullError()), PHP_EOL;
            throw new \Exception('Error creating invoice with Xendit: ' . $e->getMessage());
        }
    }

    public function updateInvoiceStatus(){
        $paidInvoices = Invoice::where('status','PAID')->get();

        foreach($paidInvoices as $invoice){


            DB::beginTransaction();
        
            $invoice_id = $invoice->invoice_id; // string | Invoice ID
            $for_user_id = "679097a12e753bd42605ae99"; // string | Business ID of the sub-account merchant (XP feature)
    
            try {
                $result = $this->apiInstance->getInvoiceById($invoice_id, $for_user_id);
                $status = $result->getStatus();

                if($status === 'SETTLED'){
                    $invoice->update([
                        'status' =>  $status
                    ]);

                    $invoice->worker->balance()->decrement('unsettlement', $invoice->amount);
                    $invoice->worker->balance()->increment('balance', $invoice->amount);
                    
                }

                DB::commit();

            } catch (\Xendit\XenditSdkException $e) {
                echo 'Exception when calling InvoiceApi->getInvoiceById: ', $e->getMessage(), PHP_EOL;
                echo 'Full Error: ', json_encode($e->getFullError()), PHP_EOL;

                DB::rollBack();
            }
        }
    
    }
    
}