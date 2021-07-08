<?php


namespace App\Services\ThirdParty\SecurityBank;


use App\Enums\TpaProviders;
use App\Enums\ReferenceNumberTypes;
use Illuminate\Http\Client\Response;
use App\Services\Utilities\XML\XmlService;
use App\Services\Utilities\API\IApiService;

class SecurityBankService implements ISecurityBankService
{
    private IApiService $apiService;

    private string $instapayUrl;
    private string $instapayUsername;
    private string $instapayPassword;
    private string $instapayAccount;

    private string $pesonetUrl;
    private string $pesonetUsername;
    private string $pesonetPassword;
    private string $pesonetAccount;

    public function __construct(IApiService $apiService)
    {
        $this->instapayUrl = config('secbank.instapay_url');
        $this->instapayUsername = config('secbank.instapay_username');
        $this->instapayPassword = config('secbank.instapay_password');
        $this->instapayAccount = config('secbank.instapay_fund_account');

        $this->pesonetUrl = config('secbank.pesonet_url');
        $this->pesonetUsername = config('secbank.pesonet_username');
        $this->pesonetPassword = config('secbank.pesonet_password');
        $this->pesonetAccount = config('secbank.pesonet_fund_account');

        $this->apiService = $apiService;
    }

    public function getBanks(string $provider): Response
    {
        if ($provider === TpaProviders::secBankInstapay) {
            $data = $this->generateInstapayListOfBanksRequest();
            return $this->apiService->postXml($this->instapayUrl, $data, $this->getXmlHeaders('getListOfBanks'));
        }
    }

    public function fundTransfer(string $provider, array $data): Response
    {
        if ($provider === TpaProviders::secBankInstapay) {
            $data = $this->generateInstapayPayBankRequest($data);
            return $this->apiService->postXml($this->instapayUrl, $data, $this->getXmlHeaders('payBank'));
        }

        if ($provider === TpaProviders::secBankPesonet) {
            $data = $this->generatePesonetPaybankRequest($data);
            return $this->apiService->post($this->pesonetUrl, $data);
        }
    }

    public function checkStatus(string $provider, string $traceNo): Response
    {
        if ($provider === TpaProviders::secBankInstapay) {
            $data = $this->generateInstapayCheckStatusRequest($traceNo);
            return $this->apiService->postXml($this->instapayUrl, $data, $this->getXmlHeaders('inquireIBFT'));
        }
    }

    private function getXmlHeaders(string $soapAction): array
    {
        return [
            'SOAPAction' => $soapAction,
            'Content-Type' => 'application/xml'
        ];
    }

    private function generateInstapayListOfBanksRequest(): string
    {
        $xmlService = new XmlService();

        $xmlService->startElement('getListOfBanks');

        $xmlService->startElement('Username', $this->instapayUsername);
        $xmlService->startElement('Password', $this->instapayPassword);

        return $xmlService->getString();
    }

    private function generateInstapayPayBankRequest(array $data): string
    {
        $xmlService = new XmlService();

        $xmlService->startElement('ser:payBank');

        $xmlService->startElement('ser:username', $this->instapayUsername);
        $xmlService->startElement('ser:password', $this->instapayPassword);
        $xmlService->startElement('ser:acctFr', $this->instapayAccount);
        $xmlService->startElement('ser:Bank', $data['bank_code']);
        $xmlService->startElement('ser:acctTo', $data['account_number']);
        $xmlService->startElement('ser:accttoCurr', 'PHP');
        $xmlService->startElement('ser:amount', $data['amount']);
        $xmlService->startElement('ser:senderFirstName', $data['sender_first_name']);
        $xmlService->startElement('ser:senderMidName', ' ', true);
        $xmlService->startElement('ser:senderLastName', $data['sender_first_name']);
        $xmlService->startElement('ser:senderAddressLine1', null, true);
        $xmlService->startElement('ser:senderAddressLine2', null, true);
        $xmlService->startElement('ser:senderCity', null, true);
        $xmlService->startElement('ser:senderStateProv', null, true);
        $xmlService->startElement('ser:senderPostalCode', null, true);
        $xmlService->startElement('ser:senderBirthDate', null, true);
        $xmlService->startElement('ser:senderBirthPlace', null, true);
        $xmlService->startElement('ser:senderNatureOfWork', null, true);
        $xmlService->startElement('ser:senderContactDetails', null, true);
        $xmlService->startElement('ser:senderSourceOfFunds', null, true);
        $xmlService->startElement('ser:senderNationality', null, true);
        $xmlService->startElement('ser:primaryIDType', null, true);
        $xmlService->startElement('ser:primaryIDNo', null, true);
        $xmlService->startElement('ser:secondaryIDType1', null, true);
        $xmlService->startElement('ser:secondaryIDNo1', null, true);
        $xmlService->startElement('ser:secondaryIDType2', null, true);
        $xmlService->startElement('ser:secondaryIDNo2', null, true);
        $xmlService->startElement('ser:originatingCountry', 'PH');
        $xmlService->startElement('ser:recipientFirstName', $data['recipient_first_name']);
        $xmlService->startElement('ser:recipientMidName', null, true);
        $xmlService->startElement('ser:recipientLastName', $data['recipient_last_name']);
        $xmlService->startElement('ser:recipientAddressLine1', null, true);
        $xmlService->startElement('ser:recipientAddressLine2', null, true);
        $xmlService->startElement('ser:recipientCity', null, true);
        $xmlService->startElement('ser:recipientStateProv', null, true);
        $xmlService->startElement('ser:recipientPostalCode', null, true);
        $xmlService->startElement('ser:traceNo', $data['refNo']);

        return $xmlService->getString();
    }

    private function generateInstapayCheckStatusRequest(string $traceNo): string
    {
        $xmlService = new XmlService();

        $xmlService->startElement('inquireIBFT');

        $xmlService->startElement('Username', $this->instapayUsername);
        $xmlService->startElement('Password', $this->instapayPassword);
        $xmlService->startElement('traceNo', $traceNo);

        return $xmlService->getString();
    }

    private function generatePesonetPaybankRequest(array $data): array
    {
        return [
            'username' => $this->pesonetUsername,
            'password' => $this->pesonetPassword,
            'fundingAcctNo' => $this->pesonetAccount,
            'amount' => $data['amount'],
            'destinationAcct' => $data['account_number'],
            'destinationBankBic' => $data['bank_code'],
            'senderName' => $data['sender_first_name'] . ' ' . $data['sender_last_name'],
            "senderAdd1" => $data['sender_address'],
            "senderAdd2" => "",
            "senderAdd3" => "",
            "senderAdd4" => "",
            "senderEmail" => "",
            "senderMobileNumber" => "",
            'beneficiaryName' => $data['recipient_first_name'] . ' ' . $data['recipient_last_name'],
            "beneficiaryAdd1" => $data['beneficiary_address'],
            "beneficiaryAdd2" => "",
            "beneficiaryAdd3" => "",
            "beneficiaryAdd4" => "",
            "beneficiaryEmail" => "",
            "beneficiaryMobileNumber" => "",
            'categoryPurpose' => 'CASH',
            'traceNo' => $data['refNo'],
            "instruction" => ""
        ];
    }

}
