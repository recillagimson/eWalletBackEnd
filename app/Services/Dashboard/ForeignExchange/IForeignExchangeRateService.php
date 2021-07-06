<?php

namespace App\Services\Dashboard\ForeignExchange;

//Models
use App\Models\Admin\ForeignExchangeRate;

interface IForeignExchangeRateService
{
    public function updateForeignCurrencyRates(ForeignExchangeRate $foreignExchange);
    public function getForeignCurrencyRates();
}
