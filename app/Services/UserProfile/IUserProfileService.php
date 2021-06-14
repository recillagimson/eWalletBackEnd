<?php
namespace App\Services\UserProfile;

use App\Models\UserAccount;

interface IUserProfileService {

    public function update(object $userAccount, array $details);
    public function addUserInput(array $details, object $userAccount, object $data=null);
    public function changeAvatar(array $data);
    public function updateUserProfile(UserAccount $userAccount, array $request, object $user);
}
