<?php

namespace App\Repositories\Tier;

use App\Models\Tier;
use App\Models\UserAccount;
use App\Repositories\Repository;
use Illuminate\Validation\ValidationException;

class TierApprovalRepository extends Repository implements ITierApprovalRepository
{
    public function __construct(Tier $model)
    {
        parent::__construct($model);
    }
}
