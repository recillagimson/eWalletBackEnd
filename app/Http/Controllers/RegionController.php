<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Enums\SuccessMessages;

//Interfaces
use App\Repositories\Address\Region\IRegionRepository;

//Services
use App\Services\Utilities\Responses\IResponseService;

class RegionController extends Controller
{

    private IRegionRepository $regionRepository;
    private IResponseService $responseService;

    public function __construct(IRegionRepository $regionRepository, IResponseService $responseService)
    {
        $this->regionRepository = $regionRepository;
        $this->responseService = $responseService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $regions = $this->regionRepository->getRegions();
        return $this->responseService->successResponse($regions->toArray(), SuccessMessages::success);
    }

}
