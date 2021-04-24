<?php


namespace App\Repositories\UserKeys\PinCodeHistory;

use App\Models\UserKeys\PinCodeHistory;
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

    public function getPrevious(int $takeCount, string $userId)
    {
        return $this->model->where('user_account_id', '=', $userId)
            ->orderByDesc('created_at')
            ->take($takeCount)
            ->get();
    }

    public function log(string $userId, string $key)
    {
        $data = [
            'user_account_id' => $userId,
            'key' => $key,
            'user_created' => $userId
        ];

        return $this->create($data);
    }

}
