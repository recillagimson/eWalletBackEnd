<?php

namespace App\Repositories\UserAccount;

use App\Repositories\IRepository;

interface IUserAccountRepository extends IRepository
{
    public function getUser(string $id);

    public function getByUsername(string $usernameField, string $username);
}
