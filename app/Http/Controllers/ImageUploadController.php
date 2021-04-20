<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ImageUpload\ImageUploadRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Carbon\Carbon;

class ImageUploadController extends Controller
{
    
    
    /**
     * Upload Image
     * cmd: php artisan storage:link
     * @param ImageUploadRequest $request
     * @param string $module
     */
    public function uploadImage(ImageUploadRequest $request, string $module): JsonResponse
    {
        $details = $request->validated();

        $imageName = Carbon::now()->timestamp.'.'.$request->image->extension(); 

        $path = $request->file('image')->storeAs($module, $imageName);

        $storagePath = storage_path('app').'/'.$path;
        
        return response()->json($storagePath, Response::HTTP_OK);
    }
}
