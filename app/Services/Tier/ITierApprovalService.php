<?php
namespace App\Services\Tier;

use App\Models\TierApproval;

interface ITierApprovalService {
    public function updateOrCreateApprovalRequest(array $attr);
    public function updateStatus(array $attr, TierApproval $tierApproval);
    public function takePhotoAction(array $attr);
    public function takeSelfieAction(array $attr);
<<<<<<< HEAD
=======
    public function sendEmail(string $email, string $message);
    public function sendSMS(string $mobile_number, string $message);
>>>>>>> stagingfix
}
