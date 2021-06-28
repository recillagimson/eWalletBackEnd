<?php
namespace App\Services\BPIService;


interface IBPIService {
    public function getAccounts(string $token);
    public function fundTopUp(array $array, string $token);
    public function otp(array $params);
    public function process(array $params);
    public function status(array $params);
}
