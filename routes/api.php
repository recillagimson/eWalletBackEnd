<?php

use App\Http\Controllers\AddMoneyController;
use App\Http\Controllers\Admin\MyTaskController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\Auth\ForgotKeyController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangayController;
use App\Http\Controllers\BPIController;
use App\Http\Controllers\BuyLoad\AtmController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DBP\DBPReportController;
use App\Http\Controllers\Disbursement\DisbursementController;
use App\Http\Controllers\DrcrMemoController;
use App\Http\Controllers\Farmer\FarmerController;
use App\Http\Controllers\HelpCenterController;
use App\Http\Controllers\IdTypeController;
use App\Http\Controllers\ImageUploadController;
use App\Http\Controllers\InAddMoneyCebuanaController;
use App\Http\Controllers\InAddMoneyUpbDirectController;
use App\Http\Controllers\KYC\KYCController;
use App\Http\Controllers\Loan\LoanController;
use App\Http\Controllers\Log\LogHistoryController;
use App\Http\Controllers\Merchant\MerchantController;
use App\Http\Controllers\MunicipalityController;
use App\Http\Controllers\NewsAndUpdateController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PayBillsController;
use App\Http\Controllers\PayloadController;
use App\Http\Controllers\PreferredCashOutPartner\PreferredCashOutPartnerController;
use App\Http\Controllers\PrepaidLoadController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\Report\ReportController;
use App\Http\Controllers\Send2BankController;
use App\Http\Controllers\SendMoneyController;
use App\Http\Controllers\ServiceFeeController;
use App\Http\Controllers\Tier\TierApprovalCommentController;
use App\Http\Controllers\Tier\TierApprovalController;
use App\Http\Controllers\TierController;
use App\Http\Controllers\UBP\UBPAddmoneyController;
use App\Http\Controllers\UBP\UBPOAuthController;
use App\Http\Controllers\User\AdminUserController;
use App\Http\Controllers\User\ChangeKeyController;
use App\Http\Controllers\User\UserAccountController;
use App\Http\Controllers\User\UsersController;
use App\Http\Controllers\UserPhotoController;
use App\Http\Controllers\UserTransactionHistoryController;
use App\Http\Controllers\UserUtilities\CountryController;
use App\Http\Controllers\UserUtilities\CurrencyController;
use App\Http\Controllers\UserUtilities\MaritalStatusController;
use App\Http\Controllers\UserUtilities\NationalityController;
use App\Http\Controllers\UserUtilities\NatureOfWorkController;
use App\Http\Controllers\UserUtilities\SignupHostController;
use App\Http\Controllers\UserUtilities\SourceOfFundController;
use App\Http\Controllers\UserUtilities\TempUserDetailController;
use App\Http\Controllers\UserUtilities\UserProfileController;
use App\Http\Controllers\v2\Auth\AuthController as AuthV2Controller;
use App\Http\Controllers\v2\Auth\RegisterController as RegisterV2Controller;
use App\Http\Controllers\v2\UserUtilities\UserProfileController as UserProfileV2Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

if (App::environment(['local', 'staging', 'production'])) {
    Route::prefix('/utils')->group(function () {
        Route::post('/encrypt', [PayloadController::class, 'encrypt']);
        Route::post('/decrypt', [PayloadController::class, 'decrypt']);

        Route::post('/encrypt/fixed', [PayloadController::class, 'encryptFixed']);
        Route::post('/decrypt/fixed', [PayloadController::class, 'decryptFixed']);
    });

    Route::prefix('/atm')->group(function () {
        Route::post('/generate/signature', [AtmController::class, 'generate']);
        Route::post('/verify/signature', [AtmController::class, 'verify']);
        Route::get('/network-types', [AtmController::class, 'showPrefixNetworkList']);
    });
}

Route::post('token', [ClientController::class, 'getToken']);

Route::prefix('/clients')->middleware(['form-data'])->name('client.')->group(function () {
    Route::post('/token', [ClientController::class, 'getToken'])->name('get.token');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('/payloads')->name('payload.')->group(function () {
        Route::get('/generate', [PayloadController::class, 'generate'])->name('generate');
        Route::get('/{payload}/key', [PayloadController::class, 'getResponseKey'])->name('get.response.key');
    });

    Route::prefix('/image')->name('image')->group(function () {
        Route::post('/upload/{module}', [ImageUploadController::class, 'uploadImage'])->name('upload');
    });

    /**
     * ROUTES FOR AUTHENTICATION ENDPOINTS AS WELL AS
     * OTP VERIFICATIONS
     */
    Route::post('auth/user/verification', [UserPhotoController::class, 'createVerification']);
    Route::post('auth/user/selfie', [UserPhotoController::class, 'createSelfieVerification']);
    Route::post('auth/user/signature', [UserPhotoController::class, 'uploadSignature']);
    Route::get('auth/user/photo/{userPhotoId}', [UserPhotoController::class, 'getImageSignedUrl']);
    Route::post('user/change_avatar', [UserProfileController::class, 'changeAvatar']);
    // Admin manual ID and selfie upload
    Route::post('/admin/id/upload', [UserPhotoController::class, 'uploadIdManually']);
    Route::post('/admin/selfie/upload', [UserPhotoController::class, 'uploadSelfieManually']);
    // FARMER
    Route::middleware(['require.user.token'])->post('/farmer/batch-upload', [FarmerController::class, 'batchUpload']);
    Route::middleware(['require.user.token'])->post('/farmer/batch-upload/v2', [FarmerController::class, 'uploadFileToS3']);
    Route::middleware(['decrypt.request', 'auth:sanctum'])->post('/upload/process', [FarmerController::class, 'batchUploadV2']);
    Route::middleware(['require.user.token'])->post('/farmer/jobs/batch-upload', [FarmerController::class, 'processBatchUpload']);
    // EKYC
    Route::post('ekyc/face/auth', [KYCController::class, 'faceAuth'])->name('face.auth');

    Route::middleware(['require.user.token'])->post('/farmer/subsidy-batch-upload', [FarmerController::class, 'subsidyBatchUpload']);

    Route::middleware(['require.user.token'])->post('/farmer/batch-upload/v2/subsidy', [FarmerController::class, 'uploadSubsidyFileToS3']);


    Route::middleware(['require.user.token'])->post('/farmer/batch-upload/v3/subsidy', [FarmerController::class, 'uploadSubsidyFileToS3v3']);
    Route::middleware(['require.user.token', 'decrypt.request', 'auth:sanctum'])->post('/farmer/batch-upload/v3/subsidy/process', [FarmerController::class, 'processSubsidyV3']);

    Route::middleware(['require.user.token', 'decrypt.request'])->post('/farmer/batch-upload/v2/subsidy/process', [FarmerController::class, 'subsidyBatchUploadV2']);

    Route::middleware(['require.user.token'])->post('/farmer/id/verification', [FarmerController::class, 'farmerIdUpload']);
    Route::middleware(['require.user.token'])->post('/farmer/selfie/verification', [FarmerController::class, 'farmerSelfieUpload']);
    // Merchat Verification of Selfie
    Route::middleware(['require.user.token'])->post('/merchant/selfie/verification', [MerchantController::class, 'selfieVerification']);

    Route::middleware(['decrypt.request'])->prefix('ekyc')->group(function () {
        Route::post('face/match', [KYCController::class, 'initFaceMatch'])->name('face.match');
        Route::post('ocr', [KYCController::class, 'initOCR'])->name('ocr');
        Route::post('expiration/check', [KYCController::class, 'checkIDExpiration'])->name('expiration.check');
        Route::post('ocr/match', [KYCController::class, 'matchOCR'])->name('ocr.match');
        Route::get('verify/{requestId}', [KYCController::class, 'verifyRequest'])->name('verify');
    });

    Route::middleware(['decrypt.request'])->prefix('ecpay')->group(function () {
        Route::post('commitpayment', [AddMoneyController::class, 'commitPayment']);
        Route::post('confirmpayment', [AddMoneyController::class, 'confirmPayment']);
//        Route::get('batchconfirmpayment', [AddMoneyController::class, 'batchConfirmPayment']);
    });

    Route::prefix('/auth')->middleware(['decrypt.request'])->group(function () {
        Route::get('/user', [AuthController::class, 'getUser'])->name('user.show');

        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/mobile/login', [AuthController::class, 'mobileLogin']);
        Route::post('/admin/login', [AuthController::class, 'adminLogin']);
        Route::post('/partners/login', [AuthController::class, 'partnersLogin']);
        Route::post('/partners/verify-pin', [AuthController::class, 'onBorderLogin']);

        Route::post('/mobile/login/validate', [AuthController::class, 'mobileLoginValidate']);
        Route::post('/confirmation', [AuthController::class, 'confirmTransactions']);
        Route::post('/confirmation/password', [AuthController::class, 'passwordConfirmation']);

        Route::post('/register', [RegisterController::class, 'register']);
        Route::post('/register/validate/pin', [RegisterController::class, 'registerValidatePin']);
        Route::post('/register/validate', [RegisterController::class, 'registerValidate']);

        Route::post('/forgot/{keyType}', [ForgotKeyController::class, 'forgotKey']);
        Route::post('/reset/{keyType}', [ForgotKeyController::class, 'resetKey']);

        Route::post('/generate/otp', [AuthController::class, 'generateTransactionOTP']);
        Route::post('/resend/otp', [AuthController::class, 'resendOTP']);

        Route::prefix('/verify')->name('verify.')->group(function () {
            Route::post('/otp', [AuthController::class, 'verifyTransactionOtp'])->name('otp');
            Route::post('/account', [RegisterController::class, 'verifyAccount'])->name('account');
            Route::post('/mobile/login', [AuthController::class, 'verifyMobileLogin'])->name('mobile.login');
            Route::post('/partners/login', [AuthController::class, 'verifyPartnersLogin'])->name('partners.login');
            Route::post('/{keyType}', [ForgotKeyController::class, 'verifyKey'])->name('key.type');
        });
    });

    Route::prefix('/auth/v2')->middleware(['decrypt.request'])->group(function () {
        Route::get('/user', [AuthV2Controller::class, 'getUser'])->name('v2.user.show');

        Route::post('/register/validate', [RegisterV2Controller::class, 'registerValidate']);
        Route::post('/register', [RegisterV2Controller::class, 'register']);
        Route::post('/register/pin', [RegisterV2Controller::class, 'registerPin']);

        Route::post('/login', [AuthV2Controller::class, 'login']);
        Route::post('/mobile/login', [AuthV2Controller::class, 'mobileLogin']);
        Route::post('/admin/login', [AuthV2Controller::class, 'adminLogin']);
        Route::post('/partners/login', [AuthV2Controller::class, 'partnersLogin']);

        Route::post('/mobile/login/validate', [AuthV2Controller::class, 'mobileLoginValidate']);
        Route::post('/confirmation', [AuthV2Controller::class, 'confirmTransactions']);
        Route::post('/confirmation/password', [AuthV2Controller::class, 'passwordConfirmation']);

        Route::post('/forgot/{keyType}', [ForgotKeyController::class, 'forgotKey']);
        Route::post('/reset/{keyType}', [ForgotKeyController::class, 'resetKey']);

        Route::post('/generate/otp', [AuthV2Controller::class, 'generateTransactionOTP']);
        Route::post('/resend/otp', [AuthV2Controller::class, 'resendOTP']);


        // verify mobile login
        Route::prefix('/verify')->name('verify.')->group(function () {
            Route::post('/otp', [AuthV2Controller::class, 'verifyTransactionOtp'])->name('v2.otp');
            Route::post('/account', [RegisterV2Controller::class, 'verifyAccount'])->name('v2.account');
            Route::post('/mobile/login', [AuthV2Controller::class, 'verifyMobileLogin'])->name('v2.mobile.login');
            Route::post('/partners/login', [AuthV2Controller::class, 'verifyPartnersLogin'])->name('v2.partners.login');
            Route::post('/{keyType}', [ForgotKeyController::class, 'verifyKey'])->name('v2.key.type');
        });
    });

    Route::prefix('/auth/v3')->middleware(['decrypt.request'])->group(function () {
        Route::get('/user', [AuthV2Controller::class, 'getUser'])->name('v2.user.show');

        Route::post('/register/validate', [RegisterV2Controller::class, 'registerValidate']);
        Route::post('/register', [RegisterV2Controller::class, 'register']);
        Route::post('/register/pin', [RegisterV2Controller::class, 'registerPin']);

        Route::post('/login', [AuthV2Controller::class, 'login']);
        Route::post('/mobile/login', [AuthV2Controller::class, 'mobileLogin']);
        Route::post('/admin/login', [AuthV2Controller::class, 'adminLogin']);
        Route::post('/partners/login', [AuthV2Controller::class, 'partnersLogin']);

        Route::post('/mobile/login/validate', [AuthV2Controller::class, 'mobileLoginValidate']);
        Route::post('/confirmation', [AuthV2Controller::class, 'confirmTransactions']);
        Route::post('/confirmation/password', [AuthV2Controller::class, 'passwordConfirmation']);

        Route::post('/forgot/{keyType}', [ForgotKeyController::class, 'forgotKey']);
        Route::post('/reset/{keyType}', [ForgotKeyController::class, 'resetKey']);

        Route::post('/generate/otp', [AuthV2Controller::class, 'generateTransactionOTP']);
        Route::post('/resend/otp', [AuthV2Controller::class, 'resendOTP']);


        // verify mobile login
        Route::prefix('/verify')->name('verify.')->group(function () {
            Route::post('/otp', [AuthV2Controller::class, 'verifyTransactionOtp'])->name('v2.otp');
            Route::post('/account', [RegisterV2Controller::class, 'verifyAccount'])->name('v2.account');
            Route::post('/mobile/login', [AuthV2Controller::class, 'verifyMobileLogin'])->name('v2.mobile.login');
            Route::post('/partners/login', [AuthV2Controller::class, 'verifyPartnersLogin'])->name('v2.partners.login');
            Route::post('/{keyType}', [ForgotKeyController::class, 'verifyKey'])->name('v2.key.type');
        });
    });

    Route::prefix('/admin')->middleware(['decrypt.request'])->group(function () {
        Route::prefix('/users')->group(function () {
            Route::get('/', [AdminUserController::class, 'get']);
            Route::post('/', [AdminUserController::class, 'create']);

            Route::get('/{id}', [AdminUserController::class, 'getById']);
            Route::put('/{id}', [AdminUserController::class, 'update']);

            Route::delete('/{id}', [AdminUserController::class, 'delete']);
            Route::post('/search/byemail', [AdminUserController::class, 'getByEmail']);
            Route::post('/search/byname', [AdminUserController::class, 'getByName']);
        });
        Route::post('/photo/action', [UserPhotoController::class, 'takePhotoAction']);
        Route::post('/selfie/action', [UserPhotoController::class, 'takeSelfieAction']);
    });

    Route::prefix('/user')->middleware(['decrypt.request'])->group(function () {
        Route::post('/email/validate', [UserAccountController::class, 'validateEmail']);
        Route::post('/email/update', [UserAccountController::class, 'updateEmail']);

        Route::post('/mobile/validate', [UserAccountController::class, 'validateMobile']);
        Route::post('/mobile/update', [UserAccountController::class, 'updateMobile']);

        Route::post('/{keyType}/validate', [ChangeKeyController::class, 'validateKey'])->name('key.type.validate');
        Route::post('/{keyType}/validate/current', [ChangeKeyController::class, 'validateCurrentKey'])->name('key.type.validate.current');
        Route::post('/{keyType}/verify', [ChangeKeyController::class, 'verifyKey'])->name('key.type.verify');
        Route::put('/{keyType}', [ChangeKeyController::class, 'changeKey'])->name('key.type');
    });

    Route::prefix('/send2bank')->middleware(['decrypt.request'])->name('send.to.bank.')->group(function () {
        Route::get('/{provider}/banks', [Send2BankController::class, 'getBanks'])->name('provider.banks');
        Route::get('/{provider}/purposes', [Send2BankController::class, 'getPurposes'])->name('provider.purposes');
        Route::post('/{provider}/validate', [Send2BankController::class, 'validateFundTransfer'])->name('provider.validate');

        Route::get('/direct/ubp/update', [Send2BankController::class, 'verifyDirectTransactions'])->name('ubp.direct');
        Route::get('/process/pending', [Send2BankController::class, 'processPending'])->name('ubp.process.pending');
        Route::get('/process/all/pending', [Send2BankController::class, 'processUsersWithPending'])->name('ubp.process.pending.all');

        //Route::post('/direct/ubp', [Send2BankController::class, 'send2BankUBPDirect'])->name('direct.ubp');
       //Route::post('/validate/ubp', [Send2BankController::class, 'validateFundTransferDirectUBP'])->name('validate.ubp');
        Route::post('/{provider}', [Send2BankController::class, 'fundTransfer'])->name('provider');
       Route::post('/{provider}/transaction/update', [Send2BankController::class, 'updateTransaction'])->name('provider.transaction.update');
    });

    Route::prefix('/load')->middleware(['decrypt.request'])->name('load.')->group(function () {
        Route::post('/{network_type}', [PrepaidLoadController::class, 'load'])->name('load');
        Route::get('/promos/{network_type}', [PrepaidLoadController::class, 'showPromos'])->name('show.promos');
    });

    Route::prefix('/id')->middleware(['decrypt.request'])->name('id.')->group(function () {
        Route::apiResources([
            '/types' => IdTypeController::class,
        ]);
        Route::get('/farmers', [IdTypeController::class, 'farmersID']);
    });

    Route::middleware(['decrypt.request'])->group(function () {
        Route::apiResources([
            'news' => NewsAndUpdateController::class,
            'help_center' => HelpCenterController::class,
            'country' => CountryController::class,
            'currency' => CurrencyController::class,
            'marital_status' => MaritalStatusController::class,
            'nationality' => NationalityController::class,
            'nature_of_work' => NatureOfWorkController::class,
            'signup_host' => SignupHostController::class,
            'source_of_fund' => SourceOfFundController::class
        ]);

        Route::prefix('/user_accounts')->group(function () {
            Route::post('/', [UserAccountController::class, 'index']);
            Route::get('/{id}', [UserAccountController::class, 'show']);

            Route::post('/{id}/supervisorUpdateProfile', [UserProfileController::class, 'supervisorUpdateProfile']);
            Route::post('/{id}/updateProfile', [UserProfileController::class, 'updateProfile']);
            Route::post('/roles', [UserAccountController::class, 'setAccountRole']);
        });

        Route::prefix('/pending_user_updates')->group(function () {
            Route::get('/', [TempUserDetailController::class, 'index']);
            Route::get('/{id}', [TempUserDetailController::class, 'show']);
            Route::post('/{id}/update-status', [TempUserDetailController::class, 'updateStatus']);
        });

        Route::prefix('/user')->group(function () {
            Route::get('/profile', [UserProfileController::class, 'show']);
            Route::post('v2/profile/tobronze', [UserProfileV2Controller::class, 'updateBronze']);
            Route::post('/profile/tobronze', [UserProfileController::class, 'updateBronze']);
            Route::post('/profile/tosilver', [UserProfileController::class, 'updateSilver']);
            Route::post('/profile/tosilver/validation', [UserProfileController::class, 'updateSilverValidation']);
            Route::post('/profile/tosilver/check/pending', [UserProfileController::class, 'checkPendingTierUpgrate']);

            Route::get('/{mobileNumber}/avatar', [UserProfileController::class, 'getAvatarLinkByMobileNumber']);

            // FARMER
            Route::middleware(['require.user.token'])->post('/farmer/tosilver', [FarmerController::class, 'updateSilver']);
            Route::middleware(['require.user.token'])->post('/farmer/verification', [FarmerController::class, 'farmerVerification']);
            Route::middleware(['require.user.token'])->post('/farmer/verification/account-number', [FarmerController::class, 'farmerVerificationUserAccountNumberOnly']);
            Route::middleware(['require.user.token'])->post('/farmer/print', [ReportController::class, 'print']);
            Route::middleware(['require.user.token'])->post('/farmer/tosilver/manual-override', [UserProfileController::class, 'addDAPersonel']);
            Route::middleware(['require.user.token'])->post('/farmer/report', [FarmerController::class, 'report']);


            // TRANSACTION LOG HISTORY
            Route::get('/transaction/histories', [UserTransactionHistoryController::class, 'index']);
            Route::post('/transaction/histories', [UserTransactionHistoryController::class, 'transactionHistoryAdmin']);
            Route::post('/transaction/histories/download', [UserTransactionHistoryController::class, 'download']);
            Route::get('/transaction/histories/{id}', [UserTransactionHistoryController::class, 'show']);
            Route::post('/transaction/histories/count/total_amount/list', [UserTransactionHistoryController::class, 'countTotalAmountEachUser']);
            Route::post('/transaction/histories/count/pdf', [UserTransactionHistoryController::class, 'downloadCountTotalAmountEachUserPDF']);
            Route::post('/transaction/histories/count/csv', [UserTransactionHistoryController::class, 'downloadCountTotalAmountEachUserCSV']);
            Route::get('/log/history', [LogHistoryController::class, 'index']);

            Route::middleware(['require.user.token'])->get('/{user}/toggle/activation', [AdminUserController::class, 'toggleActivation']);
            Route::middleware(['require.user.token'])->get('/{user}/toggle/lockout', [AdminUserController::class, 'toggleLockout']);
        });


        Route::prefix('/buy/load')->name('buy.load.')->group(function () {
            Route::post('/', [AtmController::class, 'topupLoad'])->name('top.up.load');
            Route::post('/validate', [AtmController::class, 'validateTopup'])->name('validate.load.top.up');
            Route::post('/products', [AtmController::class, 'getProductsByProvider'])->name('get.products.by.provider');
            Route::get('/process/pending', [AtmController::class, 'processPending'])->name('process.pending');
        });

        Route::prefix('/buy/epins')->name('buy.epins.')->group(function () {
            Route::post('/', [AtmController::class, 'topupEPins'])->name('top.up.load');
            Route::post('/validate', [AtmController::class, 'validateTopup'])->name('validate.load.top.up');
            Route::get('/products', [AtmController::class, 'getEpinProducts'])->name('get.products.by.provider');
            Route::get('/process/pending', [AtmController::class, 'processPending'])->name('process.pending');
        });

        Route::prefix('/address')->group(function () {
            Route::get('/regions', [RegionController::class, 'index']);
            Route::post('/provinces', [ProvinceController::class, 'getProvinces']);
            Route::post('/municipalities', [MunicipalityController::class, 'getMunicipalities']);
            Route::post('/barangays', [BarangayController::class, 'getBarangays']);
        });

        Route::prefix('/bpi')->group(function () {
            Route::get('/credentials', [BPIController::class, 'getCredentials']);
            Route::post('/auth', [BPIController::class, 'bpiAuth']);
            Route::post('/accounts', [BPIController::class, 'getAccounts']);
            Route::post('/fundtopup', [BPIController::class, 'fundTopUp']);
            Route::post('/otp', [BPIController::class, 'otp']);
            Route::post('/process', [BPIController::class, 'process']);
            Route::post('/status', [BPIController::class, 'status']);
            Route::post('/login/url', [BPIController::class, 'getBPIAuthUrl']);
        });

        Route::prefix('/ubp')->group(function () {
            Route::get('/auth/generate', [UBPOAuthController::class, 'generateAuthorizeUrl']);
            Route::post('/auth/account/link', [UBPOAuthController::class, 'linkAccount']);
        });
    });

    Route::prefix('send/money')->middleware(['decrypt.request'])->group(function () {
        Route::post('/', [SendMoneyController::class, 'send']);
        Route::post('/validate', [SendMoneyController::class, 'sendValidate'])->name('send.validate');
        Route::post('/generate/qr', [SendMoneyController::class, 'generateQr'])->name('generate.qr');
        Route::get('/get/qr', [SendMoneyController::class, 'getQr'])->name('get.qr');
        Route::post('/scan/qr', [SendMoneyController::class, 'scanQr'])->name('scan.qr');
    });

    Route::prefix('pay/bills')->middleware(['decrypt.request'])->group(function () {
        Route::get('/', [PayBillsController::class, 'getBillers']);
        Route::get('/get/biller/information/{biller_code}', [PayBillsController::class, 'getBillerInformation']);
        Route::post('/validate/account/{biller_code}', [PayBillsController::class, 'validateAccount']);
        Route::post('/validate/account/{biller_code}/{account_number}', [PayBillsController::class, 'oldValidateAccount']);
        Route::post('/create/payment/{biller_code}', [PayBillsController::class, 'createPayment']);
        Route::get('/inquire/payment/{biller_code}/{client_reference}', [PayBillsController::class, 'inquirePayment']);
        Route::get('/get/wallet', [PayBillsController::class, 'getWalletBalance']);
        Route::get('/bayad/process/pending', [PayBillsController::class, 'processPending']);
        Route::get('/list/billers', [PayBillsController::class, 'getListOfBillers']);
        Route::get('/list/billers/pdf', [PayBillsController::class, 'downloadListOfBillersPDF']);
        Route::get('/list/billers/csv', [PayBillsController::class, 'downloadListOfBillersCSV']);
    });

    Route::prefix('disbursement/dbp')->middleware(['decrypt.request'])->group(function () {
        Route::post('/transaction', [DisbursementController::class, 'transaction']);
    });


    Route::prefix('/notifications')->middleware(['decrypt.request'])->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'GetAll'])->name('list');
        Route::post('/', [NotificationController::class, 'store'])->name('store');
        Route::get('/{notification}', [NotificationController::class, 'show'])->name('show');
        Route::put('/{notification}', [NotificationController::class, 'update'])->name('update');
        Route::delete('/{notification}', [NotificationController::class, 'delete'])->name('delete');
    });

    Route::prefix('/tiers/approval/comment')->middleware(['decrypt.request'])->group(function () {
        Route::get('/', [TierApprovalCommentController::class, 'list']);
        Route::post('/', [TierApprovalCommentController::class, 'create']);
    });

    Route::prefix('/tiers/approval')->middleware(['decrypt.request'])->group(function () {
        Route::post('/', [TierApprovalController::class, 'index']);
        Route::post('email', [TierApprovalController::class, 'sendEmail']);
        Route::post('sms', [TierApprovalController::class, 'sendSMS']);
        // Route::post('/', [TierApprovalController::class, 'store']);
        Route::get('/{tierApproval}', [TierApprovalController::class, 'show'])->name('show');
        Route::put('/{tierApproval}', [TierApprovalController::class, 'update'])->name('update');
        Route::delete('/{tierApproval}', [TierApprovalController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('/tiers')->middleware(['decrypt.request'])->name('tiers.')->group(function () {
        Route::get('/', [TierController::class, 'index'])->name('list');
        Route::post('/', [TierController::class, 'store'])->name('store');
        Route::get('/{tier}', [TierController::class, 'show'])->name('show');
        Route::put('/{tier}', [TierController::class, 'update'])->name('update');
        Route::delete('/{tier}', [TierController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('/service/fees')->middleware(['decrypt.request'])->group(function () {
        Route::get('/', [ServiceFeeController::class, 'index']);
        Route::post('/', [ServiceFeeController::class, 'store']);
        Route::get('/{serviceFee}', [ServiceFeeController::class, 'show']);
        Route::put('/{serviceFee}', [ServiceFeeController::class, 'update']);
        Route::delete('/{serviceFee}', [ServiceFeeController::class, 'destroy']);
    });

    Route::prefix('/cashin')->middleware(['decrypt.request'])->name('cashin.')->group(function () {
        Route::post('/', [AddMoneyController::class, 'addMoney'])->name('add.money');
        Route::post('/cancel', [AddMoneyController::class, 'cancel'])->name('cancel');
        Route::post('/status', [AddMoneyController::class, 'getStatus'])->name('get.status');
        Route::get('/latest/pending', [AddMoneyController::class, 'getLatestPendingTrans'])->name('get.latest.pending.transactions');
        Route::post('/update/transactions', [AddMoneyController::class, 'updateUserTrans'])->name('update.user.transactions');

        Route::post('/ubp', [UBPAddmoneyController::class, 'addMoney']);
        Route::get('/ubp/process/pending', [UBPAddmoneyController::class, 'processPending']);
    });

    Route::prefix('/dashboard')->middleware(['decrypt.request'])->group(function () {
        Route::get('/', [DashboardController::class, 'index']);
        Route::get('/overview', [DashboardController::class, 'getDashboard2022']);
        Route::get('/transactions/daily', [DashboardController::class, 'getTransactionCountDaily']);
        Route::get('/transactions/monthly', [DashboardController::class, 'getTransactionCountMonthly']);
        Route::get('/transactions/weekly', [DashboardController::class, 'getTransactionWeekly']);
        Route::get('/signups/daily', [DashboardController::class, 'getDailySignups']);
        Route::get('/signups/monthly', [DashboardController::class, 'getMonthlySignups']);
        Route::get('/signups/weekly', [DashboardController::class, 'getWeeklySignups']);
        Route::get('currencies/rates', [DashboardController::class, 'getForeignCurrencyRates']);
    });

    Route::prefix('/admin')->middleware(['decrypt.request'])->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index']);
    });

    Route::prefix('/admin')->middleware(['decrypt.request'])->group(function () {
        Route::get('/mytask', [MyTaskController::class, 'index']);
    });

    Route::post('drcr/memos/batch', [DrcrMemoController::class, 'batchUpload']);
    Route::prefix('drcr/memos')->middleware(['decrypt.request'])->group(function () {
        Route::get('/index/{status}', [DrcrMemoController::class, 'index']);
        Route::get('/show/all/{status}', [DrcrMemoController::class, 'showAll']);
        Route::post('', [DrcrMemoController::class, 'store']);
        Route::get('/show/{referenceNumber}', [DrcrMemoController::class, 'show']);
        Route::get('/get/user/{accountNumber}', [DrcrMemoController::class, 'getUser']);
        Route::put('/update/memo', [DrcrMemoController::class, 'updateMemo']);
        Route::put('/approval', [DrcrMemoController::class, 'approval']);

        Route::post('/report', [DrcrMemoController::class, 'report']);

        Route::post('/report/filter', [DrcrMemoController::class, 'reportFiltered']);
        Route::post('/report/filter/pending/peruser', [DrcrMemoController::class, 'reportFilteredPending']);
        Route::post('/report/filter/pending/all', [DrcrMemoController::class, 'reportFilteredPerUser']);

        Route::post('/report/filter/run/peruser', [DrcrMemoController::class, 'updatedReportFilteredPerUser']);
        Route::post('/report/filter/run/all', [DrcrMemoController::class, 'updatedReportFilteredAll']);
    });

    Route::prefix('/cashin')->middleware(['decrypt.request'])->group(function () {
        Route::post('/postback', [AddMoneyController::class, 'postBack']);
    });

    // ADMIN
    Route::prefix('/admin/roles')->middleware(['decrypt.request'])->name('roles.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('list');
        Route::post('/', [RoleController::class, 'store'])->name('store');
        Route::get('/{role}', [RoleController::class, 'show'])->name('show.role');
        Route::get('/user/{role}', [RoleController::class, 'getUserRolesAndPermissionByUserAccountId'])->name('show.user.role');
        Route::put('/{role}', [RoleController::class, 'update'])->name('update');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('/admin/permissions')->middleware(['decrypt.request'])->name('permissions.')->group(function () {
        Route::get('/', [RoleController::class, 'rolePermissions'])->name('list');
        Route::post('/', [RoleController::class, 'setRolePermission'])->name('store');
    });

    Route::prefix('/report')->middleware(['decrypt.request'])->group(function () {
        Route::post('/biller', [ReportController::class, 'billerReport']);
        Route::post('/farmers/drcr', [ReportController::class, 'DRCRMemoFarmers']);
        Route::post('/farmers/transaction', [ReportController::class, 'TransactionReportFarmers']);
        Route::post('/farmers/list', [ReportController::class, 'FarmersList']);
    });

    Route::prefix('/s3')->middleware(['decrypt.request'])->group(function () {
        Route::post('/link', [ReportController::class, 'generateS3Link']);
    });

    Route::prefix('/loans')->middleware(['decrypt.request'])->group(function () {
        Route::get('/get/reference_number', [LoanController::class, 'generateReferenceNumber']);
        Route::post('/reference_number', [LoanController::class, 'storeReferenceNumber']);
    });

    Route::prefix('/cebuana')->middleware(['decrypt.request'])->group(function () {
        Route::post('/add/money', [InAddMoneyCebuanaController::class, 'addMoney']);

        Route::get('/generate', [InAddMoneyCebuanaController::class, 'generate']);
        Route::middleware(['whitelist.request'])->post('/submit', [InAddMoneyCebuanaController::class, 'submit']);
    });

    Route::prefix('/upb/add/money')->middleware(['decrypt.request'])->group(function () {
        Route::post('/oauth/redirect', [InAddMoneyUpbDirectController::class, 'addMoney']);
    });

    // MERCHANT
    Route::prefix('/merchant')->middleware(['decrypt.request'])->group(function () {
        Route::post('/list', [MerchantController::class, 'list']);
        Route::post('/toggle/active', [MerchantController::class, 'toggleMerchantStatus']);
        Route::post('/verify', [MerchantController::class, 'verifyMerchant']);
        Route::get('/{id}/documents', [MerchantController::class, 'showDocument']);
        Route::post('/documents/update/status', [MerchantController::class, 'updateDocumentStatus']);
        Route::get('/documents/update/status', [MerchantController::class, 'updateDocumentStatus']);

        Route::post('/pay', [MerchantController::class, 'pay']);
        Route::get('/pay/{refNo}', [MerchantController::class, 'getByRefNo']);
    });

    Route::prefix('/preferred/cashout/partner')->middleware(['decrypt.request'])->group(function () {
        Route::get('/list', [PreferredCashOutPartnerController::class, 'list']);
        Route::post('/store', [PreferredCashOutPartnerController::class, 'store']);
    });

    Route::prefix('/dbp/reports')->middleware(['decrypt.request'])->group(function () {
        Route::post('/customer/list', [DBPReportController::class, 'customerList']);
        Route::post('/disbursement', [DBPReportController::class, 'disbursement']);
        Route::post('/memo', [DBPReportController::class, 'memo']);
        Route::post('/onboarding', [DBPReportController::class, 'onBoarding']);
        Route::post('/transaction/histories', [DBPReportController::class, 'transactionHistories']);
        Route::post('/claims', [DBPReportController::class, 'claims']);
    });

    Route::prefix('/merchant/accounts')->middleware(['decrypt.request'])->group(function () {
        Route::get('/list', [MerchantController::class, 'listMerchantAccount']);
        Route::post('/store', [MerchantController::class, 'storeMerchant']);
        Route::post('/store/user', [MerchantController::class, 'storeMerchantAccount']);
        Route::post('/update', [MerchantController::class, 'updateMerchantAccount']);
        Route::post('/set/user', [MerchantController::class, 'setUserMerchantAccount']);
    });

    Route::prefix('/transactions')->middleware(['decrypt.request'])->group(function () {
        Route::post('/generate', [UserTransactionHistoryController::class, 'generateTransactionHistory']);
    });

    Route::prefix('/users')->middleware(['decrypt.request'])->group(function () {
        Route::get('/{userId}/balance', [UsersController::class, 'getBalanceInfo']);
    });
});

Route::prefix('/cashin')->middleware(['decrypt.request'])->group(function () {
    Route::get('/postback', [AddMoneyController::class, 'postBack']);
});
Route::post('/hv/callback', [KYCController::class, 'callback']);
