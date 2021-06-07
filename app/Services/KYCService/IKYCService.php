<?php
namespace App\Services\KYCService;

use Illuminate\Http\File;

interface IKYCService {
    public function initFaceMatch(array $attr);
    public function initOCR(array $attr);
}
