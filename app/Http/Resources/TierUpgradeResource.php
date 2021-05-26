<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TierUpgradeResource extends JsonResource
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
            'tier_name' =>  $this->name,
            'tier_class'    =>  $this->tier_class,
            'title' =>  $this->title,
            'wallet_limit'  =>  $this->monthly_limit,
        ];
    }
}
