<?php

namespace App\Services\BPIService;

use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Enums\SendMoneyConfig;
use App\Enums\ReferenceNumberTypes;
use App\Enums\TransactionCategoryIds;
use App\Repositories\InAddMoneyBPI\IInAddMoneyBPIRepository;
use App\Repositories\ServiceFee\IServiceFeeRepository;
use App\Traits\Errors\WithUserErrors;
use Illuminate\Support\Facades\Storage;
use App\Services\Utilities\API\IApiService;
use App\Services\BPIService\PackageExtension\Encryption;
use App\Services\Utilities\LogHistory\ILogHistoryService;
use App\Repositories\UserBalanceInfo\IUserBalanceInfoRepository;
use App\Services\Utilities\ReferenceNumber\IReferenceNumberService;
use App\Repositories\UserTransactionHistory\IUserTransactionHistoryRepository;
use Mpdf\Tag\Tr;

class BPIService implements IBPIService
{
    use WithUserErrors;

    private IApiService $apiService;
    private IReferenceNumberService $referenceNumberService;
    private ILogHistoryService $logHistory;
    private IUserTransactionHistoryRepository $transactionHistory;
    private IUserBalanceInfoRepository $userBalanceInfo;
    private IInAddMoneyBPIRepository $bpiRepository;
    private IServiceFeeRepository $serviceFee;


    public function __construct(IApiService $apiService, IReferenceNumberService $referenceNumberService, ILogHistoryService $logHistory, IUserTransactionHistoryRepository $transactionHistory, IUserBalanceInfoRepository $userBalanceInfo, IInAddMoneyBPIRepository $bpiRepository, IServiceFeeRepository $serviceFee)
    {
        $this->apiService = $apiService;
        $this->referenceNumberService = $referenceNumberService;
        $this->transactionHistory = $transactionHistory;
        $this->userBalanceInfo = $userBalanceInfo;
        $this->bpiRepository = $bpiRepository;
        $this->serviceFee = $serviceFee;
    }

    private function getHeaders(string $token) {
        return [
            'x-ibm-client-id' => env('BPI_CLIENT_ID'),
            'x-ibm-client-secret' => env('BPI_CLIENT_SECRET'),
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ];
    }

    public function bpiAuth(string $code) {
        $body = [
            'client_id' => env('BPI_CLIENT_ID'),
            'client_secret' => env('BPI_CLIENT_SECRET'),
            'grant_type' => 'authorization_code',
            'code' => $code
        ];
        return $this->apiService->postAsForm(env('BPI_AUTH'), $body, ['accept' => 'application/json', 'content-type' => 'application/x-www-form-urlencoded'])->json();
    }

    public function getAccounts(string $token) {

        $token = $this->getHeaders($token);
        $response = $this->apiService->get(env('BPI_TRANSACTIONAL_ENDPOINT'), $token)->json();

        if($response && isset($response['token'])) {
            $jwt = $this->bpiDecryptionJWE($response['token']);
            if($jwt) {
                return $this->bpiDecryptionJWT($jwt);
            }
        }

        // THROW ERROR
        $this->bpiTokenInvalid();
    }

    public function fundTopUp(Array $array, string $token) {
        $refNo = $this->referenceNumberService->generate(ReferenceNumberTypes::AddMoneyViaWebBank);
        $token = $this->getHeaders($token);

        $array['merchantTransactionReference'] = $refNo;

        $array['jti'] = Str::uuid()->toString();
        $array['iss'] = 'PARTNER';
        $array['sub'] = 'fundTopUp';
        $array['aud'] = 'BPI';
        $array['exp'] = Carbon::now()->addMinutes(30)->timestamp;
        $array['iat'] = Carbon::now()->timestamp;
        $jwt = $this->bpiEncodeJWT($array);
        $jwe = $this->bpiEncodeJWE($jwt);
        
        // Send API request
        $response = $this->apiService->post(env('BPI_FUND_TOP_UP_ENDPOINT'), ['token' => $jwe], $token);
        
        if($response && isset($response['token'])) {
            $jwt = $this->bpiDecryptionJWE($response['token']);
            if($jwt) {
                $transactionId = $response->getHeaders()['transactionId']['0'];
                $jwt_response = $this->bpiDecryptionJWE($response['token']);
                $response_raw = $this->bpiDecryptionJWT($jwt_response);

                return [
                    'response' => $response_raw,
                    'transactionId' => $transactionId,
                    'refId' => $refNo
                ];
            }
        }

        // THROW ERROR
        $this->bpiTokenInvalid();
    }

    public function otp(array $params) {
        
        $headers = $token = $this->getHeaders($params['token']);
        $headers['transactionId'] = $params['transactionId'];
        $otp_url = env('BPI_FUND_TOP_UP_OTP');

        $params['jti'] = Str::uuid()->toString();
        $params['iss'] = 'PARTNER';
        $params['sub'] = 'fundTopUp';
        $params['aud'] = 'BPI';
        $params['exp'] = Carbon::now()->addMinutes(30)->timestamp;
        $params['iat'] = Carbon::now()->timestamp;
        $jwt = $this->bpiEncodeJWT($params);
        $jwe = $this->bpiEncodeJWE($jwt);

        $response = $this->apiService->post($otp_url, ['token' => $jwe], $headers);
        if($response && isset($response['token'])) {
            $jwt = $this->bpiDecryptionJWE($response['token']);
            if($jwt) {
                $jwt_response = $this->bpiDecryptionJWE($response['token']);
                $response_raw = $this->bpiDecryptionJWT($jwt_response);
                return $response_raw;
            }
        }
        // THROW ERROR
        $this->bpiTokenInvalid();
    }

    public function status(array $params) {
        
        $headers = $this->getHeaders($params['token']);
        $headers['transactionId'] = $params['transactionId'];
        $status_url = env('BPI_FUND_TOP_UP_STATUS');

        $response = $this->apiService->get($status_url, $headers);
        if($response && isset($response['token'])) {
            $jwt = $this->bpiDecryptionJWE($response['token']);
            if($jwt) {
                $jwt_response = $this->bpiDecryptionJWE($response['token']);
                $response_raw = $this->bpiDecryptionJWT($jwt_response);
                return $response_raw;
            }
        }
        // THROW ERROR
        $this->bpiTokenInvalid();
    }

    public function process(array $params) {
        \DB::beginTransaction();
        try {
            $headers = $this->getHeaders($params['token']);
            $headers['transactionId'] = $params['transactionId'];
            $otp_url = env('BPI_PROCESS_URL');
    
            $values['otp'] = $params['otp'];
            $values['jti'] = Str::uuid()->toString();
            $values['iss'] = 'PARTNER';
            $values['sub'] = 'fundTopUp';
            $values['aud'] = 'BPI';
            $values['exp'] = Carbon::now()->addMinutes(30)->timestamp;
            $values['iat'] = Carbon::now()->timestamp;
            $jwt = $this->bpiEncodeJWT($values);
            $jwe = $this->bpiEncodeJWE($jwt);
    
            $response = $this->apiService->post($otp_url, ['token' => $jwe], $headers);
            if($response && isset($response->json()['token'])) {
                $jwt = $this->bpiDecryptionJWE($response['token']);
                if($jwt) {
                    $jwt_response = $this->bpiDecryptionJWE($response['token']);
                    $response_raw = $this->bpiDecryptionJWT($jwt_response);
    
                    $log = $this->transactionHistory->log(request()->user()->id, TransactionCategoryIds::cashinBPI, $params['transactionId'], $params['refId'], $params['amount'], Carbon::now(), request()->user()->id);
    
                    $balance = $this->userBalanceInfo->getUserBalance(request()->user()->id);
                    $cashInWithServiceFee = $params['amount'] + SendMoneyConfig::ServiceFee;
                    $total = $cashInWithServiceFee + $balance;
                    if($response_raw['status'] == 'success') {
                        $this->userBalanceInfo->updateUserBalance(request()->user()->id, $total);
                    }
    
                    $serviceFee = $this->serviceFee->getByTierAndTransCategory(request()->user()->tier_id, TransactionCategoryIds::cashinBPI);
                    $this->bpiRepository->create(
                        [
                            "user_account_id" => request()->user()->id,
                            "reference_number" => $params['refId'],
                            "amount" => $params['amount'],
                            "service_fee_id" => $serviceFee->id,
                            "service_fee" => SendMoneyConfig::ServiceFee,
                            "total_amount" => $total,
                            "transaction_date" => Carbon::now()->format('Y-m-d H:i:s'),
                            "transaction_category_id" => TransactionCategoryIds::cashinBPI,
                            "transaction_remarks" => $params['remarks'],
                            "status" => $response_raw['status'],
                            "bpi_reference" => $params['transactionId'],
                            "transaction_response" => json_encode($response_raw),
                            "user_created" => request()->user()->id,
                            "user_updated" => request()->user()->id,
                        ]
                    );
    
                    \DB::commit();
                    return $response_raw;
                }
            }
        } catch(\Exception $e) {
            dd($e->getMessage());
            \DB::rollback();
            // THROW ERROR
            $this->bpiTokenInvalid();
        }

    }

    public function bpiEncodeJWT(array $payload) {
        $factory = new \Tmilos\JoseJwt\Context\DefaultContextFactory();
        $context = $factory->get();
        $pub = Storage::disk('local')->get('keys/squid.ph.key');

        $token = Encryption::encode2($context, $payload, $pub, \Tmilos\JoseJwt\Jws\JwsAlgorithm::RS256, [
            "alg" => "RS256"
        ]);

        return $token;
    }

    public function bpiEncodeJWE(string $payload) {
        $factory = new \Tmilos\JoseJwt\Context\DefaultContextFactory();
        $context = $factory->get();

        $pub = Storage::disk('local')->get('keys/squid.ph.pub');
        $myPubKey = openssl_get_publickey($pub);
        $token = Encryption::encodeJWE($context, $payload, $myPubKey, \Tmilos\JoseJwt\Jwe\JweAlgorithm::RSA_OAEP, \Tmilos\JoseJwt\Jwe\JweEncryption::A128CBC_HS256, [
            "alg" => "RSA-OAEP",
            "enc"=> "A128CBC-HS256",
            "cty"=> "JWT"
        ]);

        return $token;
    }

    private function bpiDecryptionJWE(string $payload) {        
        $factory = new \Tmilos\JoseJwt\Context\DefaultContextFactory();
        $context = $factory->get();

        $pri = Storage::disk('local')->get('keys/squid.ph.key');
        $myPrivateKey = openssl_get_privatekey($pri, '');

        $payload = \Tmilos\JoseJwt\Jwe::decode($context, $payload, $myPrivateKey);
        return $payload;
    }

    private function bpiDecryptionJWT(string $payload) {
        $factory = new \Tmilos\JoseJwt\Context\DefaultContextFactory();
        $context = $factory->get();

        $pub = Storage::disk('local')->get('keys/squid.ph.pub');
        $partyPublicKey = openssl_get_publickey($pub);

        $payload = \Tmilos\JoseJwt\JWT::decode($context, $payload, $partyPublicKey);
        return $payload;
    }
}
