<?php

namespace App\Repositories\UserUtilities\Currency;

use App\Models\UserUtilities\Currency;
use App\Repositories\Repository;

class CurrencyRepository extends Repository implements ICurrencyRepository
{
    public function __construct(Currency $model)
    {
        parent::__construct($model);
    }

}
