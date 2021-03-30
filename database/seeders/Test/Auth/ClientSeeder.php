<?php

namespace Database\Seeders\Test\Auth;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('clients')->insert([
           'client_id' => 'spa-client',
           'client_secret' => Hash::make('secret')
        ]);
    }
}
