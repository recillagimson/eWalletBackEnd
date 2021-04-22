<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ImageUpload\ImageUploadRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Carbon\Carbon;
use App\Services\Utilities\Responses\IResponseService;
use App\Enums\SuccessMessages;

class ImageUploadController extends Controller
{
    private IResponseService $responseService;
    
    public function __construct(IResponseService $responseService)
    {
        $this->responseService = $responseService;
    }

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
        
        return $this->responseService->successResponse(array($storagePath), SuccessMessages::recordSaved);
    }
}
