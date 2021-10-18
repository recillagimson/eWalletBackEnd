<?php

namespace App\Repositories\FarmerImport;

use App\Models\FarmerImport;
use App\Models\FarmerSubsidy;
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

    public function countSequenceByProvindeAndDateCreatedSubsidy(string $province, string $dateToday) {
        // FarmerSubsidy
        return FarmerSubsidy::where('province', $province)
            ->whereDate('created_at', $dateToday)
            ->count();
    }

    public function createSubsidy(array $attr) {
        // FarmerSubsidy
        return FarmerSubsidy::create($attr);
    }
}
