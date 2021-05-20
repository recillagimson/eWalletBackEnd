<?php

namespace App\Repositories\OutSendMoney;

use App\Enums\TransactionStatuses;
use App\Repositories\Repository;
use App\Models\OutSendMoney;
use Illuminate\Support\Carbon;

class OutSendMoneyRepository extends Repository implements IOutSendMoneyRepository
{
    public function __construct(OutSendMoney $model)
    {
        parent::__construct($model);
    }

    public function getLastRefNo()
    {
        return $this->model->orderByDesc('reference_number')->pluck('reference_number')->first();
    }

    public function getByReceiversIDBetweenDates(string $receiverID, Carbon $startDate, Carbon $endDate)
    {
        return $this->model
            ->where('receiver_id', $receiverID)
            ->where('status', '!=', TransactionStatuses::failed)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
    }
}   
