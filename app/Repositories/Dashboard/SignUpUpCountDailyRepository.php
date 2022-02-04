<?php

namespace App\Repositories\Dashboard;

use App\Models\Dashboard\SignupCountDailyView;
use App\Repositories\Repository;
use Illuminate\Database\Eloquent\Model;

class SignUpUpCountDailyRepository extends Repository implements ISignUpCountDailyRepository
{
    public function __construct(SignupCountDailyView $model)
    {
        parent::__construct($model);
    }
}
