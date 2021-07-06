<?php

namespace App\Http\Controllers;

use App\Enums\SuccessMessages;
use App\Services\Admin\Dashboard\IAdminDashboardService;
use App\Services\Utilities\Responses\IResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AdminDashboardController extends Controller
{
    private IAdminDashboardService $admindashboardService;
    private IResponseService $responseService;

    public function __construct(IAdminDashboardService $admindashboardService,
                                IResponseService $responseService)
    {
        $this->admindashboardService = $admindashboardService;
        $this->responseService = $responseService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        //$UserID = $request->user()->id;
        $Dashboard = $this->admindashboardService->dashboard();
        return $this->responseService->successResponse($Dashboard, SuccessMessages::success);
        //return response($Dashboard);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request): Response
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id): Response
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id): Response
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id): Response
    {
        //
    }
}
