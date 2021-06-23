<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminDashboardResource extends JsonResource
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
            'customer_count'        =>  $this->customer_count,
            'total_transaction'     =>  $this->total_transaction,
            'total_cashin'          =>  $this->total_cashin,
            'total_disbursement'    =>  $this->total_disbursement,
        ];
    }
}
