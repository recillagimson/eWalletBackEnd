<?php

namespace App\Repositories\UserUtilities\UserRole;

use App\Models\UserAccount;
use App\Repositories\IRepository;

interface IUserRoleRepository extends IRepository
{
    public function setUserRoles(array $attr);
    public function getUserRolesAndPermissionByUserAccountId(string $userAccountId);
}
