<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

use App\Enums\SuccessMessages;
use App\Services\MyTask\IMyTaskService;
use App\Services\Utilities\Responses\IResponseService;

class MyTaskController extends Controller
{
    private IMyTaskService $mytaskService;
    private IResponseService $responseService;

    public function __construct(IMyTaskService $mytaskService, IResponseService $responseService)
    {
        $this->mytaskService = $mytaskService;
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
        $MyTask = $this->mytaskService->MyTask($UserID);
        return $this->responseService->successResponse($MyTask->toArray(), SuccessMessages::success);
        //return response($MyTask);
    }
}
