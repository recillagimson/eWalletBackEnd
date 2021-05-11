<?php
namespace App\Repositories\OutSendMoney;

use App\Repositories\IRepository;
use Illuminate\Support\Carbon;

interface IOutSendMoneyRepository extends IRepository
{
    public function getLastRefNo();
    public function getByUserAccountIDBetweenDates(string $userId, Carbon $startDate, Carbon $endDate);
}

