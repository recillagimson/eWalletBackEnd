<?php


namespace App\Traits\Transactions;

use App\Enums\BayadCenterResponseCode;
use App\Enums\PayBillsConfig;
use App\Enums\ReferenceNumberTypes;
use App\Enums\TransactionCategoryIds;
use App\Enums\TransactionStatuses;
use App\Enums\UsernameTypes;
use App\Models\OutPayBills;
use App\Models\UserAccount;
use App\Services\ThirdParty\BayadCenter\IBayadCenterService;
use App\Traits\Errors\WithTpaErrors;
use App\Traits\Errors\WithUserErrors;
use App\Traits\StringHelpers;
use App\Traits\UserHelpers;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\Client\Response;
use Str;

use function GuzzleHttp\Promise\each;

trait PayBillsHelpers
{
    use WithUserErrors, WithTpaErrors, UserHelpers, StringHelpers;

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
    private function saveTransaction(UserAccount $user, string $billerCode, $response, $data)
    {
        DB::beginTransaction();
        try {
            $serviceFee = $this->getServiceFee($user);
            $outPayBills = $this->outPayBills($user, $billerCode, $response);
            $userDetail = $this->userDetailRepository->getByUserId($user->id);

            $fillRequest['serviceFee'] = $response['data']['otherCharges'] + $serviceFee;
            $fillRequest['newBalance'] = round($this->userBalanceInfo->getUserBalance($user->id), 2);
            $fillRequest['amount'] = $response['data']['amount'];
            $fillRequest['refNo'] = $outPayBills->reference_number;
            $fillRequest['biller'] = $outPayBills->billers_name;

            $usernameField = $this->getUsernameFieldByAvailability($user);
            $username = $this->getUsernameByField($user, $usernameField);
            $notifService = $usernameField === UsernameTypes::Email ? $this->emailService : $this->smsService;
            $notifService->payBillsNotification($username, $fillRequest, $userDetail->first_name);
            $firstName = $user->profile ? ucwords($user->profile->first_name) : 'Squidee';

            $description = 'Hi '. $firstName .' Your payment of P' . $this->formatAmount($fillRequest['amount']) . ' to ' . $fillRequest['biller'] .
                ' with fee ' . $this->formatAmount($fillRequest['serviceFee']) . '. has been successfully processed on ' .
                $this->formatDate(Carbon::now()) . ' with Ref No. ' . $fillRequest['refNo'] .
                '. Visit https://my.squid.ph/ for more information or contact support@squid.ph.';

            $title = 'SquidPay - Pay Bills Notification';

            $this->subtractUserBalance($user, $billerCode, $response);
            $this->insertNotification($user, $title, $description);

            DB::commit();
            return $outPayBills;
        } catch (Exception $e) {
            DB::rollBack();
        }

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
            'transaction_date' => Carbon::now(),
            'transaction_category_id' => PayBillsConfig::BILLS,
            'transaction_remarks' => 'Pay bills to ' . $biller['data']['name'],
            'message' => '',
            'status' => TransactionStatuses::pending,
            'client_reference' => $response['data']['clientReference'],
            'billers_code' => $biller['data']['code'],
            'billers_name' => $biller['data']['name'],
            'biller_reference_number' => $response['data']['billerReference'],
            'user_created' => $user->id,
            'user_updated' => ''
        ]);
    }

    private function insertNotification(UserAccount $user, $title, $description)
    {
        $this->notificationRepository->create([
            'title' => $title,
            'status' => '1',
            'description' => $description,
            'user_account_id' => $user->id,
            'user_created' => $user->id
        ]);
    }


    private function validationResponse(UserAccount $user, $response, string $billerCode)
    {
        $data = array();
        $data += array('serviceFee' => (string)$this->getServiceFee($user));
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

            if ($status === TransactionStatuses::success
            ) {
                $this->transactionHistories->log(
                    $payBill->user_account_id,
                    $payBill->transaction_category_id,
                    $payBill->id,
                    $payBill->reference_number,
                    $payBill->total_amount,
                    $payBill->transaction_date,
                    $payBill->user_account_id
                );

                $acctNumber = $this->userAccountRepository->getAccountNumber($payBill->user_account_id);

                $this->logHistory->create([
                    'user_account_id' => $payBill->user_account_id,
                    'reference_number' =>  $payBill->reference_number,
                    'squidpay_module' => 'Pay Bills',
                    'namespace' => 'PB',
                    'transaction_date' => Carbon::now(),
                    'remarks' => $acctNumber . ' has paid '. $payBill->total_amount .' to ' . $payBill->billers_name,
                    'operation' => 'Add and Update',
                    'user_created' => $payBill->user_account_id,
                    'user_updated' => ''
                ]);

            }
        }

        return $payBill;
    }


    private function firstLayerValidation($errorDetails, $billerCode,UserAccount $user)
    {
        $errorCode = $errorDetails['code'];

        if ($errorCode == 1) return $this->accountWithDFO($errorDetails['message'], $errorDetails['validationNumber'],$billerCode, $user);

        if ($errorCode == 2) return $this->disconnectedAccount($errorDetails['message']);
        if ($errorCode == 3) return $this->invalidParameter($errorDetails['message']);
        if ($errorCode == 4) return $this->parameterMissing($errorDetails['message']);
        if ($errorCode == 5) return $this->invalidAccountNumberFormat($errorDetails['message']);
        if ($errorCode == 6) return $this->insufficientAmount($errorDetails['message']);
        if ($errorCode == 7) return $this->maximumAmountExceeded($errorDetails['message']);
        if ($errorCode == 8) return $this->invalidNumericFormat($errorDetails['message']);
        if ($errorCode == 9) return $this->invalidAlphaDashFormat($errorDetails['message']);
        if ($errorCode == 10) return $this->invalidSelectedValue($errorDetails['message']);
        if ($errorCode == 11) return $this->clientReferenceAlreadyExists($errorDetails['message']);
        if ($errorCode == 12) return $this->callBackUrlIsInvalid($errorDetails['message']);
        if ($errorCode == 13) return $this->transactionFrequencyLimitExceeded($errorDetails['message']);
        if ($errorCode == 14) return $this->invalidOtherCharges($errorDetails['message']);
        if ($errorCode == 15) return $this->invalidDateFormat($errorDetails['message']);
        if ($errorCode == 16) return $this->invalidServiceFeeValue($errorDetails['message']);
        if ($errorCode == 17) return $this->walletBalanceBelowThreshold($errorDetails['message']);
        if ($errorCode == 18) return $this->invalidAlphaNumericFormat($errorDetails['message']);
        if ($errorCode == 19) return $this->valueShouldBeSameAsValueOfX($errorDetails['message']);
        if ($errorCode == 20) return $this->accountNumberDidNotPassCheckDigitValidation($errorDetails['message']);
        if ($errorCode == 21) return $this->invalidAmount($errorDetails['message']);
        if ($errorCode == 22) return $this->accountNumberAlreadyExpired($errorDetails['message']);
        if ($errorCode == 23) return $this->transactionAlreadyBeenPaid($errorDetails['message']);
        if ($errorCode == 24) return $this->amountIsAboveWalletLimit($errorDetails['message']);
        if ($errorCode == 25) return $this->theOtherChargesMustbePhp($errorDetails['message']);
        if ($errorCode == 26) return $this->theAccountNumberisNotSupportedByTheBank($errorDetails['message']);
        if ($errorCode == 27) return $this->theAccountNumberMustStartWithAnyOf($errorDetails['message']);
        if ($errorCode == 28) return $this->possibleDuplicateDetected($errorDetails['message']);



    }


}
