<?php

namespace App\Repositories\UserUtilities\Country;

use App\Repositories\IRepository;

interface ICountryRepository extends IRepository
{
    public function getAllNaturesofWork();  
}
