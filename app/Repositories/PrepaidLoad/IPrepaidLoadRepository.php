<?php

namespace App\Repositories\PrepaidLoad;

use App\Repositories\IRepository;

interface IPrepaidLoadRepository extends IRepository
{
    public function getByRewardKeyword(string $rewardKeyword);
}
