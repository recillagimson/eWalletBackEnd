<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

use App\Enums\SuccessMessages;
use App\Services\Admin\Dashboard\IAdminDashboardService;
use App\Services\Utilities\Responses\IResponseService;

class AdminDashboardController extends Controller
{
    private IAdminDashboardService $admindashboardService;
    private IResponseService $responseService;

    public function __construct(IAdminDashboardService $admindashboardService, IResponseService $responseService)
    {
        $this->admindashboardService = $admindashboardService;
        $this->responseService = $responseService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $UserID = $request->user()->id;
        $Dashboard = $this->admindashboardService->dashboard($UserID);
        return $this->responseService->successResponse($Dashboard, SuccessMessages::success);
        //return response($Dashboard);
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
