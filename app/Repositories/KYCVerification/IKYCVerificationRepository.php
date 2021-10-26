<?php

namespace App\Repositories\KYCVerification;

use App\Repositories\IRepository;

interface IKYCVerificationRepository extends IRepository
{
    public function findByRequestId(string $requestId);
}
