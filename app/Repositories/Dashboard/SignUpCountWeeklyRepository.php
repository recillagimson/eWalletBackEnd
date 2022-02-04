<?php

namespace App\Repositories\Dashboard;

use App\Models\Dashboard\SignupCountWeeklyView;
use App\Repositories\Repository;
use Illuminate\Database\Eloquent\Model;

class SignUpCountWeeklyRepository extends Repository implements ISignUpCountWeeklyRepository
{
    public function __construct(SignupCountWeeklyView $model)
    {
        parent::__construct($model);
    }

}
