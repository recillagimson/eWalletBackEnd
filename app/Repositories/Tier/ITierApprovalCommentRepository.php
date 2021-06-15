<?php

namespace App\Repositories\Tier;

use App\Repositories\IRepository;

interface ITierApprovalCommentRepository extends IRepository
{
    public function listTierApprovalComments(array $attr);
}
