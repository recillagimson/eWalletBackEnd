<?php

namespace App\Repositories\UBPAccountToken;

use App\Models\UBP\UbpAccountToken;
use App\Repositories\IRepository;

interface IUBPAccountTokenRepository extends IRepository
{
    public function getByUser(string $userId): UbpAccountToken;
}
