<?php
namespace App\Services\SendMoney;

use App\Repositories\OutSendMoney\IOutSendMoneyRepository;
use App\Repositories\InReceiveMoney\IInReceiveMoneyRepository;

/**
 * @property IOutSendMoneyRepository $sendMoney
 * @property IInReceiveMoneyRepository $receiveMoney 
 *
 */
interface ISendMoneyService{
    public function getUserID(string $usernameField, array $fillRequest);
    public function checkAmount(string $userID ,array $fillRequest);
    public function errorMessage(string $header, string $message);
    public function subtractSenderBalance(string $senderID, array $fillRequest);
    public function addReceiverBalance(string $receiverID, array $fillRequest);
    public function isSelf(string $senderID, string $receiverID);
    public function outSendMoney(string $senderID, string $receiverID, array $fillRequest);
    public function inReceiveMoney(string $senderID, string $receiverID, array $fillRequest);
    public function generateRefNo();
    public function createUserQR(object $user, array $fillRequest);
}
    