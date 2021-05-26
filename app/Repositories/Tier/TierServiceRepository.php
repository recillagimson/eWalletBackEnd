<?php

namespace App\Repositories\Tier;

use App\Models\TierService;
use App\Models\Tier;
use App\Models\TransactionCategory;
use App\Repositories\Repository;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Builder;

class TierServiceRepository extends Repository implements ITierServiceRepository
{
    public function __construct(TierService $model)
    {
        parent::__construct($model);
    }

    public function getTierDetails()
    {
        $tierDetais = Tier::with('TransactionCategory')->get();
        return $tierDetais;
    }
}
