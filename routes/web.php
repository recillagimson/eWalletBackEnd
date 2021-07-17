<?php

use App\Models\BillerReport;
use App\Models\DRCRProcedure;
use App\Traits\LogHistory\LogHistory;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    
    $records = BillerReport::where('transaction_date', '>=', '2021-01-01')
        ->where('transaction_date', '<=', '2021-06-30')->get();

    // return view('reports.out_pay_bills_history.out_pay_bills_history_report', [
    //     'records' => $records
    // ]);

    // $records = DRCRProcedure::where('transaction_date', '>=', '2021-01-01')
    //         ->where('transaction_date', '<=', '2021-06-30')->get();

    $data = [
        [
          "2021-05-26 12:09:21",
          "1000000000002",
          "Mary Allisson  Lindayag",
          "0",
          "Debit",
          "SM0000323",
          "10000.000000",
          null,
          "0",
          "SUCCESS",
        ]
    ];

    return view('reports.log_histories.log_histories', [
        'records' => $data
    ]);
});
