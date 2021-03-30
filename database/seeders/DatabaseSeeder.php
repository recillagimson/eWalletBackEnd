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
            ClientSeeder::class,
            insert_transaction_categories::class,
        ]);
    }
}
