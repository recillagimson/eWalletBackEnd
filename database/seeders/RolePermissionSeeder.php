<?php

namespace Database\Seeders;

use App\Models\Admin\Role;
use Illuminate\Database\Seeder;
use App\Models\Admin\Permission;
use App\Models\Admin\RolePermission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            'admin', 'client'
        ];

        $permissions = Permission::all();

        foreach($roles as $role) {
            $role = Role::where('slug', $role)->first();
            foreach($permissions as $permission) {
                RolePermission::create([
                    'role_id' => $role->id,
                    'permission_id' => $permission->id
                ]);
            }
        }
    }
}
