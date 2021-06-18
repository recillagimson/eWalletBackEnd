<?php

namespace App\Http\Resources\UserAccount;

use Illuminate\Http\Resources\Json\JsonResource;

class UserAccountCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "user_accounts" => [
                "id" => $this->id,
                "account_number" => $this->account_number,
                "email" => $this->email,
                "mobile_number" => $this->mobile_number,
                "is_lockout" => $this->is_lockout,
                "is_active" => $this->is_active,
                "verified" => $this->verified
            ],
            "user_details" => [
                "first_name" => $this->profile->first_name,
                "middle_name" => $this->profile->middle_name,
                "last_name" => $this->profile->last_name,
                "nationality_id" => $this->profile->nationality_id,
                "birth_date" => $this->profile->birth_date,
                "house_no_street" => $this->profile->house_no_street,
                "province_state" => $this->profile->province_state,
                "city" => $this->profile->city,
                "postal_code" => $this->profile->postal_code,
                "place_of_birth" => $this->profile->place_of_birth,
                "mother_maidenname" => $this->profile->mother_maidenname,
                "marital_status_id" => $this->profile->marital_status_id,
                "occupation" => $this->profile->occupation,
                "nature_of_work_id" => $this->profile->nature_of_work_id,
                "encoded_nature_of_work" => $this->profile->encoded_nature_of_work,
                "source_of_fund_id" => $this->profile->source_of_fund_id,
                "encoded_source_of_fund" => $this->profile->encoded_source_of_fund,
                "employer" => $this->profile->employer,
                "guardian_name" => $this->profile->guardian_name,
                "guardian_mobile_number" => $this->profile->guardian_mobile_number,
                "is_accept_parental_consent" => $this->profile->is_accept_parental_consent,
                "contact_no" => $this->profile->contact_no
            ]
        ];
    }
}
