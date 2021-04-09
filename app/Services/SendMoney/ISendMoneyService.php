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
    public function sendMoney(string $usernameField, array $fillRequest, object $user);
    public function createUserQR(object $user, array $fillRequest);
}
    