<?php

namespace App\Repositories\Admin\Role;

use App\Repositories\IRepository;

interface IRoleRepository extends IRepository
{
    public function getRoleWithPermissions(string $id);
}
