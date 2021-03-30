<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        DB::table('transaction_categories')->insert(
            
        [
        'id' =>'0ec41025-9131-11eb-b44f-1c1b0d14e211',
        'old_transaction_category_id' => null,
        'name' => 'WITHDRAWUBPINSTAPAY',
        'description' => 'Withdraw Cash via UBP Instapay',
        'user_created' => 'Migration Team'
        ]
    
    );
    }
}

