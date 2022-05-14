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

    public function getAllClient()
    {
        return $this->model->get();
    }
}


 // return '{"id":"bda03ed7-3caf-43d2-a141-8bfa8eee6088","client_id":"spa-client","created_at":"2021-07-12T01:08:50.000000Z","updated_at":"2021-07-12T01:08:50.000000Z"}';
        
        // return [
        //     "id" => "bda03ed7-3caf-43d2-a141-8bfa8eee6088",
        //     "client_id" => "spa-client",
        //     "created_at" => "2021-07-12T01:08:50.000000Z",
        //     "updated_at" => "2021-07-12T01:08:50.000000Z"
        // ];
    
      //  return $this->model->where('client_id', '=', $clientId)->first();
