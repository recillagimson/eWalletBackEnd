<?php
namespace Database\Seeders;

use Database\Seeders\Test\Auth\ClientSeeder;
use Illuminate\Database\Seeder;

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
            insert_maritial_status::class,
            insert_nationalities::class,
            insert_natures_of_work::class,
            insert_source_of_funds::class,
            insert_signup_hosts::class,
            insert_prepaid_loads::class,
            insert_transaction_categories::class,
            insert_reference_counters::class,
            ClientSeeder::class,

        ]);

    }
}
