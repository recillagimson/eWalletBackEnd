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

    public function getRoleWithPermissions(string $id) {
        return $this->model->with(['permissions'])->where('id', $id)->first();
    }

}
