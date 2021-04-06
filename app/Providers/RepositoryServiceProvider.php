<?php

namespace App\Providers;

use App\Repositories\AddMoney\IWebBankRepository;
use App\Repositories\AddMoney\WebBankRepository;
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
use App\Repositories\ServiceFee\IServiceFeeRepository;
use App\Repositories\ServiceFee\ServiceFeeRepository;
use App\Repositories\TransactionCategory\ITransactionCategoryRepository;
use App\Repositories\TransactionCategory\TransactionCategoryRepository;
use App\Repositories\UserBalanceInfo\IUserBalanceInfoRepository;
use App\Repositories\UserBalanceInfo\UserBalanceInfoRepository;
use App\Repositories\UserDetail\IUserDetailRepository;
use App\Repositories\UserDetail\UserDetailRepository;
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

        $this->app->bind(IWebBankRepository::class, WebBankRepository::class);
        $this->app->bind(IPrepaidLoadRepository::class, PrepaidLoadRepository::class);
        $this->app->bind(IOutBuyLoadRepository::class, OutBuyLoadRepository::class);
        $this->app->bind(IUserDetailRepository::class, UserDetailRepository::class);
        $this->app->bind(IServiceFeeRepository::class, ServiceFeeRepository::class);
        $this->app->bind(ITransactionCategoryRepository::class, TransactionCategoryRepository::class);
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
