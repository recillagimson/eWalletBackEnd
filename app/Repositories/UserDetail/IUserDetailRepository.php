<?php

namespace App\Repositories\UserDetail;

use App\Repositories\IRepository;

interface IUserDetailRepository extends IRepository
{
    public function getByUserAccountId(string $userAccountId);
}
