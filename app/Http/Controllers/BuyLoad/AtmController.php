<?php

namespace App\Http\Controllers\BuyLoad;

use App\Enums\TopupTypes;
use App\Enums\UsernameTypes;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Enums\SuccessMessages;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\BuyLoad\IBuyLoadService;
use App\Services\SendMoney\ISendMoneyService;
use App\Http\Requests\BuyLoad\TopupLoadRequest;
use App\Services\UserProfile\IUserProfileService;
use App\Repositories\OutBuyLoad\IOutBuyLoadRepository;
use App\Services\Utilities\Responses\IResponseService;
use App\Services\Utilities\PrepaidLoad\ATM\IAtmService;
use App\Http\Requests\BuyLoad\ATM\VerifySignatureRequest;
use App\Http\Requests\BuyLoad\ATM\GenerateSignatureRequest;
use App\Http\Requests\BuyLoad\GetProductsByProviderRequest;
use App\Repositories\UserUtilities\UserDetail\IUserDetailRepository;
use App\Repositories\TransactionCategory\ITransactionCategoryRepository;
use App\Repositories\UserAccount\IUserAccountRepository;

class AtmController extends Controller
{
    private IAtmService $atmService;
    private IResponseService $responseService;
    public IOutBuyLoadRepository $outBuyLoadRepository;
    private IBuyLoadService $buyLoadService;
    private ISendMoneyService $sendMoneyService;
    // private IUserDetailRepository $userDetail;
    private IUserAccountRepository $accountRepository;


    public function __construct(IAtmService $atmService,
                                IResponseService $responseService,
                                IOutBuyLoadRepository $outBuyLoadRepository,
                                ITransactionCategoryRepository $transactionCategoryRepository,
                                IUserProfileService $userProfileService,
                                IBuyLoadService $buyLoadService,
                                ISendMoneyService $sendMoneyService,
                                IUserAccountRepository $accountRepository)
    {
        $this->atmService = $atmService;
        $this->responseService = $responseService;
        $this->outBuyLoadRepository = $outBuyLoadRepository;
        $this->buyLoadService = $buyLoadService;
        $this->sendMoneyService = $sendMoneyService;
        $this->accountRepository = $accountRepository;
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

    public function getEpinProducts(): JsonResponse
    {
        $responseData = $this->buyLoadService->getEpinProducts();
        return $this->responseService->successResponse($responseData);
    }

    public function getProductsByProvider(GetProductsByProviderRequest $request): JsonResponse
    {
        $data = $request->validated();
        $mobileNumber = $data['mobile_number'];
        $responseData = $this->buyLoadService->getProductsByProvider($mobileNumber);
        return $this->responseService->successResponse($responseData);
    }

    private function getUsernameField(Request $request): string
    {
        return $request->has(UsernameTypes::Email) ? UsernameTypes::Email : UsernameTypes::MobileNumber;
    }

    public function validateTopup(TopupLoadRequest $request): JsonResponse
    {
        $userId = $request->user()->id;
        $data = $request->validated();
        
        $data['amount'] = (int)filter_var($data['product_name'], FILTER_SANITIZE_NUMBER_INT);
        
        $this->buyLoadService->validateTopup($userId, $data['mobile_number'], $data['product_code'],
            $data['product_name'], $data['amount']);

        return $this->responseService->successResponse($data,
            SuccessMessages::transactionValidationSuccessful);
    }

    public function topupLoad(TopupLoadRequest $request): JsonResponse
    {
        $userId = $request->user()->id;
        $data = $request->validated();

        $data['amount'] = (int)filter_var($data['product_name'], FILTER_SANITIZE_NUMBER_INT);

        $disabledNetwork = $this->buyLoadService->executeDisabledNetwork($data['mobile_number']);

        $response = $this->buyLoadService->topup($userId, $data['mobile_number'], $data['product_code'],
            $data['product_name'], $data['amount'], TopupTypes::load);

        return $this->responseService->successResponse($response);
    }

    public function topupEPins(TopupLoadRequest $request): JsonResponse
    {
        $userId = $request->user()->id;
        $data = $request->validated();

        $response = $this->buyLoadService->topup($userId, $data['mobile_number'], $data['product_code'],
            $data['product_name'], $data['amount'], TopupTypes::epins);

        return $this->responseService->successResponse($response);
    }

    public function processPending(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $response = $this->buyLoadService->processPending($userId);
        return $this->responseService->successResponse($response);
    }
}
