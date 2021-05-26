<?php

namespace App\Repositories\Tier;

use App\Models\TierRequirement;
use App\Models\Tier;
use App\Repositories\Repository;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Builder;

class TierRequirementRepository extends Repository implements ITierRequirementRepository
{
    public function __construct(TierRequirement $model)
    {
        parent::__construct($model);
    }

    public function getTierRequirements()
    {
        $tierRequirement = Tier::with('TierRequirement')->get();
        return $tierRequirement;
    }
}
