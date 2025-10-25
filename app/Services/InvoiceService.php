<?php

namespace App\Services;

use App\Models\EmployerSubscriptionInvoice;
use App\Models\Invoice;
use Carbon\Carbon;
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

    public function createInvoice(string $externalId, string $description, array $items, int $duration = null, float $totalAmountWithTaxes = null){

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
            'amount' => $totalAmountWithTaxes ?? $totalAmount,
            'invoice_duration' => $duration,
            'success_redirect_url' => 'http://127.0.0.1:8000/',
            'failure_redirect_url' => 'http://127.0.0.1:8000/',
            //http://127.0.0.1:8000/
            //should be https in production
            //https://ican-careers.onrender.com/
            'currency' => 'PHP',
            'reminder_time' => 1,
            'items' => $invoicesItems,
        ]);
        $for_user_id = "679097a12e753bd42605ae99";
        //67bdcfb25e9bb8a85784b27b-nath
        // 679097a12e753bd42605ae99-paul

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
            //67bdcfb25e9bb8a85784b27b-nath
        // 679097a12e753bd42605ae99-paul

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


        $pendingInvoices = Invoice::where('status','PENDING')->get();

        foreach($pendingInvoices as $invoice) {

            DB::beginTransaction();

            $invoice_id = $invoice->invoice_id; // string | Invoice ID
            $for_user_id = "679097a12e753bd42605ae99"; // string | Business ID of the sub-account merchant (XP feature)
            //67bdcfb25e9bb8a85784b27b-nath
        // 679097a12e753bd42605ae99-paul

            try {
                $result = $this->apiInstance->getInvoiceById($invoice_id, $for_user_id);
                $status = $result->getStatus();

                if($status === 'EXPIRED'){
                    $invoice->update([
                        'status' =>  $status
                    ]);

                    // $invoice->worker->balance()->decrement('unsettlement', $invoice->amount);
                    // $invoice->worker->balance()->increment('balance', $invoice->amount);

                }

                DB::commit();

            } catch (\Xendit\XenditSdkException $e) {
                echo 'Exception when calling InvoiceApi->getInvoiceById: ', $e->getMessage(), PHP_EOL;
                echo 'Full Error: ', json_encode($e->getFullError()), PHP_EOL;

                DB::rollBack();
            }


        }

    }

    public function renewEmployerSubscriptionInvoices(){
        $employerSubscriptionInvoices = EmployerSubscriptionInvoice::all();

        $for_user_id = "679097a12e753bd42605ae99";
        //67bdcfb25e9bb8a85784b27b-nath
        // 679097a12e753bd42605ae99-paul

        foreach($employerSubscriptionInvoices as $subscriptionInvoice) {

            DB::beginTransaction();

            try {

                $result = $this->apiInstance->getInvoiceById($subscriptionInvoice->invoice_id, $for_user_id);

                $status = $result->getStatus();

                if($status === 'EXPIRED'){


                    $externalId = 'INV-' . uniqid();

                    $price = 0;
                    $duration = Carbon::now()->diffInSeconds(Carbon::now()->addMonth()->setTime(23,19,0));

                    if($subscriptionInvoice->subscription_type === 'Pro'){
                        $price = 3999;
                    }
                    if($subscriptionInvoice->subscription_type === 'Premium'){
                        $price = 5699;
                    }

                    $newInvoice = $this->createInvoice(
                        externalId:$externalId,
                        description:$subscriptionInvoice->description,
                        items:[
                            [
                               'description' => $subscriptionInvoice->description,
                               'rate' => $price,
                               'hours'=> 1,
                            ]
                        ],
                        duration: $duration,
                    );


                    EmployerSubscriptionInvoice::create([
                        'external_id' => $externalId,
                        'invoice_id' => $newInvoice->getId(),
                        'description' =>  $subscriptionInvoice->description,
                        'invoice_url' =>  $newInvoice->getInvoiceUrl(),
                        'subscription_type' => $subscriptionInvoice->subscription_type,
                        'duration' => $duration
                    ]);

                    $subscriptionInvoice->delete();

                    DB::commit();
                }

            }catch (\Xendit\XenditSdkException $e) {
                echo 'Exception when calling InvoiceApi->getInvoiceById: ', $e->getMessage(), PHP_EOL;
                echo 'Full Error: ', json_encode($e->getFullError()), PHP_EOL;

                DB::rollBack();
            }

        }
    }
}
