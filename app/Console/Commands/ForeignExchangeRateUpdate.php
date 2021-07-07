<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Admin\ForeignExchangeRate;
use App\Services\Dashboard\ForeignExchange\ForeignExchangeRateService;

class ForeignExchangeRateUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'every_four_hours:foreign_exchange_rates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Foreign Exchange rates update database every four hours';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    public ForeignExchangeRateService $foreignExchangeRateService;
    public ForeignExchangeRate $foreignExchangeRate;

    public function __construct(ForeignExchangeRateService $foreignExchangeRateService,ForeignExchangeRate $foreignExchangeRate)
    {
        $this->foreignExchangeRateService = $foreignExchangeRateService;
        $this->foreignExchangeRate = $foreignExchangeRate;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->foreignExchangeRateService->updateForeignCurrencyRates($this->foreignExchangeRate);
    }
}
