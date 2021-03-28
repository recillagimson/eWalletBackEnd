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

    public function createGlobePostBody() {
        $body = [
            [
                "outboundRewardRequest" => [
                    "app_id" => config('services.load.globe.id'),
                    "app_secret" => config('services.load.globe.secret'),
                    "rewards_token" => config('services.load.globe.rewards_token'),
                    "address" => "9271051129",
                    "promo" => "SQPLOAD100"
                ]
            ]
        ];

        return $body;
    }
}
