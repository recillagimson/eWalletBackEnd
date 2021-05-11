<?php

namespace App\Repositories\Tier;

use App\Repositories\IRepository;

interface ITierApprovalRepository extends IRepository
{
    public function updateOrCreateApprovalRequest(array $attr) ;
}
