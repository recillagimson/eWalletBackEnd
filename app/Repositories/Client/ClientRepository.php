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
        return 'shit';
        $this->model->where('client_id', '=', 'sdfsd')->first();
    }

    public function getAllClient()
    {
        return $this->model->get();
    }
}
