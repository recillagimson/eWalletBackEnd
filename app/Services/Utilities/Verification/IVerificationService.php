<?php
namespace App\Services\Utilities\Verification;

interface IVerificationService {
    public function create(array $data);
    public function createSelfieVerification(array $data, ?string $userAccountId = null);
    public function getSignedUrl(string $userPhotoId);
    public function updateTierApprovalIds(array $userIdPhotos, array $userSelfiePhotos, string $tierApprovalStatus, bool $is_farmer = false);
    public function createSelfieVerificationFarmers(array $data, ?string $userAccountId = null);
    public function uploadSignature(array $attr);
}
