<?php

namespace App\Repositories\TransactionCategory;

use App\Repositories\IRepository;

interface ITransactionCategoryRepository extends IRepository
{
    public function getByName(string $name);
}
