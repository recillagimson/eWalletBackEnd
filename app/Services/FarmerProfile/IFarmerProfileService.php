<?php
namespace App\Services\FarmerProfile;


interface IFarmerProfileService {
    public function upgradeFarmerToSilver(array $attr, string $authUser);
    public function batchUpload($file, string $authUser);
}
