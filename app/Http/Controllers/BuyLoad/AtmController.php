<?php

namespace App\Http\Controllers\BuyLoad;

use Illuminate\Http\Request;
use App\Enums\SuccessMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\BuyLoad\ATM\GenerateSignatureRequest;
use App\Http\Requests\BuyLoad\ATM\VerifySignatureRequest;
use App\Services\Utilities\PrepaidLoad\ATM\IAtmService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Services\Utilities\Responses\IResponseService;
use App\Http\Requests\PrepaidLoad\ATMTopUpRequest;
use App\Repositories\OutBuyLoad\IOutBuyLoadRepository;
use Carbon\Carbon;
use App\Repositories\TransactionCategory\ITransactionCategoryRepository;
use App\Enums\TransactionCategories;
use App\Services\UserProfile\IUserProfileService;

class AtmController extends Controller
{
    private IAtmService $atmService;
    private IResponseService $responseService;
    public IOutBuyLoadRepository $outBuyLoadRepository;
    private ITransactionCategoryRepository $transactionCategoryRepository;
    private IUserProfileService $userProfileService;

    public function __construct(IAtmService $atmService,
                                IResponseService $responseService,
                                IOutBuyLoadRepository $outBuyLoadRepository,
                                ITransactionCategoryRepository $transactionCategoryRepository,
                                IUserProfileService $userProfileService)
    {
        $this->atmService = $atmService;
        $this->responseService = $responseService;
        $this->outBuyLoadRepository = $outBuyLoadRepository;
        $this->transactionCategoryRepository = $transactionCategoryRepository;
        $this->userProfileService = $userProfileService;
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

    public function showPrefixNetworkList() {
        $records = $this->atmService->showNetworkAndPrefix();

        return $this->responseService->successResponse($records, SuccessMessages::success);
    }

    public function showProductList() {
        $records = $this->atmService->showProductList();

        return $this->responseService->successResponse($records, SuccessMessages::success);
    }

    public function load(ATMTopUpRequest $atmrequest): JsonResponse {
        $details = $atmrequest->validated();
        $copyATMDetails = $details;
        unset($copyATMDetails["amount"]);
        $buyLoadTransactionCategory = $this->transactionCategoryRepository->getByName(TransactionCategories::BuyLoad);

        $records = $this->atmService->atmload($copyATMDetails);
        
        $createOutBuyLoadBody = $this->createOutBuyLoadBody($details, $records->data, $atmrequest->user(), $buyLoadTransactionCategory);
        $addUserCreateAndUpdate = $this->userProfileService->addUserInput($createOutBuyLoadBody, $atmrequest->user());

        $result = ($records->responseCode == 101) ? 
        $this->outBuyLoadRepository->create($addUserCreateAndUpdate)->toArray() :
        array($records);

        return $this->responseService->successResponse($result, SuccessMessages::success);
    }

    private function createOutBuyLoadBody(array $details, object $records, object $user, object $buyLoadTransactionCategory):array {
        $body = [
            'user_account_id'=> $user->id,
            'total_amount'=> $details["amount"],
            'transaction_date' => Carbon::now(),
            'transaction_category_id'=>$buyLoadTransactionCategory->id,
            'reference_number'=>$records->referenceNo,
            'atm_reference_number'=>$records->transactionNo,
        ];

        return $body;
    }
}
