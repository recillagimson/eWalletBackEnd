<?php

namespace App\Repositories\InAddMoneyCebuana;

use App\Models\Client;
use App\Models\InAddMoneyCebuana;
use App\Repositories\Repository;

class InAddMoneyCebuanaRepository extends Repository implements IInAddMoneyCebuanaRepository
{
    public function __construct(InAddMoneyCebuana $model)
    {
        parent::__construct($model);
    }

    public function countRecords() {
        return $this->model->count();
    }

    public function getByReferenceNumber(string $referenceCode) {
        return $this->model->with(['user_account', 'user_detail'])->where('cebuana_reference', $referenceCode)->first();
    }
}
