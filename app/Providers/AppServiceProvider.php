<?php

namespace App\Providers;

use App\Enums\AddMoneyProviders;
use App\Enums\NetworkTypes;
use App\Enums\PayBillsConfig;
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
use App\Services\Auth\Registration\IRegistrationService;
use App\Services\Auth\Registration\RegistrationService;
use App\Services\Auth\UserKey\IUserKeyService;
use App\Services\Auth\UserKey\UserKeyService;
use App\Services\BuyLoad\BuyLoadService;
use App\Services\BuyLoad\IBuyLoadService;
use App\Services\Dashboard\DashboardService;
use App\Services\Dashboard\IDashboardService;
use App\Services\DrcrMemo\DrcrMemoService;
use App\Services\DrcrMemo\IDrcrMemoService;
use App\Services\Encryption\EncryptionService;
use App\Services\Encryption\IEncryptionService;
use App\Services\FarmerProfile\FarmerProfileService;
use App\Services\FarmerProfile\IFarmerProfileService;
use App\Services\KYCService\IKYCService;
use App\Services\KYCService\KYCService;
use App\Services\OutBuyLoad\IOutBuyLoadService;
use App\Services\OutBuyLoad\OutBuyLoadService;
use App\Services\PayBills\IPayBillsService;
use App\Services\PayBills\PayBillsService;
use App\Services\Send2Bank\Instapay\Send2BankInstapayService;
use App\Services\Send2Bank\ISend2BankDirectService;
use App\Services\Send2Bank\ISend2BankService;
use App\Services\Send2Bank\Pesonet\ISend2BankPesonetService;
use App\Services\Send2Bank\Pesonet\Send2BankPesonetService;
use App\Services\Send2Bank\Send2BankDirectService;
use App\Services\Send2Bank\Send2BankService;
use App\Services\SendMoney\ISendMoneyService;
use App\Services\SendMoney\SendMoneyService;
use App\Services\ThirdParty\BayadCenter\BayadCenterService;
use App\Services\ThirdParty\BayadCenter\IBayadCenterService;
use App\Services\ThirdParty\UBP\IUBPService;
use App\Services\ThirdParty\UBP\UBPService;
use App\Services\Tier\ITierApprovalService;
use App\Services\Tier\TierApprovalService;
use App\Services\Transaction\ITransactionService;
use App\Services\Transaction\ITransactionValidationService;
use App\Services\Transaction\TransactionService;
use App\Services\Transaction\TransactionValidationService;
use App\Services\UserAccount\IUserAccountService;
use App\Services\UserAccount\UserAccountService;
use App\Services\UserProfile\IUserProfileService;
use App\Services\UserProfile\UserProfileService;
use App\Services\TempUserDetail\ITempUserDetailService;
use App\Services\TempUserDetail\TempUserDetailService;
use App\Services\Utilities\API\ApiService;
use App\Services\Utilities\API\IApiService;
use App\Services\Utilities\CurlService\CurlService;
use App\Services\Utilities\CurlService\ICurlService;
use App\Services\Utilities\LogHistory\ILogHistoryService;
use App\Services\Utilities\LogHistory\LogHistoryService;
use App\Services\Utilities\Notifications\Email\EmailService;
use App\Services\Utilities\Notifications\Email\IEmailService;
use App\Services\Utilities\Notifications\INotificationService;
use App\Services\Utilities\Notifications\IPushNotificationService;
use App\Services\Utilities\Notifications\NotificationService;
use App\Services\Utilities\Notifications\PushNotificationService;
use App\Services\Utilities\Notifications\SMS\ISmsService;
use App\Services\Utilities\Notifications\SMS\SmsService;
use App\Services\Utilities\OTP\IOtpService;
use App\Services\Utilities\OTP\OtpService;
use App\Services\Utilities\PrepaidLoad\ATM\AtmService;
use App\Services\Utilities\PrepaidLoad\ATM\IAtmService;
use App\Services\Utilities\PrepaidLoad\GlobeService;
use App\Services\Utilities\PrepaidLoad\IPrepaidLoadService;
use App\Services\Utilities\ReferenceNumber\IReferenceNumberService;
use App\Services\Utilities\ReferenceNumber\ReferenceNumberService;
use App\Services\Utilities\Responses\IResponseService;
use App\Services\Utilities\Responses\ResponseService;
use App\Services\Utilities\ServiceFee\IServiceFeeService;
use App\Services\Utilities\ServiceFee\ServiceFeeService;
use App\Services\Utilities\Verification\IVerificationService;
use App\Services\Utilities\Verification\VerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use App\Services\Utilities\PDF\IPDFService;
use App\Services\Utilities\PDF\PDFService;
use App\Services\Utilities\CSV\ICSVService;
use App\Services\Utilities\CSV\CSVService;
use App\Services\MyTask\MyTaskService;
use App\Services\MyTask\IMyTaskService;
use App\Services\Dashboard\ForeignExchange\ForeignExchangeRateService;
use App\Services\Dashboard\ForeignExchange\IForeignExchangeRateService;

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
        $this->app->singleton(IEmailService::class, EmailService::class);
        $this->app->singleton(ISmsService::class, SmsService::class);
        $this->app->singleton(IPDFService::class, PDFService::class);
        $this->app->singleton(ICSVService::class, CSVService::class);

        //3PP APIs
        $this->app->singleton(IUBPService::class, UBPService::class);
        $this->app->singleton(IBayadCenterService::class, BayadCenterService::class);
        $this->app->singleton(IAtmService::class, AtmService::class);

        //APP SERVICES
        $this->app->singleton(IAuthService::class, AuthService::class);
        $this->app->singleton(IUserKeyService::class, UserKeyService::class);
        $this->app->singleton(IRegistrationService::class, RegistrationService::class);

        $this->app->bind(IInAddMoneyService::class, InAddMoneyService::class);
        $this->app->bind(IHandlePostBackService::class, HandlePostBackService::class);
        $this->app->bind(IUserProfileService::class, UserProfileService::class);
        $this->app->bind(IOutBuyLoadService::class, OutBuyLoadService::class);
        $this->app->bind(ISendMoneyService::class, SendMoneyService::class);
        $this->app->bind(IVerificationService::class, VerificationService::class);
        $this->app->bind(IPayBillsService::class, PayBillsService::class);
        $this->app->bind(IDrcrMemoService::class, DrcrMemoService::class);

        //APP SERVICES - CONTEXTUAL BINDINGS
        $this->bindNotificationService();
        $this->bindPrepaidLoadService();
        $this->bindSend2BankService();
        $this->bindAddMoneyService();
        $this->bindPayBillsService();

        //Dashboard
        $this->app->bind(IDashboardService::class, DashboardService::class);

        //Foreign Exchange
        $this->app->bind(IForeignExchangeRateService::class, ForeignExchangeRateService::class);

        // Notification
        $this->app->bind(INotificationService::class, NotificationService::class);
        // Push Notification
        // $this->app->bind(IPushNotificationService::class, PushNotificationService::class);

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

        // UBP Send to bank service
        $this->app->bind(ISend2BankDirectService::class, Send2BankDirectService::class);

        //User Account Service
        $this->app->bind(IUserAccountService::class, UserAccountService::class);

        // Tier Approval Service
        $this->app->bind(ITierApprovalService::class, TierApprovalService::class);

        // Buy Load Service
        $this->app->bind(IBuyLoadService::class, BuyLoadService::class);

        // eKYC Service
        $this->app->bind(IKYCService::class, KYCService::class);

        // CURL SERVICE
        $this->app->bind(ICurlService::class, CurlService::class);
        // Temp User Detail Service
        $this->app->bind(ITempUserDetailService::class, TempUserDetailService::class);
        // FARMER SERVICE
        $this->app->bind(IFarmerProfileService::class, FarmerProfileService::class);

        // My Task
        $this->app->bind(IMyTaskService::class, MyTaskService::class);
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

                if ($request->has('payload')) {

                    if ($request['provider'] === AddMoneyProviders::DragonPay) return $this->app->get(DragonPayService::class);
                }

                return $this->app->get(DragonPayService::class);
            });
    }

    private function bindSend2BankService()
    {
        $this->app->bind(ISend2BankPesonetService::class, Send2BankPesonetService::class);
        $this->app->when(Send2BankController::class)
            ->needs(ISend2BankService::class)
            ->give(function () {
                $request = app(Request::class);
                $provider = $request->route('provider');

                if ($provider) {
                    $provider = Str::lower($provider);
                    if ($provider == TpaProviders::ubpPesonet) return $this->app->get(Send2BankPesonetService::class);
                    if ($provider == TpaProviders::ubpInstapay) return $this->app->get(Send2BankInstapayService::class);
                }

                return $this->app->get(Send2BankService::class);
            });
    }

    private function bindPayBillsService()
    {
        $this->app->when(PayBillsService::class)
            ->needs(IBayadCenterService::class)
            ->give(function () {
                $request = app(Request::class);

                if ($request->has('payload')) {
                    if ($request['provider'] === PayBillsConfig::BayadCenter) return $this->app->get(BayadCenterService::class);
                }

            return $this->app->get(BayadCenterService::class);
            });
    }


}
