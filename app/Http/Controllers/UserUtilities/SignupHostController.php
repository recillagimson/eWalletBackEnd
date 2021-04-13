<?php

namespace App\Http\Controllers\UserUtilities;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\UserUtilities\SignupHost\ISignupHostRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Services\Encryption\IEncryptionService;
use App\Http\Requests\UserUtilities\SignupHostRequest;
use App\Models\UserUtilities\SignupHost;

class SignupHostController extends Controller
{

    private IEncryptionService $encryptionService;
    private ISignupHostRepository $signupHostRepository;
    
    public function __construct(ISignupHostRepository $signupHostRepository,
                                IEncryptionService $encryptionService)
    {
        $this->signupHostRepository = $signupHostRepository;
        $this->encryptionService = $encryptionService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $records = $this->signupHostRepository->getAll();

        $encryptedResponse = $this->encryptionService->encrypt($records->toArray());
        return response()->json($encryptedResponse, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  SignupHostRequest $request
     * @return JsonResponse
     */
    public function store(SignupHostRequest $request): JsonResponse
    {
        $details = $request->validated();
        $inputBody = $this->inputBody($details, $request->user()->id);
        $createRecord = $this->signupHostRepository->create($inputBody);

        $encryptedResponse = $this->encryptionService->encrypt($createRecord->toArray());
        return response()->json($encryptedResponse, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  SignupHost $signup_host
     * @return JsonResponse
     */
    public function show(SignupHost $signup_host): JsonResponse
    {
        $encryptedResponse = $this->encryptionService->encrypt($signup_host->toArray());
        return response()->json($encryptedResponse, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  SignupHostRequest $request
     * @param  SignupHost $signup_host
     * @return JsonResponse
     */
    public function update(SignupHostRequest $request, SignupHost $signup_host): JsonResponse
    {
        $details = $request->validated();
        $inputBody = $this->inputBody($details, $request->user()->id);
        $updateRecord = $this->signupHostRepository->update($signup_host, $inputBody);

        $encryptedResponse = $this->encryptionService->encrypt(array($updateRecord));
        return response()->json($encryptedResponse, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  SignupHost $signup_host
     * @return JsonResponse
     */
    public function destroy(SignupHost $signup_host): JsonResponse
    {
        $deleteRecord = $this->signupHostRepository->delete($signup_host);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
