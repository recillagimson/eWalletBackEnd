<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class transaction_categories extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        transaction_categories::table('users')->insert([
            'name' => str_random(8),
            'email' => str_random(12).'@mail.com',
            'password' => bcrypt('123456')
        ]);
    }
}
