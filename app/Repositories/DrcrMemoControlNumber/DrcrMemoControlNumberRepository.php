<?php

namespace App\Repositories\DrcrMemoControlNumber;

use App\Enums\DrcrStatus;
use App\Enums\TransactionStatuses;
use App\Models\DrcrMemoControlNumber;
use App\Models\DRCRProcedure;
use App\Models\UserAccount;
use App\Repositories\Repository;
use Carbon\Carbon;

class DrcrMemoControlNumberRepository extends Repository implements IDrcrMemoControlNumberRepository
{
    public function __construct(DrcrMemoControlNumber $model)
    {
        parent::__construct($model);
    }

    public function findByControlNumber($controlNumber) {
        return $this->model->where('control_number', $controlNumber)->first();
    }
}
