<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TierController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\IdTypeController;
use App\Http\Controllers\PayloadController;
use App\Http\Controllers\SendMoneyController;
use App\Http\Controllers\UserPhotoController;
use App\Http\Controllers\HelpCenterController;
use App\Http\Controllers\PrepaidLoadController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\NewsAndUpdateController;
use App\Http\Controllers\ServiceFeeController;
use App\Http\Controllers\UserUtilities\CountryController;
use App\Http\Controllers\UserUtilities\CurrencyController;
use App\Http\Controllers\UserUtilities\SignupHostController;
use App\Http\Controllers\UserUtilities\NationalityController;
use App\Http\Controllers\UserUtilities\UserProfileController;
use App\Http\Controllers\UserUtilities\NatureOfWorkController;
use App\Http\Controllers\UserUtilities\SourceOfFundController;
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
}

Route::prefix('/clients')->middleware(['form-data'])->group(function () {
    Route::post('/token', [ClientController::class, 'getToken']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('/payloads')->group(function () {
        Route::get('/generate', [PayloadController::class, 'generate']);
        Route::get('/{payload}/key', [PayloadController::class, 'getResponseKey']);
    });

    /**
     * ROUTES FOR AUTHENTICATION ENDPOINTS AS WELL AS
     * OTP VERIFICATIONS
     */
    Route::prefix('/auth')->middleware(['decrypt.request'])->group(function () {
        Route::get('/user', [AuthController::class, 'getUser']);
        Route::post('/user/verification', [UserPhotoController::class, 'createVerification']);

        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/mobile/login', [AuthController::class, 'mobileLogin']);
        Route::post('/mobile/login/validate', [AuthController::class, 'mobileLoginValidate']);

        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/register/validate', [AuthController::class, 'registerValidate']);

        Route::post('/forgot/password', [AuthController::class, 'forgotPassword']);
        Route::post('/reset/password', [AuthController::class, 'resetPassword']);

        Route::post('/resend/otp', [AuthController::class, 'resendOTP']);

        Route::prefix('/verify')->group(function () {
            Route::post('/account', [AuthController::class, 'verifyAccount']);
            Route::post('/mobile/login', [AuthController::class, 'verifyMobileLogin']);
            Route::post('/password', [AuthController::class, 'verifyPassword']);
        });
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

        Route::prefix('/user')->group(function (){
            Route::get('/profile/{user_detail}', [UserProfileController::class, 'show']);
            Route::post('/profile', [UserProfileController::class, 'update']);
        });
    });

    Route::prefix('send/money')->group(function () {
        Route::post('/', [SendMoneyController::class, 'send']);
        Route::post('/generate/qr', [SendMoneyController::class, 'generateQr']);
    });

    Route::middleware(['decrypt.request'])->prefix('/notifications')->group(function (){
        Route::get('/', [NotificationController::class, 'GetAll']);
        Route::post('/', [NotificationController::class, 'store']);
        Route::get('/{notification}', [NotificationController::class, 'show']);
        Route::put('/{notification}', [NotificationController::class, 'update']);
        Route::delete('/{notification}', [NotificationController::class, 'delete']);
    });

    Route::middleware(['decrypt.request'])->prefix('/tiers')->group(function (){
        Route::get('/', [TierController::class, 'index']);
        Route::post('/', [TierController::class, 'store']);
        Route::get('/{tier}', [TierController::class, 'show']);
        Route::put('/{tier}', [TierController::class, 'update']);
        Route::delete('/{tier}', [TierController::class, 'destroy']);
    });

    Route::middleware(['decrypt.request'])->prefix('/service/fees')->group(function (){
        Route::get('/', [ServiceFeeController::class, 'index']);
        Route::post('/', [ServiceFeeController::class, 'store']);
        Route::get('/{serviceFee}', [ServiceFeeController::class, 'show']);
        Route::put('/{serviceFee}', [ServiceFeeController::class, 'update']);
        Route::delete('/{serviceFee}', [ServiceFeeController::class, 'destroy']);
    });
});


