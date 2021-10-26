<?php

namespace App\Repositories\MerchantAccount;

use App\Models\MerchantAccount;
use App\Repositories\Repository;

class MerchantAccountRepository extends Repository implements IMerchantAccountRepository
{
    public function __construct(MerchantAccount $model)
    {
        parent::__construct($model);
    }
}
