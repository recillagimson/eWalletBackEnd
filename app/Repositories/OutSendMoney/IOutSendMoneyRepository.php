<?php
namespace App\Repositories\OutSendMoney;

use App\Repositories\IRepository;

interface IOutSendMoneyRepository extends IRepository
{
    public function getLastRefNo();
    public function getSumOfTransactions($from, $to, string $userAccountId);
    public function totalSendMoney();
    public function totalamountSendMoney();
    public function totalservicefeeSendMoney();
}

