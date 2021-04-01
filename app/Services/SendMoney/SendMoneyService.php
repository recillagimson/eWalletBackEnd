<?php
namespace App\Services\SendMoney;
use App\Repositories\OutSendMoney\IOutSendMoneyRepository;
use App\Repositories\InSendMoney\IInSendMoneyRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserBalanceInfo\IUserBalanceInfoRepository;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;

class SendMoneyService implements ISendMoneyService
{
    public IOutSendMoneyRepository $outSendMoney;
    public IInSendMoneyRepository $inSendMoney;
    public IUserAccountRepository $userAccounts;
    public IUserBalanceInfoRepository $userBalanceInfo;

    public function __construct(IOutSendMoneyRepository $outSendMoney, IInSendMoneyRepository $inSendMoney,IUserAccountRepository $userAccts, IUserBalanceInfoRepository $userBalanceInfo)
    {
        $this->outSendMoney = $outSendMoney;
        $this->inSendMoney = $inSendMoney;
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

    public function outSendMoney(string $senderID, string $receiverID, array $fillRequest){

    }

    public function inSendMoney(string $senderID, string $receiverID, array $fillRequest){
        
    }
    
    public function errorMessage(string $header, string $message) 
    {
        throw ValidationException::withMessages([
            $header => $message
        ]);
    }


}
