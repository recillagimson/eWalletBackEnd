<?php

namespace App\Repositories\Client;

use App\Models\Client;
use App\Repositories\Repository;

class ClientRepository extends Repository implements IClientRepository
{
    public function __construct(Client $model)
    {
        parent::__construct($model);
    }

    public function getClient(string $clientId)
    {
        return $this->model->where('client_id', '=', $clientId)->first();
    }
}
