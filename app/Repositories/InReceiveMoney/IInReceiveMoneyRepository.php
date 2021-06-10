<?php
namespace App\Repositories\InReceiveMoney;

use App\Repositories\IRepository;
use App\Models\InReceiveMoney;

interface IInReceiveMoneyRepository extends IRepository
{
    public function getSumOfTransactions($from, $to, $userAccountId);
}
