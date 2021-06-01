<?php


namespace App\Traits;

use Storage;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;

trait HasFileUploads
{
    // Get file extension name
    private function getFileExtensionName(UploadedFile $file) {
        return $file->getClientOriginalExtension();
    }

    // Move uploaded file to server storage
    // Returns path of the file stored
    private function saveFile(UploadedFile $file, $fileName, $folderName) {
        return Storage::disk('s3')->putFileAs($folderName, $file, $fileName);
    }

    // Delete existing file if necessary
    private function deleteFile($path) {
        return Storage::disk('s3')->delete($path);
    }
}
