<?php

namespace App\Providers;

use App\Enums\AddMoneyProviders;
use App\Enums\NetworkTypes;
use App\Enums\TpaProviders;
use App\Enums\UsernameTypes;
use App\Http\Controllers\Send2BankController;
use App\Services\AddMoney\DragonPay\HandlePostBackService;
use App\Services\AddMoney\DragonPay\IHandlePostBackService;
use App\Services\AddMoney\IInAddMoneyService;
use App\Services\AddMoney\InAddMoneyService;
use App\Services\AddMoney\Providers\DragonPayService;
use App\Services\AddMoney\Providers\IAddMoneyService;
use App\Services\Auth\AuthService;
use App\Services\Auth\IAuthService;
use App\Services\Encryption\EncryptionService;
use App\Services\Encryption\IEncryptionService;
use App\Services\OutBuyLoad\IOutBuyLoadService;
use App\Services\OutBuyLoad\OutBuyLoadService;
use App\Services\Send2Bank\ISend2BankService;
use App\Services\Send2Bank\Send2BankPesonetService;
use App\Services\SendMoney\ISendMoneyService;
use App\Services\SendMoney\SendMoneyService;
use App\Services\ThirdParty\UBP\IUBPService;
use App\Services\ThirdParty\UBP\UBPService;
use App\Services\Transaction\ITransactionService;
use App\Services\Transaction\TransactionService;
use App\Services\UserProfile\IUserProfileService;
use App\Services\UserProfile\UserProfileService;
use App\Services\Utilities\API\ApiService;
use App\Services\Utilities\API\IApiService;
use App\Services\Utilities\LogHistory\ILogHistoryService;
use App\Services\Utilities\LogHistory\LogHistoryService;
use App\Services\Utilities\Notifications\EmailService;
use App\Services\Utilities\Notifications\INotificationService;
use App\Services\Utilities\Notifications\PushNotificationService;
use App\Services\Utilities\Notifications\SmsService;
use App\Services\Utilities\OTP\IOtpService;
use App\Services\Utilities\OTP\OtpService;
use App\Services\Utilities\PrepaidLoad\GlobeService;
use App\Services\Utilities\PrepaidLoad\IPrepaidLoadService;
use App\Services\Utilities\ReferenceNumber\IReferenceNumberService;
use App\Services\Utilities\ReferenceNumber\ReferenceNumberService;
use App\Services\Utilities\Responses\IResponseService;
use App\Services\Utilities\Responses\ResponseService;
use App\Services\Utilities\ServiceFeeService\IServiceFeeService;
use App\Services\Utilities\ServiceFeeService\ServiceFeeService;
use App\Services\Utilities\Verification\IVerificationService;
use App\Services\Utilities\Verification\VerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //UTILITY SERVICES
        $this->app->singleton(IApiService::class, ApiService::class);
        $this->app->singleton(IOtpService::class, OtpService::class);
        $this->app->singleton(IEncryptionService::class, EncryptionService::class);
        $this->app->singleton(IResponseService::class, ResponseService::class);
        $this->app->singleton(IReferenceNumberService::class, ReferenceNumberService::class);
        $this->app->singleton(IPushNotificationService::class, PushNotificationService::class);
        $this->app->singleton(ILogHistoryService::class, LogHistoryService::class);
        $this->app->singleton(ITransactionService::class, TransactionService::class);

        //3PP APIs
        $this->app->singleton(IUBPService::class, UBPService::class);

        //APP SERVICES
        $this->app->bind(IAuthService::class, AuthService::class);
        $this->app->bind(IInAddMoneyService::class, InAddMoneyService::class);
        $this->app->bind(IHandlePostBackService::class, HandlePostBackService::class);
        $this->app->bind(IUserProfileService::class, UserProfileService::class);
        $this->app->bind(IOutBuyLoadService::class, OutBuyLoadService::class);
        $this->app->bind(ISendMoneyService::class, SendMoneyService::class);
        $this->app->bind(IVerificationService::class, VerificationService::class);

        //APP SERVICES - CONTEXTUAL BINDINGS
        $this->bindNotificationService();
        $this->bindPrepaidLoadService();
        
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

        // Validation Service
        $this->app->bind(ITransactionValidationService::class, TransactionValidationService::class);
        
        // Service Fee Service
        $this->app->bind(IServiceFeeService::class, ServiceFeeService::class);
        
        $this->bindSend2BankService();
        $this->bindAddMoneyService();
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
        $this->app->when([AuthService::class, SendMoneyService::class])
            ->needs(INotificationService::class)
            ->give(function () {
                $request = app(Request::class);
                $encryptionService = $this->app->make(IEncryptionService::class);

                if ($request->has('payload')) {
                    $data = collect($encryptionService->decrypt($request->payload, $request->id, false));

                    if ($data->has(UsernameTypes::MobileNumber))
                        return $this->app->get(SmsService::class);
                }

                return $this->app->get(EmailService::class);
            });
    }

    private function bindPrepaidLoadService()
    {
        $this->app->when(OutBuyLoadService::class)
            ->needs(IPrepaidLoadService::class)
            ->give(function () {
                $request = app(Request::class);

                if (strtoupper($request->route('network_type')) === NetworkTypes::Globe) {
                    return $this->app->get(GlobeService::class);
                }

                return $this->app->get(GlobeService::class);
            });
    }

    private function bindAddMoneyService()
    {
        $this->app->when(InAddMoneyService::class)
            ->needs(IAddMoneyService::class)
            ->give(function () {
                $request = app(Request::class);
                $encryptionService = $this->app->make(IEncryptionService::class);

                if ($request->has('payload')) {

                    $data = $encryptionService->decrypt($request->payload, $request->id, false);

                    if ($data['provider'] === AddMoneyProviders::DragonPay) return $this->app->get(DragonPayService::class);
                }

                return $this->app->get(DragonPayService::class);
            });
    }

    private function bindSend2BankService()
    {
        $this->app->when(Send2BankController::class)
            ->needs(ISend2BankService::class)
            ->give(function () {
                $request = app(Request::class);
                $provider = $request->route('provider');

                if ($provider) {
                    $provider = Str::lower($provider);
                    if ($provider == TpaProviders::ubpPesonet) return $this->app->get(Send2BankPesonetService::class);
                }

                return $this->app->get(Send2BankPesonetService::class);
            });
    }


}
