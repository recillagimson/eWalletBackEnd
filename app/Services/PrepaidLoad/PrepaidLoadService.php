<?php
namespace App\Services\PrepaidLoad;

use App\Repositories\PrepaidLoad\IPrepaidLoadRepository;

class PrepaidLoadService implements IPrepaidLoadService {

    public IPrepaidLoadRepository $prepaidLoads;

    public function __construct(IPrepaidLoadRepository $prepaidLoads)
    {
        $this->prepaidLoads = $prepaidLoads;
    }

     /**
     * Load Globe
     *
     * @param array $items
     * @return array
     */
    public function loadGlobe(array $items): array
    {
        return $items;
    }
}
