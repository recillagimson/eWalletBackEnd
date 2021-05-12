<?php


namespace App\Services\Utilities\PrepaidLoad\ATM;


use App\Traits\Errors\WithTransactionErrors;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use App\Services\Utilities\ReferenceNumber\IReferenceNumberService;
use App\Enums\ReferenceNumberTypes;
use Illuminate\Validation\ValidationException;

class AtmService implements IAtmService
{
    use WithTransactionErrors;
    private IReferenceNumberService $referenceNumberService;

    public function __construct(IReferenceNumberService $referenceNumberService)
    {
        $this->referenceNumberService = $referenceNumberService;
    }

    public function generateSignature(array $data): string
    {
        $privateKeyContent = Storage::disk('local')->get('/key/partnerid.private.pfx');
        openssl_pkcs12_read($privateKeyContent, $certs, '1234567890');

        $jsonData = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);
        openssl_sign($jsonData, $signature, $certs['pkey'], OPENSSL_ALGO_SHA1);
        return base64_encode($signature);
    }

    public function verifySignature(array $data, string $base64Signature)
    {
        $signature = base64_decode($base64Signature);
        $cert = Storage::disk('local')->get('/key/partnerid.public.cer');
        $publicKeyId = openssl_get_publickey($cert);

        $jsonData = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);
        $result = openssl_verify($jsonData, $signature, $publicKeyId, OPENSSL_ALGO_SHA1);

        if ($result !== 1)
            $this->transFailed();
    }

    public function generatePEM(string $publicCertContent)
    {
        /* Convert .cer to .pem, cURL uses .pem */
        $certificateCApemContent = '-----BEGIN CERTIFICATE-----' . PHP_EOL
            . chunk_split(base64_encode($publicCertContent), 64, PHP_EOL)
            . '-----END CERTIFICATE-----' . PHP_EOL;

        $certificateCApem = 'partnerid.public' . '.pem';
        Storage::disk('local')->put('/key/' . $certificateCApem, $certificateCApemContent);
    }

    public function showNetworkAndPrefix(): array
    {
        $signature = $this->generateSignature($this->createATMPostBody());

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Signature' => $signature
        ])->post(config('services.load.atm.url').'/prefix-list', 
        $this->createATMPostBody());

        $result = json_decode($response->body());
        
        return $result->data;
    }

    public function showProductList(): array
    {
        $signature = $this->generateSignature($this->createATMPostBody());

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Signature' => $signature
        ])->post(config('services.load.atm.url').'/product-list', 
        $this->createATMPostBody());

        $result = json_decode($response->body());
        
        return $result->data;
    }

    public function atmload(array $items): array
    {
        $getProductList = $this->showNetworkProuductList($items);
        $referenceNumber = $this->referenceNumberService->generate(ReferenceNumberTypes::BuyLoad);
        $items["agentRefNo"] = $referenceNumber;
        $post_data = $this->createATMPostBody($items);
        $signature = $this->generateSignature($post_data);

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Signature' => $signature
        ])->post(config('services.load.atm.url').'/topup-request', 
        $post_data);

        $result = $response->json();
        
        return array("result"=>$result, "response"=>$response->body());
    }
    
    public function showNetworkProuductList(array $items): array
    {        
        $getNetwork = $this->verifyMobileNumberFromAtmList($items);

        $showProductList = $this->showProductList();
        $getProductList = $this->findAtmProducts($showProductList, (array) $getNetwork, $items);

        return $getProductList;
    }

    private function createATMPostBody(array $items=null):array {
        $body = [
            'id' => config('services.load.atm.id'),
            'uid' => config('services.load.atm.uid'),
            'pwd' => config('services.load.atm.password'),
            'data' => $items
        ];

        return $body;
    }

    private function getMobileNumberPrefix(string $mobileNo): string {
        return str_split($mobileNo, 5)[0];
    }

    public function convertMobileNumberPrefixToAreaCode(string $mobileNo): string {
        $strSplit = str_split($mobileNo);
        if(intval($strSplit[0]) == 0) {
            $strSplit[0] = '63';
        }

        return join("",$strSplit);
    }

    private function findMobileNumberAndNetwork(array $prefixLists, string $mobileNo, object $result=null): object {
        foreach($prefixLists as $list) {
            if($list->prefix == $mobileNo) {
                $result = $list;
            }
        }
        if(!$result) throw ValidationException::withMessages(['mobileNo' => 'The number is not valid for any network provider.']);

        return $result;
    }

    private function findAtmProducts(array $products, array $items, array $details=null ,array $result=[]): array {
        if(array_key_exists('productCode', $details)) {
            foreach($products as $product) {
                if($product->productCode == $details["productCode"]) {
                    array_push($result, $product);
                }
            }
            if(sizeof($result) == 0) throw ValidationException::withMessages(['productCode' => 'The product code is not valid.']);
        }else {
            foreach($products as $product) {
                if($product->provider == $items["provider"]) {
                    array_push($result, $product);
                }
            }
        }

        return $result;
    }

    private function verifyMobileNumberFromAtmList(array $items): object {

        $getPrefixList = $this->showNetworkAndPrefix();
        $currentMobileNumber = $this->getMobileNumberPrefix($items["mobileNo"]);
        
        $getNetwork = $this->findMobileNumberAndNetwork($getPrefixList, $currentMobileNumber);

        return $getNetwork;
    }
}
