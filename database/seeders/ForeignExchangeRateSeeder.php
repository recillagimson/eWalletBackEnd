<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Enums\CurrencyRatesConfig;

///Models
use App\Models\Admin\ForeignExchangeRate;

class ForeignExchangeRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $api = CurrencyRatesConfig::api . CurrencyRatesConfig::currencies . '&access_key=' . CurrencyRatesConfig::accessKey;
        $result = Http::get($api)['response'];
        $data = collect($result)->map(function($value) {
            return [
                'name' => $value['s'],
                'from' => explode("/",$value['s'])[0],
                'rate' => $value['o']
            ];
        });
        DB::table('foreign_exchange_rates')->truncate();
        foreach($data as $rate) {
            ForeignExchangeRate::create($rate);
        }
    }
}
