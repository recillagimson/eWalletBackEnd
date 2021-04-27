<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PayBillsController extends Controller
{
    
    public function payBills(){
        return config('ubp.client_id');
    }

}
