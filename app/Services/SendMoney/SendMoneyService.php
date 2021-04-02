<?php
namespace App\Services\SendMoney;
use App\Repositories\OutSendMoney\IOutSendMoneyRepository;
use App\Repositories\InReceiveMoney\IInReceiveMoneyRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserBalanceInfo\IUserBalanceInfoRepository;
use Illuminate\Validation\ValidationException;

class SendMoneyService implements ISendMoneyService
{
    public IOutSendMoneyRepository $outSendMoney;
    public IInReceiveMoneyRepository $inReceiveMoney;
    public IUserAccountRepository $userAccounts;
    public IUserBalanceInfoRepository $userBalanceInfo;

    public function __construct(IOutSendMoneyRepository $outSendMoney, IInReceiveMoneyRepository $inReceiveMoney,IUserAccountRepository $userAccts, IUserBalanceInfoRepository $userBalanceInfo)
    {
        $this->outSendMoney = $outSendMoney;
        $this->inReceiveMoney = $inReceiveMoney;
        $this->userAccounts = $userAccts;
        $this->userBalanceInfo = $userBalanceInfo;
    }


    public function getUserID(string $usernameField, array $fillRequest)
    {
        $user = $this->userAccounts->getByUsername($usernameField, $fillRequest[$usernameField]);
        return $user['id'];
    }


    public function isSelf(string $senderID, string $receiverID)
    {
        if($senderID == $receiverID) { return true; }
        return false;
    }


    public function validateAmount(string $userID, array $fillRequest)
    {
        $balance = $this->userBalanceInfo->getUserBalance($userID);
        if($balance >= $fillRequest['amount']){ return true; }
        return false;
    }


    public function subtractSenderBalance(string $senderID, array $fillRequest)
    {
        $senderBalance = $this->userBalanceInfo->getUserBalance($senderID);
        $newBalance = $senderBalance - $fillRequest['amount'];
        $this->userBalanceInfo->updateUserBalance($senderID, $newBalance);

        return $newBalance;
    }


    public function addReceiverBalance(string $receiverID, array $fillRequest)
    {
        $senderBalance = $this->userBalanceInfo->getUserBalance($receiverID);
        $newBalance = $senderBalance + $fillRequest['amount'];
        $this->userBalanceInfo->updateUserBalance($receiverID, $newBalance);
        
        return $newBalance;
    }


    public function outSendMoney(string $senderID, string $receiverID, array $fillRequest) 
    {
        $this->outSendMoney->create([
            'user_account_id' => $senderID,
            'receiver_id' => $receiverID,
            'reference_number' => $this->generateRefNo(0),
            'amount' => $fillRequest['amount'],
            'service_fee' => 15,
            // 'service_fee_id' => '',
            'total_amount' => $fillRequest['amount'] + 15,
            // 'purpose_of_transfer_id' => '',
            'message' => $fillRequest['message'],
            'status' => false,
            'transaction_date' => date('Y-m-d H:i:s'),
            // 'transaction_category_id' => '',
            'transaction_remarks' => '',
            'user_created' => $senderID,
            'user_updated' => ''
        ]);
    }


    public function inReceiveMoney(string $senderID, string $receiverID, array $fillRequest){
        $this->inReceiveMoney->create([
            'user_account_id' => $receiverID,
            'sender_id' => $senderID,
            'reference_number' => $this->generateRefNo(1),
            'amount' => $fillRequest['amount'],
            'message' => $fillRequest['message'],
            'transaction_date' => date('Y-m-d H:i:s'),
            // 'transaction_category_id' => '',
            'transaction_remarks' => '',
            'status' => false,
            'user_created' => $senderID,
            'user_updated' => ''
        ]);
    }
    

    public function generateRefNo(int $adjust)
    {
        $index = $this->outSendMoney->getLastID() - $adjust;
        $index++;
        return 'SM'.str_pad($index, 7, "0", STR_PAD_LEFT);
    }

    public function errorMessage(string $header, string $message) 
    {
        throw ValidationException::withMessages([
            $header => $message
        ]);
    }


}
