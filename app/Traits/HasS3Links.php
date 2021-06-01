<?php

namespace App\Traits;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

trait HasS3Links
{
    private function getTempUrl(string $path, string $expiration_date_time) {
        if($path) {
            return Storage::disk('s3')->temporaryUrl($path, $expiration_date_time);
        }
        return null;
    }
}