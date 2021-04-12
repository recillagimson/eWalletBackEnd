<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Client\ClientRepository;
use App\Repositories\IdType\IdTypeRepository;
use App\Repositories\Client\IClientRepository;
use App\Repositories\IdType\IIdTypeRepository;
use App\Repositories\OtpRepository\IOtpRepository;
use App\Repositories\OtpRepository\OtpRepository;
use App\Repositories\PasswordHistory\IPasswordHistoryRepository;
use App\Repositories\PasswordHistory\PasswordHistoryRepository;
use App\Repositories\Payload\IPayloadRepository;
use App\Repositories\Payload\PayloadRepository;
use App\Repositories\HelpCenter\HelpCenterRepository;
use App\Repositories\OutBuyLoad\OutBuyLoadRepository;
use App\Repositories\HelpCenter\IHelpCenterRepository;
use App\Repositories\OutBuyLoad\IOutBuyLoadRepository;
use App\Repositories\PrepaidLoad\PrepaidLoadRepository;
use App\Repositories\PinCodeHistory\IPinCodeHistoryRepository;
use App\Repositories\PinCodeHistory\PinCodeHistoryRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserAccount\UserAccountRepository;
use App\Repositories\PrepaidLoad\IPrepaidLoadRepository;
use App\Repositories\NewsAndUpdate\NewsAndUpdateRepository;
use App\Repositories\NewsAndUpdate\INewsAndUpdateRepository;
use App\Repositories\TransactionCategory\TransactionCategoryRepository;
use App\Repositories\TransactionCategory\ITransactionCategoryRepository;
use App\Repositories\UserPhoto\IUserPhotoRepository;
use App\Repositories\UserPhoto\UserPhotoRepository;
use App\Repositories\UserUtilities\UserDetail\IUserDetailRepository;
use App\Repositories\UserUtilities\UserDetail\UserDetailRepository;
use App\Repositories\UserUtilities\Currency\ICurrencyRepository;
use App\Repositories\UserUtilities\Currency\CurrencyRepository;
use App\Repositories\UserUtilities\MaritalStatus\IMaritalStatusRepository;
use App\Repositories\UserUtilities\MaritalStatus\MaritalStatusRepository;
use App\Repositories\UserUtilities\Nationality\INationalityRepository;
use App\Repositories\UserUtilities\Nationality\NationalityRepository;
use App\Repositories\UserUtilities\NatureOfWork\INatureOfWorkRepository;
use App\Repositories\UserUtilities\NatureOfWork\NatureOfWorkRepository;
use App\Repositories\UserUtilities\SignupHost\ISignupHostRepository;
use App\Repositories\UserUtilities\SignupHost\SignupHostRepository;
use App\Repositories\UserUtilities\SourceOfFund\ISourceOfFundRepository;
use App\Repositories\UserUtilities\SourceOfFund\SourceOfFundRepository;
use App\Repositories\UserUtilities\Country\ICountryRepository;
use App\Repositories\UserUtilities\Country\CountryRepository;

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
        $this->app->bind(IPasswordHistoryRepository::class, PasswordHistoryRepository::class);
        $this->app->bind(IPinCodeHistoryRepository::class, PinCodeHistoryRepository::class);

        //News and Updates Repositories
        $this->app->bind(INewsAndUpdateRepository::class, NewsAndUpdateRepository::class);

        //Help Center Repository
        $this->app->bind(IHelpCenterRepository::class, HelpCenterRepository::class);

        //Transaction Category Repository
        $this->app->bind(ITransactionCategoryRepository::class, TransactionCategoryRepository::class);
        
        //UserUtilities Repository
        $this->app->bind(ISourceOfFundRepository::class, SourceOfFundRepository::class);
        $this->app->bind(ISignupHostRepository::class, SignupHostRepository::class);
        $this->app->bind(INatureOfWorkRepository::class, NatureOfWorkRepository::class);
        $this->app->bind(INationalityRepository::class, NationalityRepository::class);
        $this->app->bind(IMaritalStatusRepository::class, MaritalStatusRepository::class);
        $this->app->bind(ICurrencyRepository::class, CurrencyRepository::class);
        $this->app->bind(ICountryRepository::class, CountryRepository::class);
        $this->app->bind(IUserDetailRepository::class, UserDetailRepository::class);

        //Utilities Repositories
        $this->app->bind(IOtpRepository::class, OtpRepository::class);

        $this->app->bind(IPrepaidLoadRepository::class, PrepaidLoadRepository::class);
        $this->app->bind(IOutBuyLoadRepository::class, OutBuyLoadRepository::class);

        // Id Types
        $this->app->bind(IIdTypeRepository::class, IdTypeRepository::class);

        // User Photo
        $this->app->bind(IUserPhotoRepository::class, UserPhotoRepository::class);

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
