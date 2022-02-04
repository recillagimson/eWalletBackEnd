<?php

namespace App\Repositories\Dashboard;

use App\Models\Dashboard\SignupCountMonthlyView;
use App\Repositories\Repository;
use Illuminate\Database\Eloquent\Model;

class SignUpCountMonthlyRepository extends Repository implements ISignUpCountMonthlyRepository
{
    public function __construct(SignupCountMonthlyView $model)
    {
        parent::__construct($model);
    }
}
