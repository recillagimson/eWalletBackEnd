<?php

namespace App\Repositories\UserAccount;

use App\Repositories\IRepository;
use Illuminate\Database\Eloquent\Collection;

interface IUserAccountRepository extends IRepository
{
    public function getAdminUsers(): Collection;

    public function getAdminUser(string $id);

    public function getAdminUsersByEmail(string $email): Collection;

    public function getAdminUsersByName(string $lastName, string $firstName): Collection;

    public function getUser(string $id);

    public function getByUsername(string $usernameField, string $username);

    public function getUserInfo(string $userAccountID);

    public function getByEmail(string $emailField, string $email);
}
