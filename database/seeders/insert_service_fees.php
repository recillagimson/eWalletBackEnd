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
            'transaction_category_id' => '0ec43457-9131-11eb-b44f-1c1b0d14e211',
            'name' => 'ADD_MONEY_WEB_BANK_DRAGONPAY',
            'amount' => 10.000,
            'implementation_date' => '2021-04-10 09:57:29',
            'user_created' => 'd91f0d80-fdb8-4c02-a5af-1643ca772f7d',
            'user_updated' => ''
        ]);
    }
}
