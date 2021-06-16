<?php


namespace App\Traits\Transactions;

use App\Enums\BayadCenterResponseCode;
use App\Enums\PayBillsConfig;
use App\Enums\ReferenceNumberTypes;
use App\Enums\TpaProviders;
use App\Enums\TransactionCategoryIds;
use App\Enums\TransactionStatuses;
use App\Enums\UbpResponseCodes;
use App\Enums\UbpResponseStates;
use App\Enums\UsernameTypes;
use App\Models\OutPayBills;
use App\Models\OutSend2Bank;
use App\Models\UserAccount;
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


    public function __construct(IBayadCenterService $bayadCenterService)
    {
        $this->bayadCenterService = $bayadCenterService;
    }


    /**
     * Validates the transaction 
     * Validate Account Endpoint
     * 
     * @param UserAccount $user
     * @param string $billerCode
     * @param array $response
     * @return mixed
     */
    private function validateTransaction(string $billerCode, array $data, UserAccount $user)
    {
        $isEnough = $this->checkAmount($user, $data, $billerCode);
        if (!$isEnough) $this->insuficientBalance();
        $this->checkMonthlyLimit($user, $data);
    }


    /**
     * Saves the transaction after successfully validated
     * Create Payment Endpoint

     * @param UserAccount $user
     * @param string $billerCode
     * @param array $response
     * @return mixed
     */
    private function saveTransaction(UserAccount $user, string $billerCode, $response)
    {
        $this->subtractUserBalance($user, $billerCode, $response);
      //$this->notificationService->payBillsNotification();
        return $this->outPayBills($user, $billerCode, $response);
    }


    private function checkMonthlyLimit(UserAccount $user, array $data)
    {
        $this->transactionValidationService->checkUserMonthlyTransactionLimit($user, $data['amount'], TransactionCategoryIds::payBills);
    }


    private function checkAmount(UserAccount $user, array $data, string $billerCode)
    {
        $balance = $this->getUserBalance($user);
        $totalAmount = $data['amount'] + $this->getServiceFee($user) + $this->getOtherCharges($billerCode);

        if ($balance >= $totalAmount) return true;
    }


    private function subtractUserBalance(UserAccount $user, string $billerCode, $response)
    {
        $balance = $this->getUserBalance($user);
        $totalAmount = $response['data']['amount'] + $this->getServiceFee($user) + $this->getOtherCharges($billerCode);
        $balance -= $totalAmount;

        $pendingBalance = $this->userBalanceInfo->getUserPendingBalance($user->id);

        $this->userBalanceInfo->updateUserBalance($user->id, $balance);
        $this->userBalanceInfo->updateUserPendingBalance($user->id, $pendingBalance + $totalAmount);
    }


    private function getUserBalance(UserAccount $user)
    {
        return $this->userBalanceInfo->getUserBalance($user->id);
    }


    private function getReference()
    {
        return $this->referenceNumberService->generate(ReferenceNumberTypes::PayBills);
    }
    

    private function getServiceFee(UserAccount $user)
    {
        $serviceFee = $this->serviceFeeRepository->getByTierAndTransCategory($user->tier_id, TransactionCategoryIds::payBills);
        $serviceFeeAmount = $serviceFee ? $serviceFee->amount : 0;

        return $serviceFeeAmount;
    }


    private function getOtherCharges(string $billerCode)
    {
        $otherCharges = $this->bayadCenterService->getOtherCharges($billerCode);
        return $otherCharges['data']['otherCharges'];
    }


    private function outPayBills(UserAccount $user, string $billerCode, $response)
    {
        $biller = $this->bayadCenterService->getBillerInformation($billerCode);
        $serviceFee = $this->getServiceFee($user);
        return $this->outPayBills->create([
            'user_account_id' => $user->id,
            'account_number' => $response['data']['referenceNumber'],
            'reference_number' => $this->getReference(),
            'amount' => $response['data']['amount'],
            'other_charges' => $response['data']['otherCharges'],
            'service_fee' => $serviceFee,
            'total_amount' => $response['data']['amount'] + $response['data']['otherCharges'] + $serviceFee,
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


    private function validationResponse(UserAccount $user, $response, string $billerCode)
    {
        $data = array();
        $data += array('serviceFee' => (string) $this->getServiceFee($user));
        $data += array('otherCharges' => $this->getOtherCharges($billerCode));
        $data += array('validationNumber' => $response['data']['validationNumber']);

        return $data;
    }

    private function handleStatusResponse(OutPayBills $payBill, Response $response)
    {
        if (!$response->successful()) {
            return;
        } else {
            $responseData = $response->json();
            $state = $responseData['data']['status'];
            $status = '';
            
            if ($state === BayadCenterResponseCode::paymentPosted)
                $status = TransactionStatuses::success;

            if ($state === BayadCenterResponseCode::billerValidationFailed)
                $status = TransactionStatuses::failed;

            if ($state === BayadCenterResponseCode::billerTimeOut)
                $status = TransactionStatuses::failed;

            if ($state === BayadCenterResponseCode::pending)
                $status = TransactionStatuses::pending;

            if ($state === BayadCenterResponseCode::onhold)
                $status = TransactionStatuses::pending;

            if ($state === BayadCenterResponseCode::queued)
                $status = TransactionStatuses::pending;

            if ($state === BayadCenterResponseCode::processing)
                $status = TransactionStatuses::pending;
                
                
            $payBill->status = $status;
            $payBill->user_updated = $payBill->user_account_id;
            $payBill->save();

            if ($status === TransactionStatuses::success) {
                $this->transactionHistories->log(
                    $payBill->user_account_id,
                    $payBill->transaction_category_id,
                    $payBill->id,
                    $payBill->reference_number,
                    $payBill->total_amount,
                    $payBill->user_account_id
                );
            }
        }

        return $payBill;
    }

}
