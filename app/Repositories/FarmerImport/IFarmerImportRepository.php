<?php

namespace App\Repositories\FarmerImport;

use App\Repositories\IRepository;

interface IFarmerImportRepository extends IRepository
{
    public function countSequnceByProvinceAndDateCreated(string $province, string $dateToday);
    public function countSequenceByProvindeAndDateCreatedSubsidy(string $province, string $dateToday);
    public function createSubsidy(array $attr);
}
