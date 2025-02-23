<?php 


namespace App\Services;

use Xendit\Configuration;
use Xendit\Payout\CreatePayoutRequest;
use Xendit\Payout\PayoutApi;

class PayoutService {

    private $apiInstance;

    public function __construct()
    {
        Configuration::setXenditKey("YOUR_API_KEY_HERE");
        $this->apiInstance = new PayoutApi();
    }

    public function createPayoutRequest(string $payoutUniqueId, string $channelCode, string $accountHolderName,  string $accountNumber, int $amount){


        // $payoutUniqueId =  'DISB-' . uniqid();

        $idempotency_key =  $payoutUniqueId ; // string | A unique key to prevent duplicate requests from pushing through our system. No expiration.
        $for_user_id = "679097a12e753bd42605ae99"; // string | The sub-account user-id that you want to make this transaction for. This header is only used if you have access to xenPlatform. See xenPlatform for more information.
        $create_payout_request = new CreatePayoutRequest([
        'reference_id' =>  $payoutUniqueId ,
        'currency' => 'PHP',
        'channel_code' => $channelCode,
        'channel_properties' => [
            'account_holder_name' => $accountHolderName,
            'account_number' =>  $accountNumber
        ],
        'amount' => $amount,
        'description' => 'Payout',
        'type' => 'DIRECT_DISBURSEMENT'
        ]); // \Xendit\Payout\CreatePayoutRequest

        try {
            $result = $this->apiInstance->createPayout($idempotency_key, $for_user_id, $create_payout_request);
                return $result;
        } catch (\Xendit\XenditSdkException $e) {
            echo 'Exception when calling PayoutApi->createPayout: ', $e->getMessage(), PHP_EOL;
            echo 'Full Error: ', json_encode($e->getFullError()), PHP_EOL;
        }


    }

    
}