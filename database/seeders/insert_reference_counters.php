<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class insert_reference_counters extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('reference_counters')->delete();
        DB::table('reference_counters')->insert([
            ['id' => '1c42a959-d5fc-4a70-8c34-046373c3b5ab', 'code' => 'SM', 'counter' => 0],
            ['id' => '09503c5d-6506-4b71-ba60-68b7346e950b', 'code' => 'SB', 'counter' => 0],
            ['id' => 'ae9e00fd-53f0-4392-8616-4e08de6ce76a', 'code' => 'PB', 'counter' => 0],
            ['id' => '08d7a047-688c-4abd-9188-c8568cf899b4', 'code' => 'RM', 'counter' => 0],
            ['id' => 'c2a7e15e-ff48-48e5-a653-4a273b31960b', 'code' => 'BL', 'counter' => 0],
            ['id' => '46d6d532-1b1c-4e73-b0b7-2fee94c2dbf3', 'code' => 'AB', 'counter' => 0],
            ['id' => '76298932-f3a4-4fba-9ed9-6c401ce19056', 'code' => 'AC', 'counter' => 0],
            ['id' => '5226a15a-122b-41be-86ac-6f425add6e69', 'code' => 'AS', 'counter' => 0],
            ['id' => '2def1c62-29b7-4072-b2ba-05ce265d38b4', 'code' => 'DR', 'counter' => 0],
            ['id' => '78055a9c-1793-48b0-b5cb-37d90dda48f0', 'code' => 'CR', 'counter' => 0],
        ]);
    }
}
