<?php

namespace App\Providers;

use App\Services\Auth\AuthService;
use App\Services\Auth\IAuthService;
use App\Services\PrepaidLoad\IPrepaidLoadService;
use App\Services\PrepaidLoad\PrepaidLoadService;
use App\Services\Encryption\EncryptionService;
use App\Services\Encryption\IEncryptionService;
use App\Services\OutBuyLoad\OutBuyLoadService;
use App\Services\OutBuyLoad\IOutBuyLoadService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(IEncryptionService::class, EncryptionService::class);
        $this->app->bind(IAuthService::class, AuthService::class);
        $this->app->bind(IPrepaidLoadService::class, PrepaidLoadService::class);
        $this->app->bind(IOutBuyLoadService::class, OutBuyLoadService::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
