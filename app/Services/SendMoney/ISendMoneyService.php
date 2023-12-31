<?php
namespace App\Services\SendMoney;

use App\Models\UserAccount;
use App\Repositories\InReceiveMoney\IInReceiveMoneyRepository;
use App\Repositories\OutSendMoney\IOutSendMoneyRepository;

/**
 * @property IOutSendMoneyRepository $sendMoney
 * @property IInReceiveMoneyRepository $receiveMoney
 *
 */
interface ISendMoneyService
{
    public function send(string $usernameField, array $fillRequest, UserAccount $user);

    public function sendValidate(string $usernameField, array $fillRequest, UserAccount $user);

    public function generateQR(object $user, array $fillRequest);

    public function scanQr(string $id);

    public function getQr(UserAccount $user);
}
