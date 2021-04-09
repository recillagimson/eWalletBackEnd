<?php

namespace App\Repositories\TransactionCategory;

use App\Models\TransactionCategory;
use App\Repositories\Repository;

class TransactionCategoryRepository extends Repository implements ITransactionCategoryRepository
{
    public function __construct(TransactionCategory $model)
    {
        parent::__construct($model);
    }

    public function getByName(string $name)
    {
        return $this->model->where('name', '=', $name)->first();
    }
}
