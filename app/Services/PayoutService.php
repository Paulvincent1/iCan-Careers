<?php


namespace App\Services;

use Xendit\Configuration;
use Xendit\Payout\CreatePayoutRequest;
use Xendit\Payout\PayoutApi;

class PayoutService {

    private $apiInstance;

    public function __construct()
    {
        Configuration::setXenditKey(env("XENDIT_SECRET_KEY"));
        $this->apiInstance = new PayoutApi();
    }

    public function createPayoutRequest(string $payoutUniqueId, string $channelCode, string $accountHolderName,  string $accountNumber, int $amount){


        // $payoutUniqueId =  'DISB-' . uniqid();

        $idempotency_key =  $payoutUniqueId ; // string | A unique key to prevent duplicate requests from pushing through our system. No expiration.
        $for_user_id = "67bdcfb25e9bb8a85784b27b"; // string | The sub-account user-id that you want to make this transaction for. This header is only used if you have access to xenPlatform. See xenPlatform for more information.
        //67bdcfb25e9bb8a85784b27b-nath
        // 679097a12e753bd42605ae99-paul
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
            throw new \Exception('Error creating payout request with Xendit: ' . $e->getMessage());
        }


    }


}
