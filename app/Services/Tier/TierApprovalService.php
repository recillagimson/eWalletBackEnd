<?php

namespace App\Services\Tier;

use App\Repositories\Tier\ITierApprovalRepository;

class TierApprovalService implements ITierApprovalService
{   
    public ITierApprovalRepository $tierApprovalRepository;

    public function __construct(ITierApprovalRepository $tierApprovalRepository)
    {
        $this->tierApprovalRepository = $tierApprovalRepository;
    }

    public function updateOrCreateApprovalRequest(array $attr) {
        return $this->tierApprovalRepository->updateOrCreateApprovalRequest($attr);
    }
}
