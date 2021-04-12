<?php

namespace App\Services\Utilities\Verification;

use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Repositories\UserPhoto\IUserPhotoRepository;

class LogService implements ILogService
{
    public IUserPhotoRepository $userPhotoRepository;

    public function __construct(IUserPhotoRepository $userPhotoRepository)
    {
        $this->userPhotoRepository = $userPhotoRepository;
    }
}
