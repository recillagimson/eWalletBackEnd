<?php

namespace App\Repositories\Address\Barangay;

use App\Repositories\IRepository;

interface IBarangayRepository extends IRepository
{
    public function getBarangays(string $code);
}
