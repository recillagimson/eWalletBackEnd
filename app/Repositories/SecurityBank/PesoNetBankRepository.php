<?php


namespace App\Repositories\SecurityBank;

use App\Models\SecurityBank\PesoNetBank;
use App\Repositories\Repository;

class PesoNetBankRepository extends Repository implements IPesoNetBankRepository
{
    public function __construct(PesoNetBank $model)
    {
        parent::__construct($model);
    }

    public function getListSorted($sortBy = 'bank_name', $sortDirection = 'ASC') {
        return $this->model->orderBy($sortBy, $sortDirection)->get();
    }

    public function getByBankCode($bank_code) {
        return $this->model->where('bank_bic', $bank_code)->first();
    }
}
