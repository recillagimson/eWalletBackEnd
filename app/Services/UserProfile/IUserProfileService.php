<?php
namespace App\Services\UserProfile;

interface IUserProfileService {

    public function update(object $userAccount, array $details);
}
