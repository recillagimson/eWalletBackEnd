<?php

namespace App\Services\SendMoney;

use App\Enums\ErrorCodes;
use App\Enums\OtpTypes;
use App\Enums\ReferenceNumberTypes;
use App\Enums\SendMoneyConfig;
use App\Enums\TransactionCategoryIds;
use App\Enums\UsernameTypes;
use App\Models\UserAccount;
use App\Repositories\InReceiveMoney\IInReceiveMoneyRepository;
use App\Repositories\LogHistory\ILogHistoryRepository;
use App\Repositories\Notification\INotificationRepository;
use App\Repositories\OutSendMoney\IOutSendMoneyRepository;
use App\Repositories\QrTransactions\IQrTransactionsRepository;
use App\Repositories\ServiceFee\IServiceFeeRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserBalanceInfo\IUserBalanceInfoRepository;
use App\Repositories\UserTransactionHistory\IUserTransactionHistoryRepository;
use App\Repositories\UserUtilities\UserDetail\IUserDetailRepository;
use App\Services\Transaction\ITransactionValidationService;
use App\Services\Utilities\Notifications\Email\IEmailService;
use App\Services\Utilities\Notifications\INotificationService;
use App\Services\Utilities\Notifications\SMS\ISmsService;
use App\Services\Utilities\OTP\IOtpService;
use App\Services\Utilities\ReferenceNumber\IReferenceNumberService;
use App\Traits\Errors\WithSendMoneyErrors;
use App\Traits\StringHelpers;
use App\Traits\UserHelpers;
use Carbon\Carbon;
use DB;
use Exception;
use Throwable;

class SendMoneyService implements ISendMoneyService
{
    use WithSendMoneyErrors, UserHelpers, StringHelpers;

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
    private INotificationRepository $notificationRepository;
    private IServiceFeeRepository $serviceFeeRepository;

    public function __construct(
        IOutSendMoneyRepository           $outSendMoney,
        IInReceiveMoneyRepository         $inReceiveMoney,
        IUserAccountRepository            $userAccts,
        IUserBalanceInfoRepository        $userBalanceInfo,
        IQrTransactionsRepository         $qrTransactions,
        IReferenceNumberService           $referenceNumberService,
        ILogHistoryRepository             $loghistoryrepository,
        IUserTransactionHistoryRepository $userTransactionHistoryRepository,
        IUserDetailRepository             $userDetailRepository,
        INotificationService              $notificationService,
        IEmailService                     $emailService,
        ISmsService                       $smsService,
        IOtpService                       $otpService,
        ITransactionValidationService     $transactionValidationService,
        INotificationRepository           $notificationRepository,
        IServiceFeeRepository             $serviceFeeRepository
    )
    {
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
        $this->notificationRepository = $notificationRepository;
        $this->serviceFeeRepository = $serviceFeeRepository;
    }


    /**
     * Creates a new record for out_send_money, in_receive_money
     *
     * @param string $usernameField
     * @param array $fillRequest
     * @param UserAccount $user
     * @return array
     * @throws Throwable
     */
    public function send(string $usernameField, array $fillRequest, UserAccount $user): array
    {
        $senderID = $user->id;
        $receiverID = $this->getUserID($usernameField, $fillRequest);
        $receiverUser = $this->userAccounts->get($receiverID);

        $isSelf = $this->isSelf($senderID, $receiverID);
        $isEnough = $this->checkAmount($senderID, $fillRequest, $user);
        $receiverDetails = $this->userDetails($receiverID);
        $senderDetails = $this->userDetails($senderID);
        $senderAccount = $this->userAccounts->get($senderID);
        $receiverAccount = $this->userAccounts->get($receiverID);
        $identifier = OtpTypes::sendMoney . ':' . $user->id;

        $this->otpService->ensureValidated($identifier, $user->otp_enabled);
        if ($isSelf) $this->invalidRecipient();
        if (!$isEnough) $this->insuficientBalance();
        if (!$receiverDetails) $this->recipientDetailsNotFound();
        if (!$senderDetails) $this->senderDetailsNotFound();

        $this->checkMonthlyLimitForSender($senderAccount, $fillRequest);
        $this->checkMonthlyLimitForReceiver($receiverAccount, $fillRequest);

        DB::beginTransaction();
        try {
            $fillRequest['refNo'] = $this->referenceNumberService->generate(ReferenceNumberTypes::SendMoney);
            $fillRequest['refNoRM'] = $this->referenceNumberService->generate(ReferenceNumberTypes::ReceiveMoney);
            $outSendMoney = $this->outSendMoney($senderID, $receiverID, $fillRequest, $user);
            $inReceiveMoney = $this->inReceiveMoney($senderID, $receiverID, $fillRequest);

            $this->subtractSenderBalance($senderID, $fillRequest, $user);
            $this->addReceiverBalance($receiverID, $fillRequest, $user);
            $this->logHistories($senderID, $receiverID, $fillRequest);
            $this->userTransactionHistory($senderID, $receiverID, $outSendMoney, $inReceiveMoney, $fillRequest, $user);
            $this->senderNotification($user, $usernameField, $fillRequest, $receiverID, $senderID);
            $this->recipientNotification($receiverUser, $usernameField, $fillRequest, $senderID, $receiverID);

            DB::commit();
            return $this->sendMoneyResponse($receiverDetails, $fillRequest, $usernameField, $user);

        } catch (Exception $e) {
            DB::rollBack();
        }

    }


    /**
     * Validates Send money
     *
     * @param string $username
     * @param array $fillRequest
     * @param UserAccount $user
     * @return array
     */
    public function sendValidate(string $username, array $fillRequest, UserAccount $user)
    {
        $senderID = $user->id;
        $receiverID = $this->getUserID($username, $fillRequest);

        $isSelf = $this->isSelf($senderID, $receiverID);
        $isEnough = $this->checkAmount($senderID, $fillRequest, $user);
        $receiverDetails = $this->userDetails($receiverID);
        $senderDetails = $this->userDetails($senderID);
        $senderAccount = $this->userAccounts->get($senderID);
        $receiverAccount = $this->userAccounts->get($receiverID);

        if ($isSelf) $this->invalidRecipient();
        if (!$isEnough) $this->insuficientBalance();
        if (!$receiverDetails) $this->recipientDetailsNotFound();
        if (!$senderDetails) $this->senderDetailsNotFound();

        $this->checkMonthlyLimitForSender($senderAccount, $fillRequest);
        $this->checkMonthlyLimitForReceiver($receiverAccount, $fillRequest);

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


    private function sendMoneyReview(string $userID): array
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


    private function sendMoneyResponse($receiverDetails, $fillRequest, $username, UserAccount $user): array
    {
        return [
            'first_name' => $receiverDetails->first_name,
            'middle_name' => $receiverDetails->middle_name,
            'last_name' => $receiverDetails->last_name,
            'name_extension' => $receiverDetails->extension,
            $username => $fillRequest[$username],
            'message' => $fillRequest['message'],
            'reference_number' => $fillRequest['refNo'],
            'total_amount' => $fillRequest['amount'] + $this->getServiceFee($user, true),
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


    private function checkAmount(string $senderID, array $fillRequest, UserAccount $user)
    {
        $balance = $this->userBalanceInfo->getUserBalance($senderID);
        $fillRequest['amount'] = $fillRequest['amount'] + $this->getServiceFee($user, true);
        if ($balance >= $fillRequest['amount']) return true;
    }


    private function subtractSenderBalance(string $senderID, array $fillRequest, UserAccount $user)
    {
        $senderBalance = $this->userBalanceInfo->getUserBalance($senderID);
        $balanceSubtractByServiceFee = $senderBalance - $this->getServiceFee($user, true);
        $newBalance = $balanceSubtractByServiceFee - $fillRequest['amount'];
        $this->userBalanceInfo->updateUserBalance($senderID, $newBalance);
    }


    private function addReceiverBalance(string $receiverID, array $fillRequest, UserAccount $user)
    {
        $receiverBalance = $this->userBalanceInfo->getUserBalance($receiverID);
        $newBalance = $receiverBalance + $fillRequest['amount'] - $this->getServiceFee($user, false);
        $this->userBalanceInfo->updateUserBalance($receiverID, $newBalance);
    }


    private function getServiceFee(UserAccount $user, $isSend)
    {
        if ($isSend) {
            $serviceFee = $this->serviceFeeRepository->getByTierAndTransCategory($user->tier_id, TransactionCategoryIds::sendMoneyToSquidPayAccount);
            $serviceFeeAmount = $serviceFee ? $serviceFee->amount : 0;
        } else {
            $serviceFee = $this->serviceFeeRepository->getByTierAndTransCategory($user->tier_id, TransactionCategoryIds::receiveMoneyToSquidPayAccount);
            $serviceFeeAmount = $serviceFee ? $serviceFee->amount : 0;
        }

        return $serviceFeeAmount;
    }


    private function checkMonthlyLimitForSender($senderAccount, array $fillRequest)
    {
        $this->transactionValidationService->checkUserMonthlyTransactionLimit($senderAccount, $fillRequest['amount'], TransactionCategoryIds::sendMoneyToSquidPayAccount);
    }


    private function checkMonthlyLimitForReceiver($receiverAccount, array $fillRequest)
    {
        $this->transactionValidationService->checkUserMonthlyTransactionLimit($receiverAccount, $fillRequest['amount'], TransactionCategoryIds::receiveMoneyToSquidPayAccount, ['key' => ErrorCodes::receiverMonthlyLimitExceeded, 'value' => 'Receiver Transaction Limit reached.']);
    }


    private function senderNotification(UserAccount $user, string $username, array $fillRequest, string $receiverID,
                                        string      $senderID)
    {
        $userDetail = $this->userDetailRepository->getByUserId($receiverID);
        $fillRequest['serviceFee'] = $this->getServiceFee($user, true);
        $fillRequest['newBalance'] = round($this->userBalanceInfo->getUserBalance($senderID), 2);

        $usernameField = $this->getUsernameFieldByAvailability($user);
        $username = $this->getUsernameByField($user, $usernameField);
        $notifService = $usernameField === UsernameTypes::Email ? $this->emailService : $this->smsService;
        $notifService->sendMoneySenderNotification($username, $fillRequest, $userDetail->first_name);

        $strAmount = $this->formatAmount($fillRequest['amount']);
        $strNewBalance = $this->formatAmount($fillRequest['newBalance']);

        $description = 'You have forwarded: P ' . $strAmount . ' to ' . $userDetail->first_name .
            '. This amount has been debited to your account. Your new balance is P ' . $strNewBalance .
            ' with Ref No. ' . $fillRequest['refNo'] . '. Thank you for using SquidPay!';
        $title = 'SquidPay - Send Money Notification';

        $this->insertNotification($user, $title, $description);
    }

    private function recipientNotification(UserAccount $user, $username, $fillRequest, $senderID, $receiverID)
    {

        $userDetail = $this->userDetailRepository->getByUserId($senderID);
        $fillRequest['newBalance'] = round($this->userBalanceInfo->getUserBalance($receiverID), 2);

        $usernameField = $this->getUsernameFieldByAvailability($user);
        $username = $this->getUsernameByField($user, $usernameField);

        $notifService = $usernameField === UsernameTypes::Email ? $this->emailService : $this->smsService;
        $notifService->sendMoneyRecipientNotification($username, $fillRequest, $userDetail->first_name);

        $strDate = $this->formatDate(Carbon::now());
        $strAmount = $this->formatAmount($fillRequest['amount']);
        $strNewBalance = $this->formatAmount($fillRequest['newBalance']);

        $description = 'Hi Squidee! You have received P' . $strAmount . ' of SquidPay on ' .
            $strDate . ' from ' . $userDetail->first_name . '. Your new balance is P' .
            $strNewBalance . ' with Ref No. ' . $fillRequest['refNo'] .
            '. Use now to buy load, send money, pay bills and a lot more!';

        $title = 'SquidPay - Send Money Notification';
        $this->insertNotification($user, $title, $description);
    }


    private function outSendMoney(string $senderID, string $receiverID, array $fillRequest, UserAccount $user)
    {
        return $this->outSendMoney->create([
            'user_account_id' => $senderID,
            'receiver_id' => $receiverID,
            'reference_number' => $fillRequest['refNo'],
            'amount' => $fillRequest['amount'],
            'service_fee' => $this->getServiceFee($user, true),
            'total_amount' => $fillRequest['amount'] + $this->getServiceFee($user, true),
            'message' => $fillRequest['message'],
            'status' => 'SUCCESS',
            'transaction_date' => Carbon::now(),
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
            'status' => 'SUCCESS',
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


    private function userTransactionHistory($senderID, $receiverID, $outSendMoney, $inReceiveMoney, $fillRequest, UserAccount $user)
    {
        $this->userTransactionHistoryRepository->create([
            'user_account_id' => $senderID,
            'transaction_id' => $outSendMoney->id,
            'reference_number' => $fillRequest['refNo'],
            'total_amount' => $fillRequest['amount'] + $this->getServiceFee($user, true),
            'transaction_category_id' => SendMoneyConfig::CXSEND,
            'user_created' => $senderID,
            'transaction_date' => $outSendMoney->transaction_date
        ]);
        $this->userTransactionHistoryRepository->create([
            'user_account_id' => $receiverID,
            'transaction_id' => $inReceiveMoney->id,
            'reference_number' => $fillRequest['refNoRM'],
            'total_amount' => $fillRequest['amount'] + $this->getServiceFee($user, false),
            'transaction_category_id' => SendMoneyConfig::CXRECEIVE,
            'user_created' => $receiverID,
            'transaction_date' => $outSendMoney->transaction_date
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
}
