<?php

use App\Models\DRCRBalance;
use App\Http\Controllers\KYC\KYCController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Route::post('/', [KYCController::class, 'callback']);
Route::get('/', function() {
    $records = DRCRBalance::with([])
        ->where('original_transaction_date', '>=', '2021-01-01')
        ->where('original_transaction_date', '<=', '2021-12-31')
        ->where('user_account_id', 'b0a6f424-da0b-4b2b-a347-a281ed91c7c0')
        ->get();
        
    return view('reports.transaction_history.transaction_history_v2', [
        'firstName' => 'sample',
        'from' => '2021-01-01',
        'to' => '2021-12-31',
        'records' => $records,
        'dt' => '10/21/2021'
    ]);
});