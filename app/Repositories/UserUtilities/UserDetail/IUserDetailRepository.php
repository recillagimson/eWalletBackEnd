<?php

namespace App\Repositories\UserUtilities\UserDetail;

use App\Models\UserAccount;
use App\Repositories\IRepository;

interface IUserDetailRepository extends IRepository
{
    public function getByUserId(string $userId);
    public function getUserInfo(string $userAccountID);
}
