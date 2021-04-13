<?php
namespace App\Services\SendMoney;

use App\Enums\SendMoneyConfig;
use App\Repositories\OutSendMoney\IOutSendMoneyRepository;
use App\Repositories\InReceiveMoney\IInReceiveMoneyRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserBalanceInfo\IUserBalanceInfoRepository;
use Illuminate\Validation\ValidationException;
use App\Repositories\QrTransactions\IQrTransactionsRepository;

class SendMoneyService implements ISendMoneyService
{
    public IOutSendMoneyRepository $outSendMoney;
    public IInReceiveMoneyRepository $inReceiveMoney;
    public IUserAccountRepository $userAccounts;
    public IUserBalanceInfoRepository $userBalanceInfo;
    public IQrTransactionsRepository $qrTransactions;

    public function __construct(IOutSendMoneyRepository $outSendMoney, IInReceiveMoneyRepository $inReceiveMoney,IUserAccountRepository $userAccts, IUserBalanceInfoRepository $userBalanceInfo, IQrTransactionsRepository $qrTransactions)
    {
        $this->outSendMoney = $outSendMoney;
        $this->inReceiveMoney = $inReceiveMoney;
        $this->userAccounts = $userAccts;
        $this->userBalanceInfo = $userBalanceInfo;
        $this->qrTransactions = $qrTransactions;
    }


    /**
     * Creates a new record for out_send_money, in_receive_money
     * 
     * @param string $username 
     * @param array $fillRequest
     * @param object $user
     */
    public function send(string $username ,array $fillRequest, object $user)
    {
        $senderID = $user->id;
        $receiverID = $this->qrOrSendMoney($username, $fillRequest);
        $fillRequest['refNo'] = $this->generateRefNo();

        $isSelf = $this->isSelf($senderID, $receiverID);
        $isEnough = $this->checkAmount($senderID, $fillRequest);
     
        if ($isSelf) $this->invalidRecipient();
        if (!$isEnough) $this->notEnoughBalance();

        $this->subtractSenderBalance($senderID, $fillRequest);
        $this->addReceiverBalance($receiverID, $fillRequest);
        $this->outSendMoney($senderID, $receiverID, $fillRequest);
        $this->inReceiveMoney($senderID, $receiverID, $fillRequest);
    }


    /**
     * Creates a new record for qr_transactions
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
            'user_created' => '',
            'user_updated' => ''
        ]);
    }


    private function qrOrSendMoney(string $username,array $fillRequest)
    {
        if (!empty($fillRequest['recipient_account_id'])){
            return $fillRequest['recipient_account_id'];
        } 
        return $this->getUserID($username, $fillRequest);
    }


    public function generateRefNo()
    {
        $index = $this->outSendMoney->getLastRefNo();
        $index = substr($index, 2);
        $index++;
        return SendMoneyConfig::RefHeader . str_pad($index, 7, "0", STR_PAD_LEFT);
    }



    public function getUserID(string $usernameField, array $fillRequest)
    {
        $user = $this->userAccounts->getByUsername($usernameField, $fillRequest[$usernameField]);
        if(empty($user)) $this->invalidAccount(); 
        return $user['id'];
    }



    public function isSelf(string $senderID, string $receiverID)
    {
        if($senderID == $receiverID) return true; 
    }



    public function checkAmount(string $senderID, array $fillRequest)
    {
        $balance = $this->userBalanceInfo->getUserBalance($senderID);
        $fillRequest['amount'] = $fillRequest['amount'] + SendMoneyConfig::ServiceFee;
        if($balance >= $fillRequest['amount']) return true; 
    }
   


    public function subtractSenderBalance(string $senderID, array $fillRequest)
    {
        $senderBalance = $this->userBalanceInfo->getUserBalance($senderID);
        $balanceSubtractByServiceFee = $senderBalance - SendMoneyConfig::ServiceFee;
        $newBalance = $balanceSubtractByServiceFee - $fillRequest['amount'];
        $this->userBalanceInfo->updateUserBalance($senderID, $newBalance);
    }



    public function addReceiverBalance(string $receiverID, array $fillRequest)
    {
        $receiverBalance = $this->userBalanceInfo->getUserBalance($receiverID);
        $newBalance = $receiverBalance + $fillRequest['amount'];
        $this->userBalanceInfo->updateUserBalance($receiverID, $newBalance);
    }



    public function outSendMoney(string $senderID, string $receiverID, array $fillRequest) 
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
            'transction_category_id' => '1a86b905-929a-11eb-9663-1c1b0d14e211',
            'transaction_remarks' => '',
            'user_created' => '',
            'user_updated' => ''
        ]);
    }



    public function inReceiveMoney(string $senderID, string $receiverID, array $fillRequest){
        return $this->inReceiveMoney->create([
            'user_account_id' => $receiverID,
            'sender_id' => $senderID,
            'reference_number' => $fillRequest['refNo'],
            'amount' => $fillRequest['amount'],
            'message' => $fillRequest['message'],
            'transaction_date' => date('Y-m-d H:i:s'),
            'transction_category_id' => '1a86b905-929a-11eb-9663-1c1b0d14e211',
            'transaction_remarks' => '',
            'status' => true,
            'user_created' => '',
            'user_updated' => ''
        ]);
    }

    
    
    public function invalidRecipient() 
    {
        throw ValidationException::withMessages([
            'email | mobile number' => 'Not allowed to send to your own account'
        ]);
    }

    public function notEnoughBalance()
    {
        throw ValidationException::withMessages([
            'amount' => 'Not enough balance'
        ]);
    }

    public function invalidAccount()
    {
        throw ValidationException::withMessages([
            'amount' => 'Account does not exists'
        ]);
    }

}
