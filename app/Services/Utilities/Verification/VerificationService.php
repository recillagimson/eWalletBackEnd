<?php

namespace App\Services\Auth;

use App\Repositories\Client\IClientRepository;

class VerificationService implements IVerificationService
{
    public IClientRepository $clients;

    public function __construct(IClientRepository $clients)
    {
        $this->clients = $clients;
    }
}
