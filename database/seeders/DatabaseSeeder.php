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
        $this->call([
            insert_billers::class,
            insert_countries::class,
            insert_currencies::class,
            insert_id_types::class,
            insert_maritial_Status::class,
            insert_nationalities::class,
            insert_natures_of_work::class,
            insert_transaction_categories::class,
            insert_source_of_funds::class,
            insert_signup_hosts::class,
        ]); 
    }
}
