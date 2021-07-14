<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Enums\SuccessMessages;
use App\Http\Requests\Address\MunicipalityRequest;

//Interfaces
use App\Repositories\Address\Municipality\IMunicipalityRepository;

//Services
use App\Services\Utilities\Responses\IResponseService;

class MunicipalityController extends Controller
{
    //
    private IMunicipalityRepository $municipalityRepository;
    private IResponseService $responseService;

    public function __construct(IMunicipalityRepository $municipalityRepository, IResponseService $responseService)
    {
        $this->municipalityRepository = $municipalityRepository;
        $this->responseService = $responseService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function getMunicipalities(MunicipalityRequest $request): JsonResponse
    {
        $data = $request->all();
        $municipalities = $this->municipalityRepository->getMunicipalities($data['province_code']);
        return $this->responseService->successResponse($municipalities->toArray(), SuccessMessages::success);
    }
}
