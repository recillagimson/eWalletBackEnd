<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PayloadController;
use App\Http\Controllers\PrepaidLoadController;
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

    Route::prefix('/auth')->middleware(['decrypt.request'])->group(function (){
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
    });
    Route::prefix('/load')->middleware(['decrypt.request'])->group(function (){
        Route::get('/globe', [PrepaidLoadController::class, 'showGlobePromos']);
        Route::post('/globe', [PrepaidLoadController::class, 'loadGlobe']);
    });
});
