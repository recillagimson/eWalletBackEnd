<?php

namespace App\Repositories\KYCVerification;

use App\Models\KYCVerification;
use App\Repositories\Repository;

class KYCVerificationRepository extends Repository implements IKYCVerificationRepository
{
    public function __construct(KYCVerification $model)
    {
        parent::__construct($model);
    }

    public function findByRequestId(string $requestId) {
        return $this->model->where('request_id', '1629382628233-2c3fcfb5-dfcc-45f4-9082-5718dae90e4f')->first();
    }
}
