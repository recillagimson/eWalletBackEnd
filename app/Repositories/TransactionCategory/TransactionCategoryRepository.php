<?php

namespace App\Repositories\TransactionCategory;

use App\Repositories\Repository;
use App\Models\TransactionCategory;

class TransactionCategoryRepository extends Repository implements ITransactionCategoryRepository
{
    public function __construct(TransactionCategory $model)
    {
        parent::__construct($model);
    }

    public function getByName(string $name)
    {
        return $this->model->where('name', $name)->first();
    }
}
