<?php

namespace App\Services\Utilities\Verification;

use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use App\Repositories\UserPhoto\IUserPhotoRepository;

class VerificationService implements IVerificationService
{
    public IUserPhotoRepository $userPhotoRepository;

    public function __construct(IUserPhotoRepository $userPhotoRepository)
    {
        $this->userPhotoRepository = $userPhotoRepository;
    }

    public function create(array $data) {
        // GET EXT NAME
        $id_photo_ext = $data['id_photo']->getClientOriginalExtension();
        $selfie_photo_ext = $data['selfie_photo']->getClientOriginalExtension();
        
        // GENERATE NEW FILE NAME
        $id_photo_name = $data['user_account_id'] . "/" . \Str::random(40) . "." . $id_photo_ext;
        $selfie_photo_name = $data['user_account_id'] . "/" . \Str::random(40) . "." . $selfie_photo_ext;

        // PUT FILE TO STORAGE
        $id_photo_path = Storage::putFileAs('verification', new File($data['id_photo']), $id_photo_name);
        $selfie_photo_path = Storage::putFileAs('verification', new File($data['selfie_photo']), $selfie_photo_name);

        // SAVE ID PHOTO
        $params = [
            'id' => \Str::uuid(),
            'user_account_id' => $data['user_account_id'],
            'id_type_id' => $data['id_type_id'],
            'photo_location' => $id_photo_path,
        ];
        $id_photo = $this->userPhotoRepository->create($params);

        // SAVE SELFIE PHOTO
        $params = [
            'id' => \Str::uuid(),
            'user_account_id' => $data['user_account_id'],
            'id_type_id' => $data['id_type_id'],
            'photo_location' => $selfie_photo_path,
        ];
        $selfie_photo = $this->userPhotoRepository->create($params);

        return [$id_photo, $selfie_photo];

    }
}
