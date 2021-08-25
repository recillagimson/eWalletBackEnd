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
use App\Traits\UserHelpers;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\Client\Response;
use Str;

trait PayBillsHelpers
{
    use WithUserErrors, WithTpaErrors, UserHelpers;

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

            $description = 'Hi Squidee! Your payment of P' . $fillRequest['amount'] . ' to ' . $fillRequest['biller'] . ' with fee ' . $fillRequest['serviceFee'] . '. has been successfully processed on ' . date('Y-m-d H:i:s') . ' with Ref No. ' . $fillRequest['refNo'] . '. Visit https://my.squid.ph/ for more information or contact support@squid.ph.';
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


    private function firstLayerValidation(string $billerCode, $accountNumber, $data)
    {

        if(empty($data['amount'])) return $this->noAmountProvided();
        
        //1ST BILLERS

        if ($billerCode === 'MWCOM') {
            if (Str::length($accountNumber) != 8) return $this->invalidDigitsLength(8);
            if ($data['amount'] < 20.00) return $this->minimumAmount(20.00);
        }
        if ($billerCode === 'MECOR') {
            if (Str::length($accountNumber) != 10) return $this->invalidDigitsLength(10);
            if ($data['amount'] < 5.00) return $this->minimumAmount(5.00);
        }
        if ($billerCode === 'MWSIN') {
            if (Str::length($accountNumber) != 8) return $this->invalidDigitsLength(8);
            if ($data['amount'] < 20.00) return $this->minimumAmount(20.00);
        }
        if ($billerCode === 'RFID1') {
            // Random Test accounts are still accepting, biller errror
        }
        if ($billerCode === 'ETRIP') {
            if (Str::length($accountNumber) != 12) return $this->invalidDigitsLength(12);
            if ($data['amount'] < 500.00) return $this->minimumAmount(500.00);
        }
        if ($billerCode === 'SMART') {
            if (Str::length($accountNumber) != 10) return $this->invalidDigitsLength(10);
            if (empty($data['otherInfo']['Product'])) return $this->requiredField('product code', 'Product');
            if (empty($data['otherInfo']['TelephoneNumber'])) return $this->requiredField('telephone number', 'TelephoneNumber');
            if ($data['amount'] < 1.00) return $this->minimumAmount(1.00);
        }
        if ($billerCode === 'SSS03') {
            if (Str::length($accountNumber) < 10  || Str::length($accountNumber) > 13) return $this->invalidDigitsLength("10 - 13");
            if (empty($data['otherInfo']['PayorType'])) return $this->requiredField('payor type', 'PayorType');
            if (empty($data['otherInfo']['RelType'])) return $this->requiredField('relation type', 'RelType');
            if (empty($data['otherInfo']['LoanAccountNo'])) return $this->requiredField('loan account number', 'LoanAccountNo');
            if (empty($data['otherInfo']['LastName'])) return $this->requiredField('last name', 'LastName');
            if (empty($data['otherInfo']['FirstName'])) return $this->requiredField('first name', 'FirstName');
            if (empty($data['otherInfo']['MI'])) return $this->requiredField('middle initial', 'MI');
            if (empty($data['otherInfo']['PlatformType'])) return $this->requiredField('platform type', 'PlatformType');
            if ($data['amount'] < 1.00) return $this->minimumAmount(1.00);
        }
        if ($billerCode === 'PRULI') {
            if (Str::length($accountNumber) != 8) return $this->invalidDigitsLength(8);
            if (empty($data['otherInfo']['AccountName'])) return $this->requiredField('account name', 'AccountName');
            if (empty($data['otherInfo']['DueDate'])) return $this->requiredField('due date', 'DueDate');
            if ($data['amount'] < 1.00) return $this->minimumAmount(1.00);
        }

        // 2ND BILLERS

        if ($billerCode === 'SKY01') {
           // test accounts not working
        }
        if ($billerCode === 'MBCCC') {
            if (Str::length($accountNumber) != 16) return $this->invalidDigitsLength(16);
            if (empty($data['otherInfo']['ConsName'])) return $this->requiredField('consumer name', 'ConsName');
            if ($data['amount'] < 1.00) return $this->minimumAmount(1.00);
        }
        if ($billerCode === 'BPI00') {
            if (Str::length($accountNumber) != 16) return $this->invalidDigitsLength(16);
            if (empty($data['otherInfo']['ConsName'])) return $this->requiredField('consumer name', 'ConsName');
            if ($data['amount'] < 1.00) return $this->minimumAmount(1.00);
        }
        if ($billerCode === 'UNBNK') {
            if (Str::length($accountNumber) != 16) return $this->invalidDigitsLength(16);
            if (!isset($data['otherInfo']['Service'])) return $this->requiredField('service', 'Service');
            if (empty($data['otherInfo']['ConsName'])) return $this->requiredField('consumer name', 'ConsName');
            if ($data['amount'] < 1.00) return $this->minimumAmount(1.00);
        }
        if ($billerCode === 'SPLAN') {
            if (Str::length($accountNumber) != 15) return $this->invalidCharacterLength(15);
            if (!isset($data['otherInfo']['PlanType'])) return $this->requiredField('plan type', 'PlanType');
            if (empty($data['otherInfo']['AccountName'])) return $this->requiredField('account name', 'AccountName');
            if ($data['amount'] < 1.00) return $this->minimumAmount(1.00);
        }
        if ($billerCode === 'PILAM') {
            if (Str::length($accountNumber) != 10) return $this->invalidDigitsLength(10);
            if (empty($data['otherInfo']['LastName'])) return $this->requiredField('due date', 'DueDate');
            if ($data['amount'] < 1.00) return $this->minimumAmount(1.00);
        }
        if ($billerCode === 'ADMSN') {
            // Pay at Adamson University through over the counter (error)*
            
            // if (Str::length($accountNumber) != 9) return $this->invalidDigitsLength(9);
            // if (empty($data['otherInfo']['LastName'])) return $this->requiredField('last name', 'LastName');
            // if (empty($data['otherInfo']['FirstName'])) return $this->requiredField('first name', 'FirstName');
            // if (empty($data['otherInfo']['MiddleName'])) return $this->requiredField('middle name', 'MiddleName');
            // if (empty($data['otherInfo']['PaymentType'])) return $this->requiredField('payment type', 'PaymentType');
            // if (empty($data['otherInfo']['SchoolYear'])) return $this->requiredField('school year', 'SchoolYear');
            // if (empty($data['otherInfo']['Term'])) return $this->requiredField('Term', 'Term');
        }
        if ($billerCode === 'UBNK4') {
            if (Str::length($accountNumber) < 8  || Str::length($accountNumber) > 15) return $this->invalidCharacterLength("8 - 15");
            if (empty($data['otherInfo']['StudentName'])) return $this->requiredField('student number', 'StudentName');
            if (empty($data['otherInfo']['Branch'])) return $this->requiredField('branch', 'Branch');
            if ($data['amount'] < 1.00) return $this->minimumAmount(1.00);
        }
        if ($billerCode === 'MCARE') {
            // Test Account not working
        }
        if ($billerCode === 'ASLNK') {
            if (Str::length($accountNumber) != 16) return $this->invalidDigitsLength(16);
            if (empty($data['otherInfo']['LastName'])) return $this->requiredField('last name', 'LastName');
            if (empty($data['otherInfo']['FirstName'])) return $this->requiredField('first name', 'FirstName');
            if (empty($data['otherInfo']['MiddleName'])) return $this->requiredField('middle name', 'MiddleName');
            if ($data['amount'] < 1.00) return $this->minimumAmount(1.00);
        }

        // 3rd BILLERS

        if ($billerCode === 'CNVRG') {
            if (Str::length($accountNumber) != 13) return $this->invalidDigitsLength(13);
            if (empty($data['otherInfo']['AccountName'])) return $this->requiredField('account name', 'AccountName');
            if ($data['amount'] < 1.00) return $this->minimumAmount(1.00);
        }
        if ($billerCode === 'PLDT6') {
            // Test Account not working
        }
        if ($billerCode === 'AEON1') {
            if (Str::length($accountNumber) != 10) return $this->invalidDigitsLength(10);
            if (empty($data['otherInfo']['PartnerRefNo'])) return $this->requiredField('PartnerRefNo', 'PartnerRefNo');
            if ($data['amount'] < 1.00) return $this->minimumAmount(1.00);
        }
        if ($billerCode === 'BNECO') {
            if (Str::length($accountNumber) != 11) return $this->invalidDigitsLength(11);
            if (empty($data['otherInfo']['LastName'])) return $this->requiredField('last name', 'LastName');
            if (empty($data['otherInfo']['FirstName'])) return $this->requiredField('first name', 'FirstName');
            if (empty($data['otherInfo']['MiddleName'])) return $this->requiredField('middle name', 'MiddleName');
            if (empty($data['otherInfo']['DueDate'])) return $this->requiredField('due date', 'DueDate');
            if ($data['amount'] < 1.00) return $this->minimumAmount(1.00);
        }
        if ($billerCode === 'AECOR') {
            if (Str::length($accountNumber) != 16) return $this->invalidDigitsLength(16);
            if (empty($data['otherInfo']['DueDate'])) return $this->requiredField('due date', 'DueDate');
            if ($data['amount'] < 1.00) return $this->minimumAmount(1.00);
        }
        if ($billerCode === 'LAZAE') {
            // No test account provided
        }
        if ($billerCode === 'DFA01') {
            // No test account provided
        }
        if ($billerCode === 'POEA1') {
            // No test account provided
        }
        if ($billerCode === 'SSS01') {
            // No test account provided
        }
        if ($billerCode === 'SSS02') {
            // No test account provided
        }


    }
    

}
