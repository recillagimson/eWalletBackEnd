<?php

namespace App\Providers;

use App\Enums\AddMoneyProviders;
use App\Enums\UsernameTypes;
use App\Enums\NetworkTypes;
use App\Services\AddMoney\DragonPay\HandlePostBackService;
use App\Services\AddMoney\DragonPay\IHandlePostBackService;
use App\Services\AddMoney\IInAddMoneyService;
use App\Services\AddMoney\InAddMoneyService;
use App\Services\AddMoney\Providers\DragonPayService;
use App\Services\AddMoney\Providers\IAddMoneyService;
use App\Services\Auth\AuthService;
use App\Services\Auth\IAuthService;
use App\Services\Utilities\PrepaidLoad\IPrepaidLoadService;
use App\Services\Utilities\PrepaidLoad\GlobeService;
use App\Services\Encryption\EncryptionService;
use App\Services\Encryption\IEncryptionService;
use App\Services\Utilities\API\ApiService;
use App\Services\Utilities\API\IApiService;
use App\Services\Utilities\Notifications\EmailService;
use App\Services\Utilities\Notifications\INotificationService;
use App\Services\Utilities\Notifications\SmsService;
use App\Services\Utilities\OTP\IOtpService;
use App\Services\Utilities\OTP\OtpService;
use App\Services\OutBuyLoad\IOutBuyLoadService;
use App\Services\OutBuyLoad\OutBuyLoadService;
use App\Services\NewsAndUpdate\INewsAndUpdateService;
use App\Services\NewsAndUpdate\NewsAndUpdateService;
use App\Services\Utilities\ReferenceNumber\IReferenceNumberService;
use App\Services\Utilities\ReferenceNumber\ReferenceNumberService;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationException;

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
        $this->app->bind(IOutBuyLoadService::class, OutBuyLoadService::class);
        $this->app->bind(INewsAndUpdateService::class, NewsAndUpdateService::class);
        $this->app->bind(IReferenceNumberService::class, ReferenceNumberService::class);
        $this->app->bind(IInAddMoneyService::class, InAddMoneyService::class);
        $this->app->bind(IHandlePostBackService::class, HandlePostBackService::class);
        $this->bindNotificationService();
        $this->bindPrepaidLoadService();
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
                $encryptionService = $this->app->make(IEncryptionService::class);

                if($request->has('payload'))
                {
                    $data = $encryptionService->decrypt($request->payload, $request->id, false);

                    if($data['network_type'] === NetworkTypes::Globe)
                        return $this->app->get(GlobeService::class);
                }

                // return $this->app->get(GlobeService::class);
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
