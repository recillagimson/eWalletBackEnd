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
use App\Repositories\PrepaidLoad\IPrepaidLoadRepository;
use App\Repositories\PrepaidLoad\PrepaidLoadRepository;
use App\Repositories\OutBuyLoad\IOutBuyLoadRepository;
use App\Repositories\OutBuyLoad\OutBuyLoadRepository;
use App\Repositories\NewsAndUpdate\INewsAndUpdateRepository;
use App\Repositories\NewsAndUpdate\NewsAndUpdateRepository;
use App\Repositories\HelpCenter\IHelpCenterRepository;
use App\Repositories\HelpCenter\HelpCenterRepository;
use App\Repositories\OutSendMoney\IOutSendMoneyRepository;
use App\Repositories\OutSendMoney\OutSendMoneyRepository;
use App\Repositories\InReceiveMoney\IInReceiveMoneyRepository;
use App\Repositories\InReceiveMoney\InReceiveMoneyRepository;
use App\Repositories\UserBalanceInfo\IUserBalanceInfoRepository;
use App\Repositories\UserBalanceInfo\UserBalanceInfoRepository;
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

        //News and Updates Repositories
        $this->app->bind(INewsAndUpdateRepository::class, NewsAndUpdateRepository::class);

        //Help Center Repository
        $this->app->bind(IHelpCenterRepository::class, HelpCenterRepository::class);

        //Utilities Repositories
        $this->app->bind(IOtpRepository::class, OtpRepository::class);

        $this->app->bind(IPrepaidLoadRepository::class, PrepaidLoadRepository::class);
        $this->app->bind(IOutBuyLoadRepository::class, OutBuyLoadRepository::class);

        //Send Money Repositories
        $this->app->bind(IInReceiveMoneyRepository::class, InReceiveMoneyRepository::class);
        $this->app->bind(IOutSendMoneyRepository::class, OutSendMoneyRepository::class);
        $this->app->bind(IUserBalanceInfoRepository::class, UserBalanceInfoRepository::class);
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
