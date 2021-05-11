<?php

namespace App\Repositories\Tier;

use App\Models\TierApproval;
use App\Repositories\Repository;

class TierApprovalRepository extends Repository implements ITierApprovalRepository
{
    public function __construct(TierApproval $model)
    {
        parent::__construct($model);
    }

    public function updateOrCreateApprovalRequest(array $attr) {
        $record = $this->model
            ->where('user_account_id', $attr['user_account_id'])
            ->where('status', $attr['status'])
            ->first();

        if($record) {
            return $record;
        }

        return $this->model->create($attr);
    }
}
