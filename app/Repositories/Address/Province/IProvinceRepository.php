<?php

namespace App\Repositories\Address\Province;

use App\Repositories\IRepository;

interface IProvinceRepository extends IRepository
{
    public function getProvinces(string $code);
}