<?php

namespace App\Repositories\Admin\Permission;

use App\Models\Admin\Permission;
use App\Models\Admin\PermissionGroup;
use App\Models\Admin\Role;
use App\Models\Admin\RolePermission;
use App\Repositories\Repository;

class PermissionRepository extends Repository implements IPermissionRepository
{
    private $group_model;
    private $role;
    private $role_permissions;
    public function __construct(Permission $model, PermissionGroup $group_model, Role $role, RolePermission $role_permissions)
    {
        parent::__construct($model);
        $this->group_model = $group_model;
        $this->role = $role;
        $this->role_permissions = $role_permissions;
    }

    public function listPermissionsByGroup() {
        return $this->group_model->with(['permissions'])->get();
    }

    public function setRolePermissions(array $attr) {
        // GET CURRENT ROLE PERMISSIONS
        $role_permissions = $this->role_permissions->where('role_id', $attr['role_id'])->get();
        // CHECK IF EXISTS ON CURRENT UPDATE
        // IF NOT DELETE
        foreach($role_permissions as $permission) {
            if(!in_array($permission->permission_id, $attr['permission_ids'])) {
                $permission->delete();
            }
        }
        // THEN UPDATE OR CREATE ALL IN THE LIST
        foreach($attr['permission_ids'] as $permission) {
            $this->role_permissions->updateOrCreate([
                'role_id' => $attr['role_id'],
                'permission_id' => $permission
            ]);
        }

        return $this->role_permissions->where('role_id', $attr['role_id'])->get();
    }
}
