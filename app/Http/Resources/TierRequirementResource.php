<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TierRequirementResource extends JsonResource
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
            'tier_id'   =>  $this->tier_id,
            'requirement_type'  => $this->requirement_type,
            'name'  =?  $this->name,
        ];
    }
}
