<?php
namespace App\Services\KYCService;

use Illuminate\Http\File;
use phpDocumentor\Reflection\Types\Boolean;

interface IKYCService {
    public function initFaceMatch(array $attr, bool $isPath = false);
    public function initMerchantFaceMatch(array $attr);
    public function initOCR(array $attr);
}
