<?php

namespace App\Services\AddMoney\DragonPay;

use App\Models\UserAccount;
use App\Repositories\AddMoney\IWebBankRepository;
use App\Repositories\LogHistory\ILogHistoryRepository;
use App\Repositories\ServiceFee\IServiceFeeRepository;
use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserDetail\IUserDetailRepository;
use App\Services\Utilities\ReferenceNumber\IReferenceNumberService;
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

    public IWebBankRepository $webBanks;
    public IUserAccountRepository $userAccounts;
    public IUserDetailRepository $userDetails;
    public IServiceFeeRepository $serviceFees;
    public IReferenceNumberService $referenceNumberService;
    public ILogHistoryRepository $logHistory;

    public function __construct(IWebBankRepository $webBanks, 
                                IUserAccountRepository $userAccounts,
                                IUserDetailRepository $userDetails,
                                IServiceFeeRepository $serviceFees,
                                IReferenceNumberService $referenceNumberService,
                                ILogHistoryRepository $logHistory) {

        $this->baseURL = config('dragonpay.dp_base_url_v1');
        $this->merchantID = config('dragonpay.dp_merchantID');
        $this->key = config('dragonpay.dp_key');

        $this->webBanks = $webBanks;
        $this->userAccounts = $userAccounts;
        $this->userDetails = $userDetails;
        $this->serviceFees = $serviceFees;
        $this->referenceNumberService = $referenceNumberService;
        $this->logHistory = $logHistory;
    }

    /**
     * Generate the DragonPay request URL
     * to the DragonPay Web Service
     * 
     * @param UserAccount $user
     * @param float $amount
     * @param string|null @email
     * @return json $response
     */
    public function generateRequestURL(UserAccount $user, array $urlParams)
    {
        $this->setUserAccountID($user->id);
        $this->setReferenceNumber($this->referenceNumberService->getAddMoneyRefNo());

        $email = $this->getEmail($user);
        $userAccountID = $user->id;
        $amount = $urlParams['amount'];

        $this->validateTiersAndLimits($userAccountID, $amount);

        $token = $this->getToken();
        $txnID =  $this->referenceNumber;
        $url = $this->baseURL . '/' . $txnID . '/post';
        $beneficiaryName = $this->getFullname($userAccountID);
        $addMoneyServiceFee = $this->serviceFees->get('6f8b72d8-cca7-49e2-8e05-bd455f86dd2e');
        $totalAmount = $addMoneyServiceFee->amount + $amount;
        $body = $this->createBody($totalAmount, $beneficiaryName, $email);

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->withToken($token)->post($url, $body);

        // on DragPay API Error
        $this->handleError($response);

        if ($response->status() == 200 || $response->status() == 201) {

            $this->insertAddMoneyRecord($userAccountID, 
                                        $txnID, 
                                        $this->formatAmount($amount), 
                                        $addMoneyServiceFee->amount, 
                                        $addMoneyServiceFee->id, $totalAmount, 
                                        '0ec43457-9131-11eb-b44f-1c1b0d14e211', 
                                        $body['Description']);
        }

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
    public function getFullname($userAccountID)
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
    public function createBody($amount, $beneficiaryName, $email)
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
     * @param int $amount
     * @return string
     */
    public function formatAmount($amount)
    {
        if (!strpos($amount, '.')) return number_format($amount, 2, '.', '');
        return (string) $amount;
    }

    /**
     * Handles the error response
     * from DragonPay API
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
    public function insertAddMoneyRecord($userAccountID, $refNo, $amount, $serviceFee, $serviceFeeID, $totalAmount, $transactionCategoryID, $transactionRemarks)
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

        if (!$this->webBanks->create($row)) return $this->cantWriteToTable();
    }

    /**
     * Validate the user accoirding to the user's
     * tier and amount in the transaction
     * 
     * @param string $userAccountID
     * @param float $amount
     * @return exception|null
     */
    public function validateTiersAndLimits(string $userAccountID, float $amount)
    {
        $tier1 = 1;
        $amountLimit = 5000.00;

        // call tier repository and get the tier

        if ($amount > $amountLimit) return $this->tierLimitExceeded();
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
}
