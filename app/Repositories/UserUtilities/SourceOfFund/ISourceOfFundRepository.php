<?php

namespace App\Repositories\UserUtilities\SourceOfFund;

use App\Repositories\IRepository;

interface ISourceOfFundRepository extends IRepository
{
    public function getAllSourceOfFunds();  
}
