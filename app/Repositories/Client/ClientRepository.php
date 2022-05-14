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
        return $this->model->where('client_id', '=', 'spa-client')->first();
    }

    public function getAllClient()
    {
        return $this->model->get();
    }
}
