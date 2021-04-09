<?php

namespace App\Services\Utilities\Verification;

use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
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

        $records_created = [];

        // PROCESS IDS
        foreach($data['id_photos'] as $id_photo) {
            // Get file extension name
            $ext_name = $this->getFileExtensionName($id_photo);
            // Generate new file name
            $id_photo_name = $data['user_account_id'] . "/" . \Str::random(40) . "." . $ext_name;
            // Put file to storage
            $path = $this->saveFile($id_photo, $id_photo_name, 'verification');
            // Save record to DB
            $params = [
                'id' => \Str::uuid(),
                'user_account_id' => $data['user_account_id'],
                'id_type_id' => $data['id_type_id'],
                'photo_location' => $path,
            ];
            $record = $this->userPhotoRepository->create($params);
            // Collect created record
            array_push($records_created, $record);
        }

        // For Serfile Processing
        // GET EXT NAME
        $selfie_photo_ext = $this->getFileExtensionName($data['selfie_photo']);
        // GENERATE NEW FILE NAME
        $selfie_photo_name = $data['user_account_id'] . "/" . \Str::random(40) . "." . $selfie_photo_ext;
        // PUT FILE TO STORAGE
        $selfie_photo_path = $this->saveFile($data['selfie_photo'], $selfie_photo_name, 'verification');
        // SAVE SELFIE PHOTO
        $params = [
            'id' => \Str::uuid(),
            'user_account_id' => $data['user_account_id'],
            'id_type_id' => $data['id_type_id'],
            'photo_location' => $selfie_photo_path,
        ];
        $record = $this->userPhotoRepository->create($params);
        // Collect created record
        array_push($records_created, $record);

        // return to controller all created records
        return $records_created;
    }
    // Get file extension name
    public function getFileExtensionName(UploadedFile $file) {
        return $file->getClientOriginalExtension();
    }

    // Move uploaded file to server storage
    // Returns path of the file stored
    public function saveFile(UploadedFile $file, $file_name, $folder_name) {
        return Storage::putFileAs($folder_name, $file, $file_name);
    }
}
