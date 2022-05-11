<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class insert_pm_reference_counters extends Seeder
{
    /**
     * Run the database seeds.php artisan migrate --path=/database/migrations/migrations_file_name
     *
     * @return void
     */
    public function run()
    {
        DB::table('reference_counters')->insert([
            ['id' => 'fa314838-cba8-11ec-9d64-0242ac120002', 'code' => 'PM', 'counter' => 0] 
        ]);
    }
}
