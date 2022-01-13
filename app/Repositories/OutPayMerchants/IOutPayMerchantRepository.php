<?php

namespace App\Repositories\OutPayMerchants;

use App\Repositories\IRepository;
use Illuminate\Database\Eloquent\Collection;

interface IOutPayMerchantRepository extends IRepository
{
    public function getByUser(string $userId): Collection;

    public function getByRefNo(string $refNo): Collection;
}
