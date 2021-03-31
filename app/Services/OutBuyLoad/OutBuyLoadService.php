<?php

namespace App\Services\OutBuyLoad;

use App\Repositories\PrepaidLoad\IPrepaidLoadRepository;
use App\Repositories\OutBuyLoad\IOutBuyLoadRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Services\Utilities\PrepaidLoad\IPrepaidLoadService;
use Carbon\Carbon;

class OutBuyLoadService implements IOutBuyLoadService
{
    private IPrepaidLoadService $prepaidLoadService;
    public IPrepaidLoadRepository $prepaidLoads;
    public IOutBuyLoadRepository $outBuyLoads;
    private IUserAccountRepository $userAccountRepository;

    public function __construct(IPrepaidLoadService $prepaidLoadService,
                                IPrepaidLoadRepository $prepaidLoads, 
                                IOutBuyLoadRepository $outBuyLoads,
                                IUserAccountRepository $userAccountRepository)
    {
        $this->prepaidLoadService = $prepaidLoadService;
        $this->prepaidLoads = $prepaidLoads;
        $this->outBuyLoads = $outBuyLoads;
        $this->userAccountRepository = $userAccountRepository;
    }

    public function load(array $details)
    {
        return $this->prepaidLoadService->load($details);
    }

    public function createRecord(array $details) {
        $getPromoDetails = $this->prepaidLoads->getByRewardKeyword($details['promo']);
        $inputOutBuyLoad = $this->inputOutBuyLoad($getPromoDetails, $details);
        $createOutBuyLoad = $this->outBuyLoads->create($inputOutBuyLoad);
        $createOutBuyLoad->user_account_detail = $this->userAccountRepository->get($createOutBuyLoad->user_account_id);

        return $createOutBuyLoad;
    }

    public function showNetworkPromos() {
        return $this->prepaidLoadService->showNetworkPromos();
    }

    private function inputOutBuyLoad(object $promos, array $details): array {
        $body = array(
                    'user_account_id'=>$details['user_id'],
                    'prepaid_load_id'=>$promos->id,
                    'total_amount'=>$promos->amount,
                    'transaction_date'=>Carbon::now(),
                    'transaction_category_id'=>'0ec43830-9131-11eb-b44f-1c1b0d14e211',
                    'transaction_remarks'=>'',
                );
        return $body;
    }

}
