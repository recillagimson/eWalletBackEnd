<?php
namespace App\Services\Tier;

interface ITierApprovalService {
    public function updateOrCreateApprovalRequest(array $attr);
}
