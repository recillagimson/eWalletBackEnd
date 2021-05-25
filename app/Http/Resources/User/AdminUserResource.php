<?php

namespace App\Http\Resources\User;

use App\Models\UserAccount;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminUserResource extends JsonResource
{
    public function __construct(UserAccount $resource)
    {
        parent::__construct($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'last_name' => $this->profile->last_name,
            'first_name' => $this->profile->first_name,
            'middle_name' => $this->profile->middle_name,
            'birth_date' => $this->profile->birth_date,
            'marital_status_id' => $this->profile->marital_status_id,
            'house_no_street' => $this->profile->house_no_street,
            'city' => $this->profile->city,
            'province_state' => $this->profile->province_state,
            'municipality' => $this->profile->municipality,
            'country_id' => $this->profile->country_id,
            'verified' => $this->verified,
            'is_lockout' => $this->is_lockout,
            'is_active' => $this->is_active,
            'user_created' => $this->user_created,
            'user_updated' => $this->user_updated,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
