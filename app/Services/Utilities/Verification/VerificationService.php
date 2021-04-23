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

        $recordsCreated = [];

        // PROCESS IDS
        foreach($data['id_photos'] as $idPhoto) {
            // Get file extension name
            $extName = $this->getFileExtensionName($idPhoto);
            // Generate new file name
            $idPhotoName = $data['user_account_id'] . "/" . \Str::random(40) . "." . $extName;
            // Put file to storage
            $path = $this->saveFile($idPhoto, $idPhotoName, 'id_photo');
            // Save record to DB
            $params = [
                'id' => \Str::uuid(),
                'user_account_id' => $data['user_account_id'],
                'id_type_id' => $data['id_type_id'],
                'photo_location' => $path,
            ];
            $record = $this->userPhotoRepository->create($params);
            // Collect created record
            array_push($recordsCreated, $record);
        }

        // For Serfile Processing
        // GET EXT NAME
        $selfiePhotoExt = $this->getFileExtensionName($data['selfie_photo']);
        // GENERATE NEW FILE NAME
        $selfiePhotoName = $data['user_account_id'] . "/" . \Str::random(40) . "." . $selfiePhotoExt;
        // PUT FILE TO STORAGE
        $selfiePhotoPath = $this->saveFile($data['selfie_photo'], $selfiePhotoName, 'selfie_photo');
        // SAVE SELFIE PHOTO
        $params = [
            'id' => \Str::uuid(),
            'user_account_id' => $data['user_account_id'],
            'id_type_id' => $data['id_type_id'],
            'photo_location' => $selfiePhotoPath,
        ];
        $record = $this->userPhotoRepository->create($params);
        // Collect created record
        array_push($recordsCreated, $record);

        // return to controller all created records
        return $recordsCreated;
    }
    // Get file extension name
    public function getFileExtensionName(UploadedFile $file) {
        return $file->getClientOriginalExtension();
    }

    // Move uploaded file to server storage
    // Returns path of the file stored
    public function saveFile(UploadedFile $file, $fileName, $folderName) {
        return Storage::putFileAs($folderName, $file, $fileName);
    }
}
