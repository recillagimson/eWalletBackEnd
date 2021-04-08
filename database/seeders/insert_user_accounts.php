<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class insert_user_accounts extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user_accounts')->insert([
            ['id' => '64b4b974-2277-4a25-b232-34c0ed7f87a5', 'email' => 'echodunchie@gmail.com', 'password' => '$2y$10$db8LEm8q76VGiMO1g074weMtbz4Y6sIZPZGBdDM1sUPw3LGySrxrC', 'userCreated' => 'Echo','created_at' => date('Y-m-d H:i:s')],
            ['id' => 'daf51a47-561c-489e-b6a9-284ad92ec6be', 'email' => 'echo.infante28@gmail.com', 'password' => '$2y$10$qN1Igs3WjatksLzr6R5z5OT91T49sqRWNc6r0U8.HlSJodAfoeCO.', 'userCreated' => 'Echo', 'created_at' => date('Y-m-d H:i:s')]
        ]);
    }
}
