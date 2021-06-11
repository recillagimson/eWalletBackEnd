<?php

namespace Database\Seeders;

use App\Models\Admin\Role;
use App\Models\UserAccount;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $roles = [
        //     'Admin', 'KYC', 'Compliance', 'Client'
        // ];

        // foreach($roles as $role) {
        //     Role::create([
        //         'name' => $role,
        //         'description' => $role,
        //         'slug' => Str::slug($role, '-'),
        //         'user_created' => UserAccount::all()->first()->id,
        //         'user_updated' => UserAccount::all()->first()->id
        //     ]);
        // }

        DB::table('roles')->delete();
        DB::table('roles')->insert([
            ['id' =>"26b07613-3901-4daa-ac20-9a46718c2ae7",'description' =>"Client", 'name' => 'Client', 'slug' => 'client', 'user_created' => '00fd75dd-d4da-4a75-85bc-24f11983ba8d', 'user_updated' => '00fd75dd-d4da-4a75-85bc-24f11983ba8d'],
            ['id' =>"6ea33667-1326-4a20-9df6-abe6c9a00df5",'description' =>"Admin", 'name' => 'Admin', 'slug' => 'admin', 'user_created' => '00fd75dd-d4da-4a75-85bc-24f11983ba8d', 'user_updated' => '00fd75dd-d4da-4a75-85bc-24f11983ba8d'],
            ['id' =>"ac9a207b-ff81-4041-b73a-c41845ef2c95",'description' =>"KYC", 'name' => 'KYC', 'slug' => 'kyc', 'user_created' => '00fd75dd-d4da-4a75-85bc-24f11983ba8d', 'user_updated' => '00fd75dd-d4da-4a75-85bc-24f11983ba8d'],
            ['id' =>"adf550ab-136e-43ba-8412-0cdc658d3aee",'description' =>"Compliance", 'name' => 'Compliance', 'slug' => 'compliance', 'user_created' => '00fd75dd-d4da-4a75-85bc-24f11983ba8d', 'user_updated' => '00fd75dd-d4da-4a75-85bc-24f11983ba8d'],
        ]);
    }
}
