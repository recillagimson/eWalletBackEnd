<?php

namespace App\Repositories\UserAccount;

use App\Models\UserAccount;
use App\Repositories\IRepository;

interface IUserAccountRepository extends IRepository
{
    public function getByUsername(string $usernameField, string $email);
}
