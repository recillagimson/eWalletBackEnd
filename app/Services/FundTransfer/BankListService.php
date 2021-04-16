<?php

namespace App\Services\FundTransfer;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;

class BankListService implements IBankListService
{
    public function banklist()
    {
        $url = $this->ubp_api_url = config('union-bank.ubp_api_url');
        
        $response = Http::withHeaders([
            'x-ibm-client-id'   =>  $this->ubp_client_id = config('union-bank.ubp_client_id'),
            'x-ibm-client-secret'   =>  $this->ubp_client_secret = config('union-bank.ubp_client_secret'),
            'x-partner-id'  =>  $this->ubp_client_partner_id = config('union-bank.ubp_client_partner_id'),
            'Accept'    =>  'application/json',
            'Content-Type'  =>  'application/json'
        ])->get($url);

        $banklist = json_decode($response);

        return $banklist;
    }
}
