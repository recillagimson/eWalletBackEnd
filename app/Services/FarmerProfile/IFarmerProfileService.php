<?php
namespace App\Services\FarmerProfile;

use Illuminate\Http\UploadedFile;

interface IFarmerProfileService
{
    public function upgradeFarmerToSilver(array $attr, string $authUser);

    public function batchUpload($file, string $authUser);

    public function subsidyBatchUpload($file, string $authUser);

    public function processBatchUpload(UploadedFile $file, string $userId);

    public function batchUploadV2($file, string $authUser);
}
