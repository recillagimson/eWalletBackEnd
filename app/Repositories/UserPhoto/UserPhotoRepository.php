<?php

namespace App\Repositories\UserPhoto;

use App\Models\UserPhoto;
use App\Models\UserUtilities\UserDetail;
use App\Repositories\Repository;
use Illuminate\Validation\ValidationException;

class UserPhotoRepository extends Repository implements IUserPhotoRepository
{
    public function __construct(UserPhoto $model)
    {
        parent::__construct($model);
    }

    public function updateSelfiePhoto(string $selfieUrl) {
        $userDetails = UserDetail::where('user_account_id', request()->user()->id)->first();
        if($userDetails) {
            $userDetails->update([
                'selfie_loction' => $selfieUrl,
            ]);
        }
        return $userDetails;
        
        throw ValidationException::withMessages([
            'user_detail_not_found' => 'User Detail not found'
        ]);
    }

    public function updateAvatarPhoto(string $avatarUrl) {
        $userDetails = UserDetail::where('user_account_id', request()->user()->id)->first();
        if($userDetails) {
            $userDetails->update([
                'avatar_location' => $avatarUrl,
            ]);
        }
        return $userDetails->append('avatar_link');
        
        throw ValidationException::withMessages([
            'user_detail_not_found' => 'User Detail not found'
        ]);
    }
}
