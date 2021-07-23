<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Services\Dashboard\ForeignExchange\IForeignExchangeRateService;
use Illuminate\Support\Str;


///Models
use App\Models\Admin\ForeignExchangeRate;

class ForeignExchangeRateSeeder extends Seeder
{
    private IForeignExchangeRateService $foreignExchangeRateService;

    public function __construct(IForeignExchangeRateService $foreignExchangeRateService)
    {
        $this->foreignExchangeRateService = $foreignExchangeRateService;
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $data = $this->foreignExchangeRateService->mappedSourceCode();
        DB::table('foreign_exchange_rates')->truncate();
        foreach($data as $rate) {
            $rate['id'] = (string)Str::uuid();
            ForeignExchangeRate::create($rate);
        }
    }
}
