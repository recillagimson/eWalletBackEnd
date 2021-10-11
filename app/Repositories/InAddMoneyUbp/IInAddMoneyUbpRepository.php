<?php

namespace App\Repositories\InAddMoneyUbp;

use App\Repositories\IRepository;
use Illuminate\Database\Eloquent\Collection;

interface IInAddMoneyUbpRepository extends IRepository
{
    public function getPending(string $userId): Collection;
}
