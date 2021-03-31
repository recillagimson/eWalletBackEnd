<?php
namespace App\Services\SendMoney;
use App\Repositories\OutSendMoney\IOutSendMoneyRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserBalanceInfo\IUserBalanceInfoRepository;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;

class SendMoneyService implements ISendMoneyService
{
    public IOutSendMoneyRepository $outSendMoney;
    public IUserAccountRepository $userAccounts;
    public IUserBalanceInfoRepository $userBalanceInfo;

    public function __construct(IOutSendMoneyRepository $outSendMoney, IUserAccountRepository $userAccts, IUserBalanceInfoRepository $userBalanceInfo)
    {
        $this->outSendMoney = $outSendMoney;
        $this->userAccounts = $userAccts;
        $this->userBalanceInfo = $userBalanceInfo;
    }

    public function getUserID(string $usernameField, array $fillRequest)
    {
        $user = $this->userAccounts->getByUsername($usernameField, $fillRequest[$usernameField]);
        return $user['id'];
    }

    public function validateAmount(string $userID, array $fillRequest)
    {
        $senderBalance = $this->userBalanceInfo->getUserBalance($userID);

        if($senderBalance >= $fillRequest['amount']){
            return true;
        }
        return false;
    }

    public function notEnoughBalance() 
    {
        throw ValidationException::withMessages([
            'amount' => 'not enough balance'
        ]);
    }

    public function subtractSenderBalance(string $senderID, string $receiverID, array $fillRequest){
        $senderBalance = $this->userBalanceInfo->getUserBalance($senderID);
        $newBalance = $senderBalance - $fillRequest['amount'];

        $this->userBalanceInfo->updateUserBalance($senderID, $newBalance);
        return $newBalance;
    }

    
  

}
