<?php

namespace App\Http\Controllers;

use App\Enums\SuccessMessages;
use App\Services\Dashboard\IDashboardService;
use App\Services\Dashboard\ForeignExchange\IForeignExchangeRateService;
use App\Services\Utilities\Responses\IResponseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

//use App\Services\Dashboard\ForeignExchange\IForeignExchangeRateService;

class DashboardController extends Controller
{
    private IDashboardService $dashboardService;
    private IResponseService $responseService;
    private IForeignExchangeRateService $foreignExchangeRateService;

    public function __construct(
            IForeignExchangeRateService $foreignExchangeRateService,
            IDashboardService $dashboardService,
            IResponseService $responseService
        )
    {
        $this->dashboardService = $dashboardService;
        $this->responseService = $responseService;
        $this->foreignExchangeRateService = $foreignExchangeRateService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request) : JsonResponse
    {

        $UserID = $request->user()->id;
        $Dashboard = $this->dashboardService->dashboard($UserID);
        return $this->responseService->successResponse($Dashboard->toArray(), SuccessMessages::success);
        //return response()->json($Dashboard, Response::HTTP_OK);
    }

    public function getForeignCurrencyRates() : JsonResponse
    {
        $data = $this->foreignExchangeRateService->getForeignCurrencyRates();
        return $this->responseService->successResponse($data->toArray(), SuccessMessages::success);
    }

    public function getDashboard2022(): JsonResponse
    {
        $response = $this->dashboardService->getDashboard2022();
        return $this->responseService->successResponse($response->first()->toArray());
    }

    public function getTransactionCountDaily(): JsonResponse
    {
        $response = $this->dashboardService->getTransactionCountDaily();
        return $this->responseService->successResponse($response->toArray());
    }

    public function getTransactionCountMonthly(): JsonResponse
    {
        $response = $this->dashboardService->getTransactionCountMonthly();
        return $this->responseService->successResponse($response->toArray());
    }

    public function getTransactionWeekly(): JsonResponse
    {
        $response = $this->dashboardService->getTransactionCountWeekly();
        return $this->responseService->successResponse($response->toArray());
    }

    public function getDailySignups(): JsonResponse
    {
        $response = $this->dashboardService->getDailySignups();
        return $this->responseService->successResponse($response->toArray());
    }

    public function getWeeklySignups(): JsonResponse
    {
        $response = $this->dashboardService->getWeeklySignups();
        return $this->responseService->successResponse($response->toArray());
    }

    public function getMonthlySignups(): JsonResponse
    {
        $response = $this->dashboardService->getMonthlySignups();
        return $this->responseService->successResponse($response->toArray());
    }

}
