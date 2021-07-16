<?php

namespace App\Repositories\Address\Barangay;

use App\Models\Barangay;
use App\Repositories\Repository;

class BarangayRepository extends Repository implements IBarangayRepository
{
    public function __construct(Barangay $model)
    {
        parent::__construct($model);
    }

    public function getBarangays(string $code)
    {
        return $this->model->where('municipality_code', '=', $code)->get();
    }
}
