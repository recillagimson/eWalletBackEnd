<?php

namespace App\Repositories\ReferenceCounter;

use App\Repositories\IRepository;

interface IReferenceCounterRepository extends IRepository
{
    public function getByCode(string $code);
}
