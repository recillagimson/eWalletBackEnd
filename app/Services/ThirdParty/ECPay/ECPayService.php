<?php


namespace App\Services\ThirdParty\ECPay;


use App\Enums\TpaProviders;
use App\Models\UserDetail;
use App\Repositories\Notification\INotificationRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use Log;
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
use App\Enums\ECPayStatusTypes;
use App\Enums\ReferenceNumberTypes;
use App\Enums\SquidPayModuleTypes;
use App\Enums\SuccessMessages;
use App\Enums\TransactionCategoryIds;
use App\Repositories\TransactionCategory\ITransactionCategoryRepository;
use App\Repositories\UserBalanceInfo\IUserBalanceInfoRepository;
use App\Repositories\UserTransactionHistory\IUserTransactionHistoryRepository;
use App\Repositories\UserUtilities\UserDetail\IUserDetailRepository;
use App\Services\Utilities\LogHistory\ILogHistoryService;
use App\Services\Utilities\Notifications\Email\IEmailService;
use App\Services\Utilities\Notifications\SMS\ISmsService;
use App\Services\Utilities\Responses\IResponseService;

class ECPayService implements IECPayService
{
    use WithTpaErrors;

    private string $ecpayUrl;

    private string $username;
    private string $password;

    private int $serviceFee;

    private IApiService $apiService;
    private IHandlePostBackService $handlePostBackService;
    private IInAddMoneyEcPayRepository $addMoneyEcPayRepository;
    private IReferenceNumberService $referenceNumberService;
    private ITransactionCategoryRepository $transactionCategoryRepository;
    private ILogHistoryService $logHistoryService;
    private IUserTransactionHistoryRepository $userTransactionHistoryRepository;
    private IResponseService $responseService;
    private IUserDetailRepository $userDetailRepository;
    private IEmailService $emailService;
    private ISmsService $smsService;
    private IUserBalanceInfoRepository $balanceInfos;
    private IUserAccountRepository $userAccounts;
    private IInAddMoneyEcPayRepository $ecPayAddMoneyRepository;
    private INotificationRepository $appNotifications;

    public function __construct(IApiService $apiService,
                                IHandlePostBackService $handlePostBackService,
                                IInAddMoneyEcPayRepository $addMoneyEcPayRepository,
                                IReferenceNumberService $referenceNumberService,
                                ITransactionCategoryRepository $transactionCategoryRepository,
                                ILogHistoryService $logHistoryService,
                                IUserTransactionHistoryRepository $userTransactionHistoryRepository,
                                IResponseService $responseService,
                                IUserDetailRepository $userDetailRepository,
                                IEmailService $emailService,
                                ISmsService $smsService,
                                IUserBalanceInfoRepository $balanceInfos,
                                IUserAccountRepository $userAccounts,
                                IInAddMoneyEcPayRepository $ecPayAddMoneyRepository,
                                INotificationRepository $appNotifications)
    {

        $this->ecpayUrl = config('ecpay.ecpay_url');

        $this->username = config('ecpay.ecpay_username');
        $this->password = config('ecpay.ecpay_password');
        $this->expirationPerHour = config('ecpay.ecpay_expiration_per_hour');

        $this->apiService = $apiService;
        $this->handlePostBackService = $handlePostBackService;
        $this->addMoneyEcPayRepository = $addMoneyEcPayRepository;
        $this->referenceNumberService = $referenceNumberService;
        $this->transactionCategoryRepository = $transactionCategoryRepository;
        $this->logHistoryService = $logHistoryService;
        $this->userTransactionHistoryRepository = $userTransactionHistoryRepository;
        $this->responseService = $responseService;
        $this->userDetailRepository = $userDetailRepository;
        $this->emailService = $emailService;
        $this->smsService = $smsService;
        $this->balanceInfos = $balanceInfos;
        $this->serviceFee = 2;
        $this->userAccounts = $userAccounts;
        $this->ecPayAddMoneyRepository = $ecPayAddMoneyRepository;
        $this->appNotifications = $appNotifications;
    }

    private function getXmlHeaders(): array
    {
        return [
            'Content-Type' => 'text/xml'
        ];
    }


    public function commitPayment(array $data, object $user): object
    {
        $refNo = $this->referenceNumberService->generateRefNoWithThirteenLength(ReferenceNumberTypes::AddMoneyViaOTC);
        $expirationDate = Carbon::now()->addDays($this->expirationPerHour)->format('Y-m-d H:i:s');
        $result = $this->generateXmlBody($this->createBodyCommitPaymentFormat($refNo, $expirationDate, $data), "CommitPayment");
        $response = $this->apiService->postXml($this->ecpayUrl, $result, $this->getXmlHeaders());
        $xmlData = $this->xmlBodyParser($response->body());
        $jsondecode = json_decode($xmlData->soapBody->CommitPaymentResponse->CommitPaymentResult, true)[0];

        \Log::info('///// - ECPAY Commit Payment - //////');
        \Log::info(json_encode($jsondecode));
        if($jsondecode['resultCode'] != "0") throw ValidationException::withMessages(['Message' => 'Add money Failed']);

        $result = $this->createOrUpdateTransaction($jsondecode, $data, $user, $refNo, $expirationDate);

        $res = $this->returnResponseBodyFormat($user, $result);

        return $this->responseService->successResponse(
            $res,
            SuccessMessages::addMoneySuccess
        );
    }

    public function confirmPayment(array $data, object $user): object
    {
        \Log::info('///// - ECPAY Confirm Payment - //////');
        $res = $this->processConfirmPayment($data, $user);

        return $this->responseService->successResponse(
            $res,
            SuccessMessages::addMoneySuccess
        );
    }

    public function batchConfirmPayment(string $userId): array
    {
        $user = $this->userAccounts->getUser($userId);
        $data = $this->ecPayAddMoneyRepository->getRefNoInPendingStatusFromUser($userId);
        $arr = [];

        foreach($data as $refno) {
            $ref = ["referenceno" => $refno->reference_number];
            array_push($arr, $this->processConfirmPayment($ref, $user));
        }

        return $arr;
    }

    private function processConfirmPayment(array $data, object $user): array {
        $result = $this->generateXmlBody($data, "ConfirmPayment");
        $response = $this->apiService->postXml($this->ecpayUrl, $result, $this->getXmlHeaders());
        $xmlData = $this->xmlBodyParser($response->body());
        $jsondecode = json_decode($xmlData->soapBody->ConfirmPaymentResponse->ConfirmPaymentResult, true)[0];

        \Log::info('///// - ECPAY Process Confirm Payment - //////');
        \Log::info(json_encode($jsondecode));
        if($jsondecode['resultCode'] != "0") throw ValidationException::withMessages(['Message' => 'Add money Failed']);

        $result = $this->createOrUpdateTransaction($jsondecode, $data, $user, $data["referenceno"]);

        $res = $this->returnResponseBodyFormat($user, $result);

        return $res;
    }

    private function xmlBodyParser(string $data): object {
        $strXml = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $data);
        $xmlData = simplexml_load_string($strXml);

        return $xmlData;
    }

    private function createOrUpdateTransaction(array $data, array $inputData, object $user, string $refNo, string $expirationDate="") {
        $isDataExisting = $this->addMoneyEcPayRepository->getDataByReferenceNumber($refNo);
        $transCategoryId = $this->transactionCategoryRepository->getById(TransactionCategoryIds::adMoneyEcPay);

        if($isDataExisting) {
            $amount = $isDataExisting->amount;
            $refNo = $isDataExisting->reference_number;
            $logStringResult = 'Successfully updated money from EcPay with amount of ' . $amount;
            $resultData = json_decode($data["result"]);

            \Log::info('///// - ECPAY Create or Update Payment - //////');
            \Log::info(json_encode($data));
            if(!$resultData) throw ValidationException::withMessages(['Message' => 'Add money Failed']);

            $this->addMoneyEcPayRepository->update($isDataExisting, [
                'transaction_response'=>$data['result'],
                'status' => ($resultData[0]->PaymentStatus == 0) ? ECPayStatusTypes::Success : ECPayStatusTypes::Pending
            ]);
            $remainingBalance = ($resultData[0]->PaymentStatus == 0) ? $this->handlePostBackService->addAmountToUserBalance($user->id, $amount) : '';
            if($resultData[0]->PaymentStatus == 0) $this->sendNotification($this->getUserBalance(request()->user()->id), $refNo, $isDataExisting->transaction_date);
        } else {
            $amount = $inputData['amount'];
            $isDataExisting = $this->addMoneyEcPayRepository->create($this->createBodyFormat($data, $inputData, $user, $refNo, $transCategoryId, $expirationDate));
            $this->addMoneyEcPayRepository->update($isDataExisting, [
                'status' => ECPayStatusTypes::Pending
            ]);
            $logStringResult = 'Successfully added money from EcPay with amount of ' . $amount;
        }

        $this->logHistoryService->logUserHistoryUnauthenticated($user->id, $refNo, SquidPayModuleTypes::AddMoneyViaOTCECPay, __METHOD__, Carbon::now(), $logStringResult);

        $this->userTransactionHistoryRepository->log(
            $user->id,
            $transCategoryId->id,
            $isDataExisting->id,
            $isDataExisting->reference_number,
            $this->amountWithServiceFee((float)$isDataExisting->amount),
            Carbon::parse($isDataExisting->transaction_date),
            $isDataExisting->user_account_id
        );

        return $isDataExisting;
    }

    private function createBodyCommitPaymentFormat($refNo, $expirationDate, $data): array
    {
        $result = [
            "referenceno" => $refNo,
            "amount" => $data['amount'],
            "expirydate" => $expirationDate,
            "remarks" => "Send Money via Ecpay",
        ];

        return $result;
    }

    private function createBodyFormat(array $data, array $inputData, object $user, string $refNo, object $transCategoryId, string $expirationDate) {
        $ecpayResult = explode("|", $data["result"]);
        $result = [
            "user_account_id"=>$user->id,
            "reference_number"=>$refNo,
            "amount"=>$inputData['amount'],
            "total_amount"=>$inputData['amount'],
            'service_fee'=>$this->serviceFee,
            "ec_pay_reference_number"=>$ecpayResult[0],
            "expiry_date"=>$expirationDate,
            "transaction_date"=>Carbon::now()->format('Y-m-d H:i:s'),
            "transction_category_id"=>$transCategoryId->id,
            "transaction_remarks"=>"Send Money via Ecpay",
            "user_created"=>$user->id,
            "user_updated"=>$user->id,
            "updated_at"=>Carbon::now()->format('Y-m-d H:i:s'),
            'transaction_response'=> json_encode($data)
        ];

        return $result;
    }

    private function returnResponseBodyFormat(object $user, $data)
    {
        $userDetail = $this->userDetailRepository->getByUserId($user->id);
        $result = [
            "email"=>$user->email,
            "mobile_number"=>$user->mobile_number,
            "user_account_id"=>$user->id,
            "account_number"=>$user->account_number,
            "last_name"=>$userDetail->last_name,
            "first_name"=>$userDetail->first_name,
            "middle_name"=>$userDetail->middle_name,
            "reference_number"=>$data["reference_number"],
            "amount"=>$data["amount"],
            'service_fee'=>$this->serviceFee,
            'total_amount'=> (string)$this->amountWithServiceFee((float)$data["amount"]),
            "expiry_date"=>Carbon::parse($data["expiry_date"])->format('Y-m-d H:i:s P'),
            "ec_pay_reference_number"=>$data["ec_pay_reference_number"],
            "transaction_date"=>Carbon::parse($data["transaction_date"])->format('Y-m-d H:i:s P'),
            "status"=>$data["status"]
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

    private function getUserBalance(string $userAccountID) {
        $userBalanceInfo = $this->balanceInfos->getByUserAccountID($userAccountID);

        return $userBalanceInfo->available_balance;
    }

    private function sendNotification(float $newBalance, string $referenceNumber, Carbon $transactionDate): void {
        if(request()->user() && request()->user()->is_login_email == 0) {
            // SMS USER FOR NOTIFICATION
            $this->smsService->sendEcPaySuccessPaymentNotification(request()->user()->mobile_number, request()->user()->profile, $newBalance, $referenceNumber, $transactionDate);
        }else {
            // EMAIL USER FOR NOTIFICATION
            $this->emailService->sendEcPaySuccessPaymentNotification(request()->user()->email, request()->user()->profile, $newBalance, $referenceNumber, $transactionDate);
        }

        $this->createAppNotification(request()->user()->id, $newBalance, $referenceNumber, $transactionDate);
    }

    private function createAppNotification(string $userId, float $newBalance, string $refNo, Carbon $transactionDate) {
        $date = $transactionDate->setTimezone('Asia/Manila')->format('D, M d, Y h:m A');
        $content = "You have successfully added funds to your wallet via EC Pay on " .
            $date . " . Service fee for this transaction is P 0.00. Your new balance is P " . number_format($newBalance, 2) .
            " with reference no. " . $referenceNumber . ". Thank you for using SquidPay!" ;

        $title = "SquidPay - Payment via ECPay";
        $this->appNotifications->create([
            'title' => $title,
            'status' => 1,
            'description' => $content,
            'user_account_id' => $userId,
            'user_created' => $userId
        ]);
    }

    public function amountWithServiceFee(float $amount)
    {
        $serviceFee = $this->serviceFee / 10;
        return $amount + ($amount * $serviceFee);
    }
}
