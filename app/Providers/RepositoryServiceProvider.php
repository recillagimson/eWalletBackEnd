<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Client\ClientRepository;
use App\Repositories\IdType\IdTypeRepository;
use App\Repositories\Client\IClientRepository;
use App\Repositories\IdType\IIdTypeRepository;
use App\Repositories\Payload\PayloadRepository;
use App\Repositories\Payload\IPayloadRepository;
use App\Repositories\OtpRepository\OtpRepository;
use App\Repositories\OtpRepository\IOtpRepository;
use App\Repositories\HelpCenter\HelpCenterRepository;
use App\Repositories\OutBuyLoad\OutBuyLoadRepository;
use App\Repositories\HelpCenter\IHelpCenterRepository;
use App\Repositories\OutBuyLoad\IOutBuyLoadRepository;
use App\Repositories\PrepaidLoad\PrepaidLoadRepository;
use App\Repositories\UserAccount\UserAccountRepository;
use App\Repositories\PrepaidLoad\IPrepaidLoadRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\NewsAndUpdate\NewsAndUpdateRepository;
use App\Repositories\NewsAndUpdate\INewsAndUpdateRepository;
use App\Repositories\TransactionCategory\TransactionCategoryRepository;
use App\Repositories\TransactionCategory\ITransactionCategoryRepository;

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

        //Transaction Category Repository
        $this->app->bind(ITransactionCategoryRepository::class, TransactionCategoryRepository::class);

        //Utilities Repositories
        $this->app->bind(IOtpRepository::class, OtpRepository::class);

        $this->app->bind(IPrepaidLoadRepository::class, PrepaidLoadRepository::class);
        $this->app->bind(IOutBuyLoadRepository::class, OutBuyLoadRepository::class);

        // Id Types
        // $this->app->bind(IdTypeRepository::class, IdTypeRepository::class);
        $this->app->bind(IIdTypeRepository::class, IdTypeRepository::class);
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
