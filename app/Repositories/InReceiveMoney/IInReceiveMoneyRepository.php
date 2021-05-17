<?php
namespace App\Repositories\InReceiveMoney;

use App\Repositories\IRepository;
use App\Models\InReceiveMoney;

interface IInReceiveMoneyRepository extends IRepository
{
    public function getByUserAccountIDBetweenDates(string $userAccountID, string $startDate, string $endDate);
}
