<?php

namespace App\Providers;

use App\Enums\UsernameTypes;
use App\Services\Auth\AuthService;
use App\Services\Auth\IAuthService;
use App\Services\Encryption\EncryptionService;
use App\Services\Encryption\IEncryptionService;
use App\Services\Utilities\API\ApiService;
use App\Services\Utilities\API\IApiService;
use App\Services\Utilities\Notifications\EmailService;
use App\Services\Utilities\Notifications\INotificationService;
use App\Services\Utilities\Notifications\SmsService;
use App\Services\Utilities\OTP\IOtpService;
use App\Services\Utilities\OTP\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

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
        $this->bindNotificationService();

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
                $data = collect($encryptionService->decrypt($request->payload, $request->id, false));

                if($data->has(UsernameTypes::MobileNumber))
                    return $this->app->get(SmsService::class);

                return $this->app->get(EmailService::class);
            });
    }
}
