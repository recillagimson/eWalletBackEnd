<?php

namespace App\Http\Controllers;

use App\Models\IdType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\IdType\IdTypeRequest;
use App\Repositories\IdType\IIdTypeRepository;
use App\Services\Encryption\IEncryptionService;
use App\Services\Transaction\ITransactionService;

class IdTypeController extends Controller
{

    private IIdTypeRepository $idTypeRepository;
    private IEncryptionService $encryptionService;
    private ITransactionService $transaction;

    public function __construct(IIdTypeRepository $idTypeRepository, IEncryptionService $encryptionService, ITransactionService $transaction)
    {
        $this->idTypeRepository = $idTypeRepository;
        $this->encryptionService = $encryptionService;
        $this->transaction = $transaction;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): JsonResponse 
    {
        $records = $this->idTypeRepository->getAll();
        $encryptedResponse = $this->encryptionService->encrypt($records->toArray());
        return response()->json($encryptedResponse, Response::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        $encryptedResponse = $this->encryptionService->encrypt($createRecord->toArray());
        return response()->json($encryptedResponse, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(IdType $idType) : JsonResponse 
    {
        $encryptedResponse = $this->encryptionService->encrypt($idType->toArray());
        return response()->json($encryptedResponse, Response::HTTP_OK);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
    public function update(IdTypeRequest $request, IdType $idType) : JsonResponse 
    {
        $details = $request->validated();
        $details['user_updated'] = request()->user()->id;
        // $inputBody = $this->inputBody($details);
        $updateRecord = $this->idTypeRepository->update($idType, $details);

        $encryptedResponse = $this->encryptionService->encrypt(array($updateRecord));
        return response()->json($encryptedResponse, Response::HTTP_OK);
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

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    // CLEAR FOR TESTING ONLY
    public function testing() {
        $record = $this->transaction->addAvailableBalance("1", "0ed24251-9131-11eb-b44f-1c1b0d14e211", 123, 123);
        $encryptedResponse = $this->encryptionService->encrypt(array($record));
        return response()->json($encryptedResponse, Response::HTTP_OK);
    }
}
