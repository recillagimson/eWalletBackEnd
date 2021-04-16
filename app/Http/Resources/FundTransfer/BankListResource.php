<?php

namespace App\Http\Resources\FundTransfer;

use Illuminate\Http\Resources\Json\JsonResource;

class BankListResource extends JsonResource
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
            'code'  =>  $this->code,
            'bank'  =>  $this->bank,
        ];
    }
}
