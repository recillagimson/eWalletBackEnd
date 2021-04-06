<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class insert_service_fees extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('service_fees')->delete();
        DB::table('service_fees')->insert([
            'id' => '6f8b72d8-cca7-49e2-8e05-bd455f86dd2e',
            'old_service_fee_id' => '',
            'tier' => 1,
            'transaction_category_id' => '289a7112-c79b-47e0-ab38-a9d23dbce418',
            'name' => 'Add money DragonPay service see',
            'amount' => 10.000,
            'implementation_date' => '2021-05-01 00:00:00',
            'user_created' => 'd91f0d80-fdb8-4c02-a5af-1643ca772f7d',
            'user_updated' => ''
        ]);
    }
}
