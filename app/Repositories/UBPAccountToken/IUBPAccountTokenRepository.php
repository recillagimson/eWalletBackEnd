<?php

namespace App\Repositories\UBPAccountToken;

use App\Repositories\IRepository;

interface IUBPAccountTokenRepository extends IRepository
{
    public function getByUser(string $userId);
}
