<?php

namespace App\Services\AddMoney\DragonPay;

use App\Enums\DragonPayStatusTypes;
use App\Enums\TransactionCategories;
use App\Models\UserAccount;
use App\Repositories\AddMoney\IWebBankRepository;
use App\Repositories\LogHistory\ILogHistoryRepository;
use App\Repositories\ServiceFee\IServiceFeeRepository;
use App\Repositories\TransactionCategory\ITransactionCategoryRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserDetail\IUserDetailRepository;
use App\Repositories\UserTransactionHistory\IUserTransactionHistoryRepository;
use App\Services\Utilities\ReferenceNumber\IReferenceNumberService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class WebBankingService implements IWebBankingService
{
    /**
     * DragonPay API base URL V1
     * 
     * @var string
     */
    protected $baseURL;

    /**
     * DragonPay API merchant ID (Username)
     * 
     * @var string
     */
    protected $merchantID;

    /**
     * DragonPay API key (Password)
     * 
     * @var string
     */
    protected $key;

    /**
     * User account ID
     * 
     * @var uuid
     */
    protected $userAccountID;

    /**
     * Reference number for Add Money Web Bank
     * 
     * @var string
     */
    protected $referenceNumber;

    /**
     * Transaction name of this class
     * 
     * @var string
     */
    protected $moduleTransCategory;

    /**
     * test purposes
     * remover when tiers and service fees are finalize
     */
    protected $tier;

    private IWebBankRepository $webBanks;
    private IUserAccountRepository $userAccounts;
    private IUserDetailRepository $userDetails;
    private IServiceFeeRepository $serviceFees;
    private IReferenceNumberService $referenceNumberService;
    private ILogHistoryRepository $logHistory;
    private ITransactionCategoryRepository $transactionCategories;
    private IUserTransactionHistoryRepository $userTransactions;

    public function __construct(IWebBankRepository $webBanks, 
                                IUserAccountRepository $userAccounts,
                                IUserDetailRepository $userDetails,
                                IServiceFeeRepository $serviceFees,
                                IReferenceNumberService $referenceNumberService,
                                ILogHistoryRepository $logHistory,
                                ITransactionCategoryRepository $transactionCategories,
                                IUserTransactionHistoryRepository $userTransactions) {

        $this->baseURL = config('dragonpay.dp_base_url_v1');
        $this->merchantID = config('dragonpay.dp_merchantID');
        $this->key = config('dragonpay.dp_key');
        $this->moduleTransCategory = TransactionCategories::AddMoneyWebBankDragonPay;

        $this->webBanks = $webBanks;
        $this->userAccounts = $userAccounts;
        $this->userDetails = $userDetails;
        $this->serviceFees = $serviceFees;
        $this->referenceNumberService = $referenceNumberService;
        $this->logHistory = $logHistory;
        $this->transactionCategories = $transactionCategories;
        $this->userTransactions = $userTransactions;


        /**
         * Test purposes
         * remover when tiers and service fees are finalize
         */
        $this->tier = 1;
    }

    /**
     * Generate the DragonPay request URL
     * that goes to DragonPay Web Service
     * 
     * @param UserAccount $user
     * @param array $urlParams
     * @return json $response
     */
    public function generateRequestURL(UserAccount $user, array $urlParams)
    {
        $this->setUserAccountID($user->id);
        $this->setReferenceNumber($this->referenceNumberService->getAddMoneyRefNo());

        $email = $this->getEmail($user);
        $userAccountID = $user->id;
        $amount = $urlParams['amount'];

        $token = $this->getToken();
        $txnID =  $this->referenceNumber;
        $url = $this->baseURL . '/' . $txnID . '/post';
        $beneficiaryName = $this->getFullname($userAccountID);
        $addMoneyServiceFee = $this->validateTiersAndLimits($userAccountID, $amount);
        $totalAmount = $addMoneyServiceFee->amount + $amount;
        $body = $this->createBody($totalAmount, $beneficiaryName, $email);
        $transactionCategoryID = $this->transactionCategories->getByName($this->moduleTransCategory);

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->withToken($token)->post($url, $body);

        // on DragPay API Error
        $this->handleError($response);

        $currentAddMoneyRecord = null;
        if ($response->status() == 200 || $response->status() == 201) {

            $currentAddMoneyRecord = $this->insertAddMoneyRecord($userAccountID, 
                                        $txnID, 
                                        $this->formatAmount($amount), 
                                        $addMoneyServiceFee->amount, 
                                        $addMoneyServiceFee->id, 
                                        $totalAmount, 
                                        $transactionCategoryID->id, 
                                        $body['Description']);
        }

        $this->logGenerateURLInLogHistory($currentAddMoneyRecord->reference_number);
        return $response->json();
    }

    /**
     * Set the $userAccountID
     * 
     * @param uuid $userAccountID
     */
    public function setUserAccountID(string $userAccountID)
    {
        $this->userAccountID = $userAccountID;
    }

    /**
     * Set the Reference Number
     * 
     * @param string $refNo
     */
    public function setReferenceNumber(string $refNo)
    {
        $this->referenceNumber = $refNo;
    }
    
    /**
     * Get the email from user_accounts
     * 
     * @param UserAccount $user
     * @return string|exception $email
     */
    public function getEmail(UserAccount $user)
    {
        if ($user->email != null) return $user->email;

        return $this->invalidEmail();
    }

    /**
     * Build the DragonPay token to be
     * used as the Auth token
     * 
     * @return string-base64
     */
    protected function getToken()
    {
        return base64_encode($this->merchantID . ':' . $this->key);
    }

    /**
     * Get the fullname (First name and Last name)
     * 
     * @param uuid $userAccountID
     * @return string
     */
    public function getFullname(string $userAccountID)
    {
        $userDetails = $this->userDetails->getByUserAccountID($userAccountID);
        return $userDetails->firstname . ' ' . $userDetails->lastName;
    }

    /**
     * Create the request body to be used
     * as the DragonPay request
     * 
     * @param float $amount
     * @param string $beneficiaryName
     * @param string $email
     * @return array
     */
    public function createBody(float $amount, string $beneficiaryName, string $email)
    {
        return [
            'Amount' => $this->formatAmount($amount),
            'Currency' => 'PHP',
            'Description' => $beneficiaryName . ' Add Money Amount PHP ' . $this->formatAmount($amount),
            'Email' => $email,
            'mode' => 1,
            // 'ProcId' => 'BPI'
        ];
    }
    
    /**
     * Convert the data to string. 
     * If the data does not have 2 trailing 
     * zeros (decimal), adds ".00" at the end
     * 
     * @param float $amount
     * @return string
     */
    public function formatAmount(float $amount)
    {
        if (!strpos($amount, '.')) return number_format($amount, 2, '.', '');
        return (string) $amount;
    }

    /**
     * Handles the error response from DragonPay API
     * 
     * @param response $response
     * @return exception
     */
    public function handleError($response)
    {
        if ($response->status() == 401) {

            return $this->missingAuthToken();

        } elseif ($response->status() == 500) {

            return $this->invalidToken();
            
        } elseif ($response->status() == 422 && $response->json() == 'Invalid parameter') {
            
            return $this->invalidParams();
        }
    }

    /**
     * Insert a row in in_add_money_web_bank table
     * 
     * @param uuid $userAccountID
     * @param string $refNo
     * @param float $amount
     * @param float $serviceFee
     * @param uuid $serviceFeeID
     * @param float $totalAmount
     * @param uuid $transactionCategoryID
     * @param string $transactionRemarks
     * @return exception|null
     */
    public function insertAddMoneyRecord(string $userAccountID, string $refNo, float $amount, float $serviceFee, string $serviceFeeID, float $totalAmount, string $transactionCategoryID, string $transactionRemarks)
    {
        $row = [
            'user_account_id' => $userAccountID,
            'reference_number' => $refNo,
            'amount' => $amount,
            'service_fee' => $serviceFee,
            'service_fee_id' => $serviceFeeID,
            'total_amount' => $totalAmount,
            'dragonpay_reference' => null,
            'transaction_category_id' => $transactionCategoryID,
            'transaction_remarks' => $transactionRemarks,
            'user_created' => $userAccountID,
            'status' => 'PENDING',
        ];

        return $rowInserted = $this->webBanks->create($row);

        if (!$rowInserted) return $this->cantWriteToTable();
    }

    /**
     * Validate the user accoirding to the user's
     * tier and amount in the transaction
     * 
     * @param string $userAccountID
     * @param float $amount
     * @return exception|float $serviceFee
     */
    public function validateTiersAndLimits(string $userAccountID, float $amount)
    {
        $amountLimit = 5000.00;
        $serviceFee = 0.00;

        $userDetails = $this->userDetails->getByUserAccountID($userAccountID);
        // get the tier from the user details and set as params in getByTierAndTransCategoryID

        $addMoneyTransCategory = $this->transactionCategories->getByName($this->moduleTransCategory);
        $addMoneyServiceFee =  $this->serviceFees->getByTierAndTransCategoryID($this->tier, $addMoneyTransCategory->id);

        if ($addMoneyServiceFee->implementation_date <= Carbon::now()) $serviceFee = $addMoneyServiceFee->amount;

        if ($amount > $amountLimit) return $this->tierLimitExceeded();

        return $addMoneyServiceFee;
    }

    /**
     * Check the transaction status in DragonPay. Note: null 
     * reply form API means that the transaction is left in 
     * pending for a period of time (not sure how long)
     * 
     * @param array $identifier
     * @return json $response
     */
    public function getAddMoneyTransStatus(array $identifier)
    {
        if (array_key_exists('reference_number', $identifier)) {
            
            $response = $this->dragonpayRequest('/txnid/' . $identifier['reference_number']);
        } else {
            
            $response = $this->dragonpayRequest('/refno/' . $identifier['dragonpay_reference']);
        }

        return $response->json();
    }

    /**
     * A request builder with DragonPay baseURL
     * 
     * @param string $endpoint
     * @return response $response
     */
    public function dragonpayRequest(string $endpoint)
    {
        $response = Http::withToken($this->getToken())->get($this->baseURL . $endpoint);
        return $response;
    }

    /**
     * Voids the transaction in DragonPay DB and SquidPay DB
     * 
     * @param UserAccount $user
     * @param array $referenceNumber
     * @return object $responseData
     */
    public function cancelAddMoney(UserAccount $user, array $referenceNumber)
    {
        $referenceNumber = $referenceNumber['reference_number'];

        $addMoneyRecord = $this->webBanks->getByReferenceNumber($referenceNumber);

        if ($addMoneyRecord == null) throw new ModelNotFoundException();

        if ($addMoneyRecord->user_account_id != $user->id) return $this->unauthorizedForThisRecord();

        if ($addMoneyRecord->status != DragonPayStatusTypes::Pending) return $this->nonPendingTrans();

        $response = $this->dragonpayRequest('/void/' . $referenceNumber);
        $response = json_decode($response->body());

        if ($response->Status < 0) return $this->nonPendingTrans();

        $this->webBanks->update($addMoneyRecord, ['status' => DragonPayStatusTypes::Void]);
        
        return $responseData = (object) [
            'status' => true,
            'message' => 'Add money transaction has been cancelled.'
        ];
    }

    /**
     * Logs the user's action (generate URL) in log history
     * 
     * @param string $referenceNumber
     * @return void
     */
    public function logGenerateURLInLogHistory(string $referenceNumber)
    {
        $this->logHistory->create([
            'user_account_id' => $this->userAccountID,
            'reference_number' => $referenceNumber,
            'namespace' => __METHOD__,
            'remarks' => 'Requests to generate URL for adding money',
            'user_created' => $this->userAccountID
        ]);
    }
















    /**
     * Thrown when there is no email in database
     */
    private function invalidEmail() 
    {
        throw ValidationException::withMessages([
            'email' => 'Invalid Email. Please update your profile.'
        ]);
    }

    /**
     * Thrown when there is no Auth token
     * in the DragonPay request header
     */
    private function missingAuthToken()
    {
        $this->logHistory->create([
            'user_account_id' => $this->userAccountID,
            'reference_number' => 'N/A',
            'namespace' => __METHOD__,
            'remarks' => 'DragonPay: No auth token in request header',
            'user_created' => $this->userAccountID
        ]);

        $this->throw500();
    }

    /**
     * Thrown when Auth token is incorrect
     */
    private function invalidToken()
    {
        $this->logHistory->create([
            'user_account_id' => $this->userAccountID,
            'reference_number' => 'N/A',
            'namespace' => __METHOD__,
            'remarks' => 'DragonPay: Incorrect auth token in request header',
            'user_created' => $this->userAccountID
        ]);

        $this->throw500();
    }

    /**
     * Thrown when DragonPay can't validate
     * the Amount, Currency, or Description or/and
     * is missing
     */
    private function invalidParams()
    {
        $this->logHistory->create([
            'user_account_id' => $this->userAccountID,
            'reference_number' => 'N/A',
            'namespace' => __METHOD__,
            'remarks' => 'DragonPay: Invalid parameter (invalid or missing Amount, Currency, or Description)',
            'user_created' => $this->userAccountID
        ]);

        $this->throw500();
    }

    /**
     * Thrown when the App can't write to
     * the table for some reason
     */
    private function cantWriteToTable()
    {
        $this->logHistory->create([
            'user_account_id' => $this->userAccountID,
            'reference_number' => 'N/A',
            'namespace' => __METHOD__,
            'remarks' => 'Can`t write to table.',
            'user_created' => $this->userAccountID
        ]);

        $this->throw500();
    }

    /**
     * Thrown when the request amount exceeded the limit
     */
    private function tierLimitExceeded()
    {
        throw ValidationException::withMessages([
            'amount' => 'The requested amount exceeded the limits for this account.'
        ]);
    }

    /**
     * Throw error 500
     */
    private function throw500()
    {
        abort(500, 'Something went wrong :(');
    }

    /**
     * Thrown when the record is not owned by the 
     * authenticated user
     */
    private function unauthorizedForThisRecord()
    {
        throw ValidationException::withMessages([
            'reference_number' => 'Current user is unauthorized to cancel the record.'
        ]);
    }

    /**
     * Thrown when a non pending transaction is attempted 
     * to be voided
     */
    private function nonPendingTrans()
    {
        throw ValidationException::withMessages([
            'reference_number' => 'Cannot void a non-pending transaction.'
        ]);
    }
}
