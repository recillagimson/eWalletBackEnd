<?php


namespace App\Repositories\PinCodeHistory;

use App\Models\PinCodeHistory;
use App\Repositories\Repository;

class PinCodeHistoryRepository extends Repository implements IPinCodeHistoryRepository
{
    public function __construct(PinCodeHistory $model)
    {
        parent::__construct($model);
    }

    public function getLatest(string $userId)
    {
        return $this->model->where([
            'user_account_id' => $userId,
            'expired' => false
        ])->orderByDesc('created_at')->first();
    }

    public function log(string $userId, string $pinCode)
    {
        $data = [
            'user_account_id' => $userId,
            'pin_code' => $pinCode,
            'user_created' => $userId
        ];

        return $this->create($data);
    }

}
