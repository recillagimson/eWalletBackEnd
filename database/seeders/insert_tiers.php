<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class insert_tiers extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('tiers')->delete();
        DB::table('tiers')->insert([


            ['id' => '5e007ec8-a176-11eb-b447-1c1b0d14e211', 'name' => 'Tier 2', 'tier_class' => 'Silver', 'account_status' => 'FULLY VERIFIED', 'daily_limit' => '500000', 'daily_threshold' => '500000', 'monthly_limit' => '50000', 'monthly_threshold' => '50000', 'status' => '1', 'user_created' => 'Migration Team'], 
            ['id' => '60d40d2f-a176-11eb-b447-1c1b0d14e211', 'name' => 'Tier 3', 'tier_class' => 'Gold', 'account_status' => 'ADVANCED', 'daily_limit' => '100000', 'daily_threshold' => '100000', 'monthly_limit' => '100000', 'monthly_threshold' => '100000', 'status' => '1', 'user_created' => 'Migration Team'], 
            ['id' => '63baa95c-a176-11eb-b447-1c1b0d14e211', 'name' => 'Tier 4', 'tier_class' => 'Platinum', 'account_status' => 'PROFESSIONAL', 'daily_limit' => '300000', 'daily_threshold' => '300000', 'monthly_limit' => '300000', 'monthly_threshold' => '300000', 'status' => '1', 'user_created' => 'Migration Team'], 
            ['id' => '68d63df8-a176-11eb-b447-1c1b0d14e211', 'name' => 'Tier 5', 'tier_class' => 'Diamond', 'account_status' => 'EXPERT', 'daily_limit' => '1000000', 'daily_threshold' => '1000000', 'monthly_limit' => '1000000', 'monthly_threshold' => '1000000', 'status' => '1', 'user_created' => 'Migration Team'], 
            ['id' => 'c5d5cb3e-a175-11eb-b447-1c1b0d14e211', 'name' => 'Tier 1', 'tier_class' => 'Bronze', 'account_status' => 'SEMI VERIFIED', 'daily_limit' => '10000', 'daily_threshold' => '10000', 'monthly_limit' => '10000', 'monthly_threshold' => '10000', 'status' => '1', 'user_created' => 'Migration Team'], 
            
        ]);
    }


}
