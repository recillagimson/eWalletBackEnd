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
use App\Models\UserUtilities\UserDetail;
use App\Repositories\Send2Bank\IOutSend2BankRepository;
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

trait PayBillsHelpers
{
    use WithUserErrors, WithTpaErrors;

    private IBayadCenterService $bayadCenterService;
    private IReferenceNumberService $referenceNumberService;

    public function __construct(IBayadCenterService $bayadCenterService, IReferenceNumberService $referenceNumberService) {
        $this->bayadCenterService = $bayadCenterService;
        $this->referenceNumberService = $referenceNumberService;
    }

    public function outPayBills(UserAccount $user, string $billerCode, $response, $refNo)
    {
        $biller = $this->bayadCenterService->getBillerInformation($billerCode);
        $userDetail = $this->userDetailRepository->getByUserId($user->id);

        $this->outPayBills->create([
            'user_account_id' => $user->id,
            'account_number' => $response['data']['referenceNumber'],
            'reference_number' => $refNo,
            'amount' => $response['data']['amount'],
            'other_charges' => $response['data']['otherCharges'],
            'service_fee' => PayBillsConfig::ServiceFee,
            'total_amount' => $response['data']['amount']+ $response['data']['otherCharges'] + PayBillsConfig::ServiceFee,
            'transaction_category_id' => PayBillsConfig::BILLS,
            'transaction_remarks' => $userDetail->first_name .' pay bills to ' . $biller['data']['name'],
            'email_or_mobile' => '',
            'message' => '',
            'status' => $response['data']['status'],
            'billers_code' => $biller['data']['code'],
            'billers_name' => $biller['data']['name'],
            'bayad_reference_number' => $response['data']['referenceNumber'],
            'user_created' => $user->id,
            'user_updated' => ''
        ]);
    }




}
