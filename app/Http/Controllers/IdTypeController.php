<?php

namespace App\Http\Controllers;

use App\Models\IdType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Enums\SuccessMessages;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\IdType\IdTypeRequest;
use App\Repositories\IdType\IIdTypeRepository;
use App\Services\Utilities\Responses\IResponseService;

class IdTypeController extends Controller
{

    private IIdTypeRepository $idTypeRepository;
    private IResponseService $responseService;


    public function __construct(IIdTypeRepository $idTypeRepository, IResponseService $responseService)
    {
        $this->idTypeRepository = $idTypeRepository;
        $this->responseService = $responseService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): JsonResponse 
    {
        $is_primary = 2;
        if($request->has('is_primary')) {
            $is_primary = $request->is_primary;
        }
        $records = $this->idTypeRepository->getIdType($is_primary);
        return $this->responseService->successResponse($records->toArray(), SuccessMessages::success);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(IdTypeRequest $request) : JsonResponse 
    {
        $details = $request->validated();
        $details['user_created'] = request()->user()->id;
        $details['user_updated'] = request()->user()->id;
        $createRecord = $this->idTypeRepository->create($details);
        return $this->responseService->createdResponse($createRecord->toArray(), SuccessMessages::recordSaved);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(IdType $idType) : JsonResponse 
    {
        return $this->responseService->successResponse($idType->toArray(), SuccessMessages::success);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(IdTypeRequest $request, IdType $idType) : JsonResponse 
    {
        $details = $request->validated();
        $details['user_updated'] = request()->user()->id;
        // $inputBody = $this->inputBody($details);
        $updateRecord = $this->idTypeRepository->update($idType, $details);
        return $this->responseService->successResponse($updateRecord->toArray(), SuccessMessages::success);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(IdType $idType): JsonResponse
    {
        $deleteRecord = $this->idTypeRepository->delete($idType);
        return $this->responseService->noContentResponse($deleteRecord->toArray(), SuccessMessages::recordDeleted);
    }
}
