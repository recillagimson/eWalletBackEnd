<?php

namespace App\Repositories\Tier;

use App\Models\TierApprovalComment;
use App\Repositories\Repository;

class TierApprovalCommentRepository extends Repository implements ITierApprovalCommentRepository
{
    public function __construct(TierApprovalComment $model)
    {
        parent::__construct($model);
    }

    public function listTierApprovalComments(array $attr) 
    {
        $records = $this->model;
        
        if($records && isset($records['tier_request_id'])) {
            $records = $records->where('tier_request_id', $attr['tier_request_id']);
        }

        return $records->get();
    }
}
