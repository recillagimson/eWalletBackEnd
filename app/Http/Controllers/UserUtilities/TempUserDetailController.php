<?php

namespace App\Http\Controllers\UserUtilities;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\TempUserDetail\ITempUserDetailService;
use App\Services\Utilities\Responses\IResponseService;
use App\Enums\SuccessMessages;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\User\UpdateStatusUpdateProfileRequest;
use App\Models\TempUserDetail;

class TempUserDetailController extends Controller
{
    public function __construct(ITempUserDetailService $tempUserDetail, IResponseService $responseService)
    {
        $this->tempUserDetail = $tempUserDetail;
        $this->responseService = $responseService;
    }

    public function index(Request $request): JsonResponse 
    {
        $records = $this->tempUserDetail->getAllPaginated($request->all());

        return $this->responseService->successResponse($records->toArray() , SuccessMessages::success);
    }

    public function show(string $id)
    {
        $record = $this->tempUserDetail->findById($id);
        
        return $this->responseService->successResponse($record->toArray($record), SuccessMessages::success);
    }

    public function updateStatus(string $id, UpdateStatusUpdateProfileRequest $request)
    {
        $data = $request->validated(); 

        $record = $this->tempUserDetail->updateStatus($id, $data['status'], $request->user());
        
        return $this->responseService->successResponse($record->toArray($record), SuccessMessages::success);
    }
}
