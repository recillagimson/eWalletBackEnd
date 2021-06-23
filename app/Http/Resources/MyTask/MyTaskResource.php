<?php

namespace App\Http\Resources\MyTask;

use Illuminate\Http\Resources\Json\JsonResource;

class MyTaskResource extends JsonResource
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
            'temp_user_count'   =>  $this->temp_user_count,
            'tier_approval' =>  $this->tier_approval,
            'total_drcr'    =>  $this->total_drcr,
            'drcr_per_user' =>  $this->drcr_per_user,
       ];
    }
}
