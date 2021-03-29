<?php
namespace App\Services\OutBuyLoad;

use App\Repositories\OutBuyLoad\IOutBuyLoadRepository;

class OutBuyLoadService implements IOutBuyLoadService {

    public IOutBuyLoadRepository $outBuyLoads;

    public function __construct(IOutBuyLoadRepository $outBuyLoads)
    {
        $this->outBuyLoads = $outBuyLoads;
    }

     
}
