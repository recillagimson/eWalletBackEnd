<?php

namespace App\Repositories\OutPayMerchants;

use App\Models\OutPayMerchant;
use App\Repositories\Repository;
use Illuminate\Database\Eloquent\Collection;

class OutPayMerchantRepository extends Repository implements IOutPayMerchantRepository
{
    public function __construct(OutPayMerchant $model)
    {
        parent::__construct($model);
    }

    public function getByUser(string $userId): Collection
    {
        return $this->model->where('user_account_id', $userId)->get();
    }


    public function getByRefNo(string $refNo): Collection
    {
        return $this->model->where('reference_number', $refNo)->get();
    }
}
