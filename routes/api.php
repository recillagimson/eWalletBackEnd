<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TierController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\IdTypeController;
use App\Http\Controllers\PayloadController;
use App\Http\Controllers\AddMoneyController;
use App\Http\Controllers\PayBillsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Send2BankController;
use App\Http\Controllers\SendMoneyController;
use App\Http\Controllers\UserPhotoController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\HelpCenterController;
use App\Http\Controllers\ServiceFeeController;
use App\Http\Controllers\BuyLoad\AtmController;
use App\Http\Controllers\ImageUploadController;
use App\Http\Controllers\PrepaidLoadController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\NewsAndUpdateController;
use App\Http\Controllers\Auth\ForgotKeyController;
use App\Http\Controllers\User\AdminUserController;
use App\Http\Controllers\User\ChangeKeyController;
use App\Http\Controllers\User\UserAccountController;
use App\Http\Controllers\Tier\TierApprovalController;
use App\Http\Controllers\UserUtilities\CountryController;
use App\Http\Controllers\UserTransactionHistoryController;
use App\Http\Controllers\UserUtilities\CurrencyController;
use App\Http\Controllers\Tier\TierApprovalCommentController;
use App\Http\Controllers\UserUtilities\SignupHostController;
use App\Http\Controllers\UserUtilities\NationalityController;
use App\Http\Controllers\UserUtilities\UserProfileController;
use App\Http\Controllers\UserUtilities\NatureOfWorkController;
use App\Http\Controllers\UserUtilities\SourceOfFundController;
use App\Http\Controllers\UserUtilities\UserProfileController;
use App\Http\Controllers\UserUtilities\TempUserDetailController;
use App\Http\Controllers\UserUtilities\MaritalStatusController;

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

if (App::environment('local')) {
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
    Route::get('auth/user/photo/{userPhotoId}', [UserPhotoController::class, 'getImageSignedUrl']);
    Route::post('user/change_avatar', [UserProfileController::class, 'changeAvatar']);
    // Admin manual ID and selfie upload
    Route::post('/admin/id/upload', [UserPhotoController::class, 'uploadIdManually']);
    Route::post('/admin/selfie/upload', [UserPhotoController::class, 'uploadSelfieManually']);

    Route::prefix('/auth')->middleware(['decrypt.request'])->group(function () {
        Route::get('/user', [AuthController::class, 'getUser'])->name('user.show');

        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/mobile/login', [AuthController::class, 'mobileLogin']);
        Route::post('/mobile/login/validate', [AuthController::class, 'mobileLoginValidate']);
        Route::post('/confirmation', [AuthController::class, 'confirmTransactions']);

        Route::post('/register', [RegisterController::class, 'register']);
        Route::post('/register/validate', [RegisterController::class, 'registerValidate']);

        Route::post('/forgot/{keyType}', [ForgotKeyController::class, 'forgotKey']);
        Route::post('/reset/{keyType}', [ForgotKeyController::class, 'resetKey']);

        Route::post('/generate/otp', [AuthController::class, 'generateTransactionOTP']);
        Route::post('/resend/otp', [AuthController::class, 'resendOTP']);

        Route::prefix('/verify')->name('verify.')->group(function () {
            Route::post('/otp', [AuthController::class, 'verifyTransactionOtp'])->name('otp');
            Route::post('/account', [RegisterController::class, 'verifyAccount'])->name('account');
            Route::post('/mobile/login', [AuthController::class, 'verifyMobileLogin'])->name('mobile.login');
            Route::post('/{keyType}', [ForgotKeyController::class, 'verifyKey'])->name('key.type');
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
        Route::post('/{keyType}/verify', [ChangeKeyController::class, 'verifyKey'])->name('key.type.verify');
        Route::put('/{keyType}', [ChangeKeyController::class, 'changeKey'])->name('key.type');
    });

    Route::prefix('/send2bank')->middleware(['decrypt.request'])->name('send.to.bank.')->group(function () {
        Route::get('/{provider}/banks', [Send2BankController::class, 'getBanks'])->name('provider.banks');
        Route::get('/{provider}/purposes', [Send2BankController::class, 'getPurposes'])->name('provider.purposes');
        Route::post('/{provider}/validate', [Send2BankController::class, 'validateFundTransfer'])->name('provider.validate');

        Route::get('/direct/ubp/update', [Send2BankController::class, 'verifyDirectTransactions'])->name('ubp.direct');
        Route::get('/process/pending', [Send2BankController::class, 'processPending'])->name('ubp.process.pending');

        Route::post('/direct/ubp', [Send2BankController::class, 'send2BankUBPDirect'])->name('direct.ubp');
        Route::post('/validate/ubp', [Send2BankController::class, 'validateFundTransferDirectUBP'])->name('validate.ubp');
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
            'source_of_fund' => SourceOfFundController::class,
        ]);

        Route::prefix('/user_accounts')->group(function (){
            Route::get('/', [UserAccountController::class, 'index']);
            Route::get('/{id}', [UserAccountController::class, 'show']);
            
            Route::post('/{userAccount}/updateProfile', [UserProfileController::class, 'updateProfile']);
            Route::post('/roles', [UserAccountController::class, 'setAccountRole']);
        });

        Route::prefix('/pending_user_updates')->group(function (){
            Route::get('/', [TempUserDetailController::class, 'index']);
            Route::get('/{id}', [TempUserDetailController::class, 'show']);
            Route::post('/{id}/update-status', [TempUserDetailController::class, 'updateStatus']);
        });

        Route::prefix('/user')->group(function (){
            Route::get('/profile', [UserProfileController::class, 'show']);
            Route::post('/profile/tobronze', [UserProfileController::class, 'updateBronze']);
            Route::post('/profile/tosilver', [UserProfileController::class, 'updateSilver']);
            Route::post('/profile/tosilver/validation', [UserProfileController::class, 'updateSilverValidation']);
            Route::post('/profile/tosilver/check/pending', [UserProfileController::class, 'checkPendingTierUpgrate']);

            // TRANSACTION LOG HISTORY
            Route::get('/transaction/histories', [UserTransactionHistoryController::class, 'index']);
            Route::post('/transaction/histories/download', [UserTransactionHistoryController::class, 'download']);
            Route::get('/transaction/histories/{id}', [UserTransactionHistoryController::class, 'show']);

        });

        Route::prefix('/buy/load')->name('buy.load.')->group(function () {
            Route::post('/', [AtmController::class, 'topupLoad'])->name('top.up.load');
            Route::post('/validate', [AtmController::class, 'validateLoadTopup'])->name('validate.load.top.up');
            Route::post('/products', [AtmController::class, 'getProductsByProvider'])->name('get.products.by.provider');
            Route::get('/process/pending', [AtmController::class, 'processPending'])->name('process.pending');
        });

    });

    Route::prefix('send/money')->middleware(['decrypt.request'])->name('send.money')->group(function () {
        Route::post('/', [SendMoneyController::class, 'send']);
        Route::post('/validate', [SendMoneyController::class, 'sendValidate'])->name('send.validate');
        Route::post('/generate/qr', [SendMoneyController::class, 'generateQr'])->name('generate.qr');
        Route::post('/scan/qr', [SendMoneyController::class, 'scanQr'])->name('scan.qr');
    });

    Route::prefix('pay/bills')->middleware(['decrypt.request'])->group(function () {
        Route::get('/', [PayBillsController::class, 'getBillers']);
        Route::get('/get/biller/information/{biller_code}', [PayBillsController::class, 'getBillerInformation']);
        Route::post('/validate/account/{biller_code}/{account_number}', [PayBillsController::class, 'validateAccount']);
        Route::post('/create/payment/{biller_code}', [PayBillsController::class, 'createPayment']);
        Route::get('/inquire/payment/{biller_code}/{client_reference}', [PayBillsController::class, 'inquirePayment']);
        Route::get('/get/wallet', [PayBillsController::class, 'getWalletBalance']);
        Route::get('/bayad/process/pending', [PayBillsController::class, 'processPending']);
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

    Route::prefix('/tiers/approval')->middleware(['decrypt.request', 'rba'])->group(function () {
        Route::get('/', [TierApprovalController::class, 'index']);
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

    Route::prefix('/cashin')->middleware(['decrypt.request'])->name('cashin.')->group(function (){
        Route::post('/', [AddMoneyController::class, 'addMoney'])->name('add.money');
        Route::post('/cancel', [AddMoneyController::class, 'cancel'])->name('cancel');
        Route::post('/status', [AddMoneyController::class, 'getStatus'])->name('get.status');
        Route::get('/latest/pending', [AddMoneyController::class, 'getLatestPendingTrans'])->name('get.latest.pending.transactions');
        Route::post('/update/transactions', [AddMoneyController::class, 'updateUserTrans'])->name('update.user.transactions');
    });

    Route::prefix('/dashboard')->middleware(['decrypt.request'])->group(function(){
        Route::get('/', [DashboardController::class, 'index']);
    });


    // ADMIN
    Route::prefix('/admin/roles')->middleware(['decrypt.request'])->name('roles.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('list');
        Route::post('/', [RoleController::class, 'store'])->name('store');
        Route::get('/{role}', [RoleController::class, 'show'])->name('show');
        Route::put('/{role}', [RoleController::class, 'update'])->name('update');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');

    });
    
    Route::prefix('/admin/permissions')->middleware(['decrypt.request'])->name('permissions.')->group(function() {
        Route::get('/', [RoleController::class, 'rolePermissions'])->name('list');
        Route::post('/', [RoleController::class, 'setRolePermission'])->name('store');
    });
    

});


// DragonPay PostBack
Route::prefix('/cashin')->group(function (){
    Route::get('/postback', [AddMoneyController::class, 'postBack']);
});


