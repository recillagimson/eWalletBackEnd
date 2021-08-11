<?php


namespace App\Enums;


class CurrencyRatesConfig
{
    const api = 'https://fcsapi.com/api-v3/forex/latest?symbol=';
    const source = 'https://maanimo.ph/banks/bsp';
    const accessKey = 'JdRM51gQVuoNIsx1EJHS';
    const currencies = 'EUR/PHP,USD/PHP,GBP/PHP,JPY/PHP,CHF/PHP,SEK/PHP,AUD/PHP,CAD/PHP,INR/PHP,MYR/PHP,SGD/PHP,NZD/PHP,HKD/PHP,IDR/PHP,KRW/PHP';

    //sample api full url
    //https://fcsapi.com/api-v3/forex/latest?symbol=EUR/PHP,USD/PHP,GBP/PHP,JPY/PHP,CHF/PHP,SEK/PHP,AUD/PHP,CAD/PHP,INR/PHP,MYR/PHP,SGD/PHP,NZD/PHP,HKD/PHP,IDR/PHP,KRW/PHP&access_key=JdRM51gQVuoNIsx1EJHS
}
