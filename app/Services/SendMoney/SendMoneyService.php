<?php

namespace App\Services\SendMoney;

use App\Enums\ErrorCodes;
use Carbon\Carbon;
use App\Enums\OtpTypes;
use App\Enums\SendMoneyConfig;
use App\Enums\ReferenceNumberTypes;
use App\Enums\TransactionCategories;
use App\Enums\TransactionCategoryIds;
use App\Traits\Errors\WithSendMoneyErrors;
use App\Services\Utilities\OTP\IOtpService;
use App\Repositories\LogHistory\ILogHistoryRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Services\Utilities\Notifications\SMS\ISmsService;
use App\Repositories\OutSendMoney\IOutSendMoneyRepository;
use App\Services\Utilities\Notifications\Email\IEmailService;
use App\Repositories\InReceiveMoney\IInReceiveMoneyRepository;
use App\Repositories\QrTransactions\IQrTransactionsRepository;
use App\Services\Utilities\Notifications\INotificationService;
use App\Repositories\UserBalanceInfo\IUserBalanceInfoRepository;
use App\Services\Utilities\ReferenceNumber\IReferenceNumberService;
use App\Repositories\UserUtilities\UserDetail\IUserDetailRepository;
use App\Repositories\UserTransactionHistory\IUserTransactionHistoryRepository;
use App\Services\Transaction\ITransactionValidationService;

class SendMoneyService implements ISendMoneyService
{
    use WithSendMoneyErrors;

    private IOutSendMoneyRepository $outSendMoney;
    private IInReceiveMoneyRepository $inReceiveMoney;
    private IUserAccountRepository $userAccounts;
    private IUserBalanceInfoRepository $userBalanceInfo;
    private IQrTransactionsRepository $qrTransactions;
    private IReferenceNumberService $referenceNumberService;
    private ILogHistoryRepository $loghistoryrepository;
    private IUserTransactionHistoryRepository $userTransactionHistoryRepository;
    private IUserDetailRepository $userDetailRepository;
    private INotificationService $notificationService;
    private IEmailService $emailService;
    private ISmsService $smsService;
    private IOtpService $otpService;
    private ITransactionValidationService $transactionValidationService;

    public function __construct(
        IOutSendMoneyRepository $outSendMoney,
        IInReceiveMoneyRepository $inReceiveMoney,
        IUserAccountRepository $userAccts,
        IUserBalanceInfoRepository $userBalanceInfo,
        IQrTransactionsRepository $qrTransactions,
        IReferenceNumberService $referenceNumberService,
        ILogHistoryRepository $loghistoryrepository,
        IUserTransactionHistoryRepository $userTransactionHistoryRepository,
        IUserDetailRepository $userDetailRepository,
        INotificationService $notificationService,
        IEmailService $emailService,
        ISmsService $smsService,
        IOtpService $otpService,
        ITransactionValidationService $transactionValidationService

    ) {
        $this->outSendMoney = $outSendMoney;
        $this->inReceiveMoney = $inReceiveMoney;
        $this->userAccounts = $userAccts;
        $this->userBalanceInfo = $userBalanceInfo;
        $this->qrTransactions = $qrTransactions;
        $this->referenceNumberService = $referenceNumberService;
        $this->loghistoryrepository = $loghistoryrepository;
        $this->userTransactionHistoryRepository = $userTransactionHistoryRepository;
        $this->userDetailRepository = $userDetailRepository;
        $this->notificationService = $notificationService;
        $this->emailService = $emailService;
        $this->smsService = $smsService;
        $this->otpService = $otpService;
        $this->transactionValidationService = $transactionValidationService;
    }


    /**
     * Creates a new record for out_send_money, in_receive_money
     *
     * @param string $username
     * @param array $fillRequest
     * @param object $user
     * @return array
     */
    public function send(string $username, array $fillRequest, object $user)
    {
        $senderID = $user->id;
        $receiverID = $this->getUserID($username, $fillRequest);

        $isSelf = $this->isSelf($senderID, $receiverID);
        $isEnough = $this->checkAmount($senderID, $fillRequest);
        $receiverDetails = $this->userDetails($receiverID);
        $senderDetails = $this->userDetails($senderID);
        $identifier = OtpTypes::sendMoney . ':' . $user->id;

        $this->otpService->ensureValidated($identifier);
        if ($isSelf) $this->invalidRecipient();
        if (!$isEnough) $this->insuficientBalance();
        if (!$receiverDetails) $this->recipientDetailsNotFound();
        if (!$senderDetails) $this->senderDetailsNotFound();

        // ADD GLOBAL VALIDATION FOR TIER LIMITS (MONTHLY) SEND MONEY
        $sender_account = $this->userAccounts->get($senderID);
        $this->transactionValidationService->checkUserMonthlyTransactionLimit($sender_account, $fillRequest['amount'], TransactionCategoryIds::sendMoneyToSquidPayAccount);
        // ADD GLOBAL VALIDATION FOR TIER LIMITS (MONTHLY) SEND MONEY

        // ADD GLOBAL VALIDATION FOR TIER LIMITS (MONTHLY) RECEIVE MONEY
        $receiver_account = $this->userAccounts->get($receiverID);
        $this->transactionValidationService->checkUserMonthlyTransactionLimit($receiver_account, $fillRequest['amount'], TransactionCategoryIds::receiveMoneyToSquidPayAccount, [ 'key' => ErrorCodes::receiverMonthlyLimitExceeded, 'value' => 'Receiver Transaction Limit reached.' ]);
        // ADD GLOBAL VALIDATION FOR TIER LIMITS (MONTHLY) RECEIVE MONEY

        $fillRequest['refNo'] = $this->referenceNumberService->generate(ReferenceNumberTypes::SendMoney);
        $fillRequest['refNoRM'] = $this->referenceNumberService->generate(ReferenceNumberTypes::ReceiveMoney);

        $this->subtractSenderBalance($senderID, $fillRequest);
        $this->addReceiverBalance($receiverID, $fillRequest);

        $outSendMoney = $this->outSendMoney($senderID, $receiverID, $fillRequest);
        $inReceiveMoney = $this->inReceiveMoney($senderID, $receiverID, $fillRequest);

        $this->logHistories($senderID, $receiverID, $fillRequest);
        $this->userTransactionHistory($senderID, $receiverID, $outSendMoney, $inReceiveMoney, $fillRequest);

        return $this->sendMoneyResponse($receiverDetails, $fillRequest, $username);
    }


    /**
     * Validates Send money
     *
     * @param string $username
     * @param array $fillRequest
     * @param object $user
     * @return array
     */
    public function sendValidate(string $username, array $fillRequest, object $user)
    {
        $senderID = $user->id;
        $receiverID = $this->getUserID($username, $fillRequest);

        $isSelf = $this->isSelf($senderID, $receiverID);
        $isEnough = $this->checkAmount($senderID, $fillRequest);
        $receiverDetails = $this->userDetails($receiverID);
        $senderDetails = $this->userDetails($senderID);

        if ($isSelf) $this->invalidRecipient();
        if (!$isEnough) $this->insuficientBalance();
        if (!$receiverDetails) $this->recipientDetailsNotFound();
        if (!$senderDetails) $this->senderDetailsNotFound();

        // ADD GLOBAL VALIDATION FOR TIER LIMITS (MONTHLY) SEND MONEY
        $sender_account = $this->userAccounts->get($senderID);
        $this->transactionValidationService->checkUserMonthlyTransactionLimit($sender_account, $fillRequest['amount'], TransactionCategoryIds::sendMoneyToSquidPayAccount);
        // ADD GLOBAL VALIDATION FOR TIER LIMITS (MONTHLY) SEND MONEY

        // ADD GLOBAL VALIDATION FOR TIER LIMITS (MONTHLY) RECEIVE MONEY
        $receiver_account = $this->userAccounts->get($receiverID);
        $this->transactionValidationService->checkUserMonthlyTransactionLimit($receiver_account, $fillRequest['amount'], TransactionCategoryIds::receiveMoneyToSquidPayAccount, [ 'key' => ErrorCodes::receiverMonthlyLimitExceeded, 'value' => 'Receiver Transaction Limit reached.' ]);
        // ADD GLOBAL VALIDATION FOR TIER LIMITS (MONTHLY) RECEIVE MONEY

        return $this->sendMoneyReview($receiverID);
    }


    /**
     * Creates a record for qr_transactions
     *
     * @param object $user
     * @param array $fillRequest
     * @return mixed
     */
    public function generateQR(object $user, array $fillRequest)
    {
        $this->generateQRLogHistories($user->id, $fillRequest);
        return $this->qrTransactions->create([
            'user_account_id' => $user->id,
            'amount' => $fillRequest['amount'],
            'status' => true,
            'user_created' => $user->id,
            'user_updated' => ''
        ]);
    }


    /**
     * Use for getting the qr_trasaction by ID
     * Returns user mobile_number or email with amount
     *
     * @param string $id
     * @return array
     */
    public function scanQr(string $id)
    {
        $qrTransaction = $this->qrTransactions->get($id);
        if (!$qrTransaction) $this->invalidQr();

        $user = $this->userAccounts->get($qrTransaction->user_account_id);
        if (!$user) $this->invalidAccount();

        $mobileOrEmail = $this->hasMobileOrEmail($user, $qrTransaction->amount);
        $review = $this->sendMoneyReview($qrTransaction->user_account_id);

        return  array_merge($mobileOrEmail, $review);
    }


    private function hasMobileOrEmail($user, $amount)
    {
        if ($user->mobile_number) {
            return [
                'mobile_number' => $user->mobile_number,
                'amount' => $amount,
                'message' => ''
            ];
        }
        return [
            'email' => $user->email,
            'amount' => $amount,
            'message' => ''
        ];
    }


    private function sendMoneyReview(string $userID)
    {
        $user = $this->userDetailRepository->getByUserId($userID);
        return [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'middle_name' => $user->middle_name,
            'name_extension' => $user->name_extension,
            'selfie_location' => $user->selfie_loction,
        ];
    }


    private function sendMoneyResponse($receiverDetails, $fillRequest, $username)
    {
        return [
            'first_name' => $receiverDetails->first_name,
            'middle_name' => $receiverDetails->middle_name,
            'last_name' =>  $receiverDetails->last_name,
            'name_extension' => $receiverDetails->extension,
            $username => $fillRequest[$username],
            'message' => $fillRequest['message'],
            'reference_number' =>  $fillRequest['refNo'],
            'total_amount' =>  number_format($fillRequest['amount'] + SendMoneyConfig::ServiceFee, 2),
            'transaction_date' => Carbon::now()
        ];
    }


    private function getUserID(string $usernameField, array $fillRequest)
    {
        $user = $this->userAccounts->getByUsername($usernameField, $fillRequest[$usernameField]);
        if (!$user) $this->invalidAccount();
        return $user['id'];
    }


    private function userDetails($userID)
    {
        return $this->userDetailRepository->getByUserId($userID);
    }


    private function isSelf(string $senderID, string $receiverID)
    {
        if ($senderID == $receiverID) return true;
    }


    private function checkAmount(string $senderID, array $fillRequest)
    {
        $balance = $this->userBalanceInfo->getUserBalance($senderID);
        $fillRequest['amount'] = $fillRequest['amount'] + SendMoneyConfig::ServiceFee;
        if ($balance >= $fillRequest['amount']) return true;
    }


    private function subtractSenderBalance(string $senderID, array $fillRequest)
    {
        $senderBalance = $this->userBalanceInfo->getUserBalance($senderID);
        $balanceSubtractByServiceFee = $senderBalance - SendMoneyConfig::ServiceFee;
        $newBalance = $balanceSubtractByServiceFee - $fillRequest['amount'];
        $this->userBalanceInfo->updateUserBalance($senderID, $newBalance);
    }


    private function addReceiverBalance(string $receiverID, array $fillRequest)
    {
        $receiverBalance = $this->userBalanceInfo->getUserBalance($receiverID);
        $newBalance = $receiverBalance + $fillRequest['amount'];
        $this->userBalanceInfo->updateUserBalance($receiverID, $newBalance);
    }


    private function senderNotification($username, $fillRequest, $receiverID, $senderID)
    {
        if(!$username) return null;
        $userDetail  = $this->userDetailRepository->getByUserId($receiverID);
        $fillRequest['serviceFee'] = SendMoneyConfig::ServiceFee;
        $fillRequest['newBalance'] = round($this->userBalanceInfo->getUserBalance($senderID), 2);
        $this->notificationService->sendMoneySenderNotification($username, $fillRequest, $userDetail->first_name);
    }


    private function recipientNotification($username, $fillRequest, $senderID, $receiverID)
    {
        if (!$username) return null;
        $userDetail  = $this->userDetailRepository->getByUserId($senderID);
        $fillRequest['newBalance'] = round($this->userBalanceInfo->getUserBalance($receiverID), 2);
        $this->notificationService->sendMoneyRecipientNotification($username, $fillRequest, $userDetail->first_name);
    }


    private function outSendMoney(string $senderID, string $receiverID, array $fillRequest)
    {
        return $this->outSendMoney->create([
            'user_account_id' => $senderID,
            'receiver_id' => $receiverID,
            'reference_number' => $fillRequest['refNo'],
            'amount' => $fillRequest['amount'],
            'service_fee' => SendMoneyConfig::ServiceFee,
            // 'service_fee_id' => '',
            'total_amount' => $fillRequest['amount'] + SendMoneyConfig::ServiceFee,
            // 'purpose_of_transfer_id' => '',
            'message' => $fillRequest['message'],
            'status' => true,
            'transaction_date' => date('Y-m-d H:i:s'),
            'transaction_category_id' => SendMoneyConfig::CXSEND,
            'transaction_remarks' => '',
            'user_created' => $senderID,
            'user_updated' => ''
        ]);
    }


    private function inReceiveMoney(string $senderID, string $receiverID, array $fillRequest)
    {
        return $this->inReceiveMoney->create([
            'user_account_id' => $receiverID,
            'sender_id' => $senderID,
            'reference_number' => $fillRequest['refNoRM'],
            'out_send_money_reference_number' => $fillRequest['refNo'],
            'amount' => $fillRequest['amount'],
            'message' => $fillRequest['message'],
            'transaction_date' => date('Y-m-d H:i:s'),
            'transaction_category_id' => SendMoneyConfig::CXRECEIVE,
            'transaction_remarks' => '',
            'status' => 'SUCC',
            'user_created' => $senderID,
            'user_updated' => ''
        ]);
    }


    private function logHistories($senderID, $receiverID, $fillRequest)
    {
        $senderAccountNumber = $this->userAccounts->getAccountNumber($senderID);
        $receiverAccountNumber = $this->userAccounts->getAccountNumber($receiverID);
        $this->loghistoryrepository->create([
            'user_account_id' => $senderID,
            'reference_number' => $fillRequest['refNo'],
            'squidpay_module' => 'Send Money',
            'namespace' => 'SM',
            'transaction_date' => Carbon::now(),
            'remarks' => $senderAccountNumber . ' has sent money via Squidpay to ' . $receiverAccountNumber,
            'operation' => 'Add and Update',
            'user_created' => $senderID,
            'user_updated' => ''
        ]);
        $this->loghistoryrepository->create([
            'user_account_id' => $senderID,
            'reference_number' => $fillRequest['refNo'],
            'squidpay_module' => 'Receive Money',
            'namespace' => 'RM',
            'transaction_date' => Carbon::now(),
            'remarks' => $receiverAccountNumber . ' has received money via Squidpay from ' . $senderAccountNumber,
            'operation' => 'Add and Update',
            'user_created' => $senderID,
            'user_updated' => ''
        ]);
    }

    private function generateQRLogHistories($userID , $fillRequest)
    {
        $acctNumber = $this->userAccounts->getAccountNumber($userID);
        $this->loghistoryrepository->create([
            'user_account_id' => $userID,
            'reference_number' => $fillRequest && isset($fillRequest['refNo']) ? $fillRequest['refNo'] : "N/A",
            'squidpay_module' => 'Send Money',
            'namespace' => 'SM',
            'transaction_date' => Carbon::now(),
            'remarks' => $acctNumber . ' has generated QR code for ' . $fillRequest['amount'],
            'operation' => 'Add and Update',
            'user_created' => $userID,
            'user_updated' => ''
        ]);
    }


    private function userTransactionHistory($senderID, $receiverID, $outSendMoney, $inReceiveMoney, $fillRequest)
    {
        $this->userTransactionHistoryRepository->create([
            'user_account_id' => $senderID,
            'transaction_id' => $outSendMoney->id,
            'reference_number' => $fillRequest['refNo'],
            'total_amount' => $fillRequest['amount'] + SendMoneyConfig::ServiceFee,
            'transaction_category_id' => SendMoneyConfig::CXSEND,
            'user_created' => $senderID,
            'user_updated' => ''
        ]);
        $this->userTransactionHistoryRepository->create([
            'user_account_id' => $receiverID,
            'transaction_id' => $inReceiveMoney->id,
            'reference_number' => $fillRequest['refNoRM'],
            'total_amount' => $fillRequest['amount'] + SendMoneyConfig::ServiceFee,
            'transaction_category_id' => SendMoneyConfig::CXRECEIVE,
            'user_created' => $receiverID,
            'user_updated' => ''
        ]);
    }
}
