<?php


namespace App\Services\ThirdParty\SecurityBank;


use App\Enums\TpaProviders;
use App\Services\Utilities\API\IApiService;
use App\Services\Utilities\XML\XmlService;
use Illuminate\Http\Client\Response;

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

        $xmlService->startElement('payBank');

        $xmlService->startElement('username', $this->instapayUsername);
        $xmlService->startElement('password', $this->instapayPassword);
        $xmlService->startElement('acctFr', $this->instapayAccount);
        $xmlService->startElement('Bank', $data['bank_code']);
        $xmlService->startElement('acctTo', $data['account_number']);
        $xmlService->startElement('accttoCurr', 'PHP');
        $xmlService->startElement('amount', $data['amount']);
        $xmlService->startElement('senderFirstName', $data['sender_first_name']);
        $xmlService->startElement('senderLastName', $data['sender_first_name']);
        $xmlService->startElement('recipientFirstName', $data['recipient_first_name']);
        $xmlService->startElement('recipientLastName', $data['recipient_last_name']);
        $xmlService->startElement('traceNo', $data['refNo']);

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
            'beneficiaryName' => $data['recipient_first_name'] . ' ' . $data['recipient_last_name'],
            'beneficiaryAdd1' => 'na',
            'categoryPurpose' => 'CASH',
            'traceNo' => $data['refNo']
        ];
    }

}
