<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceFee\ServiceFeeRequest;
use App\Models\ServiceFee;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\Encryption\IEncryptionService;
use App\Repositories\ServiceFee\IServiceFeeRepository;

class ServiceFeeController extends Controller
{
    private IEncryptionService $encryptionService;
    private IServiceFeeRepository $iServiceFeeRepository;


    public function __construct(IEncryptionService $encryptionService, 
            IServiceFeeRepository $iServiceFeeRepository)
    {
        $this->encryptionService = $encryptionService;
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
    public function store(ServiceFeeRequest $request)
    {
        $details = $request->validated();
        $details['user_created'] = request()->user()->id;
        $details['user_updated'] = request()->user()->id;
        $createRecord = $this->iServiceFeeRepository->create($details);

        $encryptedResponse = $this->encryptionService->encrypt($createRecord->toArray());
        return response()->json($encryptedResponse, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(ServiceFee $serviceFee)
    {
        $encryptedResponse = $this->encryptionService->encrypt($serviceFee->toArray());
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
        $deleteRecord = $this->iTierRepository->delete($serviceFee);
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
