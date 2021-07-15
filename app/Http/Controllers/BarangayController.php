<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Enums\SuccessMessages;
use App\Http\Requests\Address\BarangayRequest;

//Interfaces
use App\Repositories\Address\Barangay\IBarangayRepository;

//Services
use App\Services\Utilities\Responses\IResponseService;

class BarangayController extends Controller
{
    //
    private IBarangayRepository $barangayRepository;
    private IResponseService $responseService;

    public function __construct(IBarangayRepository $barangayRepository, IResponseService $responseService)
    {
        $this->barangayRepository = $barangayRepository;
        $this->responseService = $responseService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function getBarangays(BarangayRequest $request): JsonResponse
    {
        $data = $request->all();
        $barangays = $this->barangayRepository->getBarangays($data['municipality_code']);
        return $this->responseService->successResponse($barangays->toArray(), SuccessMessages::success);
    }
}
