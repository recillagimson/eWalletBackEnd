<?php

namespace App\Providers;

use App\Enums\AddMoneyProviders;
use App\Enums\UsernameTypes;
use App\Services\Utilities\Errors\ErrorService;
use App\Services\Utilities\Errors\IErrorService;
use App\Services\AddMoney\DragonPay\HandlePostBackService;
use App\Services\AddMoney\DragonPay\IHandlePostBackService;
use App\Services\AddMoney\IInAddMoneyService;
use App\Services\AddMoney\InAddMoneyService;
use App\Services\AddMoney\Providers\DragonPayService;
use App\Services\AddMoney\Providers\IAddMoneyService;
use Illuminate\Http\Request;
use App\Services\Auth\AuthService;
use App\Services\Auth\IAuthService;
use Illuminate\Support\ServiceProvider;
use App\Services\Utilities\API\ApiService;
use App\Services\Utilities\OTP\OtpService;
use App\Services\Utilities\API\IApiService;
use App\Services\Utilities\OTP\IOtpService;
use App\Services\Encryption\EncryptionService;
use App\Services\OutBuyLoad\OutBuyLoadService;
use App\Services\Encryption\IEncryptionService;
use App\Services\OutBuyLoad\IOutBuyLoadService;
use App\Services\NewsAndUpdate\NewsAndUpdateService;
use App\Services\Utilities\ReferenceNumber\IReferenceNumberService;
use App\Services\Utilities\ReferenceNumber\ReferenceNumberService;
use App\Services\SendMoney\ISendMoneyService;
use App\Services\SendMoney\SendMoneyService;
use App\Services\Utilities\Notifications\NotificationService;
use App\Services\Utilities\Notifications\SmsService;
use App\Services\Utilities\PrepaidLoad\GlobeService;
use App\Services\Transaction\ITransactionService;
use App\Services\Transaction\TransactionService;
use App\Services\Utilities\Notifications\EmailService;
use App\Services\Utilities\LogHistory\LogHistoryService;
use App\Services\Utilities\LogHistory\ILogHistoryService;
use App\Services\Utilities\PrepaidLoad\IPrepaidLoadService;
use App\Services\Utilities\Verification\VerificationService;
use App\Services\Utilities\Verification\IVerificationService;
use App\Services\Utilities\Notifications\INotificationService;
use App\Services\Utilities\Notifications\IPushNotificationService;
use App\Services\Utilities\Notifications\PushNotificationService;
use App\Services\UserProfile\UserProfileService;
use App\Services\UserProfile\IUserProfileService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //APP SERVICES
        $this->app->bind(IAuthService::class, AuthService::class);

        //UTILITY SERVICES
        $this->app->bind(IApiService::class, ApiService::class);
        $this->app->bind(IOtpService::class, OtpService::class);
        $this->app->bind(IEncryptionService::class, EncryptionService::class);
        $this->app->bind(IErrorService::class, ErrorService::class);
        $this->app->bind(IOutBuyLoadService::class, OutBuyLoadService::class);
        $this->app->bind(INewsAndUpdateService::class, NewsAndUpdateService::class);
        $this->app->bind(IReferenceNumberService::class, ReferenceNumberService::class);
        $this->app->bind(IInAddMoneyService::class, InAddMoneyService::class);
        $this->app->bind(IHandlePostBackService::class, HandlePostBackService::class);
        $this->bindNotificationService();
        $this->bindPrepaidLoadService();
        $this->bindAddMoneyService();
        $this->app->bind(ISendMoneyService::class, SendMoneyService::class);
        $this->app->bind(IUserProfileService::class, UserProfileService::class);
        $this->bindNotificationService();
        $this->bindPrepaidLoadService();

        $this->app->bind(IUserProfileService::class, UserProfileService::class);
        $this->app->bind(IOutBuyLoadService::class, OutBuyLoadService::class);
        $this->app->bind(ISendMoneyService::class, SendMoneyService::class);

        // Notification
        $this->app->bind(INotificationService::class, NotificationService::class);
        // Push Notification
        $this->app->bind(IPushNotificationService::class, PushNotificationService::class);

        // Verification Service
        $this->app->bind(IVerificationService::class, VerificationService::class);
        // Log History Service
        $this->app->bind(ILogHistoryService::class, LogHistoryService::class);

        // Transaction Service
        $this->app->bind(ITransactionService::class, TransactionService::class);

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

    private function bindNotificationService()
    {
        $this->app->when(AuthService::class)
            ->needs(INotificationService::class)
            ->give(function() {
                $request = app(Request::class);
                $encryptionService = $this->app->make(IEncryptionService::class);

                if($request->has('payload'))
                {
                    $data = collect($encryptionService->decrypt($request->payload, $request->id, false));

                    if($data->has(UsernameTypes::MobileNumber))
                        return $this->app->get(SmsService::class);
                }

                return $this->app->get(EmailService::class);
            });
    }

    private function bindPrepaidLoadService()
    {
        $this->app->when(OutBuyLoadService::class)
            ->needs(IPrepaidLoadService::class)
            ->give(function() {
                $request = app(Request::class);

                if (strtoupper($request->route('network_type')) === NetworkTypes::Globe)
                {
                    return $this->app->get(GlobeService::class);
                }

                return $this->app->get(GlobeService::class);
            });
    }

    private function bindAddMoneyService()
    {
        $this->app->when(InAddMoneyService::class)
            ->needs(IAddMoneyService::class)
            ->give(function() {
                $request = app(Request::class);
                $encryptionService = $this->app->make(IEncryptionService::class);

                if ($request->has('payload')) {

                    $data = $encryptionService->decrypt($request->payload, $request->id, false);

                    if ($data['provider'] === AddMoneyProviders::DragonPay) return $this->app->get(DragonPayService::class);
                }

                return $this->app->get(DragonPayService::class);
            });
    }
}
