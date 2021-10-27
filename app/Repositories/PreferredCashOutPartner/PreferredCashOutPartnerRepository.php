<?php

namespace App\Repositories\PreferredCashOutPartner;

use App\Enums\IdTypes;
use App\Models\IdType;
use App\Models\PreferredCashOutPartner;
use App\Repositories\Repository;
use App\Repositories\IdType\IIdTypeRepository;

class PreferredCashOutPartnerRepository extends Repository implements IPreferredCashOutPartnerRepository
{
    public function __construct(PreferredCashOutPartner $model)
    {
        parent::__construct($model);
    }
}
