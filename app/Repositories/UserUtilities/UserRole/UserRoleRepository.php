<?php

namespace App\Repositories\UserUtilities\UserRole;

use App\Models\UserRole;
use App\Repositories\Repository;

class UserRoleRepository extends Repository implements IUserRoleRepository
{
    public function __construct(UserRole $model)
    {
        parent::__construct($model);
    }

    public function setUserRoles(array $attr)
    {
        // REMOVE CURRENT ROLES
        $current = $this->model->whereNotIn('role_id', $attr['role_ids'])
            ->where('user_account_id', $attr['user_account_id'])
            ->get();

        foreach ($current as $curr) {
            $curr->delete();
        }

        $records = [];
        foreach ($attr['role_ids'] as $role_id) {
            $record = $this->model->updateOrCreate([
                'user_account_id' => $attr['user_account_id'],
                'role_id' => $role_id,
            ]);
            array_push($records, $record);
        }

        return $records;
    }

    public function getUserRolesAndPermissionByUserAccountId(string $userAccountId)
    {
        $roles = $this->model
            ->with(['role', 'role.permissions'])
            ->where('user_account_id', $userAccountId)
            ->get();

        return $roles;
    }

    public function hasUserRole($userAccountId, $roleId)
    {
        $userRole = $this->model
            ->where('user_account_id', $userAccountId)
            ->where('role_id', $roleId)->first();

        return $userRole ? true : false;
    }
}
