<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tier\TierRequest;
use App\Models\Tier;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\Tier\ITierRepository;
use App\Services\Encryption\IEncryptionService;

class TierController extends Controller
{

    private IEncryptionService $encryptionService;
    private ITierRepository $iTierRepository;


    public function __construct(IEncryptionService $encryptionService, 
                                ITierRepository $iTierRepository)
    {
        $this->encryptionService = $encryptionService;
        $this->iTierRepository = $iTierRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function GetAll()
    {
        $records = $this->iTierRepository->getAll();
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
    public function store(TierRequest $request)
    {
        $details = $request->validated();
        $details['user_created'] = request()->user()->id;
        $details['user_updated'] = request()->user()->id;
        $createRecord = $this->iTierRepository->create($details);

        $encryptedResponse = $this->encryptionService->encrypt($createRecord->toArray());
        return response()->json($encryptedResponse, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Tier $tier)
    {
        $encryptedResponse = $this->encryptionService->encrypt($tier->toArray());
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
    public function update(Tier $tier, TierRequest $request)
    {
        $details = $request->validated();
        $updateRecord = $this->iTierRepository->update($tier, $details);

        $encryptedResponse = $this->encryptionService->encrypt(array($updateRecord));
        return response()->json($encryptedResponse, Response::HTTP_OK);
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
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
