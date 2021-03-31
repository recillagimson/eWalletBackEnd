<?php

namespace App\Repositories\PrepaidLoad;

use App\Models\PrepaidLoad;
use App\Repositories\Repository;

class PrepaidLoadRepository extends Repository implements IPrepaidLoadRepository
{
    public function __construct(PrepaidLoad $model)
    {
        parent::__construct($model);
    }

    public function getByRewardKeyword(string $rewardKeyword)
    {
        return $this->model->where('reward_keyword', '=', $rewardKeyword)->first();
    }

    public function getByNetworkType(string $network)
    {
        return $this->model->where('network', '=', $network)->get();
    }
}
