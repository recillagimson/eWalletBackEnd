<?php

namespace App\Providers;

use App\Enums\UserKeyTypes;
use App\Models\Dashboard\TransactionCountDailyView;
use App\Repositories\Dashboard\Dashboard2022Repository;
use App\Repositories\Dashboard\IDashboard2022Repository;
use App\Repositories\Dashboard\ISignUpCountDailyRepository;
use App\Repositories\Dashboard\ISignUpCountMonthlyRepository;
use App\Repositories\Dashboard\ISignUpCountWeeklyRepository;
use App\Repositories\Dashboard\ITransactionCountDailyRepository;
use App\Repositories\Dashboard\ITransactionCountMonthlyRepository;
use App\Repositories\Dashboard\ITransactionCountWeeklyRepository;
use App\Repositories\Dashboard\SignUpCountMonthlyRepository;
use App\Repositories\Dashboard\SignUpCountWeeklyRepository;
use App\Repositories\Dashboard\SignUpUpCountDailyRepository;
use App\Repositories\Dashboard\TransactionCountDailyRepository;
use App\Repositories\Dashboard\TransactionCountMonthlyRepository;
use App\Repositories\Dashboard\TransactionCountWeeklyRepository;
use App\Repositories\OutPayMerchants\IOutPayMerchantRepository;
use App\Repositories\OutPayMerchants\OutPayMerchantRepository;
use Illuminate\Http\Request;
use App\Repositories\DBP\DBPRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\DBP\IDBPRepository;
use App\Repositories\Loan\LoanRepository;
use App\Repositories\Tier\TierRepository;
use App\Repositories\Loan\ILoanRepository;
use App\Repositories\Tier\ITierRepository;
use App\Repositories\Client\ClientRepository;
use App\Repositories\IdType\IdTypeRepository;
use App\Services\Auth\UserKey\UserKeyService;
use App\Repositories\Client\IClientRepository;
use App\Repositories\IdType\IIdTypeRepository;
use App\Repositories\Admin\Role\RoleRepository;
use App\Repositories\Payload\PayloadRepository;
use App\Repositories\Admin\Role\IRoleRepository;
use App\Repositories\Payload\IPayloadRepository;
use App\Repositories\DrcrMemo\DrcrMemoRepository;
use App\Repositories\FaceAuth\FaceAuthRepository;
use App\Repositories\OtpRepository\OtpRepository;
use App\Repositories\Tier\TierApprovalRepository;
use App\Repositories\DrcrMemo\IDrcrMemoRepository;
use App\Repositories\FaceAuth\IFaceAuthRepository;
use App\Repositories\OtpRepository\IOtpRepository;
use App\Repositories\Tier\ITierApprovalRepository;
use App\Repositories\Dashboard\DashboardRepository;
use App\Repositories\UserPhoto\UserPhotoRepository;
use App\Repositories\Dashboard\IDashboardRepository;
use App\Repositories\UserPhoto\IUserPhotoRepository;
use App\Repositories\Address\Region\RegionRepository;
use App\Repositories\HelpCenter\HelpCenterRepository;
use App\Repositories\InAddMoney\InAddMoneyRepository;
use App\Repositories\LogHistory\LogHistoryRepository;
use App\Repositories\OutBuyLoad\OutBuyLoadRepository;
use App\Repositories\ServiceFee\ServiceFeeRepository;
use App\Repositories\UserKeys\IUserKeyLogsRepository;
use App\Repositories\Address\Region\IRegionRepository;
use App\Repositories\HelpCenter\IHelpCenterRepository;
use App\Repositories\InAddMoney\IInAddMoneyRepository;
use App\Repositories\LogHistory\ILogHistoryRepository;
use App\Repositories\OutBuyLoad\IOutBuyLoadRepository;
use App\Repositories\Send2Bank\OutSend2BankRepository;
use App\Repositories\ServiceFee\IServiceFeeRepository;
use App\Repositories\OutPayBills\OutPayBillsRepository;
use App\Repositories\PrepaidLoad\PrepaidLoadRepository;
use App\Repositories\Send2Bank\IOutSend2BankRepository;
use App\Repositories\UserAccount\UserAccountRepository;
use App\Repositories\UserBalance\UserBalanceRepository;
use App\Repositories\OutPayBills\IOutPayBillsRepository;
use App\Repositories\PrepaidLoad\IPrepaidLoadRepository;
use App\Repositories\Tier\TierApprovalCommentRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserBalance\IUserBalanceRepository;
use App\Repositories\Address\Barangay\BarangayRepository;
use App\Repositories\Address\Province\ProvinceRepository;
use App\Repositories\FarmerImport\FarmerImportRepository;
use App\Repositories\Notification\NotificationRepository;
use App\Repositories\OutSendMoney\OutSendMoneyRepository;
use App\Repositories\Tier\ITierApprovalCommentRepository;
use App\Repositories\UserPhoto\UserSelfiePhotoRepository;
use App\Repositories\Address\Barangay\IBarangayRepository;
use App\Repositories\Address\Province\IProvinceRepository;
use App\Repositories\FarmerImport\IFarmerImportRepository;
use App\Repositories\Notification\INotificationRepository;
use App\Repositories\OutSendMoney\IOutSendMoneyRepository;
use App\Repositories\UserPhoto\IUserSelfiePhotoRepository;
use App\Repositories\Admin\Permission\PermissionRepository;
use App\Repositories\InAddMoneyBPI\InAddMoneyBPIRepository;
use App\Repositories\InAddMoneyUbp\InAddMoneyUbpRepository;
use App\Repositories\NewsAndUpdate\NewsAndUpdateRepository;
use App\Repositories\ProviderBanks\ProviderBanksRepository;
use App\Repositories\Admin\Permission\IPermissionRepository;
use App\Repositories\InAddMoneyBPI\IInAddMoneyBPIRepository;
use App\Repositories\InAddMoneyUbp\IInAddMoneyUbpRepository;
use App\Repositories\NewsAndUpdate\INewsAndUpdateRepository;
use App\Repositories\ProviderBanks\IProviderBanksRepository;
use App\Repositories\InReceiveMoney\InReceiveMoneyRepository;
use App\Repositories\QrTransactions\QrTransactionsRepository;
use App\Repositories\UserUtilities\Country\CountryRepository;
use App\Repositories\Disbursement\InDisbursementDbpRepository;
use App\Repositories\InReceiveMoney\IInReceiveMoneyRepository;
use App\Repositories\QrTransactions\IQrTransactionsRepository;
use App\Repositories\UserUtilities\Country\ICountryRepository;
use App\Repositories\Disbursement\IInDisbursementDbpRepository;
use App\Repositories\Disbursement\OutDisbursementDbpRepository;
use App\Repositories\InAddMoneyEcPay\InAddMoneyEcPayRepository;
use App\Repositories\KYCVerification\KYCVerificationRepository;
use App\Repositories\MerchantAccount\MerchantAccountRepository;
use App\Repositories\UBPAccountToken\UBPAccountTokenRepository;
use App\Repositories\UserBalanceInfo\UserBalanceInfoRepository;
use App\Repositories\UserUtilities\Currency\CurrencyRepository;
use App\Repositories\UserUtilities\UserRole\UserRoleRepository;
use App\Repositories\Disbursement\IOutDisbursementDbpRepository;
use App\Repositories\InAddMoneyEcPay\IInAddMoneyEcPayRepository;
use App\Repositories\KYCVerification\IKYCVerificationRepository;
use App\Repositories\MerchantAccount\IMerchantAccountRepository;
use App\Repositories\UBPAccountToken\IUBPAccountTokenRepository;
use App\Repositories\UserBalanceInfo\IUserBalanceInfoRepository;
use App\Repositories\UserUtilities\Currency\ICurrencyRepository;
use App\Repositories\UserUtilities\UserRole\IUserRoleRepository;
use App\Repositories\Address\Municipality\MunicipalityRepository;
use App\Repositories\InReceiveFromDBP\InReceiveFromDBPRepository;
use App\Repositories\ReferenceCounter\ReferenceCounterRepository;
use App\Repositories\Address\Municipality\IMunicipalityRepository;
use App\Repositories\InReceiveFromDBP\IInReceiveFromDBPRepository;
use App\Repositories\ReferenceCounter\IReferenceCounterRepository;
use App\Repositories\InAddMoneyCebuana\InAddMoneyCebuanaRepository;
use App\Repositories\UserAccountNumber\UserAccountNumberRepository;
use App\Repositories\UserUtilities\SignupHost\SignupHostRepository;
use App\Repositories\UserUtilities\UserDetail\UserDetailRepository;
use App\Repositories\InAddMoneyCebuana\IInAddMoneyCebuanaRepository;
use App\Repositories\UserAccountNumber\IUserAccountNumberRepository;
use App\Repositories\UserUtilities\SignupHost\ISignupHostRepository;
use App\Repositories\UserUtilities\UserDetail\IUserDetailRepository;
use App\Repositories\UserUtilities\Nationality\NationalityRepository;
use App\Repositories\UserKeys\PinCodeHistory\PinCodeHistoryRepository;
use App\Repositories\UserUtilities\Nationality\INationalityRepository;
use App\Repositories\InAddMoneyUpbDirect\InAddMoneyUpbDirectRepository;
use App\Repositories\TransactionCategory\TransactionCategoryRepository;
use App\Repositories\UserKeys\PinCodeHistory\IPinCodeHistoryRepository;
use App\Repositories\UserUtilities\NatureOfWork\NatureOfWorkRepository;
use App\Repositories\UserUtilities\SourceOfFund\SourceOfFundRepository;
use App\Repositories\InAddMoneyUpbDirect\IInAddMoneyUpbDirectRepository;
use App\Repositories\TransactionCategory\ITransactionCategoryRepository;
use App\Repositories\UserKeys\PasswordHistory\PasswordHistoryRepository;
use App\Repositories\UserUtilities\NatureOfWork\INatureOfWorkRepository;
use App\Repositories\UserUtilities\SourceOfFund\ISourceOfFundRepository;
use App\Repositories\UserKeys\PasswordHistory\IPasswordHistoryRepository;
use App\Repositories\UserUtilities\MaritalStatus\MaritalStatusRepository;
use App\Repositories\UserUtilities\MaritalStatus\IMaritalStatusRepository;
use App\Repositories\UserUtilities\TempUserDetail\TempUserDetailRepository;
use App\Repositories\UserUtilities\TempUserDetail\ITempUserDetailRepository;
use App\Repositories\UserTransactionHistory\UserTransactionHistoryRepository;
use App\Repositories\UserTransactionHistory\IUserTransactionHistoryRepository;
use App\Repositories\PreferredCashOutPartner\PreferredCashOutPartnerRepository;
use App\Repositories\PreferredCashOutPartner\IPreferredCashOutPartnerRepository;
use App\Repositories\WhiteList\IWhiteListRepository;
use App\Repositories\WhiteList\WhiteListRepository;
use App\Repositories\DrcrMemoControlNumber\DrcrMemoControlNumberRepository;
use App\Repositories\DrcrMemoControlNumber\IDrcrMemoControlNumberRepository;

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
        $this->app->singleton(IUserAccountRepository::class, UserAccountRepository::class);
        $this->app->singleton(IUserAccountNumberRepository::class, UserAccountNumberRepository::class);
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
        $this->app->bind(IUserDetailRepository::class, UserDetailRepository::class);
        $this->app->bind(ISourceOfFundRepository::class, SourceOfFundRepository::class);
        $this->app->bind(ISignupHostRepository::class, SignupHostRepository::class);
        $this->app->bind(INatureOfWorkRepository::class, NatureOfWorkRepository::class);
        $this->app->bind(INationalityRepository::class, NationalityRepository::class);
        $this->app->bind(IMaritalStatusRepository::class, MaritalStatusRepository::class);
        $this->app->bind(ICurrencyRepository::class, CurrencyRepository::class);
        $this->app->bind(ICountryRepository::class, CountryRepository::class);
        $this->app->bind(IProviderBanksRepository::class, ProviderBanksRepository::class);

        //Utilities Repositories
        $this->app->bind(IOtpRepository::class, OtpRepository::class);
        $this->app->bind(IPrepaidLoadRepository::class, PrepaidLoadRepository::class);
        $this->app->bind(IOutBuyLoadRepository::class, OutBuyLoadRepository::class);
        $this->app->bind(IServiceFeeRepository::class, ServiceFeeRepository::class);
        $this->app->bind(ITransactionCategoryRepository::class, TransactionCategoryRepository::class);
        $this->app->bind(IUserBalanceInfoRepository::class, UserBalanceInfoRepository::class);
        $this->app->bind(ILogHistoryRepository::class, LogHistoryRepository::class);
        $this->app->bind(IUserTransactionHistoryRepository::class, UserTransactionHistoryRepository::class);
        $this->app->bind(IReferenceCounterRepository::class, ReferenceCounterRepository::class);

        //Send Money Repositories
        $this->app->bind(IInReceiveMoneyRepository::class, InReceiveMoneyRepository::class);
        $this->app->bind(IOutSendMoneyRepository::class, OutSendMoneyRepository::class);
        $this->app->bind(IUserBalanceInfoRepository::class, UserBalanceInfoRepository::class);
        $this->app->bind(IQrTransactionsRepository::class, QrTransactionsRepository::class);

        // Address
        $this->app->bind(IRegionRepository::class, RegionRepository::class);
        $this->app->bind(IProvinceRepository::class, ProvinceRepository::class);
        $this->app->bind(IMunicipalityRepository::class, MunicipalityRepository::class);
        $this->app->bind(IBarangayRepository::class, BarangayRepository::class);

        // Notification Repository
        $this->app->bind(INotificationRepository::class, NotificationRepository::class);
        // Id Types
        $this->app->bind(IIdTypeRepository::class, IdTypeRepository::class);

        // User Photo
        $this->app->bind(IUserPhotoRepository::class, UserPhotoRepository::class);
        $this->app->bind(IUserSelfiePhotoRepository::class, UserSelfiePhotoRepository::class);

        // Log History
        $this->app->bind(ILogHistoryRepository::class, LogHistoryRepository::class);

        // User Balance
        $this->app->bind(IUserBalanceRepository::class, UserBalanceRepository::class);

        // User Transaction History
        $this->app->bind(IUserTransactionHistoryRepository::class, UserTransactionHistoryRepository::class);

        // Add Money Repository
        $this->app->bind(IInAddMoneyRepository::class, InAddMoneyRepository::class);

        // Tier Repository
        $this->app->bind(ITierRepository::class, TierRepository::class);
        $this->app->bind(ITierApprovalRepository::class, TierApprovalRepository::class);
        $this->app->bind(ITierApprovalCommentRepository::class, TierApprovalCommentRepository::class);

        // Service Fee Repository
        $this->app->bind(IServiceFeeRepository::class, ServiceFeeRepository::class);

        $this->app->bind(IOutSend2BankRepository::class, OutSend2BankRepository::class);

        //Pay Bills Repositories
        $this->app->bind(IOutPayBillsRepository::class, OutPayBillsRepository::class);

        //Temp User Repositories
        $this->app->bind(ITempUserDetailRepository::class, TempUserDetailRepository::class);

        // Role Repositories
        $this->app->bind(IRoleRepository::class, RoleRepository::class);

        // Permission Repositories
        $this->app->bind(IPermissionRepository::class, PermissionRepository::class);

        // User Role Repositories
        $this->app->bind(IUserRoleRepository::class, UserRoleRepository::class);

        //Drcr Memos Repositories
        $this->app->bind(IDrcrMemoRepository::class, DrcrMemoRepository::class);

        //Drcr Memo Control Numbers Repositories
        $this->app->bind(IDrcrMemoControlNumberRepository::class, DrcrMemoControlNumberRepository::class);

        // KYC Verification
        $this->app->bind(IKYCVerificationRepository::class, KYCVerificationRepository::class);

        // Disbursement DBP
        $this->app->bind(IOutDisbursementDbpRepository::class, OutDisbursementDbpRepository::class);
        $this->app->bind(IInDisbursementDbpRepository::class, InDisbursementDbpRepository::class);

        // In Receive Money from DBP
        $this->app->bind(IInReceiveFromDBPRepository::class, InReceiveFromDBPRepository::class);

        // Add Money from UPB Direct

        //CONTEXTUAL BINDINGS
        $this->bindUserKeyRepository();

        //Add Money Repositories
        $this->app->bind(IInAddMoneyEcPayRepository::class, InAddMoneyEcPayRepository::class);
        $this->app->bind(IInAddMoneyUpbDirectRepository::class, InAddMoneyUpbDirectRepository::class);
        $this->app->bind(IInAddMoneyBPIRepository::class, InAddMoneyBPIRepository::class);
        $this->app->bind(IInAddMoneyUbpRepository::class, InAddMoneyUbpRepository::class);

        // Dashboard
        $this->app->bind(IDashboardRepository::class, DashboardRepository::class);
        $this->app->bind(IDashboard2022Repository::class, Dashboard2022Repository::class);
        $this->app->bind(ITransactionCountDailyRepository::class, TransactionCountDailyRepository::class);
        $this->app->bind(ITransactionCountMonthlyRepository::class, TransactionCountMonthlyRepository::class);
        $this->app->bind(ITransactionCountWeeklyRepository::class, TransactionCountWeeklyRepository::class);
        $this->app->bind(ISignUpCountDailyRepository::class, SignUpUpCountDailyRepository::class);
        $this->app->bind(ISignUpCountWeeklyRepository::class, SignUpCountWeeklyRepository::class);
        $this->app->bind(ISignUpCountMonthlyRepository::class, SignUpCountMonthlyRepository::class);

        // Loan
        $this->app->bind(ILoanRepository::class, LoanRepository::class);

        // FarmerImport
        $this->app->bind(IFarmerImportRepository::class, FarmerImportRepository::class);

        // PREFERRED CASHOUT PARTNER
        $this->app->bind(IPreferredCashOutPartnerRepository::class, PreferredCashOutPartnerRepository::class);

        // DBP REPOSITORY
        $this->app->bind(IDBPRepository::class, DBPRepository::class);

        //UBP Account Tokens
        $this->app->bind(IUBPAccountTokenRepository::class, UBPAccountTokenRepository::class);

        // MERCHANT ACCOUNT
        $this->app->bind(IMerchantAccountRepository::class, MerchantAccountRepository::class);

        // FACE AUTH
        $this->app->bind(IFaceAuthRepository::class, FaceAuthRepository::class);

        // CEBUANA
        $this->app->bind(IInAddMoneyCebuanaRepository::class, InAddMoneyCebuanaRepository::class);

        // WHITE LISTING
        $this->app->bind(IWhiteListRepository::class, WhiteListRepository::class);

        // OUT PAY MERCHANT
        $this->app->bind(IOutPayMerchantRepository::class, OutPayMerchantRepository::class);
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

    private function bindUserKeyRepository()
    {
        $this->app->when(UserKeyService::class)
            ->needs(IUserKeyLogsRepository::class)
            ->give(function () {
                $request = app(Request::class);

                if ($request->route('keyType')) {
                    $keyType = $request->route('keyType');
                    if ($keyType == UserKeyTypes::pin) return $this->app->get(PinCodeHistoryRepository::class);
                }

                return $this->app->get(PasswordHistoryRepository::class);
            });
    }
}
