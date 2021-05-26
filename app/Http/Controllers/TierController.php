<?php

namespace App\Http\Controllers;

use App\Models\Tier;
use App\Models\TierService;
use Illuminate\Http\Request;
use App\Enums\SuccessMessages;
use App\Http\Requests\Tier\TierRequest;
use App\Repositories\Tier\ITierRepository;
use App\Repositories\Tier\ITierServiceRepository;
use App\Repositories\Tier\ITierRequirementRepository;
use App\Services\Utilities\Responses\IResponseService;

class TierController extends Controller
{

    private ITierRepository $iTierRepository;
    private ITierServiceRepository $tierServiceRepository;
    private ITierRequirementRepository $tierRequirementRepository;
    private IResponseService $responseService;


    public function __construct(
                                ITierRepository $iTierRepository,
                                ITierServiceRepository $tierServiceRepository,
                                ITierRequirementRepository $tierRequirementRepository,
                                IResponseService $responseService
                                )
    {
        // $this->encryptionService = $encryptionService;
        $this->iTierRepository = $iTierRepository;
        $this->tierServiceRepository = $tierServiceRepository;
        $this->tierRequirementRepository = $tierRequirementRepository;
        $this->responseService = $responseService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // dd($this->iTransactionValidationService->transactionValidation("c00375fe-0409-415a-b3fe-c3229535adc9", "0ec43457-9131-11eb-b44f-1c1b0d14e211", 10000));
        // GET REQUEST VALUES
        $params = $request->all();
        $records = $this->iTierRepository->list($params);
        return $this->responseService->successResponse($records->toArray(), SuccessMessages::success);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TierRequest $request)
    {
        $details = $request->validated();
        $details['user_created'] = request()->user()->id;
        $details['user_updated'] = request()->user()->id;
        $createRecord = $this->iTierRepository->create($details);
        return $this->responseService->createdResponse($createRecord->toArray(), SuccessMessages::recordSaved);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Tier $tier)
    {
        return $this->responseService->successResponse($tier->toArray(), SuccessMessages::success);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Tier $tier, TierRequest $request)
    {
        $details = $request->validated();
        $updateRecord = $this->iTierRepository->update($tier, $details);
        return $this->responseService->successResponse($updateRecord->toArray(), SuccessMessages::success);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tier $tier)
    {
        $deleteRecord = $this->iTierRepository->delete($tier);
        return $this->responseService->noContentResponse($deleteRecord->toArray(), SuccessMessages::recordDeleted);
    }

    /**
     * Show all tier upgrade functions
     */

    public function upgrade(Request $request)
    {
        $tierService = $this->tierServiceRepository->getTierDetails();
        return $this->responseService->successResponse($tierService->toArray(), SuccessMessages::success);
        //return response($tierService);
    }

    /**
     * Show all tier requirements
     */

    public function requirements(Request $request)
    {
        $tierRequirement = $this->tierRequirementRepository->getTierRequirements();
        return $this->responseService->successResponse($tierRequirement->toArray(), SuccessMessages::success);
        //return response($tierRequirement);
    }
}
