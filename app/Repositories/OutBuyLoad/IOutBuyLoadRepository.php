<?php

namespace App\Repositories\OutBuyLoad;

use App\Repositories\IRepository;
use Illuminate\Support\Carbon;

interface IOutBuyLoadRepository extends IRepository
{
    public function getByUserAccountIDBetweenDates(string $userId, Carbon $startDate, Carbon $endDate);
}
