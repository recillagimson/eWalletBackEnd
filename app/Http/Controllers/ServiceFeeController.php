<?php

namespace App\Http\Controllers;

use App\Models\ServiceFee;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Enums\SuccessMessages;
use App\Http\Requests\ServiceFee\ServiceFeeRequest;
use App\Repositories\ServiceFee\IServiceFeeRepository;
use App\Services\Utilities\Responses\IResponseService;

class ServiceFeeController extends Controller
{
    private IResponseService $responseService;
    private IServiceFeeRepository $iServiceFeeRepository;


    public function __construct(IResponseService $responseService, 
            IServiceFeeRepository $iServiceFeeRepository)
    {
        $this->responseService = $responseService;
        $this->iServiceFeeRepository = $iServiceFeeRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // GET REQUEST VALUES
        $params = $request->all();
        $records = $this->iServiceFeeRepository->list($params);
        return $this->responseService->successResponse($records->toArray(), SuccessMessages::success);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ServiceFeeRequest $request)
    {
        $details = $request->validated();
        $details['user_created'] = request()->user()->id;
        $details['user_updated'] = request()->user()->id;
        $createRecord = $this->iServiceFeeRepository->create($details);
        return $this->responseService->createdResponse($createRecord->toArray(), SuccessMessages::recordSaved);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(ServiceFee $serviceFee)
    {
        return $this->responseService->successResponse($serviceFee->toArray(), SuccessMessages::success);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ServiceFee $serviceFee, ServiceFeeRequest $request)
    {
        $details = $request->validated();
        $updateRecord = $this->iServiceFeeRepository->update($serviceFee, $details);
        $encryptedResponse = $this->encryptionService->encrypt(array($updateRecord));
        return response()->json($encryptedResponse, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ServiceFee $serviceFee)
    {
        return $this->responseService->noContentResponse($serviceFee->toArray(), SuccessMessages::recordDeleted);
    }
}
