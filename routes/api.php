<?php

use App\Http\Controllers\AddMoneyController;
use App\Http\Controllers\Auth\ForgotKeyController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BuyLoad\AtmController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HelpCenterController;
use App\Http\Controllers\IdTypeController;
use App\Http\Controllers\ImageUploadController;
use App\Http\Controllers\NewsAndUpdateController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PayBillsController;
use App\Http\Controllers\PayloadController;
use App\Http\Controllers\PrepaidLoadController;
use App\Http\Controllers\Send2BankController;
use App\Http\Controllers\SendMoneyController;
use App\Http\Controllers\ServiceFeeController;
use App\Http\Controllers\Tier\TierApprovalController;
use App\Http\Controllers\TierController;
use App\Http\Controllers\User\ChangeKeyController;
use App\Http\Controllers\User\UserAccountController;
use App\Http\Controllers\UserPhotoController;
use App\Http\Controllers\UserTransactionHistoryController;
use App\Http\Controllers\UserUtilities\CountryController;
use App\Http\Controllers\UserUtilities\CurrencyController;
use App\Http\Controllers\UserUtilities\MaritalStatusController;
use App\Http\Controllers\UserUtilities\NationalityController;
use App\Http\Controllers\UserUtilities\NatureOfWorkController;
use App\Http\Controllers\UserUtilities\SignupHostController;
use App\Http\Controllers\UserUtilities\SourceOfFundController;
use App\Http\Controllers\UserUtilities\UserProfileController;
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

Route::prefix('/clients')->middleware(['form-data'])->group(function () {
    Route::post('/token', [ClientController::class, 'getToken']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('/payloads')->group(function () {
        Route::get('/generate', [PayloadController::class, 'generate']);
        Route::get('/{payload}/key', [PayloadController::class, 'getResponseKey']);
    });

    Route::prefix('/image')->group(function () {
        Route::post('/upload/{module}', [ImageUploadController::class, 'uploadImage']);
    });

    /**
     * ROUTES FOR AUTHENTICATION ENDPOINTS AS WELL AS
     * OTP VERIFICATIONS
     */
    Route::post('auth/user/verification', [UserPhotoController::class, 'createVerification']);
    Route::post('auth/user/selfie', [UserPhotoController::class, 'createSelfieVerification']);
    Route::post('user/change_avatar', [UserProfileController::class, 'changeAvatar']);

    Route::prefix('/auth')->middleware(['decrypt.request'])->group(function () {
        Route::get('/user', [AuthController::class, 'getUser']);

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

        Route::prefix('/verify')->group(function () {
            Route::post('/otp', [AuthController::class, 'verifyTransactionOtp']);
            Route::post('/account', [RegisterController::class, 'verifyAccount']);
            Route::post('/mobile/login', [AuthController::class, 'verifyMobileLogin']);
            Route::post('/{keyType}', [ForgotKeyController::class, 'verifyKey']);
        });
    });

    Route::prefix('/user')->middleware(['decrypt.request'])->group(function () {
        Route::post('/email/validate', [UserAccountController::class, 'validateEmail']);
        Route::post('/email/update', [UserAccountController::class, 'updateEmail']);
        
        Route::post('/mobile/validate', [UserAccountController::class, 'validateMobile']);
        Route::post('/mobile/update', [UserAccountController::class, 'updateMobile']);

        Route::post('/{keyType}/validate', [ChangeKeyController::class, 'validateKey']);
        Route::post('/{keyType}/verify', [ChangeKeyController::class, 'verifyKey']);
        Route::put('/{keyType}', [ChangeKeyController::class, 'changeKey']);
    });

    Route::prefix('/send2bank')->middleware(['decrypt.request'])->group(function () {
        Route::get('/{provider}/banks', [Send2BankController::class, 'getBanks']);
        Route::get('/{provider}/purposes', [Send2BankController::class, 'getPurposes']);
        Route::post('/{provider}/validate', [Send2BankController::class, 'validateFundTransfer']);

        Route::get('/direct/ubp/update', [Send2BankController::class, 'verifyDirectTransactions']);
        Route::get('/process/pending', [Send2BankController::class, 'processPending']);

        Route::post('/direct/ubp', [Send2BankController::class, 'send2BankUBPDirect']);
        Route::post('/validate/ubp', [Send2BankController::class, 'validateFundTransferDirectUBP']);
        Route::post('/{provider}', [Send2BankController::class, 'fundTransfer']);
        Route::post('/{provider}/transaction/update', [Send2BankController::class, 'updateTransaction']);
    });

    Route::prefix('/load')->middleware(['decrypt.request'])->group(function () {
        Route::post('/{network_type}', [PrepaidLoadController::class, 'load']);
        Route::get('/promos/{network_type}', [PrepaidLoadController::class, 'showPromos']);
    });

    Route::prefix('/id')->middleware(['decrypt.request'])->group(function () {
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

        Route::prefix('/user')->name('user.')->group(function (){
            Route::get('/profile', [UserProfileController::class, 'show'])->name('show');
            Route::post('/profile/tobronze', [UserProfileController::class, 'updateBronze'])->name('update.to.bronze');
            Route::post('/profile/tosilver', [UserProfileController::class, 'updateSilver'])->name('update.to.silver');

            // TRANSACTION LOG HISTORY
            Route::get('/transaction/histories', [UserTransactionHistoryController::class, 'index'])->name('transactions.list');
            Route::get('/transaction/histories/{id}', [UserTransactionHistoryController::class, 'show'])->name('transactions.show');

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

    Route::prefix('pay/bills')->middleware(['decrypt.request'])->name('pay.bills.')->group(function () {
        Route::get('/', [PayBillsController::class, 'getBillers'])->name('get.billers');
        Route::get('/get/biller/information/{biller_code}', [PayBillsController::class, 'getBillerInformation'])->name('get.biller.information');
        Route::get('/get/required/fields/{biller_code}', [PayBillsController::class, 'getRequiredFields'])->name('get.required.fields');
        Route::get('/get/other/charges/{biller_code}', [PayBillsController::class, 'getOtherCharges'])->name('get.other.charges');
        Route::post('/verify/account/{biller_code}/{account_number}', [PayBillsController::class, 'verifyAccount'])->name('verify.account');
        Route::post('/create/payment/{biller_code}', [PayBillsController::class, 'createPayment'])->name('create.payment');
        Route::get('/inquire/payment/{biller_code}/{client_reference}', [PayBillsController::class, 'inquirePayment'])->name('inquire.payment');
        Route::get('/get/wallet', [PayBillsController::class, 'getWalletBalance'])->name('get.wallet.balance');
    });

    Route::prefix('/notifications')->middleware(['decrypt.request'])->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'GetAll'])->name('list');
        Route::post('/', [NotificationController::class, 'store'])->name('store');
        Route::get('/{notification}', [NotificationController::class, 'show'])->name('show');
        Route::put('/{notification}', [NotificationController::class, 'update'])->name('update');
        Route::delete('/{notification}', [NotificationController::class, 'delete'])->name('delete');
    });

    Route::prefix('/tiers/approval')->middleware(['decrypt.request'])->name('tiers.approval.')->group(function () {
        Route::get('/', [TierApprovalController::class, 'index'])->name('list');
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


    Route::prefix('/service/fees')->middleware(['decrypt.request'])->name('service.fees')->group(function () {
        Route::get('/', [ServiceFeeController::class, 'index'])->name('list');
        Route::post('/', [ServiceFeeController::class, 'store'])->name('store');
        Route::get('/{serviceFee}', [ServiceFeeController::class, 'show'])->name('show');
        Route::put('/{serviceFee}', [ServiceFeeController::class, 'update'])->name('update');
        Route::delete('/{serviceFee}', [ServiceFeeController::class, 'destroy'])->name('destroy');
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

});


// DragonPay PostBack
Route::prefix('/cashin')->group(function (){
    Route::get('/postback', [AddMoneyController::class, 'postBack']);
});


