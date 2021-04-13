<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\IdTypeController;
use App\Http\Controllers\PayloadController;
use App\Http\Controllers\PrepaidLoadController;
use App\Http\Controllers\SendMoneyController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NewsAndUpdateController;
use App\Http\Controllers\HelpCenterController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserPhotoController;
use Illuminate\Support\Facades\App;

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
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/forgot/password', [AuthController::class, 'forgotPassword']);
        Route::post('/reset/password', [AuthController::class, 'resetPassword']);

        Route::prefix('/verify')->group(function () {
            Route::post('/account', [AuthController::class, 'verifyAccount']);
            Route::post('/login', [AuthController::class, 'verifyLogin']);
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
    
    Route::prefix('/help_center')->middleware(['decrypt.request'])->group(function (){
        Route::get('/', [HelpCenterController::class, 'GetAll']);
        Route::post('/', [HelpCenterController::class, 'create']);
        Route::get('/{helpCenter}', [HelpCenterController::class, 'show']);
        Route::put('/{helpCenter}', [HelpCenterController::class, 'update']);
        Route::delete('/{helpCenter}', [HelpCenterController::class, 'delete']);
    });
    Route::middleware(['decrypt.request'])->group(function () {
        Route::apiResources([
            'news' => NewsAndUpdateController::class,
            'help_center' => HelpCenterController::class,
        ]);
    });

    Route::prefix('send/money')->group(function () {
        Route::post('/', [SendMoneyController::class, 'send']);
        Route::post('/generate/qr', [SendMoneyController::class, 'generateQr']);
    });
    
    Route::middleware(['decrypt.request'])->prefix('/notifications')->group(function (){
        Route::get('/', [NotificationController::class, 'GetAll']);
        Route::post('/', [NotificationController::class, 'create']);
        Route::get('/{notification}', [NotificationController::class, 'show']);
        Route::put('/{notification}', [NotificationController::class, 'update']);
        Route::delete('/{notification}', [NotificationController::class, 'delete']);
    });
});

    