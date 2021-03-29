<?php

namespace App\Providers;

use App\Repositories\Client\ClientRepository;
use App\Repositories\Client\IClientRepository;
use App\Repositories\OtpRepository\IOtpRepository;
use App\Repositories\OtpRepository\OtpRepository;
use App\Repositories\Payload\IPayloadRepository;
use App\Repositories\Payload\PayloadRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserAccount\UserAccountRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //Encryption Repositories
        $this->app->bind(IPayloadRepository::class, PayloadRepository::class);

        //Authentication Repositories
        $this->app->bind(IUserAccountRepository::class, UserAccountRepository::class);
        $this->app->bind(IClientRepository::class, ClientRepository::class);

        //Utilities Repositories
        $this->app->bind(IOtpRepository::class, OtpRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
