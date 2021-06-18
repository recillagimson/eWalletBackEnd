<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class insert_source_of_funds extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('source_of_funds')->delete();
            DB::table('source_of_funds')->insert([

                ['id' =>"0ed7b3a8-9131-11eb-b44f-1c1b0d14e211",'description' =>"Allowance",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed7fed6-9131-11eb-b44f-1c1b0d14e211",'description' =>"Business Proceeds",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed7ffee-9131-11eb-b44f-1c1b0d14e211",'description' =>"Pension",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed801a5-9131-11eb-b44f-1c1b0d14e211",'description' =>"Farming",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed8004d-9131-11eb-b44f-1c1b0d14e211",'description' =>"Remittance",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed800a3-9131-11eb-b44f-1c1b0d14e211",'description' =>"Salary",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed800f8-9131-11eb-b44f-1c1b0d14e211",'description' =>"Self-Employed",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed8014e-9131-11eb-b44f-1c1b0d14e211",'description' =>"Commission",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ed801a1-9131-11eb-b44f-1c1b0d14e211",'description' =>"Other/s, please specify",'status' =>"1",'user_created' =>"Migration Team"],

            ]);
    }
    
}
