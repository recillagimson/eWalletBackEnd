<?php

namespace App\Services\Dashboard\ForeignExchange;
use App\Enums\CurrencyRatesConfig;
use App\Models\Admin\ForeignExchangeRate;
use App\Models\UserUtilities\Currency;
use App\Repositories\Repository;
use DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

//Models

//Repository

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
        DB::table('foreign_exchange_rates')->truncate();
        DB::transaction(function() {
            $rates = $this->mappedSourceCode()->toArray();
            foreach($rates as $rate) {
                $rate['id'] = (string)Str::uuid();
                $this->model->create($rate);
            }
        });
    }

    public function getForeignCurrencyRates()
    {
        $data = $this->model->all();
        if ($data->isEmpty()) {
            throw ValidationException::withMessages([
                'foreign_exchange_rate_details' => "Foreign exchange rate details can't be found."
            ]);
        }
        return collect(CurrencyRatesConfig::currenciesArrangement)->map(function ($e) use ($data) {
            foreach ($data as $value) {
                if ($value['code'] == $e) {
                    return $value;
                }
            }
        });
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
        $peso = array(
            'code' => 'PHP',
            'name' => 'Philippine Peso',
            'rate' => floatval($this->mappedInBetweenStrings($data, '"exchCalcMedium()">', '</span>'))
        );
        unset($exploded[0]);
        return collect($exploded)->map(function ($value) {
            return array(
                'code' => substr($value, 0, 3),
                'name' => $this->getCurrencyDescription(substr($value, 0, 3)),
                'rate' => floatval(explode('<td>', $value)[1])
            );
        })->prepend($peso);
    }

    private function getCurrencyDescription($code)
    {
        $code = $code == 'VEB' ? 'VEF' : $code;
        $currency = $this->currency->whereCode($code)->first();
        return $currency ? $currency->description : '';
    }
}
