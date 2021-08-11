<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Enums\SuccessMessages;
use App\Http\Requests\Address\ProvinceRequest;

//Interfaces
use App\Repositories\Address\Province\IProvinceRepository;

//Services
use App\Services\Utilities\Responses\IResponseService;

class ProvinceController extends Controller
{
    //
    private IProvinceRepository $provinceRepository;
    private IResponseService $responseService;

    public function __construct(IProvinceRepository $provinceRepository, IResponseService $responseService)
    {
        $this->provinceRepository = $provinceRepository;
        $this->responseService = $responseService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function getProvinces(): JsonResponse
    {
        $provinces = $this->provinceRepository->getProvinces();
        return $this->responseService->successResponse($provinces->toArray(), SuccessMessages::success);
    }
}
