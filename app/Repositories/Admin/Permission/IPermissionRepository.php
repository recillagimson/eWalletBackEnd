<?php

namespace App\Repositories\Admin\Permission;

use App\Repositories\IRepository;

interface IPermissionRepository extends IRepository
{
    public function listPermissionsByGroup();
    public function setRolePermissions(array $attr);
}
