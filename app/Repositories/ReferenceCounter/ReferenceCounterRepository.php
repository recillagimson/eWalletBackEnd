<?php


namespace App\Repositories\ReferenceCounter;


use App\Models\ReferenceCounter;
use App\Repositories\Repository;

class ReferenceCounterRepository extends Repository implements IReferenceCounterRepository
{
    public function __construct(ReferenceCounter $model)
    {
        parent::__construct($model);
    }

    public function getByCode(string $code)
    {
        return $this->model->where('code', '=', $code)
            ->lockForUpdate()->first();
    }
}
