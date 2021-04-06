<?php

namespace App\Repositories\UserDetail;

use App\Models\UserAccount;
use App\Repositories\IRepository;

interface IUserDetailRepository extends IRepository
{
    public function getByUserAccountID(string $userAccountID);
}
