<?php
namespace App\Services\Utilities\Verification;

use App\Repositories\UserAccount\IUserAccountRepository;

interface IVerificationService {
    public function create(array $data);
}
