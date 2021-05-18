<?php

namespace App\Http\Controllers\BuyLoad;

use App\Enums\SuccessMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\BuyLoad\ATM\GenerateSignatureRequest;
use App\Http\Requests\BuyLoad\ATM\VerifySignatureRequest;
use App\Http\Requests\BuyLoad\GetProductsByProviderRequest;
use App\Http\Requests\BuyLoad\TopupLoadRequest;
use App\Repositories\OutBuyLoad\IOutBuyLoadRepository;
use App\Repositories\TransactionCategory\ITransactionCategoryRepository;
use App\Services\BuyLoad\IBuyLoadService;
use App\Services\UserProfile\IUserProfileService;
use App\Services\Utilities\PrepaidLoad\ATM\IAtmService;
use App\Services\Utilities\Responses\IResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AtmController extends Controller
{
    private IAtmService $atmService;
    private IResponseService $responseService;
    public IOutBuyLoadRepository $outBuyLoadRepository;
    private IBuyLoadService $buyLoadService;

    public function __construct(IAtmService $atmService,
                                IResponseService $responseService,
                                IOutBuyLoadRepository $outBuyLoadRepository,
                                ITransactionCategoryRepository $transactionCategoryRepository,
                                IUserProfileService $userProfileService,
                                IBuyLoadService $buyLoadService)
    {
        $this->atmService = $atmService;
        $this->responseService = $responseService;
        $this->outBuyLoadRepository = $outBuyLoadRepository;
        $this->buyLoadService = $buyLoadService;
    }

    public function generate(GenerateSignatureRequest $request): JsonResponse
    {
        $data = $request->post();
        $signature = $this->atmService->generateSignature($data);

        $response = [
            'signature' => $signature
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function verify(VerifySignatureRequest $request): JsonResponse
    {
        $signature = $request->header('Signature');
        $data = $request->post();
        $this->atmService->verifySignature($data, $signature);

        $response = [
            'message' => SuccessMessages::success
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function getProductsByProvider(GetProductsByProviderRequest $request): JsonResponse
    {
        $data = $request->validated();
        $mobileNumber = $data['mobile_number'];
        $responseData = $this->buyLoadService->getProductsByProvider($mobileNumber);

        return $this->responseService->successResponse($responseData);
    }

    public function validateLoadTopup(TopupLoadRequest $request): JsonResponse
    {
        $userId = $request->user()->id;
        $data = $request->validated();

        $this->buyLoadService->validateLoadTopup($userId, $data['mobile_number'], $data['product_code'],
            $data['product_name'], $data['amount']);

        return $this->responseService->successResponse([],
            SuccessMessages::transactionValidationSuccessful);
    }

    public function topupLoad(TopupLoadRequest $request): JsonResponse
    {
        $userId = $request->user()->id;
        $data = $request->validated();

        $response = $this->buyLoadService->topupLoad($userId, $data['mobile_number'], $data['product_code'],
            $data['product_name'], $data['amount']);

        return $this->responseService->successResponse($response);
    }

    public function processPending(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $response = $this->buyLoadService->processPending($userId);
        return $this->responseService->successResponse($response);
    }
}
