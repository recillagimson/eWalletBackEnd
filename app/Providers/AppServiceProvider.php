<?php

namespace App\Providers;

use App\Services\Auth\IUserService;
use App\Services\Auth\UserKey\IUserKeyService;
use App\Services\Auth\UserKey\UserKeyService;
use App\Services\Auth\UserService;
use App\Services\BPIService\BPINotificationService;
use App\Services\BPIService\IBPINotificationService;
use App\Services\OutPayMerchant\IPayMerchantService;
use App\Services\OutPayMerchant\PayMerchantService;
use Exception;
use App\Enums\NetworkTypes;
use App\Enums\TpaProviders;
use Illuminate\Support\Str;
use App\Enums\UsernameTypes;
use Illuminate\Http\Request;
use App\Enums\PayBillsConfig;
use App\Enums\AddMoneyProviders;
use App\Services\v2\Auth\AuthService;
use App\Services\Loan\LoanService;
use App\Services\v2\Auth\IAuthService;
use App\Services\Loan\ILoanService;
use App\Services\MyTask\MyTaskService;
use App\Services\Report\ReportService;
use App\Services\BPIService\BPIService;
use App\Services\KYCService\KYCService;
use App\Services\MyTask\IMyTaskService;
use App\Services\Printing\PrintService;
use App\Services\Report\IReportService;
use App\Services\UBP\UbpAccountService;
use Illuminate\Support\ServiceProvider;
use App\Services\BPIService\IBPIService;
use App\Services\BuyLoad\BuyLoadService;
use App\Services\KYCService\IKYCService;
use App\Services\Printing\IPrintService;
use App\Services\UBP\IUbpAccountService;
use App\Services\BuyLoad\IBuyLoadService;
use App\Services\ThirdParty\GH\GHService;
use Illuminate\Support\Facades\Validator;
use App\Services\DrcrMemo\DrcrMemoService;
use App\Services\Merchant\MerchantService;
use App\Services\PayBills\PayBillsService;
use App\Services\ThirdParty\GH\IGHService;
use App\Services\Tier\TierApprovalService;
use App\Services\Utilities\API\ApiService;
use App\Services\Utilities\CSV\CSVService;
use App\Services\Utilities\OTP\OtpService;
use App\Services\Utilities\PDF\PDFService;
use Intervention\Image\ImageManagerStatic;
use App\Services\DrcrMemo\IDrcrMemoService;
use App\Services\Merchant\IMerchantService;
use App\Services\PayBills\IPayBillsService;
use App\Services\ThirdParty\UBP\UBPService;
use App\Services\Tier\ITierApprovalService;
use App\Services\Utilities\API\IApiService;
use App\Services\Utilities\CSV\ICSVService;
use App\Services\Utilities\OTP\IOtpService;
use App\Services\Utilities\PDF\IPDFService;
use App\Services\AddMoney\InAddMoneyService;
use App\Services\AddMoneyV2\AddMoneyService;
use App\Services\Dashboard\DashboardService;
use App\Services\DBPReport\DBPReportService;
use App\Services\Send2Bank\Send2BankService;
use App\Services\SendMoney\SendMoneyService;
use App\Services\ThirdParty\UBP\IUBPService;
use App\Http\Controllers\Send2BankController;
use App\Services\AddMoney\IInAddMoneyService;
use App\Services\Dashboard\IDashboardService;
use App\Services\DBPReport\IDBPReportService;
use App\Services\Send2Bank\ISend2BankService;
use App\Services\SendMoney\ISendMoneyService;
use App\Services\Encryption\EncryptionService;
use App\Services\OutBuyLoad\OutBuyLoadService;
use App\Services\Encryption\IEncryptionService;
use App\Services\OutBuyLoad\IOutBuyLoadService;
use App\Services\ThirdParty\ECPay\ECPayService;
use App\Services\FarmerProfile\DBPUploadService;
use App\Services\ThirdParty\ECPay\IECPayService;
use App\Services\Transaction\TransactionService;
use App\Services\UserAccount\UserAccountService;
use App\Services\UserProfile\UserProfileService;
use App\Services\AddMoney\UBP\UbpAddMoneyService;
use App\Services\FarmerProfile\IDBPUploadService;
use App\Services\Transaction\ITransactionService;
use App\Services\UserAccount\IUserAccountService;
use App\Services\UserProfile\IUserProfileService;
use App\Services\AddMoney\UBP\IUbpAddMoneyService;
use App\Services\Send2Bank\Send2BankDirectService;
use App\Services\Send2Bank\ISend2BankDirectService;
use App\Services\Utilities\CurlService\CurlService;
use App\Services\FarmerAccount\FarmerAccountService;
use App\Services\FarmerProfile\FarmerProfileService;
use App\Services\Utilities\CurlService\ICurlService;
use App\Services\Utilities\PrepaidLoad\GlobeService;
use App\Services\AddMoney\Providers\DragonPayService;
use App\Services\AddMoney\Providers\IAddMoneyService;
use App\Services\Disbursement\DisbursementDbpService;
use App\Services\FarmerAccount\IFarmerAccountService;
use App\Services\FarmerProfile\IFarmerProfileService;
use App\Services\Utilities\Responses\ResponseService;
use App\Services\Disbursement\IDisbursementDbpService;
use App\Services\TempUserDetail\TempUserDetailService;
use App\Services\Utilities\PrepaidLoad\ATM\AtmService;
use App\Services\Utilities\Responses\IResponseService;
use App\Services\v2\Auth\AuthService as AuthV2Service;
use App\Services\Admin\Dashboard\AdminDashboardService;
use App\Services\Auth\Registration\RegistrationService;
use App\Services\TempUserDetail\ITempUserDetailService;
use App\Services\Utilities\PrepaidLoad\ATM\IAtmService;
use App\Services\AddmoneyCebuana\AddMoneyCebuanaService;
use App\Services\Admin\Dashboard\IAdminDashboardService;
use App\Services\Auth\Registration\IRegistrationService;
use App\Services\MerchantAccount\MerchantAccountService;
use App\Services\ThirdParty\DragonPay\IDragonPayService;
use App\Services\Utilities\LogHistory\LogHistoryService;
use App\Services\Utilities\Notifications\SMS\SmsService;
use App\Services\Utilities\ServiceFee\ServiceFeeService;
use App\Services\v2\Auth\IAuthService as IAuthV2Service;
use App\Services\AddmoneyCebuana\IAddMoneyCebuanaService;
use App\Services\MerchantAccount\IMerchantAccountService;
use App\Services\Utilities\LogHistory\ILogHistoryService;
use App\Services\Utilities\Notifications\SMS\ISmsService;
use App\Services\Utilities\ServiceFee\IServiceFeeService;
use App\Services\AddMoney\DragonPay\HandlePostBackService;
use App\Services\Transaction\TransactionValidationService;
use App\Services\AddMoney\DragonPay\IHandlePostBackService;
use App\Services\Send2Bank\Pesonet\Send2BankPesonetService;
use App\Services\ThirdParty\BayadCenter\BayadCenterService;
use App\Services\Transaction\ITransactionValidationService;
use App\Services\Utilities\PrepaidLoad\IPrepaidLoadService;
use App\Services\Send2Bank\Pesonet\ISend2BankPesonetService;
use App\Services\ThirdParty\BayadCenter\IBayadCenterService;
use App\Services\Utilities\Notifications\Email\EmailService;
use App\Services\Utilities\Verification\VerificationService;
use App\Services\Send2Bank\Instapay\Send2BankInstapayService;
use App\Services\Send2Bank\Pesonet\Send2BankSBPesonetService;
use App\Services\ThirdParty\SecurityBank\SecurityBankService;
use App\Services\Utilities\Notifications\Email\IEmailService;
use App\Services\Utilities\Notifications\NotificationService;
use App\Services\Utilities\Verification\IVerificationService;
use App\Services\ThirdParty\SecurityBank\ISecurityBankService;
use App\Services\Utilities\Notifications\INotificationService;
use App\Services\Send2Bank\Instapay\Send2BankSBInstapayService;
use App\Services\Send2Bank\Instapay\ISend2BankSBInstapayService;
use App\Services\Utilities\Notifications\PushNotificationService;
use App\Services\Utilities\Notifications\IPushNotificationService;
use App\Services\Utilities\ReferenceNumber\ReferenceNumberService;
use App\Services\AddMoneyV2\IAddMoneyService as IAddMoneyServiceV2;
use App\Services\Utilities\ReferenceNumber\IReferenceNumberService;
use App\Services\Dashboard\ForeignExchange\ForeignExchangeRateService;
use App\Services\Dashboard\ForeignExchange\IForeignExchangeRateService;
use App\Services\ThirdParty\DragonPay\DragonPayService as DragonPayServiceV2;
use App\Services\Auth\Registration\RegistrationService as RegistrationV2Service;
use App\Services\Auth\Registration\IRegistrationService as IRegistrationV2Service;


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
        $this->app->singleton(IPrintService::class, PrintService::class);

        //3PP APIs
        $this->app->singleton(IUBPService::class, UBPService::class);
        $this->app->singleton(IUbpAccountService::class, UbpAccountService::class);
        $this->app->singleton(ISecurityBankService::class, SecurityBankService::class);
        $this->app->singleton(IBayadCenterService::class, BayadCenterService::class);
        $this->app->singleton(IAtmService::class, AtmService::class);
        $this->app->singleton(IDragonPayService::class, DragonPayServiceV2::class);
        $this->app->singleton(IGHService::class, GHService::class);
        $this->app->singleton(IECPayService::class, ECPayService::class);

        //APP SERVICES
        $this->app->singleton(IAuthService::class, AuthService::class);
        $this->app->singleton(IUserKeyService::class, UserKeyService::class);
        $this->app->singleton(IRegistrationService::class, RegistrationService::class);

        $this->app->bind(IInAddMoneyService::class, InAddMoneyService::class);
        $this->app->bind(IUbpAddMoneyService::class, UbpAddMoneyService::class);
        $this->app->bind(IAddMoneyServiceV2::class, AddMoneyService::class);
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

        //Admin Dashboard
        $this->app->bind(IAdminDashboardService::class, AdminDashboardService::class);

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

        // BPI SERVICE
        $this->app->bind(IDisbursementDbpService::class, DisbursementDbpService::class);

        // BPI SERVICES
        $this->app->bind(IBPIService::class, BPIService::class);
        $this->app->bind(IBPINotificationService::class, BPINotificationService::class);

        // My Task
        $this->app->bind(IMyTaskService::class, MyTaskService::class);
        // Report
        $this->app->bind(IReportService::class, ReportService::class);
        // Loan
        $this->app->bind(ILoanService::class, LoanService::class);

        // CEBUANA SERVICE
        $this->app->bind(IAddMoneyCebuanaService::class, AddMoneyCebuanaService::class);

        // Auth v2 SERVICE
        $this->app->bind(IAuthV2Service::class, AuthV2Service::class);

        // Registration v2 SERVICE
        $this->app->bind(IRegistrationV2Service::class, RegistrationV2Service::class);

        // Merchant
        $this->app->bind(IMerchantService::class, MerchantService::class);
        $this->app->bind(IMerchantAccountService::class, MerchantAccountService::class);

        // DBP
        $this->app->bind(IDBPReportService::class, DBPReportService::class);
        $this->app->bind(IDBPUploadService::class, DBPUploadService::class);

        // Pay Merchant
        $this->app->bind(IPayMerchantService::class, PayMerchantService::class);

        $this->app->bind(IUserService::class, UserService::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('imageable', function ($attribute, $value, $params, $validator) {
            try {
                ImageManagerStatic::make($value);
                return true;
            } catch (Exception $e) {
                return false;
            }
        });
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
        $this->app->bind(ISend2BankSBInstapayService::class, Send2BankSBInstapayService::class);
        $this->app->when(Send2BankController::class)
            ->needs(ISend2BankService::class)
            ->give(function () {
                $request = app(Request::class);
                $provider = $request->route('provider');

                if ($provider) {
                    $provider = Str::lower($provider);
                    if ($provider == TpaProviders::ubpPesonet) return $this->app->get(Send2BankPesonetService::class);
                    if ($provider == TpaProviders::ubpInstapay) return $this->app->get(Send2BankInstapayService::class);
                    if ($provider == TpaProviders::secBankInstapay) return $this->app->get(Send2BankSBInstapayService::class);
                    if ($provider == TpaProviders::secBankPesonet) return $this->app->get(Send2BankSBPesonetService::class);
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
