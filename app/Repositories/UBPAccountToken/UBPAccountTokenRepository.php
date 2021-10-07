<?php

namespace App\Repositories\UBPAccountToken;

use App\Models\UBP\UbpAccountToken;
use App\Repositories\Repository;

class UBPAccountTokenRepository extends Repository implements IUBPAccountTokenRepository
{
    public function __construct(UbpAccountToken $model)
    {
        parent::__construct($model);
    }

    public function getByUser(string $userId): UbpAccountToken
    {
        return $this->model->where('user_account_id', $userId)->first();
    }
}
