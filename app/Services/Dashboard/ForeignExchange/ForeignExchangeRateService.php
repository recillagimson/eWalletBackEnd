<?php

namespace App\Services\Dashboard\ForeignExchange;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use DB;
use App\Enums\CurrencyRatesConfig;

//Models
use App\Models\Admin\ForeignExchangeRate;

//Repository
use App\Repositories\Repository;

class ForeignExchangeRateService extends Repository implements IForeignExchangeRateService
{
    public function __construct(ForeignExchangeRate $model)
    {
        parent::__construct($model);
    }

    ///Update Foreign Currency Rates via Scheduler
    public function updateForeignCurrencyRates()
    {

        $api = CurrencyRatesConfig::api . CurrencyRatesConfig::currencies . '&access_key=' . CurrencyRatesConfig::accessKey;
        $result = Http::get($api);

        if(!$result['status']) {
            throw ValidationException::withMessages([
                'foreign_exchange_rate_details' => "Foreign exchange rate details can't be found."
            ]);
        }

        DB::transaction(function() use ($result) {
            $rates = collect($result['response'])->map(function($value) {
                return [
                    'name' => $value['s'],
                    'from' => explode("/",$value['s'])[0],
                    'rate' => $value['o']
                ];
            });
            DB::table('foreign_exchange_rates')->truncate();
            foreach($rates as $rate) {
                $this->model->create($rate);
            }

        });
    }

    // Get foreign exchange rates
    public function getForeignCurrencyRates()
    {
        return $this->model->all();
    }
}
