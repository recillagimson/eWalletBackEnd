<?php

namespace App\Http\Controllers\UserUtilities;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\UserUtilities\Country\ICountryRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Services\Encryption\IEncryptionService;
use App\Http\Requests\UserUtilities\CountryRequest;
use App\Models\UserUtilities\Country;

class CountryController extends Controller
{

    private IEncryptionService $encryptionService;
    private ICountryRepository $countryRepository;
    
    public function __construct(ICountryRepository $countryRepository,
                                IEncryptionService $encryptionService)
    {
        $this->countryRepository = $countryRepository;
        $this->encryptionService = $encryptionService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $records = $this->countryRepository->getAll();

        $encryptedResponse = $this->encryptionService->encrypt($records->toArray());
        return response()->json($encryptedResponse, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CountryRequest $request
     * @return JsonResponse
     */
    public function store(CountryRequest $request): JsonResponse
    {
        $details = $request->validated();
        $inputBody = $this->inputBody($details, $request->user()->id);
        $createRecord = $this->countryRepository->create($inputBody);

        $encryptedResponse = $this->encryptionService->encrypt($createRecord->toArray());
        return response()->json($encryptedResponse, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  Country $country
     * @return JsonResponse
     */
    public function show(Country $country): JsonResponse
    {
        $encryptedResponse = $this->encryptionService->encrypt($country->toArray());
        return response()->json($encryptedResponse, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function update(CountryRequest $request, Country $country): JsonResponse
    {
        $details = $request->validated();
        $inputBody = $this->inputBody($details, $request->user()->id);
        $updateRecord = $this->countryRepository->update($country, $inputBody);

        $encryptedResponse = $this->encryptionService->encrypt(array($updateRecord));
        return response()->json($encryptedResponse, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy(Country $country): JsonResponse
    {
        $deleteRecord = $this->countryRepository->delete($country);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
