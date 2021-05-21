<?php


namespace App\Traits\Transactions;

use App\Enums\PayBillsConfig;
use App\Enums\ReferenceNumberTypes;
use App\Enums\TpaProviders;
use App\Enums\TransactionStatuses;
use App\Enums\UbpResponseCodes;
use App\Enums\UbpResponseStates;
use App\Enums\UsernameTypes;
use App\Models\OutSend2Bank;
use App\Models\UserAccount;
use App\Models\UserBalanceInfo;
use App\Models\UserDetail;
use App\Repositories\UserBalance\IUserBalanceRepository;
use App\Repositories\UserBalanceInfo\IUserBalanceInfoRepository;
use App\Repositories\UserTransactionHistory\IUserTransactionHistoryRepository;
use App\Services\ThirdParty\BayadCenter\IBayadCenterService;
use App\Services\Utilities\Notifications\Email\IEmailService;
use App\Services\Utilities\Notifications\SMS\ISmsService;
use App\Services\Utilities\ReferenceNumber\IReferenceNumberService;
use App\Traits\Errors\WithTpaErrors;
use App\Traits\Errors\WithUserErrors;
use App\Traits\UserHelpers;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Str;

use function GuzzleHttp\json_encode;

trait PayBillsHelpers
{
    use WithUserErrors, WithTpaErrors;

    private IBayadCenterService $bayadCenterService;
    private IUserBalanceInfoRepository $userBalanceRepository;

    public function __construct(IBayadCenterService $bayadCenterService, IUserBalanceInfoRepository $userBalanceInfo) {
        $this->bayadCenterService = $bayadCenterService;
        $this->userBalanceInfo = $userBalanceInfo;
    }



    private function saveTransaction(UserAccount $user, string $billerCode, array $response)
    {
        $this->outPayBills($user, $billerCode, $response);
    }


    private function checkAmount(string $userID, array $response)
    {
        $balance = $this->userBalanceInfo->getUserBalance($userID);
        $response['amount'] = $response['amount'] + PayBillsConfig::ServiceFee;
        if ($balance >= $response['amount']) return true;
    }


    private function getReference()
    {
        return $this->referenceNumberService->generate(ReferenceNumberTypes::PayBills);
    }
    

    private function outPayBills(UserAccount $user, string $billerCode, array $response)
    {
        $biller = $this->bayadCenterService->getBillerInformation($billerCode);
        $this->outPayBills->create([
            'user_account_id' => $user->id,
            'account_number' => $response['data']['referenceNumber'],
            'reference_number' => $this->getReference(),
            'amount' => $response['data']['amount'],
            'other_charges' => $response['data']['otherCharges'],
            'service_fee' => PayBillsConfig::ServiceFee,
            'total_amount' => $response['data']['amount'] + $response['data']['otherCharges'] + PayBillsConfig::ServiceFee,
            'transaction_category_id' => PayBillsConfig::BILLS,
            'transaction_remarks' => 'Pay bills to ' . $biller['data']['name'],
            'message' => '',
            'status' => $response['data']['status'],
            'client_reference' =>  $response['data']['clientReference'],
            'billers_code' => $biller['data']['code'],
            'billers_name' => $biller['data']['name'],
            'biller_reference_number' => $response['data']['billerReference'],
            'user_created' => $user->id,
            'user_updated' => ''
        ]);
    }




}
