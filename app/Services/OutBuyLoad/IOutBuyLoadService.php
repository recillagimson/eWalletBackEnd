<?php
namespace App\Services\OutBuyLoad;

interface IOutBuyLoadService {

    public function load(array $details);
    public function createRecord(array $details);
    public function showNetworkPromos();
}
