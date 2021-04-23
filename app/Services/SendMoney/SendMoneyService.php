<?php
namespace App\Services\SendMoney;

use App\Enums\ReferenceNumberTypes;
use App\Enums\SendMoneyConfig;
use App\Repositories\InReceiveMoney\IInReceiveMoneyRepository;
use App\Repositories\LogHistory\ILogHistoryRepository;
use App\Repositories\OutSendMoney\IOutSendMoneyRepository;
use App\Repositories\QrTransactions\IQrTransactionsRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserBalanceInfo\IUserBalanceInfoRepository;
use App\Repositories\UserTransactionHistory\IUserTransactionHistoryRepository;
use App\Services\Utilities\LogHistory\ILogHistoryService;
use App\Services\Utilities\ReferenceNumber\IReferenceNumberService;
use App\Traits\Errors\WithSendMoneyErrors;
use Illuminate\Validation\ValidationException;

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

    
    public function __construct(IOutSendMoneyRepository $outSendMoney, IInReceiveMoneyRepository $inReceiveMoney,
                                IUserAccountRepository $userAccts, IUserBalanceInfoRepository $userBalanceInfo,
                                IQrTransactionsRepository $qrTransactions, IReferenceNumberService $referenceNumberService, ILogHistoryRepository $loghistoryrepository,
                                IUserTransactionHistoryRepository $userTransactionHistoryRepository)
    {
        $this->outSendMoney = $outSendMoney;
        $this->inReceiveMoney = $inReceiveMoney;
        $this->userAccounts = $userAccts;
        $this->userBalanceInfo = $userBalanceInfo;
        $this->qrTransactions = $qrTransactions;
        $this->referenceNumberService = $referenceNumberService;
        $this->loghistoryrepository = $loghistoryrepository;
        $this->userTransactionHistoryRepository = $userTransactionHistoryRepository;
    }


    /**
     * Creates a new record for out_send_money, in_receive_money
     *
     * @param string $username
     * @param array $fillRequest
     * @param object $user
     * @param string $senderID 
     * @param string $receiverID
     * @param boolean $isSelf
     * @param boolean $isEnough
     * @throws ValidationException
     */
    public function send(string $username ,array $fillRequest, object $user)
    {
        $senderID = $user->id;
        $receiverID = $this->getUserID($username, $fillRequest);

        $isSelf = $this->isSelf($senderID, $receiverID);
        $isEnough = $this->checkAmount($senderID, $fillRequest);

        if ($isSelf) $this->invalidRecipient();
        if (!$isEnough) $this->insuficientBalance();

        $fillRequest['refNo'] = $this->referenceNumberService->generate(ReferenceNumberTypes::SendMoney);  
        $this->subtractSenderBalance($senderID, $fillRequest);
        $this->addReceiverBalance($receiverID, $fillRequest);
        $this->outSendMoney($senderID, $receiverID, $fillRequest);
        $this->inReceiveMoney($senderID, $receiverID, $fillRequest);
        $this->logHistories($senderID, $receiverID, $fillRequest);
        $this->userTransactionHistory($senderID, $fillRequest);
    }


    /**
     * Validates Send money
     *
     * @param string $username
     * @param array $fillRequest
     * @param object $user
     * @param string $senderID 
     * @param string $receiverID
     * @param boolean $isSelf
     * @param boolean $isEnough
     * @throws ValidationException
     */
    public function validateSend(string $username, array $fillRequest, object $user)
    {
        $senderID = $user->id;
        $receiverID = $this->getUserID($username, $fillRequest);

        $isSelf = $this->isSelf($senderID, $receiverID);
        $isEnough = $this->checkAmount($senderID, $fillRequest);

        if ($isSelf) $this->invalidRecipient();
        if (!$isEnough) $this->insuficientBalance();
    }


    /**
     * Creates a record for qr_transactions
     *  
     * @param string $username
     * @param object $user
     * @param array $fillRequest
     * @return mixed
     */
    public function generateQR(object $user, array $fillRequest)
    {
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
     * @param string $qrTransaction
     * @param string $user
     *
     * @return array
     */
    public function scanQr(string $id):array
    {
        $qrTransaction = $this->qrTransactions->get($id);
        if (!$qrTransaction) $this->invalidQr();

        $user = $this->userAccounts->get($qrTransaction->user_account_id);
        if (!$user) $this->invalidAccount();

        if ($user->mobile_number) {
            return ['mobile_number' => $user->mobile_number, 'amount' => $qrTransaction->amount, 'message' => ''];
        }
        return ['email' => $user->email, 'amount' => $qrTransaction->amount, 'message' => ''];
    }



    private function getUserID(string $usernameField, array $fillRequest)
    {
        $user = $this->userAccounts->getByUsername($usernameField, $fillRequest[$usernameField]);
        if (!$user) $this->invalidAccount();
        return $user['id'];
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
            'transction_category_id' => SendMoneyConfig::CXSEND,
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
            'reference_number' => $fillRequest['refNo'],
            'amount' => $fillRequest['amount'],
            'message' => $fillRequest['message'],
            'transaction_date' => date('Y-m-d H:i:s'),
            'transction_category_id' => SendMoneyConfig::CXRECEIVE,
            'transaction_remarks' => '',
            'status' => true,
            'user_created' => $senderID,
            'user_updated' => ''
        ]);
    }


    private function logHistories($senderID, $receiverID, $fillRequest)
    {
        $this->loghistoryrepository->create([
            'user_account_id' => $senderID,
            'reference_number' => $fillRequest['refNo'],
            'squidpay_module' => 'Send Money',
            'namespace' => 'SM',
            'transaction_date' => date('Y-m-d H:i:s'),
            'remarks' => $senderID . ' sent money to ' . $receiverID,
            'operation' => 'Add and Update',
            'user_created' => $senderID,
            'user_updated' => ''
        ]);
    }

    
    private function userTransactionHistory($senderID, $fillRequest)
    {
        $this->userTransactionHistoryRepository->create([
            'user_account_id' => $senderID,
            'transaction_id' => SendMoneyConfig::CXSEND,
            'reference_number' => $fillRequest['refNo'],
            'transaction_category_id' => 'SM',
            'user_created' => $senderID,
            'user_updated' => ''
        ]);
    }

}
