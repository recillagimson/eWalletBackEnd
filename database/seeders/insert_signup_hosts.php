<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class insert_signup_hosts extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
  
            DB::table('signup_hosts')->insert([

                ['id' =>"38e986ee-91b8-11eb-8d33-1c1b0d14e211",'description' =>"ACCOUNT.SQUID.PH",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"38e9a8f5-91b8-11eb-8d33-1c1b0d14e211",'description' =>"SQUIDPAY.PH",'status' =>"1",'user_created' =>"Migration Team"],
                
            ]);
    }
    
}
