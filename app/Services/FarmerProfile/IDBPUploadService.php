<?php
namespace App\Services\FarmerProfile;

interface IDBPUploadService
{
    public function uploadSubsidyFileToS3v3($file);
    public function processSubsidyV3(array $attr, string $authUser);
}
