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

    public function createRecord(array $details, object $request) {
        $getPromoDetails = $this->prepaidLoads->getByRewardKeyword($details['promo']);
        $inputOutBuyLoad = $this->inputOutBuyLoad($getPromoDetails, $details, $request->user());
        $createOutBuyLoad = $this->outBuyLoads->create($inputOutBuyLoad);
        $createOutBuyLoad->user_account_detail = $this->userAccountRepository->get($createOutBuyLoad->user_account_id);

        return $createOutBuyLoad;
    }

    public function showNetworkPromos() {
        return $this->prepaidLoadService->showNetworkPromos();
    }

    private function inputOutBuyLoad(object $promos, array $details, object $user): array {
        $body = array(
                    'user_account_id'=>$user->id,
                    'prepaid_load_id'=>$promos->id,
                    'total_amount'=>$promos->amount,
                    'transaction_date'=>Carbon::now(),
                    'transaction_category_id'=>'edf4d5d0-9299-11eb-9663-1c1b0d14e211',
                    'transaction_remarks'=>'',
                );
        return $body;
    }

}
