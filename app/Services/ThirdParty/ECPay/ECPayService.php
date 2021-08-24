<?php


namespace App\Services\ThirdParty\ECPay;


use App\Enums\TpaProviders;
use App\Services\Utilities\API\IApiService;
use App\Traits\Errors\WithTpaErrors;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Str;
use App\Services\Utilities\XML\XmlService;
use Illuminate\Support\Stringable;
use App\Services\AddMoney\DragonPay\IHandlePostBackService;
use App\Repositories\InAddMoneyEcPay\IInAddMoneyEcPayRepository;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use App\Services\Utilities\ReferenceNumber\IReferenceNumberService;
use App\Enums\ReferenceNumberTypes;
use App\Repositories\TransactionCategory\ITransactionCategoryRepository;
use App\Enums\TransactionCategoryIds;
use App\Enums\SquidPayModuleTypes;
use App\Services\Utilities\LogHistory\ILogHistoryService;
use App\Repositories\UserTransactionHistory\IUserTransactionHistoryRepository;
use App\Services\Utilities\Responses\IResponseService;
use App\Enums\SuccessMessages;
use App\Enums\ECPayStatusTypes;

class ECPayService implements IECPayService
{
    use WithTpaErrors;

    private string $ecpayUrl;

    private string $username;
    private string $password;

    private IApiService $apiService;
    private IHandlePostBackService $handlePostBackService;
    private IInAddMoneyEcPayRepository $addMoneyEcPayRepository;
    private IReferenceNumberService $referenceNumberService;
    private ITransactionCategoryRepository $transactionCategoryRepository;
    private ILogHistoryService $logHistoryService;
    private IUserTransactionHistoryRepository $userTransactionHistoryRepository;
    private IResponseService $responseService;

    public function __construct(IApiService $apiService,
                                IHandlePostBackService $handlePostBackService,
                                IInAddMoneyEcPayRepository $addMoneyEcPayRepository,
                                IReferenceNumberService $referenceNumberService,
                                ITransactionCategoryRepository $transactionCategoryRepository,
                                ILogHistoryService $logHistoryService,
                                IUserTransactionHistoryRepository $userTransactionHistoryRepository,
                                IResponseService $responseService)
    {

        $this->ecpayUrl = config('ecpay.ecpay_url');

        $this->username = config('ecpay.ecpay_username');
        $this->password = config('ecpay.ecpay_password');

        $this->apiService = $apiService;
        $this->handlePostBackService = $handlePostBackService;
        $this->addMoneyEcPayRepository = $addMoneyEcPayRepository;
        $this->referenceNumberService = $referenceNumberService;
        $this->transactionCategoryRepository = $transactionCategoryRepository;
        $this->logHistoryService = $logHistoryService;
        $this->userTransactionHistoryRepository = $userTransactionHistoryRepository;
        $this->responseService = $responseService;
    }

    private function getXmlHeaders(): array
    {
        return [
            'Content-Type' => 'text/xml'
        ];
    }


    public function commitPayment(array $data, object $user): object
    {
        $result = $this->generateXmlBody($data, "CommitPayment");
        $response = $this->apiService->postXml($this->ecpayUrl, $result, $this->getXmlHeaders());
        $xmlData = $this->xmlBodyParser($response->body());
        $jsondecode = json_decode($xmlData->soapBody->CommitPaymentResponse->CommitPaymentResult, true)[0];

        if($jsondecode['resultCode'] != "0") throw ValidationException::withMessages(['Message' => 'Add money Failed']);

        $result = $this->createOrUpdateTransaction($jsondecode, $data, $user);

        return $this->responseService->successResponse(
            $data,
            SuccessMessages::addMoneySuccess
        );
    }

    public function confirmPayment(array $data, object $user): object
    {
        $result = $this->generateXmlBody($data, "ConfirmPayment");
        $response = $this->apiService->postXml($this->ecpayUrl, $result, $this->getXmlHeaders());
        $xmlData = $this->xmlBodyParser($response->body());
        $jsondecode = json_decode($xmlData->soapBody->ConfirmPaymentResponse->ConfirmPaymentResult, true)[0];

        if($jsondecode['resultCode'] != "0") throw ValidationException::withMessages(['Message' => 'Add money Failed']);

        $result = $this->createOrUpdateTransaction($jsondecode, $data, $user);

        return $this->responseService->successResponse(
            $data,
            SuccessMessages::addMoneySuccess
        );
    }

    private function xmlBodyParser(string $data): object {
        $strXml = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $data);
        $xmlData = simplexml_load_string($strXml);

        return $xmlData;
    }

    private function createOrUpdateTransaction(array $data, array $inputData, object $user) {
        $isDataExisting = $this->addMoneyEcPayRepository->getDataByReferenceNumber($inputData['referenceno']);
        $transCategoryId = $this->transactionCategoryRepository->getById(TransactionCategoryIds::sendMoneyToSquidPayAccount);
       
        if($isDataExisting) {
            $amount = $isDataExisting->amount;
            $refNo = $isDataExisting->reference_number;
            $logStringResult = 'Successfully updated money from EcPay with amount of ' . $amount;
            $this->addMoneyEcPayRepository->update($isDataExisting, [
                'transaction_remarks'=>$data['result'],
                'status' => ECPayStatusTypes::Success
            ]);
        } else {
            $amount = $inputData['amount'];
            $refNo = $this->referenceNumberService->generate(ReferenceNumberTypes::AddMoneyViaOTC);
            $isDataExisting = $this->addMoneyEcPayRepository->create($this->createBodyFormat($data, $inputData, $user, $refNo, $transCategoryId));
            $logStringResult = 'Successfully added money from EcPay with amount of ' . $amount;
        }
       
        $this->handlePostBackService->addAmountToUserBalance($user->id, $amount);

        $this->logHistoryService->logUserHistoryUnauthenticated($user->id, $refNo, SquidPayModuleTypes::AddMoneyViaOTCECPay, __METHOD__, Carbon::now(), $logStringResult);

        $this->userTransactionHistoryRepository->log(
            $user->id,
            $transCategoryId->id,
            $isDataExisting->id,
            $isDataExisting->reference_number,
            $isDataExisting->amount,
            Carbon::parse($isDataExisting->transaction_date),
            $isDataExisting->user_account_id
        );
        
        return $isDataExisting;
    }

    private function createBodyFormat(array $data, array $inputData, object $user, string $refNo, object $transCategoryId) {

        $result = [
            "user_account_id"=>$user->id,
            "reference_number"=>$refNo,
            "amount"=>$inputData['amount'],
            "total_amount"=>$inputData['amount'],
            "ec_pay_reference_number"=>$inputData['referenceno'],
            "expiry_date"=>Carbon::parse($inputData['expirydate'])->format('Y-m-d H:i:s'),
            "transaction_date"=>Carbon::now()->format('Y-m-d H:i:s'),
            "transction_category_id"=>$transCategoryId->id,
            "transaction_remarks"=>"Send Money via Ecpay",
            "user_created"=>$user->id,
            "user_updated"=>$user->id,
            "updated_at"=>Carbon::now()->format('Y-m-d H:i:s')
        ];
       
        return $result;
    }

    private function generateXmlBody(array $data, string $title): string
    {
        $result = $this->startXmlQuery() . $this->headerXmlQuery() . $this->bodyXmlQuery($data, $title). $this->endXmlQuery();
        $trimResult = trim(preg_replace('/\s\s+/', ' ', $result));

        return $trimResult;
    }


    private function startXmlQuery()
    {
        $str = '
        <?xml version="1.0" encoding="utf-8"?>
        <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
        ';
    
        return $str;
    }

    private function headerXmlQuery() {
        $str = '
        <soap:Header>
        <AuthHeader xmlns="https://ecpay.ph/eclink">
        <merchantCode>'.$this->username.'</merchantCode>
        <merchantKey>'.$this->password.'</merchantKey>
        </AuthHeader>
        </soap:Header>
        ';
    
        return $str;
    }

    private function bodyXmlQuery(array $data, string $title) {
        $str = '
        <soap:Body>
        <'.$title.' xmlns="https://ecpay.ph/eclink">';
        
        foreach($data as $key=>$val) {
            $str .= "<{$key}>{$val}</{$key}>";
        }

        $str .= '</'.$title.'>
        </soap:Body>
        ';

        return $str;
    }

    private function endXmlQuery() {
        $str = '
        </soap:Envelope>
        ';
    
        return $str;
    }
}
