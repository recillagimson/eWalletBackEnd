<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\PrepaidLoad\IPrepaidLoadService;

class PrepaidGlobePromoSeeder extends Seeder
{

    private IPrepaidLoadService $prepaidLoadService;

    public function __construct(IPrepaidLoadService $prepaidLoadService)
    {
        $this->prepaidLoadService = $prepaidLoadService;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $promos = [
            [
                'old_prepaid_load_id'=> "",
                'prepaid_type'=> "",
                'reward_keyword'=> "SQPLOAD1000",
                'amax_keyword'=> "LD",
                'amount'=> 1000,
                'status'=> 0
            ],
            [
                'old_prepaid_load_id'=> "",
                'prepaid_type'=> "",
                'reward_keyword'=> "SQPLOAD500",
                'amax_keyword'=> "LD",
                'amount'=> 500,
                'status'=> 0
            ],
            [
                'old_prepaid_load_id'=> "",
                'prepaid_type'=> "",
                'reward_keyword'=> "SQPLOAD300",
                'amax_keyword'=> "LD",
                'amount'=> 300,
                'status'=> 0
            ],
            [
                'old_prepaid_load_id'=> "",
                'prepaid_type'=> "",
                'reward_keyword'=> "SQPLOAD100",
                'amax_keyword'=> "LD",
                'amount'=> 100,
                'status'=> 0
            ],
            [
                'old_prepaid_load_id'=> "",
                'prepaid_type'=> "",
                'reward_keyword'=> "SQPWEBGODUOTM999",
                'amax_keyword'=> "WEBGODUOTM999",
                'amount'=> 999,
                'status'=> 0
            ],
            [
                'old_prepaid_load_id'=> "",
                'prepaid_type'=> "",
                'reward_keyword'=> "SQPWEBGODUO999",
                'amax_keyword'=> "WEBGODUO999",
                'amount'=> 999,
                'status'=> 0
            ],
            [
                'old_prepaid_load_id'=> "",
                'prepaid_type'=> "",
                'reward_keyword'=> "SQPHOMESURF1499",
                'amax_keyword'=> "HOMESURF1499",
                'amount'=> 1499,
                'status'=> 0
            ],
            [
                'old_prepaid_load_id'=> "",
                'prepaid_type'=> "",
                'reward_keyword'=> "SQPHOMESURF999",
                'amax_keyword'=> "HOMESURF999",
                'amount'=> 999,
                'status'=> 0
            ],
            [
                'old_prepaid_load_id'=> "",
                'prepaid_type'=> "",
                'reward_keyword'=> "SQPHOMESURF599",
                'amax_keyword'=> "HOMESURF599",
                'amount'=> 599,
                'status'=> 0
            ],
            [
                'old_prepaid_load_id'=> "",
                'prepaid_type'=> "",
                'reward_keyword'=> "SQPHOMESURF349",
                'amax_keyword'=> "HOMESURF349",
                'amount'=> 349,
                'status'=> 0
            ],
            [
                'old_prepaid_load_id'=> "",
                'prepaid_type'=> "",
                'reward_keyword'=> "SQPHOMESURF199",
                'amax_keyword'=> "HOMESURF199",
                'amount'=> 199,
                'status'=> 0
            ],
            [
                'old_prepaid_load_id'=> "",
                'prepaid_type'=> "",
                'reward_keyword'=> "SQPGOSAKTO140",
                'amax_keyword'=> "GOCOMBOEED140",
                'amount'=> 140,
                'status'=> 0
            ],
            [
                'old_prepaid_load_id'=> "",
                'prepaid_type'=> "",
                'reward_keyword'=> "SQPGOSAKTO120",
                'amax_keyword'=> "GOCOMBOEED120",
                'amount'=> 120,
                'status'=> 0
            ],
            [
                'old_prepaid_load_id'=> "",
                'prepaid_type'=> "",
                'reward_keyword'=> "SQPGOSURF299",
                'amax_keyword'=> "GOSURF299",
                'amount'=> 299,
                'status'=> 0
            ],
            [
                'old_prepaid_load_id'=> "",
                'prepaid_type'=> "",
                'reward_keyword'=> "SQPGOSURF999",
                'amax_keyword'=> "GOSURF999",
                'amount'=> 999,
                'status'=> 0
            ],
            [
                'old_prepaid_load_id'=> "",
                'prepaid_type'=> "",
                'reward_keyword'=> "GOSURF999",
                'amax_keyword'=> "GOSURF999",
                'amount'=> 999,
                'status'=> 0
            ],
            [
                'old_prepaid_load_id'=> "",
                'prepaid_type'=> "",
                'reward_keyword'=> "SQPEASYSURF299",
                'amax_keyword'=> "ALLSURF299",
                'amount'=> 299,
                'status'=> 0
            ],
            [
                'old_prepaid_load_id'=> "",
                'prepaid_type'=> "",
                'reward_keyword'=> "SQPEASYSURF599",
                'amax_keyword'=> "ALLSURF599",
                'amount'=> 599,
                'status'=> 0
            ],
            [
                'old_prepaid_load_id'=> "",
                'prepaid_type'=> "",
                'reward_keyword'=> "SQPEASYSURF999",
                'amax_keyword'=> "ALLSURF999",
                'amount'=> 999,
                'status'=> 0
            ],
            [
                'old_prepaid_load_id'=> "",
                'prepaid_type'=> "",
                'reward_keyword'=> "SQPLOAD10",
                'amax_keyword'=> "LD",
                'amount'=> 10,
                'status'=> 0
            ],
        ];

        foreach($promos as $promo) {
            $this->prepaidLoadService->prepaidLoads->create($promo);
        }
    }
}
