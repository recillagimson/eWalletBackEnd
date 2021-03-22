<?php

namespace App\Repositories\Client;

use App\Repositories\IRepository;

interface IClientRepository extends IRepository
{
    public function getClient(string $clientId);
}
