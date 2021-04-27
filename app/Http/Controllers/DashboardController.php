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
        $request->user()->id = "5bee832d-e14c-44b0-af34-b4821face9f0";
        $UserID = $request->user()->id;
        $Dashboard = $this->dashboardService->dashboard($UserID);
        return $this->responseService->successResponse($Dashboard->toArray(), SuccessMessages::success);
        //return response()->json($Dashboard, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
