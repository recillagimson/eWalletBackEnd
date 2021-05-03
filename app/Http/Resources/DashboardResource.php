<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DashboardResource extends JsonResource
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
            'first_name'    =>  $this->first_name,
            'middle_name'   =>  $this->middle_name,
            'last_name' =>  $this->last_name,
            'name_extension'    => $this->name_exetension,
            'user_account_status'   =>  $this->user_account_status,
            'mobile_number' =>  $this->mobile_number,
            'email' =>  $this->email,
            'selfie_location'   =>  $this->selfie_location,
            'available_balance' =>  $this->available_balance,
            'name'  =>  $this->name,
        ];
    }
}
