<?php

namespace App\Http\Resources\FundTransfer;

use Illuminate\Http\Resources\Json\JsonResource;

class CashoutResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'account_name'   =>  $this->AccountName,
            'account_number' =>  $this->AccountNo,
            'purpose'   =>  $this->Purpose,
            'email'     =>  $this->Email,
            'amount'    =>  $this->Amount,
            'transaction_remarks'   =>  $this->Remarks,
            'status'    =>  $this->TransResponse,
        ];
    }
}
