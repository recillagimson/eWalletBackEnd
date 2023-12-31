<?php

namespace App\Repositories\Tier;

use App\Models\TierApproval;
use App\Repositories\IRepository;

interface ITierApprovalRepository extends IRepository
{
    public function updateOrCreateApprovalRequest(array $attr);
    public function list(array $attr);
    public function getPendingApprovalRequest();
    public function getPendingApprovalRequestByUserAccountId(string $id);
    public function showTierApproval(TierApproval $tierApproval);
    public function getTierApproval();
    public function getLatestRequestByUserAccountId(string $userAccountId);
}
