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
use App\Repositories\UserPhoto\UserPhotoRepository;
use App\Repositories\UserPhoto\IUserPhotoRepository;
use App\Repositories\HelpCenter\HelpCenterRepository;
use App\Repositories\LogHistory\LogHistoryRepository;
use App\Repositories\OutBuyLoad\OutBuyLoadRepository;
use App\Repositories\HelpCenter\IHelpCenterRepository;
use App\Repositories\LogHistory\ILogHistoryRepository;
use App\Repositories\OutBuyLoad\IOutBuyLoadRepository;
use App\Repositories\PrepaidLoad\PrepaidLoadRepository;
use App\Repositories\UserAccount\UserAccountRepository;
use App\Repositories\PrepaidLoad\IPrepaidLoadRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\NewsAndUpdate\NewsAndUpdateRepository;
use App\Repositories\Notification\INotificationRepository;
use App\Repositories\Notification\NotificationRepository;
use App\Repositories\TransactionCategory\ITransactionCategoryRepository;
use App\Repositories\NewsAndUpdate\INewsAndUpdateRepository;
use App\Repositories\PinCodeHistory\PinCodeHistoryRepository;
use App\Repositories\PinCodeHistory\IPinCodeHistoryRepository;
use App\Repositories\PasswordHistory\PasswordHistoryRepository;
use App\Repositories\PasswordHistory\IPasswordHistoryRepository;
use App\Repositories\TransactionCategory\TransactionCategoryRepository;

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

        //Utilities Repositories
        $this->app->bind(IOtpRepository::class, OtpRepository::class);

        $this->app->bind(IPrepaidLoadRepository::class, PrepaidLoadRepository::class);
        $this->app->bind(IOutBuyLoadRepository::class, OutBuyLoadRepository::class);

        // Notification Repository
        $this->app->bind(INotificationRepository::class, NotificationRepository::class);
        // Id Types
        $this->app->bind(IIdTypeRepository::class, IdTypeRepository::class);

        // User Photo
        $this->app->bind(IUserPhotoRepository::class, UserPhotoRepository::class);
        
        // Log History
        $this->app->bind(ILogHistoryRepository::class, LogHistoryRepository::class);

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
