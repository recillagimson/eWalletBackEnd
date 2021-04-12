<?php

namespace App\Providers;

use App\Enums\NetworkTypes;
use App\Enums\UsernameTypes;
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
use App\Services\Utilities\Notifications\SmsService;
use App\Services\Utilities\PrepaidLoad\GlobeService;
use App\Services\NewsAndUpdate\INewsAndUpdateService;
use App\Services\Utilities\Notifications\EmailService;
use App\Services\Utilities\PrepaidLoad\IPrepaidLoadService;
use App\Services\Utilities\Verification\VerificationService;
use App\Services\Utilities\Verification\IVerificationService;
use App\Services\Utilities\Notifications\INotificationService;
use App\Services\Utilities\Verification\ILogService;
use App\Services\Utilities\Verification\LogService;

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
        $this->bindNotificationService();
        $this->bindPrepaidLoadService();
        // Verification Service
        $this->app->bind(IVerificationService::class, VerificationService::class);
        // Log History Service
        $this->app->bind(ILogService::class, LogService::class);
        
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

                if(strtoupper($request->route('network_type')) === NetworkTypes::Globe)
                {
                    return $this->app->get(GlobeService::class);
                }

                return $this->app->get(GlobeService::class);
            });
    }
}
