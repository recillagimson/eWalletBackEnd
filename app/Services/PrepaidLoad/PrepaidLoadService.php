<?php
namespace App\Services\PrepaidLoad;

use App\Repositories\PrepaidLoad\IPrepaidLoadRepository;
use Illuminate\Support\Facades\Http;

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
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post(config('services.load.globe.url').'/rewards/v1/transactions/send', 
        $this->createGlobePostBody($items));

        return array($response);
    }

    /**
     * Post body request
     *
     * @param array $items
     * @return array
     */
    private function createGlobePostBody(array $items): array {
        $body = [
            [
                "outboundRewardRequest" => [
                    "app_id" => config('services.load.globe.id'),
                    "app_secret" => config('services.load.globe.secret'),
                    "rewards_token" => config('services.load.globe.rewards_token'),
                    "address" => $items['mobile_number'],
                    "promo" => $items['promo']
                ]
            ]
        ];

        return $body;
    }
}
