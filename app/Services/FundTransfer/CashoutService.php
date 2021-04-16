<?php

namespace App\Services\FundTransfer;

use App\Repositories\FundTransfer\IOutSendToBankRepository;
use App\Repositories\UserBalance\IUserBalanceRepository;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class CashoutService implements ICashoutService
{
    public IOutSendToBankRepository $sendToBank;
    public IUserBalanceRepository $userBalanceInfo;

    public function __construct(IOutSendToBankRepository $stBank, IUserBalanceRepository $userbalance)
    {
        $this->sendToBank = $stBank;
        $this->userBalanceInfo = $userbalance;
    }

    public function cashout(array $newCashout)
    {
        /*
        |================================================================
        |Get available balance of the user [sender] using user_account_id
        |================================================================
         */

        $UserBalance = $this->getUserBalance($newCashout);
        /*
        |==============================================
        |Validate if the user has a balance to transfer
        |==============================================
         */

        if($UserBalance < $newCashout['amount'])
        {
            throw ValidationException::withMessages([
                'message' => 'Insufficient Balance',
            ]);
        }
        else
        {
            $LoginToken = $this->getToken();
            if (empty($LoginToken)) {
                throw ValidationException::withMessages([
                    'message' => 'Unable to connect, Please try again.',
                ]);
            }

            //API for single transfer here
            $repsonse = $this->singleTransfer($LoginToken);
            dd($response);

            //handling response of API here
            if (!empty(json_decode($response)->code) && !empty(json_decode($response)->errors[0])) {
                throw ValidationException::withMessages([
                    'message' => json_decode($response)->errors[0]->details,
                ]);
            } else {
                if (!empty(json_decode($response)->errors[0])) {
                    if (json_decode($response)->errors[0]->code == 'TF') {
                        $TransResponse = 'FAILED';
                    } else if (json_decode($response)->errors[0]->code == 'RT') {
                        $TransResponse = 'PENDING';
                    } else if (json_decode($response)->errors[0]->code == 'SP') {
                        $TransResponse = 'PENDING';
                    } else if (json_decode($response)->errors[0]->code == 'NC') {
                        $TransResponse = 'PENDING';
                    } else if (json_decode($response)->errors[0]->code == 'SC') {
                        $TransResponse = 'PENDING';
                    } else {
                        throw ValidationException::withMessages([
                            'message' => json_decode($response)->errors[0]->details,
                        ]);
                    }
                } else {
                    $RefNo = json_decode($response)->ubpTranId;
                    $newUserBalance = $UserBalance - $newCashout['amount'];
                    $TransResponse = 'SUCCESS';
                    $newCashout['instapay_reference'] = $RefNo;
                    $UserID = $newCashout['user_account_id'];
                    $UserBalance = $this->userBalanceInfo->updateUserBalance($UserID, $newUserBalance);
                }
            }

            $newCashout['status'] = $TransResponse;
            $RefNo = $this->sendToBank->getRefNo();
            $newCashout['reference_number'] = $RefNo;
            return $this->sendToBank->create($newCashout);
        }
    }

    public function getUserBalance(array $newCashout)
    {
        return $this->userBalanceInfo->getUserBalance($newCashout['user_account_id']);
    }

    public function getToken(): object
    {
        $tokenURL = $this->ubp_token_url = config('union-bank.ubp_token_url');
        $response = Http::asForm()->post($tokenURL,
            [
                'grant_type' => $this->ubp_grant_type = config('union-bank.ubp_grant_type'),
                'client_id' => $this->ubp_transfer_client = config('union-bank.ubp_transfer_client'),
                'username' => $this->ubp_username = config('union-bank.ubp_username'),
                'password' => $this->ubp_password = config('union-bank.ubp_password'),
                'scope' => $this->ubp_scope = config('union-bank.ubp_scope'),
            ]
        );

        return json_decode($response->body());
    }

    public function singleTransfer(object $LoginToken): object
    {
        $transferURL = $this->ubp_transfer_url = config('union-bank.ubp_transfer_url');
        $SenderRefID = rand(1000, 9999);
        $body = [
            "senderRefId" => $SenderRefID,
            "tranRequestDate" => "2015-10-03T15:29:16.333",
            "sender" => [
                "name" => "JUAN CRUZ",
                "address" => [
                    "line1" => "GRACE",
                    "line2" => "PARK CALOOCAN CITY",
                    "city" => "Caloocan",
                    "province" => "142",
                    "zipCode" => "1900",
                    "country" => "204",
                ],
            ],
            "beneficiary" => [
                "accountNumber" => "8439374137",
                "name" => "MARK GUANEZ",
                "address" => [
                    "line1" => "241 A.DEL MUNDO ST BET. 5TH 6TH AVE GRACE",
                    "line2" => "PARK CALOOCAN CITY",
                    "city" => "Caloon",
                    "province" => "142",
                    "zipCode" => "1900",
                    "country" => "204",
                ],
            ],
            "remittance" => [
                "amount" => "10.00",
                "currency" => "PHP",
                "receivingBank" => "161403",
                "purpose" => "1001",
                "instructions" => "instructions data",
            ],
        ];
        $response = Http::withHeaders([
            "x-ibm-client-id" => "0044210f-af84-42fb-8b6a-c5536a577dc6",
            "x-ibm-client-secret" => "H6nG1bM2aX6wD3lL2bX8dA6nO8bP7hX2dS0bC0xE8oO0gP3pG7",
            "x-partner-id" => "6823e8df-7305-4acb-b62e-53a0ce8a2042",
            "Content-Type" => "application/json",
            "Accept" => "application/json"
        ])->withToken($LoginToken->access_token)->post($transferURL, $body);
            dd(json_decode($response));
        return $response;
    }

}
