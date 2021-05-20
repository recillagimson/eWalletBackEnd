<?php
namespace App\Repositories\OutSendMoney;

use App\Repositories\IRepository;
use Illuminate\Support\Carbon;

interface IOutSendMoneyRepository extends IRepository
{
    public function getLastRefNo();
    public function getByReceiversIDBetweenDates(string $receiverID, Carbon $startDate, Carbon $endDate);
}

