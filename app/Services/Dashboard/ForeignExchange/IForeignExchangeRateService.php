<?php

namespace App\Services\Dashboard\ForeignExchange;

interface IForeignExchangeRateService
{
    public function updateForeignCurrencyRates();
    public function getForeignCurrencyRates();
}
