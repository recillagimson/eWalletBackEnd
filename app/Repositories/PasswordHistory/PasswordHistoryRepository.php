<?php


namespace App\Repositories\PasswordHistory;


use App\Models\PasswordHistory;
use App\Repositories\Repository;

class PasswordHistoryRepository extends Repository implements IPasswordHistoryRepository
{
    public function __construct(PasswordHistory $model)
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

    public function log(string $userId, string $password)
    {
        $data = [
            'user_account_id' => $userId,
            'password' => $password,
            'user_created' => $userId
        ];

        return $this->create($data);
    }

}
