<?php

namespace App\Repositories\UserUtilities\UserDetail;

use App\Repositories\IRepository;

interface IUserDetailRepository extends IRepository
{
    public function getByUserId(string $userId);
}
