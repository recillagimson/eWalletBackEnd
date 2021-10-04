<?php

namespace App\Repositories\FarmerImport;

use App\Models\FarmerImport;
use App\Repositories\Repository;

class FarmerImportRepository extends Repository implements IFarmerImportRepository
{
    public function __construct(FarmerImport $model)
    {
        parent::__construct($model);
    }

    public function countSequnceByProvinceAndDateCreated(string $province, string $dateToday) {
        return $this->model->where('province', $province)
            ->whereDate('created_at', $dateToday)
            ->count();
    }
}
