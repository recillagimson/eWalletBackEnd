<?php
namespace App\Services\PrepaidLoad;

use App\Repositories\Payload\IPrepaidLoadRepository;

class PrepaidLoadService implements IPrepaidLoadService {

    public IPrepaidLoadRepository $prepaidLoads;

    public function __construct(IPrepaidLoadRepository $prepaidLoads)
    {
        $this->prepaidLoads = $prepaidLoads;
    }

}
