<?php

namespace App\Repositories\InAddMoneyCebuana;

use App\Repositories\IRepository;

interface IInAddMoneyCebuanaRepository extends IRepository
{
    public function countRecords();
    public function getByReferenceNumber(string $referenceCode);
}
