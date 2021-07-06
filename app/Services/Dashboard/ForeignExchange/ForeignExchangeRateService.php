<?php

namespace App\Services\Dashboard\ForeignExchange;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use DB;
use App\Enums\CurrencyRatesConfig;

//Models
use App\Models\Admin\ForeignExchangeRate;
use App\Models\UserUtilities\Currency;

//Repository
use App\Repositories\Repository;

class ForeignExchangeRateService extends Repository implements IForeignExchangeRateService
{
    public $currency;

    public function __construct(ForeignExchangeRate $model, Currency $currency)
    {
        $this->currency = $currency;
        parent::__construct($model);
    }

    ///Update Foreign Currency Rates via Scheduler
    public function updateForeignCurrencyRates()
    {
        DB::transaction(function() {
            $rates = $this->mappedSourceCode()->toArray();
            DB::table('foreign_exchange_rates')->truncate();
            foreach($rates as $rate) {
                $this->model->create($rate);
            }

        });
    }

    public function getForeignCurrencyRates()
    {
        $data = $this->model->all();
        if($data->isEmpty()) {
            throw ValidationException::withMessages([
                'foreign_exchange_rate_details' => "Foreign exchange rate details can't be found."
            ]);
        }
        return $data;
    }

    private function mappedInBetweenStrings($data, $from, $to)
    {
        $result = substr($data, strpos($data,$from)+strlen($from),strlen($data));
        return substr($result,0,strpos($result,$to));
    }

    public function mappedSourceCode()
    {
        $data = Http::get(CurrencyRatesConfig::source);
        $from = '<div class="widget exchange-rates">';
        $to = '</div>';
        $exploded = explode('</i>', $this->mappedInBetweenStrings($data, $from, $to));
        unset($exploded[0]);
        return collect($exploded)->map(function($value) {
            return array(
               'code' => substr($value, 0, 3),
               'name' => $this->getCurrencyDescription(substr($value, 0, 3)),
               'rate' => floatval(explode('<td>',$value)[1])
           );
       });
    }

    private function getCurrencyDescription($code)
    {
        $code = $code == 'VEB' ? 'VEF' : $code;
        $currency = $this->currency->whereCode($code)->first();
        return $currency ? $currency->description : '';
    }
}
