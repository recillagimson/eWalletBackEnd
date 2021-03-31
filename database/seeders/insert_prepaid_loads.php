<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class insert_prepaid_loads extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
            DB::table('prepaid_loads')->insert([
                
                ['id' =>"7b7668c5-91c8-11eb-8d33-1c1b0d14e211",'prepaid_type' =>"E-LOAD",'network' =>"GLOBE",'reward_keyword' =>"SQPLOAD1000",'amax_keyword' =>"LD",'amount' =>"1000",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"7b76983b-91c8-11eb-8d33-1c1b0d14e211",'prepaid_type' =>"E-LOAD",'network' =>"GLOBE",'reward_keyword' =>"SQPLOAD500",'amax_keyword' =>"LD",'amount' =>"500",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"7b76990d-91c8-11eb-8d33-1c1b0d14e211",'prepaid_type' =>"E-LOAD",'network' =>"GLOBE",'reward_keyword' =>"SQPLOAD300",'amax_keyword' =>"LD",'amount' =>"300",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"7b76999c-91c8-11eb-8d33-1c1b0d14e211",'prepaid_type' =>"E-LOAD",'network' =>"GLOBE",'reward_keyword' =>"SQPLOAD100",'amax_keyword' =>"LD",'amount' =>"100",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"7b769a31-91c8-11eb-8d33-1c1b0d14e211",'prepaid_type' =>"E-LOAD",'network' =>"GLOBE",'reward_keyword' =>"SQPWEBGODUOTM999",'amax_keyword' =>"WEBGODUOTM999",'amount' =>"999",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"7b769ac0-91c8-11eb-8d33-1c1b0d14e211",'prepaid_type' =>"E-LOAD",'network' =>"GLOBE",'reward_keyword' =>"SQPWEBGODUO999",'amax_keyword' =>"WEBGODUO999",'amount' =>"999",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"7b769b4d-91c8-11eb-8d33-1c1b0d14e211",'prepaid_type' =>"E-LOAD",'network' =>"GLOBE",'reward_keyword' =>"SQPHOMESURF1499",'amax_keyword' =>"HOMESURF1499",'amount' =>"1499",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"7b769bf3-91c8-11eb-8d33-1c1b0d14e211",'prepaid_type' =>"E-LOAD",'network' =>"GLOBE",'reward_keyword' =>"SQPHOMESURF999",'amax_keyword' =>"HOMESURF999",'amount' =>"999",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"7b769cb3-91c8-11eb-8d33-1c1b0d14e211",'prepaid_type' =>"E-LOAD",'network' =>"GLOBE",'reward_keyword' =>"SQPHOMESURF599",'amax_keyword' =>"HOMESURF599",'amount' =>"599",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"7b769d37-91c8-11eb-8d33-1c1b0d14e211",'prepaid_type' =>"E-LOAD",'network' =>"GLOBE",'reward_keyword' =>"SQPHOMESURF349",'amax_keyword' =>"HOMESURF349",'amount' =>"349",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"7b769dc1-91c8-11eb-8d33-1c1b0d14e211",'prepaid_type' =>"E-LOAD",'network' =>"GLOBE",'reward_keyword' =>"SQPHOMESURF199",'amax_keyword' =>"HOMESURF199",'amount' =>"199",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"7b769e4a-91c8-11eb-8d33-1c1b0d14e211",'prepaid_type' =>"E-LOAD",'network' =>"GLOBE",'reward_keyword' =>"SQPGOSAKTO140",'amax_keyword' =>"GOCOMBOEED140",'amount' =>"140",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"7b769ed1-91c8-11eb-8d33-1c1b0d14e211",'prepaid_type' =>"E-LOAD",'network' =>"GLOBE",'reward_keyword' =>"SQPGOSAKTO120",'amax_keyword' =>"GOCOMBOEED120",'amount' =>"120",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"7b769f44-91c8-11eb-8d33-1c1b0d14e211",'prepaid_type' =>"E-LOAD",'network' =>"GLOBE",'reward_keyword' =>"SQPGOSURF299",'amax_keyword' =>"GOSURF299",'amount' =>"299",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"7b769fb6-91c8-11eb-8d33-1c1b0d14e211",'prepaid_type' =>"E-LOAD",'network' =>"GLOBE",'reward_keyword' =>"SQPGOSURF999",'amax_keyword' =>"GOSURF999",'amount' =>"999",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"7b76a028-91c8-11eb-8d33-1c1b0d14e211",'prepaid_type' =>"E-LOAD",'network' =>"GLOBE",'reward_keyword' =>"GOSURF999",'amax_keyword' =>"GOSURF999",'amount' =>"999",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"7b76a0a0-91c8-11eb-8d33-1c1b0d14e211",'prepaid_type' =>"E-LOAD",'network' =>"GLOBE",'reward_keyword' =>"SQPEASYSURF299",'amax_keyword' =>"ALLSURF299",'amount' =>"299",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"7b76a11c-91c8-11eb-8d33-1c1b0d14e211",'prepaid_type' =>"E-LOAD",'network' =>"GLOBE",'reward_keyword' =>"SQPEASYSURF599",'amax_keyword' =>"ALLSURF599",'amount' =>"599",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"7b76a194-91c8-11eb-8d33-1c1b0d14e211",'prepaid_type' =>"E-LOAD",'network' =>"GLOBE",'reward_keyword' =>"SQPEASYSURF999",'amax_keyword' =>"ALLSURF999",'amount' =>"999",'status' =>"1",'user_created' =>"Migration Team"],
                ['id' =>"7b76a207-91c8-11eb-8d33-1c1b0d14e211",'prepaid_type' =>"E-LOAD",'network' =>"GLOBE",'reward_keyword' =>"SQPLOAD10",'amax_keyword' =>"LD",'amount' =>"10",'status' =>"1",'user_created' =>"Migration Team"],
                
            ]);
    }
}
