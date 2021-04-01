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
    public function errorMessage(string $header, string $message);
    public function subtractSenderBalance(string $senderID, array $fillRequest);
    public function addReceiverBalance(string $receiverID, array $fillRequest);
    public function isSelf(string $senderID, string $receiverID);
}
    