<?php

namespace App\Repositories\UserUtilities\SignupHost;

use App\Models\UserUtilities\SignupHost;
use App\Repositories\Repository;

class SignupHostRepository extends Repository implements ISignupHostRepository
{
    public function __construct(SignupHost $model)
    {
        parent::__construct($model);
    }

}
