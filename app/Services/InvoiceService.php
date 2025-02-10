<?php

namespace App\Services;
use Xendit\Configuration;
use Xendit\Invoice\CreateInvoiceRequest;
use Xendit\Invoice\InvoiceApi;
use Xendit\Invoice\InvoiceItem;

class InvoiceService {

    private $apiInstance;

    public function __construct()
    {
        Configuration::setXenditKey(env("XENDIT_SECRET_KEY"));
        $this->apiInstance = new InvoiceApi();
    }

    public function createInvoice(int $amount, string $description, array $items){

        $invoicesItems = [];
        $totalAmount = 0;

        foreach($items as $item){
       
            $invoicesItems[] = new InvoiceItem(
            [
                'name' => $item['name'], 
                'price' => $item['price'], 
                'quantity' => $item['quantity'],
            ]);

            $totalAmount+= $item['price'];
        }
        // dd($totalAmount);

        $create_invoice_request = new CreateInvoiceRequest([
            'external_id' => 'ORDER-' . uniqid(),
            'description' => $description,
            'amount' => $totalAmount,
            'invoice_duration' => 172800,
            'success_redirect_url' => 'facebook.com',
            'failure_redirect_url' => 'facebook.com',
            'currency' => 'PHP',
            'reminder_time' => 1,
            'items' => $invoicesItems,
        ]);
        $for_user_id = "679097a12e753bd42605ae99";
         
          
        try {
            $result = $this->apiInstance->createInvoice($create_invoice_request, $for_user_id );
            dd($result);
                print_r($result);
        } catch (\Xendit\XenditSdkException $e) {
        // dd('error');
            echo 'Exception when calling InvoiceApi->createInvoice: ', $e->getMessage(), PHP_EOL;
            echo 'Full Error: ', json_encode($e->getFullError()), PHP_EOL;
        }
    }
    
}