<?php
namespace App\Services\Tier;

use App\Models\TierApproval;

interface ITierApprovalService {
    public function updateOrCreateApprovalRequest(array $attr);
    public function updateStatus(array $attr, TierApproval $tierApproval);
}