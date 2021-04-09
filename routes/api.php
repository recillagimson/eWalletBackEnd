<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PayloadController;
use App\Http\Controllers\PrepaidLoadController;
use App\Http\Controllers\NewsAndUpdateController;
use App\Http\Controllers\HelpCenterController;
use App\Http\Controllers\IdTypeController;
use App\Http\Controllers\UserPhotoController;
use Illuminate\Support\Facades\Route;
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

if(App::environment('local'))
{
    Route::prefix('/utils')->group(function(){
        Route::post('/encrypt', [PayloadController::class, 'encrypt']);
        Route::post('/decrypt', [PayloadController::class, 'decrypt']);

        Route::post('/encrypt/fixed', [PayloadController::class, 'encryptFixed']);
        Route::post('/decrypt/fixed', [PayloadController::class, 'decryptFixed']);
    });
}

Route::prefix('/clients')->middleware(['form-data'])->group(function (){
    Route::post('/token', [ClientController::class, 'getToken']);
});

Route::middleware('auth:sanctum')->group(function (){
    Route::prefix('/payloads')->group(function() {
        Route::get('/generate', [PayloadController::class, 'generate']);
        Route::get('/{payload}/key', [PayloadController::class, 'getResponseKey']);
    });

    Route::prefix('/auth')->middleware('auth:sanctum')->group(function (){
        Route::get('/user', [AuthController::class, 'getUser']);

        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/forgot/password', [AuthController::class, 'forgotPassword']);
        Route::post('/reset/password', [AuthController::class, 'resetPassword']);
        Route::post('/verify', [AuthController::class, 'verify']);

        // Verification Route
        Route::post('/user/verification', [UserPhotoController::class, 'createVerification']);

    });
    Route::prefix('/load')->middleware(['decrypt.request'])->group(function (){
        Route::post('/{network_type}', [PrepaidLoadController::class, 'load']);
        Route::get('/promos/{network_type}', [PrepaidLoadController::class, 'showPromos']);
    });

    Route::prefix('/id')->middleware(['decrypt.request'])->group(function (){
        Route::apiResources([
            '/types' => IdTypeController::class,
        ]);
    });


    Route::middleware(['decrypt.request'])->group(function (){
        Route::apiResources([
            'news' => NewsAndUpdateController::class,
            'help_center' => HelpCenterController::class,
        ]);
    });

    


});
