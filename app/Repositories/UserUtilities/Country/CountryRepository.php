<?php

namespace App\Repositories\UserUtilities\Country;

use App\Models\UserUtilities\Country;
use App\Repositories\Repository;

class CountryRepository extends Repository implements ICountryRepository
{
    public function __construct(Country $model)
    {
        parent::__construct($model);
    }

}
