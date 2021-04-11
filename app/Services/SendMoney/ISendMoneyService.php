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
    public function send(string $usernameField, array $fillRequest, object $user);
    public function generateQR(object $user, array $fillRequest);
}
    