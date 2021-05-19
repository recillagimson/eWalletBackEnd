<?php

namespace Database\Seeders;

use App\Models\Admin\Role;
use App\Models\UserAccount;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            'Admin', 'KYC', 'Compliance', 'Client'
        ];

        foreach($roles as $role) {
            Role::create([
                'name' => $role,
                'description' => $role,
                'slug' => Str::slug($role, '-'),
                'user_created' => UserAccount::all()->first()->id,
                'user_updated' => UserAccount::all()->first()->id
            ]);
        }
    }
}
