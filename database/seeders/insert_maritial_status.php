<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class insert_maritial_status extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
            DB::table('maritial_status')->insert([

                ['id' =>"0ec5afee-9131-11eb-b44f-1c1b0d14e211",'description' =>"Single",'legend' =>"S",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ec6e896-9131-11eb-b44f-1c1b0d14e211",'description' =>"Married",'legend' =>"M",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ec825ce-9131-11eb-b44f-1c1b0d14e211",'description' =>"Widowed",'legend' =>"W",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ec96f44-9131-11eb-b44f-1c1b0d14e211",'description' =>"Divorced",'legend' =>"D",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"0ecaabc9-9131-11eb-b44f-1c1b0d14e211",'description' =>"Separated",'legend' =>"SEP",'status' =>"1",'user_created' =>"Migration Team"]
                
            ]);
    }
}
