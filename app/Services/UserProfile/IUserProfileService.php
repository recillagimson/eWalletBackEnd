<?php
namespace App\Services\UserProfile;

interface IUserProfileService {

    public function update(object $userAccount, array $details);
    public function addUserInput(array $details, object $userAccount, object $data=null);
}
