<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminDashboardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'paybills_amount'       =>  $this->paybills_amount,
            'paybills_other_charges'    =>  $this->paybills_other_charges,
            'paybills_service_fee'  =>  $this->paybills_service_fee,
            'customer_count'        =>  $this->customer_count,
            'total_transaction'     =>  $this->total_transaction,
            'total_cashin'          =>  $this->total_cashin,
            'sendmoney_amount'      =>  $this->sendmoney_amount,
            'sendmoney_service_fee' =>  $this->sendmoney_service_fee,
            'total_disbursement'    =>  $this->total_disbursement,
            'total_collection'      =>  $this->total_collection,
            'total_available_funds' =>  $this->total_available_funds,
        ];
    }
}
