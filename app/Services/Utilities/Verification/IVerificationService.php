<?php
namespace App\Services\Utilities\Verification;

interface IVerificationService {
    public function create(array $data);
    public function createSelfieVerification(array $data);
    public function getSignedUrl(string $userPhotoId);
    public function updateTierApprovalIds(array $userIdPhotos, array $userSelfiePhotos, string $tierApprovalStatus);
}
