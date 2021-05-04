<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

use App\Enums\SuccessMessages;
use App\Services\Dashboard\IDashboardService;
use App\Services\Utilities\Responses\IResponseService;

class DashboardController extends Controller
{
    private IDashboardService $dashboardService;
    private IResponseService $responseService;

    public function __construct(IDashboardService $dashboardService, IResponseService $responseService)
    {
        $this->dashboardService = $dashboardService;
        $this->responseService = $responseService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) : JsonResponse
    {

        $UserID = $request->user()->id;
        $Dashboard = $this->dashboardService->dashboard($UserID);
        return $this->responseService->successResponse($Dashboard->toArray(), SuccessMessages::success);
        //return response()->json($Dashboard, Response::HTTP_OK);
    }
}
