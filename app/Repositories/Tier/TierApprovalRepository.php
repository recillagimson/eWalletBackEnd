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

    public function getPendingApprovalRequest() {
        return $this->model->where('user_account_id', request()->user()->id)
            ->where('status', 'PENDING')
            ->first();
    }

    public function updateOrCreateApprovalRequest(array $attr) {
        $record = $this->model
            ->where('user_account_id', $attr['user_account_id'])
            ->orderBy('created_at', 'DESC')
            ->first();

        if($record) {
            return $record;
        }

        return $this->model->create($attr);
    }

    public function list(array $attr) {
        $records = $this->model->with(['user_account', 'user_detail']);

        if(isset($attr['status'])) {
            $records = $records->where('status', $attr['status']);
        }

        return $records->paginate();
    }
}
