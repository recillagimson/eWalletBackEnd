<?php

namespace App\Repositories\Loan;

use App\Enums\IdTypes;
use App\Models\IdType;
use App\Models\Loan;
use App\Repositories\Repository;
use App\Repositories\IdType\IIdTypeRepository;

class LoanRepository extends Repository implements ILoanRepository
{
    public function __construct(Loan $model)
    {
        parent::__construct($model);
    }
}
