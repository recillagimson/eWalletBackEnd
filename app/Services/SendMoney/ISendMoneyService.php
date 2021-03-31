<?php
namespace App\Services\SendMoney;

use App\Repositories\OutSendMoney\IOutSendMoneyRepository;

/**
 * @property IOutSendMoneyRepository $sendMoney
 *
 */
interface ISendMoneyService{
    public function getUserID(string $usernameField, array $fillRequest);
    public function validateAmount(string $userID ,array $fillRequest);
    public function notEnoughBalance();
    public function subtractSenderBalance(string $senderID, string $receiverID, array $fillRequest);
}
    