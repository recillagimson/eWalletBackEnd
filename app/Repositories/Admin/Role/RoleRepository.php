<?php

namespace App\Repositories\Admin\Role;

use App\Models\Admin\Role;
use App\Repositories\Repository;

class RoleRepository extends Repository implements IRoleRepository
{
    public function __construct(Role $model)
    {
        parent::__construct($model);
    }

}
