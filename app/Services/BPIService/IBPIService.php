<?php
namespace App\Services\BPIService;


interface IBPIService {
    public function getAccounts(string $token);
    public function fundTopUp(Array $array, string $token);
}
