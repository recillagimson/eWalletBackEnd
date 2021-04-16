<?php

namespace App\Http\Controllers\api;


use App\Http\Controllers\Controller;
use App\Http\Requests\FundTransfer\BankDetailsRequest;
use App\Http\Resources\FundTransfer\BankDetailsResource;
use App\Services\Encryption\IEncryptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

use App\Services\FundTransfer\IBankListService;
use App\Services\FundTransfer\ICashoutService;

class FundTransferController extends Controller
{
    private IBanklistService $banklistService;
    private ICashoutService $cashoutService;
    private IEncryptionService $encryptionService;
    
    public function __construct(IBankListService $banklistService, ICashoutService $cashoutService, IEncryptionService $encryptionService)
    {
        $this->banklistService = $banklistService;
        $this->cashoutService = $cashoutService;
        $this->encryptionService = $encryptionService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json($this->banklistService->banklist());
    }

    public function InstapayCashout(BankDetailsRequest $request): JsonResponse
    {
        $newCashout = $request->validated();
        $newCashout['user_account_id'] = $request->user()->id;
        $newCashout['sender_recepient_to'] = $request->user()->email;
        $cashout = $this->cashoutService->cashout($newCashout);
        $encryptedResponse = $this->encryptionService->encrypt($cashout->toArray());
        return response()->json($encryptedResponse, Response::HTTP_CREATED);
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
