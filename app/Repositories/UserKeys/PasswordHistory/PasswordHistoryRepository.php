<?php


namespace App\Repositories\UserKeys\PasswordHistory;


use App\Models\UserKeys\PasswordHistory;
use App\Repositories\Repository;

class PasswordHistoryRepository extends Repository implements IPasswordHistoryRepository
{
    public function __construct(PasswordHistory $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function getLatest(string $userId)
    {
        return $this->model->where([
            'user_account_id' => $userId,
            'expired' => false
        ])->orderByDesc('created_at')->first();
    }

    public function log(string $userId, string $key)
    {
        $latesPassword = $this->getLatest($userId);
        if ($latesPassword) {
            $latesPassword->expired = true;
            $latesPassword->user_updated = $userId;
            $latesPassword->save();
        }

        $data = [
            'user_account_id' => $userId,
            'key' => $key,
            'user_created' => $userId
        ];

        return $this->create($data);
    }

    public function getPrevious(int $takeCount, string $userId)
    {
        return $this->model->where('user_account_id', '=', $userId)
            ->orderByDesc('created_at')
            ->take($takeCount)
            ->get();
    }
}
