<?php
namespace App\Services\Utilities\PrepaidLoad;

use App\Repositories\PrepaidLoad\IPrepaidLoadRepository;
use App\Repositories\OutBuyLoad\IOutBuyLoadRepository;
use Illuminate\Support\Facades\Http;
use App\Enums\NetworkTypes;

class GlobeService implements IPrepaidLoadService {

    public IPrepaidLoadRepository $prepaidLoads;
    public IOutBuyLoadRepository $outBuyLoads;

    public function __construct(IPrepaidLoadRepository $prepaidLoads, 
                                IOutBuyLoadRepository $outBuyLoads)
    {
        $this->prepaidLoads = $prepaidLoads;
        $this->outBuyLoads = $outBuyLoads;
    }

     /**
     * Load Globe
     *
     * @param array $items
     * @return array
     */
    public function load(array $items): array
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

      /**
     * Show list of promos
     *
     * @return array
     */
    public function showNetworkPromos(): array {
        $getAllGlobePromos = $this->prepaidLoads->getByNetworkType(NetworkTypes::Globe);
        return $getAllGlobePromos->toArray();
    }
}
