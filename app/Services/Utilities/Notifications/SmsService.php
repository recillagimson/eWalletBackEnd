<?php


namespace App\Services\Utilities\Notifications;


use App\Services\Utilities\API\IApiService;
use Illuminate\Validation\ValidationException;

class SmsService implements INotificationService
{
    protected string $apiUrl;

    protected string $username;

    protected string $password;

    protected string $shotcodeMask;

    private IApiService $apiService;
    private string $broadcastUrl;

    public function __construct(IApiService $apiService)
    {
        $this->apiUrl = config('sms.api_url');
        $this->username = config('sms.username');
        $this->password = config('sms.password');
        $this->shotcodeMask = config('sms.shortcode_mask');

        $this->apiService = $apiService;
        $this->broadcastUrl = $this->apiUrl.'/broadcast';
    }

    public function sendPasswordVerification(string $to, string $otp)
    {
        $content = 'Your password recovery code is: '.$otp;
        $this->sendMessages($to, $content);
    }

    public function sendAccountVerification(string $to, string $otp)
    {
        $content = 'Your account verification code is: '.$otp;
        $this->sendMessages($to, $content);
    }

    public function sendLoginVerification(string $to, string $otp)
    {
        $content = 'Your login verification code is: '.$otp;
        $this->sendMessages($to, $content);
    }

    private function sendMessages(string $to, string $content)
    {
        $message = $this->buildMessage($to, $content);
        $response = $this->apiService->post($this->broadcastUrl, $message);
        if(!$response->successful()) $this->sendingFailed();
    }

    private function buildMessage(string $to, string $content): array
    {
        return [
            'username' => $this->username,
            'password' => $this->password,
            'msisdn' => $to,
            'content' => $content,
            'shortcode_mask' => $this->shotcodeMask,
        ];
    }

    private function sendingFailed()
    {
        throw ValidationException::withMessages([
           'sms' => 'SMS provider failed to send the message. Please try again.'
        ]);
    }



}
