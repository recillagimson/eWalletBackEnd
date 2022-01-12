<?php

namespace App\Services\BPIService;

use App\Enums\Currencies;
use App\Enums\DrcrStatus;
use App\Enums\ReferenceNumberTypes;
use App\Enums\SendMoneyConfig;
use App\Enums\TransactionCategoryIds;
use App\Repositories\DrcrMemo\IDrcrMemoRepository;
use App\Repositories\InAddMoneyBPI\IInAddMoneyBPIRepository;
use App\Repositories\Notification\INotificationRepository;
use App\Repositories\OutPayMerchants\IOutPayMerchantRepository;
use App\Repositories\ServiceFee\IServiceFeeRepository;
use App\Repositories\UserBalanceInfo\IUserBalanceInfoRepository;
use App\Repositories\UserTransactionHistory\IUserTransactionHistoryRepository;
use App\Services\BPIService\PackageExtension\Encryption;
use App\Services\Utilities\API\IApiService;
use App\Services\Utilities\LogHistory\ILogHistoryService;
use App\Services\Utilities\Notifications\Email\IEmailService;
use App\Services\Utilities\Notifications\SMS\ISmsService;
use App\Services\Utilities\ReferenceNumber\IReferenceNumberService;
use App\Traits\Errors\WithUserErrors;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Log;
use Tmilos\JoseJwt\Context\DefaultContextFactory;
use Tmilos\JoseJwt\Jwe;
use Tmilos\JoseJwt\Jwe\JweAlgorithm;
use Tmilos\JoseJwt\Jwe\JweEncryption;
use Tmilos\JoseJwt\Jws\JwsAlgorithm;
use Tmilos\JoseJwt\Jwt;

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
    private ISmsService $smsService;
    private IEmailService $emailService;
    private INotificationRepository $notificationRepository;
    private IOutPayMerchantRepository $outPayMerchantRepository;
    private IDrcrMemoRepository $drcrMemoRepository;

    private $clientId;
    private $clientSecret;
    private $authUrl;
    private $transactionalUrl;
    private $fundTopUpUrl;
    private $fundTopUpOtpUrl;
    private $fundTopUpStatusUrl;
    private $processUrl;

    public function __construct(IApiService                       $apiService,
                                IReferenceNumberService           $referenceNumberService,
                                ILogHistoryService                $logHistory,
                                IUserTransactionHistoryRepository $transactionHistory,
                                IUserBalanceInfoRepository        $userBalanceInfo,
                                IInAddMoneyBPIRepository          $bpiRepository,
                                IServiceFeeRepository             $serviceFee,
                                ISmsService                       $smsService,
                                IEmailService                     $emailService,
                                INotificationRepository           $notificationRepository,
                                IOutPayMerchantRepository         $outPayMerchantRepository,
                                IDrcrMemoRepository               $drcrMemoRepository)
    {
        $this->apiService = $apiService;
        $this->referenceNumberService = $referenceNumberService;
        $this->transactionHistory = $transactionHistory;
        $this->userBalanceInfo = $userBalanceInfo;
        $this->bpiRepository = $bpiRepository;
        $this->serviceFee = $serviceFee;
        $this->smsService = $smsService;
        $this->emailService = $emailService;
        $this->notificationRepository = $notificationRepository;
        $this->logHistory = $logHistory;
        $this->outPayMerchantRepository = $outPayMerchantRepository;
        $this->drcrMemoRepository = $drcrMemoRepository;

        $this->clientId = config('bpi.clientId');
        $this->clientSecret = config('bpi.clientSecret');
        $this->authUrl = config('bpi.authUrl');
        $this->transactionalUrl = config('bpi.transactionalUrl');
        $this->fundTopUpUrl = config('bpi.fundTopUpUrl');
        $this->fundTopUpOtpUrl = config('bpi.fundTopUpOtpUrl');
        $this->fundTopUpStatusUrl = config('bpi.fundTopUpStatusUrl');
        $this->processUrl = config('bpi.processUrl');
    }

    private function getHeaders(string $token)
    {
        return [
            'x-ibm-client-id' => $this->clientId,
            'x-ibm-client-secret' => $this->clientSecret,
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ];
    }

    public function bpiAuth(string $code)
    {
        $body = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'authorization_code',
            'code' => $code
        ];
        return $this->apiService->postAsForm($this->authUrl, $body, ['accept' => 'application/json', 'content-type' => 'application/x-www-form-urlencoded'])->json();
    }

    public function getAccounts(string $token)
    {

        $token = $this->getHeaders($token);
        $response = $this->apiService->get($this->transactionalUrl, $token)->json();
        if ($response && isset($response['token'])) {
            $jwt = $this->bpiDecryptionJWE($response['token']);
            Log::info($jwt);
            if ($jwt) {
                $val = $this->bpiDecryptionJWT($jwt);
                Log::info($jwt);
                return $val;
            }
        }

        // THROW ERROR
        $this->bpiTokenInvalid();
    }

    public function fundTopUp(array $array, string $rawToken)
    {
        $array['remarks'] = 'BPI Cashin';
        $refNo = $this->referenceNumberService->generate(ReferenceNumberTypes::AddMoneyViaWebBank);
        $token = $this->getHeaders($rawToken);

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
        $response = $this->apiService->post($this->fundTopUpUrl, ['token' => $jwe], $token);

        Log::info($response);

        if ($response && isset($response['token'])) {
            $jwt = $this->bpiDecryptionJWE($response['token']);
            if ($jwt) {


                $jwt_response = $this->bpiDecryptionJWE($response['token']);
                $response_raw = $this->bpiDecryptionJWT($jwt_response);

                if ($response_raw && isset($response_raw['status']) && $response_raw['status'] != 'error') {
                    $transactionId = $response->getHeaders()['transactionId']['0'];
                    $otp_response = $this->otp([
                        'token' => $rawToken,
                        'mobileNumberToken' => $response_raw['body']['mobileNumberToken'],
                        'transactionId' => $transactionId,
                    ]);
                    return [
                        'response' => $response_raw,
                        'transactionId' => $transactionId,
                        'refId' => $refNo,
                        'otpResponse' => $otp_response
                    ];
                } else {
                    $this->accountCantBeUsed();
                }
            }
        }

        // THROW ERROR
        $this->bpiTokenInvalid();
    }

    public function otp(array $params)
    {
        $headers = $this->getHeaders($params['token']);
        $headers['transactionId'] = $params['transactionId'];
        $otp_url = $this->fundTopUpOtpUrl;

        $params['jti'] = Str::uuid()->toString();
        $params['iss'] = 'PARTNER';
        $params['sub'] = 'fundTopUp';
        $params['aud'] = 'BPI';
        $params['exp'] = Carbon::now()->addMinutes(30)->timestamp;
        $params['iat'] = Carbon::now()->timestamp;
        $jwt = $this->bpiEncodeJWT($params);
        $jwe = $this->bpiEncodeJWE($jwt);

        $response = $this->apiService->post($otp_url, ['token' => $jwe], $headers);

        Log::info($response);

        if ($response && isset($response['token'])) {
            $jwt = $this->bpiDecryptionJWE($response['token']);
            if ($jwt) {
                $jwt_response = $this->bpiDecryptionJWE($response['token']);
                $response_raw = $this->bpiDecryptionJWT($jwt_response);
                return $response_raw;
            }
        }
        // THROW ERROR
        $this->bpiTokenInvalid();
    }

    public function status(array $params)
    {
        DB::beginTransaction();
        try {
            $headers = $this->getHeaders($params['token']);
            $status_url = $this->fundTopUpStatusUrl;

            $processedTransactions = [];
            foreach ($params['transactionIds'] as $transactionId) {
                $transaction = $this->bpiRepository->get($transactionId);
                if ($transaction && $transaction->status != 'success') {
                    $headers['transactionId'] = $transaction->bpi_reference;
                    $response = $this->apiService->get($status_url, $headers);
                    if ($response && isset($response->json()['token'])) {
                        $jwt = $this->bpiDecryptionJWE($response['token']);
                        if ($jwt) {
                            $jwt_response = $this->bpiDecryptionJWE($response['token']);
                            $response_raw = $this->bpiDecryptionJWT($jwt_response);
                            if ($response_raw && $response_raw['status'] == 'success') {
                                $balance = $this->userBalanceInfo->getUserBalance(request()->user()->id);
                                $cashInWithServiceFee = $transaction->total_amount + SendMoneyConfig::ServiceFee;
                                $total = $cashInWithServiceFee + $balance;
                                $this->userBalanceInfo->updateUserBalance($transaction->user_account_id, $total);
                                $this->bpiRepository->update($transaction, ['status' => $response_raw['status']]);
                                $updated = $this->bpiRepository->get($transaction->id);
                                array_push($processedTransactions, $updated);
                            }
                        }
                    } else {
                        // THROW ERROR
                        $this->bpiTokenInvalid();
                    }
                }
            }
            DB::commit();
            return $processedTransactions;
        } catch (Exception $e) {
            DB::rollBack();
            return [];
        }
    }

    public function process(array $params, string $authUser)
    {

        $record = $this->bpiRepository->create(
            [
                "user_account_id" => request()->user()->id,
                "reference_number" => $params['refId'],
                "amount" => $params['amount'],
                "service_fee_id" => null, // UPDATE AFTER REQUEST
                "service_fee" => 0,
                "total_amount" => $params['amount'],
                "transaction_date" => Carbon::now()->format('Y-m-d H:i:s'),
                "transaction_category_id" => TransactionCategoryIds::cashinBPI,
                "transaction_remarks" => $params['remarks'] ?? '',
                "status" => "PENDING", // UPDATE AFTER REQUEST
                "bpi_reference" => $params['transactionId'],
                "transaction_response" => json_encode(["status" => "pending"]), // UPDATE AFTER REQUEST
                "account_number" => $params['accountNumber'],
                "user_created" => request()->user()->id,
                "user_updated" => request()->user()->id,
                "bank_name" => $response_raw['iss'] ?? '',
            ]
        );

        DB::beginTransaction();
        $error = '';
        try {
            $params['remarks'] = 'BPI Cashin';
            $headers = $this->getHeaders($params['token']);
            $headers['transactionId'] = $params['transactionId'];
            $otp_url = $this->processUrl;

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
            if ($response && isset($response->json()['token'])) {
                $jwt = $this->bpiDecryptionJWE($response['token']);
                if ($jwt) {
                    $jwt_response = $this->bpiDecryptionJWE($response['token']);
                    $response_raw = $this->bpiDecryptionJWT($jwt_response);

                    // CHECK ERRORS
                    if ($response_raw && isset($response_raw['code']) && $response_raw['status'] == 'error') {
                        $bpi_codes = config('bpi.bpi_codes');
                        foreach ($bpi_codes as $key => $code) {
                            if ($response_raw['code'] == $key) {
                                $error = $code;
                            }
                        }

                        if (config('bpi.BPI_426') == $response_raw['code'] || config('bpi.BPI_4262') == $response_raw['code']) {
                            $error = $response_raw['code'];
                        }

                        // HANDLE BPI ERROR
                        // UPDATE TRANSACTION
                        $this->bpiRepository->update($record, [
                            'status' => 'ERROR',
                            'transaction_response' => json_encode($response_raw),
                        ]);

                        DB::commit();

                    } else {
                        $log = $this->transactionHistory->log(request()->user()->id, TransactionCategoryIds::cashinBPI, $params['transactionId'], $params['refId'], $params['amount'], Carbon::now(), request()->user()->id);


                        $serviceFee = $this->serviceFee->getByTierAndTransCategory($authUser, TransactionCategoryIds::cashinBPI);
                        // HANDLE BPI SUCCESS TRANSACTION
                        // UPDATE TRANSACTION
                        $this->bpiRepository->update($record, [
                            'status' => 'SUCCESS',
                            'transaction_response' => json_encode($response_raw),
                            'service_fee_id' => $serviceFee ? $serviceFee->id : null,
                            'service_fee' => $serviceFee ? $serviceFee->amount : 0
                        ]);

                        // $serviceFeeAmount = $serviceFee ? $serviceFee->amount : BPI::serviceFee;
                        // Wilson please fix the amount of service fee

                        $balance = $this->userBalanceInfo->getUserBalance(request()->user()->id);
                        $cashInWithServiceFee = (double)$params['amount'];
                        $total = (double)$params['amount'] + (double)$balance;

                        if ($response_raw['status'] == 'success') {
                            $this->userBalanceInfo->updateUserBalance(request()->user()->id, $total);
                        }

                        if (request()->user() && request()->user()->is_login_email == 0) {
                            // SMS USER FOR NOTIFICATION
                            $this->smsService->sendBPICashInNotification(request()->user()->mobile_number, request()->user()->profile, $total, $params['refId']);
                        } else {
                            // EMAIL USER FOR NOTIFICATION
                            $this->emailService->sendBPICashInNotification(request()->user()->email, request()->user()->profile, $total, $params['refId']);
                        }

                        $dt = Carbon::now()->setTimezone('Asia/Manila')->format('D, M d, Y h:m A');
                        $this->notificationRepository->create([
                            'title' => "SquidPay - Cash in via BPI",
                            'status' => '1',
                            'description' => "Hi " . request()->user()->profile->first_name . "! You have successfully added funds to your wallet via BPI on " .
                                $dt . " . Service fee for this transaction is P 10.00. Your new balance is P " . number_format($total, 2) .
                                " with reference no. " . $params['refId'] . ". Thank you for using SquidPay!",
                            'user_account_id' => request()->user()->id,
                            'user_created' => request()->user()->id
                        ]);

                        $minPromoDate = Carbon::create(2022, 1, 15);
                        $maxPromoDate = Carbon::create(2022, 2, 28);
                        $transactionDate = $record->transaction_date;

                        if ($transactionDate->greaterThanOrEqualTo($minPromoDate) && $transactionDate->lessThanOrEqualTo($maxPromoDate)) {

                            if ($record->amount >= 888) {

                                $balanceInfo = $this->userBalanceInfo->getByUserAccountID(request()->user()->id);
                                $payMerchantTransactions = $this->outPayMerchantRepository->getByUser($record->user_account_id);

                                if (!$payMerchantTransactions && $payMerchantTransactions->count() == 0) {

                                    $payMerchRefNo = $this->referenceNumberService->generate('PM');
                                    $memoRefNo = $this->referenceNumberService->generate(ReferenceNumberTypes::CR);
                                    $promoAmount = 88;

                                    $crMemo = [
                                        'user_account_id' => request()->user()->id,
                                        'type_of_memo' => ReferenceNumberTypes::CR,
                                        'reference_number' => $memoRefNo,
                                        'transaction_category_id' => TransactionCategoryIds::crMemo,
                                        'amount' => $promoAmount,
                                        'currency_id' => Currencies::philippinePeso,
                                        'category' => 'Adjustment',
                                        'description' => 'Credit Memo for BPI Add Money Cashback',
                                        'status' => DrcrStatus::A,
                                        'created_by' => request()->user()->id,
                                        'approved_at' => $record->transaction_date
                                    ];

                                    $memo = $this->drcrMemoRepository->create($crMemo);

                                    $balanceInfo->available_balance += 50;
                                    $balanceInfo->save();

                                    $this->transactionHistory->log(request()->user()->id,
                                        TransactionCategoryIds::crMemo,
                                        $memo->id,
                                        $memoRefNo,
                                        $promoAmount,
                                        $record->transaction_date,
                                        request()->user()->id);
                                }
                            }
                        }

                        DB::commit();
                        return $response_raw;
                    }

                    // Trigger error here then trigger again in catch for error handling
                    if ($error != '') {
                        $this->bpiTransactionError($error);
                    }
                }
            }

            $this->bpiTokenInvalid();
        } catch (Exception $e) {
            Log::error($e);
            DB::rollback();

            // HANDLE BPI ERROR
            // UPDATE TRANSACTION
            $this->bpiRepository->update($record, [
                'status' => 'ERROR',
                'transaction_response' => json_encode($e->getMessage()),
            ]);

            // THROW ERROR
            if ($error != '') {
                if (config('bpi.BPI_426') == $response_raw['code'] || config('bpi.BPI_4262') == $response_raw['code']) {
                    $bpi_codes = config('bpi.bpi_codes');
                    $this->bpiInvalidError($bpi_codes[$error]);
                } else {
                    $this->bpiTransactionError($error);
                }
            }

            $this->bpiTokenInvalid();
        }

    }

    public function bpiEncodeJWT(array $payload)
    {
        $factory = new DefaultContextFactory();
        $context = $factory->get();
        $pub = Storage::disk('local')->get('keys/squid.ph.key');

        $token = Encryption::encode2($context, $payload, $pub, JwsAlgorithm::RS256, [
            "alg" => "RS256"
        ]);
        Log::info('///// - BPI Encode JWT - //////');
        Log::info(json_encode($token));
        return $token;
    }

    public function bpiEncodeJWE(string $payload)
    {
        $factory = new DefaultContextFactory();
        $context = $factory->get();

        $pub = Storage::disk('local')->get('keys/squid.ph.pub');
        $myPubKey = openssl_get_publickey($pub);
        $token = Encryption::encodeJWE($context, $payload, $myPubKey, JweAlgorithm::RSA_OAEP, JweEncryption::A128CBC_HS256, [
            "alg" => "RSA-OAEP",
            "enc" => "A128CBC-HS256",
            "cty" => "JWT"
        ]);
        Log::info('///// - BPI Encode JWE - //////');
        Log::info(json_encode($token));
        return $token;
    }

    private function bpiDecryptionJWE(string $payload)
    {
        try {
            $factory = new DefaultContextFactory();
            $context = $factory->get();

            $pri = Storage::disk('local')->get('keys/squid.ph.key');
            $myPrivateKey = openssl_get_privatekey($pri, '');

            $payload = Jwe::decode($context, $payload, $myPrivateKey);
            Log::info('///// - BPI DECRYPTION JWE - //////');
            Log::info(json_encode($payload));
            return $payload;
        } catch (Exception $e) {
            Log::error('BPI: Invalid Private Key');
            return [];
        }
    }

    private function bpiDecryptionJWT(string $payload)
    {
        $factory = new DefaultContextFactory();
        $context = $factory->get();

        $pub = Storage::disk('local')->get('keys/squid.ph.pub');
        $partyPublicKey = openssl_get_publickey($pub);

        $payload = Jwt::decode($context, $payload, $partyPublicKey);
        Log::info('///// - BPI DECRYPTION JWT - //////');
        Log::info(json_encode($payload));
        return $payload;
    }
}
