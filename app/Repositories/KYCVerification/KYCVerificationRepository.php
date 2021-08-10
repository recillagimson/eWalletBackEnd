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
}
